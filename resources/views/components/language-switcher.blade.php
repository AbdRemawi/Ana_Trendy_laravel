@php
    $currentLocale = $locale ?? app()->getLocale();
@endphp

@if($currentLocale === 'ar')
    <a href="{{ route('language.switch', 'en') }}"
       class="inline-flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-white/60 hover:bg-white/80 rounded-full text-sm font-medium text-gray-600 hover:text-primary transition-all duration-200 shadow-sm hover:shadow-md"
       aria-label="{{ __('app.switch_to_english') }}"
    >
        <span class="w-5 h-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 flex-shrink-0">
            EN
        </span>
        <span class="hidden sm:inline truncate max-w-[80px]">{{ __('app.switch_to_english') }}</span>
    </a>
@else
    <a href="{{ route('language.switch', 'ar') }}"
       class="inline-flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-white/60 hover:bg-white/80 rounded-full text-sm font-medium text-gray-600 hover:text-primary transition-all duration-200 shadow-sm hover:shadow-md"
       aria-label="{{ __('app.switch_to_arabic') }}"
    >
        <span class="w-5 h-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 flex-shrink-0">
            AR
        </span>
        <span class="hidden sm:inline truncate max-w-[80px]">{{ __('app.switch_to_arabic') }}</span>
    </a>
@endif
