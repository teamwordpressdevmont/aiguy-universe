@extends('layouts.app')

@section('content')

    @if(session('success'))

        <div class="bg-success text-sm text-white rounded-md p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>    

        <!-- <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto cursor-pointer -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div> -->

    @endif



        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Review List</h1>
        </div>
        <div class="relative overflow-x-auto">
            <div class="grid lg:grid-cols-3 gap-6">
            <form id="searchForm" method="GET" action="{{ route('review.list') }}" class="relative w-full max-w-96 mb-5 flex">
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
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">S.No</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">ID</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">User Name</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Tool Name</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">
                                <a href="{{ route('review.list', array_merge(request()->all(), ['sort_by' => 'review', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Review
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Rating</th>
                            <th scope="col" class="px-6 py-3 text-start text-sm text-default-500" width="130">Action</th>                       
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">     
                    @if($review->isEmpty())
                    <tr class="bg-white border-b   border-gray-200">
                        <th scope="row" class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap" colspan="8">
                            No data available.
                        </th>
                    </tr>
                    @else      
                    @foreach($review as $index => $reviews)
                        <tr class="bg-white border-b border-gray-200">
                             <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap text-start">
                                {{ ($review->currentPage() - 1) * $review->perPage() + $index + 1 }}
                            </th>
                            <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap text-start">
                                {{ $reviews->id }}
                            </th>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $reviews->user->full_name }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $reviews->tool->name }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">{{ \Illuminate\Support\Str::limit($reviews->review, 50, '...') }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                {!! str_repeat('⭐', $reviews->rating) !!}
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                <div class="flex gap-4">
                                    <form action="{{ route('review.updateStatus', $reviews->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="approved" value="{{ $reviews->approved == 1 ? 0 : 1 }}" 
                                            class="px-3 py-1 rounded text-white 
                                            {{ $reviews->approved == 1 ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                            {{ $reviews->approved == 1 ? 'Disapprove' : 'Approve' }}
                                        </button>
                                    </form>
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

                {!! $review->appends(request()->query())->links() !!}
            </div>
        </div>

@endsection