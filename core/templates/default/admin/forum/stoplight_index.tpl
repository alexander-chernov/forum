<script type="text/javascript">
	{if $_params.messageIDs} 
		var indexes = new Array(0, {$_params.messageIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" action="" method="POST">
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl" up=1}
		</div>
		{include file="admin/forum/stoplight_functions.tpl"}
		
		{foreach key=key item=curItem from=$_dataGrid}
		<div class="mess{if $key is odd}1{else}2{/if}">
			{if $_params.themeName}
				<a class="fr" href="/.admin/forum/themes/edit/{$curItem.themeID}/" target="_blank">Редактировать тему</a>
				{$curItem.counter}
				|
				<a href="/.admin/forum/themes/index/{$_params.groupID}/">{$_params.groupName}</a>
				|
				<strong>{$_params.themeName}</strong>
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
				<div style="background-color:{$curItem.color}; float: left; width: 24px; height: 16px; text-align: center" title="Уровень опасности">{$curItem.danger_level}</div>
				&nbsp;|
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
					<a href="/.admin/bans/ip/add/?ip={$curItem.author_ip}&msgID={$curItem.messageID}&theme={if $_params.themeName}{$_params.themeName}{else}{$_params.themes[$curItem.themeID]}{/if}&group={if $_params.groupName}{$_params.groupName}{else}{$_params.groups[$curItem.groupID]}{/if}" target="_blank">[ban]</a>
					<a href="/.admin/forum/messages/filter/?addr={$curItem.author_ip}" target="_blank">[поиск]</a>
					{if $curItem.authorID > 0}<a href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$curItem.authorID}" target="_blank">[ban User]</a>{/if}
				{/if}
			</div>

			<div class="tx">
				{$curItem.content|ahref|bbcode|nl2br}
			</div>
						
			<div class="tx1">
				<a class="fr" href="/.admin/forum/stoplight/complaint/?mid={$curItem.messageID}" target="_blank">Посмотреть список жалоб</a>
				<input type="checkbox" name="messages[{$curItem.messageID}]" id="messages_{$curItem.messageID}" />
				<a onclick="return chk_check('messages_{$curItem.messageID}');" href="javascript:void(0)">[Выбрать]</a>
			</div>
		</div>
		{/foreach}
		<input type="hidden" value="" id="event" name="event" />
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl"}
		</div>
		{include file="admin/forum/stoplight_functions.tpl"}
	</form>
{else}
	<div>Не найдено опасных сообщений</div>
{/if}