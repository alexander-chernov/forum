{if $_system_user.userID eq 0}<a href="#" class="name_m" onClick="LoadAuthorization()">Вход</a>
    <div class="sep"><div></div></div>
	
	{if $_system_url_params[1] eq 'register'}
		<div class="act"><a href="/register/">Регистрация</a></div>
	{else}
		<div><a href="/register/">Регистрация</a></div>
	{/if}
    <div class="sep"><div></div></div>

    {if $_system_url_params[1] eq 'users'}
        <div class='act'><a href="/users/">Пользователи</a></div>
    {else}
        <a href="/users/">Пользователи</a>
    {/if}

	 <div class="sep"><div></div></div>
{else}
    <a href="#" onclick="LoadPassport();" class="userlink"><b>{$_system_user.user_name}<sup style="font-size: 80%"><span id="karma_head">{if $_system_user.danger_level>0}+{$_system_user.danger_level}{else}{$_system_user.danger_level}{/if}</span></sup></b></a>
	{if $COMMERCIAL_ON eq 1}
    	<a href="/personal/payment/" id='user_balance' {if $_system_user.user_balance >0 }style="color:#00cc00;"{else}style="color:#cc0000;"{/if} title="ПОПОЛНИТЬ БАЛАНС.">(<span id="head_balance">{$_system_user.user_balance}</span> руб.)</a>
    {/if}
	
	{if $_system_url_params[1] neq 'users'}
		<div class="sep"><div></div></div>
	{/if}
	
    {if $_system_url_params[1] eq 'users'}
		<div class='act'><a href="/users/">Пользователи</a></div>
	{else}
		<a href="/users/">Пользователи</a>
	{/if}
	
	{if $_system_url_params[1] neq 'users' && $_system_url_params[2] neq 'rules'}
	<div class="sep"><div></div></div>
	{/if}
{/if}		
	{if $_system_url_params[1] eq 'pages' && $_system_url_params[2] eq 'rules'}
		<div class="act"><a href="/pages/rules/">Правила</a></div>	
	{else}
		<div><a href="/pages/rules/">Правила</a></div>
	{/if}	
	
	{if $_system_url_params[2] neq 'rules'}
	<div class="sep"><div></div></div>
	{/if}

{if $is_mobile neq 'mobile'}
    <a href="#" onclick="LoadCheckIp()">Проверить IP</a>
{/if}
    
	{if $_system_url_params[2] neq 'contacts'}
	<div class="sep"><div></div></div>
	{/if}
	
	{if $_system_url_params[1] eq 'pages' && $_system_url_params[2] eq 'contacts'}
		<div class="act"><a href="/pages/contacts/">Контакты</a></div>
	{else}
		<div><a href="/pages/contacts/">Контакты</a></div>
	{/if}	

	{if $_system_url_params[2] neq 'contacts' && $_system_url_params[2] neq 'reklama'}
    <div class="sep"><div></div></div>
	{/if}
    {if $is_mobile neq 'mobile'}
        {if $_system_url_params[1] eq 'pages' && $_system_url_params[2] eq 'reklama'}
            <div class="act"><a href="/pages/reklama/">Реклама</a></div>
        {else}
            <div><a href="/pages/reklama/">Реклама</a></div>
        {/if}
    {/if}
    {if $readonly eq 1}
        {if $ban_type neq 'ip'}
            <div class="sep"><div></div></div>
            <div><a class="exit" href="/forum/?event=cmsuserlogout">Выход</a></div>
        {/if}
    {else}
        {if $_system_user.banned eq 1}
            <div class="sep"><div></div></div>
            <div><a class="exit" href="?event=cmsuserlogout">Выход</a></div>
        {/if}
    {/if}


