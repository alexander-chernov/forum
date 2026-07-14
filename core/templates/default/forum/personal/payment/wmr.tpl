{include file="forum/header.tpl"}
{literal}
<script>
function getCRC()
{
	var invId = $('#invId').val();
	var shpItm = $('#shpItm').val();
	var summ = $('#summ').val();
	$('#crc_div').load('/personal/payment/robokassa/getcrc/?invId='+invId+'&shpItm='+shpItm+'&summ='+summ,function(response, status, xhr){
    	$('#crc').val(response);
    	$('#sendform').submit();
		});
	return true; 
}
</script>
{/literal}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/">Пополнение баланса</a>&nbsp;/&nbsp; 
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Пополнение баланса пользователя {$_system_user.user_name}</h3>
<div style="display:none;" id='crc_div'></div>
<form action='http://test.robokassa.ru/Index.aspx' method=POST id='sendform'>
<table align="center" width="380">
<tr>
	<td><b style="color:#000000;">Сумма платежа:</b></td>
	<td><input type="text" id='summ' name="OutSum" style="size:110px;" value="500"></td>
	<td><input type=button value='Пополнить' onclick="return getCRC();"></td>
</tr>
</table>
<input type=hidden name=MrchLogin value="{$paymentLogin}">
<input type=hidden id="invId" name=InvId value="{php}echo time();{/php}">
<input type=hidden name=Desc value='Пополнение баланса пользователя {$_system_user.user_name}'>
<input type=hidden id='crc' name=SignatureValue value="">
<input type=hidden id='shpItm' name=Shp_item value='{$_system_user.userID}'>
{* <input type=hidden name=IncCurrLabel value="PCR"> *}
<input type=hidden name=Culture value="ru">
</form>
</td>
</tr>
</table>

{/if}		
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}