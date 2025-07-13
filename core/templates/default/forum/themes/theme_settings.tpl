{include file="forum/header.tpl"}
{literal}

<script>
$(function() {

	function findValue(li) {
		if( li == null ) return alert("No match!");

		// if coming from an AJAX call, let's use the CityId as the value
		if( !!li.extra ) var sValue = li.extra[0];

		// otherwise, let's just display the value in the text box
		else var sValue = li.selectValue;

		//alert("The value you selected was: " + sValue);
	}

	function selectItem(li) {
		findValue(li);
	}

	function formatItem(row) {
		return row[0];
	}
		
	$( ".autocomplete" ).autocomplete(
		"{/literal}/themesettings/{$group_info.groupID}/{$theme_info.themeID}/{literal}",
		{
			delay:10,
			minChars:1,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:selectItem,
			onFindValue:findValue,
			formatItem:formatItem,
			autoFill:true
		});
})

function deleteUser(userId,userList) {
	$.post('{/literal}/themesettings/{$group_info.groupID}/{$theme_info.themeID}/{literal}',{action:'removeuser',dataType:'json',userId:userId,list:userList},function(result){
		if (result.success  == 1) {
			document.location='{/literal}/themesettings/{$group_info.groupID}/{$theme_info.themeID}/{literal}';
		}
	},'json');
}
</script>
{/literal}
<div class="navigation">
	<div class="box_path">
		<a href="/forum/">Томские форумы</a> &nbsp;/&nbsp; <a href="/forum/{$group_info.groupID}/">{$group_info.caption}</a>  &nbsp;/&nbsp; <a href="/forum/{$group_info.groupID}/{$theme_info.themeID}/">{$theme_info.caption}</a> &nbsp;/&nbsp; Настройки доступа темы.
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>
<div class="box_text">

<div style="text-align:left;float:left;">{include file="forum/errors.tpl"}</div>
<div class="clear" style="height:10px;"></div>
{if $isGroupAdministrator}
<div align="center""><b style='color:green;'>Вы являетесь модератором раздела</b></div>
{/if}
{if $isGroupOwner}
<div align="center""><b style='color:green;'>Вы являетесь Владельцем раздела</b></div>
{/if}
{if $isThemeOwner}
<div align="center""><b style='color:green;'>Вы являетесь Создателем темы</b></div>
{/if}
<h2>Редактирование темы</h2>
<br />
<div>
<form action="/themesettings/{$group_info.groupID}/{$theme_info.themeID}/" method="POST">
<input type="hidden" name="action" value="editTheme">
<div class="form_box_name">
<label for="name" class="l_inp_text_name" style='color:black;'>автор темы</label>
<input class="inp_text_name" id="name" tabindex="1" maxlength="20" name="theme[author]" value="{$theme_info.author}" type="text">
<label for="heading" class="l_inp_text_name" style='color:black;'>Заголовок темы:</label>
<input class="inp_text_name" id="heading" tabindex="2" maxlength="200" name="theme[caption]" value="{$theme_info.caption}" type="text">
</div>
<div class="clear"></div>
{if $isGroupOwner || $isGroupAdministrator}
<p style='color:#000;'>
<label><input type="radio" name="theme[hidden]" value="0" {if $theme_info.hidden eq 0}checked{/if}>&nbsp;Открытая</label>&nbsp;&nbsp;<label><input type="radio" name="theme[hidden]" value="1" {if $theme_info.hidden eq 1}checked{/if}>&nbsp;Скрытая</label>
</p>
<br />
{/if}
<input type="submit" value="Принять изменения (Стоимость - {$smarty.const.EDIT_THEME_COST}р.)">
</form>
</div>
<div class="clear" style="height:30px;"></div>
<h2>Настройки доступа</h2><br />
<form action="/themesettings/{$group_info.groupID}/{$theme_info.themeID}/" method="POST">
<input type="hidden" name="action" value="editThemeRights">
{*<h3>Читать:</h3>*}
<p style='color:#000;'>
    <table>
    <tr>
        <td><label><input type="radio" name="theme[optRead]" value="0" {if $theme_info.optRead eq 0 || $smarty.post.theme.optRead eq 0}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить всем (список не учитывается)</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optRead]" value="1" {if $theme_info.optRead eq 1 || $smarty.post.theme.optRead eq 1}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить читать/писать только зарегистрированным пользователям, анонимам доступ запрещен, список не учитывается</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optRead]" value="2" {if $theme_info.optRead eq 2 || $smarty.post.theme.optRead eq 2}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить (читать/писать) пользователям из списка, остальным доступ запрещен</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optRead]" value="3" {if $theme_info.optRead eq 3 || $smarty.post.theme.optRead eq 3}checked{/if}></td><td nowrap="" style='color:#000;'>запретить (читать/писать) пользователям из списка, остальным можно читать и писать</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optRead]" value="4" {if $theme_info.optRead eq 4 || $smarty.post.theme.optRead eq 4}checked{/if}></td><td nowrap="" style='color:#000;'>читать всем, независимо от списка; список определяет права отдельных пользователей</label></td>
    </tr>
    </table>
</p>
{*
<h3>Писать</h3>
<p style='color:#000;'>
    <table style='color:#000;'>
    <tr>
        <td><label><input type="radio" name="theme[optWrite]" value="0" {if $theme_info.optWrite eq 0}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить всем</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optWrite]" value="1" {if $theme_info.optWrite eq 1}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить зарегистрированным пользователям (соответственно анонимам запрещено)</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optWrite]" value="2" {if $theme_info.optWrite eq 2}checked{/if}></td><td nowrap="" style='color:#000;'>разрешить выбранным пользователям (список ниже)</label></td>
    </tr><tr>
        <td><label><input type="radio" name="theme[optWrite]" value="3" {if $theme_info.optWrite eq 3}checked{/if}></td><td nowrap="" style='color:#000;'>запретить выбранным пользователям (список ниже)</label></td>
    </tr>
    </table>
    </p>
*}
    <input type="hidden" name="theme[optWrite]" value="0">
    <div align="center"><input type="submit" value="Сохранить"></div>
</form>
<hr />
<div class="clear"></div><br /><br />
<h3>Пользовательский доступ</h3>
<br />
<table style="width:100%;">
<tr>
<td style='color:#000;'>
<form action="/themesettings/{$group_info.groupID}/{$theme_info.themeID}/" method="POST">
<input type="hidden" name="action" value="addUser">
<b style='color:#000;'>Добавить пользователя:</b><br />
<input type="text" id='blackuser_field' name="user" class='autocomplete'>&nbsp;
    <label style='color:#000;'><input type="checkbox" name="optRead" value="1">Читать</label>&nbsp;
    <label style='color:#000;'><input type="checkbox" name="optWrite" value="1">Писать</label>&nbsp;
    <br /><br /><input type="submit" value="Добавить">
</form>
<br />
<div id='blacklist' style='width:450px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
<table style="color:#000;width:450px;">
<tr>
<td></td><td style="color:#000;"><b>Пользователь</b></td><td style="color:#000;"><b>Чтение</b></td><td style="color:#000;"><b>Запись</b></td>
</tr>
{foreach from="$themeAccess" item=user}
<tr>
<td><a href="javascript:void(0);" onclick="deleteUser({$user.userID});">удалить</a></td><td style="color:#000;">{$user.user_name} </td><td style="color:#000;">{if $user.optRead}да{else}нет{/if}</td><td style="color:#000;">{if $user.optWrite}да{else}нет{/if}</td>
</tr>
{/foreach}
</table>
</div>
</td>
</tr>
</table>
</div>
<A name=bot></A>
{include file="forum/footer.tpl"}
