{include file="forum/header.tpl"}

<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; {$group_info.caption}
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>

<div class="box_text">
<p>
Доступ в группу "{$group_info.caption}" ограничен{if $group_info.deny_guest} для неавторизированых пользователей{/if}{if $group_info.deny_user}, для некоторых пользователей{/if}{if $group_info.deny_all}, для большинства пользователей{/if}.
</p>
</div>
<A name=bot></A>
{include file="forum/footer.tpl"}
