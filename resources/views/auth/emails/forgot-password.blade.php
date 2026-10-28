@component('mail::message')


{{ __( 'To whom it may concern,' ) }}

{{ __( 'You, or someone entering your username, has requested a password reset for :site.', [ 'site' => config( 'identity.sitename' ) ] ) }}

{{ __( 'If you wish to proceed, please click on the following link:' ) }}


@component('mail::button', ['url' => route( "reset-password@show-reset-form", [ "token" => $token, "username" => $user->username ] ), 'color' => 'blue'])
    {{ __( 'Reset password' ) }}
@endcomponent

{{ __( 'If you did not make this request, please ignore this email.' ) }}


{{ __( 'Thanks and kind regards,' ) }}

{{ config( 'identity.name' ) }}

[{{ config( 'identity.email' ) }}](mailto:{{ config( 'identity.email' ) }})

@endcomponent
