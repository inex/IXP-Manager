@component('mail::message')

#**Note {{$type}}**

**Customer:** {{$cust->getName()}}

**Changed by:** {{$user->getContact()->getName()}} - <{{$user->getContact()->getEmail()}}>

@if( $cn )

##**Note Details**
@component('mail::panel')
**Title:**      {{$cn->getTitle()}}

**Visibility:**@if( $cn->getPrivate()) Private @else Public @endif


**Note:**
{{$cn->getNote()}}
@endcomponent

@endif


@if( $ocn )

##**Old Note Details**
@component('mail::panel')
**Title:**      {{$ocn->getTitle()}}

**Visibility:**@if( $ocn->getPrivate()) Private @else Public @endif


**Note:**
{{$ocn->getNote()}}
@endcomponent

@endif

@endcomponent
