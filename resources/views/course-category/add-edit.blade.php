@extends('layouts.app')

@section('content')
@if (session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"> {{ isset($category) ? 'Update Course Category' : 'Add Course Category' }}</h1>
        
        <form id="courseCategory" action="{{ isset($category) ? route('course.categories.update', $category->id) : route('course.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
    
            @if(isset($category))
    
            @method('PUT')
    
            @endif
    
            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Name Field -->
                <div class="">
                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                    <div class="mt-2">
                        <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <input type="text" name="name" id="name"
                                class="form-input"
                                placeholder="Category Name" value="{{ isset($category) ? $category->name : '' }}"> <!-- Pre-fill if editing -->
                        </div>
                    </div>
                </div>
                {{--  Slug Field  --}}
                <div class="">
                    <label for="slug" class="block text-sm/6 font-medium text-gray-900">Slug</label>
                    <div class="mt-2">
                        <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <input type="text" name="slug" id="slug"
                                class="form-input"
                                placeholder="category slug" value="{{ old('slug', $category->slug ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Description Field -->
            <div class="">
                <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                <div class="mt-2">
                    <div class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300">
                        <textarea name="description" id="description"
                                  class="textarea_editor block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6"
                                  cols="30" rows="10">{{ isset($category) ? $category->description : '' }}</textarea> <!-- Pre-fill if editing -->
                    </div>
                </div>
            </div>
    
             <!-- Icon Field -->
             <div class="col-span-full mb-5">
                <label class="block text-sm/6 font-medium text-gray-900">Icon</label>
                <div class="mt-2 grid grid-cols-1">
                    <input type="file" name="icon" accept="image/*" class="w-full p-2 border border-gray-300 rounded-lg outline-none">
                     @if(isset($category) && $category->icon)
                      <div id="PreviewContainer" class="mt-2  relative">
                          <img  src="{{ asset('public/storage/course-category-images/' . $category->icon) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                          <span  class="CloseIcon  absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                      </div>
                      @else
                          <div id="PreviewContainer" class="mt-2 hidden relative">
                              <img  src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                              <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                          </div>
                       @endif
                    
                </div>
            </div>
    
            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-x-6">
                <button type="submit"
                        class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
                    {{ isset($category) ? 'Update' : 'Add' }}
                </button>
            </div>
        </form>


@endsection
