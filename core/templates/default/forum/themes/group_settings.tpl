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
		"/forum/{/literal}{$group_info.groupID}{literal}/settings/",
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
function deleteModerator(userId) {
	$.post('/forum/{/literal}{$group_info.groupID}{literal}/settings/',{action:'removemoderator',dataType:'json',userId:userId},function(result){
		if (result.success  == 1) {
			document.location='/forum/{/literal}{$group_info.groupID}{literal}/settings/';
		}
	},'json');
}
function deleteUser(userId,userList) {
	$.post('/forum/{/literal}{$group_info.groupID}{literal}/settings/',{action:'removeuser',dataType:'json',userId:userId,list:userList},function(result){
		if (result.success  == 1) {
			document.location='/forum/{/literal}{$group_info.groupID}{literal}/settings/';
		}
	},'json');
}
</script>
{/literal}
<div class="navigation">
	<div class="box_path">
		<a href="/forum/">Example Forum</a> &nbsp;/&nbsp; <a href="/forum/{$group_info.groupID}/">{$group_info.caption}</a>  &nbsp;/&nbsp; Настройки группы.
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
<div align="center""><h3>Вы являетесь модератором раздела</h3></div>
{/if}
{if $isGroupOwner}
<div align="center""><h3>Вы являетесь Владельцем раздела</h3></div>
{/if}
<h3>Настройки раздела:</h3>
<form action="/forum/{$group_info.groupID}/settings/" method="POST">
<input type="hidden" name="action" value="saveSettings">
<p style='color:#000;'><label><input type="radio" name="is_mat" value="1" {if $group_info.is_mat eq 1}checked{/if}>&nbsp;да</label>&nbsp;&nbsp;<label><input type="radio" name="is_mat" value="0" {if $group_info.is_mat eq 0}checked{/if}>&nbsp;нет</label>&nbsp;&nbsp;&nbsp; Группа матоязычная</p>
<p style='color:#000;'><label><input type="radio" name="deny_guest" value="1" {if $group_info.deny_guest eq 1}checked{/if}>&nbsp;да</label>&nbsp;&nbsp;<label><input type="radio" name="deny_guest" value="0" {if $group_info.deny_guest eq 0}checked{/if}>&nbsp;нет</label>&nbsp;&nbsp;&nbsp; Группа закрыта от Анонимно</p>
<p style='color:#000;'><label><input type="radio" name="deny_user" value="1" {if $group_info.deny_user eq 1}checked{/if}>&nbsp;да</label>&nbsp;&nbsp;<label><input type="radio" name="deny_user" value="0" {if $group_info.deny_user eq 0}checked{/if}>&nbsp;нет</label>&nbsp;&nbsp;&nbsp; Группа закрыта от некоторых пользователей</p>
<p style='color:#000;'><label><input type="radio" name="deny_all" value="1" {if $group_info.deny_all eq 1}checked{/if}>&nbsp;да</label>&nbsp;&nbsp;<label><input type="radio" name="deny_all" value="0" {if $group_info.deny_all eq 0}checked{/if}>&nbsp;нет</label>&nbsp;&nbsp;&nbsp; Группа закрыта от всех, кроме некоторых пользователей</p>
<span style='color:red;'>ВНИМАНИЕ! Последний переключатель имеет больший приоритет. Установив его в положение ДА, Вы запрещаете доступ ВСЕМ пользователям, кроме пользователей, перечисленных в блоке "разрешенные пользователи". Положение остальных переключателей в данном случае не учитывается. </span>
<div align="center"><input type="submit" value="Сохранить"></div>
</form>
<hr />
<div class="clear"></div><br /><br />
<table style="width:100%;">
<col width="50%"></col><col width="50%"></col>
<tr>
<td style='color:#000;'><b>Модераторы</b>
{if $isGroupOwner}
<br />
<form method="POST" action="">
<input type="hidden" name="action" value="addmoderator">
<b style='color:#000;'>Добавить модератора:</b><br />
<input type="text" id='moderator_field' name="moderator" class='autocomplete'><br /><input type="submit" value="Добавить">
</form>
{/if}
<br />
<div id='moderators' style='width:300px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
{foreach from="$groupModerators" item=moderator}
<p class="black">{if $isGroupOwner}<a href="javascript:void(0);" onclick="deleteModerator({$moderator.userID});">удалить</a>{/if}&nbsp;&nbsp;{$moderator.user_name} (ID: {$moderator.userID})</p>
{/foreach}
</div>
</td><td style='color:#000;'><b>Блокированные пользователи</b>
<br />
<form method="POST" action="">
<input type="hidden" name="action" value="addBlackUser">
<b style='color:#000;'>Добавить пользователя:</b><br />
<input type="text" id='blackuser_field' name="user" class='autocomplete'><br /><input type="submit" value="Добавить">
</form>
<br />
<div id='blacklist' style='width:300px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
{foreach from="$blackList" item=user}
<p><a href="javascript:void(0);" onclick="deleteUser({$user.userID},'blacklist');">удалить</a>&nbsp;&nbsp;{$user.user_name} (ID: {$user.userID})</p>
{/foreach}
</div>
</td>
</tr>
<tr>
<td style='color:#000;'><br /><br /><b>Разрешенные пользователи</b>
<br />
<form method="POST" action="">
<input type="hidden" name="action" value="addWhiteUser">
<b style='color:#000;'>Добавить пользователя:</b><br />
<input type="text" id='blackuser_field' name="user" class='autocomplete'><br /><input type="submit" value="Добавить">
</form>
<br />
(если тема закрыта от всех кроме некоторых)<br />
<div id='blacklist' style='width:300px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
{foreach from="$whiteList" item=user}
<p><a href="javascript:void(0);" onclick="deleteUser({$user.userID},'whitelist');">удалить</a>&nbsp;&nbsp;{$user.user_name} (ID: {$user.userID})</p>
{/foreach}
</div>

</td>
<td style='color:#000;'><br /><br /><b>Запрещенные IP</b>
<br />
<form method="POST" action="">
<input type="hidden" name="action" value="addBlackIp">
<b style='color:#000;'>Добавить адрес:</b><br />
<input type="text" id='blackuser_field' name="ip"><br /><input type="submit" value="Добавить">
</form>
<br />
(например: 192.168.1.1 или 10.1.1.100)<br />
<div id='blacklist' style='width:300px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
{foreach from="$ipList" item=ip}
<p ><a href="javascript:void(0);" onclick="deleteUser('{$ip.ip}','ip');">удалить</a>&nbsp;&nbsp;{$ip.ip}</p>
{/foreach}
</div>
</td>
</tr>
</table>

</div>
<A name=bot></A>
{include file="forum/footer.tpl"}
