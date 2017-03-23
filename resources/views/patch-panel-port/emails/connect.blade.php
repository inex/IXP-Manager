
Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

We have allocated the following cross connect demarcation point
for your connection to {{ env( 'IDENTITY_ORGNAME' ) }}.

Please order a {{ $ppp->getPatchPanel()->resolveCableType() }} cross connect where our demarcation point is:

```
Patch panel:    {{ $ppp->getPatchPanel()->getName() }}
Port:           {{ $ppp->getName() }}
```

@if( $ppp->getSwitchPort() )
This request is in relation the following connection:

```
Switch Port:   {{ $ppp->getSwitchName() }}::{{ $ppp->getSwitchPortName() }}
```
@endif

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')

