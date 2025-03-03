{{--  @extends('layouts.app')


@section('content')

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">View AI Tool</h1>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white border rounded-xl shadow-sm sm:flex">
            <div class="flex-shrink-0 relative w-full rounded-t-xl overflow-hidden pt-[30%] sm:rounded-s-xl sm:max-w-60 md:rounded-se-none md:max-w-xs">
                <img class="size-full absolute top-0 start-0 object-cover" src="{{ asset('public/storage/ai-tools-images/' . $tool->cover) }}" alt="" />
            </div>
            <div class="flex flex-wrap">
                <div class="p-4 flex flex-col h-full sm:p-7">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="{{ asset('public/storage/ai-tools-images/' . $tool->logo) }}" alt="Logo"/ width="70">
                    <h3 class="text-lg font-bold text-default-800 mb-2">
                        {{ $tool->name }}
                    </h3>
                    <p class="text-sm text-default-800 mb-2">
                        Slug: {{ $tool->slug }}
                    </p>
                    <p class="text-sm text-default-800 mb-2">
                        Tagline: {{ $tool->tagline }}
                    </p>
                    <div class="mb-4">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $tool->category->pluck('name')->join(', ') }}</span>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl py-6">
    <a href="{{ route('ai-tools.list') }}" class=" rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Back To List
    </a> 
    </div>    

@endsection  --}}

@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">View AI Tool</h1>
</div>

<div class="bg-white border rounded-xl shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border rounded-xl p-3 items-center">
        <div class="bg-white border rounded-xl shadow-sm sm:flex">
            <div class="flex-shrink-0 relative w-full rounded-t-xl overflow-hidden pt-[30%] sm:rounded-s-xl sm:max-w-60 md:rounded-se-none md:max-w-xs">
                <img class="size-full absolute top-0 start-0 object-cover" src="https://laravel.devmontdigital.co/aiguy/public/storage/ai-tools-images/ai_tool_cover_1740122463.png" alt="Featured image">
            </div>
            <div class="flex flex-wrap">
                <div class="p-4 flex flex-col h-full sm:p-7">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="https://laravel.devmontdigital.co/aiguy/public/storage/ai-tools-images/ai_tool_logo_1740122463.png" alt="Logo" width="70">
                    <h3 class="text-lg font-bold text-default-800 mb-2">
                        Jane Mcdaniel
                    </h3>
                    <p class="text-xs text-default-500 mb-2">
                        <strong>Slug:</strong> adipisicing-ipsum-n
                    </p>
                    <p class="text-xs text-default-500 mb-2">
                        <strong>Tagline:</strong> Quas veritatis dolor
                    </p>
                    <div class="mb-4">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-commerce</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <div class="mb-2">
                    <h4 class="text-md font-semibold text-gray-900">Release Date</h4>
                    <p class="text-sm text-gray-700">{{ $tool->release_date }}</p>
                </div>
                <div class="mb-2">
                    <h4 class="text-md font-semibold text-gray-900">Verified Status</h4>
                    <p class="text-sm text-gray-700">{{ $tool->verified_status }}</p>
                </div>
                <div class="mb-2">
                    <h4 class="text-md font-semibold text-gray-900">Integration Capabilities</h4>
                    <p class="text-sm text-gray-700">{{ $tool->integration_capabilities }}</p>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-900">Payment Text</h4>
                    <p class="text-sm text-gray-700">{{ $tool->payment_text }}</p>
                </div>
            </div>
            <div>
                <div class="mb-2">
                    <h4 class="text-md font-semibold text-gray-900">Type</h4>
                    <p class="text-sm text-gray-700">type</p>
                </div>
                <div class="mb-2">
                    <h4 class="text-md font-semibold text-gray-900">Website Link</h4>
                    <a href="{{ $tool->website_link }}" class="text-indigo-600 hover:underline">{{ $tool->website_link }}</a>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-900">Platform Compatibility</h4>
                    <p class="text-sm text-gray-700">{{ $tool->platform_compatibility }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 my-6 mb-5">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">{{ $tool->short_description_heading }}</h4>
            </div>
            <div class="md:col-span-4">
                <p class="text-sm text-gray-700">{{ $tool->short_description }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3 my-6 mb-5">
            <div>
                <h4 class="text-md font-semibold text-gray-900">{{ $tool->description_heading }}</h4>
            </div>
            <div>
                <p class="text-sm text-gray-700">{{ $tool->description }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3 my-6 mb-5">
            <div>
                <h4 class="text-md font-semibold text-gray-900">Key Features</h4>
            </div>
            <div>
                <p class="text-sm text-gray-700">{{ $tool->key_features }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
            <div>
                <h4 class="text-md font-semibold text-gray-900">Pros</h4>
                <ul class="list-disc list-inside text-sm text-gray-700">pros</ul>
            </div>
            <div>
                <h4 class="text-md font-semibold text-gray-900">Cons</h4>
                <ul class="list-disc list-inside text-sm text-gray-700">cons</ul>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
            <div>
                <h4 class="text-md font-semibold text-gray-900">Voila Description</h4>
                <p class="text-sm text-gray-700">{{ $tool->voila_description }}</p>
            </div>
            <div>
                <h4 class="text-md font-semibold text-gray-900">Long Description</h4>
                <p class="text-sm text-gray-700">{{ $tool->long_description }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4 w-full bg-white border border-gray-200 rounded-lg shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3 my-6 mb-5">
            <div>
                <h4 class="text-md font-semibold text-gray-900">Ai Tool Filter</h4>
                <p class="text-sm text-gray-700">{{ $tool->aitool_filter }}</p>
            </div>
        </div>
    </div>
    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('ai-tools.list') }}" class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
            Back To List
        </a>
    </div>
</div>

@endsection