Hi,

You or someone in your organisation requested a details on the
following cross connect to {{ env( 'IDENTITY_ORGNAME' ) }}.

@if( trim( $ppp->getDescription() ) )
**Description**: {{ $ppp->getDescription() }}
@endif

```
Facility:        {{ $ppp->getPatchPanel()->getCabinet()->getLocation()->getName() }}
Rack:            {{ $ppp->getPatchPanel()->getCabinet()->getCololocation() }}
Patch panel:     {{ $ppp->getPatchPanel()->getName() }}
Colo Reference:  {{ $ppp->getColoCircuitRef() }}
Type:            {{ $ppp->getPatchPanel()->resolveCableType() }}
Port:            {{ $ppp->getName() }} @if( $ppp->hasSlavePort() ) *(duplex port)* @endif

State:           {{ $ppp->resolveStates() }}
@if( $ppp->getCeaseRequestedAt() )
Cease requested: {{  $ppp->getCeaseRequestedAt()->format('Y-m-d') }}
@endif
@if( $ppp->getConnectedAt() )
Connected on:    {{  $ppp->getConnectedAt()->format('Y-m-d') }}
@endif
```

@if( $ppp->hasPublicFiles() )
We have attached all the documentation which we have on file regarding this connection.
@endif

@if( strlen( trim( $ppp->getNotes() ) ) )
We have also recorded the following notes:

@foreach( explode( "\n", $ppp->getNotes() ) as $l )
> {{$l}}
@endforeach

@endif

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')
