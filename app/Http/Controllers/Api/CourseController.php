<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Course;

class CourseController extends Controller
{
    
    public function getCourses( Request $request )
    {
        try {

            // Get only the expected parameters
            $allowedParams = ['limit', 'page_no', 'order_by', 'filter', 'columns', 'ids'];

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
                'filter' => 'nullable|string|in:ai_guy', // Ensures 'filter' is a string if provided
                'limit' => 'nullable|integer|min:1', // Must be a positive integer
                'page_no' => 'nullable|integer|min:1', // Must be a positive integer
                'order_by' => 'nullable|in:asc,desc', // Restricts to 'asc' or 'desc'
                'ids' => 'nullable|string',
                'columns' => 'nullable|string' // If provided, must be a string
            ]);

            $columns = $request->columns ?? false;
            
            // if columns provided then convert it from string to array.
            if ( $columns != false ) {
                
                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {
                
                $columns = '*';
            } 
            
            $per_page = $request->limit ?? 10;
            $page_no = $request->page_no ?? 1;
            $sort_by = $request->order_by ?? 'DESC';
            $ids = $request->input( 'ids', false );

            $course = Course::query();
            
            // if ids provided then convert it from string to array
            if ( $ids != false ) {
                
                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $course->whereIn('id', $ids);
            }

            $course->orderBy( 'id', $sort_by );

            if ( $request->filter == 'ai_guy' ) {
                
                $course->where( 'courses_filter', $request->filter );
            }

            $total_academies_count = $course->count();

            if ( $per_page == -1 ) {
                
                $total_pages = 1;
            } else {
                
                $offset = ($page_no - 1) * $per_page;
                $course->offset( $offset ); 
                $course->limit( $per_page );
                $total_pages = ceil($total_academies_count / $per_page);
            }

            $course->select( $columns );
            $academiesData = $course->with('categoryCourses')->get();

            // Check if $tools is empty
            if ($academiesData->isEmpty()) {
                
                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }

            return response()->json([
                'status'         => 'success',
                'message'        => 'Academies fetched successfully.',
                'count'  => $total_academies_count,
                'total_pages'    => $total_pages,
                'current_page'   => $page_no,
                'data'       => $academiesData,
                'asset_url' => asset( 'public/storage/courses-images' ),
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
            
        } catch (\Exception $e) {
            
             // Log the error for debugging
            \Log::error('Error fetching Courses: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Courses. Please try again later.'
            ], 500);
        }
    }
}