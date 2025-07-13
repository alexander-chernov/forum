{include file="forum/header.tpl"}
<script type="text/javascript" src="/js/jquery.imgareaselect-0.3.min.js"></script>

{literal}
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = {/literal}{$_thumb_width}{literal} / selection.width; 
	var scaleY = {/literal}{$_thumb_height}{literal} / selection.height; 
	
	$('#preview').css({ 
		width: Math.round(scaleX * {/literal}{$_current_large_image_width}{literal}) + 'px', 
		height: Math.round(scaleY *{/literal}{$_current_large_image_height}{literal}) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("Вы должны выбрать область");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview }); 
});

</script>
{/literal}

<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Обрезка
    </div>
</div>
<div class="box_pasport">
	<div style="padding: 20px">
		<h3>Обрезка аватары</h3>
		<br />
		<div align="center">
			<img src="/{$_large_image}" id="thumbnail" style="float:left; border:1px dotted #000" alt="Обрезать аватару" />
			<h4>Превью</h4>
			<div style="overflow:hidden; width:{$_thumb_width}px; height:{$_thumb_height}px;">
				<img id="preview" src="/{$_large_image}" style="position: relative;" alt="Предпросмотр аватары" />
			</div>
			<br />
			<form name="thumbnail" action="" method="post">
				<input type="hidden" name="event" value="usercropavatar" />
				<input type="hidden" name="x1" value="" id="x1" />
				<input type="hidden" name="y1" value="" id="y1" />
				<input type="hidden" name="x2" value="" id="x2" />
				<input type="hidden" name="y2" value="" id="y2" />
				<input type="hidden" name="w" value="" id="w" />
				<input type="hidden" name="h" value="" id="h" />
				<input type="submit" name="upload_thumbnail" value="Сохранить" id="save_thumb" />
			</form>
		</div>
			<br style="clear:both;"/>
	</div>
</div>
<div class="line1"></div>
{include file="forum/footer.tpl"}