<script id="objCalendarScript" type="text/javascript" src="/js/calendar.js"></script>
<form action="" name="editform" method="post">
<table class="theme">
    <!--
	<tr>
		<td align="right">
			От:
		</td>
		<td>
			<input id="objDate1" type="text" name="filter[init]" value="{if isset($_params._filter.init)}{$_params._filter.init}{/if}" />
			{literal}
			<script language="JavaScript">
					var crCal1 = Calendar('objDate1', fmtYYYYMMDD24, '-', ':', 2000, 2055, false, objRussianStrings);
			</script>
			{/literal}
		</td>
	</tr>
	<tr>
		<td align="right">
			До:
		</td>
		<td>
			<input id="objDate2" type="text" name="filter[end]" value="{if isset($_params._filter.end)}{$_params._filter.end}{/if}" />
			{literal}
			<script language="JavaScript">
					var crCal2 = Calendar('objDate2', fmtYYYYMMDD24, '-', ':', 2000, 2055, false, objRussianStrings);
			</script>
			{/literal}
		</td>
	</tr>
    -->
	<tr>
		<td align="right">
			IP:
		</td>
		<td>
			<input type="text" name="filter[addr]" value="{if isset($_params._filter.addr)}{$_params._filter.addr}{/if}" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="event" value="forumfiltermessage" />
			<input type="submit" value="Поиск" />
		</td>
	</tr>
</table>
</form>
{if count($_dataGrid) neq 0 }
	<script type="text/javascript">
		{if $_params.messageIDs} 
			var indexes = new Array(0, {$_params.messageIDs}); 
		{else}
			var indexes = new Array(0);
		{/if}
	</script>

	{include file="admin/error/error_messages.tpl"}
	{include file="admin/messages/messages_messages.tpl"}

	{if count($_dataGrid) > 0 }
		<form id="mainform" method="POST">
			<input type="hidden" name="filter[init]" value="{if isset($_params._filter.init)}{$_params._filter.init}{/if}" />
			<input type="hidden" name="filter[end]" value="{if isset($_params._filter.end)}{$_params._filter.end}{/if}" />
			<input type="hidden" name="filter[addr]" value="{if isset($_params._filter.addr)}{$_params._filter.addr}{/if}" />

			<div style="float:right">
				<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			</div>
			{include file="admin/forum/messages_functions.tpl"}
			
			{foreach key=key item=curItem from=$_dataGrid}
			<div class="mess{if $key is odd}1{else}2{/if}">
				{if $_params.themeName}
					<a class="fr" href="/.admin/forum/themes/edit/{$curItem.themeID}/" target="_blank">Редактировать тему</a>
					{$curItem.counter}
					|
					<a href="/.admin/forum/themes/index/{$_params.groupID}/">{$_params.groupName}</a>
					|
					{$_params.themeName}
					<a href="/forum/{$curItem.groupID}/{$curItem.themeID}/" target="_blank">
						<img src="/images/admin/book_next.png" title="Перейти к теме на форуме" alt="Перейти к теме на форуме" />
					</a>
				{else}
					<a class="fr" href="/.admin/forum/themes/edit/{$curItem.themeID}/" target="_blank">Редактировать тему</a>
					{$curItem.counter}
					|
					<a href="/.admin/forum/themes/index/{$curItem.groupID}/">{$_params.groups[$curItem.groupID]}</a>
					|
					<a href="/.admin/forum/messages/index/{$curItem.themeID}/">{$_params.themes[$curItem.themeID]}</a>
					<a href="/forum/{$curItem.groupID}/{$curItem.themeID}/" target="_blank">
						<img src="/images/admin/book_next.png" title="Перейти к теме на форуме" alt="Перейти к теме на форуме" />
					</a>
				{/if}
					
				<div class="tx">
					<a class="fr" target="_blank" href="/.admin/forum/messages/edit/{$curItem.messageID}/" target="_blank">Редактировать</a>
					{$curItem.created|date_format:"%d/%m/%Y %H:%M"}
					|
					{if $curItem.realname neq $curItem.author}
						<strong>{$curItem.author}</strong>
						|
						{if $curItem.authorID > 0  && isset($curItem.realname)} 
							(наст. <a href="/.admin/users/pager/send/to/{$curItem.authorID}" target="_blank">{$curItem.realname}</a>)
						{else}
							N/A
						{/if}
					{else}
						{if $curItem.authorID > 0  && isset($curItem.realname)} 
							<a href="/.admin/users/pager/send/to/{$curItem.authorID}" target="_blank">{$curItem.realname}</a>
						{else}
							<strong>{$curItem.author}</strong>
						{/if}
					{/if}
					|
					{if $curItem.author_ip neq 0}
						<span id="author_ip_{$curItem.messageID}">{$curItem.author_ip}</span>
						<a href="/.admin/bans/ip/add/?ip={$curItem.author_ip}&msgID={$curItem.messageID}" target="_blank">[ban]</a>
						<a href="/.admin/forum/messages/filter/?addr={$curItem.author_ip}" target="_blank">[поиск]</a>
					{/if}
					{if $curItem.caption|trim neq ''}
						-> {$curItem.caption}
					{/if}
				</div>
				
				<div class="tx">
					{$curItem.content|bbcode|nl2br}
				</div>
				
				<div class="tx1">
					<input type="checkbox" name="messages[{$curItem.messageID}]" id="messages_{$curItem.messageID}" value="on" />
					<a onclick="return chk_check('messages_{$curItem.messageID}');" href="javascript:void(0)">[Выбрать]</a>
				</div>
			</div>
			{/foreach}
			<input type="hidden" id="event" name="event"/>
			<div style="float:right">
				<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			</div>
			{include file="admin/forum/messages_functions.tpl"}
		</form>
	{else}
		<div>Список запрошенных сообщений пуст</div>
	{/if}
{/if}