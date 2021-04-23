@component('mail::message')

# Note {{$event->actionDescription()}} :: {{$event->customer()->getFormattedName()}}

**{{$event->actionDescription()}} by:** [{{$event->user()->username}}](mailto:{{$event->user()->email}})

**Visibility:** @if( $event->eitherNote()->private) Admins only @else Admins and customer @endif


@if( $event->note() )

## {{$event->note()->title}}

@component('mail::panel')
{{$event->note()->note}}
@endcomponent

@endif


@if( $event->oldNote() )

## Old Note Details

### {{$event->oldNote()->title}}

@if( $event->note() && $event->note()->private !== $event->oldNote()->private )
**Visibility:** @if( $event->oldNote()->private) Admins only @else Admins and customer @endif
@endif

@component('mail::panel')
{{$event->oldNote()->note}}
@endcomponent

@endif

@endcomponent