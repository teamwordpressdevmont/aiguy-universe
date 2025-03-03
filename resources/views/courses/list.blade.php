@extends('layouts.app')

@section('content')

   

    @if(session('success'))
        <div class="bg-success text-sm text-white rounded-md p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>    
    @endif

    <div class="flex justify-between items-center mb-6">
        <h4 class="text-3xl font-bold tracking-tight text-gray-900">All Courses</h4>

        <a href="{{ route('courses.addEdit') }}" class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
            Add New  
        </a>            
    </div>
    <div class="relative overflow-x-auto">
        <div class="grid lg:grid-cols-4 gap-6">
            <form id="searchForm" method="GET" action="{{ route('courses.list') }}" class="relative w-full max-w-96 mb-5 flex">
                    <input type="text" name="search" value="{{ request('search') }}" id="table-search" class="form-input rounded-e-none" placeholder="Search for items">
                    <button  type="submit" class="flex items-center justify-center border border-default-200 bg-default-100 px-3 font-semibold rounded-r-md border-s-0">Search</button>
                    <div class="searchCloseIcon input-group-append absolute">
                        <span class="input-group-text close-icon" style="cursor: pointer; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="20px" height="20px"><path d="M 7.71875 6.28125 L 6.28125 7.71875 L 23.5625 25 L 6.28125 42.28125 L 7.71875 43.71875 L 25 26.4375 L 42.28125 43.71875 L 43.71875 42.28125 L 26.4375 25 L 43.71875 7.71875 L 42.28125 6.28125 L 25 23.5625 Z"/></svg>
                        </span>
                    </div>
            </form>            
        </div>
        <div id="table-container" class="border rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left">
                <tr class="text-xs text-gray-700 bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">S.No</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">ID</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                        <a href="{{ route('courses.list', array_merge(request()->all(), ['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Name
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                        <a href="{{ route('courses.list', array_merge(request()->all(), ['sort_by' => 'slug', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}">
                            Slug
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">logo</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Categories</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Type</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Affiliate Link</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Cover Image</th>
                    <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Actions</th>
                </tr>
            </thead>
                <tbody>
                @if($courses->isEmpty())
                <tr class="bg-white border-b   border-gray-200">
                    <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap " colspan="10">
                        No data available.
                    </th>
                </tr>
                @else  
                @foreach($courses as $index =>  $course)
                <tr class="bg-white border-b border-gray-200">
                    <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap text-start">
                        {{ ($courses->currentPage() - 1) * $courses->perPage() + $index + 1 }}
                    </th>
                    <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap text-start">
                        {{ $course->id }}
                    </th>
                    <td scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $course->name }}</td>
                    <td scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $course->slug }}</td>
                    <td class="px-6 py-4">
                        @if($course->logo)
                            <img src="{{ asset('public/storage/courses-images/' . $course->logo) }}" alt="Logo" width="50">
                        @else
                            No Logo
                        @endif                    
                    </td>
                    <td class="px-6 py-4">
                        @if($course->categoryCourses->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-primary/25 text-sky-800">{{ $course->categoryCourses->pluck('name')->join(', ') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-default-800">No Category</span>
                        @endif     
                    </td>
                    <td class="px-6 py-4">{{ ucfirst($course->pricing) }}</td>
                    <td scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $course->affiliate_link }}</td>
                    <td class="px-6 py-4">
                        @if($course->cover)
                            <img src="{{ asset('public/storage/courses-images/' . $course->cover) }}" alt="Cover" width="50">
                        @else
                            No Cover Image
                        @endif                    
                    </td>
                    <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                        <div class="flex gap-3">
                            <a href="{{ route('courses.edit', $course) }}" class="edit_course">
                                <span class="material-symbols-rounded text-2xl text-secondary">edit</span>
                            </a>
                            <a href="{{ route('courses.delete', $course->id) }}" onclick="return confirm('Are you sure you want to delete this tool?');">
                                <span class="material-symbols-rounded text-2xl text-red-800">delete</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4 pagination-container flex" id="pagination-container">
                <div class="site_number_per_item flex items-center gap-5">
                        <label> Items per page:</label>
                        <select class="form-select" id="">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>                    
                </div>
            {{ $courses->appends(request()->query())->links() }}
        </div>
    </div>

@endsection
