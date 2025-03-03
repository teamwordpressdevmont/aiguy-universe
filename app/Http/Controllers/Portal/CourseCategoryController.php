<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseCategoryController extends Controller
{

    // Display the form for creating a new category
    public function addEdit() 
    {

        // Retrieve all categories to list as potential parents.
        $allCategories = CategoryCourse::all();
        return view('course-category.add-edit', compact('allCategories'));
    }

    // Display a list of all categories
    public function list(Request $request) 
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $categories = CategoryCourse::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {

            return response()->json([
                'html' => view('course-category.list', compact('categories'))->render(),
                'pagination' => (string) $categories->appends($request->all())->links()
            ]);
        }

        return view('course-category.list', compact('categories', 'search', 'sortBy', 'sortDirection'));
    }

    // Store a new category in the database
    public function store(Request $request) 
    {

        DB::beginTransaction();

        try {

            $validatedData = $request->validate([
                'name'              => 'required|string|max:255',
                'slug'              => 'required|unique:course_category,slug',
                'description'       => 'nullable|string',
                'icon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_category_id'=> 'nullable|exists:course_category,id',
            ]);

            if ($request->hasFile('icon')) {

                $image = $request->file('icon');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'course_cateogry_icon_' . $formattedDate . '.' . $extension;
                $leftImagePath = $image->storeAs('course-category-images', $imageName, 'public');
                $validatedData['icon'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($validatedData['slug']);
            CategoryCourse ::create($validatedData);

            DB::commit();

            return redirect()->route('course.categories.list')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to Submit Category: ' . $e->getMessage());
        }  
    }

    // Display the form for editing a category
    public function edit($id) 
    {

        $category = CategoryCourse::findOrFail($id); // Retrieve the category by ID
        $allCategories = CategoryCourse::all(); // Retrieve all categories for the parent dropdown
        return view('course-category.add-edit', compact('category', 'allCategories')); // Pass both to the view
    }

    // Update an existing category in the database
    public function update(Request $request, $id) 
    {

        DB::beginTransaction();

        try {

            $validatedData = $request->validate([
                'name'              => 'required|string|max:255',
                'slug'              => 'required|unique:course_category,slug,' . $id,
                'description'       => 'nullable|string',
                'icon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_category_id'=> 'nullable|exists:course_category,id',
            ]);

            $category = CategoryCourse::findOrFail($id); // Retrieve the category by ID

            if ($request->hasFile('icon')) {

                // If a new icon is uploaded, store it and update the path
                if ($category->icon) {

                    Storage::disk('public')->delete($category->icon);
                }

                $image = $request->file('icon');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'course_cateogry_icon_' . $formattedDate . '.' . $extension;
                $leftImagePath = $image->storeAs('course-category-images', $imageName, 'public');
                $validatedData['icon'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($request->input('slug'));
            $category->update($validatedData); // Update the category with validated data

            DB::commit();

            return redirect()->route('course.categories.list')->with('success', 'Category updated successfully.'); // Redirect with success message
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to Submit Category: ' . $e->getMessage());
        }  
    }

    // Delete a category from the database
    public function destroy($id) 
    {

        DB::beginTransaction();

        try {

            $category = CategoryCourse::findOrFail($id); // Retrieve the category by ID

            if ($category) {

                $category->delete(); // Delete the category
            }

            DB::commit();

            return redirect()->route('course.categories.list')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete Category: ' . $e->getMessage());

        }  
    }
}

