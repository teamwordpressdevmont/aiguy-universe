@extends('layouts.app')
@section('content')
@if (session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif

    <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"> {{ isset($blog) ? 'Update Blog' : 'Add Blog' }}</h1>
    <div class="">
        <form id="blogForm" action="{{ isset($blog) ? route('blog.update', $blog->id) : route('blog.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if (isset($blog))
                @method('PUT')  <!-- Use PUT for update -->
            @endif


            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block text-sm/6 font-medium text-gray-900">Title</label>
                    <div class="mt-2">
                        <div class="">
                            <input type="text" name="name" id="name" class="form-input" placeholder="janesmith" value="{{ old('name', $blog->name ?? '') }}">
                        </div>
                    </div>
                </div>
                <div>
                    <label for="slug" class="block text-sm/6 font-medium text-gray-900">Slug</label>
                    <div class="mt-2">
                        <div class="">
                            <input type="text" name="slug" id="slug" class="form-input" placeholder="Enter slug" value="{{ old('slug', $blog->slug ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label for="content" class="block text-sm/6 font-medium text-gray-900">Content</label>
                <div class="mt-2">
                    <textarea name="content" id="content" rows="3" class="form-input">
                        {{ old('content', $blog->content ?? '') }}
                    </textarea>
                </div>
            </div>

            <div class="mb-5">
                <label for="category_id" class="block text-sm/6 font-medium text-gray-900">Categories</label>
                <div class="mt-2">
                    <select id="category_id" name="category[]" multiple class="form-input">
                        <option value="" disabled selected>Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>
                                    {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label for="username" class="block text-sm/6 font-medium text-gray-900">Reading Time</label>
                <div class="mt-2">
                    <div class="">
                        <input type="number" name="reading_time" id="reading_time" class="form-input" placeholder="janesmith" value="{{ old('reading_time', $blog->reading_time ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm/6 font-medium text-gray-900">Fearured Image</label>
                    <div class="mt-2">
                        <input type="file" name="featured_image" id="featured_image" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg">
                        @if(isset($blog) && $blog->featured_image)
                            <div id="PreviewContainer" class="mt-2  relative">
                                <img  src="{{ asset('public/storage/blog-images/' . $blog->featured_image) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                        @else
                            <div id="PreviewContainer" class="mt-2 hidden relative">
                                <img  src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm/6 font-medium text-gray-900">Left Image</label>
                        <div class="mt-2">
                        <input type="file" name="left_image" id="left_image" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg">
                        @if(isset($blog) && $blog->left_image)
                                <div id="PreviewContainer" class="mt-2  relative">
                                    <img  src="{{ asset('public/storage/blog-images/' . $blog->left_image) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                    <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                                </div>
                        @else
                                <div id="PreviewContainer" class="mt-2 hidden relative">
                                    <img src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                                    <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                                </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label for="right_text" class="block text-sm/6 font-medium text-gray-900">Right Text</label>
                <div class="mt-2">
                    <textarea name="right_text" id="right_text" rows="3" class="textarea_editor block w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">
                        {{ old('right_text', $blog->right_text ?? '') }}
                    </textarea>

                </div>
            </div>

            <div class="mb-5">
                <label for="middle_text" class="block text-sm/6 font-medium text-gray-900">Middle Text</label>
                <div class="mt-2">
                    <textarea name="middle_text" id="middle_text" rows="3" class="textarea_editor block w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">
                        {{ old('middle_text', $blog->middle_text ?? '') }}
                    </textarea>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm/6 font-medium text-gray-900">Middle Image</label>
                <div class="mt-2">
                    <input type="file" name="middle_image" id="middle_image" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg">
                    @if(isset($blog) && $blog->middle_image)
                        <div id="PreviewContainer" class="mt-2 eee relative">
                            <img  src="{{ asset('public/storage/blog-images/' . $blog->middle_image) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                        </div>
                    @else
                        <div id="PreviewContainer" class="mt-2 hidden relative">
                            <img src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-5">
                <label for="sub_title" class="block text-sm/6 font-medium text-gray-900">Sub Title</label>
                <div class="mt-2">
                    <div class="">
                        <input type="text" name="sub_title" id="sub_title" class="form-input" placeholder="janesmith" value="{{ old('sub_title', $blog->sub_title ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label for="sub_content" class="block text-sm/6 font-medium text-gray-900">Sub Content</label>
                <div class="mt-2">
                    <textarea name="sub_content" id="sub_content" rows="3" class="textarea_editor block w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">
                        {{ old('sub_content', $blog->sub_content ?? '') }}
                    </textarea>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm/6 font-medium text-gray-900">Sub Image</label>
                <div class="mt-2">
                    <input type="file" name="sub_image" id="sub_image" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg">
                    @if(isset($blog) && $blog->sub_image)
                        <div id="PreviewContainer" class="mt-2  relative">
                            <img  src="{{ asset('public/storage/blog-images/' . $blog->sub_image) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <span class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                        </div>
                    @else
                        <div id="PreviewContainer" class="mt-2 hidden relative">
                            <img  src="" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <span  class="CloseIcon absolute top-0 right-0 bg-gray-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer">X</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-6">
                <button type="submit"
                    class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
                    {{ isset($blog) ? 'Update' : 'Add' }} <!-- Dynamically change button text -->
                </button>
            </div>
        </form>
    </div>
@endsection
