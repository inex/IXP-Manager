@component('mail::message')

To whom it may concern,

You, or someone entering your email address, has requested a username reminder for your email address for <?= config( "identity.sitename" ) ?>.

The usernames linked to your account are:


@foreach( $users as $user )

* {{ $user->username }} (for *{{ $user->customer->name }}*)

@endforeach


If you did not make this request, please ignore this email.


Thanks and kind regards,

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
