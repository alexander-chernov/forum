{include file="forum/header.tpl"}
{include file="forum/messages/favorite.tpl"}

<div class="navigation">
	{if $_logged_in eq 1}
		<div class="complaint">
			<a href="javascript:;" title="Добавить тему в избранное" onclick="LoadAddFavorites({$current_theme.themeID});">
				<img src="/images/add_theme.gif" alt="Добавить тему в избранное" title="Добавить тему в избранное">
			</a>
		</div>
	{/if}
	<div class="box_path">
		:: <a href="/forum/">Example Forum</a> &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/">{$current_group.caption|strip_tags|htmlspecialchars}</a> &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/{$current_theme.themeID}/">{$current_theme.caption|strip_tags|htmlspecialchars}</a>
		{if $_system_user.is_admin}
			&nbsp;<a href="/.admin/forum/themes/edit/{$current_theme.themeID}/" target="_blank">[ред.]</a>
			&nbsp;<a href="/.admin/forum/messages/index/{$current_theme.themeID}/" target="_blank">[в админку]</a>
			{if $current_theme.authorID}
				&nbsp;<a href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$current_theme.authorID}" target="_blank">[бан {$current_theme.realname}]</a>
			{/if}
		{/if}
    </div>
</div>
<div class="line1"></div>
{include file="forum/banner/middle.tpl"}
	<div class="text_box_1">
		<div class="complaint">
			{if $_logged_in eq 1 && $current_theme.authorID eq $_system_user.userID && $current_theme.moderated eq 1}
				<a href="?event=forumdeletemessageinmytheme&_msgId={$messages.messageID}" onclick="return confirm('Удалить?');">Удалить</a>
			{/if}
			{if $_logged_in eq 1}
				<a href="#" onclick="LoadComplaint({$messages.messageID}, '{$messages.author}')" title="Пожаловаться на сообщение.">
					<img src="/images/complaint_mess.gif" alt="Пожаловаться на сообщение." title="Пожаловаться на сообщение.">
				</a> 
			{/if}
		</div>
		<div class="box_user">
		   {$messages.created|date_format:"%e.%m.%Y (%H:%M)"} | {if $messages.authorID >0 && $messages.author eq $messages.realname}<a href="#" class="name" onclick="LoadPassport({$messages.authorID});">{$messages.author}
		   </a>{else}<span class="name1">{$messages.author}
		   </span>{/if} -&gt; <span class="white1">{$messages.caption}</span>
		</div>
	</div>
	<div class="text_box_2">
	<div id="message[{$messages.messageID}]" class="text_box_2_mess">{$messages.content|ahref|bbcode|nl2br}</div>
	</div>
{include file="forum/footer.tpl"}