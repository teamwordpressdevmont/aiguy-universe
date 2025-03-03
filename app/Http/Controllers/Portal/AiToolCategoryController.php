<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiToolsCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiToolCategoryController extends Controller
{

    // Display a list of all categories
    public function list(Request $request)
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $categories = AiToolsCategory::when($search, function ($query, $search) {
                        return $query->where('name', 'like', "%{$search}%");
                        })
                        ->orderBy($sortBy, $sortDirection)
                        ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {

            return response()->json([
                'html' => view('ai-tools-category.list', compact('categories'))->render(),
                'pagination' => (string) $categories->appends($request->query())->links()
            ]);
        }

        return view('ai-tools-category.list', compact('categories', 'search', 'sortBy', 'sortDirection'));
    }

    // Display the form for creating a new category
    public function addEdit()
    {

        // Retrieve all categories to list as potential parents.
        $allCategories = AiToolsCategory::all();
        return view('ai-tools-category.add-edit', compact('allCategories'));
    }

    // Store a new category in the database
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            $validatedData = $request->validate([
                'name'              => 'required|string|max:255',
                'slug'              =>  'required|unique:ai_tool_category,slug',
                'description'       => 'nullable|string',
                'icon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_category_id'=> 'nullable|exists:ai_tool_category,id',
            ]);

            if ($request->hasFile('icon')) {

                $image = $request->file('icon');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); // Get the file extension
                $imageName = 'ai-tools-category-icon-' . $formattedDate . '.' . $extension;
                $iconPath = $image->storeAs('ai-tools-category-images', $imageName, 'public');
                $validatedData['icon'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($validatedData['slug']);

            AiToolsCategory::create($validatedData);

            DB::commit();

            return redirect()->route('tools.categories.list')->with('success', 'Category created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }    
    }

    // Display the form for editing a category
    public function edit($id)
    {

        $category = AiToolsCategory::findOrFail($id); // Retrieve the category by ID
        $allCategories = AiToolsCategory::all(); // Retrieve all categories for the parent dropdown
        return view('ai-tools-category.add-edit', compact('category', 'allCategories')); // Pass both to the view
    }

    // Update an existing category in the database
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $validatedData = $request->validate([
                'name'              => 'required|string|max:255',
                'slug'              => 'required|unique:ai_tool_category,slug,' . $id,
                'description'       => 'nullable|string',
                'icon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_category_id'=> 'nullable|exists:ai_tool_category,id',
            ]);

            $category = AiToolsCategory::findOrFail($id); // Retrieve the category by ID

            if ($request->hasFile('icon')) {

                // If a new icon is uploaded, store it and update the path
                if ($category->icon) {
                    Storage::disk('public')->delete($category->icon);
                }

                $image = $request->file('icon');
                $formattedDate = Carbon::now()->timestamp; // Standard timestamp format
                $extension = $image->getClientOriginalExtension();
                $imageName = 'ai-tools-category-icon-' . $formattedDate . '.' . $extension;
                $iconPath = $image->storeAs('ai-tools-category-images', $imageName, 'public');
                $validatedData['icon'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($request->input('slug'));

            $category->update($validatedData); // Update the category with validated data

            DB::commit();

            return redirect()->route('tools.categories.list')->with('success', 'Category updated successfully.'); // Redirect with success message
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }
    }

    // Delete a category from the database
    public function destroy($id)
    {

        DB::beginTransaction();
        try {

            $category = AiToolsCategory::findOrFail($id); // Retrieve the category by ID

            if ($category) {

                $category->delete(); // Delete the category
            }

            DB::commit();

            return redirect()->route('tools.categories.list')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}