{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		<a href="/forum/">Томские форумы</a> &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/">{$current_group.caption}</a>  &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/{$current_theme.themeID}/">{$current_theme.caption}</a>
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>
<div class="box_text">
Тема закрыта.

</div>
<A name=bot></A>
{include file="forum/footer.tpl"}