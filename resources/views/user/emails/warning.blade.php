@component('mail::message')

    ## {{ config('identity.sitename') }} Alert

    {{ $event->warningTitle }}

    @component('mail::panel')
        {{ $event->errorMessage }}
    @endcomponent


@endcomponent