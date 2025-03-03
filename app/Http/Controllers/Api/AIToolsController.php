<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\AiToolsCategory;
use App\Models\ToolReview;
use App\Models\AiTool;
use App\Models\UserTool;

class AIToolsController extends Controller
{
    /* Fetch Category API
    * Method      : GET
    * URL         : domain.com/api/categories/get
    * return      : If any error returns error message with 422 status code, if success return 200 with categories_data
    */
    public function fetchCategories1( Request $request )
    {
        try {
            $categoriesData = AiToolsCategory::orderByRaw('CASE WHEN parent_category_id IS NULL THEN id ELSE parent_category_id END')

                                        ->orderByRaw('parent_category_id IS NOT NULL')

                                        ->orderBy('id')

                                        ->get();

            return response()->json([
                'status'         => 'success',
                'message'        => 'Categories fetched successfully.',
                'categories_data'  => $categoriesData,
                'asset_url' => asset( 'public/storage/ai-tools-category-images' ),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
        } catch (\Exception $e) {
             // Log the error for debugging
            \Log::error('Error fetching Categories: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching categories. Please try again later.'
            ], 500);
        }
    }
    
    public function fetchCategories2( Request $request )
    {

        try {

            // Get only the expected parameters
            $allowedParams = ['only_parent', 'columns','order_by', 'limit', 'page_no', 'custom_order', 'ids'];
        
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
                'only_parent' => 'nullable|in:true,false',
                'limit' => 'nullable|integer|min:1', // Must be a positive integer
                'page_no' => 'nullable|integer|min:1', // Must be a positive integer
                'order_by' => 'nullable|in:asc,desc', // Restricts to 'asc' or 'desc'
                'columns' => 'nullable|string', // If provided, must be a string
                'custom_order'  => 'nullable|string',
                'ids'   => 'nullable|string',
            ]);
            
            $just_parent_categories = $request->only_parent ?? false;
            $columns = $request->columns ?? false;
            $per_page    = $request->limit ?? 10;
            $page_no     = $request->page_no ?? 1;
            $sort_by     = $request->order_by ?? 'DESC';
            $customOrderString = $request->custom_order ?? '';
            $ids = $request->ids ?? false;

            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {

                $columns = '*';
            }
            
            if ( $just_parent_categories == true || $ids != false ) {
                
                $query = AiToolsCategory::query();

                if ( $ids == false ) {

                    $query->withCount('tools')->where('parent_category_id', null);
                } else {

                    $query->withCount('tools');
                    $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                    $query->whereIn('id', $ids);
                }

                //$query->where('parent_category_id', null);
                $query->select($columns);

                if (!empty($customOrderString)) {
                    $query->orderByRaw("FIELD(id, " . $customOrderString . ") DESC"); // Custom order first
                }
                
                $query->orderBy('id', $sort_by); // Default order applied afterward
                
                $total_categories = $query->count();
                
                if ($per_page == -1) {

                    $categories = $query->get();
                    $total_pages = 1;
                } else {

                    $categories = $query->offset(($page_no - 1) * $per_page)
                                   ->limit($per_page)
                                   ->get();
                    $total_pages = ceil($total_categories / $per_page);
                }

                // Check if $tools is empty
                if ($categories->isEmpty()) {

                    return response()->json(['success' => false, 'message' => 'No record found'], 200);
                }
                
                return response()->json([
                    'status'         => 'success',
                    'message'        => 'Categories fetched successfully.',
                    'count'  => $total_categories,
                    'total_pages'       => $total_pages,
                    'current_page'  => $page_no,
                    'data'  => $categories,
                    'asset_url' => asset( 'public/storage/ai-tools-category-images' ),
                ], 200);
            } else {
            
                $query = AiToolsCategory::withCount('tools')->select($columns);

                if (!empty($customOrderString)) {
                    $query->orderByRaw("FIELD(id, " . $customOrderString . ") DESC"); // Custom order
                }

                $query->orderBy('id', $sort_by); // Default order by id
            
                $categories = $query->get()->groupBy('parent_category_id');
            
                $categoriesData = [];
            
                foreach ($categories[null] as $parent) {

                    $childrenWithCount = isset($categories[$parent->id]) ? $categories[$parent->id]->map(function ($child) {
                        $child->ai_tools_count = DB::table('ai_tools_relation')->where('category_id', $child->id)->count();
                        return $child;
                    }) : [];
                    
                    $categoriesData[] = [
                        'parent' => $parent,
                        'children' => $childrenWithCount,
                        'ai_tools_count' => $parent->tools_count ?? 0,
                    ];
                }

                // Check if $tools is empty
                if (empty( $categoriesData )) {

                    return response()->json(['success' => false, 'message' => 'No record found'], 200);
                }
            
                return response()->json([
                    'status'        => 'success',
                    'message'       => 'Categories fetched successfully.',
                    'data' => $categoriesData,
                    'asset_url'     => asset('public/storage/ai-tools-category-images'),
                ], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {
            
             // Log the error for debugging
            \Log::error('Error fetching Categories: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching categories. Please try again later.'
            ], 500);

        }
    }
    
    public function getTools (Request $request)
    {

        try{

            // Get only the expected parameters
            $allowedParams = ['user_id', 'filter', 'limit', 'page_no', 'order_by', 'columns', 'ids', 'rating', 'industry', 'category', 'pricing', 'integration', 'sort_by'];

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
                'user_id'   => 'nullable|exists:users,id',
                'filter' => 'nullable|string', // Ensures 'filter' is a string if provided
                'limit' => 'nullable|integer|min:1', // Must be a positive integer
                'page_no' => 'nullable|integer|min:1', // Must be a positive integer
                'order_by' => 'nullable|in:asc,desc', // Restricts to 'asc' or 'desc'
                'sort_by' => 'nullable|in:id,name', // Restricts to 'asc' or 'desc'
                'columns' => 'nullable|string', // If provided, must be a string
                'ids' => 'nullable|string', // If provided, must be
                'rating' => 'nullable|integer' ,// If provided, must be
                'industry' => 'nullable', // If provided, must be
                'category' => 'nullable', // If provided, must be
                'pricing' => 'nullable|integer', // If provided, must be
                'integration' => 'nullable|integer', // If provided, must be
            ]);

            // Fetch query parameters
            $user_id = $request->user_id ?? false;
            $aitool_filter = $request->input('filter'); // popular_tool, latest_tool, alternative_tool
            $limit = $request->input('limit', 10); // Default 10 rows
            $page_no = $request->input( 'page_no', 1 );
            $orderBy = $request->input('order_by', 'desc'); // Default DESC
            $sort_by = $request->input('sort_by', 'id'); // Default DESC
            $columns = $request->input( 'columns', false ); //
            $ids = $request->input( 'ids', false ); // Single Tool ID, false mean All
            $rating = $request->input( 'rating', false ); 
            $industry = $request->input( 'industry', false );
            $category = $request->input( 'category', false );
            $pricing = $request->input( 'pricing', false );
            $integration = $request->input( 'integration', false );

            // Start query
            $query = AiTool::query();

            //Filteration Start
            if ( $rating != false ) {

                $query->where( 'avg_rating', '>=', $rating );
            }

            if ( $pricing != false ) {

                $query->where( 'payment_status', $pricing );
            }

            if ( $integration != false ) {

                $query->where( 'integration_capabilities', $integration );
            }

            if ($industry != false && $category == false) {

                $childCategoryIds = AiToolsCategory::whereIn('parent_category_id', $industry)
                    ->pluck('id')
                    ->toArray();
            
                $allCategoryIds = array_merge($industry, $childCategoryIds);
            
                if (!empty($allCategoryIds)) {

                    $query->whereHas('category', function ($q) use ($allCategoryIds) {
                        $q->whereIn('ai_tool_category.id', $allCategoryIds);
                    });
                } else {

                    $tools = collect();
                    return;
                }
            }

            if ( $category != false ) {

                $query->whereHas('category', function ($q) use ($category) {
                    $q->whereIn('ai_tool_category.id', $category);
                });
            }

            //Filteration End

            //If not false to will just get given columns in '$columns'
            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
                $query->select( $columns );
            }

