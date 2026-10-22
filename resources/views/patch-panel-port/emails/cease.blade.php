{{ __( 'Hi,' ) }}

** {{ __( 'ACTION REQUIRED - PLEASE SEE BELOW' ) }} **

{{ __( 'You have a cross connect to :org which our records indicate is no longer required.', [ 'org' => env( 'IDENTITY_ORGNAME' ) ] ) }}

{{ __( 'Please contact the co-location facility and request that they cease the following cross connect:' ) }}


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
{{ __( 'We have attached documentation which we have on file regarding this connection which may help process this request.' ) }}
@endif

@if( strlen( trim( $ppp->notes ) ) )
{{ __( 'We have also recorded the following notes:' ) }}

@foreach( explode( "\n", $ppp->notes ) as $l )
> {{$l}}
@endforeach

@endif

{{ __( 'If you have any queries about this, please reply to this email.' ) }}

** {{ __( 'Please email us and confirm when this has been completed.' ) }} **

@include('patch-panel-port/emails/signature')

