<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\CommunityQuestions;
use App\Models\CommunityAnswers;
use App\Models\CommunityLikeDislike;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    
    public function addQuestionAnswersCommunity( Request $request )
    {
        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'category_id', 'question_title', 'question_brief', 'question_id', 'parent_answer_id', 'answer'];

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
                'category_id'       => 'required|exists:ai_tool_category,id',
                'question_title'    => 'nullable|string',
                'question_brief'    => 'nullable|string',
                'question_id'       => 'nullable|exists:community_questions,id',
                'parent_answer_id'  => 'nullable|exists:community_answers,id',
                'answer'            => 'nullable|string',
            ]);
            
            $user_id            = $request->user_id;
            $category_id        = $request->category_id;
            $question_title     = $request->question_title ?? false;
            $question_brief     = $request->question_brief ?? false;
            $question_id        = $request->question_id ?? false;
            $parent_answer_id   = $request->parent_answer_id ?? false;
            $answer             = $request->answer ?? false;
            
            //if question id provided then perform the answer.
            if ( $question_id != false ) {
                
                //if answer is empty then return.
                if ( $answer == false ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Answer should not be empty.',
                    ], 400);
                }
                
                //if parent_id is provided then cater as child answer otherwise parent_answer
                if ( $parent_answer_id != false ) {

                    $data = [
                        'user_id'               => $user_id,
                        'category_id'           => $category_id,
                        'community_question_id' => $question_id,
                        'parent_answer_id'      => $parent_answer_id,
                        'answer'                => $answer,
                        'approved'              => 1,
                    ];
                } else {
                    
                    $data = [
                        'user_id'               => $user_id,
                        'category_id'           => $category_id,
                        'community_question_id' => $question_id,
                        'parent_answer_id'      => 0,
                        'answer'                => $answer,
                        'approved'              => 1,
                    ];
                }
                
                //store answer in database
                $answer = CommunityAnswers::create( $data );
                DB::commit();
                
                //if everything goes well then return success.
                return response()->json([
                    'success' => true,
                    'message' => 'Answer submitted successfully.',
                ], 200);

            }
            
            //if question_id is not provided then cater as question.
            if ( $question_id == false ) {
                
                //if question is empty then return.
                if ( $question_title == false ) {
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Question title should not be empty.',
                    ], 400);
                }
                
                //question brief is empty then return.
                if ( $question_brief == false ) {
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Question brief should not be empty.',
                    ], 400);
                }

                $data = [
                    'user_id'           => $user_id,
                    'category_id'           => $category_id,
                    'question_title'     => $question_title,
                    'question_brief'   => $question_brief,
                    'approved'          => 1,
                ];
                
                //store question in database.
                $question = CommunityQuestions::create( $data );
                DB::commit();
                
                //if everything goes well return with success.
                return response()->json([
                    'success' => true,
                    'message' => 'Question submitted successfully.',
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
            \Log::error('Failed to submit: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit. Please try again later.' .$e->getMessage(),
            ], 500);
        }
    }

    public function addLikeDislikeOnAnswers( Request $request )
    {
        DB::beginTransaction();
        try {

            // Get only the expected parameters
            $allowedParams = ['user_id', 'category_id', 'question_id', 'community_answer_id', 'action'];

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
                'category_id'           => 'required|exists:ai_tool_category,id',
                'question_id'           => 'required|exists:community_questions,id',
                'community_answer_id'   => 'required|exists:community_answers,id',
                'action'                => 'required|in:like,dislike',
            ]);

            $user_id                = $request->user_id;
            $category_id            = $request->category_id;
            $question_id            = $request->question_id ?? false;
            $community_answer_id    = $request->community_answer_id ?? false;
            $action                 = $request->action ?? false;

            //Get answer from database.
            $answer = CommunityAnswers::where( 'category_id', $category_id )
                                    ->where( 'community_question_id', $question_id )
                                    ->where( 'id', $community_answer_id )
                                    ->first();

            //if answer not found return.
            if ( !$answer ) {

                return response()->json([
                    'success' => false,
                    'message' => 'Selected question id or answer id is invalid',
                ], 400);
            }

            // if action is like then add like to an answer.
            if ( $action == 'like' ) {

                //Get previous entry if any.
                $previousEntry = CommunityLikeDislike::where( 'user_id', $user_id )
                                ->where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->first();

                //if previous entry found.
                if ( $previousEntry ) {

                    $like = $previousEntry->like;
                    $dislike = $previousEntry->dislike;
                    
                    //if there is an previous entry with like then return.
                    if ( $like == 1 ) {

                        return response()->json([
                            'success' => false,
                            'message' => 'You have already liked this answer'
                        ], 400);
                    }

                    // if previous entry is dislike then modify it to make it like
                    if ( $dislike == 1 ) {
                        $previousEntry->like = 1;
                        $previousEntry->dislike = 0;
                        $previousEntry->save();
                    }

                } else {

                    // if previous entry is not found then just add like
                    $data = [
                        'user_id'   => $user_id,
                        'category_id'   => $category_id,
                        'question_id'    => $question_id,
                        'community_answer_id' => $community_answer_id,
                        'like'  => 1,
                        'dislike'   => 0,
                    ];
    
                    $data = CommunityLikeDislike::create( $data );   
                }

                // get total like counts of answer
                $like_counts = CommunityLikeDislike::where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->where( 'like', 1 )
                                ->count();

                //get total dislike count of answer. 
                $dislike_counts = CommunityLikeDislike::where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->where( 'dislike', 1 )
                                ->count();

                //update total like and dislike count into tool table.
                $answer->like_count = $like_counts;
                $answer->dislike_count = $dislike_counts;
                $answer->save();
                DB::commit();

                // if everything goes well then return with success
                return response()->json([
                    'success' => true,
                    'message' => 'Answer liked successfully.'
                ], 200);

            }

            //if action is dislike
            if ( $action == 'dislike' ) {

                // get previous entry
                $previousEntry = CommunityLikeDislike::where( 'user_id', $user_id )
                                ->where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->first();

                // if previous found 
                if ( $previousEntry ) {
                    $like = $previousEntry->like;
                    $dislike = $previousEntry->dislike;
                    
                    // if previous is dislike then return.
                    if ( $dislike == 1 ) {
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'You have already disliked this answer'
                        ], 400);
                    }

                    // if previous entry is like then modify it and make it dislike
                    if ( $like == 1 ) {

                        $previousEntry->like = 0;
                        $previousEntry->dislike = 1;
                        $previousEntry->save();
                    }

                } else {

                    // if previous entry not found then just dislike the answer.
                    $data = [
                        'user_id'   => $user_id,
                        'category_id'   => $category_id,
                        'question_id'    => $question_id,
                        'community_answer_id' => $community_answer_id,
                        'like'  => 0,
                        'dislike'   => 1,
                    ];
    
                    $data = CommunityLikeDislike::create( $data );   
                }

                //get total like count of answer.
                $like_counts = CommunityLikeDislike::where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->where( 'like', 1 )
                                ->count();

                //get total dislike count of answer
                $dislike_counts = CommunityLikeDislike::where( 'category_id', $category_id )
                                ->where( 'question_id', $question_id )
                                ->where( 'community_answer_id', $community_answer_id )
                                ->where( 'dislike', 1 )
                                ->count();

                // update total like and dislike count of answer and store it in answer table.
                $answer->like_count = $like_counts;
                $answer->dislike_count = $dislike_counts;
                $answer->save();
                DB::commit();

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
            $allowedParams = ['user_id', 'limit', 'order_by', 'page_no', 'ids', 'columns', 'just_questions', 'category_id', 'search'];

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
                'limit'             => 'nullable|integer',
                'page_no'           => 'nullable|integer',
                'order_by'          => 'nullable|in:asc,desc',
                'ids'               => 'nullable|string',
                'columns'           => 'nullable|string',
                'just_questions'    => 'nullable|string',
                'category_id'       => 'nullable|string',
                'search'            => 'nullable|string',
            ]);
    
            $user_id = $request->user_id ?? false;
            $limit = $request->limit ?? 10;
            $order_by = $request->order_by ?? 'desc';
            $page_no = $request->page_no ?? 1;
            $ids = $request->ids ?? false;
            $columns = $request->columns ?? false;
            $just_questions = $request->just_questions ?? false;
            $category_id = $request->category_id ?? false;
            $search = $request->search ?? false;

            $query = CommunityQuestions::query();
            
            $query->where( 'approved', 1 );
            
            //if category ids provided then convert it into array from string.
            if ( $category_id != false ) {

                $category_id = explode(',', $category_id); // Convert comma-separated IDs into an array
                $query->whereIn('category_id', $category_id);
            }

            // if search provided then filter the query
            if ( $search != false ) {

                $query->where('question_title', 'LIKE', "%{$search}%");
            }

            // if just question not true then add child answer with the questions.
            if ($just_questions == false) {

                $query->with([
                    'answer' => function ($query) {
                        $query->where('parent_answer_id', 0)->where('approved', 1)->with([
                            'childAnswers'  => function ( $query ) {
                                $query->where( 'approved', 1 );  
                            }
                        ]);
                    }
                ]);
            }

            // if ids provided then filter the query to return only provided ids data
            if ( $ids != false ) {

                $ids = explode(',', $ids); // Convert comma-separated IDs into an array
                $query->whereIn('id', $ids);
            }

            // if columns provided then return only selected columns
            if ( $columns != false ) {
            
                $columns = explode(',', str_replace(["'", '"'], '', $columns));
                $columns = array_map('trim', $columns);
            } else {
            
                $columns = '*';
            }

            $query->select( $columns );
            $query->orderBy( 'id', $order_by );

            //if limit proided then limit the data otherwise default limit.
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

            // if data is empty then return and show the message.
            if ($comments->isEmpty()) {

                return response()->json(['success' => false, 'message' => 'No record found'], 200);
            }

            // modify data to map the answers of question.
            $comments = $comments->map(function ($comment) {

                $comment->total_replies = CommunityAnswers::where('community_question_id', $comment->id)->where( 'category_id', $comment->category_id )->where( 'approved', 1 )->count();
                $comment->user = User::where( 'id', $comment->user_id )->select( 'id', 'full_name', 'avatar' )->first();
                return $comment;
            });
            
            if (!$just_questions) {
                $comments = $comments->map(function ($comment) use ($user_id) {
                    if ($user_id) {
                        $comment->answer = collect($comment->answer)->map(function ($answer) use ($user_id) {
                            return $this->processAnswer($answer, $user_id);
                        });
                    }
                    return $comment;
                });
            }


            // if everything goes well then return the success.
            return response()->json([
                'status'        => 'success',
                'message'       => 'Community Questions/Answers fetched successfully.',
                'count'         => $totalCommentsCount,
                'total_pages'   => $total_pages,
                'current_page'  => $page_no,
                'data'          => $comments,
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
    
    private function processAnswer($answer, $user_id)
    {
        // Fetch like/dislike status for this answer
        $like_dislike = CommunityLikeDislike::where('user_id', $user_id)
            ->where('category_id', $answer->category_id)
            ->where('question_id', $answer->community_question_id)
            ->where('community_answer_id', $answer->id)
            ->first();

        // Assign like/dislike status
        $answer->current_user_like = $like_dislike && $like_dislike->like == 1 ? 1 : 0;
        $answer->current_user_dislike = $like_dislike && $like_dislike->like == 0 ? 1 : 0;
        $answer->user = User::where( 'id', $answer->user_id )->select( 'id', 'full_name', 'avatar' )->first();
        // Ensure child answers are processed recursively
        if ($answer->childAnswers) {
            $answer->child_answers = $answer->childAnswers->map(function ($child_answer) use ($user_id) {
                return $this->processAnswer($child_answer, $user_id);
            });
        }
        return $answer;
    }
    
}
