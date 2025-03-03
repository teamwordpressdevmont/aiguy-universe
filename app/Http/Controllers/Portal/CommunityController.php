<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CommunityQuestions;
use App\Models\CommunityAnswers;
use App\Models\AiToolsCategory;


class CommunityController extends Controller
{
    // Community Question
    public function questions(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $communityQuestions = CommunityQuestions::with('user', 'aiToolCategory')
        ->when($search, function ($query) use ($search) {
            $query->where('question_title', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            
            return response()->json([
                'html' => view('community.questions', compact('communityQuestions'))->render(),
                'pagination' => (string) $communityQuestions->appends($request->all())->links()
            ]);
        }

        return view('community.questions' , compact('communityQuestions' , 'search', 'sortBy', 'sortDirection'));
    }


    // Community Answer List
    public function answers(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $CommunityAnswers = CommunityAnswers::with('user' , 'aiToolCategory' , 'question')
        ->when($search, function ($query) use ($search) {
            $query->where('answer', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            
            return response()->json([
                'html' => view('community.answer', compact('CommunityAnswers'))->render(),
                'pagination' => (string) $CommunityAnswers->appends($request->all())->links()
            ]);
        }

        return view('community.answer' , compact('CommunityAnswers' , 'search', 'sortBy', 'sortDirection'));
    }

    // Community Question Status
    public function updateStatus(Request $request, $id)
    {

        $communityQuestion = CommunityQuestions::findOrFail($id);
        $communityQuestion->approved = $request->input('approved') == 1 ? 1 : 0; // 1 -> Approved, 0 -> Disapproved
        $communityQuestion->save();
        return back()->with('success', 'Community Questions status updated successfully.');
    }


    // Community Answer Status
    public function updateStatusAnswer(Request $request, $id)
    {

        $CommunityAnswer = CommunityAnswers::findOrFail($id);
        $CommunityAnswer->approved = $request->input('approved') == 1 ? 1 : 0; // 1 -> Approved, 0 -> Disapproved
        $CommunityAnswer->save();
        return back()->with('success', 'Community Answer status updated successfully.');
    }

    // Community View
    public function view($id, Request $request)
    {
        $search = $request->input('search');
    
        $communityView = CommunityQuestions::with('answer' , 'user' , 'aiToolCategory')
            ->where('id', $id)
            ->when($search, function ($query) use ($search) {
                return $query->where('question_title', 'like', "%{$search}%");
            })->firstOrFail();
    
        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('community.view', compact('communityView'))->render(),
            ]);
        }
    
        return view('community.view', compact('communityView', 'search'));
    }
}
