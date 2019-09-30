@component('mail::message')

@component('mail::panel')
    Notification of <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> MAC Address Change
@endcomponent

On {{ date('Y-m-d H:i:s') }}, the user {{ $event->user->getUsername() }} ({{ $event->user->getEmail() }})
of {{ $event->customer }} {{ $event->action == 'add' ? 'added' : 'deleted' }}
the MAC address ``{{ $event->mac }}`` {{ $event->action == 'add' ? 'to' : 'from' }}
[this VLAN interface (id: {{ $event->vli->getId() }})]({{ route( "layer2-address@forVlanInterface" , [ "vliid" => $event->vli->getId() ] ) }}).

**NB:** You should view the link above before making any switch changes to ensure the <?= config( 'ixp_fe.lang.customer.one' ) ?> has completed all their editing.

@endcomponent
