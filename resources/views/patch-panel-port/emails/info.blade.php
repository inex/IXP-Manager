Hi,

You or someone in your organisation requested a details on the
following cross connect to {{ env( 'IDENTITY_ORGNAME' ) }}.

@if( trim( $ppp->description ) )
**Description**: {{ $ppp->description }}
@endif

```
Facility:        {{ $ppp->patchPanel->cabinet->location->name }}
Rack:            {{ $ppp->patchPanel->cabinet->colocation }}
Patch panel:     {{ $ppp->patchPanel->name }}
Colo Reference:  {{ $ppp->colo_circuit_ref }}
Type:            {{ $ppp->patchPanel->cableType() }}
Port:            {{ $ppp->name() }} @if( $ppp->duplexSlavePorts()->count() ) *(duplex port)* @endif

State:           {{ $ppp->states() }}
@if( $ppp->cease_requested_at )
Cease requested: {{  $ppp->cease_requested_at }}
@endif
@if( $ppp->connected_at )
Connected on:    {{  $ppp->connected_at }}
@endif
```

@if( $ppp->patchPanelPortFilesPublic()->count() )
We have attached all the documentation which we have on file regarding this connection.
@endif

@if( strlen( trim( $ppp->notes ) ) )
We have also recorded the following notes:

@foreach( explode( "\n", $ppp->notes ) as $l )
> {{$l}}
@endforeach

@endif

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')
