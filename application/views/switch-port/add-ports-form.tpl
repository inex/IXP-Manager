<form action="{$element->getAction()}" method="{$element->getMethod()}" enctype="{$element->getAttrib('enctype')}" id="{$element->getId()}" {if $element->getName() != ''}name="{$element->getName()}"{/if} {if $element->getAttrib('target')}target="{$element->getAttrib('target')}"{/if} class="form">

<table>
<tr>
    <td align="right" valign="top">
    	<label for="{$element->switchid->getId()}">
    		{$element->switchid->getLabel()}:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
        {$element->switchid}

        {if $element->switchid->getMessages()}
            <ul class="errors">
                {foreach from=$element->switchid->getMessages() item=messages}
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
    	<label for="{$element->deftype->getId()}">
    		{$element->deftype->getLabel()}:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
        {$element->deftype}
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="numports">
    		Number of First Port:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="numfirstport" id="numfirstport" type="text" size="6" maxlength="3" />
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="numports">
    		Number of Ports:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="numports" id="numports" type="text" size="6" maxlength="3" />
    </td>
</tr>


<tr>
    <td align="right" valign="top">
    	<label for="numports">
    		<code>printf</code> Format:
		</label>
	</td>
    <td valign="top">
    	&nbsp;
		<input name="prefix" id="prefix" type="text" size="20" maxlength="60" /> (e.g. <code>GigabitEthernet%02d</code>)
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
    	
        var numports     = parseInt( $( "#numports"     ).val() );
        var numfirstport = parseInt( $( "#numfirstport" ).val() );

        if( isNaN( numports ) || numports <= 0 )
        {
            alert( "Invalid number of ports!" );
            return false;
        }

        if( isNaN( numfirstport ) || numfirstport < 0 )
        {
            alert( "Invalid number for first port!" );
            return false;
        }

        var c = "<h3>The following ports will be created:</h3>\n\n<table>\n";
        
        for( var i = numfirstport; i < ( numfirstport + numports ); i++ )
        {
			c += "<tr>\n    <td><strong>Name:</strong>&nbsp;<input name=\"np_name" 
				+ ( i - numfirstport ) + "\" value=\"" 
				+ sprintf( trim( $( "#prefix" ).val() ), i ) + "\" /></td>\n"
				+ "\n    <td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Type:</strong>&nbsp;"
				+ "<select id=\"np_type"+ ( i - numfirstport ) + "\" name=\"np_type" 
				+ ( i - numfirstport ) + "\"></select>"
				+ "</td>\n</tr>\n";
        }

        c += "</table>\n";
        
        $( "#gendiv" ).html( c );

        for( var i = 0; i < numports; i++ )
        {
            selid = 'np_type' + i;
        	document.getElementById( selid ).innerHTML = document.getElementById( 'deftype' ).innerHTML;

        	$( '#' + selid ).val( $( '#deftype' ).val() );
        }

        $( '#spansubmit' ).show();
    });
});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#vlanid").trigger( 'change' );
});
	
/* ]]> */ </script> 
{/literal}







