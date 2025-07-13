{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Загрузка аватары
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
		<h3>Загрузка аватары</h3>
		
		{if $_thumb_image neq ''}
			<h4>Текущая аватара</h4>
			<img src="/{$_thumb_image}" />
			<br />
		{/if}
		
		{if $_error neq ''}
			<br />
			<div style="color: red">{$_error}</div>
		{/if}
		<br />
		<form name="photo" enctype="multipart/form-data" action="" method="post">  
			<input type="file" name="image" size="30" />
			<input type="hidden" name="event" value="useruploadavatar" />
			<input type="submit" name="upload" value="Загрузить" />  
		</form>  
	</div>
</div>
<div class="line1"></div>
{include file="forum/footer.tpl"}