
<form class="form-horizontal" enctype="application/x-www-form-urlencoded"
        accept-charset="UTF-8" method="post" horizontal="1"
        {if $isEdit}
            action="{genUrl controller="vlaninterface" action="edit" id=$object.id}"
        {else}
            action="{genUrl controller="vlaninterface" action="add"}"
        {/if}>
            
<div class="row-fluid">
    <div class="span6">
    
        {$element->virtualinterfaceid}
        {$element->vlanid}
        {$element->irrdbfilter}
        {$element->mcastenabled}
        
    </div>
    <div class="span6">
    
        {$element->maxbgpprefix}
        {$element->rsclient}
        {$element->as112client}
        {$element->busyhost}
        
    </div>
    </div>

<div class="row-fluid">

    <div class="span6">
        {$element->ipv4enabled}
        
        <div id="ipv4details" class="hide">
            {$element->ipv4DisplayGroup}
        </div>
    </div>

    <div class="span6">
        {$element->ipv6enabled}
        
        <div id="ipv6details" class="hide">
            {$element->ipv6DisplayGroup}
        </div>
    </div>
    
    
</div>
        

{$element->preselectIPv4Address}
{$element->preselectIPv6Address}
{$element->preselectVlanInterface}

<div class="form-actions">
    <a class="btn" href="{genUrl controller="virtual-interface" action="edit" id=$element->virtualinterfaceid->getValue()}">Cancel</a>
    <input type="submit" name="commit" id="commit" value="{if $isEdit}Save Changes{else}Add{/if}" class="btn btn-primary">
</div>

    
</form>



