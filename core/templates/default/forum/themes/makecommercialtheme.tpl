{literal}
<script language="javascript" type="text/javascript">
<!--

function tag1(text1, text2)
{
	/*
	if ((document.selection))
	{
		//alert (23);
		document.getElementById('mess_text').focus();
		document.postform.document.selection.createRange().text = text1+document.postform.document.selection.createRange().text+text2;
	} else 
	{ 
		//alert(56); 
		document.getElementById('mess_text').value += text1+text2;
	}
	*/
	document.getElementById('mess_text').value += text1+text2;
}

ie4 = (document.all)? true:false;
lastKey = 0;
textFocus = false;

function keyUp(e) {
	lastKey = 0;
}

function keyDown(e) {
	if (ie4) {
		var ieKey = event.keyCode;
		if ((lastKey == 17) && (ieKey == 13) && textFocus) {
			document.postform.submit();
		}
		lastKey = ieKey;
	}
}

if (ie4) {
	document.onkeydown = keyDown;
	document.onkeyup = keyUp;
}

//-->
</script>
<script type="text/javascript" src="/js/testbbcode.js"></script>
{/literal}
<div>
	<form action="" method="post" class="form_mess" id="formMessage" name="formMessage">
		<input type="hidden" name="event" value="forumcreatecommercialtheme">
		{include file="forum/banner/right.tpl"}
		{include file="forum/banner/left.tpl"}
	
		<div class="form_box">
			{include file="forum/errors.tpl"}
					{*
					<select name="_package">
						<option value="0">Прикрепить пакет</option>
						{foreach item="package" from=$listmypackages}
							<option value="{$package.id}" {if $_themeParam.packageid == $package.id}selected="selected"{/if}>{$package.name}</option>
						{/foreach}
					</select>
					*}
			<div class="form_box_name">
				<label for="name" class="l_inp_text_name">{if $_system_user.userID >0}<span>{$_system_user.user_name}</span>{else} Ваше имя:{/if}</label>
				<input class="inp_text_name" id="name" tabindex="1" maxlength="20" name="message[author]" value="{$_themeParam.author}" type="text">
			</div>
			<div class="form_box_title">
				<label for="heading" class="l_inp_text_name">Заголовок темы:</label>
				<input class="inp_text_name" id="heading" tabindex="2" maxlength="64" name="message[caption]" type="text" value="{$_themeParam.caption}">
			</div>
			<div class="form_box_mess">
				<div>
					<select name="_package" style="width: 524px; background-color: #E6E6E6;">
						<option value="0">Прикрепить пакет</option>
						{foreach item="package" from=$listmypackages}
							<option value="{$package.id}" {if $_themeParam.packageid == $package.id}selected="selected"{/if}>{$package.name}</option>
						{/foreach}
					</select>
				</div>
				<textarea class="area_text" tabindex="3" id="mess_text" name="message[content]" onFocus="javascript: textFocus = true;" onBlur="javascript: textFocus = false;" style="height: 85px;">{$_themeParam.content}</textarea>        
			</div>
			<div class="form_box_btn">
				<input class="btn_form" value="Добавить тему" tabindex="4" type="submit">
			</div>
			<div class="format">
				<div id="web"></div>
				<span><a href="" onclick="doInsert('[b]','[/b]', false);return false;" class="for1" id="bold">Bold</a>&nbsp;-&nbsp;<a href="" onclick="doInsert('[i]','[/i]', true);return false;" class="for2">Italic</a>&nbsp;-&nbsp;<a href="" onclick="doInsert('[re]','[/re]', false);return false;" class="for3">Cite</a></span>
			</div>
		</div>
	</form>
</div>
{literal}
<script language="javascript" type="text/javascript">
<!--
	var fombj = document.getElementById( 'formMessage' );
//-->
</script>
{/literal}