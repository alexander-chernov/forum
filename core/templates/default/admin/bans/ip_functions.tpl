{if $up eq 1}
	{literal}
	<script type="text/javascript">
	function ip_search(up) {
		var ip = document.getElementById('ip_search' + up).value;
		var flag = verifyIP(ip);
		
		if (!flag) {
			alert('Введён некорректный IP-адрес');
		}
		else {
			location.href = '/.admin/bans/ip/index/search/' + ip;
		}
	}

	function verifyIP(IPvalue) {
		IPvalue = IPvalue.replace(/^\s{1,}/, '');
		IPvalue = IPvalue.replace(/\s{1,}$/, '');
		errorString = "";
		theName = "IPaddress";
		
		var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
		var ipArray = IPvalue.match(ipPattern);

		if (IPvalue == "0.0.0.0")
			errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
		else if (IPvalue == "255.255.255.255")
			errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
		if (ipArray == null)
			errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
		else {
			for (i = 0; i < 4; i++) {
				thisSegment = ipArray[i];
				if (thisSegment > 255) {
					errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
					i = 4;
				}
				if ((i == 0) && (thisSegment > 255)) {
					errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
					i = 4;
				}
			}
		}
		
		extensionLength = 3;
		
		if (errorString == "") return true;
		else return false;
	}
	</script>
	{/literal}
{/if}

<div align="left">
	{if $_params.confirm eq 1}
		<input type="button" value="Снять баны" onclick="deleteConfirm('bans', 'forumunbanip', 'удалить')" />
		<input type="button" value="Подтвердить баны" onclick="deleteConfirm('bans', 'forumbanconfirmaddr', 'изменить')" />
		<select name="move{$up}">
			<option value="11" selected="selected">Только-чтение</option>
			<option value="10">Необх. авторизация</option>
			<option value="1">Полный бан</option>
		</select>
		<input type="button" value="Сменить тип" onclick="deleteConfirm('bans', 'forumbanchangetype', 'изменить', {$up})" />
	{/if}
</div>	

<div align="right" style="padding-top:5px">
Фильтры по типу: 
<a href="/.admin/bans/ip/index">Показать все</a> |
<a href="/.admin/bans/ip/index/type/11">Только чтение</a> |
<a href="/.admin/bans/ip/index/type/10">Необх. авторизация</a> |
<a href="/.admin/bans/ip/index/type/1">Полный бан</a>
</div>

<div align="right" style="padding-top:5px">
<input type="text" id="ip_search{$up}" value="{if isset($_search_ip)}{$_search_ip}{/if}" />
<input type="button" value="Поиск" onclick="ip_search({$up})" />
</div>