{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Vendor">
        About IXP Manager
    </th>
</tr>
</table>

<p>
<br />
</p>

<p>
	<strong>IXP Manager</strong> is primarily a web application with associated 
	scripts and utilities which will allow Internet Exchange Points (IXPs) to 
	manage new members (or customers), provision new connections and services and 
	monitor traffic usage. It also has a self contained customer portal allowing 
	IXP members to view their IXP traffic statistics and a unique tool called 
	<em>My Peering Manager</em> enabling IXP members to request, manage and track 
	peerings with other members.
</p>

<p>
	IXP Manager is a web application built entirely in house by the INEX operations team
	to support the running of the exchange. <a href="https://www.inex.ie/">INEX</a> 
	(Internet Neutral Exchange Association) is Ireland's IP peering hub.
</p>

<p>
	INEX are pleased to to be able to release IXP Manager under an open source license 
	(the <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU Public License V2)</a> 
	which we hope will benefit the wider IXP community, and especially new and small 
	IXPs looking to expand.
</p>

<p> 
	IXP Manager is written in PHP using the Zend Framework, the Doctrine ORM and the 
	Smarty templating engine. The project website and source code can be viewed at 
	<a href="https://github.com/inex/IXP-Manager">https://github.com/inex/IXP-Manager</a>.
</p>


<h3>Software License</h3>

<p>
	<strong>
		Copyright (C) 2009 - {$smarty.now|date_format:"%Y"} Internet Neutral Exchange Association Limited.<br />
		All Rights Reserved.
	</strong>
</p>

<p>
	<code>
	IXP Manager is free software: you can redistribute it and/or modify it<br />
    under the terms of the GNU General Public License as published by the Free<br />
    Software Foundation, version v2.0 of the License.<br />
    <br />
    IXP Manager is distributed in the hope that it will be useful, but WITHOUT<br />
    ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or<br />
    FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for<br />
    more details.
    </code>
</p>



</div>

</div>

{tmplinclude file="footer.tpl"}
