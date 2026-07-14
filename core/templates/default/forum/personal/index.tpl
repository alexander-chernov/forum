{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; Паспорт
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
	
	{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	{else}
	<h3>Паспорт пользователя</h3>
		Для регистрации на Форуме Вам необходимо заполнить форму. Поля, обязательные для заполнения, обозначены значком (*).

	{if $_error neq ''}
		<br />
		<h2 style="color: red">{$_error}</h2>
	{/if}

        {literal}
            <script type="text/javascript">
                function BuyKarma(uId) {
                    $.getJSON('/personal/buykarma',
                            function (response){
                                if (response.errors) {
                                    $(document).find('.error').empty();
                                    $('#err_karma').html(response.errors.karma);
                                } else {
                                    var karma = response.karma;
                                    var balance = response.balance;
                                    if (karma>0) {
                                        $('#karma_digit').html('Карма : +'+karma);
                                        $('#karma_head').html('+'+karma);
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    } else {
                                        $('#karma_digit').html('Карма : '+karma);
                                        $('#karma_head').html(karma);
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    }

                                }
                            });

                }
                function SellKarma(uId) {
                    $.getJSON('/personal/sellkarma',
                            function (response){
                                if (response.errors) {
                                    $(document).find('.error').empty();
                                    $('#err_karma').html(response.errors.karma);
                                } else {
                                    var karma = response.karma;
                                    var balance = response.balance;
                                    if (karma>0) {
                                        $('#karma_digit').html('Карма : +'+karma);
                                        $('#karma_head').html('+'+karma);
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    } else {
                                        $('#karma_digit').html('Карма : '+karma);
                                        $('#karma_head').html(karma);
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    }

                                }
                            });

                }
                function BuyUser(_uId) {
                    var _type = 0;
                    if(document.getElementById('p_one').checked) {
                        _type = 1;
                    }else if(document.getElementById('p_two').checked) {
                        _type = 2;
                    }

                    $.getJSON('/personal/buyuser/?u='+_uId+'&t='+_type,
                            function (response){
                                if (response.errors) {
                                    $(document).find('.error').empty();
                                    $('#err_user').html(response.errors.pass_genn);
                                } else {
                                    var pass_type = response.pass_type;
                                    var pass_genn = response.pass_genn;
                                    var balance = response.balance;
                                    if (pass_type==2) {
                                        $('#user_pass_gen').html('Новый пароль от пользователя "'+_uId+'": '+pass_genn);
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    } else {
                                        $('#user_pass_gen').html('Пользователю "'+_uId+'" установлен пароль от текущего пользователя');
                                        $('#head_balance').html(balance);
                                        $('#balance_footer').html(balance);
                                    }
                                    $('#buy_button').hide();
                                }
                            });

                }
                $(function() {
                    function findValue(li) {
                        if( li == null )
                            return alert("Ничего не выбрано!");
                        if( !!li.extra ) {
                            var sValue = li.extra[0];
                        } else {
                            var sValue = li.selectValue;
                        }
                        //alert("The value you selected was: " + sValue);
                    }
                    function selectItem(li) {
                        findValue(li);
                    }
                    function formatItem(row) {
                        return row[0];
                    }
                    $( ".autocomplete" ).autocomplete("/personal/buyuser/", {
                        delay:10,
                        minChars:1,
                        matchSubset:1,
                        matchContains:1,
                        cacheLength:10,
                        onItemSelect:selectItem,
                        onFindValue:findValue,
                        formatItem:formatItem,
                        autoFill:true
                    });
                })
            </script>
        {/literal}



    <form action="" method="post" class="form_reg" name="registration" enctype="multipart/form-data">
	<input type="hidden" name="event" value="forumuserprofileupdate">
	<label for="login">Имя пользователся (login){if $_auth_errors.user_name ne ''}<div class="error">{$_auth_errors.user_name}</div>{/if}</label>

	<h3>{$_system_user.user_name}</h3>
	<h3 style="font-size:15px;font-style:bold;">(Ваш ID для пополнения баланса: {$_system_user.userID})</h3>
    <p></p>
    <h3 id="karma_digit">Карма : {if $_system_user.danger_level>0}+{$_system_user.danger_level}{else}{$_system_user.danger_level}{/if}</h3>

    <span class="error" id="err_karma"></span>
    <div align="center"><a href="javascript:void(0)" onclick="BuyKarma({$_system_user.userID})"><h1 class="btn_reg" >&nbsp;ПОПОЛНИТЬ КАРМУ&nbsp;</h1></a></div>
    <div align="center"><a href="javascript:void(0)" onclick="SellKarma({$_system_user.userID})"><h1 class="btn_reg" >&nbsp;ПЕРЕВЕСТИ КАРМУ В ДЕНЬГИ&nbsp;</h1></a></div>

        <h3>Последние 50 операций над вашей кармой</h3>
        <div style="width:400px;height:100px;overflow:auto;text-align: left;">
            <table class=themes2 style="width:350px;text-align: left;display: block;float: left;">
                {foreach key=key item=curItem from=$_dataGrid}
                    <tr class="tr2">
                        <td class="tdw3" >{$curItem.created|date_format:"%H:%M %e/%m"}</td>
                        <td class="tdw3">{$curItem.user_name}</td>
                        <td class="tdw3">{if $curItem.rating>0}+{$curItem.rating}{else}{$curItem.rating}{/if}</td>
                    </tr>
                {/foreach}
            </table>
        </div>


            <br><br><div class="line2"><div></div></div>
            {if $COMMERCIAL_ON eq 1}
                <a name="userbalance"></a>
                <p style="color:black">Ваш Баланс составляет: <b> <span id="balance_footer">{$_system_user.user_balance}</span> руб</b>
                <div class="box_small_text">Баланс пользователя будет использоваться для оплаты коммерческих сервисов. Пополнить баланс можно разными способами.</div>
                <div align="center"><a href="/personal/payment/"><h1 class="btn_reg" >&nbsp;ПОПОЛНИТЬ БАЛАНС&nbsp;</h1></a></div>
                </p>
            {/if}
            <br><br><div class="line2"><div></div></div>



            <div class="box_small_text">Изменить имя пользователя невозможно.</div>
	<div class="line2"><div></div></div>
	<label for="password1">* Пароль {if $_auth_errors.user_password ne ''}<div class="error">{$_auth_errors.user_password}</div>{/if}</label>
	<input type="password" class="inp_text_reg" name="user[user_password]" id="password1" maxlength="100">
	<label for="password2">* Подтверждение пароля {if $_auth_errors.user_password ne ''}<div class="error">{$_auth_errors.user_password}</div>{/if}</label>
	<input type="password" class="inp_text_reg" name="user[user_password_confirm]" id="password2" maxlength="100">

	<div class="box_small_text">Если не хотите изменять пароль - оставьте поля пустыми.</div>
	<div class="line2"><div></div></div>
	<label for="email">* Ваш e-mail {if $_auth_errors.user_email ne ''}<div class="error">{$_auth_errors.user_email}</div>{/if}</label>
	<input type="text" class="inp_text_reg" name="user[user_email]" id="email"  value="{$_system_user.user_email}">
	<div class="line2"><div></div></div>
	<label for="fio">Имя Фамилия Отчество</label>
	<input type="text" class="inp_text_reg" name="user[user_fio]" id="fio"  value="{$_system_user.user_fio}">
	<label>Ваш пол</label>
	<input type="radio" name="user[user_gender]" value="1" id="1" class="radioinput" {if $_system_user.user_gender eq 1}checked="checked"{/if}>
	<label for="1" class="radiolab">Не имеет значения</label><br>
	<input type="radio" name="user[user_gender]" value="2" id="2" class="radioinput" {if $_system_user.user_gender eq 2}checked="checked"{/if}>
	<label for="2" class="radiolab">Мужской</label><br>
	<input type="radio" name="user[user_gender]" value="3" id="3" class="radioinput" {if $_system_user.user_gender eq 3}checked="checked"{/if}>
	<label for="3" class="radiolab">Женский</label><br>
	<input type="radio" name="user[user_gender]" value="4" id="4" class="radioinput" {if $_system_user.user_gender eq 4}checked="checked"{/if}>
	<label for="4" class="radiolab">Средний</label><br>	</p>
	<label for="icq">Номер ICQ</label>
	<input type="text" class="inp_text_reg" name="user[user_icq]"  value="{$_system_user.user_icq}">

	<label for="addition">Дополнительно</label>
	<textarea name="user[description]" id="addition" class="area_text_reg">{$_system_user.description}</textarea>
	<label for="image">Аватар:</label>
	<input type="file" name="image" size="30" />
	{if $_thumb_image neq ''}
		<label>Текущий аватар</label>
		<a href="/{$_large_image}"><img src="/{$_thumb_image}?t={php} echo time(); {/php}" alt="Щелкните для просмотра полного изображения" title="Щелкните для просмотра полного изображения" /></a>
		<br />
		<label for="delUserPic"><input type="checkbox" value="1" name="delUserPic" id="delUserPic" />&nbsp;удалить&nbsp;аватар</label>
		<br />
	{/if}
		
	<div class="line2_wide"><div></div></div>
        <div class="wide_line">
            <label for="groups" class="wide_line"><strong>Не отображать темы следующих групп в топе:</strong></label>
                <div style="float: left;">
                    {foreach key=key item=curItem from=$groups_common}
                        <div style="color: #000;width: 180px;float: left">
                            <input type="checkbox" name="igroups[{$curItem.groupID}]" id="ig_{$curItem.groupID}" {if $curItem.i eq 1}checked="checked"{/if} />
                            <label for="ig_{$curItem.groupID}" class="radiolab">{$curItem.caption}</label>
                        </div>
                    {/foreach}
                </div>

{*
                <div style="float: left; width:360px">
                    {foreach key=key item=curItem from=$groups_left}
                        <div style="color: #000;width: 175px;float: left">
                            <input type="checkbox" name="igroups[{$curItem.groupID}]" id="ig_{$curItem.groupID}" {if $curItem.i eq 1}checked="checked"{/if} />
                            <label for="ig_{$curItem.groupID}" class="radiolab">{$curItem.caption}</label>
                        </div>
                    {/foreach}
                </div>
                <div style="float: left; width:360px">
                    {foreach key=key item=curItem from=$groups_right}
                        <div style="color: #000;width: 175px;float: left">
                            <input type="checkbox" name="igroups[{$curItem.groupID}]" id="ig_{$curItem.groupID}" {if $curItem.i eq 1}checked="checked"{/if} />
                            <label for="ig_{$curItem.groupID}" class="radiolab">{$curItem.caption}</label>
                        </div>
                    {/foreach}
                </div>
*}
                <br style="clear: both" ?>
        </div>
        {if $_system_user.fz152_agreement neq 1}
            <div class="line2_wide"><div></div></div>
            <a name="fz152" id="fz152"></a>
            <label for="fz152_agreement" class="wide_line">
                <input type="checkbox" name="user[fz152_agreement]" id="fz152_agreement" class="radioinput" {if $_request.fz152_agreement eq 'on'}checked="checked"{/if}>
                Я согласен(на) на обработку персональных данных
            </label>
            <div class="box_small_text_wide"><ol>
                    <li>Настоящим в соответствии с Федеральным законом № 152-ФЗ «О персональных данных» от 27.07.2006 года Вы подтверждаете свое согласие на обработку Медиа Холдингом «Example Holding» персональных данных: сбор, систематизацию, накопление, хранение, уточнение (обновление, изменение), использование, блокирование, обезличивание, уничтожение. Мы, Медиа Холдинг "Example Holding", гарантируем конфиденциальность получаемой нами информации. </li>
                    <li>Настоящее согласие распространяется на следующие Ваши персональные данные: фамилия, имя и отчество, адрес электронной почты, icq, платёжные реквизиты.</li>
                    <li>Срок действия Вашего согласия является неограниченным, однако, Вы вправе в любой момент отозвать настоящее согласие, путём направления письменного уведомления на адрес: 000000, Российская Федерация, г. Пример, ул. Примерная, д. 1, в ООО «Example Company», с пометкой «отзыв согласия на обработку персональных данных». Обращаем Ваше внимание, что отзыв Вашего согласия на обработку персональных данных влечёт за собой удаление Вашей учётной записи с Интернет-сайта (http://forum.site), а также уничтожение записей, содержащих Ваши персональные данные, в системах обработки персональных данных Медиа Холдинга «Example Holding», что может сделать невозможным пользование Интернет-сервисами Медиа Холдинга «Example Holding».</li>
                    <li>P.S. Данные не будут использоваться для рассылок.</li>
                    <li>P.P.S. Никакие новые данные, кром тех, что Вы уже сохранили не требуются. Данным согласием Вы, согласно закону №152-ФЗ, даете согласие на их хранение и обработку. Если Вы не соглашаетесь, то согласно этому же закону, мы обязаны Ваши данные удалить.</li>
                </ol></div>

            <div class="line2_wide"><div></div></div>
        {/if}
        <div class="line2_wide"><div></div></div>
        <label for="pager_subscribe" class="wide_line">
            <input type="checkbox" name="user[pager_subscribe]" id="pager_subscribe" class="radioinput" {if $_system_user.pager_subscribe eq 1}checked="checked"{/if}>
            <strong>Отправлять уведомления о новых сообщения в пейджере на указанную в профиле почту</strong>
        </label>

        <div class="box_small_text_wide">Если вся информация верна - нажмите кнопку (достаточно одного раза):</div>
	<input type="submit" class="btn_reg" value="Сохранить изменения">
	</form>

	{*
    <br><br><div class="line2_wide"><div></div></div>

    <h3>Купить старый ник ({$smarty.const.OLD_NICKNAME_COST}р.)</h3>
    <br />
        <a name="buy"></a>
    <div style="width:700px">
        <!--<form action="/personal/buyuser2/#buy" method="POST">
            <input type="hidden" name="action" value="addUser">-->

            <b style='color:#000;'>Выбрать пользователя:</b><br />
            <input type="text" id='blackuser_field' name="puser" class='autocomplete'>&nbsp;
            {if $is_mobile eq 'mobile'}
                <br><br>
            {/if}
            <label style='color:#000;'><input id="p_one" type="radio" name="pgen" value="1" checked="">Поставить пароль от текущего пользователя</label>&nbsp;
            <label style='color:#000;'><input id="p_two"  type="radio" name="pgen" value="2">Сгенерировать новый</label>&nbsp;
            <br /><br />
            <span class="error" id="err_user"></span>
            <h3 id="user_pass_gen"></h3>
            <!--<input type="submit" value="Добавить">-->
            <a id="buy_button" href="javascript:void(0)" onclick="BuyUser(document.getElementById('blackuser_field').value)"><h1 class="btn_reg" >&nbsp;Купить&nbsp;</h1></a>
        </form>
        <br />
    </div>
    <div id='blacklist' style='width:450px;height:200px;overflow : auto; font:14px solid #000;color:#000;'>
    *}



    {/if}
	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}