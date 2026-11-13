@component('mail::message')

{{ config( 'identity.sitename' ) }} - Account Restricted


To whom it may concern,

Your user account on **{{ config( 'identity.sitename' ) }}** has been locked due to too many failed two factor login attempts.

If this wasn't you, it means that someone else has your username and password and you should reset your password immediately and contact our operations team on [{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }}) and let us know.

@if($referToCustAdmin)
To have the 2FA restriction removed, you will need to contact support [{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }}), or an admin user at {{ $referToCustAdmin->name }}.
@else
To have the 2FA restriction removed, you will need to contact support [{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }}).
@endif

Thanks and kind regards,


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent