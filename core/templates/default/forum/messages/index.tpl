{include file="forum/header.tpl"}
{include file="forum/messages/favorite.tpl"}
<script type="text/javascript" src="/js/testbbcode.js"></script>
{literal}
<script type="text/javascript">
    var mId = [];
    var mIdSmall = [];
    var im = 0;
    var imSmall = 0;
    function ShowHideMess(messId) {
        if ($('#m_'+messId).is(':hidden')) {
            $('#m_'+messId).show();
        } else {
            $('#m_'+messId).hide();
        }
    }
    function PutInBlackList(messId,coin) {
        if (coin>0) {
            if (confirm('Функция платная - '+coin+'р.')) {
                $('#'+messId).hide();
                $('#m_'+messId).hide();
                $.get("?", { event: "forumaddblacklist", _messId: messId });
            }
        } else {
            $('#'+messId).hide();
            $('#m_'+messId).hide();
            $.get("?", { event: "forumaddblacklist", _messId: messId });
        }
    }
    function showMore(id){
        $('#message_'+id).html($('#hide_message_'+id).html());
        replaceSmilesId(id);
    }

</script>
{/literal}
{if $_system_user.is_admin || $isGroupAdministrator}
    {literal}
        <script type="text/javascript">
            function HideTheme(tId) {
                $('#messages').hide();
                $.get("?", { event: "forumhideajaxtheme", _thId: tId });
            }
            function HideMessage(messId) {
                $('#'+messId).hide();
                $('#m_'+messId).hide();
                $.get("?", { event: "forumhideajaxmessage", _msgId: messId });
            }
            function HideAllMessage(messId) {
                $('#'+messId).hide();
                $('#m_'+messId).hide();
                $.get("?", { event: "forumhideajaxmessageall", _msgId: messId });
            }
            function HideFile(fId,picCount) {
                if (picCount==1) {
                    $('#thumb_div_'+fId).hide();
                } else {
                    $('#small_div_'+fId).hide();
                }
                $.get("?", { event: "forumhideajaxpicture", _msgId: fId });
            }
        </script>
    {/literal}
{/if}
{if $_system_user.userID eq $current_theme.authorID}
    {literal}
    <script type="text/javascript">
        function DeleteMessage(messId) {
            $.getJSON("?event=forumdeletemessageinmytheme&_msgId="+messId,
                    function (response){
                        if (response.errors) {
                            $(document).find('.error').empty();
                            $('#err_'+messId+'_mess').html(response.errors.system);
                        } else {
                            $('#'+messId).hide();
                            $('#m_'+messId).hide();
                        }
                    });

            $.get();
        }
    </script>
    {/literal}
{/if}

<div class="navigation">
	{if $_logged_in eq 1}
		<div class="complaint">
			<a href="javascript:;" title="Добавить тему в избранное" onclick="LoadAddFavorites({$current_theme.themeID});">
				<img src="/images/v_1_1/fav_b.gif" alt="Добавить тему в избранное" title="Добавить тему в избранное" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/fav_y.gif'" onmouseout="this.src = '/images/v_1_1/fav_b.gif'">
			</a>
		</div>
	{/if}
	<div class="box_path">
        {if $is_mobile eq 'mobile'}
            :: <a href="/forum/">Томские форумы</a> &nbsp;/&nbsp;<a href="/forum/{$current_group.groupID}/{$current_theme.themeID}/">{$current_theme.caption|strip_tags|htmlspecialchars|html_entity_decode|truncate:50}</a>
        {else}
            :: <a href="/forum/">Томские форумы</a> &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/">{$current_group.caption|strip_tags|htmlspecialchars|html_entity_decode}</a> &nbsp;/&nbsp; <a href="/forum/{$current_group.groupID}/{$current_theme.themeID}/">{$current_theme.caption|strip_tags|htmlspecialchars|html_entity_decode|truncate:50}</a>
        {/if}

		{if $_system_user.is_admin}
			&nbsp;<a href="/.admin/forum/themes/edit/{$current_theme.themeID}/" target="_blank">[ред.]</a>
			&nbsp;<a href="/.admin/forum/messages/index/{$current_theme.themeID}/" target="_blank">[в адм.]</a>
            &nbsp;<a href="javascript:void(0)" onclick="HideTheme({$current_theme.themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть" class="to_head"></a>


