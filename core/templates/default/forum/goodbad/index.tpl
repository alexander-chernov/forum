{include file="forum/header.tpl"}
{if $_system_user.is_admin}
    {literal}
        <script type="text/javascript">
            function HideTheme(tId) {
                $('#'+tId).hide();
                $.get("?", { event: "forumhideajaxtheme", _thId: tId });
            }
        </script>
    {/literal}
{/if}

<div class="navigation">
	<div class="complaint" id="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/#">Example Forum</A> &nbsp;/&nbsp; {$title_part}
    </div>
</div>
<div class="line1"></div>
<div id='messages'>
{section name="t" loop="$themes"}
    <div class="text_box_0" id="{$themes[t].themeID}"><table class=themes2>
    {if $themes[t].hottop}
            <tr>
            <td class="tdw1_top">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
			<td class="tdw3_top" ><a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/">{$themes[t].caption|strip_tags|htmlspecialchars}</a>&nbsp;&nbsp;<span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span></td>
			<td class="tdw2_top">
			{if $themes[t].authorID >0  && $themes[t].author eq $themes[t].realname}
				<a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});" title="Автор">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
			{else}
				<span class="last_user"><span class="title">{$themes[t].author}</span></span>
			{/if}				
			</td>
            <td class="tdw2_top">{$themes[t].themeRating}</td>
		</tr>
    {else}
        <tr>
        <td class="tdw1">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
	    <td class="tdw3">{assign var=thid  value=$themes[t].themeID}{php} if ($this->_tpl_vars['_fav'][$this->_tpl_vars['thid']]['userID']){echo "<sup><img src='/images/v_1_1/fav_y.gif' width=7></sup> ";}{/php}<a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/" title="{php}
            foreach ($this->_tpl_vars['groups'] as $_k => $_group) {
                if ($_group['groupID'] == $this->_tpl_vars['themes'][$this->_sections['t']['index']]['groupID']) {
                    echo html_entity_decode(htmlspecialchars(strip_tags($_group['caption'])));
                }
            }
            {/php}">{$themes[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a> <span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span>&nbsp;<a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/?p={math equation="ceil((x)/y)" x = $themes[t].messages y = $_per_page}#bottom" title="В начало">
            <sub><img src="/images/first_1.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_2.gif'" onmouseout="this.src = '/images/first_1.gif'"></sub>
            </a>
            </td>
            {if $_system_user.is_admin}
                <td width="10"> 
                <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                </td>
            {/if}

	    <td class="tdw2">
            {if $themes[t].authorID > 0  && $themes[t].author eq $themes[t].realname}
                <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});" title="Автор">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
            {else}
                <span class="last_user"><span class="title">{$themes[t].author}</span></span>
            {/if}
	    </td>
        <td class="tdw2">{$themes[t].themeRating}</td>
        </tr>
    {/if}
    </table></div>
{/section}
</div>
{include file="forum/footer.tpl"}