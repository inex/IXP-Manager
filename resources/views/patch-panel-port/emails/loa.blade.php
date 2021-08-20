Hi,

You or someone in your organisation requested a LoA on the
following cross connect to {{ env( 'IDENTITY_ORGNAME' ) }}.


```
Facility:       {{ $ppp->patchPanel->cabinet->location->name }}
Rack:           {{ $ppp->patchPanel->cabinet->colocation }}
Patch panel:    {{ $ppp->patchPanel->name }}
Port:           {{ $ppp->name() }} @if( $ppp->duplexSlavePorts()->count() ) *(duplex port)* @endif

Colo Reference: {{ $ppp->colo_circuit_ref }}
Type:           {{ $ppp->patchPanel->cableType() }}
Connector:      {{ $ppp->patchPanel->connectorType() }}
State:          {{ $ppp->states() }}
```

Please find the LoA attached as a PDF.

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')

