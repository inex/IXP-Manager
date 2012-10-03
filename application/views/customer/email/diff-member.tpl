

<h2>{$cust.name}</h2>

{if $dIn gt $threasholdIn}
<p>
<strong>INBOUND:</strong>

	{if $percentIn neq 'NONE'}
		There has been a <strong>{$percentIn}% {$sIn}</strong> is this
		member's traffic as recorded yesterday ({mrtgScale value=$in})
		compared to the average over the past {$days} days
		({mrtgScale value=$meanIn}). (Standard deviation: {mrtgScale value=$stddevIn}).
	{else}
		No previous records - possibly a new connection brought live?
	{/if}

</p>
{/if}

{if $dOut gt $threasholdOut}
<p>
<strong>OUTBOUND:</strong>

	{if $percentIn neq 'NONE'}
		There has been a <strong>{$percentOut}% {$sOut}</strong> is this member's
		traffic as recorded yesterday ({mrtgScale value=$out}) compared to the
		average over the past {$days} days ({mrtgScale value=$meanOut}). (Standard
		deviation: {mrtgScale value=$stddevOut}).
	{else}
		No previous records - possibly a new connection brought live?
	{/if}
</p>
{/if}

<p>
<a href="{$config.identity.ixp.url}/dashboard/statistics/shortname/{$cust.shortname}">
	<img src="cid:{$cust.shortname}" alt="[{$cust.shortname}]" />
</a>
</p>

