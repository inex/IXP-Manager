
<form class="form-horizontal" enctype="application/x-www-form-urlencoded"
        accept-charset="UTF-8" method="post" horizontal="1"
        action="{genUrl controller="vlaninterface" action="quick-add"}"
>
            

<div class="row-fluid">
    <div class="span12">
        <fieldset>
            <legend>Add New Interface Wizard</legend>
        </fieldset>
    </div>
</div>
        
<div class="row-fluid">
    <div class="span4">

        <fieldset>
            <legend>General Interface Settings</legend>

            {$element->custid}
            {$element->vlanid}
            {$element->trunk}
            {$element->ipv4enabled}
            {$element->ipv6enabled}
            
        </fieldset>
        
    </div>
    <div class="span4">

        <fieldset>
            <legend>Physical Interface Settings</legend>

            {$element->switch_id}
            {$element->switchportid}
            {$element->status}
            {$element->speed}
            {$element->duplex}
            
        </fieldset>

    </div>

    <div class="span4">

        <fieldset>
            <legend>General VLAN Settings</legend>

            {$element->maxbgpprefix}
            {$element->irrdbfilter}
            {$element->mcastenabled}
            {$element->rsclient}
            {$element->as112client}
                        
        </fieldset>

    </div>
</div>
    
<div class="row-fluid">

    <div class="span4">
        <div id="ipv4details" class="hide">
            {$element->ipv4DisplayGroup}
        </div>
    </div>

    <div class="span4">
        <div id="ipv6details" class="hide">
            {$element->ipv6DisplayGroup}
        </div>
    </div>
    
    <div class="span4"></div>
    
</div>
        

{$element->preselectIPv4Address}
{$element->preselectIPv6Address}
{$element->preselectVlanInterface}
{$element->preselectSwitchPort}
{$element->preselectPhysicalInterface}

<div class="form-actions">
    <a class="btn btn-inverse" href="{genUrl controller="virtual-interface" action="add"}">Skip Wizard</a>
    {if $element->custid->getValue() > 0}
        <a class="btn" href="{genUrl controller="customer" action="dashboard" id=$element->custid->getValue()}">Cancel</a>
    {else}
        <a class="btn" href="{genUrl controller="virtual-interface" action="list"}">Cancel</a>
    {/if}
    <input type="submit" name="commit" id="commit" value="Add" class="btn btn-primary">
</div>

    
</form>

