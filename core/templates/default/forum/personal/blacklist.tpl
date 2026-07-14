{include file="forum/header.tpl" title="Черный список"}
{literal}
<script>
function deleteBlackUser(userId) {
	$.post('/personal/blacklist/',{action:'removeuser',dataType:'json',userId:userId},function(result){
		if (result.success  == 1) {
			document.location='/personal/blacklist/';
		}
	},'json');
}
$(document).ready(function(){
	function findValue(li) {
		if( li == null ) return alert("No match!");

		if( !!li.extra ) var sValue = li.extra[0];

		else var sValue = li.selectValue;

	}

	function selectItem(li) {
		findValue(li);
	}

	function formatItem(row) {
		return row[0];
	}
		
$( ".autocomplete" ).autocomplete(
		"/personal/blacklist/",
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
});
</script>
{/literal}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; Черный список
    </div>
</div>
<div class="box_pasport">
<div style="color:#000;padding:10px;">
<h3>Сообщения от пользователей с этого списка не будут видны Вам в темах.</h3><br />
<br /><br />
<h3>Настройки приватности</h3>
<form method="POST" action="">
<input type="hidden" name="action" value="savePrivateSettings">
<table>
<tr style="color:#000;padding:10px;">
<td style="color:#000;padding:10px;">Скрывать сообщения анонимных пользователей</td><td style="color:#000;padding:10px;"><input type="radio" name="hideAnonymous" value="1" {if $blackListSettings.hideAnonymous eq 1}checked{/if}>&nbsp;Да&nbsp;&nbsp;<input type="radio" name="hideAnonymous" value="0" {if $blackListSettings.hideAnonymous neq 1}checked{/if}>&nbsp;нет</td>
</tr>
<tr style="color:#000;padding:10px;">
<td style="color:#000;padding:10px;">Скрывать сообщения пользователей из списка</td><td style="color:#000;padding:10px;"><input type="radio" name="hideUsers" value="1" {if $blackListSettings.hideUsers eq 1}checked{/if}>&nbsp;Да&nbsp;&nbsp;<input type="radio" name="hideUsers" value="0" {if $blackListSettings.hideUsers neq 1}checked{/if}>&nbsp;нет</td>
</tr>
</table>
<div style="text-align:center"><input type="submit" value="Сохранить настройки" /></div>
</form>
<br />
<form method="POST" action="">
<input type="hidden" name="action" value="addBlackUser">
<b style='color:#000;'>Добавить пользователя:</b><br />
<div style="border:1px solid black;height:20px;width:170px;float:left;"><input type="text" id='blackuser_field' name="user" class='autocomplete' style='width:167px;'></div><input type="submit" value="Добавить">
</form>
<br />
<h3>Сообщения от этих пользователей не будут Вам показаны.</h3>
<div id='blackusers' style='width:100%;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
{foreach from="$bannedUsers" item=moderator}
<p class="black"><a href="javascript:void(0);" onclick="deleteBlackUser('{$moderator.userEncode}');">удалить</a>&nbsp;&nbsp; {if $moderator.hidden neq 1}{$moderator.user_name}{else}Имя пользователя скрыто{/if}</p>
{/foreach}
</div>	
</div>
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}