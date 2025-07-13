
<html> 
<head> 
    <title>ПЕЙДЖЕР | {$user_info.user_name}</title> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    
    <meta http-equiv="cache-control" content="no-cache"> 
    <meta http-equiv="pragma" content="no-cache"> 

    <link rel="stylesheet" type="text/css" href="/style/dialog.css"> 
    <link rel="stylesheet" type="text/css" href="/style/style.css">
    <link rel="stylesheet" type="text/css" href="/style/jquery.lightbox-0.5.css" >

	<script type="text/javascript" src="/js/jquery-1.3.2.js"></script>
    <script type="text/javascript" src="/js/jquery.form.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="/js/jquery.lightbox.js"></script>
    <script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="/js/script.js"></script>
	<script type="text/javascript" src="/js/testbbcode.js"></script>
{if $is_mobile eq 'mobile'}
    <link href="/style/mobile.css?t={php} echo date('dmY');{/php}" type="text/css" rel="stylesheet">
    <meta name="viewport" content="width=540px; initial-scale=1.0, minimum-scale=0.25, maximum-scale=2.0">
{/if}

{literal}
    <script type="text/javascript">
$(document).ready(function(){
    ReloadMess({/literal}{if $_page>1}{$_page}{else}1{/if}{literal},true);
    $("#clear_file_1").click(function(event){
        event.preventDefault();
        $("#picfile_1").replaceWith("<input type='file' name='file_1' id='picfile_1'>");
        $('#file_1_hidden').value('1');
    });
    $("#clear_file_2").click(function(event){
        event.preventDefault();
        $("#picfile_2").replaceWith("<input type='file' name='file_2' id='picfile_2'>");
        $('#file_2_hidden').value('1');
    });
    $("#clear_file_3").click(function(event){
        event.preventDefault();
        $("#picfile_3").replaceWith("<input type='file' name='file_3' id='picfile_3'>");
        $('#file_3_hidden').value('1');
    });
    $('#formPagerMessage').submit(function(){
        $(this).ajaxSubmit({
            type:'post',
            target:"#err_formMessage_system_div",
            beforeSubmit: onAjaxRequestForm,
            success : onAjaxSubmitPagerForm,
            async: false,
            dataType : 'json',
            timeout: 5000,
            error: errorPagerAjax,
            notsuccess: errorPagerAjax
            });
        return false;
    });
});
var sAjx = true;
function ReloadMess(page,reload) {

    if ($(window).scrollTop()<20 && !$("#refreshIn").is(':checked')) {
        if($('.text_box_1_dialog').offset().top >= 0)
        {
            if (page>1) {
                $('#dialog_box').load('/pager/dialog/{/literal}{$_userto}{literal}/?page='+page+'&a='+Math.random(666666));
            }
            else
            {
                $('#dialog_box').load('/pager/dialog/{/literal}{$_userto}{literal}/?a='+Math.random(6666666));
            }
        }
    }
    setTimeout(function() { ReloadMess(page,false); }, 60000);
}

ie4 = (document.all)? true:false;
lastKey = 0;
textFocus = false;

{/literal}
    {if $_system_user.banned neq 1}
        {literal}
        function addMessage()
        {
            var currentDate = new Date();
            $('#mess_text').val('');
            $('.error').html('');
            ReloadMess(1,ReloadMess);
        }

        function submitAction() {
            $('#formPagerMessage').submit();
            $('#s_emo').hide();
            $('#files_form').hide();
            $('#video_form').hide();
        }
        {/literal}
    {/if}
{literal}
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
{/literal}

</head>
<body style="min-width: 300px">

<div class="head" >
	<div class="user_info">
	{php}
	$filename = HOME_DIR.'upload/resized-'.$this->_tpl_vars['_userto'].'.jpg';
	$small_filename = HOME_DIR.'upload/avatar-'.$this->_tpl_vars['_userto'].'.jpg';
	$full_file = '/upload/resized-'.$this->_tpl_vars['_userto'].'.jpg';
	$small_file = '/upload/avatar-'.$this->_tpl_vars['_userto'].'.jpg';
	if (file_exists($filename)) {
	    echo '<a href="'.$full_file.'" targer=_blank>';
	    if (file_exists($small_filename)) {
    	    echo '<img src="'.$small_file.'" align="left" class="user_info_img">';
	    } else {
	        echo '<img src="'.$full_file.'" width=72 align="left" class="user_info_img">';
	    }
	    echo '</a>';
    } else {
        echo '<img src="/images/user_pic.gif" align="left" class="user_info_img">';
    }
	{/php}
        <h3 style="text-align: center"><a href="#">{$user_info.user_name}</a></h3>
        <span class="user_info_date">был на FTR: {$user_info.lastlogin|date_format:"%d/%m/%Y %H:%M"}</span>
        <!-- <span class="user_info_date_act">сейчас на сайте</span> -->
    </div>
    <div class="dialog_brn_box" id="ajax_form_message">
    	{* {include file="forum/banner/pager.tpl"} *}
        <div class="dialog_answer_box">
            <div class="dialog_answer_box_form">
            {if $_system_user.banned neq 1}
            <form action="" method="post" class="form_dialog" id="formPagerMessage">
            <input type="hidden" name="event" value="forumpagercreatemess">
                <textarea class="area_dialog_text" name="pagermess[content]" id='mess_text'></textarea>
                <input type="button" class="btn_dialog_pager" value="Отправить" onclick="submitAction(); return false">
                    <span class="left">
                        <a href="javascript:void(0)" onclick="doInsert('[b]','[/b]', true); return false;" class="for1" id="bold">Жирный</a>&nbsp;
                        <a href="javascript:void(0)" onclick="doInsert('[i]','[/i]', false); return false;" class="for2">Курсив</a>&nbsp;
                        <a href="javascript:void(0)" onclick="doInsert('[re]','[/re]', false); return false;" class="for3">Цитата</a>&nbsp;
                        <a href="javascript:void(0)" onclick="opa_st(document.getElementById('s_emo'),1);return false" class="for3">Смайлы</a>&nbsp;
                        {if $user_info.danger_level >= 0}
                            <a href="javascript:void(0)" onclick="opa_st(document.getElementById('files_form'),1);return false" class="for3">Изображения</a>&nbsp;
                        {/if}
                        {*<a href="javascript:void(0)" onclick="opa_st(document.getElementById('video_form'),1);return false" class="for3">Видео</a>&nbsp;*}
                    </span>
                    <br class="clear">
                    <label><input type="checkbox" id="refreshIn">Отключить автообновление</label>
                    <br class="clear"><span class="error" id='err_formMessage_imageString'></span>
                        <br style="clear:both" />
                        <div id="s_emo" class="box_emtn_pgr">
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
                        {if $user_info.danger_level >= 0}
                        <div id="files_form" class="box_fls_pgr">
                            <div class="file_input"><input type="file" name="file_1" id="picfile_1"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_1"></a></div>

                            <div class="file_input"><input type="file" name="file_2" id="picfile_2"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_2"></a></div>

                            <div class="file_input"><input type="file" name="file_3" id="picfile_3"></div><div class="image_del"><a href="javascript:void(0)"><img src="/images/btn_close.gif" alt="Очистить" title="Очистить" id="clear_file_3"></a></div>

                            <br style="clear:both" />
                            <div class="box_barr" style="float:right;margin-top:2px">
                                <a href="javascript:;;" onclick="opa_st(document.getElementById('files_form'),0);return false">Закрыть</a>
                            </div>
                        </div>
                        {/if}

            </form>
            {/if}
            </div>
        </div>
    </div>
    <div id="ajax_pager_loader"><img src="/images/ajax-loader.gif" alt=""></div>
</div>


<div class="dialog_box" id="dialog_box">

</div>
<br>

{literal}
<script language="javascript" type="text/javascript">
<!--
	var fombj = document.getElementById( 'formPagerMessage' );
//-->
</script>
{/literal}
<center>

</center>
</html>

