@component('mail::message')

{{ __( 'Hello :name,', [ 'name' => $user->name ?: $user->username ] ) }}

{{ __( 'This is a reminder that the following API key(s) for :site will expire in 14 days:', [ 'site' => config( 'identity.sitename' ) ] ) }}

@foreach( $apiKeys as $apiKey )
- **{{ $apiKey->description ?: __( 'API key #:id', [ 'id' => $apiKey->id ] ) }}** {{ __( 'ending' ) }} `{{ Str::limit( $apiKey->apiKey, 6 ) }} ({{ __( 'expires' ) }}: {{ \Carbon\Carbon::parse( $appPassword->expires )->format( 'Y-m-d' ) }})
@endforeach

{{ __( 'Please review and renew these credentials if needed.' ) }}

{{ config( 'identity.name' ) }}

@endcomponent
