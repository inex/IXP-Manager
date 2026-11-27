@component('mail::message')

{{ config( 'identity.sitename' ) }} - Logins Throttled


To whom it may concern,

Logins to your user account on **{{ config( 'identity.sitename' ) }}** have been throttled due to too many logins with the wrong password.

You will have to wait between login attempts until the throttle clears.

If this is unexpected, or you require assistance, please contact support on the email below.

Thanks and kind regards,


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent