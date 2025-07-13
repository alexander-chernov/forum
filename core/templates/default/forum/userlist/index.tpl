{include file="forum/header.tpl"}
{if $_system_user.is_admin || $isGroupAdministrator}
    {literal}
        <script type="text/javascript">
            function HideUserMessages(uId,userName) {
                alert('Скрытие сообщений пользователя: '+userName);
                $.get("/forum/?", { event: "forumhideajaxusermessages", _uId: uId });
            }
            function BanUserForever(uId) {
                $.get("/forum/?", { event: "forumbanajaxuserforever", _uId: uId });
                $('#img'+uId).hide();
            }
            function CrearKarma(uId) {
                $.get("/forum/?", { event: "forumajaxuserclearkarma", _uId: uId });
            }
        </script>
    {/literal}
{/if}


<div class="navigation">
	<div class="box_path">
		:: <a href="/forum/">Томские форумы</A> &nbsp;/&nbsp; Список пользователей
    </div>
</div>
<div class="line1"></div>

<div class="box_alfavit">
<a href="/users/?letter=А">А</a> <a href="/users/?letter=Б">Б</a> <a href="/users/?letter=В">В</a> <a href="/users/?letter=Г">Г</a> <a href="/users/?letter=Д">Д</a> <a href="/users/?letter=Е">Е</a> <a href="/users/?letter=Ж">Ж</a> <a href="/users/?letter=З">З</a> <a href="/users/?letter=И">И</a> <a href="/users/?letter=Й">Й</a> <a href="/users/?letter=К">К</a> <a href="/users/?letter=Л">Л</a> <a href="/users/?letter=М">М</a> <a href="/users/?letter=Н">Н</a> <a href="/users/?letter=О">О</a> <a href="/users/?letter=П">П</a> <a href="/users/?letter=Р">Р</a> <a href="/users/?letter=С">С</a> <a href="/users/?letter=Т">Т</a> <a href="/users/?letter=У">У</a> <a href="/users/?letter=Ф">Ф</a> <a href="/users/?letter=Х">Х</a> <a href="/users/?letter=Ц">Ц</a> <a href="/users/?letter=Ч">Ч</a> <a href="/users/?letter=Ш">Ш</a> <a href="/users/?letter=Щ">Щ</a> <a href="/users/?letter=Ъ">Ъ</a> <a href="/users/?letter=Ы">Ы</a> <a href="/users/?letter=Ь">Ь</a> <a href="/users/?letter=Э">Э</a> <a href="/users/?letter=Ю">Ю</a> <a href="/users/?letter=Я">Я</a> &nbsp;&nbsp; <a href="/users/?letter=A">A</a> <a href="/users/?letter=B">B</a> <a href="/users/?letter=C">C</a> <a href="/users/?letter=D">D</a> <a href="/users/?letter=E">E</a> <a href="/users/?letter=F">F</a> <a href="/users/?letter=G">G</a> <a href="/users/?letter=H">H</a> <a href="/users/?letter=I">I</a> <a href="/users/?letter=J">J</a> <a href="/users/?letter=K">K</a> <a href="/users/?letter=L">L</a> <a href="/users/?letter=M">M</a> <a href="/users/?letter=N">N</a> <a href="/users/?letter=O">O</a> <a href="/users/?letter=P">P</a> <a href="/users/?letter=Q">Q</a> <a href="/users/?letter=R">R</a> <a href="/users/?letter=S">S</a> <a href="/users/?letter=T">T</a> <a href="/users/?letter=U">U</a> <a href="/users/?letter=V">V</a> <a href="/users/?letter=W">W</a> <a href="/users/?letter=X">X</a> <a href="/users/?letter=Y">Y</a> <a href="/users/?letter=Z">Z</a> &nbsp;&nbsp; <span><a href="/users/?letter=@">Другое</a></span></div>

<table class="userstable" style="vertical-align:top;">
<tr>

    {if $_system_user.is_admin}
	    <td colspan="10">
    {else}
        <td colspan="5">
    {/if}
		<form action="" method="GET">
			<span>Поиск по нику:</span>
			<input type="text" id="user_filter" name="letter" value="{php}echo $_GET['letter']{/php}" />
			<input type="submit" value="Поиск" />
		</form>
	</td>
</tr>
{if $badUsers || $goodUsers}
<tr>
<td >
    <h2 style="color:white">Наши двоечники и двоечницы</h2></td>
<td>
    <h2 style="color:white">Наши отличники и отличницы</h2></td>
</tr>
<tr style="vertical-align:top;">
<td style="padding:0px;vertical-align:top;margin:0px;">
<table {if $is_mobile eq 'mobile'}
       style="width:255px;border:0px;margin:0px;padding:0px;"
       {else}
       style="width:383px;border:0px;margin:0px;padding:0px;"  class="userstable"
       {/if}
        >
{section loop=$badUsers name="u"}
<tr>
	<td><a onclick="LoadPassport({$badUsers[u].userID});" href="#">{$badUsers[u].user_name}<sup title="Карма">{if $badUsers[u].danger_level>0}+{$badUsers[u].danger_level}{else}{$badUsers[u].danger_level}{/if}</sup></a></td>
