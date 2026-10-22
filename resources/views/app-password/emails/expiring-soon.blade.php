@component('mail::message')

{{ __( 'Hello :name,', [ 'name' => $user->name ?: $user->username ] ) }}

{{ __( 'This is a reminder that the following application password(s) for :site will expire in 14 days:', [ 'site' => config( 'identity.sitename' ) ] ) }}

@foreach( $appPasswords as $appPassword )
- **{{ $appPassword->description ?: __( 'Application password #:id', [ 'id' => $appPassword->id ] ) }}** ({{ __( 'expires' ) }}: {{ \Carbon\Carbon::parse( $appPassword->expires )->format( 'Y-m-d' ) }})
@endforeach

{{ __( 'Please review and renew these credentials if needed.' ) }}

{{ config( 'identity.name' ) }}

@endcomponent
