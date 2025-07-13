{include file="forum/header.tpl"}
<div class="navigation">
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; {$title_part}
    </div>
</div>
<div class="line1"></div>
{* {if ($curent_group.groupID >0) && (!$curent_group.commerce)}<div class="zapret">В данной группе <span>ЗАПРЕЩЕНО</span> создание коммерческих тем</div>{/if} *}
{include file="forum/paging.tpl"}
{section name="g" loop="$groups"}
<div class="box1">
	<a href="/forum/{$groups[g].groupID}/" class="title">{$groups[g].caption}</a>{if strlen($groups[g].description) > 0} - {$groups[g].description}{/if}<br><span class="white">Тем: <span class="bold">{$groups[g].themes}</span>&nbsp;|&nbsp;Обновление: <span class="bold">{$groups[g].updated}</span></span>
</div>
{/section}
{include file="forum/footer.tpl"}