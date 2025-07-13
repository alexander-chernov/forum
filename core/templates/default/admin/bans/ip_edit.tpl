<form action="" name="editform" method="post">
<table class="theme">
	<tr>
		<td align="right">
			IP-адрес
		</td>
		<td>
			<input readonly="readonly" type="text" name="ban[init_ip]" value="{$_dataGrid.init_ip}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			Ник автора
		</td>
		<td>
			<input type="text" name="ban[author]" value="{$_dataGrid.author}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			Реальный ник
		</td>
		<td>
			<input type="text" name="ban[realname]" value="{$_dataGrid.realname}" />
		</td>
	</tr>
	<tr>
		<td align="right">
			Правило
		</td>
		<td>
			<select name="ban[ruleID]" class="complaint_sel" id="rule_id">
				{foreach item=rule from=$_params.rules}
					<option value="{$rule.ruleID}" {if $_dataGrid.ruleID eq $rule.ruleID}selected="selected"{/if}>{$rule.caption}</option>
				{/foreach}
			</select>			
		</td>
	</tr>
	<tr>
		<td align="right">
			Комментарий
		</td>
		<td>
			<textarea name="ban[comments]" rows="5" cols="80">{$_dataGrid.comments}</textarea>
		</td>
	</tr>
	<tr>
		<td align="right">
			Период:
		</td>
		<td>
			<select name="ban[time]">
					<option value="86400" {if $_dataGrid.banned_period eq 86400}selected="selected"{/if}>1 день</option>
					<option value="172800" {if $_dataGrid.banned_period eq 172800}selected="selected"{/if}>2 дня</option>
					<option value="259200" {if $_dataGrid.banned_period eq 259200}selected="selected"{/if}>3 дня</option>
					<option value="604800" {if $_dataGrid.banned_period eq 604800}selected="selected"{/if}>1 неделя</option>
					<option value="2419200" {if $_dataGrid.banned_period eq 2419200}selected="selected"{/if}>1 месяц</option>
					<option value="4838400" {if $_dataGrid.banned_period eq 4838400}selected="selected"{/if}>2 месяца</option>
					<option value="7257600" {if $_dataGrid.banned_period eq 7257600}selected="selected"{/if}>3 месяца</option>
					<option value="14515200" {if $_dataGrid.banned_period eq 14515200}selected="selected"{/if}>6 месяцев</option>
					<option value="29030400" {if $_dataGrid.banned_period eq 29030400}selected="selected"{/if}>1 год</option>
					<option value="300000000" {if $_dataGrid.banned_period eq 300000000}selected{/if}>Навсегда</option>
			</select>
			<br/><br/>					
			{if $_params.binfo.btime != ''}
				<table>
					<tr>
						<td><b>Начало бана</b></td>
						<td><b>{$_params.binfo.btime}</b></td>
					</tr>
					<tr>
						<td><b>Истекает</b></td>
						<td><b>{if $_params.binfo.etime neq $_params.binfo.btime}{$_params.binfo.etime}{else}Бесконечный{/if}</b></td>
					</tr>
				</table>
			{/if}
		</td>
	</tr>
	<tr>
		<td align="right">
			Тип бана
		</td>
		<td>
			<select name="ban[ban_type]">
				<option value="11"{if $_dataGrid.ban_type eq 11}selected="selected"{/if}>Только-чтение</option>
				<option value="10"{if $_dataGrid.ban_type eq 10}selected="selected"{/if}>Необх. авторизация</option>
				<option value="1"{if $_dataGrid.ban_type eq 1}selected="selected"{/if}>Полный бан</option>
			</select>				
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumeditban" />
			<input type="submit" value="Редактировать" />
		</td>
	</tr>
</table>
</form>