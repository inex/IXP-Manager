{include file="header-base.tpl"}


<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="{if isset( $hasIdentity ) and $hasIdentity and $identity.user.privs eq 3}container-fluid{else}container{/if}">
            <a class="brand" href="{genUrl}">{if isset( $brand )}{$brand}{else}IXP Manager{/if}</a>
            {if isset( $hasIdentity ) and $hasIdentity}
                <div class="nav-collapse">
                     <ul class="nav">
                        {if     $user.privs eq 3}
                            <li>
                                <a href="{genUrl}">Home</a>
                            </li>
                        {elseif $user.privs eq 2}
                            <li>
                                <a href="{genUrl controller="cust-admin"}">User Admin</a>
                            </li>
                        {elseif $user.privs eq 1}
                            <li {if $controller eq 'dashboard' and $action eq 'index'}class="active"{/if}>
                                <a href="{genUrl controller="dashboard"}">
                                    Dashboard
                                </a>
                            </li>
                        {/if}
                        {if $user.privs neq 2}
                            <li class="dropdown {if $action eq 'switch-configuration' or $action eq 'members-details-list' or ( $controller eq 'meeting' and $action eq 'read' )}active{/if}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Member Information <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="switch-configuration"}">Switch Configuration</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="members-details-list"}">Member Details</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="meeting" action="read"}">Meetings</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    {if $user.privs eq 1}
                                        <li>
                                            <a href="{genUrl controller="peering-manager"}">Peering Manager</a>
                                        </li>
                                    {/if}
                                    <li>
                                        <a href="{genUrl controller="peering-matrix"}">Public Peering Matrix</a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="dropdown {if $controller eq 'dashboard' and $action eq 'static'}active{/if}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Documentation<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="fees"}">Fees and Charges</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="housing"}">Equipment Housing</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="misc-benefits"}">Miscellaneous Benefits</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="support"}">Technical Support</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="switches"}">Connecting Switches</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="static" page="port-security"}">Port Security Policies</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="as112"}">AS112 Service</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="rs-info"}">Route Servers</a>
                                    </li>
                                </ul>
                            </li>
    
                            
                            <li class="dropdown {if $controller eq 'statistics' or substr( $action, 0, 9 ) eq 'statistic' or $action eq 'traffic-stats' or $action eq 'trunk-graphs' or $action eq 'switch-graphs' or $action eq 'weathermap'}active{/if}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    {if $user.privs eq 1 and $customer->isFullMember()}
                                        <li>
                                            <a href="{genUrl controller="dashboard" action="statistics"}">My Statistics</a>
                                        </li>
                                    {/if}
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="traffic-stats"}">Overall Peering Statistics</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="trunk-graphs"}">Trunk Graphs</a>
                                    </li>
                                    <li>
                                        <a href="{genUrl controller="dashboard" action="switch-graphs"}">Switch Aggregate Graphs</a>
                                    </li>
                                    {if isset( $config.weathermap )}
                                        {foreach from=$config.weathermap key=k item=w}
                                            <li>
                                                <a href="{genUrl controller="dashboard" action="weathermap" id=$k}">Weathermap - {$w.menu}</a>
                                            </li>
                                        {/foreach}
                                    {/if}
                                </ul>
                            </li>
                        {/if}
                        <li>
                            <a href="{genUrl controller="dashboard" action="static" page="support"}">Support</a>
                        </li>
                        {if $user.privs eq 3 and isset( $config.menu.staff_links )}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Staff Links<b class="caret"></b></a>
                                
                                <ul class="dropdown-menu">
                        
                                    {foreach from=$config.menu.staff_links item=i}
                                        <li>
                                            <a href="{$i.link}">{$i.name}</a>
                                        </li>
                                    {/foreach}
                                    
                                </ul>
                            </li>
                        {/if}
                        <li class="{if $controller eq 'profile'}active{/if}">
                            <a href="{genUrl controller="profile"}">Profile</a>
                        </li>
                        
                        {if $user.privs eq 3}
                            <li class="{if $controller eq 'index' and $action eq 'help'}active{/if}">
                                <a href="{genUrl controller="index" action="help"}">Help</a>
                            </li>
                        {/if}
                        
                        <li class="{if $controller eq 'index' and $action eq 'about'}active{/if}">
                            <a href="{genUrl controller="index" action="about"}">About</a>
                        </li>
                    </ul>
                    <ul class="nav pull-right">
                        {if $user.privs eq 3}
                            <form class="navbar-search pull-left">
                                <select data-placeholder="View a Customer..." id="menu-select-customer" type="select" name="id" class="chzn-select">
                                    <option></option>
                                    {foreach from=$customers key=k item=i}
                                        <option value="{$k}">{$i}</option>
                                    {/foreach}
                                </select>
                            </form>
                        {/if}
                            
                        {if isset( $session->switched_user_from ) and $session->switched_user_from}
                            <li><a href="{genUrl controller="auth" action="switch-back"}">Switch Back</a></li>
                        {else}
                            <li><a href="{genUrl controller="auth" action="logout"}">Logout</a></li>
                        {/if}
                    </ul>
                </div><!--/.nav-collapse -->
            {/if}
        </div>
    </div>
</div>
    
{if isset( $hasIdentity ) and $hasIdentity and $user.privs eq 3}
    
    <div class="container-fluid">

    {include file="menu.tpl"}
    
{elseif isset( $mode ) and $mode eq 'fluid'}

    <div class="container-fluid">

{else}

    <div class="container">

{/if}
