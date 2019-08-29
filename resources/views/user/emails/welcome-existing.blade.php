@component('mail::message')

{{ config( 'identity.sitename' ) }} - Updated Access Details


To whom it may concern,

your user account on **{{ config( 'identity.sitename' ) }}** has been updated to give you access to another member account: {{ $c2u->getCustomer()->getName() }}.


The next time you login, you can act for this member by selecting them in under the *My Account* menu on the top right.


@if( ( $c2u->getExtraAttributes()['created_by']['type'] ?? '' ) === 'PeeringDB' and config( 'auth.peeringdb.privs' ) == \Entities\User::AUTH_CUSTUSER )
**Accounts created with PeeringDB have non-admin access by default. If you would like your privileges escalated, please email us
at {{ config( 'identity.support_email') }} quoting your username ({{ $user->getUsername() }}) and the member name.**
@endif


Thanks and kind regards,


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
