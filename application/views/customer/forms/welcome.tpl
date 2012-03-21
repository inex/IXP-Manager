
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

        {$element->message}
        
    </div>
</div>

<div class="form-actions">

    <a class="btn" href="{genUrl controller="customer" action="list"}">Cancel</a>
    <input type="submit" name="commit" id="commit" value="Send Welcome Email" class="btn btn-primary">

</div>

    
</form>



