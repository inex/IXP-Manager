@component('mail::message')

{{ config( 'identity.sitename' ) }} - Updated Access Details


To whom it may concern,

your user account on **{{ config( 'identity.sitename' ) }}** has been updated to give you access to another member account: {{ $c2u->getCustomer()->getName() }}.


The next time you login, you can act for this member by selecting them in under the *My Account* menu on the top right.

Thanks and kind regards,


{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
