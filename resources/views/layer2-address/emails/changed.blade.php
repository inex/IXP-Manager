At {{ date('Y-m-d H:i:s') }} user {{ $user->getUsername() }}/{{$user->getEmail() }}
<br>
of {{ $user->getCustomer()->getName() }} {{ $added ? 'added' : 'deleted' }} the MAC
address {{ $mac }} from
<a href="{{ route( "Layer2AddressController@forVlanInterface" , [ "id" => $vli->getId() ] ) }}">this VLAN
interface (id: {{ $vli->getId() }}) </a>.
<br>
You should view the link above before making any switch changes to ensure the customer
has completed their editing.