<!-- Start Sidebar -->
<aside id="app-menu"
    class="hs-overlay fixed inset-y-0 start-0 z-60 hidden w-sidenav min-w-sidenav -translate-x-full transform overflow-y-auto bg-body transition-all duration-300 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">
    <div class="sticky top-0 flex h-16 items-center justify-center px-6">
        <a href="{{ route( 'dashboard' ) }}">
            <img src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" alt="logo" class="flex h-10">
        </a>
    </div>

    <div class="h-[calc(100%-64px)] p-4 lg:ps-8" data-simplebar>
        <ul class="admin-menu hs-accordion-group flex w-full flex-col gap-1.5">

            <li class="menu-item">
                <a class="group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5"
                    href="{{ route( 'dashboard' ) }}">
                    <i data-lucide="airplay" class="size-5"></i>
                    Dashboard
                </a>
            </li>


            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">person</span>
                    <span class="menu-text">User</span>
                    <span
                        class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'user.list' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                All User
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">collections</span>
                    <span class="menu-text">Collection</span>
                    <span
                        class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'collection.list' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                All Collections
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">handyman</span>
                    <span class="menu-text"> AI Tools </span>
                    <span
                        class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'ai-tools.list' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                All AI Tools
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'ai-tools.addEdit' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                Add New AI Tools
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'tools.categories.list' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                Categories
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <i data-lucide="notebook-pen" class="size-5"></i>
                    <span class="menu-text"> Blogs </span>
                    <span
                        class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a href="{{ route( 'blog.list' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">All Blogs</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route( 'blog.addEdit' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">Add New Blog</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route( 'blog.categories.list' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">Categories</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">two_pager</span>
                    <span class="menu-text"> Courses </span>
                    <span
                        class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a href="{{ route( 'courses.list' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">All Course</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route( 'courses.addEdit' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">Add New Course</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route( 'course.categories.list' ) }}"
                                class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                <span class="menu-text">Categories</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>            


            <li class="menu-item">
                <a href="{{ route( 'contact' ) }}"
                    class="group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">mail</span>
                    <span class="menu-text"> Contact </span>
                </a>
            </li>


            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">reviews</span>
                    <span class="menu-text">Review</span>
                    <span class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route( 'review.list' ) }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                All Review
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="menu-item hs-accordion">
                <a href="javascript:void(0)"
                    class="hs-accordion-toggle group flex items-center gap-x-4 rounded-md px-3 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-900/5 hs-accordion-active:bg-default-900/5 hs-accordion-active:text-default-700">
                    <span class="material-symbols-rounded text-xl">quiz</span>
                    <span class="menu-text">Question & Answer</span>
                    <span class="i-tabler-chevron-right ms-auto text-sm transition-all hs-accordion-active:rotate-90"></span>
                </a>

                <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                    <ul class="mt-2 space-y-2">
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route('question-answer.questions-list') }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                Question List
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5"
                                href="{{ route('question-answer.answer-list') }}">
                                <i class="i-tabler-circle-filled scale-25 text-lg opacity-75"></i>
                                Answer List
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>       

    <div class="site_user_dropdown">
            <div id="hs-show-hide-collapse-heading" class="hs-collapse w-full overflow-hidden transition-[height] duration-300 hidden" aria-labelledby="hs-show-hide-collapse">
            <ul class="mt-3 mb-3">
                        <!-- <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5 active" href="https://laravel.devmontdigital.co/aiguy/ai-tools">
                                <span class="material-symbols-rounded text-xl">person</span> 
                                Profile
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5" href="https://laravel.devmontdigital.co/aiguy/ai-tools/add">
                            <span class="material-symbols-rounded text-xl">settings</span> 
                                Setting
                            </a>
                        </li> -->
                        <li class="menu-item">
                            <a class="flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-700 hover:bg-default-900/5" href="{{ route('logout') }}">
                                <span class="material-symbols-rounded text-xl">logout</span> 
                                Logout
                            </a>
                        </li>

                    </ul>
            </div> 
    
            <button class="hs-collapse-toggle flex items-center gap-x-2 text-primary w-full"
                id="hs-show-hide-collapse"
                data-hs-collapse="#hs-show-hide-collapse-heading"> 
                <img src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" class="rounded-full h-10"> {{ Auth::user()->name }}
                <i class="ms-auto i-tabler-chevron-down hs-collapse-open:rotate-180 transition-all duration-300 text-lg ms-2"></i>
            </button>
                                    

        </div>
    </div>

   
    


</aside>
<!-- End Sidebar -->


<!-- Mobile Nav Start -->
<div class="md:hidden flex">
    <div
        class="fixed bottom-0 z-50 shadow-md w-full h-16 flex items-center justify-between px-5 gap-4 bg-white border-b border-default-100">

        <a href="#" class="flex flex-col items-center justify-center gap-1 text-default-600">
            <i data-lucide="gauge" class="size-5"></i>
            <span class="text-xs font-semibold">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center gap-1 text-default-600">
            <i data-lucide="search" class="size-5"></i>
            <span class="text-xs font-semibold">Search</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center gap-1 text-default-600">
            <i data-lucide="compass" class="size-5"></i>
            <span class="text-xs font-semibold">Explore</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center gap-1 text-default-600">
            <i data-lucide="bell" class="size-5"></i>
            <span class="text-xs font-semibold">Alerts</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center gap-1 text-default-600">
            <i data-lucide="circle-user" class="size-5"></i>
            <span class="text-xs font-semibold">Profile</span>
        </a>
    </div>
</div>
<!-- Mobile Nav End -->