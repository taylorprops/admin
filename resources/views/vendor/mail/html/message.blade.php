@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
        <img class="email-header-logo" src="http://taylor-properties.net/images/emails/AAP-flat-white.png{{-- {{ \Session::get('email_logo_src') }} --}}">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }}
            @if(auth() -> user() && stristr(auth() -> user() -> group, 'agent'))
                {{ \Session::get('user_details') -> company }}
                @else
                Taylor Properties
                @endif
                @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
