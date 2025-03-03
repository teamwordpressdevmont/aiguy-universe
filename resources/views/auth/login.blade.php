<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="pb-10">
            <h1 class="font-[THICCCBOI-bold] text-4xl text-center text-[#20BF0E]">Login</h1>
        </div>

        {{-- <div class="py-10">
            <a href="#" class="w-full flex items-center justify-center rounded-2xl border border-[#021F11] py-2 gap-4">
                <img src="{{ asset( 'public/storage/aiguy-images/google.svg' ) }}" alt="google" />
                <span class="text-white font-[THICCCBOI-Regular] text-sm">Login With Google</span>
            </a>
        </div> --}}

        {{-- <div class="flex items-center justify-center pb-10">
            <div class="border-t  border-[#021F11] flex-grow"></div>
            <p class="mx-4 text-[rgba(255,255,255,0.37)] font-[THICCCBOI-Regular] text-sm">or use email</p>
            <div class="border-t  border-[#021F11] flex-grow"></div>
        </div> --}}
        
        

        <!-- Email Address -->
        <div>
            {{-- <x-input-label for="email" :value="__('Email')" /> --}}
            <x-text-input id="email" class="block mt-1 w-full py-3 px-5" type="email" name="email" :value="old('email')" placeholder="Email Address" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            {{-- <x-input-label for="password" :value="__('Password')" /> --}}

            <x-text-input id="password" class="block mt-1 w-full py-3 px-5"
                            type="password"
                            name="password" placeholder="Password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-primary-button class="">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        {{-- <div class="mt-5  flex gap-2">
            <span class="text-[rgba(255,255,255,0.37)] text-right font-[THICCCBOI-Regular] text-sm">Don’t have an account?</span>
            <a href="{{ route('register') }}" class="text-white font-[THICCCBOI-Regular] text-sm">
                Sign up
            </a>
        </div> --}}

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-4 text-white font-[THICCCBOI-Regular] text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        
    </form>

</x-guest-layout>
