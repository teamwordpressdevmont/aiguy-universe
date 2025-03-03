<!-- Topbar Start -->
<header class="app-header md:hidden h-16 flex items-center lg:bg-opacity-10 bg-white  backdrop-blur-sm">
    <div class="container flex items-center gap-4">
        <!-- Topbar Brand Logo -->
        <a href="index.php" class="md:hidden flex">
            <img src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" class="h-6" alt="Small logo">
        </a>

        <!-- Sidenav Menu Toggle Button -->
        <button id="button-toggle-menu" class="text-default-500 hover:text-default-600 p-2 rounded-full cursor-pointer"
            data-hs-overlay="#app-menu" aria-label="Toggle navigation">
            <i class="i-tabler-menu-2 text-2xl"></i>
        </button>

        <!-- Language Dropdown Button -->
        <!-- <div class="ms-auto hs-dropdown relative inline-flex [--placement:bottom-right]">
            <button type="button" class="hs-dropdown-toggle inline-flex items-center">
                <img src="assets/images/flags/us.jpg" alt="user-image" class="h-4 w-6">
            </button>

            <div
                class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                <a href="javascript:void(0);"
                    class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100">
                    <img src="assets/images/flags/germany.jpg" alt="user-image" class="h-4">
                    <span class="align-middle">German</span>
                </a>

        
                <a href="javascript:void(0);"
                    class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100">
                    <img src="assets/images/flags/italy.jpg" alt="user-image" class="h-4">
                    <span class="align-middle">Italian</span>
                </a>

           
                <a href="javascript:void(0);"
                    class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100">
                    <img src="assets/images/flags/spain.jpg" alt="user-image" class="h-4">
                    <span class="align-middle">Spanish</span>
                </a>

                
                <a href="javascript:void(0);"
                    class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100">
                    <img src="assets/images/flags/russia.jpg" alt="user-image" class="h-4">
                    <span class="align-middle">Russian</span>
                </a>
            </div>
        </div> -->

        <!-- Fullscreen Toggle Button -->
        <!-- <div class="md:flex hidden">
            <button data-toggle="fullscreen" type="button" class="nav-link p-2">
                <span class="sr-only">Fullscreen Mode</span>
                <span class="flex items-center justify-center size-6">
                    <i class="i-tabler-maximize text-2xl flex group-[-fullscreen]:hidden"></i>
                    <i class="i-tabler-minimize text-2xl hidden group-[-fullscreen]:flex"></i>
                </span>
            </button>
        </div> -->

        <!-- Profile Dropdown Button -->
        <div class="relative ms-auto">
            <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                <button type="button" class="hs-dropdown-toggle nav-link flex items-center gap-2">
                    <img src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" alt="user-image" class="rounded-full h-10">
                    <i class="i-tabler-chevron-down text-sm ms-2"></i>
                </button>
                <div
                    class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                    <div class="px-3 py-3" role="none">
                        <p class="" role="none">
                           Ai Guy 
                           <!-- {{ Auth::user()->name }} -->
                        </p>
                     </div>
                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100"
                        href="#">
                        Profile
                    </a>
                    <hr class="my-2">
                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-gray-100"
                        href="{{ route('logout') }}">
                        Log Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->


<!-- <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
   <span class="sr-only">Open sidebar</span>
   <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
   <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
   </svg>
</button>

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
        <a href="" class="flex ms-2 md:me-24">
          <img src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" class="h-8 me-3" alt="FlowBite Logo" />
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">AI GUY</span>
        </a>
      </div>
      <div class="flex items-center">
          <div class="flex items-center ms-3">
            <div>
              <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                <span class="sr-only">Open user menu</span>
                <img class="w-8 h-8 rounded-full" src="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}" alt="user photo">
              </button>
            </div>
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
              <div class="px-4 py-3" role="none">
                <p class="text-sm text-gray-900 dark:text-white" role="none">
                  {{ Auth::user()->name }}
                </p>
              </div>
              <ul class="py-1" role="none">
                  <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Sign out</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </div>
  </div>
