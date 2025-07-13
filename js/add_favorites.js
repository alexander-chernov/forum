function LoadAddFavorites(themeid)
{
	var overlay = $("#overlay");
	var w = $(document).width();
	var h = $(document).height();
	overlay.css('width', w);
	overlay.css('height', h);
	overlay.css('display','block');
	
	var top = $(window).height()/2 + document.documentElement.scrollTop;
	$("#add_favorites_box").css({ "top": top });
	$("#add_favorites_box").css('display','block');		
	$("select").css('visibility','hidden');		
	$("div.box_brn").css('visibility','hidden');
	$("#add_favorites_result").load('/auth/?event=favoriteuserthemeadd&theme='+themeid,function(text){
	$("#add_favorites_result").html('');
	var favorites = $("#fav").html();
	favorites++;
	$("#fav").html(favorites);
	HideAddFavorites();
	});
	e.preventDefault();
}

function HideAddFavorites(e){
	$("#overlay").css('display','none');
	$("#add_favorites_box").css('display','none');
	$("select").css('visibility','visible');
	$("div.box_brn").css('visibility','visible');
	e.preventDefault();		
}	
	
	
$(document).ready(function(){

	$(".close_btn, #overlay").click(function(e){
		HideAddFavorites(e);
	});
		
	$(document).keypress(function(e){
    	if (e.keyCode==27) HideAddFavorites();
	});
	
});

