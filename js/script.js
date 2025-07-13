function editMessage(messageId,pay)
{
	$.getJSON('/mess/'+messageId+'/?action=edit',
		function (response)
		{
			if (response.errors){
				$(document).find('.error').empty();
				for (x in response.errors)
				{
					$('#err_edit_'+messageId+'_'+x).html(response.errors[x]);
				}
			}
			if (response.messageID) {
				$(document).find('.error').empty();
				$('#message_'+messageId).html('<textarea style="width: 731px; height: 156px;" id="content-'+messageId+'">'+response.content+'</textarea>');
				$('#editbut-'+messageId).html('<a href="javascript:void(0)" onclick="saveMessage('+messageId+','+pay+')"><img src="/images/accept.png" alt="Сохранить" title="Сохранить" class="to_head" width="16" height="16"></a>&nbsp;<a href="javascript:void(0)" onclick="cancelEditMessage('+messageId+','+pay+')"><img src="/images/remove.png" alt="Отменить" title="Отменить" class="to_head" width="16" height="16"></a>');
			} 
		});
}

function cancelEditMessage(messageId,pay)
{
	$.getJSON('/mess/'+messageId+'/?action=edit&type=cancle',
		function (response)
		{
			if (response.messageID) {
				$(document).find('.error').empty();
				$('#message_'+messageId).html(response.formattedcontent);
				$('#editbut-'+messageId).html('<a href="javascript:void(0)" onclick="editMessage('+messageId+','+pay+')"><img src="/images/notes_edit.png" alt="Редактировать" title="Редактировать" class="to_head" width="16" height="16"></a>');
			} 
		});
}
function saveMessage(messageId, pay)
{
	if (confirm('Вы уверены что хотите изменить сообщение? Это стоит '+pay+'р.')){
		var messagecontent = $('#content-'+messageId+'').val();
		$.post('/mess/'+messageId+'/?action=edit',
				{
					content:messagecontent
				},
				function (result)
				{
					var response = (result);
					if (response.errors){
						$(document).find('.error').empty();
						for (x in response.errors)
						{
							$('#err_edit_'+messageId+'_'+x).html(response.errors[x]);
						}
					}
					if (response.messageID) {
						$(document).find('.error').empty();
						$('#message_'+messageId).html(response.formattedcontent);
						$('#editbut-'+messageId).html('<a href="javascript:void(0)" onclick="editMessage('+messageId+','+pay+')"><img src="/images/notes_edit.png" alt="Редактировать" title="Редактировать" class="to_head" width="12" height="12"></a>');
						$('#user_balance').html('('+response.user_balance.toFixed(2)+' руб.)');
					} 
				},'json');
	}else {
		cancleEditMessage(messageId,pay);
	}
}
function showRatingLog(messageId,x,y)
{
	var myalt = $('#ratinglog')
	myalt.css({'left':x});
	myalt.css({'top':y});
	message = messageId.substr(7,messageId.length-7);
	if(true){
	    myalt.load('/mess/'+message+'/?action=showRate');
	    myalt.show();
	    hide=false;
	}
    //if ($('#m_'+messageId).is(':hidden')) {
    $('#m_'+messageId).show();
    //}
}
function trim(s)
{
    return trimstr(s);
}
function trimstr(str){
   return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '').replace("'", '');
}
/*
function LoadComplaint(messageId) {
    $.getJSON('/mess/'+messageId+'/?action=setRedZone',
        function (response){
            if (response.errors) {
                $(document).find('.error').empty();
                $('#err_'+messageId+'_rating').html(response.errors.result);
            } else {
                $('#err_'+messageId+'_rating').html(result);
            }
        });
}
*/

