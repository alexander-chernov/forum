<div class="karmalist">
    <div id='closeRating' style="float:right;margin-top:2px">
        <a href="javascript:void(0);">Закрыть</a>
    </div>
    Карма сообщения
    <table cellpadding="1" cellspacing="2">
        {foreach item=rating from=$rating_list}
            <tr>
            <td><a href="#" class="name" onclick="LoadPassport({$rating.userID});return false;">
            <img src="/upload/avatar-{$rating.userID}.jpg?t={php} echo time(); {/php}" width="20" height="20" class="user_info_img" alt="{$rating.user_name}" title="{$rating.user_name}">
            </a></td>
            <td style="vertical-align:middle;"><a href="#" class="name" onclick="LoadPassport({$rating.userID});return false;">
            <b>{$rating.user_name}</a>&nbsp;</b>
            </td>
            <td style="vertical-align:middle;"><img src="/images/arr.gif"></td>
            <td style="vertical-align:middle;">&nbsp;{if $rating.rating>0}+{$rating.rating}{else}{$rating.rating}{/if}</td>
            </tr>
        {/foreach}
    </table>
</div>
<script>
{literal}
$('#closeRating').click(function(){
		$('#ratinglog').hide();
	});
{/literal}
</script>
