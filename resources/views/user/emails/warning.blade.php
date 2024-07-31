@component('mail::message')

    ## Warning Message

    {{ $warningTitle }}

    @component('mail::panel')
        {{ $errorMessage }}
    @endcomponent

    @component('mail::subcopy')
        &nbsp;
    @endcomponent

    Thanks and kind regards,


    {{ config( 'identity.name' ) }}

    [{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent