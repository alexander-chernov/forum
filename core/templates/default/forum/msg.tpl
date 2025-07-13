{if count($__msg) > 0}
	<div class="errors" style="text-align:center; padding-top: 2px">
	{foreach item=curMsg from=$__msg}
		<div style="color: rgb(0,0,0)">{$curMsg}</div>
	{/foreach}
	</div>
{/if}