</nav>

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-green-900 border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto dark:bg-gray-800 flex flex-col">
      <ul class="space-y-2 font-medium">
         <li>
            <a href="{{ route( 'dashboard' ) }}" class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-green-100 dark:hover:bg-gray-700 group">
               <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" viewBox="0 0 15 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M2.8 1L2.74967 0.99997C2.52122 0.999752 2.32429 0.999564 2.14983 1.04145C1.60136 1.17312 1.17312 1.60136 1.04145 2.14983C0.999564 2.32429 0.999752 2.52122 0.99997 2.74967L1 2.8V5.2L0.99997 5.25033C0.999752 5.47878 0.999564 5.67572 1.04145 5.85017C1.17312 6.39864 1.60136 6.82688 2.14983 6.95856C2.32429 7.00044 2.52122 7.00025 2.74967 7.00003L2.8 7H5.2L5.25033 7.00003C5.47878 7.00025 5.67572 7.00044 5.85017 6.95856C6.39864 6.82688 6.82688 6.39864 6.95856 5.85017C7.00044 5.67572 7.00025 5.47878 7.00003 5.25033L7 5.2V2.8L7.00003 2.74967C7.00025 2.52122 7.00044 2.32429 6.95856 2.14983C6.82688 1.60136 6.39864 1.17312 5.85017 1.04145C5.67572 0.999564 5.47878 0.999752 5.25033 0.99997L5.2 1H2.8ZM2.38328 2.01382C2.42632 2.00348 2.49222 2 2.8 2H5.2C5.50779 2 5.57369 2.00348 5.61672 2.01382C5.79955 2.05771 5.94229 2.20045 5.98619 2.38328C5.99652 2.42632 6 2.49222 6 2.8V5.2C6 5.50779 5.99652 5.57369 5.98619 5.61672C5.94229 5.79955 5.79955 5.94229 5.61672 5.98619C5.57369 5.99652 5.50779 6 5.2 6H2.8C2.49222 6 2.42632 5.99652 2.38328 5.98619C2.20045 5.94229 2.05771 5.79955 2.01382 5.61672C2.00348 5.57369 2 5.50779 2 5.2V2.8C2 2.49222 2.00348 2.42632 2.01382 2.38328C2.05771 2.20045 2.20045 2.05771 2.38328 2.01382ZM9.8 1L9.74967 0.99997C9.52122 0.999752 9.32429 0.999564 9.14983 1.04145C8.60136 1.17312 8.17312 1.60136 8.04145 2.14983C7.99956 2.32429 7.99975 2.52122 7.99997 2.74967L8 2.8V5.2L7.99997 5.25033C7.99975 5.47878 7.99956 5.67572 8.04145 5.85017C8.17312 6.39864 8.60136 6.82688 9.14983 6.95856C9.32429 7.00044 9.52122 7.00025 9.74967 7.00003L9.8 7H12.2L12.2503 7.00003C12.4788 7.00025 12.6757 7.00044 12.8502 6.95856C13.3986 6.82688 13.8269 6.39864 13.9586 5.85017C14.0004 5.67572 14.0003 5.47878 14 5.25033L14 5.2V2.8L14 2.74967C14.0003 2.52122 14.0004 2.32429 13.9586 2.14983C13.8269 1.60136 13.3986 1.17312 12.8502 1.04145C12.6757 0.999564 12.4788 0.999752 12.2503 0.99997L12.2 1H9.8ZM9.38328 2.01382C9.42632 2.00348 9.49222 2 9.8 2H12.2C12.5078 2 12.5737 2.00348 12.6167 2.01382C12.7995 2.05771 12.9423 2.20045 12.9862 2.38328C12.9965 2.42632 13 2.49222 13 2.8V5.2C13 5.50779 12.9965 5.57369 12.9862 5.61672C12.9423 5.79955 12.7995 5.94229 12.6167 5.98619C12.5737 5.99652 12.5078 6 12.2 6H9.8C9.49222 6 9.42632 5.99652 9.38328 5.98619C9.20045 5.94229 9.05771 5.79955 9.01382 5.61672C9.00348 5.57369 9 5.50779 9 5.2V2.8C9 2.49222 9.00348 2.42632 9.01382 2.38328C9.05771 2.20045 9.20045 2.05771 9.38328 2.01382ZM2.74967 7.99997L2.8 8H5.2L5.25033 7.99997C5.47878 7.99975 5.67572 7.99956 5.85017 8.04145C6.39864 8.17312 6.82688 8.60136 6.95856 9.14983C7.00044 9.32429 7.00025 9.52122 7.00003 9.74967L7 9.8V12.2L7.00003 12.2503C7.00025 12.4788 7.00044 12.6757 6.95856 12.8502C6.82688 13.3986 6.39864 13.8269 5.85017 13.9586C5.67572 14.0004 5.47878 14.0003 5.25033 14L5.2 14H2.8L2.74967 14C2.52122 14.0003 2.32429 14.0004 2.14983 13.9586C1.60136 13.8269 1.17312 13.3986 1.04145 12.8502C0.999564 12.6757 0.999752 12.4788 0.99997 12.2503L1 12.2V9.8L0.99997 9.74967C0.999752 9.52122 0.999564 9.32429 1.04145 9.14983C1.17312 8.60136 1.60136 8.17312 2.14983 8.04145C2.32429 7.99956 2.52122 7.99975 2.74967 7.99997ZM2.8 9C2.49222 9 2.42632 9.00348 2.38328 9.01382C2.20045 9.05771 2.05771 9.20045 2.01382 9.38328C2.00348 9.42632 2 9.49222 2 9.8V12.2C2 12.5078 2.00348 12.5737 2.01382 12.6167C2.05771 12.7995 2.20045 12.9423 2.38328 12.9862C2.42632 12.9965 2.49222 13 2.8 13H5.2C5.50779 13 5.57369 12.9965 5.61672 12.9862C5.79955 12.9423 5.94229 12.7995 5.98619 12.6167C5.99652 12.5737 6 12.5078 6 12.2V9.8C6 9.49222 5.99652 9.42632 5.98619 9.38328C5.94229 9.20045 5.79955 9.05771 5.61672 9.01382C5.57369 9.00348 5.50779 9 5.2 9H2.8ZM9.8 8L9.74967 7.99997C9.52122 7.99975 9.32429 7.99956 9.14983 8.04145C8.60136 8.17312 8.17312 8.60136 8.04145 9.14983C7.99956 9.32429 7.99975 9.52122 7.99997 9.74967L8 9.8V12.2L7.99997 12.2503C7.99975 12.4788 7.99956 12.6757 8.04145 12.8502C8.17312 13.3986 8.60136 13.8269 9.14983 13.9586C9.32429 14.0004 9.52122 14.0003 9.74967 14L9.8 14H12.2L12.2503 14C12.4788 14.0003 12.6757 14.0004 12.8502 13.9586C13.3986 13.8269 13.8269 13.3986 13.9586 12.8502C14.0004 12.6757 14.0003 12.4788 14 12.2503L14 12.2V9.8L14 9.74967C14.0003 9.52122 14.0004 9.32429 13.9586 9.14983C13.8269 8.60136 13.3986 8.17312 12.8502 8.04145C12.6757 7.99956 12.4788 7.99975 12.2503 7.99997L12.2 8H9.8ZM9.38328 9.01382C9.42632 9.00348 9.49222 9 9.8 9H12.2C12.5078 9 12.5737 9.00348 12.6167 9.01382C12.7995 9.05771 12.9423 9.20045 12.9862 9.38328C12.9965 9.42632 13 9.49222 13 9.8V12.2C13 12.5078 12.9965 12.5737 12.9862 12.6167C12.9423 12.7995 12.7995 12.9423 12.6167 12.9862C12.5737 12.9965 12.5078 13 12.2 13H9.8C9.49222 13 9.42632 12.9965 9.38328 12.9862C9.20045 12.9423 9.05771 12.7995 9.01382 12.6167C9.00348 12.5737 9 12.5078 9 12.2V9.8C9 9.49222 9.00348 9.42632 9.01382 9.38328C9.05771 9.20045 9.20045 9.05771 9.38328 9.01382Z"
                    fill="currentColor"
                />
                </svg>               
               <span class="flex-1 ms-3 whitespace-nowrap group-hover:text-gray-900">Dashboard</span>
            </a>
         </li>        
         <li>
            <button type="button" class="flex items-center w-full p-2 text-base text-white transition duration-75 rounded-lg group hover:bg-green-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-1" data-collapse-toggle="dropdown-1">
                <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.773 3.485l-.78-.184-2.108 2.096-1.194-1.216 2.056-2.157-.18-.792a4.42 4.42 0 0 0-1.347-.228 3.64 3.64 0 0 0-1.457.28 3.824 3.824 0 0 0-1.186.84 3.736 3.736 0 0 0-.875 1.265 3.938 3.938 0 0 0 0 2.966 335.341 335.341 0 0 0-6.173 6.234c-.21.275-.31.618-.284.963a1.403 1.403 0 0 0 .464.967c.124.135.272.247.437.328.17.075.353.118.538.127.316-.006.619-.126.854-.337 1.548-1.457 4.514-4.45 6.199-6.204.457.194.948.294 1.444.293a3.736 3.736 0 0 0 2.677-1.133 3.885 3.885 0 0 0 1.111-2.73 4.211 4.211 0 0 0-.196-1.378zM2.933 13.928a.31.31 0 0 1-.135.07.437.437 0 0 1-.149 0 .346.346 0 0 1-.144-.057.336.336 0 0 1-.114-.11c-.14-.143-.271-.415-.14-.568 1.37-1.457 4.191-4.305 5.955-6.046.1.132.21.258.328.376.118.123.245.237.38.341-1.706 1.75-4.488 4.564-5.98 5.994zm11.118-9.065c.002.765-.296 1.5-.832 2.048a2.861 2.861 0 0 1-4.007 0 2.992 2.992 0 0 1-.635-3.137A2.748 2.748 0 0 1 10.14 2.18a2.76 2.76 0 0 1 1.072-.214h.254L9.649 3.839v.696l1.895 1.886h.66l1.847-1.816v.258zM3.24 6.688h1.531l.705.717.678-.674-.665-.678V6.01l.057-1.649-.22-.437-2.86-1.882-.591.066-.831.849-.066.599 1.838 2.918.424.215zm-.945-3.632L4.609 4.58 4.57 5.703H3.494L2.002 3.341l.293-.285zm7.105 6.96l.674-.673 3.106 3.185a1.479 1.479 0 0 1 0 2.039 1.404 1.404 0 0 1-1.549.315 1.31 1.31 0 0 1-.437-.315l-3.142-3.203.679-.678 3.132 3.194a.402.402 0 0 0 .153.105.477.477 0 0 0 .359 0 .403.403 0 0 0 .153-.105.436.436 0 0 0 .1-.153.525.525 0 0 0 .036-.184.547.547 0 0 0-.035-.184.436.436 0 0 0-.1-.153L9.4 10.016z"/></svg>

                  <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap group-hover:text-gray-900">AI Tools</span>
                  <svg class="w-3 h-3 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                  </svg>
            </button>
            <ul id="dropdown-1" class="hidden py-2 space-y-2">
                  <li>
                     <a href="{{ route( 'ai-tools.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">All AI Tools</a>
                  </li>
                  <li>
                     <a href="{{ route( 'ai-tools.addEdit' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Add New AI Tools</a>
                  </li>
                  <li>
                     <a href="{{ route( 'tools.categories.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Categories</a>
                  </li>                  
            </ul>
         </li>         

         <li>
            <button type="button" class="flex items-center w-full p-2 text-base text-white transition duration-75 rounded-lg group hover:bg-green-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-3" data-collapse-toggle="dropdown-3">
                <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 36 36">
                    <title>pin-line</title>
                    <path class="clr-i-outline clr-i-outline-path-1" d="M33,16.59a1,1,0,0,1-.71-.29L19.7,3.71a1,1,0,0,1,1.41-1.41L33.71,14.89A1,1,0,0,1,33,16.59Z"></path><path class="clr-i-outline clr-i-outline-path-2" d="M28.52,15.56l-1.41-1.41-7.2,7.2a1,1,0,0,0-.25,1,9,9,0,0,1-1.53,8.09L5.58,17.87a9,9,0,0,1,8.09-1.53,1,1,0,0,0,1-.25l7.2-7.2L20.44,7.48l-6.79,6.79A10.94,10.94,0,0,0,3.41,17.11a1,1,0,0,0,0,1.42l6.33,6.33L2.29,32.29a1,1,0,1,0,1.41,1.41l7.44-7.44,6.33,6.33a1,1,0,0,0,.71.29h0a1,1,0,0,0,.71-.3,11,11,0,0,0,2.84-10.24Z"></path>
                    <rect x="0" y="0" width="36" height="36" fill-opacity="0"/>
                </svg>                
                  <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap group-hover:text-gray-900">Blogs</span>
                  <svg class="w-3 h-3 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                  </svg>
            </button>
            <ul id="dropdown-3" class="hidden py-2 space-y-2">
                  <li>
                     <a href="{{ route( 'blog.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">All Blogs</a>
                  </li>
                  <li>
                     <a href="{{ route( 'blog.addEdit' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Add New Blog</a>
                  </li>
                  <li>
                     <a href="{{ route( 'blog.categories.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Categories</a>
                  </li>                  
            </ul>
         </li>  
        
         
         <li>
            <button type="button" class="flex items-center w-full p-2 text-base text-white transition duration-75 rounded-lg group hover:bg-green-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-5" data-collapse-toggle="dropdown-5">
                <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1750.21 0v1468.235h-225.882v338.824h169.412V1920H451.387c-82.447 0-161.506-36.141-214.701-99.388-43.934-51.953-67.652-116.33-67.652-182.965V282.353C169.034 126.494 295.528 0 451.387 0H1750.21Zm-338.823 1468.235H463.81c-89.223 0-166.136 59.86-179.576 140.047-1.242 9.036-2.259 18.07-2.259 27.106v2.26c0 40.658 13.553 77.928 40.659 109.552 32.753 38.4 79.059 59.859 128.753 59.859h960v-112.941H409.599v-112.942h1001.788v-112.94Zm225.882-1355.294H451.387c-92.725 0-169.412 75.67-169.412 169.412v1132.8c50.824-37.27 113.958-59.859 181.835-59.859h1173.46V112.941ZM1354.882 903.53v112.942H564.294V903.529h790.588Zm56.47-564.705v451.764H507.825V338.824h903.529Zm-112.94 112.94H620.765v225.883h677.647V451.765Z" fill-rule="evenodd"/>
                </svg>              
                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap group-hover:text-gray-900">Courses</span>  
                <svg class="w-3 h-3 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                  </svg>                              
            </button>
            <ul id="dropdown-5" class="hidden py-2 space-y-2">
                  <li>
                     <a href="{{ route( 'courses.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">All Courses</a>
                  </li>
                  <li>
                     <a href="{{ route( 'courses.addEdit' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Add New Course</a>
                  </li>
                  <li>
                     <a href="{{ route( 'course.categories.list' ) }}" class="flex items-center w-full p-2 text-green-200 transition duration-75 rounded-lg pl-11 group hover:bg-green-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700">Categories</a>
                  </li>                  
            </ul>
         </li>          
        

         <li>
            <a href="{{ route( 'contact' ) }}" class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-green-100 dark:hover:bg-gray-700 group">
               <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-green-900 dark:group-hover:text-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18"><path d="M19,4H5A3,3,0,0,0,2,7V17a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4ZM5,6H19a1,1,0,0,1,1,1l-8,4.88L4,7A1,1,0,0,1,5,6ZM20,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V9.28l7.48,4.57a1,1,0,0,0,1,0L20,9.28Z"/></svg>               
               <span class="flex-1 ms-3 whitespace-nowrap group-hover:text-gray-900">Contact</span>
            </a>
         </li>


      </ul>

    
   </div>
</aside>



 -->
