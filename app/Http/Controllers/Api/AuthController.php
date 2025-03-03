<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\PasswordResetTokens;
use App\Events\PasswordResetRequest;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register New User
    public function register(Request $request)
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }

            // Validate request
            $request->validate([
                'first_name'            => 'required|string',
                'last_name'             => 'nullable|string',
                'email'                 => ['required', 'email', 'unique:users,email'],
                'phone'                 => 'nullable',
                'password'              => ['required', 'min:6', 'confirmed'],
            ]);
        
            $user = User::create([
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user_profile = UserProfile::create([
                'user_id'               => $user->id,
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'phone_num'             => $request->phone,
            ]);
            
            $token = $user->createToken('API Token')->accessToken;
            
            $user->assignRole('user');

            DB::commit();
        
            return response()->json([
                'success'   => true,
                'message' => 'User created successfully!',
                'user_id'   => $user->id,
                'view_platform' => 0,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            DB::rollBack();
            \Log::error('User creation failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'User creation failed. Please try again later.'
            ], 500);
        }
    }

    public function googleRegister(Request $request)
    {

        $request->validate([
            'email' => ['required', 'unique:users,email' ],
            'name'  => 'required',
            'google_id' => ['required', 'unique:users,google_id' ],
            'avatar'  => 'required',
            'google_token' => 'required',
            'email_verified' => 'required',
            'password' => 'required|min:6',
        ]);
        
        try{
            
            $user = User::updateOrCreate([
                'email' => $request->email,
            ], [
                'full_name' => $request->name,
                'google_id' => $request->google_id,
                'avatar' => $request->avatar,
                'google_token'  => $request->google_token,
                'email_verified'    => $request->email_verified,
                'password' => bcrypt(str()->random(16)),
            ]);
            
            $token = $user->createToken('API Token')->accessToken;
            
            $user->assignRole('user');
            
             return response()->json([
                'message' => 'User created successfully!',
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'User creation failed! ' . $th->getMessage()], 500);
        }

    }

    // Login User & Generate Access Token
    public function login(Request $request)
    {

        try {
            // Get only the expected parameters
            $allowedParams = ['email', 'password'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }
            // Validate request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {

                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            
            $view_platform = UserProfile::where( 'user_id', $user->id )->select( 'view_platform' )->first();
        
            if ( !$view_platform ) {

                return response()->json([
                    'success'   => false,
                    'message' => 'Account not completed please contact support.',
                ], 400);
            }

            // Generate Passport Token
            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'success'   => true,
                'message' => 'Login successful',
                'user_id'   => $user->id,
                'view_platform' => $view_platform->view_platform,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            \Log::error('User Login failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'User Login failed. Please try again later.'
            ], 500);
        }
    }

    // Logout User & Revoke Token
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $token = $user->token();
        $token->revoke(); // Revoke token
        
        return response()->json(['success' => true, 'message' => 'Logout successful'], 200);
    }

    public function profileUpdate( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'avatar', 'first_name', 'last_name', 'email', 'phone', 'industry', 'ai_expertise_level', 'areas_of_interest', 'password', 'password_confirmation', 'skip'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }
            
            // Validate request
            $request->validate([
                'user_id'               => 'required|exists:users,id',
                'avatar'                => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg|max:40000',
                'first_name'            => 'nullable|string',
                'last_name'             => 'nullable|string',
                'email'                 => [
                    'nullable',
                    Rule::unique('users', 'email')->ignore($request->user_id),
                ],
                'phone'                 => 'nullable',
                'industry'              => 'nullable',
                'ai_expertise_level'    => 'nullable|in:advanced,intermediate,beginner',
                'areas_of_interest'     => 'nullable',
                'password'              => ['nullable', 'min:6', 'confirmed'],
                'skip'                  => 'nullable|in:true',
            ]);

            $user_id = $request->user_id;
            $first_name = $request->first_name ?? false;
            $last_name = $request->last_name ?? false;
            $email = $request->email ?? false;
            $phone = $request->phone ?? false;
            $industry = $request->industry ?? false;
            $ai_expertise_level = $request->ai_expertise_level ?? false;
            $areas_of_interest = $request->areas_of_interest ?? false;
            $password = $request->password;
            $skip = $request->skip ?? false;
    
            $user = User::with('user_profile')->find($user_id);
            $user_profile = $user?->user_profile;
            
            if ( $skip == true ) {

                $alreadySkipped = $user_profile->view_platform;

                if ( $alreadySkipped == 1 ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Complete profile already skipped.',
                    ], 400);
                }

                $user_profile->view_platform = 1;
                $user_profile->save();
            }
            
            $user_avatar = $user->avatar;

            if ( $request->hasfile( 'avatar' ) ) {

                if ( $user_avatar != null ) {
                    Storage::disk('public')->delete('users-avatars/' . $user_avatar);
                }
                $user_avatar = date('Y-m-d') . '-' . time() . '-' . preg_replace('/[^A-Za-z0-9\-.]/', '_', $request->avatar->getClientOriginalName());
                $request->file('avatar')->storeAs('users-avatars', $user_avatar, 'public');
                
                $user->avatar = $user_avatar;
            }

            if ( $email != false ) 
            {

                $user->email = $email;
            }

            if ( $first_name != false ) {

                $user_profile->first_name = $first_name;
                $previous_last_name = $user_profile->last_name;
                $user->full_name = $first_name . ' ' . $previous_last_name;
            }

            if ( $last_name != false ) {

                $user_profile->last_name = $last_name;
                $previous_first_name = $user_profile->first_name;
                $user->full_name = $previous_first_name . ' ' . $last_name;
            }
            
            if ( $phone != false ) {

                $user_profile->phone_num = $phone;
            }

            if ( $industry != false ) {

                $user_profile->industry = $industry;
            }

            if ( $ai_expertise_level != false ) {

                $user_profile->ai_expertise_level = $ai_expertise_level;
            }

            if ( $areas_of_interest != false ) {

                $user_profile->area_of_interest = $areas_of_interest;
            }

            if ( $first_name != false && $last_name != false ) {

                $user->full_name = $first_name . ' ' . $last_name;
            }

            if ( $password != false ) {

                $user->password = Hash::make($password);
            }
            
            $user_profile->view_platform = 1;
            $user->save();
            $user_profile->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
        
        } catch (\Exception $e) {
        
            DB::rollBack();
            \Log::error('Failed to update user: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user. Please try again later.'
            ], 500);
        }
    }

    public function profileDelete( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'password'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }
            
            // Validate request
            $request->validate([
                'user_id'               => 'required|exists:users,id',
                'password'              => 'required',
            ]);

            $user_id = $request->user_id;
            $password = $request->password;
    
            $user = User::with('user_profile')->find($user_id);
            
            if (!Hash::check($password, $user->password)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password!',
                ], 200);
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            DB::rollBack();
            \Log::error('Failed to delete user: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user. Please try again later.'
            ], 500);
        }
    }
    
    public function forgotPassword( Request $request )
    {
        
        DB::beginTransaction();
        try {
            
            // Get only the expected parameters
            $allowedParams = ['email'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }

            // Validate request
            $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
            ]);
            
            $user = User::where( 'email', $request->email )->select( 'id' )->first();
            
            if ( $user ) {
                $token = Str::uuid();
                $hashedToken = Hash::make($token);
                
                $previousToken = PasswordResetTokens::where( 'email', $request->email )->delete();
                
                PasswordResetTokens::create([
                    'email'         => $request->email,
                    'token'         => $hashedToken,
                    'created_at'    => now(),
                ]);
 
                event(new PasswordResetRequest($user->id, $request->email, $token));
            
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'An email has been sent to forgot your password.',
                ], 200);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found, invalid or removed.',
                ], 400);
            }
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            DB::rollBack();
            \Log::error('Failed to forgot password: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to forgot password. Please try again later.',
            ], 500);
        }
    }
    
    public function resetPassword( Request $request )
    {
        
        DB::beginTransaction();
        try {
            
            // Get only the expected parameters
            $allowedParams = ['user_id', 'email', 'token', 'password', 'password_confirmation'];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }

            // Validate request
            $request->validate([
                'user_id'   => 'required',
                'email'     => 'required',
                'token'     => 'required',
                'password'  => ['required', 'min:6', 'confirmed'],
            ]);
            
            try {
                $user_id = decrypt($request->user_id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                throw ValidationException::withMessages([
                    'user_id' => ['The provided user id is incorrect.'],
                ]);
            }
            
            try {
                $email = decrypt($request->email);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                throw ValidationException::withMessages([
                    'email' => ['The provided email is incorrect.'],
                ]);
            }
            
            try {
                $token = decrypt($request->token);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                throw ValidationException::withMessages([
                    'token' => ['The provided token is incorrect.'],
                ]);
            }
            
            $password = $request->password;
            
            $tokenVerify = PasswordResetTokens::where( 'email', $email )->first();
            
            if ( !$tokenVerify ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request for password forgot not found.',
                ], 400);
            }
            
            if ( !Hash::check($token, $tokenVerify->token) ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token mistmatched kindly request for new link.',
                ], 400);
            }
            
            $user = User::where( 'email', $email )->where( 'id', $user_id )->first();
            
            if ( $user ) {
                
                $user->password = Hash::make($request->password);
                $user->save();
                PasswordResetTokens::where( 'email', $email )->delete();
                $token = $user->createToken('API Token')->accessToken;
                $view_platform = UserProfile::where( 'user_id', $user->id )->select( 'view_platform' )->first();
                
                DB::commit();
                
                return response()->json([
                    'success'   => true,
                    'message' => 'Password reset successfully!',
                    'user_id'   => $user->id,
                    'view_platform' => $view_platform->view_platform,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ], 200);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found, invalid or removed.',
                ], 400);
            }
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            DB::rollBack();
            \Log::error('Failed to reset password: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again later.',
            ], 500);
        }
    }
    
    public function userProfile( Request $request )
    {
        
        try {
            
            // Get only the expected parameters
            $allowedParams = [ 'columns', 'profile_columns' ];

            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }
            
            // Validate request
            $request->validate([
                'columns' => 'nullable|string', // If provided, must be a string
                'profile_columns' => 'nullable|string', // If provided, must be a string
            ]);
            
            $columns = $request->columns ?? false;
            $profile_columns = $request->profile_columns ?? false;
            
            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                if ( !in_array( 'id', $columns ) ) {
                    if ( !in_array( ' id', $columns ) ) {
                        $columns[] = 'id';
                    }
                }
                $columns = array_map('trim', $columns);
            } else {

                $columns = '*';
            }
            
            if ($profile_columns != false) {

                $profile_columns = explode(',', str_replace(["'", '"'], '', $profile_columns));
                if ( !in_array( 'user_id', $profile_columns ) ) {
                    if ( !in_array( ' user_id', $profile_columns ) ) {
                        $profile_columns[] = 'user_id';
                    }
                }
                $profile_columns = array_map(fn($col) => 'user_profile.' . trim($col), $profile_columns);
            } else {

                $profile_columns = 'user_profile.*';
            }
            
            $user = $request->user();
            
            $query = User::query();
            $query->select( $columns );
            $query->where( 'id', $request->user()->id );
            $query->with(['user_profile' => function ($query) use ($profile_columns) {
                $query->select($profile_columns);
            }]);
            $user = $query->first();
            
            return response()->json([
                'success'    => true,
                'message' => 'User data fetched successfully!',
                'user'   => $user,
            ], 200);
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            \Log::error('Failed to get user data: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user data. Please try again later.'
            ], 400);
        }
    }

}