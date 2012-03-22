
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
            <legend>{if $isEdit}Edit the{else}Add a{/if} Change Log Entry</legend>
        </fieldset>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        {$element->title}
    </div>
</div>

<div class="row-fluid">
    <div class="span6">
        {$element->visibility}
    </div>
    <div class="span6">
        {$element->livedate}
    </div>
</div>
        
<div class="row-fluid">

    <div class="span12">

    
        <div class="control-group">
        
            <label for="message" class="control-label required">Details</label>

            <div class="controls">
            
                <textarea name="details" id="details" cols="80" rows="10" class="span12">{$element->details->getValue()}</textarea>

            </div>
            
        </div>
        
    </div>
</div>

<div class="form-actions">

    <a class="btn" href="{genUrl controller="change-log" action="list"}">Cancel</a>
    <input type="submit" name="commit" id="commit" value="{if not $isEdit}Add{else}Save Changes{/if}" class="btn btn-primary">

</div>

    
</form>

<link rel="stylesheet" href="{genUrl}/js/jwysiwyg/jquery.wysiwyg.old-school.css" type="text/css" />
<script type="text/javascript" src="{genUrl}/js/jwysiwyg/jquery.wysiwyg.js"></script>

<script type="text/javascript">

$(document).ready( function(){

	
	$('#details').wysiwyg( {
		css: '{genUrl}/css/800-bootstrap.css'
	} );

    $( '#livedate' ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
});
	
</script>



