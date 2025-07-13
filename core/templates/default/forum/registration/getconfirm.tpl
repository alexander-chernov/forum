{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Подтверждение пользователя
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
	<form method="post">
		<input type="hidden" name="event" value="sendconfirm" />
		<b style="color: #000;">E-mail:</b> <input type="text" name="email" class="inp_text_name" />
		<input type="submit" value="Получить код" class="btn_form" style="width: 90px;" />
	</form>
	{/if}
</div>
<A name=bot></A>

{include file="forum/footer.tpl"}
