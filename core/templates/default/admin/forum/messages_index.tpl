<script type="text/javascript">
	{if $_params.messageIDs} 
		var indexes = new Array(0, {$_params.messageIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" action="?p={$smarty.get.p}" method="POST">
		<div style="float:right">
			{assign var="__prev" value=`$__next-2`}
			{if $__next > 2}<a href="?p={$__prev}">Назад</a>{/if}
			<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			{if $_com_filter neq 'all'}<a href="?p={$__next}">Далее</a>{/if}
		</div>
		{include file="admin/forum/messages_functions.tpl"}
		
		{foreach key=key item=curItem from=$_dataGrid}
		<div  {if $curItem.hidden eq 1}style="background:#999999;"{else} class="mess{if $key is odd}1{else}2{/if}"{/if} >
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
					<a href="/.admin/bans/ip/add/?ip={$curItem.author_ip}&msgID={$curItem.messageID}&theme={if $_params.themeName}{$_params.themeName}{else}{$_params.themes[$curItem.themeID]}{/if}&group={if $_params.groupName}{$_params.groupName}{else}{$_params.groups[$curItem.groupID]}{/if}" target="_blank">[ban IP]</a>
                    <span id="author_ip_{$curItem.messageID}">{$curItem.author_ip_grey}</span>
					<a href="/.admin/bans/ip/add/?ip={$curItem.author_ip_grey}&msgID={$curItem.messageID}&theme={if $_params.themeName}{$_params.themeName}{else}{$_params.themes[$curItem.themeID]}{/if}&group={if $_params.groupName}{$_params.groupName}{else}{$_params.groups[$curItem.groupID]}{/if}" target="_blank">[ban IP]</a>
					<a href="/.admin/forum/messages/filter/?addr={$curItem.author_ip}" target="_blank">[поиск]</a>
					<a href="/.admin/forum/messages/filter/?addr={$curItem.author_ip_grey}" target="_blank">[поиск (серый ip)]</a>
				{/if}
				{if $curItem.authorID > 0  && isset($curItem.realname)}
					<a href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$curItem.authorID}" target="_blank">[ban User]</a>
				{/if} 
				{if $curItem.caption|trim neq ''}
					-> {$curItem.caption}
				{/if}
			</div>
			
			<div class="tx">
				{$curItem.content|bbcode|nl2br}
			</div>
			
			<div class="tx1">
				<input type="checkbox" name="messages[{$curItem.messageID}]" id="messages_{$curItem.messageID}" />
				<a onclick="return chk_check('messages_{$curItem.messageID}');" href="javascript:void(0)">[Выбрать]</a>
			</div>
            {if $curItem.hidden eq 1}
                Скрыто: {$curItem.hide_author} в {$curItem.hide_time}
            {/if}

        </div>
		{/foreach}
		<input type="hidden" value="" id="event" name="event"/>
		<div style="float:right">
			{if $__next > 2}<a href="?p={$__prev}">Назад</a>{/if}
			<a href="javascript:void(0)" onclick="selectAll('messages')">Инвертировать выделение</a>&nbsp;
			{if $_com_filter neq 'all'}<a href="?p={$__next}">Далее</a>{/if}
		</div>
		{include file="admin/forum/messages_functions.tpl"}
	</form>
{else}
	<div>Список запрошенных сообщений пуст</div>
{/if}