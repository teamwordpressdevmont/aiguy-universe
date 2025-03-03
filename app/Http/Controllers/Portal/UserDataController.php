<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;



class UserDataController extends Controller
{
    public function list(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $user = User::query()
        ->when($search, function ($query) use ($search) {
            $query->where('full_name', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            
            return response()->json([
                'html' => view('user.list', compact('user'))->render(),
                'pagination' => (string) $user->appends($request->all())->links()
            ]);
        }

        return view('user.list' , compact('user' , 'search', 'sortBy', 'sortDirection'));
    }

    public function view($id)
    {

        $users = User::with('user_profile')->findOrFail($id);
        return view('user.view', compact('users'));
    }
}
