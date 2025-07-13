{foreach item=error from=$_errors}
	<div style="color:#F00">{$error}</div>
{/foreach}
{foreach item=msg from=$_params._msg}
	<div style="color:#000"><b>{$msg}</b></div>
{/foreach}
<form method="post">
	<table class="theme">
		<tr>
			<td align="right">
				ID пользователя
			</td>
			<td>
				<input type="text" name="_data[userid]" value="{$_params.obj.userid}" />
			</td>
		</tr>
		<tr>
			<td align="right">
				Сумма
			</td>
			<td>
				<input type="text" name="_data[money]" value="{$_params.obj.money}"/>
			</td>
		</tr>
		<tr>
			<td align="right">
				Способ оплаты
			</td>
			<td>
				<textarea name="_data[action]" rows="5" cols="80">{$_params.obj.action}</textarea>
			</td>
		</tr>
		<tr>
			<td align="right">
				Комментарий
			</td>
			<td>
				<textarea name="_data[comment]" rows="5" cols="80">{$_params.obj.action}</textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="event" value="addmoney" />
				<input type="submit" value="Добавить" />
			</td>
		</tr>
	</table>
</form>