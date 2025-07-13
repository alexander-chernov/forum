{if $_params.form eq 1}
	<form action="" name="editform" method="post">
	<table class="theme">
		<tr>
			<td align="right">
				IP-адрес
			</td>
			<td>
				<input readonly="readonly" type="text" name="ban[value]" value="{$_params.addr}" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Ник автора
			</td>
			<td>
				<input readonly="readonly" type="text" name="ban[author]" value="{$_params.author}" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Реальный ник
			</td>
			<td>
				<input readonly="readonly" type="text" name="ban[realname]" value="{$_params.realname}" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Правило
			</td>
			<td>
                <select name="ban[ruleID]" class="complaint_sel" id="rule_id">
					{foreach item=rule from=$_params.rules}
						<option value="{$rule.ruleID}">{$rule.caption}</option>
					{/foreach}
				</select>			
			</td>
		</tr>
		<tr>
			<td align="right">
				Комментарий
			</td>
			<td>
				<textarea name="ban[comment]" rows="5" cols="80">{if $_params.content}{$_params.content}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td align="right">
				Период
			</td>
			<td>
				<select name="ban[time]">
					<option value="86400">1 день</option>
					<option value="172800">2 дня</option>
					<option value="259200">3 дня</option>
					<option value="604800">1 неделя</option>
					<option value="2419200">1 месяц</option>
					<option value="4838400">2 месяца</option>
					<option value="7257600">3 месяца</option>
					<option value="14515200">6 месяцев</option>
					<option value="29030400">1 год</option>
					<option value="300000000">Навсегда</option>
				</select>					
			</td>
		</tr>
		<tr>
			<td align="right">
				Тип бана
			</td>
			<td>
				<select name="ban[type]">
					<option value="11" selected="selected">Только-чтение</option>
					<option value="10">Необх. авторизация</option>
					<option value="1">Полный бан</option>
				</select>				
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="event" value="forumbanip" />
				<input type="submit" value="Добавить" />
			</td>
		</tr>
	</table>
	</form>
{else}
	{include file="admin/error/error_messages.tpl"}
	{if $_params.template eq 'ip_index'}
		<table class="theme">
			<tr>
				<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
				<th>№</th>
				<th>IP-адрес</th>
				<th>Начало бана</th>
				<th>Истекает</th>
				<th>Комментарий</th>
				<th>Тип бана</th>
				<th>Забанил</th>
				<th>Подтверждено?</th>
			</tr>
			{foreach key=key item=curItem from=$_dataGrid}
			<tr class="tr{if $key is odd}1{else}2{/if}">
				<td align="center"><input type="checkbox" id="bans_{$curItem.id}" name="bans[{$curItem.id}]" /></td>
				<td>{$curItem.counter}</td>
				<td>{$curItem.init_ip}</td>
				<td>{$curItem.banned_time|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>
					{if $curItem.banned_period eq $curItem.banned_time}
						Никогда
					{else}
						{$curItem.banned_period|date_format:"%d/%m/%Y %H:%M"}
					{/if}
				</td>
				<td>{$curItem.comments}</td>
				<td>{$_ban_types[$curItem.ban_type]}</td>
				<td>
					<a href="javascript:void(0)" onclick="pagerSend({$curItem.adminID})">{$_params.authors[$curItem.adminID]}</a>
				</td>
				{if $_params.confirm eq 1}
					<td>
						{if $curItem.is_confirmed eq 1}
							Да
						{else}
							До {$curItem.banned_month|date_format:"%d/%m/%Y %H:%M"}
						{/if}
					</td>
				{/if}
			</tr>
			{/foreach}
		</table>	
	{else}
		<table class="theme">
			<tr>
				<th>№</th>
				<th>Начальный IP</th>
				<th>Конечный IP</th>
				<th>Начало бана</th>
				<th>Истекает</th>
				<th>Описание сети</th>
				<th>Тип бана</th>
			</tr>
			{foreach key=key item=curItem from=$_dataGrid}
			<tr class="tr{if $key is odd}1{else}2{/if}">
				<td><strong>{$curItem.counter}</strong></td>
				<td>{$curItem.init_ip}</td>
				<td>{$curItem.end_ip}</td>
				<td>{$curItem.banned_time|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>{$curItem.banned_period|date_format:"%d/%m/%Y %H:%M"}</td>
				<td>{$curItem.network_description}</td>
				<td>{$curItem.ban_type}</td>
			</tr>
			{/foreach}
		</table>	
	{/if}
{/if}