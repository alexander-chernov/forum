<script type="text/javascript">
	{if $_params.themeIDs} 
		var indexes = new Array(0, {$_params.themeIDs}); 
	{else}
		var indexes = new Array(0);
	{/if}
</script>

{include file="admin/error/error_messages.tpl"}

{if count($_dataGrid) > 0 }
	<form id="mainform" action="?p={$smarty.get.p}" method="POST">
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('themes')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl" up=1}
		</div>
		{include file="admin/forum/themes_functions.tpl" up=1}
		<table class="theme">
		<tr>
			<th>
                <input type="checkbox" onclick="checkAll('themes', this.checked, indexes)" /></th>
			<th>№</th>
		{foreach item=curItem from=$_tableHeaders}
			<th>{$curItem}</th>
		{/foreach}
			<th><img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" /></th>
			<th><img src="/images/admin/book_next.png" title="Перейти к теме на форуме" alt="Перейти к теме на форуме" /></th>
		</tr>
		{foreach key=key item=curItem from=$_dataGrid}
		<tr {if $curItem.hidden eq 1}style="background:#999999;"
            {else}class="tr{if $curItem.is_top eq 1 || $curItem.hottop eq 1}3{else}{if $key is odd}1{else}2{/if}{/if}"
            {/if}
            >
			<td align="center"><input type="checkbox" id="themes_{$curItem.themeID}" name="themes[{$curItem.themeID}]" {if $curItem.is_top eq 1 || $curItem.hottop eq 1} style='display:none'{/if}/></td>
			<td>{$curItem.counter}{if $curItem.autoup >0 && $curItem.autoup_interval neq 86400}&nbsp<b>[autoup]</b>{/if}</td>
			<td>
				<a href="/.admin/forum/messages/index/{$curItem.themeID}/" target="_blank">
					{if $_params.currentGroupIsCommerce neq 1}
						{$curItem.caption|regex_replace:"/(продам|куплю|сдам|сниму|продаю|меняю|отдам|возьму|ищется|ищет|железо|надо|есть|кому)/imsu":'<span style="background-color: #FFA000;">$1</span>'}
					{else}
						{$curItem.caption}
					{/if}
				</a>
			</td>
			<td>{$curItem.messages}</td>
			<td>
				{if $curItem.authorID > 0 && isset($curItem.realname)}
					{if $curItem.author != $curItem.realname}
						{$curItem.author}
						(наст.&nbsp;<a class="name" href="/.admin/users/pager/send/to/{$curItem.authorID}" target="_blank">{$curItem.realname}</a>)
					{else}
						<a class="name" href="/.admin/users/pager/send/to/{$curItem.authorID}" target="_blank">{$curItem.realname}</a>
					{/if}
				{else}
					<span class="name1">{$curItem.author}</span>
				{/if}
			</td>
			<td>
				{if $curItem.author_ip neq 0}
					{$curItem.author_ip} 
					<a href="/.admin/bans/ip/add/?ip={$curItem.author_ip}&themeID={$curItem.themeID}&group={$_params.groupName}" target="_blank">[ban]</a>
					<a href="/.admin/forum/messages/filter/?addr={$curItem.author_ip}" target="_blank">[поиск]</a>
				{/if}
			</td>
			<td>{$curItem.created|date_format:"%d/%m/%Y %H:%M"}</td>
			<td>{$curItem.updated|date_format:"%d/%m/%Y %H:%M"}</td>
			<td align="center" style="vertical-align:middle">
				<a href="/.admin/forum/themes/edit/{$curItem.themeID}/" target="_blank">
					<img src="/images/admin/book_edit.png" title="Редактировать" alt="Редактировать" />
				</a>
			</td>
			<td align="center" style="vertical-align:middle">
				<a href="/forum/{$curItem.groupID}/{$curItem.themeID}/" target="_blank">
					<img src="/images/admin/book_next.png" title="Перейти к теме на форуме" alt="Перейти к теме на форуме" />
				</a>
			</td>
		</tr>
		{/foreach}
		</table>
		<input type="hidden" value="" id="event" name="event"/>
		<input type="hidden" value="1" id="selector" name="selector"/>
		<div style="float:right">
			<a href="javascript:void(0)" onclick="selectAll('themes')">Инвертировать выделение</a>&nbsp;
			{include file="admin/pager.tpl"}
		</div>
		{include file="admin/forum/themes_functions.tpl" up=2}
	</form>
{else}
	<div>Не найдено тем</div>
{/if}