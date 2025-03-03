@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">View User</h1>
</div>

<div class="bg-white border rounded-xl shadow-sm p-6">
   
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border rounded-xl p-3">
        <!-- First Card -->
            <div class="flex flex-wrap">
                <div class="p-4 flex flex-col h-full sm:p-7">
                  
                    @if($users->avatar)
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="{{  $users->avatar }}" alt="avatar" width="70">
                    @else
                        No Avatar
                    @endif
                    <h3 class="text-lg font-bold text-default-800 mb-2">
                        {{ $users->full_name  }} 
                    </h3>
                    <p class="text-xs text-default-500 mb-2">
                        <strong>Ai Interest Level:</strong> {{ $users->user_profile->ai_expertise_level ?? 'Ai Expertise Level Not Found' }}
                    </p>
                    <p class="text-xs text-default-500 mb-2">
                        <strong>Area Of Interest:</strong>  {{ $users->user_profile->area_of_interest ?? 'Area Of Interest Not Found' }}
                    </p>
                    <div class="mb-4">
                        <strong>Industry:</strong>  <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800"> {{ $users->user_profile->industry  ?? 'Industry Not Found' }}</span>
                    </div>
                </div>
            </div>

    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('user.list') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Back To List
        </a>
    </div>
</div>

@endsection