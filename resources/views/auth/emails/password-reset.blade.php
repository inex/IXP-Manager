@component('mail::message')

{{ __( 'To whom it may concern,' ) }}

{{ __( 'Your password for :site has been reset by the user initiated password reset procedure.', [ 'site' => config( 'identity.sitename' ) ] ) }}

{{ __( 'If you did not make this request, please contact our support team.' ) }}

{{ __( 'Thanks and kind regards,' ) }}

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
