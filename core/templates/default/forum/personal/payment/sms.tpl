{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/payment/">Пополнение баланса</a>&nbsp;/&nbsp; SMS
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Пополнение баланса пользователя {$_system_user.user_name}</h3>
	<a href="#" onclick="window.open('http://smszamok.ru/client/izamok.php?{*32400*}31255&text=303030&bg=D0D0D0', '_blank', 'location=no,height=700,width=700', false);">Инструкции по отправке SMS.</a><br><br>
	{if $request eq 1 && $result eq 1}
		<div style="text:#000;">Платеж успешно выполнен. Сумма к зачислению: 50 руб.</div>
	{/if}
	<form method="POST" action="">
	<input type="text" name="sms[code]" value="Введите полученый код">
	<input type="submit" value="Пополнить баланс">
	</form>
{/if}		
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}