
@if( trim( $ppp->patchPanel->locationDescription() ) !== '' || trim( $ppp->patchPanel->location_notes ) !== '' )
#### {{ __( 'Notes for the Colocation Provider' ) }}

{{ __( "The records of :org include the following notes to help identify the above patch panel:", [ 'org' => env( 'IDENTITY_ORGNAME' ) ] ) }}

@if( trim( $ppp->patchPanel->locationDescription() ) !== '' )
{{ $ppp->patchPanel->locationDescription() }}
@endif

@if( trim( $ppp->patchPanel->location_notes ) !== '' )
{{$ppp->patchPanel->location_notes}}
@endif

@endif


{{ __( 'Kind regards,' ) }}

{{ env('IDENTITY_NAME') }}

{{ env('IDENTITY_SUPPORT_EMAIL') }} / {{ env('IDENTITY_SUPPORT_PHONE') }}

