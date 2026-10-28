
{{ __( 'Hi,' ) }}

** {{ __( 'ACTION REQUIRED - PLEASE SEE BELOW' ) }} **

{{ __( 'We have allocated the following cross connect demarcation point for your connection to :org.', [ 'org' => env( 'IDENTITY_ORGNAME' ) ] ) }}

{{ __( 'Please order a :cableType cross connect where our demarcation point is:', [ 'cableType' => $ppp->patchPanel->cableType() ] ) }}

```
Facility:       {{ $ppp->patchPanel->cabinet->location->name }}
Rack:           {{ $ppp->patchPanel->cabinet->colocation }}
Colo Reference: {{ $ppp->colo_circuit_ref }}
Type:           {{ $ppp->patchPanel->cableType() }}
Patch panel:    {{ $ppp->patchPanel->name }}
Port:           {{ $ppp->name() }} @if( $ppp->duplexSlavePorts()->count() ) *(duplex port)* @endif

```

@if( $ppp->switchPort )
{{ __( 'This request is in relation the following connection:' ) }}

```
Switch Port:   {{ $ppp->switchPort->switcher->name }}::{{ $ppp->switchPort->name }}
```
@endif

{{ __( 'If you have any queries about this, please reply to this email.' ) }}

@include('patch-panel-port/emails/signature')

