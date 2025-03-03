<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CommentQuestions;
use App\Models\CommentAnswers;
use App\Models\User;
use App\Models\AiTool;
use App\Models\CommentsLikeDislike;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{

    public function addQuestion( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'tool_id', 'question_title', 'question_content'];

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
                'user_id'           => 'required|exists:users,id',
                'tool_id'           => 'required|exists:ai_tools,id',
                'question_title'    => 'required|string|regex:/^[a-zA-Z\s]+$/',
                'question_content'  => 'nullable|string|regex:/^[a-zA-Z\s]+$/',
            ]);

            $user_id            = $request->user_id;
            $tool_id            = $request->tool_id;
            $question_title     = $request->question_title;
            $question_content   = $request->question_content ?? '';
            
            $data = [
                'user_id'           => $user_id,
                'tool_id'           => $tool_id,
                'comment_title'     => $question_title,
                'comment_content'   => $question_content,
                'approved'          => 1,
            ];

            $question = CommentQuestions::create( $data );

            DB::commit();
            
            //return success.
            return response()->json([
                'success' => true,
                'message' => 'Question submitted successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Failed to submit comment: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit comment. Please try again later.',
            ], 500);
        }
    }
    
    public function addAnswers( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'tool_id', 'comment_id', 'answer'];

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
                'user_id'           => 'required|exists:users,id',
                'tool_id'           => 'required|exists:ai_tools,id',
                'comment_id'        => 'required|exists:comment_questions,id',
                'answer'            => 'required|string|regex:/^[a-zA-Z\s]+$/',
            ]);

            $user_id            = $request->user_id;
            $tool_id            = $request->tool_id;
            $comment_id         = $request->comment_id;
            $answer             = $request->answer ?? false;
            
            $question = CommentQuestions::where( 'tool_id', $tool_id )->where( 'comment_id', $comment_id )->first();
            
            //if question not found return error.
            if ( !$question ) {
                
                //return success.
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid comment id not exist or deleted.',
                ], 400);
            }

            $data = [
                'user_id'    => $user_id,
                'tool_id'    => $tool_id,
                'comment_id' => $comment_id,
                'comment'    => $answer,
                'approved'   => 1,
            ];

            //insert the answer into database.
            $answer = CommentAnswers::create( $data );
            
            DB::commit();

            //return success.
            return response()->json([
                'success' => true,
                'message' => 'Answer submitted successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Failed to submit comment: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit comment. Please try again later.',
            ], 500);
        }
    }

    public function addLikeDislikeOnAnswers( Request $request )
    {

        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'tool_id', 'comment_id', 'comment_answer_id', 'action'];

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
                'user_id'           => 'required|exists:users,id',
                'tool_id'           => 'required|exists:ai_tools,id',
                'comment_id'        => 'required|exists:comment_questions,id',
                'comment_answer_id' => 'required|exists:comment_answers,id',
                'action'            => 'required|in:like,dislike',
            ]);

            $user_id            = $request->user_id;
            $tool_id            = $request->tool_id;
            $comment_id         = $request->comment_id ?? false;
            $comment_answer_id  = $request->comment_answer_id ?? false;
            $action             = $request->action ?? false;

            // get answer
            $answer = CommentAnswers::where( 'tool_id', $tool_id )
                                    ->where( 'comment_id', $comment_id )
                                    ->where( 'id', $comment_answer_id )
                                    ->first();

            // if not answer then return. 
            if ( !$answer ) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Selected comment id or answer id is invalid',
                ], 400);
            }

            // if action is like
            if ( $action == 'like' ) {

                // get previous entry
                $previousEntry = CommentsLikeDislike::where( 'user_id', $user_id )
                                ->where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->first();  

                // if previous entry found then modify it. 
                if ( $previousEntry ) {

                    $like = $previousEntry->like;
                    $dislike = $previousEntry->dislike;
                    
                    // if previous entry is already like then return.
                    if ( $like == 1 ) {

                        return response()->json([
                            'success' => false,
                            'message' => 'You have already liked this answer'
                        ], 400);
                    }

                    // if previous entry is dislike then modufy it and make it like
                    if ( $dislike == 1 ) {

                        $previousEntry->like = 1;
                        $previousEntry->dislike = 0;
                        $previousEntry->save();
                    }

                } else {
                    
                    // if previous entry is not found then just like answer. 
                    $data = [
                        'user_id'   => $user_id,
                        'tool_id'   => $tool_id,
                        'comment_id'    => $comment_id,
                        'comment_answer_id' => $comment_answer_id,
                        'like'  => 1,
                        'dislike'   => 0,
                    ];
    
                    $data = CommentsLikeDislike::create( $data );   
                }

                // get total like counts.
                $like_counts = CommentsLikeDislike::where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->where( 'like', 1 )
                                ->count();

                // get total dislike counts. 
                $dislike_counts = CommentsLikeDislike::where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->where( 'dislike', 1 )
                                ->count();

                // updaye total like and dislike count to answer table.
                $answer->like_count = $like_counts;
                $answer->dislike_count = $dislike_counts;
                $answer->save();
                
                DB::commit();

                // if everything goes well then return.
                return response()->json([
                    'success' => true,
                    'message' => 'Answer liked successfully.'
                ], 200);

            }  

            // if action is dislike
            if ( $action == 'dislike' ) {

                // get previous entry
                $previousEntry = CommentsLikeDislike::where( 'user_id', $user_id )
                                ->where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->first();

                // if previous entry is found 
                if ( $previousEntry ) {

                    $like = $previousEntry->like;
                    $dislike = $previousEntry->dislike;
                    
                    // if previous entry is already dislike then return
                    if ( $dislike == 1 ) {

                        return response()->json([
                            'success' => false,
                            'message' => 'You have already disliked this answer'
                        ], 400);
                    }

                    // if previous entry is like then modify it and update it to make dislike
                    if ( $like == 1 ) {

                        $previousEntry->like = 0;
                        $previousEntry->dislike = 1;
                        $previousEntry->save();
                    }

                } else {

                    // if previous entry is not found then just make the anser dislike
                    $data = [
                        'user_id'   => $user_id,
                        'tool_id'   => $tool_id,
                        'comment_id'    => $comment_id,
                        'comment_answer_id' => $comment_answer_id,
                        'like'  => 0,
                        'dislike'   => 1,
                    ];
    
                    $data = CommentsLikeDislike::create( $data );   
                }

                // get total like counts
                $like_counts = CommentsLikeDislike::where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->where( 'like', 1 )
                                ->count();

                //get total dislike count 
                $dislike_counts = CommentsLikeDislike::where( 'tool_id', $tool_id )
                                ->where( 'comment_id', $comment_id )
                                ->where( 'comment_answer_id', $comment_answer_id )
                                ->where( 'dislike', 1 )
                                ->count();

                // update total like and dislike count to an answer table
                $answer->like_count = $like_counts;
                $answer->dislike_count = $dislike_counts;
                $answer->save();
                
                DB::commit();
                
                // if everything goes well then return success
                return response()->json([
                    'success' => true,
                    'message' => 'Answer disliked successfully.'
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
            \Log::error('Failed to like/dislike answer: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to like/dislike answer. Please try again later.',
            ], 500);
        }
    }

    public function fetchQuestionAnswers( Request $request )
    {
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'tool_id', 'limit', 'order_by', 'page_no', 'ids', 'columns', 'just_questions'];

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
                'user_id'           => 'nulable|exists:users,id',
                'tool_id'           => 'required|exists:ai_tools,id',
                'limit'             => 'nullable|integer',
                'page_no'           => 'nullable|integer',
                'order_by'          => 'nullable|in:asc,desc',
                'ids'               => 'nullable|string',
                'columns'           => 'nullable|string',
                'just_questions'    => 'nullable|in:true,false',
            ]);
            
            $tool_id = $request->tool_id;
            $user_id = $request->user_id ?? false;
            $limit = $request->limit ?? 10;
            $order_by = $request->order_by ?? 'desc';
            $page_no = $request->page_no ?? 1;
            $ids = $request->ids ?? false;
            $columns = $request->columns ?? false;
            $just_questions = $request->just_questions ?? false;
            
            $query = CommentQuestions::query();
            $query->where( 'approved', 1 );
            $query->where( 'tool_id', $tool_id );
            
            $tool_name = AiTool::where( 'id', $tool_id )->select( 'name' )->first();
                
            if ( $just_questions == false ) {

                $query->with([
                    'answer' => function( $query ) {
                        $query->where( 'approved', 1 );
                    }
                ]);
            }

            // if ids provided then filter the queery to only provide the data of 
            if ( $ids != false ) {
            
                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            // if columns provided then just return the selected columns data
            if ( $columns != false ) {
            
                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {
            
                $columns = '*';
            }

            $query->select( $columns );
            $query->orderBy( 'id', $order_by );

            // if limited provided then retiurn only limited data otherwise default limit data.
            if ($limit == -1) {
            
                $totalCommentsCount = $query->count();
                $total_pages = 1;
            } else {
            
                $offset = ($page_no - 1) * $limit;
                
                $totalCommentsCount = $query->count();
                $query->offset($offset);
                $query->limit($limit);

                $total_pages = ceil($totalCommentsCount / $limit);
            }
            
            $comments = $query->get();
            
            // modify data to map the answers of question.
            $comments = $comments->map(function ($comment) {

                $comment->total_answers = CommentAnswers::where('comment_id', $comment->id)->where( 'tool_id', $comment->tool_id )->where( 'approved', 1 )->count();
                $comment->user = User::where( 'id', $comment->user_id )->select( 'id', 'full_name', 'avatar' )->first();
                $comment->answer = collect($comment->answer)->map(function ($answer) {
                    $answer->user = User::where( 'id', $answer->user_id )->select( 'id', 'full_name', 'avatar' )->first();
                    return $answer;
                });
                return $comment;
            });
            
            if ( $just_questions == false ) {
                
                // modify data to map the answers of question.
                $comments = $comments->map(function ($comment) use ( $user_id ) {
    
                    if ( $user_id != false ) {
                        $comment->answer = collect($comment->answer)->map(function ($answer) use ($user_id) {
                            $like_dislike = CommentsLikeDislike::where( 'tool_id', $answer->tool_id )->where( 'user_id', $user_id )->where( 'comment_id', $answer->comment_id )->where( 'comment_answer_id', $answer->id )->first();
                            if ( $like_dislike ) {
                                if ( $like_dislike->like == 0 ) {
                                    $answer->current_user_like = 0;
                                    $answer->current_user_dislike = 1;
                                } 
                                
                                if ( $like_dislike->like == 1 ) {
                                    $answer->current_user_like = 1;
                                    $answer->current_user_dislike = 0; 
                                }
                                
                                return $answer;
                            } else {
                                $answer->current_user_like = 0;
                                $answer->current_user_dislike = 0;
                                return $answer;
                            }
                        });
                    }
                    
                    return $comment;
                });
            }

            // if data is empty then return.
            if ($comments->isEmpty()) {
            
                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }

            // if everything goes well then return success.
            return response()->json([
                'status'        => 'success',
                'message'       => 'Comments fetched successfully.',
                'count'         => $totalCommentsCount,
                'total_pages'   => $total_pages,
                'current_page'  => $page_no,
                'data'          => $comments,
                'tool_name'     => $tool_name->name,
                'asset_url'     => asset( 'public/storage/users-avatars' ),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() // Return validation error message
            ], 422);

        } catch (\Exception $e) {

            \Log::error('Failed to fetch comments: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments. Please try again later.' . $e->getMessage(),
            ], 500);
        }
    }
    
}
