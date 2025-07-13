
{literal}
<script language="javascript" type="text/javascript">
$(document).ready(function(){
    {/literal}
    {php}
        for ($i=1;$i<=MAX_FILES_UPLOAD;$i++) {
            echo "
            $('#clear_file_".$i."').click(function(event){
                event.preventDefault();
                $('#picfile_".$i."').replaceWith(\"<input type='file' name='file_".$i."' id='picfile_".$i."'>\");
                $('#file_".$i."_hidden').value('1');
            });
            ";
        }
    {/php}
    {literal}
    $('#formMessage').submit(function(){
        $('#s_emo').hide();
        $('#files_form').hide();
        $('#video_form').hide();
        $(this).ajaxSubmit({
            type:'post',
            target:"#err_formMessage_system_div",
            beforeSubmit: onAjaxRequestForm,
            success : onAjaxSubmitForm2,
            async: true,
            dataType : 'json',
            timeout: 3000,
            error: errorAjax,
            notsuccess: errorAjax
            });
        return false;
    });
});
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
<div id="ajax_form_message">
    <form class="form_mess" name="formMessage" id="formMessage" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="event" value="forumcreatemessage">
    <input type="hidden" name="ajx" value="1">
    {*include file="forum/banner/right.tpl"*}
    {*include file="forum/banner/left.tpl"*}

    <div class="form_box">
        {include file="forum/errors.tpl"}
        <!--<div style="align:center;" id='err_formMessage_system_div'><p class="error" id='err_formMessage_system'></p></div>-->
            <div class="form_box_name">
                <label for="name" class="l_inp_text_name">{if $_system_user.userID >0}<span>{$_system_user.user_name}</span>{else} Ваше имя:{/if}</label>

                <input class="inp_text_name" id="name" tabindex="1" maxlength="20" name="_message[author]"
                {if count($__errors) > 0}
                    value="{$smarty.request.message.author}"
                {else}
                    {if $smarty.request.message.author ne ''}
                        value="{$smarty.request.message.author}"
                    {else}
                        value="{$smarty.cookies._cookie_name|htmlspecialchars}"
                    {/if}
                {/if}
                type="text">
            </div>
            <div class="form_box_title">
                <label for="heading" class="l_inp_text_name">Заголовок сообщения:</label>
                <input class="inp_text_name" id="heading" tabindex="2" maxlength="200" name="_message[caption]"
                {if count($__errors) > 0}
                    value="{$smarty.request.message.caption}"
                {/if}
                type="text">
            </div>
            <div class="form_box_mess">
                <textarea class="area_text" tabindex="3" id="mess_text" name="_message[content]" onFocus="javascript: textFocus = true;" onBlur="javascript: textFocus = false;">{if count($__errors) > 0}{$smarty.request.message.content}{/if}</textarea>
            </div>
            <div class="form_box_btn">
                <div class="captchas">
                    <input class="btn_form" value="Добавить сообщение" tabindex="4" type="submit">

                    <span class="right">
                        <a href="javascript:void(0)" onclick="doInsert('[b]','[/b]', true); return false;" class="for1" id="bold">Жирный</a>&nbsp;
                        <a href="javascript:void(0)" onclick="doInsert('[i]','[/i]', false); return false;" class="for2">Курсив</a>&nbsp;
                        <a href="javascript:void(0)" onclick="doInsert('[re]','[/re]', false); return false;" class="for3">Цитата</a>&nbsp;
                        <a href="javascript:void(0)" onclick="opa_st(document.getElementById('s_emo'),1);return false" class="for3">Смайлы</a>&nbsp;
                        <a href="javascript:void(0)" onclick="opa_st(document.getElementById('files_form'),1);return false" class="for3">Изображения</a>&nbsp;
                        {*<a href="javascript:void(0)" onclick="opa_st(document.getElementById('video_form'),1);return false" class="for3">Видео</a>&nbsp;*}
                        {if $_system_user.userID >0}
                            &nbsp;<a href="javascript:;;" onclick="opa_st(document.getElementById('spec_form'),1);return false" class="for3"><strong>В ТОП</strong></a>
                        {/if}
                    </span><br class="clear"><span class="error" id='err_formMessage_imageString'></span>
                    {if $current_group.is_mat}<h4 style="padding:0px 0px 5px 0px;margin:0px;">Данная группа является матоязычной. Уберите от экранов детей, младше 21 года!</h4>{/if}

                </div>
            </div>
            <div class="form_box_btn" id="captcha_div">
                <br style="clear:both" />
                {*<div class="form_box_btn_112" id='web1'>*}
                {if $_system_user.is_admin || $isGroupAdministrator || $isThemeOwner}
                {else}
                    {if $captcha}
                        <span><b>Проверочный код:</b></span>
                            <div class="format"><div id="web">
                            <img id='randomImage' src="/antibot.php?{php} echo time();{/php}" width="190" height="30">
                            </div></div>
                        <input type="text" name="imageString" class="inp_text_name_span" style="width:70px;"  maxlength="7" id="capthaString">
                    {/if}
                {/if}
                {*</div>*}
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
                    {php}
                    for ($i=1;$i<=MAX_FILES_UPLOAD;$i++) {
                        echo '<div class="file_input"><input type="file" name="file_'.$i.'" id="picfile_'.$i.'"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_'.$i.'"></a></div>';
                    }
                    {/php}
                    {*
                    <div class="file_input"><input type="file" name="file_1" id="picfile_1"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_1"></a></div>

                    <div class="file_input"><input type="file" name="file_2" id="picfile_2"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_2"></a></div>

                    <div class="file_input"><input type="file" name="file_3" id="picfile_3"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_3"></a></div>

                    <div class="file_input"><input type="file" name="file_4" id="picfile_4"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_4"></a></div>

                    <div class="file_input"><input type="file" name="file_5" id="picfile_5"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_5"></a></div>

                    <div class="file_input"><input type="file" name="file_6" id="picfile_6"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_6"></a></div>
                    *}

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
                    </table>
                    <br style="clear:both" />
                        <div class="box_barr" style="float:right;margin-top:2px">
                            <a href="javascript:;;" onclick="opa_st(document.getElementById('spec_form'),0);return false">Закрыть</a>
                        </div>
                    </div>
                {/if}


        </div>
    </div>
    </form>

</div>
<div id="ajax_loader"><img src="/images/ajax-loader.gif" alt=""></div>
{/if}
{literal}
<script language="javascript" type="text/javascript">
<!--
    var fombj = document.getElementById( 'formMessage' );
//-->
</script>
{/literal}

