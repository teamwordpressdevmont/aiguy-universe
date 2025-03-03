@extends('layouts.app')

@section('content')

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">
                {{ session('success') }}
            </span>
        </div>
    @endif




        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Course Category List</h1>
            <a href="{{ route('course.categories.addEdit') }}" class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
                Add New
            </a>
        </div>


        <div class="relative">

            <div class="grid lg:grid-cols-4 gap-6">
                <form id="searchForm" method="GET" action="{{ route('course.categories.list') }}" class="relative w-full max-w-96 mb-5 flex">
                    <input type="text" name="search" value="{{ request('search') }}" id="table-search" class="form-input rounded-e-none" placeholder="Search for items">
                    <button  type="submit" class="flex items-center justify-center border border-default-200 bg-default-100 px-3 font-semibold rounded-r-md border-s-0">Search</button>
                    <div class="searchCloseIcon input-group-append absolute">
                        <span class="input-group-text close-icon" style="cursor: pointer; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="20px" height="20px"><path d="M 7.71875 6.28125 L 6.28125 7.71875 L 23.5625 25 L 6.28125 42.28125 L 7.71875 43.71875 L 25 26.4375 L 42.28125 43.71875 L 43.71875 42.28125 L 26.4375 25 L 43.71875 7.71875 L 42.28125 6.28125 L 25 23.5625 Z"/></svg>
                        </span>
                    </div>
                </form>
            </div>            

            <div id="table-container" class="border rounded-lg overflow-x-auto ">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">S.no</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">ID</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                <a href="{{ route('course.categories.list', array_merge(request()->all(), ['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Name
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                Slug
                            </th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                Description
                            </th>

                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                Icon
                            </th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @if($categories->isEmpty())
                    <tr class="bg-white border-b   border-gray-200">
                        <th scope="row" class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap " colspan="7">
                            No data available.
                        </th>
                    </tr>
                    @else  
                    @foreach($categories as $index => $category)
                        <tr class="bg-white border-b border-gray-200">
                            <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap text-start">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}
                            </th>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-start">
                                {{ $category->id }}
                            </th>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                {!! html_entity_decode($category->description) !!}
                            </td>

                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                @if($category->icon)
                                    <img src="{{ asset('public/storage/course-category-images/' . $category->icon) }}" alt="Logo" width="50">
                                @else
                                    No Icon
                                @endif
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                <div class="flex gap-4">
                                    <a href="{{ route('course.category.edit', $category->id) }}">
                                        <span class="material-symbols-rounded text-2xl text-secondary">edit</span>
                                    </a>
                                    <a href="{{ route('course.category.delete', $category->id) }}" onclick="return confirm('Are you sure you want to delete this Course?');">
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

                {{ $categories->appends(request()->query())->links() }}
            </div>            
        </div>


@endsection
