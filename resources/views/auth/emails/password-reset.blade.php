@component('mail::message')

To whom it may concern,

Your password for <?= config( "identity.sitename" ) ?> has been reset by the user initiated password reset procedure.

If you did not make this request, please contact our support team.

Thanks and kind regards,

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent