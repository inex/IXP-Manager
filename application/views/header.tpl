{include file="header-base.tpl"}


<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="{if $hasIdentity and $identity.user.privs eq 3}container-fluid{else}container{/if}">
            <a class="brand" href="{genUrl}">IXP Manager</a>
            {if $hasIdentity}
                <div class="nav-collapse">
                     <ul class="nav">
                        <li>
                            {if     $user.privs eq 3}<a href="{genUrl}">Home</a>
                            {elseif $user.privs eq 2}<a href="{genUrl controller="cust-admin"}">User Admin</a>
                            {elseif $user.privs eq 1}<a href="{genUrl controller="dashboard"}">Dashboard</a>
                            {/if}
                        </li>
                        <li class="dropdown">
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
                                        <a href="{genUrl controller="dashboard" action="my-peering-matrix"}">My Peering Manager</a>
                                    </li>
                                {/if}
                                {foreach from=$config.peering_matrix.public key=index item=lan}
                                    <li>
                                        <a target="_blank" href="{genUrl controller="dashboard" action="peering-matrix" lan=$index}">Matrix - {$lan.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                        
                        <li class="dropdown">
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

                        
                        <li class="dropdown">
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
                          
                    </ul>
                     <ul class="nav pull-right">
                        <li><a href="{genUrl controller="auth" action="logout"}">Logout</a></li>
                     </ul>
                 </div><!--/.nav-collapse -->
            {/if}
        </div>
    </div>
</div>
    
{if $hasIdentity and $user.privs eq 3}
    
    <div class="container-fluid">

    {include file="menu.tpl"}
    
{elseif isset( $mode ) and $mode eq 'fluid'}

    <div class="container-fluid">

{else}

    <div class="container">

{/if}
