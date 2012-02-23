
<div class="row-fluid">
<div class="span2">
<div class="well sidebar-nav">
        
<ul class="nav nav-list">

    <li class="nav-header">IXP Customer Actions</li>
    
        <li {if $controller eq 'customer'}class="active"{/if}>
            <a href="{genUrl controller='customer' action='list'}">Customers</a>
        </li>
        <li {if $controller eq 'virtual-interface'}class="active"{/if}>
            <a href="{genUrl controller='virtual-interface' action='list'}">Ports</a>
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
