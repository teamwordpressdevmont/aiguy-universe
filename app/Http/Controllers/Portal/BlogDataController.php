<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogDataController extends Controller
{
    // Display a listing of the blog posts
    public function addEdit() {
        $categories = BlogCategory::all();
        $selectedCategories = [];
        return view('blog.add-edit', compact('categories' , 'selectedCategories' ));
    }

    public function store(Request $request) {

        DB::beginTransaction();
        try {

            $request->merge(['user_id' => 37]);
        
            $request->validate([
                    'user_id'        => 'required|exists:users,id',
                    'category'       => 'nullable|array',
                    'category.*'     => 'exists:blog_category,id',
                    'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'name'           => 'required|string|max:255',
                    'slug'           =>  'required|unique:blogs,slug',
                    'reading_time'   => 'nullable|string|max:255',
                    'content'        => 'nullable|string',
                    'left_image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'right_text'     => 'nullable|string',
                    'middle_text'    => 'nullable|string',
                    'middle_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'sub_title'      => 'nullable|string|max:255',
                    'sub_content'    => 'nullable|string',
                    'sub_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        
            $categories = is_array($request->category) ? $request->category : [];
        
            $validatedData = $request->only([ 'user_id' , 'name', 'slug', 'reading_time', 
            'content', 'right_text', 'middle_text', 'sub_title', 'sub_content']);



            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_featured_image_' . $formattedDate . '.' . $extension;
                $imagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['featured_image'] = $imageName;
            }
            
            if ($request->hasFile('left_image')) {
                $image = $request->file('left_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_left_image_' . $formattedDate . '.' . $extension;
                $leftImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['left_image'] = $imageName;
            }
            
            if ($request->hasFile('middle_image')) {
                $image = $request->file('middle_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_middle_image_' . $formattedDate . '.' . $extension;
                $middleImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['middle_image'] = $imageName;
            }
            
            if ($request->hasFile('sub_image')) {
                $image = $request->file('sub_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_sub_image_' . $formattedDate . '.' . $extension;
                $subImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['sub_image'] = $imageName;
            }
            
            $validatedData['slug'] = Str::slug($validatedData['slug']);


            // Save to Database
            $blog = Blog::create($validatedData);
            
            if (!empty($categories)) {
                $blog->category()->attach($categories);
            }

            DB::commit();
            return redirect()->route('blog.list')->with('success', 'Blog submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit Blog: ' . $e->getMessage());
        }
    }

    // Blog List
    public function list(Request $request) {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $categories = BlogCategory::all();
        $blog = Blog::with('category')
                    ->when($search, function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortDirection)
                    ->paginate(10);

        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('blog.list', compact('blog'))->render(),
                'pagination' => (string) $blog->appends($request->all())->links()
            ]);
        }
        
        
        return view('blog.list', compact('categories', 'blog', 'search', 'sortBy', 'sortDirection'));

    }

    // Blog Edit
    public function edit($id) {
        $blog = Blog::with('category')->findOrFail($id);
        $categories = BlogCategory::all();
        $selectedCategories = $blog->category->pluck('id')->toArray();
        return view('blog.add-edit', compact('blog', 'categories' , 'selectedCategories'));
    }

    // Blog Update
    public function update(Request $request, $id) {

        DB::beginTransaction();
        try {
        
            $request->validate([
                'featured_image' => 'image|mimes:jpg,jpeg,png|max:2048',
                'category'       => 'required|array',
                'category_id'    => 'exists:blog_category,id',
                'name'           => 'required|string|max:255',
                'slug'           => 'required|unique:blogs,slug,' . $id,
                'reading_time'   => 'nullable|integer',
                'content'        => 'nullable|string',
                'left_image'     => 'image|mimes:jpg,jpeg,png|max:2048',
                'right_text'     => 'nullable|string',
                'middle_text'    => 'nullable|string',
                'middle_image'   => 'image|mimes:jpg,jpeg,png|max:2048',
                'sub_title'      => 'nullable|string',
                'sub_content'    => 'nullable|string',
                'sub_image'      => 'image|mimes:jpg,jpeg,png|max:2048',

            ]);
            

            $blog = Blog::findOrFail($id);
            
            $validatedData = $request->except(['featured_image', 'left_image' , 'middle_image' , 'sub_image' ]);
            $validatedData['slug'] = Str::slug($request->input('slug'));

            if ($request->hasFile('featured_image')) {
                
                // Delete old featured_image
                if ($blog->featured_image) {
                    
                    Storage::disk('public')->delete($blog->featured_image);
                }
                
                $image = $request->file('featured_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_featured_image_' . $formattedDate . '.' . $extension;
                $imagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['featured_image'] = $imageName;
            }
            
            if ($request->hasFile('left_image')) {
                
                // Delete old left_image
                if ($blog->left_image) {
                    
                    Storage::disk('public')->delete($blog->left_image);
                }
                
                $image = $request->file('left_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_left_image_' . $formattedDate . '.' . $extension;
                $leftImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['left_image'] = $imageName;
            }
            
            if ($request->hasFile('middle_image')) {
                
                // Delete old left_image
                if ($blog->middle_image) {
                    
                    Storage::disk('public')->delete($blog->middle_image);
                }
                
                $image = $request->file('middle_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_middle_image_' . $formattedDate . '.' . $extension;
                $middleImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['middle_image'] = $imageName;
            }
            
            if ($request->hasFile('sub_image')) {
                
                // Delete old left_image
                if ($blog->sub_image) {
                    
                    Storage::disk('public')->delete($blog->sub_image);
                }
                
                $image = $request->file('sub_image');
                $formattedDate = Carbon::now()->timestamp;
                $extension = $image->getClientOriginalExtension(); 
                $imageName = 'blog_sub_image_' . $formattedDate . '.' . $extension;
                $subImagePath = $image->storeAs('blog-images', $imageName, 'public');
                $validatedData['sub_image'] = $imageName;
            }

            

            $blog->update($validatedData);
            
            if (!empty($request->category)) {
                $blog->category()->sync($request->category);
            }

            DB::commit();
            return redirect()->route('blog.list')->with('success', 'Blog updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to Blog: ' . $e->getMessage());
        }
    }

    // Blog Delete
    public function destroy($id) {
        DB::beginTransaction();
        try {
            $blog = Blog::findOrFail($id);
            
            if (!is_null($blog)){
                
                $blog->delete();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Blog deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete blog: ' . $e->getMessage());
        }  
    }

    // Blog View
    public function view($id) {
        $blog = Blog::findOrFail($id);
        return view('blog.view', compact('blog'));
    }

}
