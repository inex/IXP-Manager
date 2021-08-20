
Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

As promised, here are the cross connect details for your new connection with INEX.

Please order a {{ $ppp->patchPanel->cableType() }} cross connect to the following demarcation point:

```
Facility:       {{ $ppp->patchPanel->cabinet->location->name }}
Rack:           {{ $ppp->patchPanel->cabinet->colocation }}
Colo Reference: {{ $ppp->colo_circuit_ref }}
Type:           {{ $ppp->patchPanel->cableType() }}
Patch panel:    {{ $ppp->patchPanel->name }}
Port:           {{ $ppp->name() }} @if( $ppp->duplexSlavePorts()->count() ) *(duplex port)* @endif

```

**We have attached a LoA to this email also.**

@if( $ppp->switchPort )
This request is in relation the following connection:

```
Switch Port:   {{ $ppp->switchPort->switcher->name }}::{{ $ppp->switchPort->name }}
```
@endif

Note that not all of our colocation providers notify us in a timely fashion when new cross-connects are complete. Please contact us when you have been advised that this connection has been completed. We would also appreciate if you could include the colocation reference number and any associated documentation / test results for our own files.

@include('patch-panel-port/emails/signature')

