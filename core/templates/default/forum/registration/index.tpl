{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; Регистрация пользователя
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Регистрация пользователя</h3>
		Для регистрации на Форуме Вам необходимо заполнить форму. Поля, обязательные для заполнения, обозначены значком (*).
	
	<form action="" method="post" class="form_reg" name="registration">
	<input type="hidden" name="event" value="cmscreateuserbyform">
	<label for="login">* Имя пользователся (login){if $_auth_errors.user_name ne ''}<div class="error">{$_auth_errors.user_name}</div>{/if}</label>

	<input type="text" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="{$_request.user_name}">
	<div class="box_small_text">Если выбранное Вами имя уже зарегистрировано, Вы сможете просто ввести другое имя, при этом остальные заполненные поля будут сохранены.</div>
	<div class="line2"><div></div></div>
	<label for="password1">* Пароль {if $_auth_errors.user_password ne ''}<div class="error">{$_auth_errors.user_password}</div>{/if}</label>
	<input type="password" class="inp_text_reg" name="user[user_password]" id="password1" maxlength="100">
	<label for="password2">* Подтверждение пароля {if $_auth_errors.user_password ne ''}<div class="error">{$_auth_errors.user_password}</div>{/if}</label>
	<input type="password" class="inp_text_reg" name="user[user_password_confirm]" id="password2" maxlength="100">

	<div class="box_small_text">При наборе пароля допускаются любые буквы (как русские, так и латинские) и символы. Пароль регистрозависим (советуем перед набором глянуть на Caps Lock)</div>
	<div class="line2"><div></div></div>
	<label for="email">* Ваш e-mail {if $_auth_errors.user_email ne ''}<div class="error">{$_auth_errors.user_email}</div>{/if}</label>
	<input type="text" class="inp_text_reg" name="user[user_email]" id="email"  value="{$_request.user_email}">
	<div class="line2"><div></div></div>
	<label for="fio">Имя Фамилия Отчество</label>
	<input type="text" class="inp_text_reg" name="user[user_fio]" id="fio"  value="{$_request.user_fio}">

	<label>Ваш пол</label>
	<input type="radio" name="user[user_gender]" value="1" id="1" class="radioinput" {if $_request.user_gender eq 1}checked="checked"{/if}>
	<label for="1" class="radiolab">Не имеет значения</label><br>
	<input type="radio" name="user[user_gender]" value="2" id="2" class="radioinput" {if $_request.user_gender eq 2}checked="checked"{/if}>
	<label for="2" class="radiolab">Мужской</label><br>
	<input type="radio" name="user[user_gender]" value="3" id="3" class="radioinput" {if $_request.user_gender eq 3}checked="checked"{/if}>
	<label for="3" class="radiolab">Женский</label><br>
	<input type="radio" name="user[user_gender]" value="4" id="4" class="radioinput" {if $_request.user_gender eq 4}checked="checked"{/if}>
	<label for="4" class="radiolab">Средний</label><br>	</p>
	<label for="icq">Номер ICQ</label>
	<input type="text" class="inp_text_reg" name="user[user_icq]"  value="{$_request.user_icq}">

	<label for="addition">Дополнительно</label>
	<textarea name="user[description]" id="addition" class="area_text_reg">{$_request.description}</textarea>
	<div class="line2_wide"><div></div></div>
        <label for="rules_agreement" class="wide_line">
            <input type="checkbox" name="user[rules_agreement]" id="rules_agreement" class="radioinput" {if $_request.rules_agreement eq 'on'}checked="checked"{/if}>
            Я полностью прочитал(а) <a href="/pages/rules/" target="_blank">правила</a> и согласен(на) их соблюдать.
            {if $_auth_errors.rules_agreement ne ''}<div class="error">{$_auth_errors.rules_agreement}</div>{/if}
        </label>

        <div class="line2_wide"><div></div></div>


    <label for="fz152_agreement" class="wide_line">
        <input type="checkbox" name="user[fz152_agreement]" id="fz152_agreement" class="radioinput" {if $_request.fz152_agreement eq 'on'}checked="checked"{/if}>
        Я согласен(на) на обработку персональных данных
        {if $_auth_errors.fz152_agreement ne ''}<div class="error">{$_auth_errors.fz152_agreement}</div>{/if}
    </label>
    <div class="box_small_text_wide"><ol>
            <li>Настоящим в соответствии с Федеральным законом № 152-ФЗ «О персональных данных» от 27.07.2006 года Вы подтверждаете свое согласие на обработку Медиа Холдингом «Example Holding» персональных данных: сбор, систематизацию, накопление, хранение, уточнение (обновление, изменение), использование, блокирование, обезличивание, уничтожение. Мы, Медиа Холдинг "Example Holding", гарантируем конфиденциальность получаемой нами информации. Обработка персональных данных осуществляется в целях эффективного исполнения заказов, договоров и иных обязательств, принятых Медиа Холдингом «Example Holding» в качестве обязательных к исполнению перед Вами.</li>
            <li>Настоящее согласие распространяется на следующие Ваши персональные данные: фамилия, имя и отчество, адрес электронной почты, icq, платёжные реквизиты.</li>
            <li>Срок действия Вашего согласия является неограниченным, однако, Вы вправе в любой момент отозвать настоящее согласие, путём направления письменного уведомления на адрес: 000000, Российская Федерация, г. Пример, ул. Примерная, д. 1, в ООО «Example Company», с пометкой «отзыв согласия на обработку персональных данных». Обращаем Ваше внимание, что отзыв Вашего согласия на обработку персональных данных влечёт за собой удаление Вашей учётной записи с Интернет-сайта (http://forum.site), а также уничтожение записей, содержащих Ваши персональные данные, в системах обработки персональных данных Медиа Холдинга «Example Holding», что может сделать невозможным пользование Интернет-сервисами Медиа Холдинга «Example Holding».</li>
        </ol></div>

    <div class="line2_wide"><div></div></div>

	<div class="box_small_text_wide">Если вся информация верна - нажмите кнопку (достаточно одного раза):</div>

        <div class="form_box_btn">
            {if $captcha}
                {if $_auth_errors.imageString ne ''}<div class="error">{$_auth_errors.imageString}</div>{/if}
                <span class="form_box_btn_span"><b>Проверочный код:</b></span>
                <div class="format"><div id="web">
                        <img id='randomImage' src="/antibot.php?invert&{php} echo time();{/php}" width="190" height="30">
                    </div></div>
                <input type="text" name="user[imageString]" class="inp_text_name_span" style="width:70px;"  maxlength="7" >

            {/if}
        </div>

	
	<input type="submit" class="btn_reg" value="Зарегистрироваться">
	</form>
	{/if}
	</div>
</div>
{include file="forum/footer.tpl"}