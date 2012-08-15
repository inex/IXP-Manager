{include file="header.tpl" pageTitle="IXP Manager :: Provision New Interface"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
    <tr>
        <th class="Provision">Provisioning: New Interface</th>
    </tr>
</table>


{include file="message.tpl"}

<div id="ajaxMessage"></div>

<br />

{if $progress.cust_id neq 0}
	<h3>New Interface for {$progress.Cust.name}</h3>

	<p>
	Started by <strong>{$progress.CreatedBy.username}</strong> on <strong>{$progress.created_at}</strong>.
	</p>
{/if}

<div id="box_new_virtual_interface" class="code_confirmation_box roundcorner_5px form">

    {if $progress.cust_id neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>1. Select Customer</strong>

    {if $progress.cust_id neq 0}
        <p>Provisioning new interface for <strong><em>{$progress.Cust.name}</em></strong>.</p>
    {else}
	    <form action="{genUrl controller="provision" action="interface-choose-cust"}" method="post" style="float:right">
	    <select name="cust">
	        <option></option>
	        {foreach from=$customers key=id item=name}
	            <option value="{$id}">{$name}</option>
	        {/foreach}
	    </select><br />
	    <input type="submit" name="submit" value="Select" style="margin-top: 5px" />

	    </form>

	    <p>
	    Select a customer for whom you wish to provision a new interface.<br /><br />
	    </p>
    {/if}
</div>



<div id="box_new_virtual_interface" class="code_confirmation_box roundcorner_5px form">

    {if $progress.virtualinterface_id neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>2. Create a Virtual Interface</strong>

    {if $progress.cust_id neq 0}
	    {if $progress.virtualinterface_id neq 0}
	        <form action="{genUrl controller="virtual-interface" action="edit" id=$progress.Virtualinterface.id}" method="post" style="float:right">
	            <input type="submit" name="submit" value="Edit Virtual Interface" style="margin-top: 5px" />
	    {else}
	        <form action="{genUrl controller="virtual-interface" action="add"}" method="post" style="float:right">
	            <input type="submit" name="submit" value="Create Virtual Interface" style="margin-top: 5px" />
	    {/if}

	        <input type="hidden" name="prov_cust_id" value="{$progress.Cust.id}" />
	        <input type="hidden" name="return"       value="/provision/interface-virtual-interface" />
	    </form>
    {/if}

    <p>
        Create a virtual interface for the customer to which we will later add physical interface(s) and VLAN interface(s).
    </p>

</div>



<div id="box_new_physical_interface" class="code_confirmation_box roundcorner_5px form">

    {if $progress.physicalinterface_id neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>3. Create / Add a Physical Interface</strong>

    {if $progress.virtualinterface_id neq 0}
	    {if $progress.physicalinterface_id neq 0}
	        <form action="{genUrl controller="physical-interface" action="edit" id=$progress.Physicalinterface.id}" method="post" style="float:right">
	            <input type="submit" name="submit" value="Edit Physical Interface" style="margin-top: 5px" />
                <input type="hidden" name="prov_physicalinterface_id" value="{$progress.Physicalinterface.id}" />
	    {else}
	        <form action="{genUrl controller="physical-interface" action="add"}" method="post" style="float:right">
	            <input type="submit" name="submit" value="Add Physical Interface" style="margin-top: 5px" />
	    {/if}

	        <input type="hidden" name="virtualinterfaceid"       value="{$progress.Virtualinterface.id}" />
	        <input type="hidden" name="prov_virtualinterface_id" value="{$progress.Virtualinterface.id}" />
	        <input type="hidden" name="return"       value="/provision/interface-physical-interface" />
	    </form>
    {/if}

    <p>
        Create a phsyical interface for the customer.
    </p>

</div>



<div id="box_new_vlan_interface" class="code_confirmation_box roundcorner_5px form">

    {if $progress.vlaninterface_id neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>4. Create / Add a VLAN Interface</strong>

    {if $progress.virtualinterface_id neq 0}
        {if $progress.vlaninterface_id neq 0}
            <form action="{genUrl controller="vlan-interface" action="edit" id=$progress.Vlaninterface.id}" method="post" style="float:right">
                <input type="submit" name="submit" value="Edit VLAN Interface" style="margin-top: 5px" />
        {else}
            <form action="{genUrl controller="vlan-interface" action="add"}" method="post" style="float:right">
                <input type="submit" name="submit" value="Add VLAN Interface" style="margin-top: 5px" />
        {/if}

            <input type="hidden" name="virtualinterfaceid"       value="{$progress.Virtualinterface.id}" />
            <input type="hidden" name="prov_virtualinterface_id" value="{$progress.Virtualinterface.id}" />
            <input type="hidden" name="return"       value="/provision/interface-vlan-interface" />
        </form>
    {/if}

    <p>
        Create a phsyical interface for the customer.
    </p>

</div>



<div id="box_send_email" class="code_confirmation_box roundcorner_5px form">

    {if $progress.mail_sent neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>5. Send Interface Details Mail</strong>

    {if $progress.physicalinterface_id neq 0 and $progress.vlaninterface_id neq 0}
        <form action="{genUrl controller="provision" action="interface-send-mail"}" method="post" style="float:right">
        {if $progress.mail_sent neq 0}
            <input type="submit" name="submit" value="Resend Mail" style="margin-top: 5px" />
        {else}
            <input type="submit" name="submit" value="Send Mail"   style="margin-top: 5px" />
        {/if}
        </form>
    {/if}

    <p>
        Send interface details email including cross connect information to the member.
    </p>

</div>



<div id="box_switch_config" class="code_confirmation_box roundcorner_5px form">

    {if $progress.switch_config neq 0}
        <img src="{genUrl}/images/32x32/check.png" alt="[DONE]"    title="Done"    class="overview_status_icon" />
    {else}
        <img src="{genUrl}/images/32x32/cross.png" alt="[PENDING]" title="Pending" class="overview_status_icon" />
    {/if}

    <strong>6. Configure the Switch Port for Quarantine</strong>

    {if $progress.physicalinterface_id neq 0 and $progress.vlaninterface_id neq 0}
        <form action="{genUrl controller="provision" action="interface-switch-config"}" method="post" style="float:right">
            <input type="submit" name="submit" value="Generate Configuration"   style="margin-top: 5px" />
        </form>
    {/if}

    <p>
        Generate the configuration necessary to set up the port on the quarantine LAN.
    </p>

</div>







</div> <!-- content -->
</div> <!--  yui-g -->

{include file="footer.tpl"}
