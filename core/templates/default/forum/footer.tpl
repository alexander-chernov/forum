<div class="block_menu_b">
	{include file="forum/submenu.tpl"}
</div>
<A name=bot></A>



{if $is_mobile neq 'mobile'}
    {include file="forum/banner/bottom.tpl"}
{/if}
<div class="block2">

</div>
<div class="line1"></div>
<div class="block2">
	Тем: {$_counters.themes_count}
	&nbsp;|&nbsp; Сообщений: {$_counters.messages_count}
	&nbsp;|&nbsp; Пользователей: {$_counters.users_count}
</div>
<div class="line1"></div>
<div class="block2" style="min-height:50px;">
    <div class="copy">Не является СМИ<br>
	</div>
</div>
<div class="line1"></div>
<div class="block2">

</div>
<div class="line1"></div>
<div class="block2">

</div>



</body>
</html>