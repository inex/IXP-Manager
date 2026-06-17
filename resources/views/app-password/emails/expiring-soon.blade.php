@component('mail::message')

Hello {{ $user->name ?: $user->username }},

This is a reminder that the following application password(s) for {{ config('identity.sitename') }} will expire in 14 days:

@foreach( $appPasswords as $appPassword )
- **{{ $appPassword->description ?: 'Application password #' . $appPassword->id }}** (expires: {{ \Carbon\Carbon::parse( $appPassword->expires )->format( 'Y-m-d' ) }})
@endforeach

Please review and renew these credentials if needed.

{{ config( 'identity.name' ) }}

@endcomponent
