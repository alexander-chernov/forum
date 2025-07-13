			<script type="text/javascript">
				{if $_params.userIDs} 
					var indexes = new Array(0, {$_params.userIDs}); 
				{else}
					var indexes = new Array(0);
				{/if}
			</script>

			<b>{$_pageTitle}</b>
			
			{foreach item=error from=$_errors}
				<div style="color:#F00">{$error}</div>
			{/foreach}
			
			{include file="admin/pager.tpl"}
			<form id="mainform" action="" method="POST">
				<br />
				<div align="right">
					<input type="button" value="Забанить выделенные (по нику)" onclick="banConfirm('users', 'forumbanuser', 'по нику')" />
					<input type="button" value="Забанить выделенные (по IP)" onclick="banConfirm('users', 'forumbanaddr', 'по IP')" />
					<input type="button" value="Двойной бан" onclick="banConfirm('users', 'forumbandouble', 'по IP и по нику')" />

					<div id="reason" style="display:none">
						<br />
						<b/>Укажите информацию о бане <span id="commentLocale"></span></b><br />
						Правило:
						<select name="ban[ruleID]">
							{foreach item=rule from=$_params.rules}
								<option value="{$rule.ruleID}">{$rule.caption}</option>
							{/foreach}
						</select>
						<br />
						Комментарий: <br /><textarea name="ban[comment]" rows="5" cols="80"></textarea><br />			
						<span id="ban_type">
							Тип бана:
							<select name="ban[type]">
								<option value="1">Полный бан</option>
								<option value="10">Необх. авторизация</option>
								<option value="11">Только-чтение</option>
								<option value="12">Бан за флуд</option>
							</select>
						</span>
						Срок:
						<select name="ban[time]">
							<option value="86400">1 день</option>
							<option value="604800">1 неделя</option>
							<option value="2419200">1 месяц</option>
							<option value="29030400">1 год</option>
							<option value="300000000">Навсегда</option>
						</select>
						<br />
						<input type="button" value="Скрыть" onclick="$('#reason').hide()" />
						<input type="submit" value="Отправить" style="font-weight:bold" />
					</div>
				</div>
			
				<table class="theme">
					<tr>
					<th><input type="checkbox" onclick="checkAll('users', this.checked, indexes)" /></th>
					{foreach item=curItem from=$_tableHeaders}
						<th>{$curItem}</th>
					{/foreach}
					</tr>
					{foreach item=curItem from=$_dataGrid}
					<tr class="tr1">
						<td align="center"><input type="checkbox" name="users[{$curItem.userID}]" id="users_{$curItem.userID}" /></td>
						<td><b>{$curItem.user_name}</b></td>
						<td><a href="mailto:{$curItem.user_email}">{$curItem.user_email}</a></td>
						<td>{$curItem.user_fio}</td>
						<td>
							{$curItem.user_ip}
							<input type="hidden" name="addr[{$curItem.userID}]" value="{$curItem.user_ip}" />
						</td>
					</tr>
					{/foreach}
				</table>
				<input type="hidden" value="" id="event" name="event"/>
				<br />
			</form>
			{include file="admin/pager.tpl"}