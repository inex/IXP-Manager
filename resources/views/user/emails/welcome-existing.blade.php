@component('mail::message')

{{ config( 'identity.sitename' ) }} - {{ __( 'Updated Access Details' ) }}


{{ __( 'To whom it may concern,' ) }}

{{ __( 'your user account on **:site** has been updated to give you access to another member account: :customer.', [ 'site' => config( 'identity.sitename' ), 'customer' => $c2u->customer->name ] ) }}


{{ __( 'The next time you login, you can act for this member by selecting them in under the *My Account* menu on the top right.' ) }}


@if( ( $c2u->extra_attributes['created_by']['type'] ?? '' ) === 'PeeringDB' && (int)config( 'auth.peeringdb.privs' ) === \IXP\Models\User::AUTH_CUSTUSER )
{{ __( '**Accounts created with PeeringDB have non-admin access by default. If you would like your privileges escalated, please email us at :email quoting your username (:username) and the member name.**', [ 'email' => config( 'identity.support_email' ), 'username' => $c2u->user->username ] ) }}
@endif


{{ __( 'Thanks and kind regards,' ) }}


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
