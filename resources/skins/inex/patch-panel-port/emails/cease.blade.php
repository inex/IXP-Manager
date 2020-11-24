Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

You have a cross connect to INEX which our records indicate is no longer required.

Please contact the co-location facility and request that they cease the following cross connect:

```
Facility:        {{ $ppp->patchPanel->cabinet->location->name }}
Rack:            {{ $ppp->patchPanel->cabinet->colocation }}
Colo Reference:  {{ $ppp->colo_circuit_ref }}
Patch panel:     {{ $ppp->patchPanel->name }}
Type:            {{ $ppp->patchPanel->cableType() }}
Port:            {{ $ppp->name() }} @if( $ppp->duplexSlavePorts()->count() ) *(duplex port)* @endif

@if( $ppp->connected_at )
Connected on:    {{  $ppp->connected_at }}
@endif
```

@if( $ppp->patchPanelPortFilesPublic()->count() )
We have attached documentation which we have on file regarding this connection which may help process this request.
@endif

@if( strlen( trim( $ppp->notes ) ) )
We have also recorded the following notes:

@foreach( explode( "\n", $ppp->notes ) as $l )
> {{$l}}
@endforeach

@endif

** Please email us and confirm when this has been completed. **

@include('patch-panel-port/emails/signature')

