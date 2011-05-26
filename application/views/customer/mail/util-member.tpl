

<h2>{$cust.name} :: {$switchport}</h2>

{if $utilIn gt $threshold}
<p>
<strong>INBOUND:</strong> Traffic inbound on this port reached a maximum of {$utilIn*100|string_format:"%.2f"}%.
</p>
{/if}

{if $utilOut gt $threshold}
<p>
<strong>OUTBOUND:</strong> Traffic outbound on this port reached a maximum of {$utilOut*100|string_format:"%.2f"}%.
</p>
{/if}

<p>
<a href="{$config.identity.ixp.url}/dashboard/statistics/shortname/{$cust.shortname}">
	<img src="cid:{$cust.shortname}" alt="[{$cust.shortname}]" />
</a>
</p>

