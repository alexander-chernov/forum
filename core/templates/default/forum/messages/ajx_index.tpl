{if $userRights.access_type eq 0 ||
($userRights.access_type eq 1 && $_system_user.userID > 0) ||
($userRights.access_type eq 2 && $userRights.optRead eq 1) ||
($userRights.access_type eq 3 && $userRights.optRead neq 1) ||
($userRights.access_type eq 4) ||
$_system_user.is_admin ||
$isGroupAdministrator ||
$isThemeOwner}

<!--<script type="text/javascript" src="/js/jquery.lightbox.js"></script>-->
{section name="m" loop="$messages"}
	{if $messages[m].rating > $smarty.const.MESSAGE_HIDE_RATING}
	    <div
	    class="text_box_1" id="{$messages[m].messageID}"
        ondblclick="ShowHideMess({$messages[m].messageID})"
	    >
	    <a name="mess{$messages[m].messageID}"></a>
		<div class="complaint">
{*
            {if $_logged_in eq 1 && $current_theme.authorID eq $_system_user.userID && $current_theme.moderated eq 1}
                <a href="?event=forumdeletemessageinmytheme&_msgId={$messages[m].messageID}" onclick="return confirm('Удалить?');">Удалить</a>
            {/if}
                {if $_system_user.is_admin}
                    <a href="/.admin/bans/ip/add/?ip={$messages[m].author_ip}&msgID={$messages[m].messageID}&theme={$current_theme.caption}&group={$current_group.caption}" target="_blank"><img src="/images/btn_pasport_close.gif" alt="Бан {$messages[m].author_ip}" title="Бан {$messages[m].author_ip}" class="to_head" style="padding-top:5px"></a>
                    {if $messages[m].author_ip_grey neq ''}
                        <a href="/.admin/bans/ip/add/?ip={$messages[m].author_ip_grey}&msgID={$messages[m].messageID}&theme={$current_theme.caption}&group={$current_group.caption}" target="_blank"><img src="/images/btn_pasport_close2.gif" alt="Бан {$messages[m].author_ip_grey}" title="Бан {$messages[m].author_ip_grey} (Серый ip)" class="to_head" style="padding-top:5px"></a>
                    {/if}
                    {if $messages[m].authorID >0}
                        <a href="javascript:void(0)" onclick="HideAllMessage({$messages[m].messageID})"><img src="/images/remove.png" winth=11 height="11" alt="Скрыть все сообщения этого пользователя за последние 7 дней" title="Скрыть все сообщения этого пользователя за последние 7 дней" class="to_head"></a>
                    {/if}
                {/if}
                {if $isGroupAdministrator || $_system_user.is_admin}
                    <a href="javascript:void(0)" onclick="HideMessage({$messages[m].messageID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть" class="to_head"></a>
                {/if}
*}
                <span class="error" id="err_{$messages[m].messageID}_mess"></span>

                {if $_system_user.is_admin eq 1}
                    {if $_system_user.is_admin eq 1}
                        <a href="/.admin/bans/ip/add/?ip={$messages[m].author_ip}&msgID={$messages[m].messageID}&theme={$current_theme.caption}&group={$current_group.caption}" target="_blank"><img src="/images/btn_pasport_close.gif" alt="Бан {$messages[m].author_ip}" title="Бан {$messages[m].author_ip}" class="to_head" style="padding-top:5px"></a>
                        {if $messages[m].author_ip_grey neq ''}
                            <a href="/.admin/bans/ip/add/?ip={$messages[m].author_ip_grey}&msgID={$messages[m].messageID}&theme={$current_theme.caption}&group={$current_group.caption}" target="_blank"><img src="/images/btn_pasport_close2.gif" alt="Бан {$messages[m].author_ip_grey}" title="Бан {$messages[m].author_ip_grey} (Серый ip)" class="to_head" style="padding-top:5px"></a>
                        {/if}

                        {if $messages[m].authorID >0}
                            <a target=_blank href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$messages[m].authorID}&msgID={$messages[m].messageID}&theme={$current_theme.caption}&group={$current_group.caption}"><img src="/images/banned.png" winth=13 height="13" alt="Забанить пользователя" title="Забанить пользователя" class="to_head"></a>
                        {/if}

                        {if $messages[m].authorID >0}
                            <a href="javascript:void(0)" onclick="HideAllMessage({$messages[m].messageID})"><img src="/images/remove.png" winth=11 height="11" alt="Скрыть все сообщения этого пользователя за последние 7 дней" title="Скрыть все сообщения этого пользователя за последние 7 дней" class="to_head"></a>
                        {/if}

                        {if $_system_user.is_admin eq 1}
                            <a href="javascript:void(0)" onclick="HideMessage({$messages[m].messageID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть" class="to_head"></a>
                        {/if}
{*
                        {if $messages[m].authorID >0}
                            <a href="javascript:void(0)" onclick="HideIpMessage({$messages[m].messageID})"><img src="/images/remove.png" winth=11 height="11" alt="Скрыть все сообщения этого пользователя за последние 7 дней" title="Скрыть все сообщения с данного IP за сегодня" class="to_head"></a>
                        {/if}
*}
                    {/if}

                {else}
                    {if $_system_user.userID eq $current_theme.authorID && $_system_user.userID>0}
                        <a href="javascript:void(0)" onclick="DeleteMessage({$messages[m].messageID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть" class="to_head"></a>
                        {else}
                        {if $isGroupAdministrator || $isGroupOwner}
                            <a href="javascript:void(0)" onclick="HideMessage({$messages[m].messageID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть" class="to_head"></a>
                        {/if}
                    {/if}
                {/if}
                <div id="help_{$messages[m].messageID}" class="box_buttons">
                    <br style="clear:both" />
                    <div class="box_barr" style="float:right;margin-top:2px">
                        <a href="javascript:;;" onclick="opa_st(document.getElementById('help_{$messages[m].messageID}'),0);return false">Закрыть</a>
                    </div>
                </div>
    {if $_logged_in eq 1}
            	{* 
                обрати внимание на айдишник у блока. 
                в блок ставится ссылка по клику на которую происходит событие редактирования, и содержимое этой дивки(по айдишнику сообщения) меняется на 
                две другие кнопки "сохранить" и "отменить". поэтому на дивку событие никакое вешать не надо. 
                ато раньше было так что ты айдишник прописал к ссылке и на ней же было событие. потом содержимое ссылки заменилось на другое, а событие осталось
                и по клику на "сохранить" или "отменить" у нас вызывалось событие сохранения и событие открытия формы редактирования заново. 
                *}
                    {if $messages[m].editInterval < $smarty.const.EDIT_MESSAGE_TIME_LIMIT}
                        {if $messages[m].authorID >0 && $messages[m].authorID eq $_system_user.userID}
                            <div id='editbut-{$messages[m].messageID}' style='display:inline'><a onclick="editMessage('{$messages[m].messageID}','{$smarty.const.EDIT_MESSAGE_COST}');" href="Javascript:;">
                                <img src="/images/notes_edit.png" alt="Редактировать" title="Редактировать" class="to_head" width="12" height="12">
                            </a></div>
                    	{/if}
                	{/if}
                <a href="javascript:void(0)"  onclick="addRating('{$messages[m].messageID}','-1');">
                    <img src="/images/v_1_1/unlike_b.gif" alt="Не понравилось (-1)" title="Не понравилось (-1)" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/unlike_y.gif'" onmouseout="this.src = '/images/v_1_1/unlike_b.gif'">
                </a>
                <a href="javascript:void(0);" title='Карма'><b id='rating_{$messages[m].messageID}' class="ratinglog">{if $messages[m].rating > 0}+{$messages[m].rating|round}{else}{$messages[m].rating|round}{/if}</b></a>
                <a href="javascript:void(0)" onclick="addRating('{$messages[m].messageID}',1);">
                    <img src="/images/v_1_1/like_b.gif" alt="Понравилось (+1)" title="Понравилось (+1)" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/like_y.gif'" onmouseout="this.src = '/images/v_1_1/like_b.gif'">
                </a>
                <a href="javascript:void(0)" onclick="LoadComplaint({$messages[m].messageID}, '{$messages[m].author}')" title="Пожаловаться на сообщение.">
                    <img src="/images/v_1_1/block_b.gif" alt="Пожаловаться на сообщение" title="Пожаловаться на сообщение" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/block_y.gif'" onmouseout="this.src = '/images/v_1_1/block_b.gif'">
                </a>
                <span class="error" id="err_{$messages[m].messageID}_rating"></span>
            {/if}
		</div>
		<div class="box_user">
		    {$messages[m].created|date_format:"%e.%m.%Y (%H:%M)"} |
            {if $messages[m].authorID >0 && $messages[m].author eq $messages[m].realname}
                <a href="#" class="name" onclick="LoadPassport({$messages[m].authorID});return false;">{$messages[m].author}<sup title="Карма">{$messages[m].karma}</sup></a>

                {if $messages[m].authorID >0 && $messages[m].authorID neq $_system_user.userID}
                    {if $messages[m].country_code neq ''}<span class="name2">({$messages[m].country_code})</span>{/if}
                {/if}

                {if $messages[m].authorID >0 && $messages[m].authorID neq $_system_user.userID}
                    | <a href="javascript:void(0)" onclick="PutInBlackList({$messages[m].messageID},0)"><img src="/images/banned.png" winth=13 height="13" alt="Поместить пользователя в черный список" title="Поместить пользователя в черный список" class="to_head"></a>
                {/if}
            {else}
                <span class="name1">{$messages[m].author}</span>

                {if $messages[m].authorID >0 && $messages[m].authorID neq $_system_user.userID}
                    {if $messages[m].country_code neq ''}<span class="name2">({$messages[m].country_code})</span>{/if}
                {/if}

                {if $_system_user.is_admin  || $_system_user.isKGB eq 1}
                    {if $messages[m].authorID >0 && $messages[m].author neq $messages[m].realname}
                         (<a href="#" class="name" onclick="LoadPassport({$messages[m].authorID});return false;">{$messages[m].realname}</a>)
                    {/if}
                {/if}
                {if $messages[m].authorID >0 && $messages[m].authorID neq $_system_user.userID}
                    | <a href="javascript:void(0)" onclick="PutInBlackList({$messages[m].messageID},{$smarty.const.EDIT_HIDE_ANONIM_COST})"><img src="/images/banned.png" winth=13 height="13" alt="Поместить пользователя в черный список" title="Поместить пользователя в черный список" class="to_head"></a>
                {/if}
            {/if}
            {if $_system_user.isKGB eq 1}
                [{$messages[m].author_ip}]
            {/if}
            <img src="/images/arr.gif"> <span class="white1" id='caption-{$messages[m].messageID}'>{$messages[m].caption}</span>
		</div>
	</div>
    <div
        id="m_{$messages[m].messageID}"
        class="text_box_2"
        style="
        {if $messages[m].forme == 1}
            background-color:#2f6091;
        {elseif $messages[m].authorID eq $_system_user.userID && $_system_user.userID >0}
            background-color:#326293;
        {/if}
        {if $messages[m].rating <= $smarty.const.MESSAGE_HALFHIDE_RATING}
            display:none;
        {/if}
        "
        >

    <table width="100%" cellpadding="0" cellspacing="0" border=0>
    <tr>
        {if $messages[m].hav_file > 0}
            {literal}
            <script type="text/javascript">
                mId[im]={/literal}{$messages[m].messageID}{literal};
                im++;
            </script>
            {/literal}
            <td align="center">
        {/if}
        {if $messages[m].hav_file > 0}
            {if $messages[m].first_file.id>0}
            <div class="files" id="thumb_div_{$messages[m].first_file.id}">

                <div class="thumb_img">
                <a href="/attaches/{$messages[m].themeID}/{$messages[m].messageID}/{$messages[m].first_file.filename}" class="zoomin_{$messages[m].messageID}" id="zoom_{$messages[m].messageID}">
                <img
                        alt="{$messages[m].author}: {$messages[m].content|strip_tags|htmlspecialchars|html_entity_decode}"
                        title="{$messages[m].author}: {$messages[m].content|strip_tags|htmlspecialchars|html_entity_decode}"
                        id="thumb_pic_{$messages[m].messageID}" src="/attaches/{$messages[m].themeID}/{$messages[m].messageID}/thumb_{$messages[m].first_file.filename}"
                        width="110" height="110">
                </a>
                {if $_system_user.is_admin}
                    {if $messages[m].hav_file eq 1}
                        <div class="thumb_img_hide"><a id="thumb_{$messages[m].messageID}" href="javascript:void(0)" onclick="HideFile({$messages[m].first_file.id},{$messages[m].hav_file})"><img src="/images/del_pic.gif"></a></div>
                    {/if}
                {/if}
                </div>

            </div>
            {/if}
        {/if}

        {if $messages[m].count_files > 1}
            {literal}
            <script type="text/javascript">
                mIdSmall[imSmall]={/literal}{$messages[m].messageID}{literal};
                imSmall++;
            </script>
            {/literal}
            {foreach from=$messages[m].files item=file}
                {if $file.id>0}
                <div class="small_img" id="small_div_{$file.id}">
                <a href="/attaches/{$messages[m].themeID}/{$messages[m].messageID}/{$file.filename}" class="zoomsmall_{$messages[m].messageID}"
                onmouseover="document.getElementById('thumb_pic_{$messages[m].messageID}').src = '/attaches/{$messages[m].themeID}/{$messages[m].messageID}/thumb_{$file.filename}';document.getElementById('zoom_{$messages[m].messageID}').href = '/attaches/{$messages[m].themeID}/{$messages[m].messageID}/{$file.filename}'"
                {* document.getElementById('thumb_pic_{$messages[m].messageID}').title='{$file.id}'; *}
                >
                <img
                        alt="{$messages[m].author}: {$messages[m].content|strip_tags|htmlspecialchars|html_entity_decode}"
                        title="{$messages[m].author}: {$messages[m].content|strip_tags|htmlspecialchars|html_entity_decode}"
                        src="/attaches/{$messages[m].themeID}/{$messages[m].messageID}/thumb_{$file.filename}" width="32" height="32">
                </a>
                {if $_system_user.is_admin || $isGroupAdministrator}
                    <div class="small_img_hide"><a id="small_{$messages[m].messageID}" href="javascript:void(0)" onclick="HideFile({$file.id},{$messages[m].hav_file})"><img src="/images/del_pic.gif"></a></div>
                {/if}
                </div>
                {/if}
            {/foreach}
        {/if}

        {if $messages[m].hav_file > 0}
            </td>
    	{/if}
    	<td width="100%">
            <div id="message_{$messages[m].messageID}" class="text_box_2_mess">
                {if $messages[m].strlen > $smarty.const.STRLEN_HIDE}
                    {$messages[m].content|truncate:1000|bbcode|nl2br}
                    <br><a class='more' href="javascript:void(0);" onclick="showMore({$messages[m].messageID})">подробнее...</a>
                {else}
                    {if $messages[m].linelen > $smarty.const.LINE_HIDE}
                        {$messages[m].shortline_content|bbcode|nl2br}
                        <br><a class='more' href="javascript:void(0);" onclick="showMore({$messages[m].messageID})">подробнее...</a>
                    {else}
                        {$messages[m].content|bbcode|nl2br}
                    {/if}
                {/if}
            </div>
            <div id="hide_message_{$messages[m].messageID}" class="text_box_2_mess_hide">
                {$messages[m].content|bbcode|nl2br}
            </div>
        </td>
    </tr>
    </table>
        <div class="message_bottom">
            <a href="javascript:void(0)" onclick="reply('{$messages[m].author} ({$messages[m].created|date_format:"%e.%m.%Y (%H:%M)"})')" class="answer"><img src="/images/v_1_1/ans_w.gif" alt="Ответить" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/ans_y.gif'" onmouseout="this.src = '/images/v_1_1/ans_w.gif'"></a>
            <a href="javascript:void(0)" onclick="citata('message_{$messages[m].messageID}');reply('{$messages[m].author} ({$messages[m].created|date_format:"%e.%m.%Y (%H:%M)"})')" class="answer"><img src="/images/v_1_1/cit_w.gif" alt="Цитировать" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/cit_y.gif'" onmouseout="this.src = '/images/v_1_1/cit_w.gif'"></a>
            <a href="?p={$smarty.get.p}#mess{$messages[m].messageID}" class="answer"><img src="/images/link_w.gif" alt="Ссылка на это сообщение" title="Ссылка на это сообщение" border="0" class="to_head" onmouseover="this.src = '/images/link_y.gif'" onmouseout="this.src = '/images/link_w.gif'"></a>
            <a href="#ftop" class="answer_up"><img src="/images/v_1_1/u_w.gif" alt="Вверх" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/u_y.gif'" onmouseout="this.src = '/images/v_1_1/u_w.gif'"></a>
        </div>
        <input type=hidden value=0 id="url_message_{$messages[m].messageID}">
        <br>
        </div>
    {/if}

	{/section}
{/if}
{literal}
<script type="text/javascript">
    $(document).ready(function() {
        $('.ratinglog').click(function(e){
            $('#status').html(e.pageX +', '+ e.pageY);
            if(e.pageY-document.documentElement.scrollTop > $(window).height()-200){
                showRatingLog(this.id,e.pageX+10,e.pageY-160);
            } else {
                showRatingLog(this.id,e.pageX+10,e.pageY+5);
            }
        });
        replaceSmiles({/literal}{$__countperpage}{literal});
        for(var i = 0; i < mId.length; i++){
            $('.zoomin_'+mId[i]).lightBox();
        }
        for(var i = 0; i < mIdSmall.length; i++){
            $('.zoomsmall_'+mIdSmall[i]).lightBox();
        }

    });

</script>
{/literal}
