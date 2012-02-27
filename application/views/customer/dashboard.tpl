{include file="header.tpl" pageTitle="IXP Manager :: Administrator's Home"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller="customer" action="list"}">Customers</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Dashboard for {$customer.name}
    </li>
    <li class="pull-right">
        <div class="btn-group">
            <a class="btn btn-mini" href="{genUrl controller='customer' action="edit" id=$customer.id}"><i class="icon-pencil"></i></a>
            <a class="btn btn-mini" onclick="return confirm( 'Are you sure you want to delete this record?' );" href="{genUrl controller='customer' action="delete" id=$customer.id}"><i class="icon-trash"></i></a>
        </div>
    </li>
</ul>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<div class="row-fluid">

    <div class="span6">
    
        <div class="row-fluid">
        
            <h3>
                {$customer.name}
                -
                <a href="{$customer.corpwww}">{$customer.corpwww}</a>
                -
                {mailto address=$customer.peeringemail}
            </h3>
            <br />
            
            <table class="table">
                <tbody>
                
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>{Cust::$CUST_STATUS_TEXT[$customer.status]}</td>
                            <td><strong>Joined</strong></td>
                            <td>{$customer.datejoin}</td>
                        </tr>
                        <tr>
                            <td><strong>Type</strong></td>
                            <td>{Cust::$CUST_TYPES_TEXT[$customer.type]}</td>
                            <td><strong>Left</strong></td>
                            <td>{$customer.dateleave}</td>
                        </tr>
                        <tr>
                            <td><strong>Peering Policy</strong></td>
                            <td>{$customer.peeringpolicy}</td>
                            <td><strong>ASN</strong></td>
                            <td>{$customer.autsys|asnumber} {if $customer.peeringmacro}({$customer.peeringmacro}){/if}</td>
                        </tr>
                        <tr>
                            <td><strong>NOC Details</strong></td>
                            <td>
                                {if $customer.nochours}    {$customer.nochours}<br />    {/if}
                                {if $customer.nocemail}    {mailto address=$customer.nocemail}<br />{/if}
                                {if $customer.nocwww}      {$customer.nocwww}<br />      {/if}
                                {if $customer.nocphone}    {$customer.nocphone}<br />    {/if}
                                {if $customer.noc24hphone} {$customer.noc24hphone} (24h) {/if}
                            </td>
                            <td><strong>Billing Details</strong></td>
                            <td>
                                {if $customer.billingContact}    {$customer.billingContact}<br />    {/if}
                                {if $customer.billingAddress1}    {$customer.billingAddress1}<br />    {/if}
                                {if $customer.billingAddress2}    {$customer.billingAddress2}<br />    {/if}
                                {if $customer.billingCity}    {$customer.billingCity}<br />    {/if}
                                {if $customer.billingCountry}    {$customer.billingCountry}<br />    {/if}
                            </td>
                        </tr>
                        
                </tbody>
            </table>


            
            
            <br />
            <h3>Interfaces</h3>
            
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
                                <a href="{genUrl controller='dashboard' action="statistics-drilldown" monitorindex=$pi.monitorindex category='bits' shortname=$customer.shortname}">
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
                    
            
            
            
            <br />
            <h3>User Accounts</h3>
            
            <table class="table">
                <thead>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th></th>
                </thead>
                <tbody>
                    {foreach from=$customer->User item=u}
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
                            
                            
                            
            <br />
            <h3>Contacts</h3>
            
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Mobile</th>
                    <th></th>
                </thead>
                <tbody>
                    {foreach from=$customer->Contact item=c}
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
                            
                            
                            
                            
            
        </div>
    </div>
    


    <div class="span6">
    
        <div class="row-fluid">
        
            <div class="well">
                <h3>Aggregate Traffic Statistics</h3>
        
                <p>
                    <br />
                    <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex='aggregate' category='bits' shortname=$customer.shortname}">
                        {genMrtgImgUrlTag shortname=$customer.shortname category='bits' monitorindex='aggregate'}
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
                            <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex=$pi.monitorindex category='bits' shortname=$customer.shortname}">
                                {genMrtgImgUrlTag shortname=$customer.shortname category='bits' monitorindex=$pi.monitorindex}
                            </a>
                        </p>
        
                    </div>

                </div>

            {/foreach}
            
        {/foreach}


        
    </div>

    
    
</div>





{include file="footer.tpl"}
