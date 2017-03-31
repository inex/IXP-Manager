
@if( trim( $ppp->getPatchPanel()->getLocationNotes() ) != '' )
#### Notes for the Colocation Provider

{{ env( 'IDENTITY_ORGNAME' ) }}'s records include the following notes to help identify the above patch panel:

```
{{$ppp->getPatchPanel()->getLocationNotes()}}
```

@endif


Kind regards,

{{ env('IDENTITY_NAME') }} Operations

{{ env('IDENTITY_SUPPORT_EMAIL') }} / {{ env('IDENTITY_SUPPORT_PHONE') }}

