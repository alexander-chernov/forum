{include file="forum/header.tpl"}
    {literal}
        <script type="text/javascript">
            function HideTheme(tId) {
                $('#'+tId).hide();
                $.get("?", { event: "favoriteuserthemedelete", theme: tId });
            }
        </script>
    {/literal}

<div class="navigation">
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/">Томские форумы</A> &nbsp;/&nbsp; Избранное
    </div>
</div>
<div class="line1"></div>
{if ($curent_group.groupID >0) && (!$curent_group.commerce)}<div class="zapret">В данной группе <span>ЗАПРЕЩЕНО</span> создание коммерческих тем</div>{/if}
{include file="forum/paging.tpl"}

<table class=themes>

{if count($favorite) > 0}
{section name="t" loop="$favorite"}
	<tr id="{$favorite[t].themeID}"><td class="tdw1" title="{$favorite[t].updated}">{$favorite[t].updated|date_format:"%H:%M %e/%m"}</td>
		<td class="tdw3"><a href="/forum/{$favorite[t].groupID}/{$favorite[t].themeID}/">{$favorite[t].caption}</a>
		<span class="last_user">[
		<span class="title">{if $favorite[t].messages > $favorite[t].views}{$favorite[t].messages}{else}{$favorite[t].views}{/if}</span>/<span class="title">{$favorite[t].messages}</span>
		&nbsp;-&nbsp;{$favorite[t].updated_by}
		]</span>
            <a href="/forum/{$favorite[t].groupID}/{$favorite[t].themeID}/?p={math equation="ceil((x)/y)" x = $favorite[t].messages y = $_per_page}#bottom" title="В начало">
            <sub><img src="/images/first_1.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_2.gif'" onmouseout="this.src = '/images/first_1.gif'"></sub>
            </a>

		</td>
		<td class="tdw4" width="10">
		<a href="javascript:void(0)" onclick="HideTheme({$favorite[t].themeID})">
		<img src="/images/del_post.gif" alt="Удалить" title="Удалить"></a></td>
		<td class="tdw3" width="30"><span class="last_user">{$favorite[t].author}</span></td>
	</tr>
{/section}
{else}
	<tr><td colspan="4">Список избранного пуст</td></tr>
{/if}
</table>
{include file="forum/paging.tpl"}

{include file="forum/footer.tpl"}