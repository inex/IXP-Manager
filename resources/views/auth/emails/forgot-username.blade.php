@component('mail::message')

To whom it may concern,

You, or someone purporting to be you, has requested a username reminder for your email address for <?= config( "identity.sitename" ) ?>.

The usernames linked to your account are:


@foreach( $users as $user )

* {{ $user->getUsername() }} (for *{{ $user->getCustomer()->getName() }}*)

@endforeach


If you did not make this request, please contact our support team.


Thanks and kind regards,

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent