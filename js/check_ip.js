function LoadCheckIp()
{
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	
	$("#login_result").html('').removeClass();
	var top = $(window).height()/2 + document.documentElement.scrollTop;
	$("#check_ip_box").css({ "top": top });
	$("#check_ip_box").css('display','block');		
	$("select").css('visibility','hidden');		
	$("div.box_brn").css('visibility','hidden');
	
	//e.preventDefault();
}

function HideCheckIp(){
	$("#checkipname").val('');
	$(".check_ip_result").html('');
	$("#check_ip_box").css('height', '204px');
	$("#check_ip_box_1").css('height', '200px');
	$("#overlay").css('display','none');
	$("#check_ip_box").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	
	//e.preventDefault();		
}	
	
$(document).ready(function(){

	/* begin user info */
	$(".close_btn, #overlay").click(function(e){
		HideCheckIp();
	});
		
	$(document).keypress(function(e){
    	if (e.keyCode==27) HideCheckIp();
	});
	
	$("#check_ip_form").submit(function() {
		// указываем класс process для div-а сообщений и плавно показываем его
		//$("#check_ip_form").removeClass().addClass('process').text('Проверка....').fadeIn(1000);
		$("#check_ip_box").css('height', '304px');
		$("#check_ip_box_1").css('height', '300px');
		$(".check_ip_result").html('<div></div>');
		$(".check_ip_result div").text('Проверка....').fadeIn(1000);
		// проверяем через AJAX имя пользователя пароль
		var ourDate = new Date( );
		date_rand = ourDate.getUTCMilliseconds( );
		$(".check_ip_result div").load('/banned/checkipnick/?request='+document.getElementById('checkipname').value+'&s='+date_rand);
		return false;// отмена отправки формы (действие по умолчанию)
	});
		
	$(window).scroll(function(e){
		if ( $("#check_ip_box").is(":block") )
		{
			var top = $(window).height()/2 + document.documentElement.scrollTop;
			//$("#user_info_box").stop(); 
			//$("#user_info_box").animate( {top: "(0px)"} ,"fast");
			$("#check_ip_box").css({ "top": top });
		}
	});
	
	/* end user info */
	
});

function pasteDataAndSubmitCheck(data) {
	$("#checkipname").val(data);
	LoadCheckIp();
	$("#check_ip_form").submit();
}