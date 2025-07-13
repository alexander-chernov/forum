	<form id="mainform" action="" method="POST">
		<table class="theme">
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
					Период
				</td>
				<td>
					<select name="ban[ban_period]">
						{foreach item=period from=$_params.banPeriod key="keyBann"}
							<option value="{$keyBann}" {if $_params.ban.ban_period == $keyBann}selected="selected"{/if}>{$period}</option>
						{/foreach}
					</select>
					{if $_params.ban.btime != ''}
						<br/><br/>
						<table>
							<tr>
								<td><b>Начало бана</b></td>
								<td><b>{$_params.ban.btime}</b></td>
							</tr>
							<tr>
								<td><b>Истекает</b></td>
								<td><b>{$_params.ban.etime}</b></td>
							</tr>
						</table>
					{/if}
				</td>
			</tr>
			<tr>
				<td align="right">
					Комментарий
				</td>
				<td>
					<textarea name="ban[admin_comment]" rows="5" cols="80">{if $_params.ban.admin_comment}{$_params.ban.admin_comment}{/if}</textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
				</td>
				<td>
					<input type="button" value="Сохранить" onclick="sendEventForm('forumsavebanuser')" />
					<input type="hidden" value="" id="event" name="event"/>
				</td>
			</tr>
		</table>
	</form>