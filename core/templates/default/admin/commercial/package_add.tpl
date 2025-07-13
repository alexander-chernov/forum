{include file="admin/error/error_messages.tpl"}
{if $_params.pack.isactive == 0}
	<form action="" name="editform" method="post">
		<table class="theme">
			<tr>
				<td align="right">
					Название
				</td>
				<td>
					<input type="text" name="pack[name]" value="{$_params.pack.name}" />
				</td>
			</tr>
			<tr>
				<td align="right">
					Время действия (10-ки минут)
				</td>
				<td>
					<input type="text" name="pack[lifetime]" value="{$_params.pack.lifetime}" />
				</td>
			</tr>
			<tr>
				<td align="right">
					Цена
				</td>
				<td>
					<input type="text" name="pack[price]" value="{$_params.pack.price}" />
				</td>
			</tr>
			<tr>
				<td align="right">
					Описание
				</td>
				<td>
					<textarea name="pack[comment]" rows="5" cols="80">{if $_params.pack.comment}{$_params.pack.comment}{/if}</textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
					Продлевать
				</td>
				<td>
					<input type="checkbox" name="pack[mayup]" value="1" {if $_params.pack.mayup == 1}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td align="right">
					Тип объекта
				</td>
				<td>
					<select name="pack[objtype]">
						<option value="theme">Тема</option>
						<option value="pager" {if $_params.pack.objtype == 'pager'}selected="selected"{/if}>Пэйджер</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="hidden" name="event" value="forumaddpackage" />
					<input type="submit" value="Добавить" />
				</td>
			</tr>
		</table>
		<table class="theme">
			<tr>
			{foreach key=key item=curItem from=$_params.groups name=s}
				{if $smarty.foreach.s.iteration % 3 == 1}
					</tr><tr>
				{/if}
				<td style="width: 30px;"><input type="checkbox" name="ingroup[{$curItem.groupID}]" value="{$curItem.groupID}" {if $_params.pack.groups_id[$curItem.groupID]}checked="checked"{/if} /></td>
				<td style="{if $curItem.commerce eq 1}background-color: #cccccc;{/if}">{$curItem.caption}</td>
			{/foreach}
			</tr>
		</table>
		<table class="theme">
			<tr>
				<th>Номер</th>
				<th>&nbsp;</th>
				<th>Название</th>
				<th>Действие сервиса<br/>(10-ки минут)</th>
				<th>Период<br/>(10-ки минут)</th>
				<th>Время действия<br/>(10-ки минут)</th>
			</tr>
			{if $_params.objID>0}
				<input type="hidden" name="_objId" value="{$_params.objID}">
			{/if}
			{assign var="i" value="1"}
			{foreach key=key item=curItem from=$_params.listServices}
				<input type="hidden" name="idList[{$curItem.sId}]" value="{$curItem.id}" />
				<tr>
					<td>
						{$i}
					</td>
					<td>
						<input type="checkbox" name="inpack[]" value="{$curItem.sId}" {if $curItem.isSelected == 1}checked="checked"{/if}>
					</td>
					<td>
						{$curItem.name}
					</td>
					<td>
						<select name="sList[{$curItem.sId}][periodical]">
							<option value="">Необходимо выбрать</option>
							<option value="0" {php} if(trim($this->_tpl_vars['curItem']['periodical']) == '0') echo 'selected="selected"'; {/php}>Одноразовый</option>
							<option value="1" {php} if($this->_tpl_vars['curItem']['periodical'] == 1) echo 'selected="selected"'; {/php}>Переодичный</option>
						</select>
					</td>
					<td>
						<input type="text" name="sList[{$curItem.sId}][period]" value="{$curItem.period}">
					</td>
					<td>
						<input type="text" name="sList[{$curItem.sId}][acttime]" value="{$curItem.acttime}">
					</td>
				</tr>
				{assign var="i" value=`$i+1`}
			{/foreach}
		</table>
	</form>
{/if}