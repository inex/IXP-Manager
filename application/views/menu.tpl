
<div class="row-fluid">
<div class="span2">
<div class="well sidebar-nav">
        
<ul class="nav nav-list">

    <li class="nav-header">IXP Customer Actions</li>
    
        <li {if $controller eq 'customer'}class="active"{/if}>
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
        <li {if $controller eq 'user'}class="active"{/if}>
            <a href="{genUrl controller='user' action='list'}">Users</a>
        </li>
        <li {if $controller eq 'contact'}class="active"{/if}>
            <a href="{genUrl controller='contact' action='list'}">Contacts</a>
        </li>
        <li {if $controller eq 'cust-kit'}class="active"{/if}>
            <a href="{genUrl controller='cust-kit' action='list'}">Colocated Equipment</a>
        </li>
        <li {if $controller eq 'meeting'}class="active"{/if}>
            <a href="{genUrl controller='meeting' action='list'}">Meetings</a>
        </li>
        
              
              
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
            </ul>
            
        </div><!--/.well -->
        
    </div><!--/span-->

    <div class="span10">
