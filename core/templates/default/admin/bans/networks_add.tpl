{include file="admin/error/error_messages.tpl"}
<form action="" name="editform" method="post">
	<table class="theme">
		<tr>
			<td align="right">
				Начальный IP
			</td>
			<td>
				<input type="text" name="ban[init_ip_list][]" value="{$_params.ban.init_ip_list[0]}" style="width: 40px;" />
				-
				<input type="text" name="ban[init_ip_list][]" value="{$_params.ban.init_ip_list[1]}" style="width: 40px;" />
				-
				<input type="text" name="ban[init_ip_list][]" value="{$_params.ban.init_ip_list[2]}" style="width: 40px;" />
				-
				<input type="text" name="ban[init_ip_list][]" value="{$_params.ban.init_ip_list[3]}" style="width: 40px;" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Конечный IP
			</td>
			<td>
				<input type="text" name="ban[end_ip_list][]" value="{$_params.ban.end_ip_list[0]}" style="width: 40px;" />
				-
				<input type="text" name="ban[end_ip_list][]" value="{$_params.ban.end_ip_list[1]}" style="width: 40px;" />
				-
				<input type="text" name="ban[end_ip_list][]" value="{$_params.ban.end_ip_list[2]}" style="width: 40px;" />
				-
				<input type="text" name="ban[end_ip_list][]" value="{$_params.ban.end_ip_list[3]}" style="width: 40px;" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Правило
			</td>
			<td>
				<select name="ban[ruleID]" class="complaint_sel" id="rule_id">
					{foreach item=rule from=$_params.rules}
						<option value="{$rule.ruleID}" {if $_params.ban.ruleID == $rule.ruleID}selected="selected"{/if}>{$rule.caption}</option>
					{/foreach}
				</select>			
			</td>
		</tr>
		<tr>
			<td align="right">
				Комментарий
			</td>
			<td>
				<textarea name="ban[comments]" rows="5" cols="80">{if $_params.ban.comments}{$_params.ban.comments}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td align="right">
				Период
			</td>
			<td>
				<select name="ban[banned_period]">
					<option value="0">Бесконечный</option>
					<option value="86400" {if $_params.ban.banned_period == 86400}selected="selected"{/if}>1 день</option>
					<option value="172800" {if $_params.ban.banned_period == 172800}selected="selected"{/if}>2 дня</option>
					<option value="259200" {if $_params.ban.banned_period == 259200}selected="selected"{/if}>3 дня</option>
					<option value="604800" {if $_params.ban.banned_period == 604800}selected="selected"{/if}>1 неделя</option>
					<option value="2419200" {if $_params.ban.banned_period == 2419200}selected="selected"{/if}>1 месяц</option>
					<option value="4838400" {if $_params.ban.banned_period == 4838400}selected="selected"{/if}>2 месяца</option>
					<option value="7257600" {if $_params.ban.banned_period == 7257600}selected="selected"{/if}>3 месяца</option>
					<option value="14515200" {if $_params.ban.banned_period == 14515200}selected="selected"{/if}>6 месяцев</option>
					<option value="29030400" {if $_params.ban.banned_period == 29030400}selected="selected"{/if}>1 год</option>
					<option value="300000000" {if $_params.ban.banned_period == 300000000}selected="selected"{/if}>Навсегда</option>
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
					<option value="11" {if $_params.ban.ban_type == 11}selected="selected"{/if}>Только-чтение</option>
					<option value="10" {if $_params.ban.ban_type == 10}selected="selected"{/if}>Необх. авторизация</option>
					<option value="1" {if $_params.ban.ban_type == 1}selected="selected"{/if}>Полный бан</option>
				</select>				
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="event" value="forumbannetworks" />
				<input type="submit" value="Добавить" />
			</td>
		</tr>
	</table>
</form>