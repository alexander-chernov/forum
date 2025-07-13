{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Список сервисов
    </div>
</div>
{literal}
<style type="text/css">
	 .hSpn {
	 	font-weight: bold;
	 	color: #cccccc;
	 }
</style>
{/literal}
<script type="text/javascript">
	{literal}
		function showPackInfo(id){
			if ($("#infoPack" + id).css('display') == 'none'){
				$("#infoPack" + id).css('display', 'block');
			}else{
				$("#infoPack" + id).css('display', 'none');
			}
			return false;
		}
	{/literal}
</script>
<div class="box_pasport">
	<table class="themes">
		{foreach from=$listPackages item=package}
			<tr>
				<td onclick="return showPackInfo({$package.info.packageid});" style="cursor: pointer;">
					<h2 style="color: #ff9900;">{$package.info.name}</h2>
					<div style="display: block;" id="infoPack{$package.info.packageid}">
						<div>{$package.info.comment|nl2br}</div>
						<br/>
						{foreach from=$package.list item=service}
							<div id="infoService{$service.spid}">
								<div><span class="hSpn">{$service.sname}</span></div>
								<div>
								<span class="hSpn">Периодичный:</span>
									{if $service.periodical == 1}
										Да
									{else}
										Нет
									{/if}
								</div>
								<div>
									<span class="hSpn">Активация:</span>
									{if $service.mayup == 1}
										С возможностью продления
									{else}
										Разовая
									{/if}
								</div>
								{if $service.period > 0}
									<div><span class="hSpn">Период:</span> {$service.period} ч.</div>
								{/if}
								{if $service.acttime > 0}
									<div>
										<span class="hSpn">Время действия:</span> {$service.acttime} ч.
									</div>
								{/if}
							</div>
							<br/>
						{/foreach}
						{php}
							$this->_tpl_vars['gc'] = count($this->_tpl_vars['package']['info']['groups_id']);
						{/php}
						{if $gc>0}
							{assign var="gi" value=1}
							<span class="hSpn">Темы:</span>
							{section name="g" loop="$groups"}
								{assign var="inx" value=$groups[g].groupID}
								{if $package.info.groups_id[$inx]}
									{$groups[g].caption}{if $gc<$gi}, {/if}
								{/if}
								{assign var="gi" value=`$gi+1`}
							{/section}
						{/if}
						{if $package.info.price > 0}
							<div><span class="hSpn">Цена:</span> {$package.info.price} руб.</div>
						{/if}
					</div>
				</td>
			</tr>
			{if $package.info.showBuy}
				<tr>
					<td>
						<form method="get" action="/personal/buypackage/{$package.info.packageid}/"><input type="submit" value="Купить" class="btn_form" style="width: 60px;" /></form>
					</td>
				</tr>
			{/if}
		{/foreach}
	</table>
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}