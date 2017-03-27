Hi,

You or someone in your organisation requested a LoA on the
following cross connect to {{ env( 'IDENTITY_ORGNAME' ) }}.


```
Colo Reference: {{ $ppp->getColoCircuitRef() }}
Patch panel:    {{ $ppp->getPatchPanel()->getName() }}
Port:           {{ $ppp->getName() }} @if( $ppp->hasSlavePort() ) *(duplex port)* @endif

State:          {{ $ppp->resolveStates() }}
```

Please find the LoA attached as a PDF.

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')

