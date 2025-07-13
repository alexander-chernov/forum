{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/">Пополнение баланса</a>&nbsp;/&nbsp; Webmoney
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Пополнение баланса пользователя {$_system_user.user_name} НЕВЫПОЛНЕНО :(</h3>
	
{/if}		
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}