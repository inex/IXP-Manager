@component('mail::message')

# Note {{$event->getActionDescription()}} :: {{$event->getCustomer()->getFormattedName()}}

**{{$event->getActionDescription()}} by:** [{{$event->getUser()->getUsername()}}](mailto:{{$event->getUser()->getEmail()}})

**Visibility:** @if( $event->getEitherNote()->getPrivate()) Admins only @else Admins and customer @endif


@if( $event->getNote() )

## {{$event->getNote()->getTitle()}}

@component('mail::panel')
{{$event->getNote()->getNote()}}
@endcomponent

@endif


@if( $event->getOldNote() )

## Old Note Details

### {{$event->getOldNote()->getTitle()}}

@if( $event->getNote() && $event->getNote()->getPrivate() != $event->getOldNote()->getPrivate() )
**Visibility:** @if( $event->getOldNote()->getPrivate()) Admins only @else Admins and customer @endif
@endif

@component('mail::panel')
{{$event->getOldNote()->getNote()}}
@endcomponent

@endif

@endcomponent
