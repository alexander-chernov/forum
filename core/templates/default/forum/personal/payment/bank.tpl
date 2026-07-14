{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/">Пополнение баланса</a>&nbsp;/&nbsp; Webmoney
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Пополнение баланса пользователя {$_system_user.user_name}</h3>
	<form id=pay name=pay method="POST" action="/personal/payment/bank/pay/">
<table align="center" width="380">
<tr>
	<td><b style="color:#000000;">Сумма платежа:</b></td>
	<td colspan=2><input type="text" name="LMI_PAYMENT_AMOUNT" style="size:110px;" value="500"></td>
</tr>
<tr>
	<td><b style="color:#000000;">ФИО плательщика:</b></td>
	<td colspan=2><input type="text" name="fio" style="size:110px;" value="500"></td>
</tr>
<tr>
	<td><b style="color:#000000;">Сумма платежа:</b></td>
	<td><input type="text" name="LMI_PAYMENT_AMOUNT" style="size:110px;" value="500"></td>
	<td><input type="submit" value="Пополнить" /></td>
</tr>
</table>
<input type="hidden" name="LMI_PAYMENT_DESC" value="Pay money to account {$_system_user.userID} in forum.site">
<input type="hidden" name="userid" value="{$_system_user.userID}">
<input type="hidden" name="LMI_PAYEE_PURSE" value="XXXXXXXXXXX">
<input type="hidden" name="LMI_PAYMENT_NO" value="1024">
<input type="hidden" name="LMI_MODE" value="1"> 
<input type="hidden" name="LMI_SIM_MODE" value="0">
<input type="hidden" name="RND" value="67512164">
</form>
												</td>
				</tr>
			</table>

{/if}		
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}