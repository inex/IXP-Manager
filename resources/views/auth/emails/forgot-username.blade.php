@component('mail::message')

{{ __( 'To whom it may concern,' ) }}

{{ __( 'You, or someone entering your email address, has requested a username reminder for your email address for :site.', [ 'site' => config( 'identity.sitename' ) ] ) }}

{{ __( 'The usernames linked to your account are:' ) }}


@foreach( $users as $user )

* {{ $user->username }} ({{ __( 'for' ) }} *{{ $user->customer->name }}*)

@endforeach


{{ __( 'If you did not make this request, please ignore this email.' ) }}


{{ __( 'Thanks and kind regards,' ) }}

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
