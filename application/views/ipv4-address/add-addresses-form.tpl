<form action="{$element->getAction()}" method="{$element->getMethod()}" enctype="{$element->getAttrib('enctype')}" id="{$element->getId()}" {if $element->getName() != ''}name="{$element->getName()}"{/if} {if $element->getAttrib('target')}target="{$element->getAttrib('target')}"{/if} class="form">

<table>
<tr>
    <td align="right" valign="top">
    	<label for="{$element->vlanid->getId()}">
    		{$element->vlanid->getLabel()}:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
        {$element->vlanid}

        {if $element->vlanid->getMessages()}
            <ul class="errors">
                {foreach from=$element->vlanid->getMessages() item=messages}
                    {foreach from=$messages item=msg}
                        <li>{$msg}</li>
                    {/foreach}
                {/foreach}
            </ul>
        {/if}
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="{$element->type->getId()}">
    		{$element->type->getLabel()}:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
        {$element->type}
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="numfirst">
    		Last Section of First Address:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="numfirst" id="numfirst" type="text" size="6" maxlength="4" />
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="numaddrs">
    		Number of Addresses:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="numaddrs" id="numaddrs" type="text" size="6" maxlength="3" />
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="prefix">
    		Start of Address:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="prefix" id="prefix" type="text" size="20" maxlength="60" /> (e.g. <code>192.0.2.</code> or <code>2001:db8:85a3::370:</code>)
    </td>
</tr>

<tr>
    <td align="right" valign="top">
	</td>
    <td valign="top">
    	&nbsp;
		<button id="genbutton" type="button">Generate</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span id="spansubmit">{$element->commit}</span>
    </td>
</tr>



</table>


<div id="gendiv"></div>

</form>



{literal}
<script type="text/javascript"> /* <![CDATA[ */ 

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

        var c = "<h3>The following " + $( "#type" ).val() + " addresses will be created:</h3>\n\n<table>\n";
        
        for( var i = numfirst; i < ( numfirst + numaddrs ); i++ )
        {
			c += "<tr>\n    <td><strong>Address:</strong>&nbsp;<input name=\"np_name" 
				+ ( i - numfirst ) + "\" value=\"" 
				+ trim( $( "#prefix" ).val() )
				+ ( $( "#type" ).val() == 'IPv6' ? i.toString( 16 ) : i ) 
				+ "\" /></td>\n"
				+ "</tr>\n";
        }

        c += "</table>\n";

        $( "#gendiv" ).html( c );

        $( '#spansubmit' ).show();
    });
});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#vlanid").trigger( 'change' );
});


function h2d( h )
{
	return parseInt( h, 16 );
}

function d2h( d )
{
	return d.toString( 16 );
}
	

/* ]]> */ </script> 
{/literal}







