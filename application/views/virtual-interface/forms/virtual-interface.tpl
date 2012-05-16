
<form class="form-horizontal" enctype="application/x-www-form-urlencoded"
        accept-charset="UTF-8" method="post" horizontal="1"
        {if $isEdit}
            action="{genUrl controller="virtualinterface" action="edit" id=$object.id}"
        {else}
            action="{genUrl controller="virtualinterface" action="add"}"
        {/if}>
            
<div class="row-fluid">

    <div class="span6">
    
        <fieldset>
            <legend>Virtual Interface Details</legend>
            
            {$element->custid}
            {$element->trunk}
            
        </fieldset>
        
    </div>

    <div class="span6">
    
        <fieldset>
            <legend>&nbsp;</legend>
            
            <div id="advanced-options" class="hide">
            
                {$element->name}
                {$element->description}
                {$element->channelgroup}
                {$element->mtu}
                
            </div>
            
        </fieldset>
        
    </div>

    
</div>
        


<div class="form-actions">
    {if isset( $cust )}
        <a class="btn" href="{genUrl controller='customer' action='dashboard' id=$cust.id}">Cancel</a>
    {else}
        <a class="btn btn-success" href="{genUrl controller='vlan-interface' action='quick-add'}">Wizard Add</a>
        <a class="btn" href="{genUrl controller="virtual-interface" action="list"}">Cancel</a>
    {/if}

    <button class="btn" id="btn-advanced-options">Advanced Options</button>
    <input type="submit" name="commit" id="commit" value="{if $isEdit}Save Changes{else}Add{/if}" class="btn btn-primary">

</div>

    
</form>


<script type="text/javascript">

$(document).ready( function(){

	$( '#btn-advanced-options' ).on( 'click', function( event ){

		if( $( '#btn-advanced-options' ).hasClass( 'active' ) )
		    $( '#advanced-options' ).slideUp();
		else
			$( '#advanced-options' ).slideDown();
		
		$( '#btn-advanced-options' ).button( 'toggle' );
		return false;
	});
	
    /*$( '#type' ).bind( 'change', function( event ){
        if( $( '#type' ).val() == 2 )  // associate member
            $( '#full-member-details' ).slideUp( 'fast' );
        else
            $( '#full-member-details' ).slideDown( 'fast' );
    });
    */
    
});

</script>

