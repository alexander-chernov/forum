function LoadComplaint(mid, author)
{	
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	
	var top = $(window).height()/2 + document.documentElement.scrollTop;
	$("#complaint_box").css({ "top": top });
	$("#complaint_box").css('display','block');		
	$("select").css('visibility','hidden');		
	$("#complaint_box select").css('visibility','visible');	
	$("div.box_brn").css('visibility','hidden');
	
	var msg = $('#message_' + mid).html();
	$('#complaint_msg').html(msg.substring(0, 100));
	$('#complaint_nick').html(author);
	
	$("#complaint_result").html('');
	$('#message_id').val(mid);
	//e.preventDefault();
}


function HideComplaint(e){
	$("#overlay").css('display','none');
	$("#complaint_box").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	
	//e.preventDefault();
}
	
	
$(document).ready(function(){
						   					   
	$(".close_btn, #overlay").click(function(e){
		HideComplaint(e);		
	});
	
	$(document).keypress(function(e){
    	if (e.keyCode==27) HideComplaint(e);	
	});
			
	$("#complaint_form").submit(function() {
		if ($('#rule_id').val() != 0) {
			$("#complaint_result").html("Отправка запроса...");
		
			$("#complaint_result").load('/forum/complaint/'
				+ '?messageID=' + $('#message_id').val() 
				+ '&ruleID=' + $('#rule_id').val(), null,
				function (responseText, textStatus, XMLHttpRequest) {
					setTimeout('HideComplaint()', 2000);
				}
			);
			return false;
		}
		else {
			alert('Выберите правило из списка!');
		}
	});
	
	$(window).scroll(function(e){
		if ( $("#complaint_box").is(":block") )
		{
			var top = $(window).height()/2 + document.documentElement.scrollTop;
			$("#complaint_box").css({ "top": top });
		}
	});
		
});
