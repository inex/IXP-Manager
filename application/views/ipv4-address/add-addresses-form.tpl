<form action="{$element->getAction()}" method="{$element->getMethod()}" enctype="{$element->getAttrib('enctype')}" id="{$element->getId()}" {if $element->getName() != ''}name="{$element->getName()}"{/if} {if $element->getAttrib('target')}target="{$element->getAttrib('target')}"{/if} class="form">

    {$element->vlanid}
    {$element->type}


<div class="control-group">
    <label class="control-label required" for="numfirst">Last Section of First Address</label>
    
    <div class="controls">
		<input name="numfirst" id="numfirst" type="text" size="6" maxlength="4" />
	</div>
</div>
		
<div class="control-group">
    <label class="control-label required" for="numaddrs">Number of Addresses</label>
    
    <div class="controls">
		<input name="numaddrs" id="numaddrs" type="text" size="6" maxlength="3" />
	</div>
</div>

<div class="control-group">
    <label class="control-label required" for="prefix">Start of Address</label>
    
    <div class="controls">
		<input name="prefix" id="prefix" type="text" size="20" maxlength="60" /> (e.g. <code>192.0.2.</code> or <code>2001:db8:85a3::370:</code>)
	</div>
</div>

<div class="control-group">
    <label class="control-label" for="genbutton"></label>
    <div class="controls">
        <button class="btn btn-primary" id="genbutton">Generate</button>
    </div>
</div>

<div id="gendiv"></div>

<div id="spansubmit" class="form-actions hide">

    <a href="{genUrl}" class="btn">Cancel</a>
    <input type="submit" class="btn btn-primary" name="commit" value="Generate" />

</div>

</form>

<script type="text/javascript">

$( "#spansubmit" ).hide();

$( function()
{
	$( "#genbutton" ).click( function()
    {
    	$( "#gendiv" ).html( "" );
    	$( "#spansubmit" ).hide();
    	
        var numaddrs = parseInt( $( "#numaddrs" ).val() );

        if( $( '#type' ).val() == 'IPv6' )
        	var numfirst = parseInt( $( "#numfirst" ).val(), 16 );
        else
        	var numfirst = parseInt( $( "#numfirst" ).val(), 10 );
        
        if( isNaN( numaddrs ) || numaddrs <= 0 )
        {
            alert( "Invalid number of addresses!" );
            return false;
        }

        if( isNaN( numfirst ) || numfirst < 0 )
        {
            alert( "Invalid number for first address!" );
            return false;
        }

        var c = "<h3>The following " + $( "#type" ).val() + " addresses will be created:</h3>\n\n"

            + "<table class=\"table\">\n"
            + "<thead><tr><th></th><th>Address</th></thead><tbody>"
        
        for( var i = numfirst; i < ( numfirst + numaddrs ); i++ )
        {
			c += "<tr>\n    <td><strong>Address:</strong></td><td><input name=\"np_name"
				+ ( i - numfirst ) + "\" value=\""
				+ trim( $( "#prefix" ).val() )
				+ ( $( "#type" ).val() == 'IPv6' ? i.toString( 16 ) : i )
				+ "\" /></td>\n"
				+ "</tr>\n";
        }

        c += "</tbody></table>\n";

        $( "#gendiv" ).html( c );

        $( '#spansubmit' ).show();

        return false;
    });
});

function h2d( h )
{
	return parseInt( h, 16 );
}

function d2h( d )
{
	return d.toString( 16 );
}
	

</script>

