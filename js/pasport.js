function LoadPassport(user)
{
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	var ourDate = new Date( );
	date_rand = ourDate.getUTCMilliseconds( );
	var top = $(window).height()/2 + $(document).scrollTop();
	$("#user_info_box").css('top', top);
	//$("#user_info_box").show(500);
	//$("#user_info_box_brn").show(1);	
	$("#user_info_box").css('display','block');
	//$("#user_info_box_brn").css('display','block');
	$("select").css('visibility','hidden');		
	$("div.box_brn").css('visibility','hidden');
	$("#user_info_box").html('');
	$("#user_info_box_brn").html('');
	$("#user_info_box").load('/personal/passport/'+user+'/?g='+date_rand+'&uid='+user);
		
	//e.preventDefault();	
}

function HidePasp(e){
	$("#overlay").css('display','none');
	$("#user_info_box").css('display','none');
	$("#user_info_box").html='';
	$("#user_info_box_brn").html('');
	//$("#user_info_box_brn").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	
	//e.preventDefault();		
}	


$(document).ready(function(){

	/* begin user info */

	$("#pasport_close_btn, #overlay").click(function(e){
		HidePasp(e);
	});
		
	$(document).keypress(function(e){
    	if (e.keyCode==27) HidePasp();
	});
	
	
	$(window).scroll(function(e){
		if ( $("#user_info_box").is(":block") )
		{
			var top = $(window).height()/2 + document.documentElement.scrollTop;
			//$("#user_info_box").stop(); 
			//$("#user_info_box").animate( {top: "(0px)"} ,"fast");
			$("#user_info_box").css({ "top": top });
		}
	});
	
	/* end user info */
	
});

function ShowUserDialog(popwindowURL)
{
	var win = window.open("/pager/udialog/"+popwindowURL+"/", "page", "width=800px,height=650px,location=0,status=1,toolbar=0,scrollbars=1,resizable=1");
	if (win.focus) win.focus();
	return false;
}