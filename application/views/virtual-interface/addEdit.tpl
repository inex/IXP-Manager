{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    {if isset( $cust )}
        <li>
            <a href="{genUrl controller='customer' action='dashboard' id=$cust.id}">{$cust.name}</a> <span class="divider">/</span>
        </li>
    {/if}
    <li>
        <a href="{genUrl controller='virtual-interface' action='list'}">Virtual Interfaces</a> <span class="divider">/</span>
    </li>
    <li class="active">
        {if $isEdit}Edit{else}Create New Customer Interface{/if}
    </li>
</ul>

{include file="message.tpl"}

<div class="well">
{$form}
</div>

{if $isEdit}

<div>

    <h3>
        Physical Interfaces
        <a class="btn btn-mini"
            href="{genUrl controller='physical-interface' action="add" virtualinterfaceid=$object.id}"><i class="icon-plus"></i></a>
    </h3>

    {if count( $phyInts )}
        <table class="table">
    
        <thead>
            <tr>
                <th>Location</th>
                <th>Switch</th>
                <th>Port</th>
                <th>Speed/Duplex</th>
                <th></th>
            </tr>
        </thead>
    
        <tbody>
        {foreach from=$phyInts item=int}
    
            <tr>
                <td>
                    {$int.Switchport.SwitchTable.Cabinet.Location.name}
                </td>
                <td>
                    {$int.Switchport.SwitchTable.name}
                </td>
                <td>
                    {$int.Switchport.name}
                </td>
                <td>
                    {$int.speed}/{$int.duplex}
                </td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-mini" href="{genUrl controller='physical-interface' action="edit"   id=$int.id}"><i class="icon-pencil"></i></a>
                        <a data-url="{genUrl controller="physical-interface" action="delete" id=$int.id virtualinterfaceid=$object.id}"
                            class="btn btn-mini" id="pi-object-delete-{$int.id}"><i class="icon-trash"></i></a>
                    </div>
                </td>
            </tr>
    
        {/foreach}
    
        </tbody>
    
        </table>
        
    {else}
    
        <p>
            There are no physical interfaces defined for this virtual interface.
            <a href="{genUrl controller="physical-interface" action="add" virtualinterfaceid=$object.id}">Add one now...</a>
        </p>
        
    {/if}

    <br />
</div>


<div>

    <h3>
        VLAN Interfaces
        <a class="btn btn-mini"
            href="{genUrl controller='vlan-interface' action="add" virtualinterfaceid=$object.id}"><i class="icon-plus"></i></a>
    </h3>

    {if count( $vlanInts )}
    
        <table class="table">
    
            <thead>
                <tr>
                    <th>VLAN Name</th>
                    <th>VLAN ID</th>
                    <th>IPv4 Address</th>
                    <th>IPv6 Address</th>
                    <th></th>
                </tr>
            </thead>
    
            <tbody>
            {foreach from=$vlanInts item=int}
    
                <tr>
                    <td>
                        {$int.Vlan.name}
                    </td>
                    <td>
                        {$int.Vlan.number}
                    </td>
                    <td>
                        {$int.Ipv4address.address}
                    </td>
                    <td>
                        {$int.Ipv6address.address}
                    </td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-mini" href="{genUrl controller='vlan-interface' action="edit"   id=$int.id}"><i class="icon-pencil"></i></a>
                            <a data-url="{genUrl controller="vlan-interface" action="delete" id=$int.id virtualinterfaceid=$object.id}"
                                class="btn btn-mini" id="vi-object-delete-{$int.id}"><i class="icon-trash"></i></a>
                        </div>
                    </td>
                </tr>
    
            {/foreach}
    
            </tbody>
    
        </table>

    {else}
    
        <p>
            There are no VLAN interfaces defined for this virtual interface.
            <a href="{genUrl controller="vlan-interface" action="add" virtualinterfaceid=$object.id}">Add one now...</a>
        </p>
        
    {/if}
        
</div>

{/if}

{include file="confirm-dialog.tpl"}

<script type="text/javascript">

$(document).ready(function() {

	$('a[id|="pi-object-delete"]').click( function( event ){

		var id = substr( $( this ).attr( 'id' ), 17 );

		if( $( this ).attr( 'data-url' ) ) {
		    $( '#modal-confirm-action' ).attr( 'href', $( this ).attr( 'data-url' ) );
		} else {
		    $( '#modal-confirm-action' ).attr( 'href', "{genUrl controller=$controller action="delete"}/id/" + id );
	    }
		
		$( "#modal-confirm" ).modal( { 'show': true } );
	});

	$('a[id|="vi-object-delete"]').click( function( event ){

		var id = substr( $( this ).attr( 'id' ), 17 );

		if( $( this ).attr( 'data-url' ) ) {
		    $( '#modal-confirm-action' ).attr( 'href', $( this ).attr( 'data-url' ) );
		} else {
		    $( '#modal-confirm-action' ).attr( 'href', "{genUrl controller=$controller action="delete"}/id/" + id );
	    }
		
		$( "#modal-confirm" ).modal( { 'show': true } );
	});

});

</script>

{include file="footer.tpl"}

