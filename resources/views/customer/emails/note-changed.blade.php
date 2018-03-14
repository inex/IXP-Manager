@component('mail::message')

#**Note {{$event->getActionDescription()}}**

**Customer:** {{$event->getCustomer()->getName()}}

**Changed by:** {{$event->getUser()->getContact()->getName()}} - <{{$event->getUser()->getContact()->getEmail()}}>

@if( $event->getNote() )

##**Note Details**
@component('mail::panel')
**Title:**      {{$event->getNote()->getTitle()}}

**Visibility:**@if( $event->getNote()->getPrivate()) Private @else Public @endif


**Note:**
{{$event->getNote()->getNote()}}
@endcomponent

@endif


@if( $event->getOldNote() )

##**Old Note Details**
@component('mail::panel')
**Title:**      {{$event->getOldNote()->getTitle()}}

**Visibility:**@if( $event->getOldNote()->getPrivate()) Private @else Public @endif


**Note:**
{{$event->getOldNote()->getNote()}}
@endcomponent

@endif

@endcomponent
