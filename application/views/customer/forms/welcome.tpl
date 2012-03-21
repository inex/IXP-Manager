
<form class="form-horizontal" enctype="application/x-www-form-urlencoded"
        accept-charset="UTF-8" method="post" horizontal="1"
        {if $isEdit}
            action="{genUrl controller="customer" action="edit" id=$object.id}"
        {else}
            action="{genUrl controller="customer" action="add"}"
        {/if}>
            
<div class="row-fluid">

    <div class="span12">
    
        <fieldset>
            <legend>Send Welcome Email to Customer</legend>
            <br><br>
        </fieldset>
    </div>
</div>

<div class="row-fluid">

    <div class="span6">

        {$element->to}
        {$element->subject}
            
    </div>

    <div class="span6">

        {$element->cc}
        {$element->bcc}
            
    </div>
    
</div>
        
<div class="row-fluid">

    <div class="span12">

    
        <div class="control-group">
        
            <label for="message" class="control-label required">&nbsp;</label>

            <div class="controls">
            
                <textarea style='font-family: Menlo, Monaco, "Courier New", monospace;' name="message" id="message" cols="80" rows="20" class="span12">{$element->message->getValue()}</textarea>

            </div>
            
        </div>
        
    </div>
</div>

<div class="form-actions">

    <a class="btn" href="{genUrl controller="customer" action="list"}">Cancel</a>
    <input type="submit" name="commit" id="commit" value="Send Welcome Email" class="btn btn-primary">

</div>

    
</form>


<script type="text/javascript">

$(document).ready( function(){

    /* $( '#message' ).wysihtml5({

    	'allowObjectResizing':  true
    	    
    }); */
	
});
	
</script>



