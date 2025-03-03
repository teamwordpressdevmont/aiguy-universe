@extends('layouts.app')
@section('content')


    <div class="lg:col-span-2">
        <div class="bg-white border rounded-xl shadow-sm sm:flex">
            <div class="flex-shrink-0 relative w-full rounded-t-xl overflow-hidden pt-[30%] sm:rounded-s-xl sm:max-w-60 md:rounded-se-none md:max-w-xs">
                <img class="size-full absolute top-0 start-0 object-cover" src="{{ asset('public/storage/blog-images/' . $blog->featured_image) }}" alt="Featured image">
            </div>
            <div class="flex flex-wrap">
                <div class="p-4 flex flex-col h-full sm:p-7">
                    <div class="">
                        <p class="text-xs text-default-500">
                        {{ $blog->reading_time }} min read
                        </p>
                    </div>
                    <h3 class="text-lg font-bold text-default-800 mb-2">
                        {{ $blog->heading }} Card title
                    </h3>
                    <div class="mb-4">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $blog->category->pluck('name')->join(', ') }}</span>
                    </div>                    
                    <p class="mt-1 text-default-500">
                        {!! html_entity_decode($blog->right_text) !!}
                    </p>
                    

                </div>
            </div>
        </div>
    </div>


    {{--  <div class="mt-5 mx-auto max-w-7xl bg-white border border-gray-200 rounded-lg shadow-sm mx-auto">
        <div class="flex flex-col pb-10">
            <span class="text-sm text-gray-500">{{ $blog->category->pluck('name')->join(', ') }}</span>
            <img class="" src="{{ asset('public/storage/blog-images/' . $blog->featured_image) }}" alt="Featured image"/>
            <h5 class="mb-1 text-xl font-medium text-gray-900">{{ $blog->heading }}</h5>
            <h5 class="mb-1 text-xl font-medium text-gray-900">{{ $blog->reading_time }}</h5>
            <p class="mb-1 text-xl font-medium text-gray-900">{{ $blog->content }}</p>
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="{{ asset('public/storage/blog-images/' . $blog->left_image) }}" alt="Left image"/>
            <p class="mb-1 text-xl font-medium text-gray-900">{!! html_entity_decode($blog->right_text) !!}</p>
            <p class="mb-1 text-xl font-medium text-gray-900">{!! html_entity_decode($blog->middle_text) !!}</p>
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="{{ asset('public/storage/blog-images/' . $blog->middle_image) }}" alt="Left image"/>
            <h5 class="mb-1 text-xl font-medium text-gray-900">{{ $blog->sub_title }}</h5>
         <p class="mb-1 text-xl font-medium text-gray-900">{!! html_entity_decode($blog->sub_content) !!}</p>
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="{{ asset('public/storage/blog-images/' . $blog->sub_image) }}" alt="Left image"/>
            <div class="flex mt-4 md:mt-6">
            </div>
        </div>
    </div>  --}}

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">

        {{-- Blog Content --}}
        <!-- <p class="text-gray-700 mt-4">{{ $blog->content }}</p> -->

        {{-- Left Image - Right Text --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 my-6 mb-5">
            <!-- Image Column (40%) -->
            <div class="md:col-span-2 flex justify-start">
                <img class="rounded-lg shadow-lg w-full" src="{{ asset('public/storage/blog-images/' . $blog->left_image) }}" alt="Left image" />
            </div>

            <!-- Text Column (60%) -->
            <div class="md:col-span-4">
                <p class="text-gray-800 text-lg">{!! html_entity_decode($blog->right_text) !!}</p>
            </div>
        </div>



        {{-- Center Content --}}
        <div class="my-8 mb-5">
            <p class="text-gray-700 text-lg">{!! html_entity_decode($blog->middle_text) !!}</p>
            <img class="w-full my-8 rounded-lg shadow-lg" src="{{ asset('public/storage/blog-images/' . $blog->middle_image) }}" alt="Middle image"/>
        </div>

        {{-- Left Text - Right Image --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 my-6 mb-5">
            <div class="md:col-span-4">
                <p class="text-gray-800 text-lg">{!! html_entity_decode($blog->sub_content) !!}</p>
            </div>
            <div class="md:col-span-2 flex justify-start">
                <img class="rounded-lg shadow-lg object-cover" src="{{ asset('public/storage/blog-images/' . $blog->sub_image) }}" alt="Sub image"/>
            </div>
        </div>
    </div>


    
@endsection