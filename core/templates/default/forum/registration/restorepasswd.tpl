{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Восстановление пароля
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>
<div class="box_text" >
	<p>
		{foreach item=error from=$_err}
			<h2 style="color:#F00;text-align: center;">{$error}</h2>
		{/foreach}
		{foreach item=error from=$_msg}
			<h2 style="color:#000;text-align: center;">{$error}</h2>
		{/foreach}
	</p>
	<br/>
	{if $_frm ne 1}

<div
    <div class="clear"></div>
    <h1>Восстановление пароля</h1>
    <div class="restore_passw_box_text">Вы можете восстановить свой логин/пароль по e-mail-у на который осуществлялась регистрация.</div>
    <form method="get" action="" id="login_form">
		<label for="username" class="restore_passw">Ваш e-mail:</label>
        <input name="email" type="text" id="restorePasswD1" class="restore_passw_inp" value="" maxlength="30" /><br/>
		<input type="button" id="submit_restore_passw" value="Восстановить" onclick='restorePassAction()'/>
    </form>
    <div class="clear"></div>
    <div class="restore_passw_box_text" id="restore_passw_result"></div>

{/if}
</div>
<A name=bot></A>

{include file="forum/footer.tpl"}
