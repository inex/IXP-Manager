
<form class="form-horizontal" enctype="application/x-www-form-urlencoded"
        accept-charset="UTF-8" method="post" horizontal="1"
        {if $isEdit}
            action="{genUrl controller="customer" action="edit" id=$object.id}"
        {else}
            action="{genUrl controller="customer" action="add"}"
        {/if}>
            
<div class="row-fluid">

    <div class="span6">
    
        <fieldset>
            <legend>Customer Details</legend>
            
            {$element->name}
            {$element->type}
            {$element->shortname}
            {$element->corpwww}
            {$element->datejoin}
            {$element->dateleave}
            {$element->status}
            
        </fieldset>
        
    </div>

    <div class="span6">
    
        <fieldset>
            <legend>Billing Details</legend>
            
            {$element->billingContact}
            {$element->billingAddress1}
            {$element->billingAddress2}
            {$element->billingCity}
            {$element->billingCountry}
            
        </fieldset>
        
    </div>

    
</div>
        
<div id="full-member-details" class="row-fluid {if $isEdit and $object.type eq '2'}hide{/if}">

    <div class="span6">
    
        <fieldset>
            <legend>Peering Details</legend>

            {$element->autsys}
            {$element->maxprefixes}
            {$element->peeringemail}
            {$element->peeringmacro}
            {$element->peeringpolicy}
            {$element->irrdb}
            {$element->activepeeringmatrix}
    
        </fieldset>
    </div>

    <div class="span6">
    
        <fieldset>
            <legend>NOC Details</legend>

            {$element->nocphone}
            {$element->noc24hphone}
            {$element->nocfax}
            {$element->nocemail}
            {$element->nochours}
            {$element->nocwww}
    
        </fieldset>
    </div>
    
</div>
            


<div class="form-actions">

    <a class="btn" href="{genUrl controller="customer" action="list"}">Cancel</a>
    <input type="submit" name="commit" id="commit" value="{if $isEdit}Save Changes{else}Add New Customer{/if}" class="btn btn-primary">

</div>

    
</form>


<script type="text/javascript">

$(document).ready( function(){
	
    $('#datejoin').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#dateleave').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });

    $( '#type' ).bind( 'change', function( event ){
        if( $( '#type' ).val() == 2 )  // associate member
            $( '#full-member-details' ).slideUp( 'fast' );
        else
            $( '#full-member-details' ).slideDown( 'fast' );
    });
    
    
});

</script>

