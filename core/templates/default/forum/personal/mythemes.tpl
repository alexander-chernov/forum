{include file="forum/header.tpl"}
<div class="navigation">
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/">Example Forum</A> &nbsp;/&nbsp; Мои темы
    </div>
</div>
<div class="line1"></div>
{include file="forum/paging.tpl"}

{if $_error neq ''}
	<h2 style="text-align: center; color: #FFFFFF;">{$_error}</h2>
{/if}
{if $_msg neq ''}
	<h2 style="text-align: center; color: #FF9900;">{$_msg}</h2>
{/if}

{literal}
<script type="text/javascript">
    function HideMyTheme(tId) {
        $.getJSON('/personal/hidetheme/'+tId,
                function (response){
                    if (response.errors) {
                        $(document).find('.error').empty();
                        $('#err_'+tId+'_theme').html(response.errors.balance);
                    } else {
                        $('#'+tId).hide();
                        var balance = response.balance;
                        $('#head_balance').html(balance);
                    }
                });

    }
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

<table class=themes>
{if count($mythemes) > 0}
{section name="t" loop="$mythemes"}
		<tr id="{$mythemes[t].themeID}">

			<td {if $mythemes[t].is_top || $mythemes[t].hottop} class="tdw1_top" {else} class="tdw1" {/if} id="tdw1{$mythemes[t].themeID}">{$mythemes[t].updated|date_format:"%H:%M %e/%m"}</td>
			<td {if $mythemes[t].is_top || $mythemes[t].hottop} class="tdw3_top" {else} class="tdw3" {/if} id="tdw3{$mythemes[t].themeID}">
				<a href="/forum/{$mythemes[t].groupID}/{$mythemes[t].themeID}/" style="color: {if $mythemes[t].hidden eq 1}#cccccc{else}#33CCFF{/if} !important;">{$mythemes[t].caption}</a>&nbsp;&nbsp;[{$mythemes[t].messages}&nbsp;-&nbsp;{$mythemes[t].updated_by}]
				<br/>
				{assign var="sId" value=$mythemes[t].themeID}
				{foreach item="services" from=$listServices[$sId] name="sErv"}
					{if $smarty.foreach.sErv.last}
						{$services.name}
					{else}
						{$services.name},
					{/if}
				{/foreach}
			</td>
            <td class="tdw3" width="10">
                <a href="/themesettings/{$mythemes[t].groupID}/{$mythemes[t].themeID}/"><img src="/images/process.png" alt="Настройки темы" title="Настройки темы" width="12" height="12"></a>
            </td>
            <td class="tdw3" width="10">
                <span class="error" id="err_{$mythemes[t].themeID}_theme"></span>
                <a href="javascript:void(0)" onclick="HideMyTheme({$mythemes[t].themeID})"><img src="/images/del_post.gif" alt="Удалить тему" title="Удалить тему"></a>
            </td>
            <td width="10" id="top{$mythemes[t].themeID}">
                <a href="javascript:void(0)" onclick="ShowTopTheme({$mythemes[t].themeID})"><img src="/images/arrow_up.png" alt="Закрепить в топе" title="Закрепить в топе"  width="12" height="12"></a>
                <div id='spec_top_form{$mythemes[t].themeID}' class="box_fls"><br />
                    <div class="error" id='err_top_form{$mythemes[t].themeID}'></div>
                    <table width="380">
                        <tr>
                            <td width="100%"><a href="javascript:void(0)" onclick="TopTheme({$mythemes[t].themeID})">
                                    Закрепить тему в ТОПе раздела на сутки
                                    <a></td>
                            <td nowrap>({$smarty.const.TOP_DAY_PRICE}&nbsp;руб)</td>

                        </tr>
                        <tr>
                            <td width="100%"><a href="javascript:void(0)" onclick="Top30Theme({$mythemes[t].themeID})">
                                    Закрепить тему в Горячем на сутки:&nbsp;</a></td>
                            <td nowrap>({$smarty.const.TOP30_DAY_PRICE}&nbsp;руб)</td>
                        </tr>
                    </table>
                    <br style="clear:both" />
                    <div class="box_barr" style="float:right;margin-top:2px">
                        <a href="javascript:;;" onclick="ShowTopTheme({$mythemes[t].themeID})">Закрыть</a>
                    </div>
                </div>
            </td>


{*
			{if $COMMERCIAL_ON}
				<td class="tdw3">
					{if $mythemes[t].enddate neq '0000-00-00 00:00:00' && $mythemes[t].enddate neq ''}
						<b>Истекает:</b><br/>
						{$mythemes[t].enddate|date_format:"%e/%m"}
					{/if}
				</td>
				<form method="post">
				<td class="tdw3">
					<input type="hidden" name="event" value="userlinkpackage">
					<input type="hidden" name="_objId" value="{$mythemes[t].themeID}">
					<select name="_packageId" style="border: solid 0px;">
						<option value="0">Выберите пакет</option>
						{foreach item="services" from=$listServices[0]}
							<option value="{$services.packageid}">{$services.name}</option>
						{/foreach}
					</select>
				</td>
				<td class="tdw2">
					<input type="submit" value="Прикрепить" class="btn_form" style="width: 80px;" />
				</td>
				</form>
			{/if}
*}			
		</tr>
{/section}
{else}
	<tr><td colspan="4">Вы не создали ни одной темы</td></tr>
{/if}
</table>
{include file="forum/paging.tpl"}

{include file="forum/footer.tpl"}