function addRating(messageId,rValue)
{
    var ratingValue = 0;
    if (rValue>0) {
        ratingValue = 1;
    } else {
        ratingValue = -1;
    }
	$.getJSON('/mess/'+messageId+'/?action=setRate&rate='+ratingValue,
		  function (response){
			if (response.errors) {
				$(document).find('.error').empty();
				$('#err_'+messageId+'_rating').html(response.errors.rating);
			} else {
				var rating = response.rating;
                //alert(rating);
                var half_hide = response.half_hide;
                //alert(half_hide);
                var hide = response.hide;
                //alert(hide);
                if (rating<=half_hide) {
                    $('#m_'+messageId).hide();
                } else {
                    if ($('#m_'+messageId).is(':hidden')) {
                        $('#m_'+messageId).show();
                    }
                }
                if (rating<=hide) {
                    $('#'+messageId).hide();
                }
                //alert(rating);                
                if (rating>0) {
                    rating = '+'+ratingValue;
                }
                //alert(ratingValue);
                //alert(messageId);
				$('#rating_'+messageId).html(rating);
			}
		  });
}
function CheckValue(thisname, sourse_text)
{
    var keyword_value = trim(thisname.value);
    if (keyword_value=='')
        thisname.value=sourse_text;
}
function HideValue(thisname, sourse_text)
{
    var keyword_value = trim(thisname.value);
    if (keyword_value==sourse_text)
        thisname.value='';
}
function errorAjax(response, statusText, xhr, form) {
    if (response.errors) {
        if ($('#randomImage')) {
            $('#randomImage')
                    .attr('src', '/antibot.php?u=' + Math.random());
        }
        $(form).find('.fs_error').empty();
        $(form).find('.error').empty();
        for ( var ctrlErr in response.errors) {
            $('#err_formMessage_imageString').html(response.errors[ctrlErr]).show();
        }
    }
}
function errorPagerAjax(response, statusText, xhr, form) {
    if (response.errors) {
        $(form).find('.fs_error').empty();
        $(form).find('.error').empty();
        for ( var ctrlErr in response.errors) {
            $('#err_formMessage_imageString').html(response.errors[ctrlErr]).show();
        }
    }
}


