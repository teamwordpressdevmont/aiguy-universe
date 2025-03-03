<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserFolder;
use App\Models\UserTool;
use App\Models\User;



class CollectionController extends Controller
{
    public function list(Request $request)
    {
      
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $collection = UserFolder::with('ai_tools' , 'user')
        ->when($search, function ($query) use ($search) {
            $query->where('folder_name', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

         // Check if the request is AJAX
         if ($request->ajax()) {
            return response()->json([
                'html' => view('collection.list', compact('collection'))->render(),
                'pagination' => (string) $collection->appends($request->all())->links()
            ]);
        }

        return view('collection.list' , compact('collection', 'search', 'sortBy', 'sortDirection'));
    }

    public function view(Request $request, $id)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
    
        $folder = UserFolder::with('user')->findOrFail($id);
    
        $aiTools = $folder->ai_tools()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(10); // Pagination added
    
        return view('collection.view', compact('folder', 'aiTools', 'search', 'sortBy', 'sortDirection'));
    }

}
