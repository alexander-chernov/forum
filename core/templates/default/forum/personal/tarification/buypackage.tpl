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
<div class="box_pasport">
	<table class="themes">
		<tr>
			<td>
				{if $_error}<h1 style="text-align: center;">{$_error}</h1>{/if}
				{if $_msg}<h1 style="text-align: center;">{$_msg}</h1>{/if}
				<h2 style="color: #ff9900;">{$package.info.name}</h2>
				<div id="infoPack{$package.info.packageid}">
					<div>{$package.info.comment|nl2br}</div>
					<br/>
					{foreach from=$package.list item=service}
						<div id="infoService{$service.spid}">
							<div><span class="hSpn">{$service.sname}</span></div>
							<div>
							<span class="hSpn">Переодичный:</span>
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
					<span class="hSpn">Цена:</span> {$package.info.price} руб.
				</div>
				{if $showBuyButton}
					<div style="text-align: right;">
						<form method="post">
							<input type="hidden" name="buyPackageId" value="{$package.info.packageid}" />
							<input type="hidden" name="event" value="forumuserbuypackage" />
							<input type="submit" value="Купить" class="btn_form" style="width: 60px;" />
						</form>
					</div>
				{/if}
			</td>
		</tr>
	</table>
</div>
{include file="forum/footer.tpl"}