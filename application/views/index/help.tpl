{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Vendor">
        IXP Manager Help
    </th>
</tr>
</table>

<div id="documentation">

<p>
<br />
</p>

<p>
This help section is designed to show new users how to set up IXP Manager objects and
work through normal day to day procedures. First time users setting up a new instance
of IXP Manager should start with the <em>Initial Setup Tasks</em> section at the end.
</p>


<h1>Day to Day Tasks</h1>

<p>
	The normal day to day tasks are provisioning new members / customers and interfaces.
</p>

<table width="100%">
<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/user.png" width="48" height="48" alt="[Users]" title="Users" />
		<br />
		<strong><em>Members</em></strong>
	</td>
	<td>
		<p>
			Customers / members are those organisations who peer at your IXP. The first step in 
			provisioning services for a new customer is to 
			<a href="{genUrl controller="customer" action="add"}">add them</a>. You can also
			<a href="{genUrl controller="customer" action="list"}">list existing customers</a>.  
		</p>
	</td>
</tr>

<table width="100%">
<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/interface.png" width="48" height="48" alt="[Interfaces]" title="Interfaces" />
		<br />
		<strong><em>Interfaces</em></strong>
	</td>
	<td>
		<p>
			Customers are assigned an interface through which they get connectivity to the peering LAN(s).
			The easiest way to set up a standard simple interface is to use the
			<a href="{genUrl controller="vlan-interface" action="quick-add"}">Quick Add wizard</a>. 
			The braver among you can use the more manual process which starts by 
			<a href="{genUrl controller="virtual-interface" action="add"}">adding a virtual
			interface</a>. You then need to add one (or more for port channel / trunk groups)
			physical interfaces to this virtual interface followed by one or more VLAN interfaces.
		</p>
	</td>
</tr>

<table width="100%">
<tr>
	<td valign="top">
		<img src="{genUrl}/images/joomla-admin/system-users.png" width="48" height="48" alt="[Users]" title="Users" />
		<br />
		<strong><em>Users</em></strong>
	</td>
	<td>
		<p>
			IXP Manager supports three types of users:
			<dl>
				<dt><strong>Superuser</strong></dt>
				<dd>
					These are IXP staff members such as yourself with full unrestricted administrative access.
				</dd>
				<dt><strong>Customer Superuser</strong></dt>
				<dd>
					Each customer gets a Customer Superuser account. This does not provide any access other than
					to allow them to create and manage customer user accounts.
				</dd>
				<dt><strong>Customer User</strong></dt>
				<dd>
					Customer Users are customer portal accounts allowing them access to their dashboard,
					statistics, peering manager and so forth.
				</dd>
			</dl>

			You can <a href="{genUrl controller="user" action="add"}">add new users</a> and
			<a href="{genUrl controller="user" action="list"}">list existing users</a>.  
		</p>
	</td>
</tr>

</table>

<h1>Inital Setup Tasks</h1>

<p>
	Welcome to your new IXP Manager! The following steps will walk you through the initial set up and
	pupulation of your new system.
</p>

<table width="100%">
<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/browser.png" width="48" height="48" alt="[Locations]" title="Locations" />
		<br />
		<strong><em>Locations</em></strong>
	</td>
	<td>
		<p>
			Locations are typically data centres / colocation facilities where you would put a rack / cabinet.
			In IXP Manager, a location contains cabinets which in turn contain switches. Start by either
			<a href="{genUrl controller="location" action="add"}">adding a new location</a> or
			<a href="{genUrl controller="location" action="list"}">listing existing locations</a>.  
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/cabinets.png" width="48" height="48" alt="[Cabinets]" title="Cabinets" />
		<br />
		<strong><em>Cabinets</em></strong>
	</td>
	<td>
		<p>
			Cabinets (or racks) are stored in <em>locations</em> as described above. You can create
			one or more cabinets per location. Please
			<a href="{genUrl controller="cabinet" action="add"}">add a new cabinet</a> or
			<a href="{genUrl controller="cabinet" action="list"}">list existing cabinets</a>.  
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/vendor.png" width="48" height="48" alt="[Vendors]" title="Vendors" />
		<br />
		<strong><em>Vendors</em></strong>
	</td>
	<td>
		<p>
			A vendor is simply a way to group switches by their manufacturer. This can be useful when, for
			example, autogenerating configurations with systems such as Nagios. Before you can add a switch
			to the IXP Manager, you must first <a href="{genUrl controller="vendor" action="add"}">add a 
			vendor</a>. You can also <a href="{genUrl controller="vendor" action="list"}">list existing 
			vendors</a>.  
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/switch.png" width="48" height="48" alt="[Switches]" title="Switches" />
		<br />
		<strong><em>Switches</em></strong>
	</td>
	<td>
		<p>
			Switches are the key infrastructure elements of any IXP. In the IXP Manager, switches are
			are stored in <em>cabinets</em> as described above. If you haven't already, you should
			<a href="{genUrl controller="switch" action="add"}">add a new switch</a>. You can also
			<a href="{genUrl controller="switch" action="list"}">list existing switches</a>.  
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/interface.png" width="48" height="48" alt="[Switch Ports]" title="Switch Ports" />
		<br />
		<strong><em>Switch Ports</em></strong>
	</td>
	<td>
		<p>
			Members / customers of an IXP connect through dedicated switch ports. In IXP Manager, you
			need to add ports to switches. We make this easy by allowing you to add multiple ports
			in one go via the <a href="{genUrl controller="switch" action="add-ports"}">add ports wizard</a>. 
			You can also <a href="{genUrl controller="switch-port" action="list"}">list ports by switch</a>
			and <a href="{genUrl controller="switch" action="port-report"}">generate reports</a> of switch 
			ports showing who is connected.  
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/vlan.png" width="48" height="48" alt="[VLANs]" title="VLANs" />
		<br />
		<strong><em>VLANs</em></strong>
	</td>
	<td>
		<p>
			Some IXPs have more than one peering LAN but all have at least one. Even if you only have
			one peering LAN, you need to <a href="{genUrl controller="vlan" action="add"}">add a
			VLAN</a>. You can also <a href="{genUrl controller="vlan" action="list"}">list existing
			VLANs</a>.
		</p>
	</td>
</tr>

<tr>
	<td>
		<img src="{genUrl}/images/joomla-admin/kdmconfig.png" width="48" height="48" alt="[IP Addresses]" title="IP Addresses" />
		<br />
		<strong><em>Addresses</em></strong>
	</td>
	<td>
		<p>
			All IXP participants need an IP address for peering. Before you can provision customers,
			you need to populate the database with IP addresses for your peering VLAN(s). We've made
			this quite easy with our <a href="{genUrl controller="ipv4-address" action="add-addresses"}">IP 
			address add wizard</a> (supporting IPv4 and IPv6 addresses). You can also list existing
			<a href="{genUrl controller="ipv4-address" action="list"}">IPv4 addrseses</a> and
			<a href="{genUrl controller="ipv6-address" action="list"}">IPv6 addrseses</a> (including
			who they are assigned to).
		</p>
	</td>
</tr>

</table>

</div>

</div>

</div>

{tmplinclude file="footer.tpl"}
