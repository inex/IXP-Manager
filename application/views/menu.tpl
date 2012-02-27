
<div class="row-fluid">
<div class="span2">
<div class="well sidebar-nav">
        
<ul class="nav nav-list">

    <li class="nav-header">IXP Customer Actions</li>
    
        <li {if $controller eq 'customer' and ( $action eq 'list' or $action eq 'add' or $action eq 'edit' or $action eq 'dashboard' )}class="active"{/if}>
            <a href="{genUrl controller='customer' action='list'}">Customers</a>
        </li>
        <li>
            <a href="{genUrl controller='virtual-interface' action='list'}">Ports</a>
            
            {if $controller eq 'virtual-interface' or $controller eq 'vlan-interface' or $controller eq 'physical-interface'}
                <ul class="nav nav-list">
                    <li {if $controller eq 'virtual-interface'}class="active"{/if}>
                        <a href="{genUrl controller='virtual-interface' action='list'}">Virtual Interfaces</a>
                    </li>
                    <li {if $controller eq 'physical-interface'}class="active"{/if}>
                        <a href="{genUrl controller='physical-interface' action='list'}">Physical Interfaces</a>
                    </li>
                    <li {if $controller eq 'vlan-interface' and $action neq 'quick-add'}class="active"{/if}>
                        <a href="{genUrl controller='vlan-interface' action='list'}">Vlan Interfaces</a>
                    </li>
                    <li {if $controller eq 'vlan-interface' and $action eq 'quick-add'}class="active"{/if}>
                        <a href="{genUrl controller='vlan-interface' action='quick-add'}">Quick Add</a>
                    </li>
                </ul>
            {/if}
            
        </li>
        <li {if $controller eq 'user' and $action neq 'last'}class="active"{/if}>
            <a href="{genUrl controller='user' action='list'}">Users</a>
        </li>
        <li {if $controller eq 'contact'}class="active"{/if}>
            <a href="{genUrl controller='contact' action='list'}">Contacts</a>
        </li>
        <li {if $controller eq 'cust-kit'}class="active"{/if}>
            <a href="{genUrl controller='cust-kit' action='list'}">Colocated Equipment</a>
        </li>
        
        <li {if $controller eq 'meeting' and $controller neq 'meeting-item' and $action neq 'read'}class="active"{/if}>
            <a href="{genUrl controller='meeting' action='list'}">Meetings</a>
            
            {if $controller eq 'meeting' or $controller eq 'meeting-item'}
                <ul class="nav nav-list">
                    <li {if $controller eq 'meeting-item'}class="active"{/if}>
                        <a href="{genUrl controller='meeting-item' action='list'}">Presentations</a>
                    </li>
                    <li {if $controller eq 'meeting' and $action eq 'read'}class="active"{/if}>
                        <a href="{genUrl controller='meeting' action='read'}">Member View</a>
                    </li>
                    <a href="{genUrl controller='admin' action='static' page='instructions-meetings'}">Instructions</a>
                </ul>
            {/if}
            
        </li>
        
              
    <li class="nav-header">IXP Admin Actions</li>
        
        <li {if $controller eq 'location'}class="active"{/if}>
            <a href="{genUrl controller='location' action='list'}">Locations</a>
        </li>

        <li {if $controller eq 'cabinet'}class="active"{/if}>
            <a href="{genUrl controller='cabinet' action='list'}">Cabinets</a>
        </li>
        
        <li {if $controller eq 'switch' and $action neq 'add-ports'}class="active"{/if}>
            <a href="{genUrl controller='switch' action='list'}">Switches</a>
            
            {if $controller eq 'switch' or $controller eq 'switch-port'}
                <ul class="nav nav-list">
                    <li {if $controller eq 'switch-port'}class="active"{/if}>
                        <a href="{genUrl controller='switch-port' action='list'}">Switch Ports</a>
                    </li>
                    <li {if $controller eq 'switch' and $action eq 'add-ports'}class="active"{/if}>
                        <a href="{genUrl controller='switch' action='add-ports'}">Add Ports</a>
                    </li>
                </ul>
            {/if}
            
        </li>
        
        <li>
            <a href="{genUrl controller='ipv4-address' action='list'}">IP Addressing</a>
            
            {if $controller eq 'ipv4-address' or $controller eq 'ipv6-address'}
                <ul class="nav nav-list">
                    <li {if $controller eq 'ipv4-address' and $action neq 'add-addresses'}class="active"{/if}>
                        <a href="{genUrl controller='ipv4-address' action='list'}">IPv4 Addresses</a>
                    </li>
                    <li {if $controller eq 'ipv6-address'}class="active"{/if}>
                        <a href="{genUrl controller='ipv6-address' action='list'}">IPv6 Addresses</a>
                    </li>
                    <li {if $controller eq 'ipv4-address' and $action eq 'add-addresses'}class="active"{/if}>
                        <a href="{genUrl controller='ipv4-address' action='add-addresses'}">Add New Addresses</a>
                    </li>
                </ul>
            {/if}
            
        </li>
        
        <li {if $controller eq 'vendor'}class="active"{/if}>
            <a href="{genUrl controller='vendor' action='list'}">Vendors</a>
        </li>
        
        <li {if $controller eq 'console-server-connection'}class="active"{/if}>
            <a href="{genUrl controller='console-server-connection' action='list'}">Console Server Connections</a>
        </li>
        
        <li {if $controller eq 'vlan'}class="active"{/if}>
            <a href="{genUrl controller='vlan' action='list'}">VLANs</a>
        </li>
    
        <li {if $controller eq 'irrdb-config'}class="active"{/if}>
            <a href="{genUrl controller='irrdb-config' action='list'}">IRRDB Configuration</a>
        </li>
    
        
              
    <li class="nav-header">IXP Statistics</li>
        
        <li {if $controller eq 'customer' and $action eq 'statistics-overview'}class="active"{/if}>
            <a href="{genUrl controller='customer' action='statistics-overview'}">Member Statistics - Graphs</a>
        </li>
        <li {if $controller eq 'customer' and $action eq 'statistics-list'}class="active"{/if}>
            <a href="{genUrl controller='customer' action='statistics-list'}">Member Statistics - List</a>
        </li>
        <li {if $controller eq 'customer' and $action eq 'league-table'}class="active"{/if}>
            <a href="{genUrl controller='customer' action='league-table'}">League Table</a>
        </li>
                
        {* 95th Percentiles {genUrl controller="customer" action="ninety-fifth"} *}
        {* Last Logins      {genUrl controller="user" action="last"} *}
        

        
    <li class="nav-header">IXP Utilities</li>
        
        <li {if $controller eq 'utils' and $action eq 'phpinfo'}class="active"{/if}>
            <a href="{genUrl controller='utils' action='phpinfo'}">PHP Info</a>
        </li>
        <li {if $controller eq 'utils' and $action eq 'apcinfolist'}class="active"{/if}>
            <a href="{genUrl controller='utils' action='apcinfo'}">APC Info</a>
        </li>
        <li {if $controller eq 'user' and $action eq 'last'}class="active"{/if}>
            <a href="{genUrl controller='user' action='last'}">Last Logins</a>
        </li>
        
                
    </ul>
    
</div><!--/.well -->
</div><!--/span-->
<div class="span10">
