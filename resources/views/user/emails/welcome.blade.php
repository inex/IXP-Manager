@component('mail::message')

{{ config( 'identity.sitename' ) }} - Your Access Details


To whom it may concern,

@if( $resend )
**This email is being sent to you because either you requested a reminder of your account details or an administrator thought it appropriate to send you a reminder.**
@else
A new user account has been created for you on the {{ config( 'identity.sitename' ) }}.
@endif

You can login to your account using the following details:

|                                |                                                                  |
| ------------------------------ | ---------------------------------------------------------------  |
| **URL:     **                  | [{{ config( 'identity.url' ) }}]({{ config( 'identity.url' ) }}) |
| **Username:**                  | {{ $user->getUsername() }}                                       |
| **Password:**                  | (see below)                                                      |



Once logged in, you will have access to a number of features including:

* list of IXP members and peering contact details;
* the peering manager tool;
* your port and member to member traffic graphs;
* ability to view and edit your company details;
* your port configuration details;
* the peering matrix;
* route server, AS112 and other service information.


If you require any assistance, please contact {{ config('identity.name') }} on [{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }}).


## Getting Your Password


To get your new password (or reset it), please use the *lost password* procedure by visiting the following link and entering your username as above:


[Click here to reset password]({{ route( "forgot-password@show-form" ) }})

@component('mail::button', ['url' => route( "reset-password@show-reset-form", [ "token" => $token, "username" => $user->getUsername() ] ), 'color' => 'blue'])
    Reset password
@endcomponent



Thanks and kind regards,


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent