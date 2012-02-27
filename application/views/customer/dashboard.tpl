{include file="header.tpl" pageTitle="IXP Manager :: Administrator's Home"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller="customer" action="list"}">Customers</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Dashboard for {$acust.name}
    </li>
    <li class="pull-right">
        <div class="btn-toolbar" style="display: inline;">
            <div class="btn-group">
                <a class="btn btn-mini" href="{genUrl controller='customer' action="edit" id=$acust.id}"><i class="icon-pencil"></i></a>
                <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this record?' );" href="{genUrl controller='customer' action="delete" id=$acust.id}"><i class="icon-trash"></i></a>
            </div>
    
            <div class="btn-group">
                {assign var=haveprev value=0}
                {assign var=havenext value=0}
                {foreach from=$customers key=k item=i name=custs}
                    {if $smarty.foreach.custs.first}
                        {assign var=cidprev  value=$k}
                    {/if}
                    
                    {if $k eq $acust.id}
                        {assign var=haveprev value=1}
                    {elseif $haveprev and not $havenext}
                        {assign var=havenext value=1}
                        {assign var=cidnext value=$k}
                    {/if}
                    
                    {if not $haveprev}
                        {assign var=cidprev value=$k}
                    {/if}
                    
                    {if not $havenext and $smarty.foreach.custs.last}
                        {assign var=cidnext value=$k}
                    {/if}
                    
                {/foreach}
                
                <a class="btn btn-mini" href="{genUrl controller='customer' action="dashboard" id=$cidprev}"><i class="icon-chevron-left"></i></a>
                <a class="btn btn-mini" href="{genUrl controller='customer' action="dashboard" id=$acust.id}"><i class="icon-refresh"></i></a>
                <a class="btn btn-mini" href="{genUrl controller='customer' action="dashboard" id=$cidnext}"><i class="icon-chevron-right"></i></a>
            </div>
        </div>
    </li>
