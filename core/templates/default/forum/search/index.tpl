{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="/forum/">Example Forum</A> &nbsp;/&nbsp; ПОИСК
    </div>
    {literal}
    	<script type="text/javascript">
    		function showExtSearch(){
        		var obj = document.getElementById('listTheme');
        		if (obj.style.display == 'block'){
        			obj.style.display = 'none';
        		}else{
        			obj.style.display = 'block';
             	}
        		return false;
    		}
    	</script>
    {/literal}
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
</div>
<div class="block_menu">
	<div class="complaint"></div>
	<div class="box_path">
		<div align="center">
            <form action="" method="post">
                <input type="hidden" value="forumsearch" name="event"/>
                <input type="text" name="query" value="{$_query}" id="search" />
                <input type="submit" value="Искать!" />
                <div style="text-align: center;"><a href="" onclick="return showExtSearch();" style="color:black">Расширенный поиск</a></div>
                <div style="display: {php}if (count($this->_tpl_vars['SListThemes'])>0)echo 'block'; else echo 'none';{/php}; padding: 10px 0px 0px 0px;" id="listTheme">
                	<table>
                		{assign var="io" value=1}
						{section name="g" loop="$groups"}
							{if $io == 1}
								<tr>
							{/if}
								{assign var="gID" value=$groups[g].groupID}
									<td style="color:black" ><input type="checkbox" value={$groups[g].groupID} name="sListThemes[{$groups[g].groupID}]" {if $SListThemes[$gID]}checked="checked"{/if}>&nbsp;{$groups[g].caption}</td>
							{if $io%3 == 0}
								</tr>
		                		{assign var="io" value=1}
							{else}
		                		{assign var="io" value=`$io+1`}
							{/if}
						{/section}
						{if $io != 1}
							</tr>
						{/if}
					</table>
                </div>
            </form>
		</div>
	</div>
</div>
{if count($results) > 0}
    <div class="text_box_0"><table class="themes2">
	{section name="t" loop="$results"}
            <tr id="{$results[t].themeID}">
                <td class="tdw1">{$results[t].created|date_format:"%H:%M %e/%m"}</td>
                <td class="tdw3">{assign var=thid  value=$results[t].themeID}{php} if ($this->_tpl_vars['_fav'][$this->_tpl_vars['thid']]['userID']){echo "<sup><img src='/images/v_1_1/fav_y.gif' width=7></sup> ";}{/php}<a href="/forum/{$results[t].groupID}/{$results[t].themeID}/">{$results[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a>&nbsp;<span class="last_user">[<span class="title">{if $results[t].messages > $results[t].views}{$results[t].messages}{else}{$results[t].views}{/if}</span>/<span class="title">{$results[t].messages}</span>&nbsp;-&nbsp;{$results[t].updated_by}]</span>
                <a href="/forum/{$results[t].groupID}/{$results[t].themeID}/?p={math equation="ceil((x)/y)" x = $results[t].messages y = $_per_page}#bottom" title="В начало">
                <img src="/images/first_1.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_2.gif'" onmouseout="this.src = '/images/first_1.gif'">
                </a>
                </td>
                {if $_system_user.is_admin || $isGroupAdministrator}
                    <td width="10">
                    <a href="javascript:void(0)" onclick="HideTheme({$results[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                    </td>
                {/if}
                <td class="tdw2">{if $results[t].authorID >0  && $results[t].author eq $results[t].realname}<a href="javascript:void(0)" onclick="LoadPassport({$results[t].authorID});">{$results[t].author}<sup title="Карма">{$results[t].karma}</sup></a>
                {else}
                    <span class="last_user">{$results[t].author}</span>
                {/if}
                </td>
            </tr>
{*
		    <tr><td class="tdw1">{$results[t].updated|date_format:"%H:%M %d/%m"}</td>
			<td><a href="/forum/{$results[t].groupID}/{$results[t].themeID}/">{$results[t].caption}</a></td>
            {if $_system_user.is_admin || $isGroupAdministrator}
                <td width="10">
                <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                </td>
            {/if}
			<td>{$results[t].author}</td>
		</tr>
*}


	{/section}
        </table>
    </div>
{else}
		<tr><td colspan="4"></td></tr>
{/if}
</table>

{include file="forum/footer.tpl"}

