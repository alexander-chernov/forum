{if $is_mobile neq 'mobile'}
<div id="user_info_box_bnr">{include file="forum/banner/pasport.tpl"}</div>
{/if}
<script type="text/javascript" src="/js/jquery.lightbox.js"></script>
{literal}
<script type="text/javascript">
    $(document).ready(function() {
        $('#user_thumb').lightBox();
    });

</script>
{/literal}
<div class="pasport1">
	<div class="pasport_info">
    {if $is_mobile neq 'mobile'}
        {if $_thumb_image}
    		<a href="/{$_large_image}" id="user_thumb"><img src="/{$_thumb_image}?t={php} echo time(); {/php}" width="70" height="70" class="user_info_img" alt="{$_user_passport_info.user_name}" title="{$_user_passport_info.user_name}"></a>
    	{else}
    		<img src="/upload/avatar-default.jpg" width="47" height="62" class="user_info_img" alt="">
    	{/if}
    {/if}
        <div class="pasport_title">Паспорт<span>пользователя</span></div>
        <div class="clear"></div>
        <div class="pasport_info_box">
            <span>На форуме:</span>
            {$_user_passport_info.user_name}<sup>
                {if $_user_passport_info.danger_level<0}
                    <a href="#" style="color:red">{$_user_passport_info.danger_level}</a>
                {else}
                    <a href="#">+{$_user_passport_info.danger_level}</a>
                {/if}
            </sup>
        </div>
        <div class="line3"></div>
        <div class="pasport_info_box">
            <span>В реале:</span>
           {$_user_passport_info.user_fio}
        </div>
        <div class="line3"></div>
        <div class="pasport_info_box">
            <span>Пол:</span>
            {if $_user_passport_info.user_gender eq 1}
    			Не имеет значения
    		{elseif $_user_passport_info.user_gender eq 2}
    			Мужской
    		{elseif $_user_passport_info.user_gender eq 3}
    			Женский
    		{else}
    			Средний
    		{/if}
        </div>
        <div class="line3"></div>
    {if $is_mobile neq 'mobile'}
        <div class="pasport_info_box">
            <span>Почта:</span>
            <a href="#">
            {if $_system_user.is_admin}
                {$_user_passport_info.user_email}
            {else}
                {php}
                    list ($user,$domain) = explode('@',$this->_tpl_vars['_user_passport_info']['user_email']);
                    for ($i; $i<strlen($user); $i++) {
                        echo 'x';
                    }
                    echo '@'.$domain;
                {/php}
            {/if}
            </a>
        </div>
        <div class="line3"></div>
    {/if}
        <div class="pasport_info_box">
            <span>ICQ:</span>
            {$_user_passport_info.user_icq}
        </div>
        <div class="line3"></div>
        <div class="pasport_info_box">
            <span>Был на форуме:</span>
            {$_user_passport_info.lastlogin}
        </div>   
    </div>
    <div class="pasport_info_box_write">
        <a href="#" onclick="ShowUserDialog('{$_user_passport_info.userID}');">Написать сообщение</a>
    </div>                      
</div>
<div class="pasport2">
    <div class="pasport_text_full">
        {$_user_passport_info.description|nl2br}
    </div>
    <div class="line3"></div>
    <div class="pasport_close"><a href="#" id="pasport_close_btn" onclick="HidePasp(); return false;">Закрыть <img src="/images/btn_pasport_close.gif" alt=""></a></div>
</div>

