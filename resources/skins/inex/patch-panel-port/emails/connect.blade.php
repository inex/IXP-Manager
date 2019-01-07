
Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

As promised, here are the cross connect details for your new connection with INEX.

Please order a {{ $ppp->getPatchPanel()->resolveCableType() }} cross connect where our demarcation point is:

```
Facility:       {{ $ppp->getPatchPanel()->getCabinet()->getLocation()->getName() }}
Rack:           {{ $ppp->getPatchPanel()->getCabinet()->getCololocation() }}
Colo Reference: {{ $ppp->getColoCircuitRef() }}
Type:           {{ $ppp->getPatchPanel()->resolveCableType() }}
Patch panel:    {{ $ppp->getPatchPanel()->getName() }}
Port:           {{ $ppp->getName() }} @if( $ppp->hasSlavePort() ) *(duplex port)* @endif

```

**We have attached a LoA to this email also.**

@if( $ppp->getSwitchPort() )
This request is in relation the following connection:

```
Switch Port:   {{ $ppp->getSwitchName() }}::{{ $ppp->getSwitchPortName() }}
```
@endif

Note that not all of our colocation providers notify us in a timely fashion when new connections are complete. Please contact us when you have been advised that this connection has been completed. We would also appreciate if you could include the colocation reference number and any associated documentation / test results for our own files.

@include('patch-panel-port/emails/signature')

