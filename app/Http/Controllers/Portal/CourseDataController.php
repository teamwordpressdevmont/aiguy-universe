<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CategoryCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseDataController extends Controller
{

    // Show All Courses
    public function list(Request $request)
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $categoryCourses = CategoryCourse::all();

        $courses = Course::with('categoryCourses')->when($search, function ($query, $search) {
                        return $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortDirection)
                    ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {

            return response()->json([
                'html' => view('courses.list', compact('courses'))->render(),
                'pagination' => (string) $courses->appends($request->all())->links()
            ]);
        }

        return view('courses.list', compact('categoryCourses', 'courses', 'search', 'sortBy', 'sortDirection'));
    }

    // Show Create Form
    public function addEdit()
    {

        $categoryCourses  = CategoryCourse::all(); 
        $selectedCategories = [];
        return view('courses.add-edit', compact('categoryCourses' , 'selectedCategories'));
    }

    // Store Course Data
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|unique:courses,slug',
                'categoryCourses' => 'required|array',
                'categoryCourses.*' => 'exists:course_category,id',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'members' => 'nullable|integer',
                'affiliate_link' => 'nullable|string',
                'pricing' => 'required|in:free,paid',
            ]);

            // Ensure category is always an array
            $categoryCourses = is_array($request->categoryCourses) ? $request->categoryCourses : [];
            $validatedData = $request->only([ 'slug', 'name', 'description', 'pricing', 'affiliate_link', 'members']);

            // Check if the 'featured' checkbox is checked, and set 'course_filter' accordingly
            $validatedData['courses_filter'] = $request->has('featured') && $request->featured == 'ai_guy' ? 'ai_guy' : null;

            // Upload Images
            if ($request->hasFile('logo')) {

                $image = $request->file('logo');
                $formattedDate = Carbon::now()->timestamp; 
                $extension = $image->getClientOriginalExtension(); // Get file extension
                $imageName = 'course_logo_' . $formattedDate . '.' . $extension; // New file name with formatted date-time
                $logoPath = $image->storeAs('courses-images', $imageName, 'public');
                $validatedData['logo'] = $imageName;
            }

            if ($request->hasFile('cover')) {

                $image = $request->file('cover');
                $formattedDate = Carbon::now()->timestamp; 
                $extension = $image->getClientOriginalExtension(); // Get file extension
                $imageName = 'course_cover_' . $formattedDate . '.' . $extension; // New file name with formatted date-time
                $coverPath = $image->storeAs('courses-images', $imageName, 'public');
                $validatedData['cover'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($validatedData['slug']);

            // Create Course
            $course = Course::create($validatedData);

            // Attach Categories (Only if they exist)
            if (!empty($categoryCourses)) {

                $course->categoryCourses()->attach($categoryCourses);
            }

            DB::commit();

            return redirect()->route('courses.list')->with('success', 'Course Created Successfully');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }
    }

    // Show Edit Form
    public function edit($id)
    {

        $course = Course::with('categoryCourses')->findOrFail($id);
        $categoryCourses  = CategoryCourse::all();
        $selectedCategories = $course->categoryCourses->pluck('id')->toArray();
        return view('courses.add-edit', compact('course', 'categoryCourses' , 'selectedCategories'));
    }
    
    // Update Course
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|unique:courses,slug,' . $id,
                'categoryCourses' => 'required|array',
                'categoryCourses.*' => 'exists:course_category,id',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'affiliate_link' => 'nullable|string',
                'members' => 'nullable|integer',
                'pricing' => 'required|in:free,paid',
            ]);

            $course = Course::findOrFail($id);
            $validatedData = $request->except(['logo', 'cover']);

            // Check if the 'featured' checkbox is checked, and update 'course_filter'
            $validatedData['courses_filter'] = $request->has('featured') && $request->featured == 'ai_guy' ? 'ai_guy' : null;

            // Handle image upload for logo
            if ($request->hasFile('logo')) {

                // Delete old logo
                if ($course->logo) {

                    Storage::disk('public')->delete($course->logo);
                }

                $image = $request->file('logo');
                $formattedDate = Carbon::now()->timestamp; // Standard timestamp format
                $extension = $image->getClientOriginalExtension();
                $imageName = 'course_logo_' . $formattedDate . '.' . $extension;
                $logoPath = $image->storeAs('courses-images', $imageName, 'public');
                $validatedData['logo'] = $imageName;
            }

            // Handle image upload for cover
            if ($request->hasFile('cover')) {

                // Delete old cover
                if ($course->cover) {

                    Storage::disk('public')->delete($course->cover);
                }

                $image = $request->file('cover');
                $formattedDate = Carbon::now()->timestamp; // Standard timestamp format
                $extension = $image->getClientOriginalExtension();
                $imageName = 'course_cover_' . $formattedDate . '.' . $extension;
                $coverPath = $image->storeAs('courses-images', $imageName, 'public');
                $validatedData['cover'] = $imageName;
            }

            $validatedData['slug'] = Str::slug($request->input('slug'));

            $course->update($validatedData);

            if (!empty($request->categoryCourses)) {

                $course->categoryCourses()->sync($request->categoryCourses);
            }

            DB::commit();

            return redirect()->route('courses.list')->with('success', 'Course Updated Successfully');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit AI Tool: ' . $e->getMessage());
        }    
    }

    // Delete Course
    public function destroy($id)
    {

        DB::beginTransaction();
        try {

            $course = Course::findOrFail($id);

            if ($course) {

                if ($course->cover) {

                    Storage::disk('public')->delete($course->cover);
                }

                if ($course->logo) {

                    Storage::disk('public')->delete($course->cover);
                }

                $course->delete();
            }

            DB::commit();

            return redirect()->route('courses.list')->with('success', 'Course Deleted Successfully');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete course: ' . $e->getMessage());
        }  
    }
}