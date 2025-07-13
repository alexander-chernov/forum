

{*<div style="display:none" class="paging1">
    <form class="paging_sel" action="">
        <b>Страница: </b>&nbsp;
        <select class=pagsel name=p onchange="ReloadMess(document.getElementById('pg').value);" id='pg'>
        {section name=foo start=1 loop=$_pages+1 step=1}
            <option label=1 value=1 selected>{$smarty.section.foo.index}</option>
        {/section}
        </select>
        <input class="btn_paging_sel" type="submit" value="&nbsp;" onclick="ReloadMess(document.getElementById('pg').value);">	
        &nbsp;из {$_pages}
    </form>
    <span class="prev"><a href="javascript:void(0)" name="mtop" onclick="ReloadMess(1);">Обновить</a></span>
</div>*}

    {literal}
    <script type="text/javascript">
        var mId = [];
        var mIdSmall = [];
        var im = 0;
        var imSmall = 0;
    </script>
    {/literal}

    <div class="dialog_box_text">
        {section name="m" loop="$messages"}
		<div class="text_box_1_dialog">
            <div class="box_user">
                <span class="name">{ if $_system_user.userID == $messages[m].fromuser}{$_system_user.user_name}{else}{$user_info.user_name}{/if}</span>
                <img src="/images/arr.gif"><span>{$messages[m].created|date_format:"%d/%m/%Y %H:%M"} </span>
                {if $_system_user.userID neq $messages[m].fromuser && $_system_user.is_admin eq 1}
                    [{$messages[m].creator_ip}]
                    <a href="/.admin/bans/ip/add/?ip={$messages[m].creator_ip}&pagerMsgId={$messages[m].id}" target="_blank"><img src="/images/btn_pasport_close.gif" alt="Бан {$messages[m].creator_ip}" title="Бан {$messages[m].creator_ip}" class="to_head" style="padding-top:5px"></a>

                    <a target=_blank href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$messages[m].fromuser}"><img src="/images/banned.png" winth=13 height="13" alt="Забанить пользователя" title="Забанить пользователя" class="to_head"></a>
                {/if}
                {if $_system_user.isKGB eq 1}
                    [{$messages[m].creator_ip}]
                {/if}
            </div>
        </div>
        <table width="100%" cellpadding="0" cellspacing="0" border=0>
        <tr>

            {if $messages[m].hav_file > 0}
                {literal}
                <script type="text/javascript">
                    mId[im]={/literal}{$messages[m].id}{literal};
                    im++;
                </script>
                {/literal}
                <td align="center"  class="text_box_2_dialog_pic">
            {/if}
            {if $messages[m].hav_file > 0}
                <div class="files" id="thumb_div_{$messages[m].first_file.id}">
                    <div class="thumb_img">
                    {*
                    <a href="/attaches/personal/{$messages[m].id}/{$messages[m].first_file.filename}" class="zoomin_{$messages[m].id}" id="zoom_{$messages[m].id}">
                        <img id="thumb_pic_{$messages[m].id}" src="/attaches/personal/{$messages[m].id}/thumb_{$messages[m].first_file.filename}" width="110" height="110" alt="{$messages[m].content|strip_tags}" title="{$messages[m].content|strip_tags}">
                    </a>
                    *}
                    <a href="/pager/img/{$messages[m].first_file.id}/" class="zoomin_{$messages[m].id}" id="zoom_{$messages[m].id}">
                        <img id="thumb_pic_{$messages[m].id}" src="/pager/thumb_img/{$messages[m].first_file.id}/" width="110" height="110" alt="{$messages[m].content|strip_tags}" title="{$messages[m].content|strip_tags}">
                    </a>
                    </div>
                </div>
            {/if}

            {if $messages[m].hav_file > 1}
                {literal}
                <script type="text/javascript">
                    mIdSmall[imSmall]={/literal}{$messages[m].id}{literal};
                    imSmall++;
                </script>
                {/literal}
                {foreach from=$messages[m].files item=file}
                    <div class="small_pager_img" id="small_div_{$file.id}">
                    {*
                    <a href="/attaches/personal/{$messages[m].id}/{$file.filename}" class="zoomsmall_{$messages[m].id}"
                    onmouseover="document.getElementById('thumb_pic_{$messages[m].id}').src = '/attaches/personal/{$messages[m].id}/thumb_{$file.filename}';document.getElementById('zoom_{$messages[m].id}').href = '/attaches/personal/{$messages[m].id}/{$file.filename}'"
                    >
                    <img src="/attaches/personal/{$messages[m].id}/thumb_{$file.filename}" width="32" height="32" alt="{$messages[m].content|strip_tags}" title="{$messages[m].content|strip_tags}"></a>
                    *}
                    <a href="/pager/img/{$file.id}/" class="zoomsmall_{$messages[m].id}"
                    onmouseover="document.getElementById('thumb_pic_{$messages[m].id}').src = '/pager/thumb_img/{$file.id}/';document.getElementById('zoom_{$messages[m].id}').href = '/pager/img/{$file.id}/'"
                    >
                    <img src="/pager/thumb_img/{$file.id}/" width="32" height="32" alt="{$messages[m].content|strip_tags}" title="{$messages[m].content|strip_tags}"></a>
                    </div>
                {/foreach}
            {/if}

            {if $messages[m].hav_file > 0}
                </td>
                <td width="100%"   class="text_box_2_dialog_txt">
            {else}
                <td width="100%"   class="text_box_2_dialog">
            {/if}

                <div  id="message_{$messages[m].id}" class="text_box_2_mess">
                   {$messages[m].content|bbcode|nl2br}
                </div>
                <div  id="hide_message_{$messages[m].id}" class="text_box_2_mess_hide" style="display: hone">
                    {$messages[m].content|bbcode|nl2br}
                </div>
            <div class="message_bottom_pager" {if $messages[m].id eq $lastId}id="mess_bottom_last"{/if}>
                    <a href="javascript:void(0)" onclick="citata('message_{$messages[m].id}');" class="answer"><img src="/images/v_1_1/cit_w.gif" alt="Цитировать" title="Цитировать" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/cit_y.gif'" onmouseout="this.src = '/images/v_1_1/cit_w.gif'"></a>
                </div>
                <input type=hidden value=0 id="url_message_{$messages[m].id}">
                <br>
            </td>
        </tr>
        </table>
		{/section}
	</div>

    {if $_lastpage > 1}
        <div class=pages>
            {if $_page>1}
                <a href="./?page=1">&nbsp;<<&nbsp;</a>|
            {/if}
            {section name = mySection start = 1 loop = $_lastpage step = 1}
                {if $smarty.section.mySection.index <= $_page+2 && $smarty.section.mySection.index >= $_page-2}
                    {if $smarty.section.mySection.index == $_page}
                        <span>&nbsp;{$_page}&nbsp;</span>|
                    {else}
                        <a href="./?page={$smarty.section.mySection.index}">&nbsp;{$smarty.section.mySection.index}&nbsp;</a>|
                    {/if}
                {/if}
            {/section}
            {if $_page<$_lastpage}
                <a href="./?page={$_lastpage}">&nbsp;>>&nbsp;</a>
            {/if}
        </div>
    {/if}
{literal}
<script language="javascript" type="text/javascript">
<!--
    $(document).ready(function() {
        replaceSmilesPager(50);
        for(var i = 0; i < mId.length; i++){
            $('.zoomin_'+mId[i]).lightBox({auto_resize:true});
        }
        for(var i = 0; i < mIdSmall.length; i++){
            $('.zoomsmall_'+mIdSmall[i]).lightBox();
        }
    });
//-->
</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter17732713 = new Ya.Metrika({id:17732713, enableAll: true, webvisor:true});
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/17732713" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
{/literal}
