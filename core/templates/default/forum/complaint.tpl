<div id="complaint_box"><div id="complaint_box_1">
	<a href="#" class="close_btn"><img src="/images/btn_close.gif" alt=""></a>
    <div class="clear"></div>
    <h1>Жалоба на сообщение</h1>
    <div class="complaint_box_text">
        <div>
    	<b>Ник: </b> <span id="complaint_nick"></span>
		<br />
       	<b>Сообщение: </b> <span id="complaint_msg"></span>
        </div>
    </div>
	<form method="post" action="" id="complaint_form">
    	<label for="username" class="complaint">Основание:</label>
		<select class="complaint_sel" id="rule_id">
			<option value="0">-- Выберите правило из списка --</option>
			{foreach item=rule from=$__rules}
				<option value="{$rule.ruleID}">{$rule.caption}</option>
			{/foreach}
        </select>
		<input name="Submit" type="submit" id="submit_complaint" value="Отправить" />
		<input type="hidden" name="msgID" id="message_id" value="" />
	</form>
    <div class="clear"></div>
    <div class="complaint_box_text_result" id="complaint_result" style="font-weight:bold"></div>
</div></div>