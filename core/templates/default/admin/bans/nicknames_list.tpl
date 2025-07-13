{include file="admin/error/error_messages.tpl"}<br/>
<script type="text/javascript">
	{if $_params.banIDs} 
		var indexes = new Array(0, {$_params.banIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>
<form method="post" action="/.admin/bans/nicknames/list/">
	<input type="text" value="{$_params.userSearch|htmlspecialchars}" name="usersearch">
	&nbsp;
	<input type="submit" value="Искать"><br/>
	<input type="checkbox" value="1" name="userIsId" {if $_params.userIsId == 1}checked="checked"{/if}>&nbsp;Искать по ID
</form>
{if count($_dataGrid) > 0 }
	<form id="mainform" action="" method="POST">
				<input type="hidden" value="{$_params.userSearch|htmlspecialchars}" name="usersearch">
				<input type="hidden" value="{$_params.userIsId}" name="userIsId">
				Правило


        <select name="ban[ruleID]" class="complaint_sel" id="rule_id">

					{foreach item=rule from=$_params.rules}
						<option value="{$rule.ruleID}" {if $_params.ban.ruleID == $rule.ruleID}selected="selected"{/if}>{$rule.caption}</option>
					{/foreach}
				</select>
				<br/><br/>
				Период
				<select name="ban[ban_period]">
					{foreach item=period from=$_params.banPeriod key="keyBann"}
						<option value="{$keyBann}" {if $_params.ban.ban_period == $keyBann}selected="selected"{/if}>{$period}</option>
					{/foreach}
				</select>
				<br/><br/>
				Комментарий
				<br/>
				<textarea name="ban[admin_comment]" rows="5" cols="80">
                    {if $_params.ban.comments}{$_params.ban.admin_comment}{/if}
                    {if $_params.content}{$_params.content}{/if}
                </textarea>
				<br/>
				<input type="button" value="Бан" onclick="sendEventForm('forumbanuser')" />
		<div style="float:right">{include file="admin/pager.tpl" up=1}</div>
		<table class="theme">
			<tr>
				<th><input type="checkbox" onclick="checkAll('bans', this.checked, indexes)" /></th>
				<th>№</th>
				<th>Ник</th>
			</tr>
			{assign var="i" value=$_params.userCounter}
			{foreach key=key item=curItem from=$_dataGrid}
				<tr class="tr{if $key is odd}1{else}2{/if}">
					<td align="center"><input type="checkbox" id="bans_{$curItem.userID}" name="users[{$curItem.userID}]" checked="checked"/></td>
					<td>{$i}</td>
					<td>{$curItem.user_name}</td>
				</tr>
				{assign var="i" value=`$i+1`}
			{/foreach}
		</table>
		<input type="hidden" value="" id="event" name="event"/>
		<div style="float:right">{include file="admin/pager.tpl"}</div>
	</form>
{else}
	<div>Список пуст</div>
{/if}