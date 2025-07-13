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
$('.btn_form').dblclick(function(e){
    e.preventDefault();
})

//-->
</script>
{/literal}
{if $readonly neq 1}
<div class="form_box">
	<form action="" method="post" class="form_mess" name="formMessage" id="formMessage" enctype="multipart/form-data">
	<input type="hidden" name="event" value="forumcreatetheme">
	{*include file="forum/banner/right.tpl"*}
	{*include file="forum/banner/left.tpl"*}
	<div>

		<div class="form_box_name">
			<label for="name" class="l_inp_text_name">{if $_system_user.userID >0}<span>{$_system_user.user_name}</span>{else} Ваше имя:{/if}</label>
			<input class="inp_text_name" id="name" tabindex="1" maxlength="20" name="_message[author]"
			{if count($__errors) > 0}
                value="{$smarty.request._message.author}"
            {else}
                {if $smarty.request._message.author ne ''}
                    value="{$smarty.request._message.author}"
                {else}
                    value="{$smarty.cookies._cookie_name}"
                {/if}
			{/if}
			type="text">
		</div>
		<div class="form_box_title">
			<label for="heading" class="l_inp_text_name">Заголовок темы:</label>
			<input class="inp_text_name" id="heading" tabindex="2" maxlength="200" name="_message[caption]"
			{if count($__errors) > 0}
			    value="{$smarty.request._message.caption}"
			{/if}
			type="text">        
		</div>
		<div class="form_box_mess">
			<textarea class="area_text" tabindex="3" id="mess_text" name="_message[content]" onFocus="javascript: textFocus = true;" onBlur="javascript: textFocus = false;">{if count($__errors) > 0}{$smarty.request._message.content}{/if}</textarea>
		</div>
        <div class="clear"></div>
		<div class="form_box_btn">
		    <div id='web1' class="captchas">
			<input class="btn_form" value="Добавить тему" tabindex="4" type="submit">
			<span class=right>
                <a href="" onclick="doInsert('[b]','[/b]', true); return false;" class="for1" id="bold">Жирный</a>&nbsp;
                <a href="" onclick="doInsert('[i]','[/i]', false); return false;" class="for2">Курсив</a>&nbsp;
                <a href="" onclick="doInsert('[re]','[/re]', false); return false;" class="for3">Цитата</a>&nbsp;
                <a href="javascript:;;" onclick="opa_st(document.getElementById('s_emo'),1);return false" class="for3">Смайлы</a>&nbsp;
                <a href="javascript:;;" onclick="opa_st(document.getElementById('files_form'),1);return false" class="for3">Изображения</a>&nbsp;
                <a href="javascript:;;" onclick="opa_st(document.getElementById('video_form'),1);return false" class="for3">Видео</a>&nbsp;
                {if $_system_user.userID >0}
                    &nbsp;<a href="javascript:;;" onclick="opa_st(document.getElementById('spec_form'),1);return false" class="for3">В ТОП</a>
                {/if}
			</span><br class="clear">{include file="forum/errors.tpl"}<br class="clear">
            {if $current_group.is_mat}<h4 style="padding:0px 0px 5px 0px;margin:0px;">Данная группа является матоязычной. Уберите от экранов детей, младше 21 года!</h4>{/if}
            </div>
		</div>
		<div class="form_box_btn">
		    {* <div class="form_box_btn_112"> *}
                {if $captcha}
                    <span class="form_box_btn_span"><b>Проверочный код:</b></span>
                    <div class="format"><div id="web">
                        <img id='randomImage' src="/antibot.php?{php} echo time();{/php}" width="190" height="30">
                    </div></div>
                    <input type="text" name="imageString" class="inp_text_name_span" style="width:70px;"  maxlength="7" >

                {/if}
            {* </div> *}
		</div>

            <br style="clear:both" />
            <div id="s_emo" class="box_emtn">
            {literal}
                <script type="text/javascript">
                    smiles=new Array(':)',':(','>:(','0_o',':sad:',':kiss:',':eye:',':tong:',':hungry:',':sleep:',':ugly:',':smile:',':flower:',':devel:',':glass:',':sunglass:',':lazy:',':crazy:',':anger:',':love:',':cry:',':bigcry:',':fun:',':cool:',':pnone:',':angel:');
                    sfiles=new Array('1.png','2.png','3.png','4.png','5.png','6.png','7.png','8.png','9.png','10.png','11.png','12.png','13.png','14.png','15.png','16.png','17.png','18.png','19.png','20.png','21.png','22.png','23.png','24.png','25.png','26.png');
                    smilie_box();
                </script>
            {/literal}
                <br style="clear:both" />
                <div class="box_barr" style="float:right;margin-top:2px">
                <a href="javascript:;;" onclick="opa_st(document.getElementById('s_emo'),0);return false">Закрыть</a>
                </div>
            </div>
            <div id="files_form" class="box_fls">
                <input type="file" name="file_1" >
                <input type="file" name="file_2" >
                <input type="file" name="file_3" >
                <br style="clear:both" />
                <div class="box_barr" style="float:right;margin-top:2px">
                    <a href="javascript:;;" onclick="opa_st(document.getElementById('files_form'),0);return false">Закрыть</a>
                </div>
            </div>
            <div id="video_form" class="box_fls">
                <a href="" onclick="doInsertVideo('youtube'); return false;" class="for3">YouTube</a>&nbsp;|&nbsp;
                <a href="" onclick="doInsertVideo('smotri'); return false;" class="for3">Smotri.com</a>&nbsp;|&nbsp;
                <a href="" onclick="doInsertVideo('rutube'); return false;" class="for3">RuTube.Ru</a>&nbsp;|&nbsp;
                <a href="" onclick="doInsertVideo('tomskfm'); return false;" class="for3">Tomsk.FM</a>
                <br style="clear:both" />
                <div class="box_barr" style="float:right;margin-top:2px">
                    <a href="javascript:;;" onclick="opa_st(document.getElementById('video_form'),0);return false">Закрыть</a>
                </div>
            </div>
            {if $_system_user.userID >0}
                <div id='spec_form' class="box_fls"><br />
                <table width="380">
                    <tr>
                        <td width="100%"><b>Закрепить&nbsp;в&nbsp;ТОПе раздела:&nbsp;</b></td>
                        <td nowrap>({$smarty.const.TOP_PRICE}&nbsp;руб/нед)</td>
                        <td><input type="checkbox" name="_message[top]" value="1"></td>
                    </tr>
                    <tr>
                        <td width="100%"><b>Закрепить в ТОП50 (горячее):&nbsp;</b></td>
                        <td nowrap>({$smarty.const.TOP30_PRICE}&nbsp;руб/нед)</td>
                        <td><input type="checkbox" name="_message[top30]" value="1"></td>
                    </tr>
                    <tr>
                        <td colspan="2">Закрыть тему от комментариев?</td><td><input type="checkbox" name="_message[close_theme]" value="1"></td>
                    </tr>

                </table>
                <br style="clear:both" /><div class="box_barr" style="float:right;margin-top:2px">
                <a href="javascript:;;" onclick="opa_st(document.getElementById('spec_form'),0);return false">Закрыть</a>
                </div>
                </div>
            {/if}

	</div>
	</form>
</div>
{literal}
<script language="javascript" type="text/javascript">
<!--
	var fombj = document.getElementById( 'formMessage' );
    //var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 3 );
    //multi_selector.addElement( document.getElementById( 'my_file_element' ) );
//-->
</script>
{/literal}
{/if}