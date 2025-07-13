{if count($__errors) > 0}
	<div class="errors" style="text-align:center; padding-top: 2px;float:left;">
	{foreach item=curErr from=$__errors}
		<div style="color: rgb(255,0,0)"><b>ОШИБКА</b>: {$curErr}</div>
	{/foreach}
	</div>
{/if}