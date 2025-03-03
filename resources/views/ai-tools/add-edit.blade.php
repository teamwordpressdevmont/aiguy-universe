@extends('layouts.app')

@section('content')

@if (session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif

            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"> {{ isset($tool) ? 'Update AI Tool' : 'Add AI Tool' }}</h1>
                <form id="aiToolForm" action="{{ isset($tool) ? route('tools.update', $tool->id) : route('tools.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @if (isset($tool))
                        @method('PUT')  <!-- Use PUT for update -->
                    @endif
                 
                 
                    <input type="hidden" name="added_by" value="{{ auth()->id() }}">
                    
                    <div class="grid lg:grid-cols-2 gap-6">

                        <div> 
                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                            <div class="mt-2">
                                <div class="">
                                    <input type="text" name="name" id="name" class="form-input" placeholder="Enter tool name" value="{{ old('name', $tool->name ?? '') }}">
                                </div>
                                @error('name')
                                    <div class="text-red-600 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="slug" class="block text-sm/6 font-medium text-gray-900">Slug</label>
                            <div class="mt-2">
                                <div class="">
                                    <input type="text" name="slug" id="slug"class="form-input" placeholder="Enter slug" value="{{ old('slug', $tool->slug ?? '') }}">
                                </div>
                                
                            </div>
                        </div>

                    </div>
                
                    <div class="mb-5">
                        <label for="category_id" class="block text-sm/6 font-medium text-gray-900">Categories</label>
                        <div class="mt-2 grid grid-cols-1">
                            <select id="category_id" name="category[]" multiple
                                class="form-input">
                                <option value="" disabled selected>Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-6">
                        <div>
                            <label for="tagline" class="block text-sm/6 font-medium text-gray-900">Tagline</label>
                            <div class="mt-2">
                                <div class="">
                                    <input type="text" name="tagline" id="tagline"
                                        class="form-input"
                                        placeholder="" value="{{ old('tagline', $tool->tagline ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="short_description_heading" class="block text-sm/6 font-medium text-gray-900">Short Description Heading</label>
                            <div class="mt-2">
                                <div class="">
                                    <input type="text" name="short_description_heading" id="short_description_heading"
                                        class="form-input"
                                        value="{{ old('short_description_heading', $tool->short_description_heading ?? '') }}">
                                </div>
                            </div>
                        </div>                        
                    </div>



                
                    <div class="sm:col-span-4 mb-5">
                        <label for="short_description" class="block text-sm/6 font-medium text-gray-900">Short Description</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white  outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-green-600">
                                <textarea name="short_description" id="short_description"
                                    class="textarea_editor block w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">{{ old('short_description', $tool->short_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                
                    <div class="grid lg:grid-cols-2 gap-6">
                        
                        <div class="">
                            <label for="verified_status" class="block text-sm/6 font-medium text-gray-900">Release Date</label>
                            <div class="mt-2">
                                <div class="">
                                    <input type="date" name="release_date" id="release_date"
                                        class="form-input"
                                        value="{{ old('release_date', $tool->release_date ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="">
                            <label for="verified_status" class="block text-sm/6 font-medium text-gray-900">Verified Status</label>
                            <div class="mt-2">
                                <div class="">
                                    <select name="verified_status" id="verified_status"
                                        class="form-input">
                                        {{--<option value="0" {{ old('verified_status', $tool->verified_status ?? '') == 0 ? 'selected' : '' }}>Verified</option>--}}
                                        {{--<option value="1" {{ old('verified_status', $tool->verified_status ?? '') == 1 ? 'selected' : '' }}>Not Verified</option>--}}

                                        @foreach (\App\Models\AiTool::getVerifiedStatusOptions() as $key => $value)
                                            <option value="{{ $key }}" {{ old('verified_status', $tool->verified_status ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <label for="integration_capabilities" class="block text-sm/6 font-medium text-gray-900">Integration Capabilities</label>
                            <div class="mt-2">

                                <select name="integration_capabilities" id="integration_capabilities"
                                    class="form-input">
                                    @foreach (\App\Models\AiTool::getIntegrationCapabilitiesOptions() as $key => $value)
                                        <option value="{{ $key }}" {{ old('integration_capabilities', $tool->integration_capabilities ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>                        

                        <div class="">
                            <label for="payment_text" class="block text-sm/6 font-medium text-gray-900">Payment Text</label>
                            <div class="mt-2">
                                <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-green-600">
                                    <input type="text" name="payment_text" id="payment_text"
                                        class="form-input"
                                        value="{{ old('payment_text', $tool->payment_text ?? '') }}">
                                </div>
                            </div>
                        </div> 
                       
                       <div class="">
                            <label for="payment_status" class="block text-sm/6 font-medium text-gray-900">Type</label>
                            <div class="mt-2">
                                                
                                <select name="payment_status" id="payment_status"  class="form-input">
                                        {{--<option value="1" {{ isset($tool) && $tool->payment_status ==  1 ? 'selected' : '' }}>Free</option>--}}
                                        {{--<option value="2" {{ isset($tool) && $tool->payment_status == 2 ? 'selected' : '' }}>Paid</option>--}}

                                    @foreach (\App\Models\AiTool::getPaymentStatusOptions() as $key => $value)
                                        <option value="{{ $key }}" {{ old('payment_status', $tool->payment_status ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach

                                </select>  

                            </div>
                        </div>

                        
                        <div class="">
                            <label for="website_link" class="block text-sm/6 font-medium text-gray-900">Website Link</label>
                            <div class="mt-2">
                                <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-green-600">
                                    <input type="text" name="website_link" id="website_link"
                                        class="form-input"
                                        value="{{ old('website_link', $tool->website_link ?? '') }}">
                                </div>
                            </div>
                        </div>                        

                    </div>


                

                    
 
                    <div class="">
                            <label for="platform_compatibility" class="block text-sm/6 font-medium text-gray-900">Platform Compatibility</label>
                            <div class="mt-2">
                                @php
                                    $selectedPlatforms = explode(',', $tool->platform_compatibility ?? '');
                                @endphp
                                <select name="platform_compatibility[]" id="platform_compatibility"  class="form-input" multiple>
                                    @foreach (\App\Models\AiTool::getPlatformCompatibilityOptions() as $key => $value)
                                        <option value="{{ $key }}" {{ in_array($key, $selectedPlatforms) ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach

                                </select>  

                            </div>
                        </div>

                
                    <div class="sm:col-span-4 mb-5">
                        <label for="description_heading" class="block text-sm/6 font-medium text-gray-900">Description Heading</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-green-600">
                                <input type="text" name="description_heading" id="description_heading"
                                    class="form-input"
                                    value="{{ old('description_heading', $tool->description_heading ?? '') }}">
                            </div>
                        </div>
                    </div>
                
                    <div class="sm:col-span-4 mb-5">
                        <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300">
                                <textarea name="description" id="description"
                                    class="block w-full py-1.5 textarea_editor px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">{{ old('description', $tool->description ?? '') }}</textarea>
                             
                            </div>
                        </div>
                    </div>
                
                    <div class="sm:col-span-4 mb-5">
                        <label for="key_features" class="block text-sm/6 font-medium text-gray-900">Key Features</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300">
                                <textarea name="key_features" id="key_features"
                                    class="textarea_editor block w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6">{{ old('key_features', $tool->key_features ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                
                    <div class="sm:col-span-4 mb-5 setting_fields">
                        <label for="pros" class="block text-sm/6 font-medium text-gray-900">Pros</label>
                        <div class="pros_container">
                            @if(!empty($pros))
                                @foreach($pros as $index => $pro)
                                    <div class="flex items-center gap-4">
                                        <input type="text" name="pros[title][]" id="pros_title_{{ $index }}"
                                            class="form-input"
                                            value="{{ $pro['heading'] ?? '' }}">
                                        <textarea name="pros[content][]" id="pros_content_{{ $index }}"
                                            class="form-input">{{ $pro['description'] ?? '' }}</textarea>
                                        
                                        <div class="col-1">
                                            <a href="javascript:void(0)" class="add_sub_btn add_field" data-field_type="pros">
                                                <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                    <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                                                    </svg>
                                                </svg>
                                            </a>
                                            @unless($loop->first)
                                                <a href="javascript:void(0)" class="add_sub_btn sub_field new" data-field_type="pros">
                                                    <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                        <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>
                                                        </svg>
                                                    </svg>
                                                </a>
                                            @endunless
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-4 ">
                                    <input type="text" name="pros[title][]" id="pros_title_0" class="form-input"
                                        value="">
                                    <textarea name="pros[content][]" id="pros_content_0" class="form-input"></textarea>
                                    <div class="col-1">
                                        <a href="javascript:void(0)" class="add_sub_btn add_field" data-field_type="pros">
                                            <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                                                </svg>
                                            </svg>
                                        </a>
                                        <!--<a href="javascript:void(0)" class="add_sub_btn sub_field" data-field_type="pros">-->
                                        <!--    <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                        <!--        <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#2005B7"></path>-->
                                        <!--        <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                        <!--            <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>-->
                                        <!--        </svg>-->
                                        <!--    </svg>-->
                                        <!--</a>-->
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                
                    <div class="sm:col-span-4 mb-5 setting_fields">
                        <label for="cons" class="block text-sm/6 font-medium text-gray-900">Cons</label>
                        <div class="cons_container">
                            @if(!empty($cons))
                                @foreach($cons as $index => $con)
                                    <div class="flex items-center gap-4">
                                        <input type="text" name="cons[title][]" id="cons_title_{{ $index }}"
                                            class="form-input"
                                            value="{{ $con['heading'] ?? '' }}">
                                        <textarea name="cons[content][]" id="cons_content_{{ $index }}"
                                            class="form-input">{{ $con['description'] ?? '' }}</textarea>
                                        
                                        <div class="col-1">
                                            <a href="javascript:void(0)" class="add_sub_cons_btn add_cons_field" data-field_type="cons">
                                                <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                    <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                                                    </svg>
                                                </svg>
                                            </a>
                                            @unless($loop->first)
                                                <a href="javascript:void(0)" class="add_sub_cons_btn sub_cons_field" data-field_type="cons">
                                                    <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                        <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>
                                                        </svg>
                                                    </svg>
                                                </a>
                                            @endunless
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-4 ">
                                    <input type="text" name="cons[title][]" id="cons_title_0" class="form-input"
                                        value="">
                                    <textarea name="cons[content][]" id="cons_content_0" class="form-input"></textarea>
                                    <div class="col-1">
                                        <a href="javascript:void(0)" class="add_sub_cons_btn add_cons_field" data-field_type="cons">
                                           <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                                <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                                                </svg>
                                            </svg>
                                        </a>
                                        <!--<a href="javascript:void(0)" class="add_sub_cons_btn sub_cons_field" data-field_type="cons">-->
                                        <!--    <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                        <!--        <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>-->
                                        <!--        <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                        <!--            <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>-->
                                        <!--        </svg>-->
                                        <!--    </svg>-->
                                        <!--</a>-->
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <label for="voila_description" class="block text-sm/6 font-medium text-gray-900">Voila Description</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300">
                                <textarea name="voila_description" id="voila_description"
                                    class="block textarea_editor w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400">{{ old('voila_description', $tool->voila_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                
                    <div class="mb-5">
                        <label for="long_description" class="block text-sm/6 font-medium text-gray-900">Long Description</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300">
                                <textarea name="long_description" id="long_description"
                                    class="block textarea_editor w-full py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400">{{ old('long_description', $tool->long_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                
                    <div class="mb-5">
                        <label for="aitool_filter" class="block text-sm/6 font-medium text-gray-900">AI Tool Filter</label>
                        <div class="mt-2">
                            <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300">
                                <input type="text" name="aitool_filter" id="aitool_filter"
                                    class="form-input"
                                    value="{{ old('aitool_filter', $tool->aitool_filter ?? '') }}">
                            </div>
                        </div>
                    </div>
                
                
                    <div class="col-span-full mb-5">
                    <label class="block text-sm/6 font-medium text-gray-900">Logo</label>
                    <div class="mt-2 grid grid-cols-1">
                        <input type="file" name="logo" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg" id="logoInput">
                        <!-- Image Preview -->
                        @if(isset($tool) && $tool->logo)
                            <div id="PreviewContainer" class="mt-2  relative">
                                <img  src="{{ asset('public/storage/ai-tools-images/' . $tool->logo) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
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
                        <input type="file" name="cover" accept="image/*" class="image_choose w-full p-2 border border-gray-300 rounded-lg focus:ring-2" id="coverInput">
                        <!-- Image Preview -->
                        @if(isset($tool) && $tool->cover)
                            <div id="PreviewContainer" class="mt-2 relative">
                                <img  src="{{ asset('public/storage/ai-tools-images/' . $tool->cover) }}" class="Preview w-32 h-32 object-cover rounded-lg border border-gray-300">
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
                    <div class="flex items-center justify-end gap-x-6">
                        <button type="submit"
                            class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white">
                            {{ isset($tool) ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </form>
             
                


@endsection