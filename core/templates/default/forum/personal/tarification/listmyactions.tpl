{include file="forum/header.tpl"}
<div class="navigation">
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/">Томские форумы</A> &nbsp;/&nbsp; Список платежей
    </div>
</div> 
<div class="line1"></div>
{literal}
	<style type="text/css">
		.cBlack {
			width: 100%;
		}

		.cBlack tr td {
			color: #000000;
			padding: 4px;
		}

		.cBlack tr th {
			text-align: center;
			color: #000000;
			padding: 4px;
		}
	</style>
{/literal}
	<table class=themes>
		{foreach from=$listActions item="action"}
			<tr>
				<td>{$action.date|date_format:"%H:%M %e/%m"}</td>
				<td>{$action.action}</td>
				<td>{$action.payment} р.</td>
				<td>Успешно</td>
			</tr>
		{/foreach}
	</table>
{include file="forum/footer.tpl"}