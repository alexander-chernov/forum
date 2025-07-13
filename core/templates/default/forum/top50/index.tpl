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
		:: <a href="/forum/#">Томские форумы</A> &nbsp;/&nbsp; {$title_part}
    </div>
</div>
<div class="line1"></div>
<div class="text_box_0" >
<div id='messages'>
<div class="text_box_0" ><table class=themes2>
{section name="t" loop="$themes"}

    {if $themes[t].hottop}
            <tr id="{$themes[t].themeID}"><td class="tdw1_top" title="{$themes[t].fact_date}">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
			<td class="tdw3_top" ><a
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
			href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/">{$themes[t].caption|strip_tags|htmlspecialchars}</a></a>&nbsp;&nbsp;<span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span>
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
				<a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});" title="Автор">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
			{else}
				<span class="last_user"><span class="title">{$themes[t].author}</span></span>
			{/if}				
			</td>
		</tr>
    {else}
        <tr id="{$themes[t].themeID}"><td class="tdw1" title="{$themes[t].fact_date}">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
	    <td class="tdw3">{assign var=thid  value=$themes[t].themeID}{php} if ($this->_tpl_vars['_fav'][$this->_tpl_vars['thid']]['userID']){echo "<sup><img src='/images/v_1_1/fav_y.gif' width=7></sup> ";}{/php}
	    <a
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
	        {* title="{php}
            foreach ($this->_tpl_vars['groups'] as $_k => $_group) {
                if ($_group['groupID'] == $this->_tpl_vars['themes'][$this->_sections['t']['index']]['groupID']) {
                    echo html_entity_decode(htmlspecialchars(strip_tags($_group['caption'])));
                }
            }
            {/php}" *}>{$themes[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a> <span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;<span class="title">{$themes[t].updated_by}</span>]</span>&nbsp;
            <a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/?p={math equation="ceil((x)/y)" x = $themes[t].messages y = $_per_page}#bottom" title="В начало">
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
                {if $is_mobile eq 'mobile'}
                    <span class="title"><a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});" title="Автор">{$themes[t].author|truncate:9}<sup title="Карма">{$themes[t].karma}</sup></a>
                {else}
                    <span class="title"><a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});" title="Автор">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
                {/if}
            {else}
                {if $is_mobile eq 'mobile'}
                    <span class="last_user"><span class="title">{$themes[t].author|truncate:9}</span></span>
                {else}
                    <span class="last_user"><span class="title">{$themes[t].author}</span></span>
                {/if}
            {/if}
	    </td>
        </tr>
    {/if}
{/section}
    </table>
    </div>
</div>
    <div style="float: right">
        {if !$isLast}<span class="next"><a  href="?page={$page+1}"><img src="/images/v_1_1/r_b.gif" title="Вперед" alt="Вперед" border="0" class="to_head" style="width:24px" onmouseover="this.src = '/images/v_1_1/r_y.gif'" onmouseout="this.src = '/images/v_1_1/r_b.gif'"></a></span>{/if}
        {if $page >1}<span class="prev"><a href="?page={$page-1}"><img src="/images/v_1_1/l_b.gif" title="Назад" alt="Назад" border="0" class="to_head" style="width:24px" onmouseover="this.src = '/images/v_1_1/l_y.gif'" onmouseout="this.src = '/images/v_1_1/l_b.gif'"></a></span>{/if}&nbsp;
    </div>
</div>
{if $is_top ne 1}
    {literal}
    <script type="text/javascript">
    var page = {/literal}{$page}{literal};
    function updateThemes(repeat) {
        if($(window).scrollTop()<=290){
            var messages = $.ajax({
                url: '/forum/?ajx=1&page='+page,
                success: function(data){
                    $('#messages').empty();
                    $('#messages').append(data);
                }
            });
        }
        if (repeat == true) {
            setTimeout("updateThemes(1);",60000);
        }
    }
    $(document).ready(function() {
        setTimeout("updateThemes(1);",60000);
    });
    </script>
    {/literal}
{/if}
{literal}
<script class="example" type="text/javascript">
$(document).ready(function()
{
   $('#messages a[href][title]').qtip({
      content: {
         text: false
      },
      style: 'blue'
   });
});
</script>
{/literal}

{include file="forum/footer.tpl"}