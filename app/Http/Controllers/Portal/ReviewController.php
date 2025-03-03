<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ToolReview;
use App\Models\User;
use App\Models\AiTool;

class ReviewController extends Controller
{
    public function list(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $review = ToolReview::with('user' , 'tool')
        ->when($search, function ($query) use ($search) {
            $query->where('review', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            
            return response()->json([
                'html' => view('review.list', compact('review'))->render(),
                'pagination' => (string) $review->appends($request->all())->links()
            ]);
        }

        return view('review.list' , compact('review' , 'search', 'sortBy', 'sortDirection'));
    }


    public function updateStatus(Request $request, $id)
    {
        $review = ToolReview::findOrFail($id);
        $review->approved = $request->input('approved') == 1 ? 1 : 0; // 1 -> Approved, 0 -> Disapproved
        $review->save();
        return back()->with('success', 'Review status updated successfully.');
    }
    
}
