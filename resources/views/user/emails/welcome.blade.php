@component('mail::message')

{{ config( 'identity.sitename' ) }} - {{ __( 'Your Access Details' ) }}


{{ __( 'To whom it may concern,' ) }}

@if( $resend )
{{ __( '**This email is being sent to you because either you requested a reminder of your account details or an administrator thought it appropriate to send you a reminder.**' ) }}
@elseif( $user->peeringdb_id )
{{ __( 'A new user account has been created for you on the :site as you logged in with your PeeringDB account.', [ 'site' => config( 'identity.sitename' ) ] ) }}

@if( config( 'auth.peeringdb.privs' ) === \IXP\Models\User::AUTH_CUSTUSER )
{{ __( '**Accounts created with PeeringDB have non-admin access by default. If you would like your privileges escalated, please email us at :email with your username (:username).**', [ 'email' => config( 'identity.support_email' ), 'username' => $user->username ] ) }}
@endif

@else
{{ __( 'A new user account has been created for you on the :site.', [ 'site' => config( 'identity.sitename' ) ] ) }}
@endif

{{ __( 'You can login to your account using the following details:' ) }}

|                                |                                                                  |
| ------------------------------ | ---------------------------------------------------------------  |
| **{{ __( 'URL:' ) }}     **                  | [{{ config( 'identity.url' ) }}]({{ config( 'identity.url' ) }}) |
| **{{ __( 'Username:' ) }}**                  | {{ $user->username }}                                            |
| **{{ __( 'Password:' ) }}**                  | ({{ __( 'see below' ) }})                                                      |



{{ __( 'Once logged in, you will have access to a number of features including:' ) }}

* {{ __( 'list of IXP members and peering contact details;' ) }}
* {{ __( 'the peering manager tool;' ) }}
* {{ __( 'your port and member to member traffic graphs;' ) }}
* {{ __( 'ability to view and edit your company details;' ) }}
* {{ __( 'your port configuration details;' ) }}
* {{ __( 'the peering matrix;' ) }}
* {{ __( 'route server, AS112 and other service information.' ) }}


{{ __( 'If you require any assistance, please contact :name on :email.', [ 'name' => config( 'identity.name' ), 'email' => '[' . config( 'identity.email' ) . '](mailto:' . config( 'identity.email' ) . ')' ] ) }}


## {{ __( 'Getting Your Password' ) }}


{{ __( 'To set your password, please use following link:' ) }}

@component('mail::button', ['url' => route( "reset-password@show-reset-form", [ "token" => $token, "username" => $user->username ] ), 'color' => 'blue'])
    {{ __( 'Reset password' ) }}
@endcomponent



{{ __( 'Thanks and kind regards,' ) }}


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
