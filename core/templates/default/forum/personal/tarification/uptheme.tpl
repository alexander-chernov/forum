{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; Список сервисов
    </div>
</div>
<div class="box_pasport">
	{$_error}
	{if $_error neq ''}<h2 style="text-align: center;">{$_error}</h2>{/if}
	<form method="post">
		<input type="hidden" value="themeuptop" name="event">
		<input type="hidden" value="{$upTheme}" name="upTheme[_theme]" />
		<div>
			<select name="_package">
				<option value="0">Прикрепить пакет</option>
				{foreach item="package" from=$listPackages}
					<option value="{$package.id}" {if $_themeParam.packageid == $package.id}selected="selected"{/if}>{$package.name}</option>
				{/foreach}
			</select>
		</div>
		<input type="submit" value="Купить" />
	</form>
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}