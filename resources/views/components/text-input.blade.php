@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block mt-1 border border-[#021F11] text-[rgba(255,255,255,0.37)] font-[THICCCBOI-Regular] text-sm rounded-2xl w-full p-2 bg-transparent focus:outline-none']) }}>