{*
            {if $current_theme.authorID}
				&nbsp;<a href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$current_theme.authorID}" target="_blank">[бан {$current_theme.realname}]</a>
			{/if}
*}
		{/if}
    </div>
</div>
<div class="line1"></div>
<div style="display:none">
</div>
{* {if ($curent_group.groupID >0) && (!$curent_group.commerce)}<div class="zapret">В данной группе <span>ЗАПРЕЩЕНО</span> создание коммерческих тем</div>{/if} *}
{if !$current_theme.is_locked}
    {if $current_group.is_mat eq 1 && $_system_user.userID >0 && $_system_user.danger_level < $smarty.const.USER_MAT_LEVEL}
        <h1>К сожалению, Каша карма очень снизилась и Вы не можете писать даже в матоязычных темах.</h1><br /><br /><br /><div class="clear"></div>
    {elseif $current_group.is_mat neq 1 && $_system_user.userID >0 && $_system_user.danger_level < $smarty.const.USER_LEVEL}
        <h1>К сожалению, Ваша карма снизилась. Вы не можете писать в данной теме.</h1><br /><br /><div class="clear"></div>
    {elseif $_system_user.userID == 0 && $GroupInfo.deny_guest eq 1}
        {include file="forum/banner/middle.tpl"}
    {else}
        {if $isGroupOpen}
            {if $userRights.access_type eq 0
                || ($userRights.access_type eq 1 && $_system_user.userID > 0)
                || ($userRights.access_type eq 2 && $userRights.optWrite eq 1)
                || ($userRights.access_type eq 3 && $userRights.optWrite neq 1)
                || ($userRights.access_type eq 4 && $userRights.optWrite eq 1)
                || $_system_user.is_admin
                || $isGroupAdministrator
                || $isThemeOwner} {* *}

                    {include file="forum/messages/create.tpl"}
                    {include file="forum/banner/middle.tpl"}
            {else}

                <table class="themes">
                    <tr><td><h1>Не хватает прав для возможности писать в данной теме</h1></td></tr>
                </table>

                <div class="clear"></div>
            {/if}
        {/if}
{*
        {if ($userRights.optWrite eq 1 || $_system_user.is_admin || $isGroupAdministrator || $isThemeOwner}
        	{include file="forum/messages/create.tpl"}
        	{include file="forum/banner/middle.tpl"}
        {else}
        <br />
        <h1>К сожалению, Вы не можете писать в данной теме.</h1><br /><br /><div class="clear"></div>
        {/if}
*}
    {/if}
{/if}
{*  1. разрешить всем (access_type eq 0)
    2. разрешить только зареганым (access_type eq 1)
    3. разрешить только из списка (access_type eq 2)
    4. запретить из списка (access_type eq 3)
    5. читать всем (access_type eq 4)
*}
{include file="forum/paging.tpl" __up=1}

{if $userRights.access_type eq 0 ||
    ($userRights.access_type eq 1 && $_system_user.userID > 0) ||
    ($userRights.access_type eq 2 && $userRights.optRead eq 1) ||
    ($userRights.access_type eq 3 && $userRights.optRead neq 1) ||
    ($userRights.access_type eq 4) ||
    $_system_user.is_admin ||
    $isGroupAdministrator ||
    $isThemeOwner}{* *}


	<div id='messages'>

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
                    {if $_system_user.is_admin eq 1 || $_system_user.isKGB eq 1}
                    {else}
                        <a href="javascript:void(0)" onclick="LoadComplaint({$messages[m].messageID}, '{$messages[m].author}')" title="Пожаловаться на сообщение.">
                            <img src="/images/v_1_1/block_b.gif" alt="Пожаловаться на сообщение" title="Пожаловаться на сообщение" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/block_y.gif'" onmouseout="this.src = '/images/v_1_1/block_b.gif'">
                        </a>
                    {/if}
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

                    {if $_system_user.is_admin || $_system_user.isKGB eq 1}
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
                <img src="/images/arr.gif"> <span class="white1" id='caption-{$messages[m].messageID}'>{$messages[m].caption|truncate}</span>
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
                    {*1={$messages[m].strlen}
                    <br>
                    2={$messages[m].linecount}
                    <br>
                    3={$messages[m].strlen2}
                    <br>
                    4={$messages[m].linecount2}
                    <br>
                    5={$messages[m].prevblock}
                    <br>
                    6={$messages[m].lastblock}
                    <br>
                    *}
                    {*
                    {if $messages[m].hav_re neq ''}
                        <div class="box_cite"><br><br></div>
                    {/if}
                    *}
                    {if $messages[m].hav_hide eq true}
                        {$messages[m].shortline_content|bbcode|nl2br}
                        <br><a class='more' href="javascript:void(0);" onclick="showMore({$messages[m].messageID})">показать...</a>
                    {else}
                        {$messages[m].content|bbcode|nl2br}
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
    </div>
{else}
    <table class="themes">
        <tr><td><h1>Не хватает прав для возможности читать данную тему. Чтоб получить такую возможность обратитесь к автору темы или модератору.</h1></td></tr>
    </table>
{/if}

    <div id=ratinglog style="position:absolute;display:none; left:0; top:0;"></div>
    {if count($messages) eq 0}

    {/if}
{literal}
<script type="text/javascript">
function updateTheme(repeat,c){
    if ($('.text_box_1')) {
        lastId = ($('.text_box_1').slice(0).attr('id'));
    } else {
        lastId = 1;
    }
    if (lastId >0) {
    } else {
        lastId=1;
    }
    if($(window).scrollTop()<=($('#mess_text').offset().top + $('#mess_text').height()+210))
    {
        var messages = $.ajax({
            url: '/forum/{/literal}{$current_group.groupID}/{$current_theme.themeID}/?ajx=1&c='+c+'&lastmsg={literal}'+lastId,
            success: function(data){
                $('#messages').prepend(data);
            }
        });
    }
    if (repeat == true) {
        setTimeout("updateTheme(1,0);",60000);
    }
}
function addMessage() {
    var currentDate = new Date();
    $('#heading').val('');
    $('#mess_text').val('');
    {/literal}
    {if $_system_user.userID>0}
        {literal}
        $('#web1').hide();
        $('#web').hide();
        {/literal}
    {else}
        {literal}
        $('#capthaString').val('');
        $('#web').html('<img id="randomImage" src="/antibot.php?'+(currentDate.getTime()*1000)+'" width="190" height="30">');
        {/literal}
    {/if}
    {literal}
    $('.error').html('');
    updateTheme(0,1);
}

$(document).ready(function() {
    $('.ratinglog').click(function(e){
        $('#status').html(e.pageX +', '+ e.pageY);
        if(e.pageY-document.documentElement.scrollTop > $(window).height()-200){
            showRatingLog(this.id,e.pageX+10,e.pageY-160);
        } else {
            showRatingLog(this.id,e.pageX+10,e.pageY+5);
        }
    });
    for(var i = 0; i < mId.length; i++){
        $('.zoomin_'+mId[i]).lightBox();
    }
    for(var i = 0; i < mIdSmall.length; i++){
        $('.zoomsmall_'+mIdSmall[i]).lightBox();
    }
    {/literal}
    {if $page eq 1}
        {literal}
            //setTimeout("updateTheme(1);",20000);
            updateTheme(1,0);
        {/literal}
    {/if}
    {literal}
    replaceSmiles({/literal}{$__countperpage}{literal});
});

</script>
{/literal}
{include file="forum/paging.tpl"}
{include file="forum/footer.tpl"}