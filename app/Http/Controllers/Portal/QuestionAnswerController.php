<?php

namespace App\Http\Controllers\Portal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommentQuestions;
use App\Models\CommentAnswers;
use App\Models\User;
use App\Models\AiTool;


class QuestionAnswerController extends Controller
{
    // Question List
    public function questionsList(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $CommentQuestions = CommentQuestions::with('user' , 'tool')
        ->when($search, function ($query) use ($search) {
            $query->where('comment_title', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            
            return response()->json([
                'html' => view('questions-answer.questions-list', compact('CommentQuestions'))->render(),
                'pagination' => (string) $CommentQuestions->appends($request->all())->links()
            ]);
        }

        return view('questions-answer.questions-list' , compact('CommentQuestions' , 'search', 'sortBy', 'sortDirection'));
    }

    // Question Status
    public function updateStatus(Request $request, $id)
    {
        $CommentQuestions = CommentQuestions::findOrFail($id);
        $CommentQuestions->approved = $request->input('approved') == 1 ? 1 : 0; // 1 -> Approved, 0 -> Disapproved
        $CommentQuestions->save();
        return back()->with('success', 'Review status updated successfully.');
    }


     // Answer List
     public function answerList(Request $request)
     {
       
         $search = $request->input('search');
         $sortBy = $request->input('sort_by', 'id');
         $sortDirection = $request->input('sort_direction', 'desc');
         $CommentAnswer = CommentAnswers::with('user' , 'tool' , 'question')
         ->when($search, function ($query) use ($search) {
             $query->where('comment', 'like', "%{$search}%");
         })
         ->orderBy($sortBy, $sortDirection)
         ->paginate(10);
 
          // Check if the request is AJAX
          if ($request->ajax()) {
             
             return response()->json([
                 'html' => view('questions-answer.answer-list', compact('CommentAnswer'))->render(),
                 'pagination' => (string) $CommentAnswer->appends($request->all())->links()
             ]);
         }
 
         return view('questions-answer.answer-list' , compact('CommentAnswer' , 'search', 'sortBy', 'sortDirection'));
     }

    // Answer Status
    public function updateStatusAnswer(Request $request, $id)
    {
        $CommentAnswer = CommentAnswers::findOrFail($id);
        $CommentAnswer->approved = $request->input('approved') == 1 ? 1 : 0; // 1 -> Approved, 0 -> Disapproved
        $CommentAnswer->save();
        return back()->with('success', 'Review status updated successfully.');
    }

    public function questionsView($id, Request $request)
    {
        $search = $request->input('search');
    
        $question = CommentQuestions::with('answer')
            ->where('id', $id)
            ->when($search, function ($query) use ($search) {
                return $query->where('comment_title', 'like', "%{$search}%");
            })->firstOrFail();
    
        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('questions-answer.question-view', compact('question'))->render(),
            ]);
        }
    
        return view('questions-answer.question-view', compact('question', 'search'));
    }
    

}
