{include file="forum/header.tpl"}
<div class="navigation">
	<div class="complaint"></div>
	<div class="box_path">
		:: <a href="/forum/#">Example Forum</A> &nbsp;/&nbsp; {$title_part}
    </div>
</div>
<div class="line1"></div>
<div class="box_pasport">
	<table class="themes">
		<tr>
			<td>
			<div style="text-align: center;">
				{include file="forum/errors.tpl"}
				{if $sendmsg eq 'ok'}
					<h1 style="text-align: center;">Сообщение отправлено</h1>
				{/if}
				{if $moderates && $sendmsg neq 'ok'}
					<form method="post">
						<div style="width: 400px; text-align: left; margin: auto;" align="center">
							<div style="margin-top: 5px;">
								{foreach from=$moderates_names item=mod}
									<b>{$mod.user_name}</b> {if $moderates[$mod.userID].question_describe neq ""}({$moderates[$mod.userID].question_describe}){/if}
									<div style="height: 8px;overflow: hidden;">&nbsp;</div>
								{/foreach}
							</div>
							<div>
								<select name="_data[mod]" style="width: 400px; border: solid 1px;">
									<option value="0">Выбрать</option>
									{foreach from=$moderates_names item=mod}
										<option value="{$mod.userID}" {if $obj.mod eq $mod.userID}selected="selected"{/if}>{$mod.user_name}</option>
									{/foreach}
								</select>
							</div>
							<div><b>Сообщение</b></div>
							<div>
								<textarea name="_data[question]" style="width: 400px; height: 160px; border: solid 1px;">{$obj.question}</textarea>
							</div>
							<div style="text-align: right; margin-top: 5px;">
								<input type="hidden" name="event" value="addquestion"  />
								<input type="submit" class="btn_form" value="Задать вопрос" />
							</div>
						</div>
					</form>
				{/if}
			</div>
			</td>
		</tr>
	</table>
</div>
{include file="forum/footer.tpl"}