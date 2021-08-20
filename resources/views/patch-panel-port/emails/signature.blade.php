
@if( trim( $ppp->patchPanel->locationDescription() ) !== '' || trim( $ppp->patchPanel->location_notes ) !== '' )
#### Notes for the Colocation Provider

{{ env( 'IDENTITY_ORGNAME' ) }}'s records include the following notes to help identify the above patch panel:

@if( trim( $ppp->patchPanel->locationDescription() ) !== '' )
{{ $ppp->patchPanel->locationDescription() }}
@endif

@if( trim( $ppp->patchPanel->location_notes ) !== '' )
{{$ppp->patchPanel->location_notes}}
@endif

@endif


Kind regards,

{{ env('IDENTITY_NAME') }} Operations

{{ env('IDENTITY_SUPPORT_EMAIL') }} / {{ env('IDENTITY_SUPPORT_PHONE') }}