</ul>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<div class="row-fluid">

    <div class="span6">
    
        <div class="row-fluid">
        
            <h3>
                {$acust.name}
                {if $acust.dateleave and $acust.dateleave neq '0000-00-00'}
                    - <strong>ACCOUNT CLOSED</strong>
                {/if}
            </h3>
            <h4 style="padding-left: 30px;">
                {if $acust.corpwww}<a href="{$acust.corpwww}">{$acust.corpwww}</a>{/if}
                {if $acust.peeringemail} - {mailto address=$acust.peeringemail}{/if}
            </h4>
            <br />
            
            <table class="table">
                <tbody>
                
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>{Cust::$CUST_STATUS_TEXT[$acust.status]}</td>
                            <td><strong>Joined</strong></td>
                            <td>{$acust.datejoin}</td>
                        </tr>
                        <tr>
                            <td><strong>Type</strong></td>
                            <td>{Cust::$CUST_TYPES_TEXT[$acust.type]}</td>
                            <td><strong>Left</strong></td>
                            <td>{$acust.dateleave}</td>
                        </tr>
                        <tr>
                            <td><strong>Peering Policy</strong></td>
                            <td>{$acust.peeringpolicy}</td>
                            <td><strong>ASN</strong></td>
                            <td>{$acust.autsys|asnumber} {if $acust.peeringmacro}({$acust.peeringmacro}){/if}</td>
                        </tr>
                        <tr>
                            <td><strong>NOC Details</strong></td>
                            <td>
                                {if $acust.nochours}    {$acust.nochours}<br />    {/if}
                                {if $acust.nocemail}    {mailto address=$acust.nocemail}<br />{/if}
                                {if $acust.nocwww}      {$acust.nocwww}<br />      {/if}
                                {if $acust.nocphone}    {$acust.nocphone}<br />    {/if}
                                {if $acust.noc24hphone} {$acust.noc24hphone} (24h) {/if}
                            </td>
                            <td><strong>Billing Details</strong></td>
                            <td>
                                {if $acust.billingContact}    {$acust.billingContact}<br />    {/if}
                                {if $acust.billingAddress1}    {$acust.billingAddress1}<br />    {/if}
                                {if $acust.billingAddress2}    {$acust.billingAddress2}<br />    {/if}
                                {if $acust.billingCity}    {$acust.billingCity}<br />    {/if}
                                {if $acust.billingCountry}    {$acust.billingCountry}<br />    {/if}
                            </td>
                        </tr>
                        
                </tbody>
            </table>


            
            {if $acust.type neq Cust::TYPE_INTERNAL and ( not $acust.dateleave or $acust.dateleave eq '0000-00-00' )}
            
                <br /><br />
                <h3>Interfaces</h3>
                
                {if count( $connections )}
                
                    <table class="table">
                        <thead>
                            <th>Infrastructure</th>
                            <th>Location</th>
                            <th>Switch</th>
                            <th>Port</th>
                            <th>Speed</th>
                            <th></th>
                        </thead>
                        <tbody>
        
                        {foreach from=$connections item=c}
                        
                            <tr>
                            
                                <td>LAN #{$c.Physicalinterface.0.Switchport.SwitchTable.infrastructure}</td>
                                <td>{$c.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}</td>
                                <td>
                                    {foreach from=$c.Physicalinterface item=pi name=pis1}
                                        {$pi.Switchport.SwitchTable.name}{if not $smarty.foreach.pis1.last}<br />{/if}
                                    {/foreach}
                                </td>
                                <td>
                                    {foreach from=$c.Physicalinterface item=pi name=pis2}
                                        <a href="{genUrl controller='dashboard' action="statistics-drilldown" monitorindex=$pi.monitorindex category='bits' shortname=$acust.shortname}">
                                            {$pi.Switchport.name}
                                        </a>{if not $smarty.foreach.pis2.last}<br />{/if}
                                    {/foreach}
                                </td>
                                <td>
                                    {foreach from=$c.Physicalinterface item=pi name=pis3}
                                        {$pi.speed}/{$pi.duplex}{if not $smarty.foreach.pis3.last}<br />{/if}
                                    {/foreach}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-mini" href="{genUrl controller='virtual-interface' action="edit" id=$c.id}"><i class="icon-pencil"></i></a>
                                    </div>
                                </td>
                                
                            </tr>
                            
                        {/foreach}
                        
                        </tbody>
                        
                    </table>

                {else}
                
                    <p style="padding-left: 40px;">No interfaces found.</p>
                    
                {/if}
                
            {/if} {* end dateleave *}
                
                
            
            
            <br /><br />
            <h3>User Accounts</h3>
            
            {if count( $acust->User )}
            
                <table class="table">
                    <thead>
                        <th>Username</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th></th>
                    </thead>
                    <tbody>
                        {foreach from=$acust->User item=u}
                            <tr>
                                <td>{$u.username}</td>
                                <td>{User::$PRIVILEGES[$u.privs]}</td>
                                <td>{$u.email}</td>
                                <td>{$u.authorisedMobile}</td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-mini" href="{genUrl controller='user' action="edit" id=$u.id}"><i class="icon-pencil"></i></a>
                                        <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this user?' );" href="{genUrl controller='user' action="delete" id=$u.id}"><i class="icon-trash"></i></a>
                                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{genUrl controller='auth' action='switch' id=$u.id}">Log in as...</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                            
            {else}
            
                <p style="padding-left: 40px;">No users found.</p>
                
            {/if}
                
                            
            <br /><br />
            <h3>Contacts</h3>

            
            {if count( $acust->Contact )}
            
                <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                        <th></th>
                    </thead>
                    <tbody>
                        {foreach from=$acust->Contact item=c}
                            <tr>
                                <td>{$c.name}</td>
                                <td>{$c.email}</td>
                                <td>{$c.phone}</td>
                                <td>{$c.mobile}</td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-mini" href="{genUrl controller='contact' action="edit" id=$c.id}"><i class="icon-pencil"></i></a>
                                        <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this user?' );" href="{genUrl controller='contact' action="delete" id=$c.id}"><i class="icon-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                                
                            
            {else}
            
                <p style="padding-left: 40px;">No contacts found.</p>
                
            {/if}
        
                            
            
        </div>
    </div>
    


    <div class="span6">
    
        {if $acust.type neq Cust::TYPE_ASSOCIATE and $acust.type neq Cust::TYPE_INTERNAL and ( not $acust.dateleave or $acust.dateleave eq '0000-00-00' )}
    
                <div class="row-fluid">
                
                    <div class="well">
                        <h3>Aggregate Traffic Statistics</h3>
                
                        <p>
                            <br />
                            <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex='aggregate' category='bits' shortname=$acust.shortname}">
                                {genMrtgImgUrlTag shortname=$acust.shortname category='bits' monitorindex='aggregate'}
                            </a>
                        </p>
                    </div>
                
                </div>
        
        
                {foreach from=$connections item=connection}
        
                    {foreach from=$connection.Physicalinterface item=pi}
        
                        <div class="row-fluid">
                        
                            <div class="well">
                
                                <h4>
                                        {$pi.Switchport.SwitchTable.Cabinet.Location.name}
                                        / {$pi.Switchport.SwitchTable.name}
                                        / {$pi.Switchport.name} ({$pi.speed}Mb/s)
                                </h4>
                
                
                                <p>
                                    <br />
                                    <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex=$pi.monitorindex category='bits' shortname=$acust.shortname}">
                                        {genMrtgImgUrlTag shortname=$acust.shortname category='bits' monitorindex=$pi.monitorindex}
                                    </a>
                                </p>
                
                            </div>
        
                        </div>
        
                    {/foreach}
                    
                {/foreach}


            {/if}  {* end dateleave *}
        
    </div>

    
    
</div>





{include file="footer.tpl"}
