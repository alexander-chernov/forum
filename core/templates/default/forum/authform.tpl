<div id="login_box"><div id="login_box_1">
	<img src="/images/btn_close.gif" class="close_btn" alt="Закрыть">
    <div class="clear"></div>
    <h1>Авторизация</h1>
	<div id="login_result"></div>
	<form method="post" action="" id="login_form">
	<input type="hidden" name="event" value="cmsauthuserbyform">
		<label for="username" class="auth_login">Логин:</label><input name="user_name" type="text" id="username" class="auth_login_inp" value="" maxlength="20" /><br/>
		<label for="password" class="auth_passw">Пароль:</label><input name="user_password" type="password" id="password" class="auth_login_inp" value="" maxlength="20" /><br/>
        <label for="imageString" class="auth_passw">Код:</label><input type="text" name="imageString" class="auth_login_inp" style="width:53px;vertical-align: top;height:20px"  maxlength="5" id="imageString"><img id='randomImage' src="/antibot.php?invert&{php} echo time();{/php}" width="190" height="30"><br/>
        <a href="#" onClick="LoadRestorePassw()" class="conf_passw">Забыли пароль?</a><br>
		<input name="Submit" type="submit" id="submit_auth" value="вход" />
	</form>
</div></div>