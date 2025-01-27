@component('mail::message')

    ## {{ config('identity.sitename') }} Alert

    {{ $alert }}

    @component('mail::panel')
        {{ $exception->getMessage() }}
    @endcomponent


@endcomponent