{include file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller="change-log"}">Change Log</a> <span class="divider">/</span>
    </li>
    <li class="active">
        <a href="{genUrl controller=$controller action='read'}">Entries</a>
    </li>
</ul>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<dl>

{foreach from=$entries item=e}

    {if !isset( $lastdate )}
        <dt>{$e.livedate}</dt>
        <dd>
            <ul>
            
        {assign var=lastdate value=$e.livedate}
    {/if}
    
    {if $lastdate neq $e.livedate}
    
            </ul>
        </dd>
        <dt>{$e.livedate}</dt>
        <dd>
            <ul>
            
        {assign var=lastdate value=$e.livedate}
    {/if}

    <li>
        {$e.title}
        
        {if $e.details}
            (<a id="more-info-{$e.id}">more</a>)
        {/if}
        
        <div id="info-{$e.id}" class="hide">
            <br />
            <div  class="well">
                {$e.details}
            </div>
        </div>
    </li>

{/foreach}

{if isset( $lastdate )}
        </ul>
    </dd>
</dl>
{/if}


<script>

    $(document).ready(function() {
        
        $('a[id|="more-info"]').click( function( event ){

    		var id = substr( $( this ).attr( 'id' ), 10 );
    		
    		if( $( '#info-' + id ).hasClass( 'hide' ) )
    		{
    			$( '#more-info-' + id ).html( 'less' );
    			$( '#info-' + id ).slideDown( 'slow' );
    			$( '#info-' + id ).removeClass( 'hide' )
    		}
    		else
    		{
    			$( '#more-info-' + id ).html( 'more' );
    			$( '#info-' + id ).slideUp( 'fast' );
    			$( '#info-' + id ).addClass( 'hide' )
		    }
    		
	    });
	    
    } );

</script>

{include file="footer.tpl"}
