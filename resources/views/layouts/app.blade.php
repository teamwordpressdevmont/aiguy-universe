<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/ui/trumbowyg.min.css">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset( 'public/storage/aiguy-images/logo.png' ) }}">
            <!-- Icons css  (Mandatory in All Pages) -->
        <link href="{{ url('/public/css/icons.min.css') }}" rel="stylesheet" type="text/css">

        <!-- App css  (Mandatory in All Pages) -->
        <link href="{{ url('/public/css/app.css') }}" rel="stylesheet" type="text/css"></head>
        

       <link rel="stylesheet" href="{{ url('/public/css/style.css') }}">
    </head>
    <body>
    
        <!-- Trumbowyg CSS -->
       
    
        @include('partials.header')
    
        <div class="wrapper">
            @include('partials.sidebar')
            <!-- Start Page Content here -->
            <div class="page-content">
                <main>
                    <div class="p-6">
                        @yield('content')
                    </div>
                </main>
            </div>
            <!-- End Page content -->
        </div>
        
        @include('partials.footer')

        <!-- <script src="{{ url('/public/js/tailwind.js') }}"></script>
        <script src="{{ url('/public/js/jquery.min.js') }}"></script>
    
        <script src="{{ url('/public/js/flowbite.min.js') }}"></script> -->



        
        <!-- validation -->
        <!-- <script src="{{ url('/public/js/jquery.validate.min.js') }}"></script> -->


        <!-- Plugin Js (Mandatory in All Pages) -->
        <script src="{{ url('/public/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ url('/public/libs/preline/preline.js') }}"></script>
        <script src="{{ url('/public/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ url('/public/libs/lucide/umd/lucide.min.js') }}"></script>
        <script src="{{ url('/public/libs/iconify-icon/iconify-icon.min.js') }}"></script>
        <!-- <script src="{{ url('/public/libs/node-waves/waves.min.js') }}"></script> -->

        <!-- Trumbowyg JS -->
        <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/trumbowyg.min.js"></script>        

        <!-- App Js (Mandatory in All Pages) -->
        <script src="{{ url('/public/js/app.js') }}"></script>        

        
        <script src="{{ url('/public/js/custom-script.js') }}"></script>
    
    </body>
</html>
