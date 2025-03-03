<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Comment;
use App\Models\AiTool;
use App\Models\Blog;
use App\Models\Course;

class MainController extends Controller
{
    
    //Generate access token
    public static function generateAccessToken()
    {
        
        DB::beginTransaction();
        try {
            
            // Generate a strong random token
            $tokenString = Str::random(64);
    
            // Encrypt the token before saving
            $encryptedToken = Crypt::encryptString($tokenString);
    
            // Set token expiry (e.g., 7 days from now)
            $expiresAt = Carbon::now()->addDays(30);
            $expiresAtPrev = Carbon::now()->addDays(1);
    
            // Check if a token already exists
            $token = Token::first();
    
            if ($token) {
                $previous_token = $token->token;
                // Update the existing token
                $token->update([
                    'token'      => $encryptedToken,
                    'expires_at' => $expiresAt,
                    'previous_token'      => $previous_token,
                    'previous_expires_at' => $expiresAtPrev,
                ]);
            } else {
                // Create a new token
                Token::create([
                    'token'      => $encryptedToken,
                    'expires_at' => $expiresAt,
                ]);
            }
            DB::commit();
            
            // Return the plain token to the user
            return response()->json([ 
                'message' => 'Token created successfully.',
                'token' => $encryptedToken,
                'previous' => $previous_token,
            ]); 
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
            
        } catch (\Exception $e) {
            
            DB::rollback();
            \Log::error('Failed to generate token: ' . $e->getMessage());
            
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token. Please try again later.',
            ], 500);
        }
        
    }
    
    // get access token
    public static function getAccessToken()
    {
        
        // Fetch the first token from the database
        $token = Token::first();

        // If no token exists, return an appropriate message
        if (!$token) {
            return [
                'message' => 'No token found',
                'encrypted_token' => null,
                'decrypted_token' => null
            ];
        }

        // Decrypt the token safely
        try {
            $decryptedToken = Crypt::decryptString($token->token);
        } catch (\Exception $e) {
            $decryptedToken = 'Decryption failed';
        }

        return response()->json([
            'encrypted_token' => $token->token,
        ]);
    }
    
    //Search API
    public function searchToolsBlogsCourses(Request $request)
    {
        try {
            
            // Get only the expected parameters
            $allowedParams = ['limit', 'order_by', 'page_no', 'blog_columns', 'ai_tool_columns', 'course_columns', 'search', 'filter'];

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
                'limit'             => 'nullable|integer',
                'page_no'           => 'nullable|integer',
                'order_by'          => 'nullable|in:asc,desc',
                'search'            => 'required|string',
                'blog_columns'      => 'nullable|string',
                'ai_tool_columns'   => 'nullable|string',
                'course_columns'    => 'nullable|string',
                'filter'            => 'nullable|string',
            ]);

            //Assign variables to default values or provided values.
            $limit = $request->limit ?? 10;
            $order_by = $request->order_by ?? 'desc';
            $page_no = $request->page_no ?? 1;
            $blog_columns = $request->blog_columns ?? false;
            $ai_tool_columns = $request->ai_tool_columns ?? false;
            $course_columns = $request->course_columns ?? false;
            $search = $request->search;
            $filter = $request->filter ?? false;
            
            //if filters provided then convert array from string
            $filters = [];
            if ($filter !== false) {
            
                $filters = array_map('trim', explode(',', $filter));
            }
            
            $data = [];
            
            //Tools provided in filters or filter is empty then add tools in data.
            if ( empty( $filters ) || in_array( 'tools', $filters ) ) {
                
                $tools = AiTool::query();
                $tools->where('name', 'LIKE', "%{$search}%");
                
                //if tools_columns provided then convert columns from string to array.
                if ( $ai_tool_columns != false ) {
                    
                    $ai_tool_columns = explode(',', str_replace(["'", '"'], '', $ai_tool_columns));
                    $ai_tool_columns = array_map('trim', $ai_tool_columns);
                } else {
                    
                    $ai_tool_columns = '*';
                }
                
                $tools->select( $ai_tool_columns );
                $tools->orderBy( 'id', $order_by );
                
                //if limit provided then limit the query to just show the limited data otherwise default data.
                if ($limit == -1) {
                    
                    $totalToolsCounts = $tools->count();
                    $total_tools_pages = 1;
                } else {
                    
                    $offset = ($page_no - 1) * $limit;
                    
                    $totalToolsCounts = $tools->count();
                    $tools->offset($offset);
                    $tools->limit($limit);
    
                    $total_tools_pages = ceil($totalToolsCounts / $limit);
                }
                
                $tools = $tools->get();
                
                $tools = $tools->map(function ($tool) {
                    // Convert model to array first
                    $toolArray = $tool->toArray();
                
                    // Overwrite values using accessors if they exist
                    if ($tool->verified_status !== null) {
                        $toolArray['verified_status'] = $tool->verified_status_text;
                    }
                
                    if ($tool->payment_status !== null) {
                        $toolArray['payment_status'] = $tool->payment_status_text;
                    }
                
                    if ($tool->platform_compatibility !== null) {
                        $toolArray['platform_compatibility'] = $tool->platform_compatibility_text;
                    }
                
                    if ($tool->integration_capabilities !== null) {
                        $toolArray['integration_capabilities'] = $tool->integration_capabilities_text;
                    }
                
                    return $toolArray; // Return the modified array after all updates
                });

                // Check if $tools is empty
                if ($tools->isEmpty()) {
                    
                    $tools = 'No record found';
                }
                
                $data[] = [
                    'tools' => [
                        'count' => $totalToolsCounts,
                        'data'  => $tools,
                    ],
                ];
            } else {
                
                $total_tools_pages = 0;
            }
            
            //Blogs provided in filters or filter is empty then add tools in data.
            if ( empty( $filters ) || in_array( 'blogs', $filters ) ) {
             
                $blogs = Blog::query();
                $blogs->where('name', 'LIKE', "%{$search}%");
                
                //if blogs columns provided then convert string from array.
                if ( $blog_columns != false ) {
                    
                    $blog_columns = explode(',', str_replace(["'", '"'], '', $blog_columns));
                    $blog_columns = array_map('trim', $blog_columns);
                } else {
                    
                    $blog_columns = '*';
                }
    
                $blogs->select( $blog_columns );
                $blogs->orderBy( 'id', $order_by );
    
                //if limit provided then limit the data to show the provided limit otherwise default data.
                if ($limit == -1) {
                    
                    $totalBlogsCounts = $blogs->count();
                    $total_blogs_pages = 1;
                } else {
                    
                    $offset = ($page_no - 1) * $limit;
                    
                    $totalBlogsCounts = $blogs->count();
                    $blogs->offset($offset);
                    $blogs->limit($limit);
    
                    $total_blogs_pages = ceil($totalBlogsCounts / $limit);
                }
    
                $blogs = $blogs->get();
    
                // Check if $tools is empty
                if ($blogs->isEmpty()) {
                    $blogs = 'No record found';
                }
                
                $data[] = [
                    'blogs'     => [
                        'count' => $totalBlogsCounts,
                        'data'  => $blogs,
                    ],
                ];
            } else {
                
                $total_blogs_pages = 0;
            }
            
            //Blogs provided in filters or filter is empty then add tools in data.
            if ( empty( $filters ) || in_array( 'courses', $filters ) ) {
                
                $courses = Course::query();
                $courses->where('name', 'LIKE', "%{$search}%");
                
                //if course columns provided then convert it from string to array.
                if ( $course_columns != false ) {
                    
                    $course_columns = explode(',', str_replace(["'", '"'], '', $course_columns));
                    $course_columns = array_map('trim', $course_columns);
                } else {
                    
                    $course_columns = '*';
                }
    
                $courses->select( $course_columns );
                $courses->orderBy( 'id', $order_by );
                
                //if limit provided then return the limited data otherwise default data
                if ($limit == -1) {
                    
                    $totalCoursesCounts = $courses->count();
                    $total_courses_pages = 1;
                } else {
                    
                    $offset = ($page_no - 1) * $limit;
                    
                    $totalCoursesCounts = $courses->count();
                    $courses->offset($offset);
                    $courses->limit($limit);
    
                    $total_courses_pages = ceil($totalCoursesCounts / $limit);
                }
    
                $courses = $courses->get();
    
                // Check if $tools is empty
                if ($courses->isEmpty()) {
                    $courses = 'No record found';
                }
                
                $data[] = [
                    'courses'   => [
                        'count' => $totalCoursesCounts,
                        'data'  => $courses,
                    ],
                ];    
            } else {
                
                $total_courses_pages = 0;
            }
            
            //get the maximum page number we got from our data. 
            $pages = max($total_courses_pages, $total_blogs_pages, $total_tools_pages);
            
            //return data if everythings goes well.
            return response()->json([
                'status'        => 'success',
                'message'       => 'Search results fetched successfully.',
                'total_pages'   => $pages,
                'current_page'  => $page_no,
                'data'          => $data,
                'asset_url'     => [
                    'tools'     => asset( 'public/storage/ai-tools-images' ),
                    'blogs'     => asset( 'public/storage/blog-images' ),
                    'courses'   => asset( 'public/storage/course-images' ),
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            
            //return validation errors if have any.
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            
            //return others errors if have any.
            \Log::error('Failed to search: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to search. Please try again later.',
            ], 500);
        }
    }
}
