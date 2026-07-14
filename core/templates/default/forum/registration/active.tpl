{include file="forum/header.tpl"}

<div class="navigation">
	<div class="box_path">
		:: <a href="#">Example Forum</A> &nbsp;/&nbsp; Регистрация пользователя
    </div>
</div>
<div id="overlay"></div>
{include file="forum/authform.tpl"}
<div id="user_info_box"></div>

<A name=top></A>

<div class="box_text">
<p>
	{foreach item=error from=$_err}
		<h2 style="color:#F00;text-align: center;">{$error}</h2>
	{/foreach}
	{foreach item=error from=$_msg}
		<h2 style="color:#000;text-align: center;">{$error}</h2>
	{/foreach}
</p>
</div>
<A name=bot></A>
{include file="forum/footer.tpl"}