</tr>
{/section}
</table>
</td><td style="padding:0px;vertical-align:top;">
    <table {if $is_mobile eq 'mobile'}
            style="width:255px;border:0px;margin:0px;padding:0px;"
        {else}
            style="width:383px;border:0px;margin:0px;padding:0px;"  class="userstable"
    {/if}
            >
{section loop=$goodUsers name="u"}
<tr>
	<td><a onclick="LoadPassport({$goodUsers[u].userID});" href="#">{$goodUsers[u].user_name}<sup title="Карма">{if $goodUsers[u].danger_level>0}+{$goodUsers[u].danger_level}{else}{$goodUsers[u].danger_level}{/if}</sup></a></td>
</tr>
{/section}
</table>
</td>
</tr>
{else}
<tr><th>На форуме</th>
	<th>Пол</th>
	<th>Фото</th>
	<th>Время посещения</th>
	<th>Дата регистрации</th>
    {if $_system_user.is_admin}
        <th style="width:10px;"></th>
        <th style="width:10px;"></th>
        <th style="width:10px;"></th>
        <th style="width:10px;"></th>
        <th style="width:10px;"></th>
    {/if}
</tr>
{section name="u" loop="$_users"}
<tr>
	<td><a onclick="LoadPassport({$_users[u].userID});" href="#">{$_users[u].user_name}<sup title="Карма">{if $_users[u].danger_level>0}+{$_users[u].danger_level}{else}{$_users[u].danger_level}{/if}</sup></a></td>
	<td class="tdw1">
            {if $_users[u].user_gender eq 1}
    			Не имеет значения
    		{elseif $_users[u].user_gender eq 2}
    			Мужской
    		{elseif $_users[u].user_gender eq 3}
    			Женский
    		{else}
    			Средний
    		{/if}		
	</td>
	<td class="tdw2"><span class="no">{if $_users[u].photo eq 1}ЕСТЬ{else}НЕТ{/if}</span></td>
	<td class="tdw3" align="center">{$_users[u].lastlogin|date_format:"%e.%m.%Y (%H:%M)"}</td>
	<td class="tdw3" align="center">{$_users[u].registered|date_format:"%e.%m.%Y (%H:%M)"}</td>
    {if $_system_user.is_admin}
        <td class="tdw1" style="width:10px;">{if $_users[u].user_ip}<a target=_blank href="/.admin/bans/ip/add/?ip={$_users[u].userIP}&msgID=&theme=&group=&authorID={$_users[u].userID}"><img src="/images/btn_pasport_close.gif" alt="Бан" title="Бан" class="to_head">{$_users[u].userIP}</a>{/if}</td>
        <td class="tdw1" style="width:10px;"><a href="javascript:void(0)" onclick="HideUserMessages({$_users[u].userID},'{$_users[u].user_name}')"><img src="/images/remove.png" width=11 height="11" alt="Скрыть все сообщения этого пользователя за последние 7 дней" title="Скрыть все сообщения этого пользователя за последние 7 дней" class="to_head"></a></td>
        <td class="tdw1" style="width:10px;"><a target=_blank href="/.admin/bans/nicknames/list/?userIsId=1&usersearch={$_users[u].userID}"><img src="/images/banned.png" width=13 height="13" alt="Забанить пользователя" title="Забанить пользователя" class="to_head"></a></td>
        <td class="tdw1" style="width:10px;">{if $_users[u].banned eq 0}<a href="javascript:void(0)" onclick="BanUserForever({$_users[u].userID})"><img id="img{$_users[u].userID}" src="/images/foreverban.gif" width=13 height="13" alt="Скрыть все сообщения этого пользователя за последние 7 дней" title="Забанить пользователя насовсем" class="to_head"></a>{/if}</td>

        <td class="tdw1" style="width:10px;"><a href="javascript:void(0)" onclick="CrearKarma({$_users[u].userID})"><img id="img{$_users[u].userID}" src="/images/btn_pasport_close.gif" alt="Обнулить карму" title="Обнулить карму" class="to_head"></a></td>
    {/if}
</tr>
{/section}

        <tr>
            <th colspan="10">
                <span class="next"><a  href="?letter={$letter}&page={$page+1}"><img src="/images/v_1_1/r_b.gif" title="Вперед" alt="Вперед" border="0" class="to_head" style="width:24px" onmouseover="this.src = '/images/v_1_1/r_y.gif'" onmouseout="this.src = '/images/v_1_1/r_b.gif'"></a></span>
            </th>
        </tr>

{/if}
</table>
{include file="forum/footer.tpl"}