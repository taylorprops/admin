@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
        <img class="email-header-logo" src="{{ asset(\Session::get('email_logo_src')) }}">
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
            © {{ date('Y') }} {{ stristr(auth() -> user() -> group, 'agent') ? \Session::get('agent_details') -> company : 'Taylor Properties' }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
