<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactDataController extends Controller 
{

    public function list(Request $request)
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        $contacts = Contact::when($search, function ($query, $search) {
                            return $query->where('name', 'like', "%{$search}%");
                        })
                        ->orderBy($sortBy, $sortDirection)
                        ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {

            return response()->json([
                'html' => view('contact.list', compact('contacts'))->render(),
                'pagination' => (string) $contacts->appends($request->query())->links()
            ]);
        }

        return view('contact.list', compact('contacts', 'search', 'sortBy', 'sortDirection'));
    }
}