function onAjaxRequestForm (response, statusText, xhr, form) {
    $('#ajax_form_message').hide();
    $('#ajax_loader').show();
    return true;
}
function onAjaxSubmitForm(response, statusText, xhr, form) {
    var currentDate = new Date();
    $("#picfile_1").replaceWith("<input type='file' name='file_1' id='picfile_1'>");
    $("#picfile_2").replaceWith("<input type='file' name='file_2' id='picfile_2'>");
    $("#picfile_3").replaceWith("<input type='file' name='file_3' id='picfile_3'>");
    $("#picfile_4").replaceWith("<input type='file' name='file_4' id='picfile_4'>");
    $("#picfile_5").replaceWith("<input type='file' name='file_5' id='picfile_5'>");
    $("#picfile_6").replaceWith("<input type='file' name='file_6' id='picfile_6'>");
    $("#picfile_7").replaceWith("<input type='file' name='file_7' id='picfile_7'>");
    $("#picfile_8").replaceWith("<input type='file' name='file_8' id='picfile_8'>");
    $("#picfile_9").replaceWith("<input type='file' name='file_9' id='picfile_9'>");
    $("#picfile_10").replaceWith("<input type='file' name='file_10' id='picfile_10'>");
    $("#picfile_11").replaceWith("<input type='file' name='file_11' id='picfile_11'>");
    $("#picfile_12").replaceWith("<input type='file' name='file_12' id='picfile_12'>");
    $("#picfile_13").replaceWith("<input type='file' name='file_13' id='picfile_13'>");
    $("#picfile_14").replaceWith("<input type='file' name='file_14' id='picfile_14'>");
    $("#picfile_15").replaceWith("<input type='file' name='file_15' id='picfile_15'>");
    $("#picfile_16").replaceWith("<input type='file' name='file_16' id='picfile_16'>");
    $("#picfile_17").replaceWith("<input type='file' name='file_17' id='picfile_17'>");
    $("#picfile_18").replaceWith("<input type='file' name='file_18' id='picfile_18'>");
    $("#picfile_19").replaceWith("<input type='file' name='file_19' id='picfile_19'>");
    $("#picfile_20").replaceWith("<input type='file' name='file_20' id='picfile_20'>");
    $("#picfile_21").replaceWith("<input type='file' name='file_21' id='picfile_21'>");

    if (statusText == 'success') {
		if (response.submitOn) {
			if (response.redirectUrl) {
				if (response.openerOn) {
					window.opener.location = response.redirectUrl;
					window.opener.location.reload();
					window.close();
				} else {
					window.location = response.redirectUrl;
				}
			} else if (response.reloadOn) {
				if (typeof reloadPage == 'function') {
					closeDialogForm('fs_dialogForm');
					reloadPage(window.location.href, response);
				} else {
					window.location.reload();
				}
			} else if (response.callFunc) {
				try {
					(function(e){
						var e = response;
						eval(response.callFunc + '(e);');
					})();
				} catch (e) {
				}
			} else {
				closeDialogForm('fs_dialogForm');
				if (response.dirsTree) {
					dirsTree = response.dirsTree;
					buildDirsTree();
				}
			}
            if (response.author) {
                $('#name').val(response.author);
                $.cookie("_cookie_name", response.author);
            }
            if (response.authorID) {
                $('#capthaString').val('');
                $('#web1').hide();
                $('#web').html('');
                $('#captcha_div').hide();
            }
		}
		if (response.errors) {
			if ($('#randomImage')) {
				$('#randomImage')
						.attr('src', '/antibot.php?u=' + Math.random());
			}
			$(form).find('.fs_error').empty();
			$(form).find('.error').empty();
			for ( var ctrlErr in response.errors) {
                //alert(response.errors[ctrlErr]);
                $('#err_formMessage_imageString').html(response.errors[ctrlErr]).show();
				$('#err_' + $(form).attr('id') + '_' + ctrlErr).html(
						response.errors[ctrlErr]).show();
			}
		}
        $('#ajax_form_message').show();
        $('#ajax_loader').hide();
    }
}
function onAjaxSubmitForm2(response, statusText, xhr, form) {
    var currentDate = new Date();
    $("#picfile_1").replaceWith("<input type='file' name='file_1' id='picfile_1'>");
    $("#picfile_2").replaceWith("<input type='file' name='file_2' id='picfile_2'>");
    $("#picfile_3").replaceWith("<input type='file' name='file_3' id='picfile_3'>");
    $("#picfile_4").replaceWith("<input type='file' name='file_4' id='picfile_4'>");
    $("#picfile_5").replaceWith("<input type='file' name='file_5' id='picfile_5'>");
    $("#picfile_6").replaceWith("<input type='file' name='file_6' id='picfile_6'>");
    $("#picfile_7").replaceWith("<input type='file' name='file_7' id='picfile_7'>");
    $("#picfile_8").replaceWith("<input type='file' name='file_8' id='picfile_8'>");
    $("#picfile_9").replaceWith("<input type='file' name='file_9' id='picfile_9'>");
    $("#picfile_10").replaceWith("<input type='file' name='file_10' id='picfile_10'>");
    $("#picfile_11").replaceWith("<input type='file' name='file_11' id='picfile_11'>");
    $("#picfile_12").replaceWith("<input type='file' name='file_12' id='picfile_12'>");
    $("#picfile_13").replaceWith("<input type='file' name='file_13' id='picfile_13'>");
    $("#picfile_14").replaceWith("<input type='file' name='file_14' id='picfile_14'>");
    $("#picfile_15").replaceWith("<input type='file' name='file_15' id='picfile_15'>");
    $("#picfile_16").replaceWith("<input type='file' name='file_16' id='picfile_16'>");
    $("#picfile_17").replaceWith("<input type='file' name='file_17' id='picfile_17'>");
    $("#picfile_18").replaceWith("<input type='file' name='file_18' id='picfile_18'>");
    $("#picfile_19").replaceWith("<input type='file' name='file_19' id='picfile_19'>");
    $("#picfile_20").replaceWith("<input type='file' name='file_20' id='picfile_20'>");
    $("#picfile_21").replaceWith("<input type='file' name='file_21' id='picfile_21'>");

    $('#ajax_form_message').show();
    $('#ajax_loader').hide();

    if (statusText == 'success') {
        if (response.submitOn) {
            if (response.redirectUrl) {
                if (response.openerOn) {
                    window.opener.location = response.redirectUrl;
                    window.opener.location.reload();
                    window.close();
                } else {
                    window.location = response.redirectUrl;
                }
            } else if (response.reloadOn) {
                if (typeof reloadPage == 'function') {
                    closeDialogForm('fs_dialogForm');
                    reloadPage(window.location.href, response);
                } else {
                    window.location.reload();
                }
            } else if (response.callFunc) {
                try {
                    (function(e){
                        var e = response;
                        eval(response.callFunc + '(e);');
                    })();
                } catch (e) {
                }
            } else {
                closeDialogForm('fs_dialogForm');
                if (response.dirsTree) {
                    dirsTree = response.dirsTree;
                    buildDirsTree();
                }
            }
            if (response.author) {
                $('#name').val(response.author);
                $.cookie("_cookie_name", response.author);
            }
            if (response.authorID) {
                $('#capthaString').val('');
                $('#web1').hide();
                $('#web').html('');
                $('#captcha_div').hide();
            }
        }
        if (response.errors) {
            if ($('#randomImage')) {
                $('#randomImage')
                    .attr('src', '/antibot.php?u=' + Math.random());
            }
            $(form).find('.fs_error').empty();
            $(form).find('.error').empty();
            for ( var ctrlErr in response.errors) {
                //alert(response.errors[ctrlErr]);
                $('#err_formMessage_imageString').html(response.errors[ctrlErr]).show();
                $('#err_' + $(form).attr('id') + '_' + ctrlErr).html(
                    response.errors[ctrlErr]).show();
            }
        }
    }
}

