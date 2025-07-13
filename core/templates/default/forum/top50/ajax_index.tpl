<div class="text_box_0"><table class=themes2>

{section name="t" loop="$themes"}
    {if $themes[t].hottop}
            <tr id="{$themes[t].themeID}"><td class="tdw1_top" title="{$themes[t].fact_date}">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
			<td class="tdw3_top" ><a
            title="{$themes[t].group_title}"
{*	        title="{php}
	                $split_array = 0;
	                $mess = '';
	                $word_count = 0;
	                $mess = strip_tags(htmlspecialchars($this->_tpl_vars['themes'][$this->_sections['t']['index']]['first_mess']));
                    $split_array = preg_split('/\s+/',$mess);
                    $word_count = preg_grep('/[a-zA-Z0-9\\x80-\\xff]/', $split_array);
                    if (count($word_count)>30) {
                        for ($i=0;$i<=30;$i++){
                            echo $split_array[$i].' ';
                        }
                        echo '...';
                    } else {
                        echo $mess;
                    }
	            {/php}"
*}	            
			href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/">{$themes[t].caption|strip_tags|htmlspecialchars}</a>&nbsp;&nbsp;<span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span>
			&nbsp;<a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/?p={math equation="ceil((x)/y)" x = $themes[t].messages y = $_per_page}#bottom" title="В начало">
			<sub><img src="/images/first_11.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_21.gif'" onmouseout="this.src = '/images/first_11.gif'"></sub>
            </a>
			</td>
            {if $_system_user.is_admin}
                <td width="10" class="tdw2_close">
                <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                </td>
            {/if}
			<td class="tdw2_top">
			{if $themes[t].authorID >0  && $themes[t].author eq $themes[t].realname}
				<a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
			{else}
				<span class="last_user"><span class="title">{$themes[t].author}</span></span>
			{/if}				
			</td>
		</tr>
    {else}
        <tr id="{$themes[t].themeID}"><td class="tdw1" title="{$themes[t].fact_date}">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
	    <td class="tdw3">{assign var=thid  value=$themes[t].themeID}{php} if ($this->_tpl_vars['_fav'][$this->_tpl_vars['thid']]['userID']){echo "<sup><img src='/images/v_1_1/fav_y.gif' width=7></sup> ";}{/php}<a
	    href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/"
        title="{$themes[t].group_title}"
{*
	        title="{php}
	                $split_array = 0;
	                $mess = '';
	                $word_count = 0;
	                $mess = strip_tags(htmlspecialchars($this->_tpl_vars['themes'][$this->_sections['t']['index']]['first_mess']));
                    $split_array = preg_split('/\s+/',$mess);
                    $word_count = preg_grep('/[a-zA-Z0-9\\x80-\\xff]/', $split_array);
                    if (count($word_count)>30) {
                        for ($i=0;$i<=30;$i++){
                            echo $split_array[$i].' ';
                        }
                        echo '...';
                    } else {
                        echo $mess;
                    }
	            {/php}"
*}
	     {*
	    title="{php}
            foreach ($this->_tpl_vars['groups'] as $_k => $_group) {
                if ($_group['groupID'] == $this->_tpl_vars['themes'][$this->_sections['t']['index']]['groupID'])
                {
                    echo html_entity_decode(htmlspecialchars(strip_tags($_group['caption'])));
                }
            }
            {/php}" *}>{$themes[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a> <span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span>&nbsp;<a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/?p={math equation="ceil((x)/y)" x = $themes[t].messages y = $_per_page}#bottom" title="В начало">
            <sub><img src="/images/first_1.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_2.gif'" onmouseout="this.src = '/images/first_1.gif'"></sub>
            </a>
            </td>
            {if $_system_user.is_admin}
                <td width="10">
                <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                </td>
            {/if}
            </td>
	    <td class="tdw2">
            {if $themes[t].authorID > 0  && $themes[t].author eq $themes[t].realname}
                <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
            {else}
                <span class="last_user"><span class="title">{$themes[t].author}</span></span>
            {/if}
	    </td>
        </tr>
    {/if}
{/section}
    </table>
    </div>

