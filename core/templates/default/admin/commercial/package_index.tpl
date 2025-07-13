<script type="text/javascript">
	{if $_params.packIDs} 
		var indexes = new Array(0, {$_params.packIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>
<form id="mainformAdd" action="/.admin/commercial/package/add/"></form>
{include file="admin/error/error_messages.tpl"}
{include file="admin/commercial/package_functions.tpl"}
{if count($_dataGrid) > 0 }
	<form id="mainform" action="?" method="POST">
		<div style="float:right">{include file="admin/pager.tpl" up=1}</div>
		<table class="theme">
			<tr>
				<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
				<th>№</th>
				<th>Название</th>
				<th>Время жизни</th>
				<th>Цена</th>
				<th>Отображается</th>
				<th>Активный</th>
				<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
			</tr>
			{foreach key=key item=curItem from=$_dataGrid}
			<tr class="tr{if $key is odd}1{else}2{/if}">
				<td align="center"><input type="checkbox" id="bans_{$curItem.id}" name="hidId[{$curItem.id}]"  value="{$curItem.id}" /></td>
				<td>{$curItem.counter}</td>
				<td>{$curItem.name}</td>
				<td>{$curItem.lifetime}</td>
				<td>{$curItem.price}</td>
				<td>
					{if $curItem.display eq 1}
						Да
						<div>
							<a href="/.admin/commercial/package/?event=forumpackageundisplay&hidId[]={$curItem.id}">
								Скрыть
							</a>
						</div>
					{else}
						Нет
						<div>
							<a href="/.admin/commercial/package/?event=forumpackagedisplay&hidId[]={$curItem.id}">
								Показать
							</a>
						</div>
					{/if}
				</td>
				<td>
					{if $curItem.isactive eq 1}
						Да
						<div>
							<a href="/.admin/commercial/package/?event=forumpackageunactive&hidId[]={$curItem.id}">
								Деактивировать
							</a>
						</div>
					{else}
						Нет
						<div>
							<a href="/.admin/commercial/package/?event=forumpackageactive&hidId[]={$curItem.id}">
								Активировать
							</a>
						</div>
					{/if}
				</td>
				<td align="center" style="vertical-align:middle">
					{if $curItem.isactive neq 1}
						<a href="/.admin/commercial/package/edit/{$curItem.id}/" target="_blank">
							<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
						</a>
					{/if}
				</td>
			</tr>
			{/foreach}
		</table>
		<input type="hidden" value="" id="event" name="event"/>
		<div style="float:right">{include file="admin/pager.tpl"}</div>
	</form>
{else}
	<div>Список пуст</div>
{/if}
{include file="admin/commercial/package_functions.tpl"}