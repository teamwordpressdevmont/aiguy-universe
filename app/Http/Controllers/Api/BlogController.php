<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\AiToolsCategory;
use App\Models\Blog;

class BlogController extends Controller
{
    public function getBlogs( Request $request )
    {

        try {
            
            // Get only the expected parameters
            $allowedParams = ['columns','order_by', 'limit', 'page_no', 'ids'];
        
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
                'limit' => 'nullable|integer|min:1', // Must be a positive integer
                'page_no' => 'nullable|integer|min:1', // Must be a positive integer
                'order_by' => 'nullable|in:asc,desc', // Restricts to 'asc' or 'desc'
                'columns' => 'nullable|string', // If provided, must be a string
                'ids' => 'nullable|string',
            ]);
            
            $per_page = $request->limit ?? 10;
            $page_no = $request->page_no ?? 1;
            $sort_by = $request->order_by ?? 'DESC';
            $columns = $request->columns ?? false;
            $ids = $request->input( 'ids', false );
            
            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {

                $columns = '*';
            }

            $query = Blog::query();

            if ( $ids != false ) {

                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            $query->with([
                'user:id,full_name,email,avatar', // Fetch specific fields for the user
                'user.roles:id,name', // Fetch only the id and name for roles (no pivot)
                'category:id,name,slug,icon' // Fetch specific fields for categories (no pivot)
            ]);

            $query->orderBy( 'id', $sort_by );
            $query->select( $columns );

            if ($per_page == -1) {

                $total_blogs_count = $query->count();
                $total_pages = 1;
            } else {

                $offset = ($page_no - 1) * $per_page;
                
                $total_blogs_count = $query->count();
                $query->offset($offset);
                $query->limit($per_page);

                $total_pages = ceil($total_blogs_count / $per_page);
            }

            $blogs = $query->get();

            $blogs->transform(function ($blog) {

                if (isset($blog->user)) {

                    // Extract only role names
                    $blog->user->role_names = $blog->user->roles->pluck('name');
                    // Remove the full roles data (pivot, etc.)
                    unset($blog->user->roles);
            
                    // Only retain the required fields in the user object
                    $blog->user = $blog->user->only(['id', 'name', 'email', 'avatar', 'role_names']);
                }
                
                // Clean up categories to only contain relevant fields (no pivot data)
                if (isset($blog->category)) {

                    // Loop through each category and remove the pivot key manually
                    $blog->category->each(function ($category) {

                        unset($category->pivot); // Remove pivot
                    });
                }
                
                return $blog;
            });

            // Check if $tools is empty
            if ($blogs->isEmpty()) {

                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }

            return response()->json([
                'status'         => 'success',
                'message'        => 'Blogs fetched successfully.',
                'count'  => $total_blogs_count,
                'total_pages'    => $total_pages,
                'current_page'   => $page_no,
                'data'       => $blogs,
                'asset_url' => asset( 'public/storage/blog-images' ),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
             // Log the error for debugging
            \Log::error('Error fetching Blogs: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching blogs. Please try again later.'
            ], 500);
        }
    }
}