function onAjaxRequestPagerForm (response, statusText, xhr, form) {
    $('#ajax_form_message').hide();
    $('#ajax_pager_loader').show();
    return true;
}
function onAjaxSubmitPagerForm(response, statusText, xhr, form) {
    var currentDate = new Date();
    $("#picfile_1").replaceWith("<input type='file' name='file_1' id='picfile_1'>");
    $("#picfile_2").replaceWith("<input type='file' name='file_2' id='picfile_2'>");
    $("#picfile_3").replaceWith("<input type='file' name='file_3' id='picfile_3'>");
    $("#picfile_4").replaceWith("<input type='file' name='file_4' id='picfile_4'>");
    $("#picfile_5").replaceWith("<input type='file' name='file_5' id='picfile_5'>");
    $("#picfile_6").replaceWith("<input type='file' name='file_6' id='picfile_6'>");
    $("#picfile_7").replaceWith("<input type='file' name='file_7' id='picfile_7'>");
    $("#picfile_8").replaceWith("<input type='file' name='file_8' id='picfile_8'>");
    $("#picfile_9").replaceWith("<input type='file' name='file_9' id='picfile_9'>");
    $("#picfile_10").replaceWith("<input type='file' name='file_10' id='picfile_10'>");
    $("#picfile_11").replaceWith("<input type='file' name='file_11' id='picfile_11'>");
    $("#picfile_12").replaceWith("<input type='file' name='file_12' id='picfile_12'>");
    $("#picfile_13").replaceWith("<input type='file' name='file_13' id='picfile_13'>");
    $("#picfile_14").replaceWith("<input type='file' name='file_14' id='picfile_14'>");
    $("#picfile_15").replaceWith("<input type='file' name='file_15' id='picfile_15'>");
    $("#picfile_16").replaceWith("<input type='file' name='file_16' id='picfile_16'>");
    $("#picfile_17").replaceWith("<input type='file' name='file_17' id='picfile_17'>");
    $("#picfile_18").replaceWith("<input type='file' name='file_18' id='picfile_18'>");
    $("#picfile_19").replaceWith("<input type='file' name='file_19' id='picfile_19'>");
    $("#picfile_20").replaceWith("<input type='file' name='file_20' id='picfile_20'>");
    $("#picfile_21").replaceWith("<input type='file' name='file_21' id='picfile_21'>");
    if (statusText == 'success') {
		if (response.submitOn) {
			if (response.redirectUrl) {
				if (response.openerOn) {
					window.opener.location = response.redirectUrl;
					window.opener.location.reload();
					window.close();
				} else {
					window.location = response.redirectUrl;
				}
			} else if (response.reloadOn) {
				if (typeof reloadPage == 'function') {
					closeDialogForm('fs_dialogForm');
					reloadPage(window.location.href, response);
				} else {
					window.location.reload();
				}
			} else if (response.callFunc) {
				try {
					(function(e){
						var e = response;
						eval(response.callFunc + '(e);');
					})();
				} catch (e) {
				}
			} else {
				closeDialogForm('fs_dialogForm');
				if (response.dirsTree) {
					dirsTree = response.dirsTree;
					buildDirsTree();
				}
			}
		}
		if (response.errors) {
			$(form).find('.fs_error').empty();
			$(form).find('.error').empty();
			for ( var ctrlErr in response.errors) {
                //alert(response.errors[ctrlErr]);
                $('#err_formMessage_imageString').html(response.errors[ctrlErr]).show();
				$('#err_' + $(form).attr('id') + '_' + ctrlErr).html(
						response.errors[ctrlErr]).show();
			}
		}
        $('#ajax_form_message').show();
        $('#ajax_pager_loader').hide();
    }
}
