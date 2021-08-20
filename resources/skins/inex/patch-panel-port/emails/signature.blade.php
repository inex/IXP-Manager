
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

If you have any queries about this, please reply to this email which will create a ticket with the INEX Operations Team.

Kind regards,

INEX Operations - https://www.inex.ie/support/



