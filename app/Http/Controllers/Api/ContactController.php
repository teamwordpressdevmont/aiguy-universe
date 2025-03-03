<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\ContactFormSubmitted;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    
    public function submitContactForm( Request $request )
    {
        DB::beginTransaction();
        try{

            // Get only the expected parameters
            $allowedParams = ['name', 'email', 'message'];
            
            // Check for unexpected parameters
            $unexpectedParams = array_diff(array_keys($request->all()), $allowedParams);
            if (!empty($unexpectedParams)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters detected: ' . implode(', ', $unexpectedParams)
                ], 400);
            }

            // Validate request
            $validatedData = $request->validate([
                'name' => 'required|string|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|email',
                'message' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            ]);

            Contact::create($validatedData);

            event(new ContactFormSubmitted($request->name, $request->email, $request->message));
            
            DB::commit();
            
            // Return JSON response
            return response()->json([
                'success' => true,
                'message' => 'Form Submitted Succesfully!',
                ], 200);
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
            DB::rollBack();
             // Log the error for debugging
            \Log::error('Error Submitting Form: ' . $e->getMessage());
    
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the form. Please try again later.'
            ], 500);
        }
    }
}
