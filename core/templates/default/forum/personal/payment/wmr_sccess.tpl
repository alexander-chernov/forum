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
	<h3>Пополнение баланса пользователя {$_system_user.user_name} Выполнено успешно! Зачисление произойдет в течение суток. Спасибо за понимание!</h3>
	
{/if}		
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}