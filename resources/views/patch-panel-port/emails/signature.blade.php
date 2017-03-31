
@if( trim( $ppp->getPatchPanel()->getLocationDescription() ) != '' || trim( $ppp->getPatchPanel()->getLocationNotes() ) != '' )
#### Notes for the Colocation Provider

{{ env( 'IDENTITY_ORGNAME' ) }}'s records include the following notes to help identify the above patch panel:

@if( trim( $ppp->getPatchPanel()->getLocationDescription() ) != '' )
{{ $ppp->getPatchPanel()->getLocationDescription() }}
@endif

@if( trim( $ppp->getPatchPanel()->getLocationNotes() ) != '' )
{{$ppp->getPatchPanel()->getLocationNotes()}}
@endif

@endif


Kind regards,

{{ env('IDENTITY_NAME') }} Operations

{{ env('IDENTITY_SUPPORT_EMAIL') }} / {{ env('IDENTITY_SUPPORT_PHONE') }}

