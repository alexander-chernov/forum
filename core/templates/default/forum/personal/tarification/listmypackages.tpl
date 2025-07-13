{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Список сервисов
    </div>
</div>
{literal}
	<style type="text/css">
		.cBlack {
			width: 100%;
		}

		.cBlack tr td {
			color: #000000;
			padding: 4px;
		}

		.cBlack tr th {
			text-align: center;
			color: #000000;
			padding: 4px;
		}
	</style>
{/literal}
{if $packages}
	<div class="box_pasport">
		<table class="theme cBlack">
			<tr>
				<th>Название</th>
				<th>Тип</th>
				<th>Продление</th>
				<th>Период</th>
				<th>Время действия</th>
				<th>Цена</th>
				<th>Статус</th>
			</tr>
			{foreach from=$packages item=package}
				<tr>
					<th><h2>{$package.info.name}</h2></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>
							{if $package.info.price > 0}
								{$package.info.price}&nbsp;р.</div>
							{/if}
					</th>
					<th>
						{if $package.info.status == 0}
							Неактивный
						{elseif $package.info.status == 1}
							Активирован
						{elseif $package.info.status == 2}
							Закончен
						{/if}
					</th>
				</tr>
				{foreach from=$package.list item=service}
					<tr>
						<td>{$service.sname}</td>
						<td>
							{if $service.periodical == 1}
								Периодичный
							{else}
								Одноразовый
							{/if}
						</td>
						<td>
							{if $service.mayup == 1}
								Можно продливать
							{else}
								Нельзя продлевать
							{/if}
						</td>
						<td>
							{if $service.period > 0}
								{$service.period}&nbsp;ч.
							{/if}
						</td>
						<td>
							{if $service.acttime > 0}
								{$service.acttime}&nbsp;ч.
							{/if}
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				{/foreach}
			{/foreach}
		</table>
	</div>
{else}
	<h2 style="margin: 5px; color: #FFF; text-align: center;">Нет приобретенных пакетов услуг</h2>
	<div style="text-align: center;">Информацию по приобретению услуг можно найти <a href="/personal/tarification/">тут</a></div>
	<div style="height: 10px; overflow: hidden;">&nbsp;</div>
{/if}
{include file="forum/footer.tpl"}