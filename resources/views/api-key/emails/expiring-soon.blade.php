@component('mail::message')

Hello {{ $user->name ?: $user->username }},

This is a reminder that the following API key(s) for {{ config('identity.sitename') }} will expire in 14 days:

@foreach( $apiKeys as $apiKey )
- **{{ $apiKey->description ?: 'API key #' . $apiKey->id }}** ending `{{ Str::limit( $apiKey->apiKey, 6 ) }} (expires: {{ \Carbon\Carbon::parse( $appPassword->expires )->format( 'Y-m-d' ) }})
@endforeach

Please review and renew these credentials if needed.

{{ config( 'identity.name' ) }}

@endcomponent
