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
of IXP Manager should start with the <a href="#initial"><em>Initial Setup Tasks</em></a> section at the end.
</p>


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

</table>

</div>

</div>

</div>

{tmplinclude file="footer.tpl"}
