<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserFolder;
use App\Models\User;
use App\Models\UserTool;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    
    public function createCollection( Request $request )
    {
    
        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'folder_name', 'access_type'];

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
                'user_id'        => 'required|exists:users,id',
                'folder_name'    => 'required|string',
                'access_type'    => 'required|in:private,public',
            ]);
            
            // Create data array to insert in database.
            $data = [
                'user_id'           => $request->user_id,
                'folder_name'       => $request->folder_name,
                'access_type'       => $request->access_type ?? 'private',
                'shareable_link'    => Str::uuid(),
            ];

            // insert data array in database.
            $folder = UserFolder::create($data);
            
            DB::commit();
            
            // if everything goes well return with success.
            return response()->json([
                'success' => true,
                'message' => 'Collection created successfully!',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Collection creation failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Collection creation failed. Please try again later.',
            ], 500);
        }
    }

    public function addToolInCollection( Request $request )
    {

        DB::beginTransaction();
        try {

            //Get only the expected parameters
            $allowedParams = ['user_id', 'folder_id', 'tool_id'];

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
                'user_id'    => 'required|exists:users,id',
                'folder_id'  => 'required',
                'tool_id'    => 'required|exists:ai_tools,id',
            ]);

            $folder_ids = $request->folder_id;

            // if more than 1 folder id provided then convert them from string to array.
            if ( $folder_ids != false ) {

                $folder_ids = explode(',', $folder_ids); // Convert comma-separated IDs into an array
            }

            $collectionsCount = count( $folder_ids );

            // loop for insert tool in multiple collections.
            foreach( $folder_ids as $id ) {

                $collection = UserFolder::where( 'id', $id )->where( 'user_id', $request->user_id )->first();

                if ( !$collection ) {
                    
                    // if provided data found no collection then return.
                    return response()->json([
                        'success'   => false,
                        'message'   => "One of the selected collections is invalid, does not exist, or has been removed.",
                    ], 200);
                }

                // check for exists tool in collection
                $existTool = UserTool::where( 'user_id', $request->user_id )->where( 'folder_id', $id )->where( 'tool_id', $request->tool_id )->first();

                // if tool exists then return.
                if ( $existTool ) {

                    return response()->json([
                        'success' => false,
                        'message' => "The tool is already present in one of the selected collection.",
                    ], 200);
                }

                $data = [
                    'folder_id'     => $id,
                    'user_id'       => $request->user_id,
                    'tool_id'       => $request->tool_id,
                ];
    
                $folder = UserTool::create($data);
            }
            
            DB::commit();

            // if provided collection is one then return with singular otherwise plural.
            if ( $collectionsCount == 1 ) {

                return response()->json([
                    'success' => true,
                    'message' => 'Tool added in collection successfully!',
                ], 200);
            } else {

                return response()->json([
                    'success' => true,
                    'message' => 'Tool added in collections successfully!',
                ], 200);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
        
        } catch (\Exception $e) {
        
            DB::rollBack();
            \Log::error('Failed to add tool in collection: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to add tool in collection. Please try again later.',
            ], 500);
        }
    }

    public function removeToolInCollection( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'folder_id', 'tool_ids'];

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
                'user_id'    => 'required|exists:users,id',
                'folder_id'  => 'required|exists:user_folders,id',
                'tool_ids'    => 'required|string',
            ]);

            $tool_id = $request->tool_ids ?? false;
            $tool_ids = explode(',', $tool_id);

            $loop_count = 0;

            foreach( $tool_ids as $id ) {

                $toolInCollection = UserTool::where( 'folder_id', $request->folder_id )
                    ->where( 'tool_id', $id )
                    ->where( 'user_id', $request->user_id )
                    ->first();

                // if provided tool in is not in collection then return.
                if ( !$toolInCollection ) {
                    return response()->json([
                        'success'   => false,
                        'message'   => "One of the selected tool is invalid, does not exist, or has been removed.",
                    ], 200);
                }
                
                // delete toool from collection.
                $toolInCollection->delete();

                $loop_count++;
            }
            
            DB::commit();

            // if tool is 1 then retrun with singluar otherwise plural.
            if ( $loop_count > 1 ) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tools remove from collection successfully!',
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Tool remove from collection successfully!',
                ], 200);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);
        
        } catch (\Exception $e) {
        
            DB::rollBack();
            \Log::error('Failed to add tool in collection: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to add tool in collection. Please try again later.',
            ], 500);
        }
    }

    public function getCollectionsData( Request $request )
    {
        
        try {

            // Get only the expected parameters
            $allowedParams = ['limit', 'order_by', 'sort_by', 'page_no', 'user_id', 'ids', 'columns', 'ai_tool_columns', 'just_collections'];

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
                'user_id'           => 'required|exists:users,id',
                'page_no'           => 'nullable|integer',
                'order_by'          => 'nullable|in:asc,desc',
                'sort_by'           => 'nullable|in:name,id',
                'ids'               => 'nullable|string',
                'columns'           => 'nullable|string',
                'ai_tool_columns'   => 'nullable|string',
                'just_collections'  => 'nullable',
            ]);

            // variable assign, if data is provide otherwise assing default data.
            $limit = $request->limit ?? 10;
            $order_by = $request->order_by ?? 'desc';
            $sort_by = $request->sort_by ?? 'id';
            $page_no = $request->page_no ?? 1;
            $user_id = $request->user_id;
            $ids = $request->ids ?? false;
            $columns = $request->columns ?? false;
            $ai_tool_columns = $request->ai_tool_columns ?? false;
            $just_collections = $request->just_collections ?? false;

            $query = UserFolder::query();
            $query->where( 'user_id', $user_id );

            // if tools table column is provided then convert string to array.
            if ($ai_tool_columns != false) {

                $ai_tool_columns = explode(',', str_replace(["'", '"'], '', $ai_tool_columns));
                $ai_tool_columns = array_map(fn($col) => 'ai_tools.' . trim($col), $ai_tool_columns);
            } else {

                $ai_tool_columns = 'ai_tools.*';
            } 

            // if just collection is provided then filter the quert to just return the collections without tools.
            if ( $just_collections == false ) {

                $query->with(['ai_tools' => function ($query) use ($ai_tool_columns, $sort_by, $order_by) {
                    $query->select($ai_tool_columns);
                    $query->orderBy( 'ai_tools.' . $sort_by, $order_by );
                }]);
            }

            // if specific collections ids provided then filter the query to just return the provided ids data.
            if ( $ids != false ) {

                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            // if collection table columns provided then return only provided columns data.
            if ( $columns != false ) {

                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {

                $columns = '*';
            }

            $query->select( $columns );
            $query->orderBy( 'id', $order_by );

            // if limit provided then return the limited data otherwise default.
            if ($limit == -1) {

                $totalCollectionCount = $query->count();
                $total_pages = 1;
            } else {
                
                $offset = ($page_no - 1) * $limit;
                
                $totalCollectionCount = $query->count();
                $query->offset($offset);
                $query->limit($limit);

                $total_pages = ceil($totalCollectionCount / $limit);
            }

            $collections = $query->get();

            // Check if $tools is empty
            if ($collections->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }   

            // if everything goes well then return success.
            return response()->json([
                'status'        => 'success',
                'message'       => 'Collections fetched successfully.',
                'count'         => $totalCollectionCount,
                'total_pages'   => $total_pages,
                'current_page'  => $page_no,
                'data'          => $collections,
                'asset_url'     => asset( 'public/storage/ai-tools-images' ),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            \Log::error('Failed to fetch collection: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch collection. Please try again later.',
            ], 500);
        }
    }

    public function collectionUpdate ( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['new_name', 'folder_id', 'user_id', 'access_type'];

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
                'folder_id'         => 'required|exists:user_folders,id',
                'user_id'           => 'required|exists:users,id',
                'new_name'          => 'nullable|string',
                'access_type'       => 'nullable|in:public,private',
            ]);

            $folder_id = $request->folder_id;
            $user_id = $request->user_id;
            $new_name = $request->new_name ?? false;
            $access_type = $request->access_type ?? 'private';

            $collection = UserFolder::where( 'user_id', $user_id )->where( 'id', $folder_id )->first();

            if ( !$collection ) {

                return response()->json([
                    'success' => false,
                    'message' => 'The collection is either invalid or has already been deleted.',
                ], 200);
            }

            if ( $new_name != false ) {

                $previous_name = $collection->folder_name;
                
                if ( $new_name == '' ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter a name to rename the collection.',
                    ], 200);
                }
    
                if ( $previous_name == $new_name ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'The chosen name is already in use.',
                    ], 200);
                }
    
                $collection->folder_name = $new_name;
            }
            
            $collection->access_type = $access_type;
            $collection->save();

            DB::commit();

            return response()->json([

                'success' => true,
                'message' => 'The collection has been successfully updated.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Failed to update folder: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to update folder. Please try again later.',
            ], 500);
        }
    }

    public function collectionShare ( Request $request )
    {
        
        try {

            // Get only the expected parameters
            $allowedParams = ['folder_id', 'user_id'];

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
                'folder_id'     => 'required|exists:user_folders,id',
                'user_id'           => 'required|exists:users,id',
            ]);

            $folder_id = $request->folder_id;
            $user_id = $request->user_id;

            $collection = UserFolder::where( 'user_id', $user_id )->where( 'id', $folder_id )->first();

            if ( !$collection ) {

                return response()->json([
                    'success' => false,
                    'message' => 'The collection is either invalid or has already been deleted.',
                ], 200);
            }

            $share_link = $collection->shareable_link;

            return response()->json([
                'success' => true,
                'share_link'    => $share_link,
                'message' => 'The collection has been successfully shared.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            \Log::error('Failed to shared folder: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to shared folder. Please try again later.'
            ], 500);
        }
    }

    public function collectionDelete ( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['folder_id', 'user_id'];

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
                'folder_id'     => 'required|exists:user_folders,id',
                'user_id'           => 'required|exists:users,id',
            ]);

            $folder_id = $request->folder_id;
            $user_id = $request->user_id;

            $collection = UserFolder::where( 'user_id', $user_id )->where( 'id', $folder_id )->first();

            if ( !$collection ) {

                return response()->json([
                    'success' => false,
                    'message' => 'The collection is either invalid or has already been deleted.',
                ], 200);
            }

            $collection->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'The collection has been successfully deleted.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Failed to delete folder: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete folder. Please try again later.'
            ], 500);
        }
    }
}
