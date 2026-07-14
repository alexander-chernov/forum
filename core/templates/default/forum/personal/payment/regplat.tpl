{include file="forum/header.tpl"}
{literal}
<script type="text/javascript">
    function changeRub (myNum){
        if(parseInt(myNum)>0) {
            document.getElementById("amount_cop").value = parseInt(myNum)*100;
        } else {
            alert('Введите корректное знаение');
            document.getElementById("amount_rub").focus();
        }
    }

</script>
{/literal}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/">Пополнение баланса</a>&nbsp;/&nbsp; Regplat
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
<form action='https://oplata.regplat.ru/webpay/getinit' method=POST id='sendform'>
<table align="center" width="380">
<tr>
	<td><b style="color:#000000;">Сумма платежа</b></td>
	<td><input type="text" name="amount_rub" id="amount_rub" style="width:50px;" value="5" onkeyup="changeRub(this.value)"><b style="color:#000000;">руб.</b>
    <input type="hidden" name="amount" id="amount_cop" value="500"></td>
	<td><input type=submit value='Пополнить'></td>
</tr>
</table>
<input type=hidden name=id_merchant value="{$id_merchant}">
<input type=hidden name=id_order value="{$id_order}">
<input type=hidden name=desc value='{$converted_desc}'>
<input type=hidden name=successURL value="http://{$DOMAIN}/personal/payment/regplat/success/">
<input type=hidden name=cancelURL value="http://{$DOMAIN}/personal/payment/regplat/">
<input type=hidden name=failURL value="http://{$DOMAIN}/personal/payment/regplat/fail/">
</form>
</td>
</tr>
</table>

{/if}
	</div>

</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}