            //If not false so will get just these ids
            if ( $ids != false ) {

                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            // Apply conditions based on flags
            if ($aitool_filter) {

                $query->where('aitool_filter', $aitool_filter);
            }

            // Apply ordering
            $query->orderBy($sort_by, $orderBy);

            $totalToolsCount = $query->count();

            // Apply offset
            $offset = ($page_no - 1) * $limit;
            $query->offset( $offset );

            // Limit the results
            $tools = $query->limit($limit)->get();
            //
            $tools = $tools->map(function ($tool) use ( $user_id )  {
                // Convert model to array first
                $toolArray = $tool->toArray();
                
                if ( $user_id != false ) {
                    $userHaveTool = UserTool::where( 'tool_id', $toolArray['id'] )->where( 'user_id', $user_id )->count();
                    if ( $userHaveTool >= 1 ) {
                        $toolArray['user_have_tool'] = true;
                    } else {
                        $toolArray['user_have_tool'] = false;
                    }
                }
                
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
                
            // Get total count of records
            $totalCount = $tools->count();

            // Check if $tools is empty
            if ($tools->isEmpty()) {

                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }

            foreach ($tools as $tool) {

                // Transform 'pros' if available
                if (isset( $tool['pros'] )) {

                    // Decode the 'pros' JSON string into an array (only one decode)
                    $decodedPros = json_decode(json_decode($tool['pros'], true), true);

                    if (json_last_error() !== JSON_ERROR_NONE) {

                        // Handle JSON decode error if occurs
                        \Log::error('JSON Decode Error for Pros: ' . json_last_error_msg());
                        continue; // Skip to next tool if error occurs
                    }

                    // Format the 'pros' data
                    $formattedPros = [];
                    foreach ($decodedPros as $key => $value) {

                        $formattedPros[] = [
                            'title' => $value['heading'],
                            'description' => $value['description']
                        ];
                    }
                    // Assign formatted data back to 'pros'
                    $tool['pros'] = $formattedPros;
                }
                
                if (isset( $tool['cons'] )) {

                    // Decode the 'pros' JSON string into an array (only one decode)
                    $decodedCons = json_decode(json_decode($tool['cons'], true), true);

                    if (json_last_error() !== JSON_ERROR_NONE) {

                        // Handle JSON decode error if occurs
                        \Log::error('JSON Decode Error for cons: ' . json_last_error_msg());
                        continue; // Skip to next tool if error occurs
                    }

                    // Format the 'cons' data
                    $formattedCons = [];
                    foreach ($decodedCons as $key => $value) {

                        $formattedCons[] = [
                            'title' => $value['heading'],
                            'description' => $value['description']
                        ];
                    }
                    // Assign formatted data back to 'cons'
                    $tool['cons'] = $formattedCons;
                }
            }

            $total_pages = ceil($totalToolsCount / $limit);
            
            // Return JSON response
            return response()->json([
                'success'       => true,
                'count'         => $totalToolsCount,
                'total_pages'   => $total_pages,
                'current_page'  => $page_no,
                'data'          => $tools,
                'asset_url'     => asset( 'public/storage/ai-tools-images' )
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            // Log the error for debugging
            \Log::error('Error fetching AI tools: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching tools. Please try again later.' . $e->getMessage()
            ], 500);
        }
    }

    public function getReviews(Request $request)
    {

        try{

            // Get only the expected parameters
            $allowedParams = [ 'limit', 'page_no', 'order_by', 'columns', 'tool_id', 'user_id', 'ids' ];

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
                'tool_id' => 'nullable|string', // If provided, must be a string
                'user_id' => 'nullable|string', // If provided, must be a string
                'ids' => 'nullable|string' // If provided, must be
            ]);

            // Fetch query parameters
            $limit = $request->input('limit', 10); // Default 10 rows
            $page_no = $request->input( 'page_no', 1 );
            $orderBy = $request->input('order_by', 'desc'); // Default DESC
            $columns = $request->input( 'columns', false ); //
            $tool_id = $request->input( 'tool_id', false ); //
            $user_id = $request->input( 'user_id', false ); //
            $review_ids = $request->input( 'ids', false ); // Single Tool ID, false mean All

            // Start query
            $query = ToolReview::query();
            $query->where( 'approved', 1 );
            $query->with( 'user' );

            //If not false to will just get given columns in '$columns'
            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
                $query->select( $columns );
            }

            //If not false so will get just these ids
            if ( $review_ids != false ) {

                $ids = explode(',', $review_ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            //If not false so will get Reviews by Tool ID
            if ( $tool_id != false ) {

                $query->where('tool_id', $tool_id);
            }

            //If not false so will get Reviews by Tool ID
            if ( $user_id != false ) {

                $query->where('user_id', $user_id);
            }

            // Apply ordering
            $query->orderBy('id', $orderBy);

            $totalReviewsCount = $query->count();

            // Apply offset
            $offset = ($page_no - 1) * $limit;
            $query->offset( $offset );

            // Limit the results
            $reviews = $query->limit($limit)->get();

            // Get total count of records
            $totalCount = $reviews->count();

            $total_pages = ceil($totalReviewsCount / $limit);

            // Check if $tools is empty
            if ($reviews->isEmpty()) {

                return response()->json(['success' => false, 'total_count' => $totalCount, 'message' => 'No record found'], 200);
            }

            if ( $tool_id != false ) {

                $query = ToolReview::query();
                $query->where( 'tool_id', $tool_id );
                $query->with( 'user' );

                $totalAverage = ToolReview::where('tool_id', $tool_id)->avg('rating');
                $numberOfUserReviews = $query->count();
                $fiveStar = (clone $query)->where('rating', 5)->count();
                $fourStar = (clone $query)->where('rating', 4)->count();
                $threeStar = (clone $query)->where('rating', 3)->count();
                $twoStar = (clone $query)->where('rating', 2)->count();
                $oneStar = (clone $query)->where('rating', 1)->count();

                // Check if $tools is empty
                if ($reviews->isEmpty()) {

                    return response()->json(['success' => false, 'message' => 'No record found'], 200);
                }

                // Return JSON response
                return response()->json([
                    'success' => true,
                    'count' => $totalReviewsCount,
                    'total_pages'   => $total_pages,
                    'page_no'   => $page_no,
                    // 'total_average' => number_format((float)$totalAverage, 1, '.', ''),
                    'five_star_ratings' => $fiveStar,
                    'four_star_ratings' => $fourStar,
                    'three_star_ratings' => $threeStar,
                    'two_star_ratings' => $twoStar,
                    'one_star_ratings' => $oneStar,
                    'data' => $reviews,
                    'asset_url'     => asset( 'public/storage/users-avatars' )
                ], 200);

            } else {

                // Check if $tools is empty
                if ($reviews->isEmpty()) {

                    return response()->json(['success' => false, 'message' => 'No record found'], 200);
                }

                // Return JSON response
                return response()->json([
                    'success' => true,
                    'count' => $totalReviewsCount,
                    'total_pages'   => $total_pages,
                    'page_no'   => $page_no,
                    'data' => $reviews,
                    'asset_url'     => asset( 'public/storage/users-avatars' )
                ], 200);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            // Log the error for debugging
            \Log::error('Error fetching AI tools: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching tools. Please try again later.'
            ], 500);

        }
    
    }

    public function writeReview ( Request $request )
    {

        DB::beginTransaction();
        try{

            // Get only the expected parameters
            $allowedParams = [ 'tool_id', 'user_id', 'review', 'rating', 'review_id' ];

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
                'tool_id'   => 'required|integer', // must be a integer
                'user_id'   => 'required|exists:users,id', // must be a integer
                'review'    => 'required|string', // must be a string
                'rating'    => 'required|integer', // must be a integer
                'review_id'    => 'nullable|exists:tool_reviews,id', // must be a integer
            ]);

            // Fetch query parameters
            $tool_id = $request->input( 'tool_id', false ); //
            $user_id = $request->input( 'user_id', false ); //
            $review_text = $request->input( 'review', false );
            $rating = $request->input( 'rating', false );
            $review_id = $request->input( 'review_id', false );

            $tool = AiTool::where( 'id', $tool_id )->first();
            
            $data = [
                'tool_id'   => $tool_id,
                'user_id'   => $user_id,
                'review'    => $review_text,
                'rating'    => $rating,
                'approved'  => 1,
            ];

            if ( $review_id == false ) {

                $review = ToolReview::create( $data );
            } else {

                $review = ToolReview::where( 'id', $review_id )->where( 'user_id', $user_id )->where( 'tool_id', $tool_id )->first();

                if ( $review != false ) {

                    $review->review = $review_text;
                }
                
                if ( $rating != false ) {

                    $review->rating = $rating;
                }

                $review->save();
            }

            $total_reviews = ToolReview::where( 'tool_id', $tool_id )->count();
            $average_ratings = ToolReview::where( 'tool_id', $tool_id )->avg( 'rating' );

            $average_ratings = number_format((float)$average_ratings, 1, '.', '');
            
            $tool->reviews_received = $total_reviews;
            $tool->avg_rating = $average_ratings;
            $tool->save();
          
            DB::commit();

            // Return JSON response
            return response()->json([
                'success' => true,
                'data' => $review->id
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
            \Log::error('Error fetching AI tools: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding review. Please try again later.' . $e->getMessage()
            ], 500);
        }
    }
}