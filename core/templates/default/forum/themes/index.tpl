{include file="forum/header.tpl"}
<div class="navigation">
{if $isGroupAdministrator}
<div style="float:right;padding-top:5px;"><a href="/forum/{$current_group.groupID}/settings/"><img src="/images/process.png" alt="Настройки" width="24" height="24"></a></div>
{/if}
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/">Example Forum</A> &nbsp;/&nbsp; <a href="">{$title_part|strip_tags|htmlspecialchars|html_entity_decode} {if $current_group.is_mat}{/if}</a>
    </div>
</div>
<div class="line1"></div>
{* {if ($current_group.groupID >0) && ($current_group.commerce ne '1')}<div class="zapret">В данной группе <span>ЗАПРЕЩЕНО</span> создание коммерческих тем</div>{else}<div class="zapret">Данная группа является <span>Коммерческой</span><br>При размещении тем, нарушающих Правила форумов, темы удаляются без возврата денежных средств.</div>{/if} *}
{if $current_group.is_mat eq 1 && $_system_user.userID >0 && $_system_user.danger_level < $smarty.const.USER_MAT_LEVEL}
    <h1>К сожалению, Каша карма очень снизилась и Вы не можете писать даже в матоязычных темах.</h1><br /><br /><br /><div class="clear"></div>
{elseif $current_group.is_mat neq 1 && $_system_user.userID >0 && $_system_user.danger_level < $smarty.const.USER_LEVEL}
    <h1>К сожалению, Ваша карма снизилась. Вы не можете писать в данной теме.</h1><br /><br /><div class="clear"></div>
{else}{include file=$addthemestpl}{/if}
{include file="forum/banner/middle.tpl"}
{include file="forum/paging.tpl" __up=1}
{if $_system_user.is_admin || $isGroupAdministrator}
    {literal}
        <script type="text/javascript">
            function HideTheme(tId) {
                $('#'+tId).hide();
                $.get("?", { event: "forumhideajaxtheme", _thId: tId });
            }
        </script>
    {/literal}
{/if}
{literal}
    <script type="text/javascript">
        function TopTheme(tId) {
            $.getJSON("?event=forumtopdaytheme&_thId="+tId,
                function (response){
                    if (response.errors) {
                        $('#err_top_form'+tId).html('Ошибка.'+response.errors.system);
                    } else {
                        $('#tdw1'+tId).attr('class', 'tdw1_top');
                        $('#tdw2'+tId).attr('class', 'tdw2_top');
                        $('#tdw3'+tId).attr('class', 'tdw3_top');
                        $('#spec_top_form'+tId).toggle(500);
                    }
                });
        }
        function Top30Theme(tId) {
            $.getJSON("?event=forumtop30daytheme&_thId="+tId,
                    function (response){
                        if (response.errors) {
                            $('#err_top_form'+tId).html('Ошибка.'+response.errors.system);
                        } else {
                            $('#tdw1'+tId).attr('class', 'tdw1_top');
                            $('#tdw2'+tId).attr('class', 'tdw2_top');
                            $('#tdw3'+tId).attr('class', 'tdw3_top');
                            $('#spec_top_form'+tId).toggle(500);
                        }
                    });
        }
        function ShowTopTheme(tId) {
            $('#spec_top_form'+tId).css('left',$('#top'+tId).offset().left+'px');
            $('#spec_top_form'+tId).css('top',$('#top'+tId).offset().top+'px');
            $('#err_top_form'+tId).html('');
            $('#spec_top_form'+tId).toggle(500);
        }
    </script>
{/literal}

    <div id="content">
        <table class="themes2">
		{section name="t" loop="$themes"}
		    <tr class="text_box_0" id="{$themes[t].themeID}">

            {if $themes[t].is_top || $themes[t].hottop}
                <td class="tdw1_top">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
                {if $_system_user.is_admin || $isGroupAdministrator || $themes[t].authorID==$_system_user.userID}
                    <td class="tdw3_top" colspan="2">
                        {else}
                    <td class="tdw3_top" colspan="3">
                {/if}
                <a href="/forum/{$current_group.groupID}/{$themes[t].themeID}/">{$themes[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a>&nbsp;&nbsp;<span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;{$themes[t].updated_by}]</span></td>
                {if $_system_user.is_admin || $isGroupAdministrator}
                    <td width="10">
                        <a href="/themesettings/{$themes[t].groupID}/{$themes[t].themeID}/"><img src="/images/process.png" alt="Настройки" width="12" height="12"></a>
                    </td>
                    <td width="10">
                        <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                    </td>
                {elseif $themes[t].authorID==$_system_user.userID}
                    <td width="10" class="tdw3_top">
                        <a href="/themesettings/{$themes[t].groupID}/{$themes[t].themeID}/"><img src="/images/process.png" alt="Настройки" width="12" height="12"></a>
                    </td>
                {/if}
                <td class="tdw2_top">
                    {if $themes[t].authorID >0  && $themes[t].author eq $themes[t].realname}
                        {if $is_mobile eq 'mobile'}
                            <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author|truncate:9}<sup title="Карма">{$themes[t].karma}</sup></a>
                        {else}
                            <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
                        {/if}
                    {else}
                        {if $is_mobile eq 'mobile'}
                            <span class="last_user">{$themes[t].author|truncate:9}</span>
                        {else}
                            <span class="last_user">{$themes[t].author}</span>
                        {/if}
                    {/if}

                </td>

            {else}
                <td class="tdw1" id="tdw1{$themes[t].themeID}">{$themes[t].fact_date|date_format:"%H:%M %e/%m"}</td>
                {if $_system_user.is_admin || $isGroupAdministrator}
                    <td class="tdw3" colspan="2" id="tdw3{$themes[t].themeID}">
                {elseif $themes[t].authorID==$_system_user.userID}
                    <td class="tdw3" id="tdw3{$themes[t].themeID}">
                {else}
                    <td class="tdw3" colspan="3" id="tdw3{$themes[t].themeID}">
                {/if}
                {assign var=thid  value=$themes[t].themeID}
                {php}
                    if ($this->_tpl_vars['_fav'][$this->_tpl_vars['thid']]['userID']){
                        echo "<sup><img src='/images/v_1_1/fav_y.gif' width=7></sup> ";
                    }
                {/php}
                <a href="/forum/{$current_group.groupID}/{$themes[t].themeID}/">{$themes[t].caption|strip_tags|htmlspecialchars|html_entity_decode}</a>
                &nbsp;<span class="last_user">[<span class="title">{if $themes[t].messages > $themes[t].views}{$themes[t].messages}{else}{$themes[t].views}{/if}</span>/<span class="title">{$themes[t].messages}</span>&nbsp;-&nbsp;{$themes[t].updated_by}]</span>
                <a href="/forum/{$themes[t].groupID}/{$themes[t].themeID}/?p={math equation="ceil((x)/y)" x = $themes[t].messages y = $_per_page}#bottom" title="В начало">
                <img src="/images/first_1.gif" alt="В начало" border="0" class="to_head" onmouseover="this.src = '/images/first_2.gif'" onmouseout="this.src = '/images/first_1.gif'">
                </a>
                </td>

                {if $_system_user.is_admin || $isGroupAdministrator}
                    <td width="10" id="top{$themes[t].themeID}">
                        <a href="/themesettings/{$themes[t].groupID}/{$themes[t].themeID}/"><img src="/images/process.png" alt="Настройки" width="12" height="12"></a>
                    </td>
                    <td width="10">
                        <a href="javascript:void(0)" onclick="HideTheme({$themes[t].themeID})"><img src="/images/del_post.gif" alt="Скрыть" title="Скрыть"></a>
                    </td>
                {elseif $themes[t].authorID==$_system_user.userID}
                    <td width="10" id="top{$themes[t].themeID}">
                        <a href="/themesettings/{$themes[t].groupID}/{$themes[t].themeID}/"><img src="/images/process.png" alt="Настройки" width="12" height="12"></a>
                    </td>
                    <td width="10" id="top{$themes[t].themeID}">
                        <a href="javascript:void(0)" onclick="ShowTopTheme({$themes[t].themeID})"><img src="/images/arrow_up.png" alt="Закрепить в топе" title="Закрепить в топе"  width="12" height="12"></a>
                        <div id='spec_top_form{$themes[t].themeID}' class="box_fls"><br />
                            <div class="error" id='err_top_form{$themes[t].themeID}'></div>
                            <table width="380">
                                <tr>
                                    <td width="100%"><a href="javascript:void(0)" onclick="TopTheme({$themes[t].themeID})">
                                            Закрепить тему в ТОПе раздела на сутки
                                            <a></td>
                                    <td nowrap>({$smarty.const.TOP_DAY_PRICE}&nbsp;руб)</td>

                                </tr>
                                <tr>
                                    <td width="100%"><a href="javascript:void(0)" onclick="Top30Theme({$themes[t].themeID})">
                                            Закрепить тему в Горячем на сутки:&nbsp;</a></td>
                                    <td nowrap>({$smarty.const.TOP30_DAY_PRICE}&nbsp;руб)</td>
                                </tr>
                            </table>
                            <br style="clear:both" />
                            <div class="box_barr" style="float:right;margin-top:2px">
                                <a href="javascript:;;" onclick="ShowTopTheme({$themes[t].themeID})">Закрыть</a>
                            </div>
                        </div>
                    </td>
                {/if}

                <td class="tdw2" id="tdw2{$themes[t].themeID}">
                {if $themes[t].authorID >0  && $themes[t].author eq $themes[t].realname}
                    {if $is_mobile eq 'mobile'}
                        <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author|truncate:9}<sup title="Карма">{$themes[t].karma}</sup></a>
                    {else}
                        <a href="javascript:void(0)" onclick="LoadPassport({$themes[t].authorID});">{$themes[t].author}<sup title="Карма">{$themes[t].karma}</sup></a>
                    {/if}
                {else}
                    {if $is_mobile eq 'mobile'}
                        <span class="last_user">{$themes[t].author|truncate:9}</span>
                    {else}
                        <span class="last_user">{$themes[t].author}</span>
                    {/if}
                {/if}
                </td>
            {/if}
		    </tr>
		{/section}
        </table>
    </div>

{literal}
<script class="example" type="text/javascript">
$(document).ready(function()
{
   $('#content a[href][title]').qtip({
      content: {
         text: false
      },
      style: 'blue' 
   });
});
</script>
{/literal}
{include file="forum/paging.tpl"}
{include file="forum/footer.tpl"}