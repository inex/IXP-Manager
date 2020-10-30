@component('mail::message')
    Notification of <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> MAC Address Change

On {{ date('Y-m-d H:i:s') }}, the user {{ $event->user->username }} ({{ $event->user->email }})
of {{ $event->customer }} {{ $event->action === 'add' ? 'added' : 'deleted' }}
the MAC address ``{{ $event->mac }}`` {{ $event->action === 'add' ? 'to' : 'from' }}
[this VLAN interface (id: {{ $event->vli->id }})]({{ route( "layer2-address@forVlanInterface" , [ "vli" => $event->vli->id ] ) }}).

**NB:** You should view the link above before making any switch changes to ensure the <?= config( 'ixp_fe.lang.customer.one' ) ?> has completed all their editing.

@endcomponent