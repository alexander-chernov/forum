<div align="left">
	<select name="move{$up}">
		{foreach item=curItem from=$_params.groups}
			<option {if $curItem.groupID eq 49}selected="selected"{/if}value="{$curItem.groupID}">{$curItem.caption}</option>
		{/foreach}
	</select>
	<input type="button" value="Перенести" onclick="deleteConfirm('themes', 'forummovetheme', 'перенести', {$up})" />
	<input type="button" value="Скрыть" onclick="deleteConfirm('themes', 'forumhidetheme', 'скрыть')" />
</div>