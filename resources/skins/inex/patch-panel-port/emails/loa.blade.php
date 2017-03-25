Hi,

You or someone in your organisation requested a LoA on the following cross connect to INEX.


```
Colo Reference: {{ $ppp->getColoCircuitRef() }}
Patch panel:    {{ $ppp->getPatchPanel()->getName() }}
Port:           {{ $ppp->getName() }}
State:          {{ $ppp->resolveStates() }}
```

Please find the LoA attached as a PDF.

@include('patch-panel-port/emails/signature')

