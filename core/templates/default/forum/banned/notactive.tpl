{include file="forum/header.tpl"}

<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; Подтверждение пользователя
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>

<div class="box_text">
<p>
    {if $result_header neq ''}
        <h2 style="text-align: center;">{$result_header}.</h2>
    {else}
        <h2 style="text-align: center;">В форме авторизации Вы указали неверные данные или Ваш аккаунт не активирован.</h2>
        <div style="text-align: center;color: #000;">Если Вы недавно зарегистрировавшийся пользователь - На указанный Вами почтовый адрес должны прийти инструкции подтверждения аккаунта</div>
        <div style="text-align: center;color: #000;"><a href="/register/getconfirm" style="color: #000; text-decoration: underline;">Получить код подтверждения</a></div>
    {/if}
    <p></p>
    <div style="text-align: center;color: #000;">Если Вы уже зарегистрированный и подтвержденный пользователь и по каким то причинам не можете войти нажмите ссылку "Забыли пароль", чтоб на Ваш почтовый ящик пришел новый автоматически сгенерированный пароль</div>
    <div style="text-align: center;color: #000;"><a href="#" onClick="LoadRestorePassw()" class="conf_passw">Забыли пароль?</a><br></div>
</p>
</div>
<A name=bot></A>

{include file="forum/footer.tpl"}
