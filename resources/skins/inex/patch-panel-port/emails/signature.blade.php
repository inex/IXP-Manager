
@if( trim( $ppp->getPatchPanel()->getLocationNotes() ) != '' )
#### Notes for the Colocation Provider

{{ env( 'IDENTITY_ORGNAME' ) }}'s records include the following notes to help identify the above patch panel:

```
{{$ppp->getPatchPanel()->getLocationNotes()}}
```

@endif


If you have any queries about this, please just reply to this email which will create a ticket with our Operations Team.

Kind regards,

{{Auth::user()->getContact()->getName()}}

INEX Operations - https://www.inex.ie/support/



