Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

You have a cross connect to INEX which our records indicate is no longer required.

Please contact the co-location facility and request that they cease the following cross connect:

```
Facility:        {{ $ppp->getPatchPanel()->getCabinet()->getLocation()->getName() }}
Rack:            {{ $ppp->getPatchPanel()->getCabinet()->getCololocation() }}
Colo Reference:  {{ $ppp->getColoCircuitRef() }}
Patch panel:     {{ $ppp->getPatchPanel()->getName() }}
Type:            {{ $ppp->getPatchPanel()->resolveCableType() }}
Port:            {{ $ppp->getName() }} @if( $ppp->hasSlavePort() ) *(duplex port)* @endif

@if( $ppp->getConnectedAt() )
Connected on:    {{  $ppp->getConnectedAt()->format('Y-m-d') }}
@endif
```

@if( $ppp->hasPublicFiles() )
We have attached documentation which we have on file regarding this connection which may help process this request.
@endif

@if( strlen( trim( $ppp->getNotes() ) ) )
We have also recorded the following notes:

@foreach( explode( "\n", $ppp->getNotes() ) as $l )
> {{$l}}
@endforeach

@endif

** Please email us and confirm when this has been completed. **

@include('patch-panel-port/emails/signature')

