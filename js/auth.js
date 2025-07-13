function LoadAuthorization(){
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	
	$("#login_result").html('').removeClass();
	var top = $(window).height()/2 + document.documentElement.scrollTop;
	$("#login_box").css({ "top": top });
	$("#login_box").css('display','block');		
	$("select").css('visibility','hidden');		
	$("div.box_brn").css('visibility','hidden');
	
	//e.preventDefault();
}


function HideAuth(){
	$("#overlay").css('display','none');
	$("#login_box").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	
	//e.preventDefault();
}
	
	
$(document).ready(function(){
						   
	/* begin user authorization */						   
	$(".close_btn, #overlay").click(function(e){
		$("#overlay").css('display','none');
		$("#login_box").css('display','none');
		$("select").css('visibility','visible');
		$("div.box_brn").css('visibility','visible');
		
		//e.preventDefault();
	});
	
	$(document).keypress(function(e){
    	if (e.keyCode==27) HideAuth();
	});
    $('#login_form').submit(function(){
        $(this).ajaxSubmit({
            type:'post',
            target:"#login_result",
            beforeSubmit: onAuthCheck,
            success : onAuthResultForm,
            async: true,
            dataType : 'json',
            timeout: 1000,
            error: onAuthError,
            notsuccess: onAuthError
        });
        return false;
    });
    function onAuthCheck (response, statusText, xhr, form) {
        $("#login_result").removeClass().addClass('process').text('Проверка....').fadeIn(1000);
        //alert('PRE');
        return true;
    }
    function onAuthResultForm(response, statusText, xhr, form) {
        //alert('POST: '.statusText);
        if (statusText == 'success') {
            if (response.submitOn) {
                if (response.redirectUrl) {
                    window.location = response.redirectUrl;
                }
                $("#login_result").fadeTo(200,0.1,function(){
                    $(this).html('Успешно').removeClass().addClass('success').fadeTo(900,1);
                });
            }
            if (response.errors) {
                for ( var ctrlErr in response.errors) {
                    $('#login_result').html(response.errors[ctrlErr]).show();
                }
            }
        }
    }
    function onAuthError(response, statusText, xhr, form) {
        //alert(response.errors);
        if (statusText=='timeout') {
            if (response.submitOn) {
                if (response.redirectUrl) {
                    window.location = response.redirectUrl;
                }
                $("#login_result").fadeTo(200,0.1,function(){
                    $(this).html('Успешно').removeClass().addClass('success').fadeTo(900,1);
                });
            }
            if (response.errors) {
                for ( var ctrlErr in response.errors) {
                    $('#login_result').html(response.errors[ctrlErr]).show();
                }
            }
        } else {
            if (response.errors) {
                for ( var ctrlErr in response.errors) {
                    $('#login_result').html(response.errors[ctrlErr]).show();
                }
            }
        }
    }
	$(window).scroll(function(e){
		if ( $("#login_box").is(":block") )
		{
			var top = $(window).height()/2 + document.documentElement.scrollTop;
			$("#login_box").css({ "top": top });
		}
	});
	
	/* end user authorization */	
	
});


