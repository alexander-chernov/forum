function LoadRestorePassw(){
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	$("#login_box").css('display','none');	
	$("select").css('visibility','hidden');		
	$("div.box_brn").css('visibility','hidden');
	
	var top = $(window).height()/2 + document.documentElement.scrollTop;
	$("#restore_passw_box").css({ "top": top });
	$("#restore_passw_box").css('display','block');	
	
	//e.preventDefault();
}


function HideRestorePassw(){
	$("#restore_passw_result").text('');
	$("#restorepasswd").val('');
	$("#overlay").css('display','none');
	$("#restore_passw_box").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	
	//e.preventDefault();
}

function restorePassAction() {

    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (filter.test($('#restorePasswD1').val())) {
        $.get('/auth/recovery/?email='+$('#restorePasswD1').val(), function(data) {
            alert(data);
            //$('#restore_passw_result').text(data);
        });
    } else {
        alert('некоректный email');
    }


}
	
$(document).ready(function(){
						   
	/* begin user authorization */						   
	$(".close_btn, #overlay").click(function(e){
		HideRestorePassw();
	});
	
	$(document).keypress(function(e){
    	if (e.keyCode==27) HideRestorePassw();
	});
			
	$("#submit_restore_passw").click(function() {
		// указываем класс process для div-а сообщений и плавно показываем его
		$("#restore_passw_result").text('Проверка....').fadeIn(1000);
		// проверяем через AJAX имя пользователя пароль
		var email=document.getElementById('restorepasswd').value;
		$("#restore_passw_result").load('/auth/recovery/?email='+email);
		return false;// отмена отправки формы (действие по умолчанию)
	});
	
	$(window).scroll(function(e){
		if ( $("#restore_passw_box").is(":block") )
		{
			var top = $(window).height()/2 + document.documentElement.scrollTop;
			//$("#user_info_box").stop(); 
			//$("#login_box").animate( {top: "(0px)"} ,"fast");
			$("#restore_passw_box").css({ "top": top });
		}
	});
	
	/* end user authorization */	
	
});


