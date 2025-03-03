@extends('layouts.app')

@section('content')
@if (session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif


            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"> {{ isset($tool) ? 'Update Course' : 'Add Course' }}</h1>


    <form id="coursesForm" action="{{ isset($course) ? route('courses.update', $course->id) : route('courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($course))
            @method('PUT')  <!-- Use PUT for update -->
        @endif
        
        <div class="grid lg:grid-cols-2 gap-6">

            <div class="">
                <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label> 
                <div class="mt-2">
                    <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                        <input type="text" name="name" id="name" class="form-input" 
                        placeholder="janesmith" value="{{ old('name', $course->name ?? '') }}">
                    </div>
                </div>
            </div>  
            
            <div class="">
                <label for="slug" class="block text-sm/6 font-medium text-gray-900">Slug</label>
                <div class="mt-2">
                    <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                        <input type="text" name="slug" id="slug" class="form-input" 
                        placeholder="" value="{{ old('slug', $course->slug ?? '') }}">
                    </div>
                </div>
            </div>  

        </div>

       <div class="mt-4">
            <label for="categories" class="block text-sm/6 font-medium text-gray-900">Categories</label>
            <div class="mt-2">
                                  
                 <select id="category_id" name="categoryCourses[]" multiple
                    class="form-input">
                    <option value="" disabled selected>Select a category</option>
                           @foreach($categoryCourses as $category_course)
                            <option value="{{ $category_course->id }}" 
                                {{ in_array($category_course->id, $selectedCategories) ? 'selected' : '' }}>
                                {{ $category_course->name }}
                            </option>
                        @endforeach
                    </select>

            </div>
        </div> 
        
        <div class="mt-4 grid lg:grid-cols-2 gap-6">        
            <div class="">
                <label for="pricing" class="block text-sm/6 font-medium text-gray-900">Type</label>
                <div class="mt-2">
                                    
                    <select name="pricing"  class="form-input">
                        <option value="free" {{ isset($course) && $course->pricing == 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ isset($course) && $course->pricing == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>  

                </div>
            </div> 
        
            <div class="">
                <label for="members" class="block text-sm/6 font-medium text-gray-900">Members</label>
                <div class="mt-2">
                    <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                        <input type="number" name="members" id="members" class="form-input" 
                        placeholder="Enter number of members" value="{{ old('members', $course->members ?? '') }}" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4">
            <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
            <div class="mt-2">
                                 
                <textarea name="description" class="textarea_editor block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                    {{ old('description', $course->description ?? '') }}
                    
                </textarea>

            </div>
        </div> 
        
        <div class="form-check mb-4">
            <input id="featured-checkbox" type="checkbox" name="featured" value="ai_guy"
                class="form-checkbox rounded text-primary" {{ isset($course) && $course->courses_filter == 'ai_guy' ? 'checked' : '' }}>
            <label for="featured-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Featured</label>
        </div>




       <div class="mb-4">
            <label for="affiliate_link" class="block text-sm/6 font-medium text-gray-900">Affiliate Link</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                    <input type="text" name="affiliate_link" id="affiliate_link" class="form-input" 
                    placeholder="" value=" {{ old('affiliate_link', $course->affiliate_link ?? '') }}">
                </div>
            </div>
        </div>  

        
        <div class="grid lg:grid-cols-2 gap-6">
                <div class="col-span-full mb-5">
                    <label class="block text-sm/6 font-medium text-gray-900">Logo</label>
                    <div class="mt-2 grid grid-cols-1">
                        <input type="file" name="logo" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg outline-none" id="logoInput">
                        <!-- Image Preview -->
                        @if(isset($course) && $course->logo)
                            <div id="PreviewContainer" class="mt-2  relative">
                                <img  src="{{ asset('public/storage/courses-images/' . $course->logo) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                        @else
                            <div id="PreviewContainer" class="mt-2 hidden relative">
                                <img  src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                         @endif
                    </div>
                </div>
                
                <div class="col-span-full mb-5">
                    <label class="block text-sm/6 font-medium text-gray-900">Cover Image</label>
                    <div class="mt-2 grid grid-cols-1">
                        <input type="file" name="cover" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg outline-none" id="coverInput">
                        <!-- Image Preview -->
                        @if(isset($course) && $course->cover)
                            <div id="PreviewContainer" class="mt-2 relative">
                                <img  src="{{ asset('public/storage/courses-images/' . $course->cover) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                        @else
                            <div id="PreviewContainer" class="mt-2 hidden relative">
                                <img  class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                        @endif
                    </div>
                </div>
        </div>
        
        
        
       
        
                

        <div class="flex items-center justify-end gap-x-6">
            <button type="submit" class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white"> {{ isset($course) ? 'Update' : 'Add' }}</button>
        </div>               

        
    </form>

@endsection
