{if $_system_user.userID >0 && $_system_user.banned neq 1}
<div class="submenu">
    <a class="exit" href="?event=cmsuserlogout">Выход</a>
	<a href="/pager/">Пейджер (&nbsp;<span id="newmess" style="color:white">{$_pager_info.new_mess*1}</span>&nbsp;|&nbsp;<span id="allmess" style="color:white">{$_pager_info.current_mess*1}</span>&nbsp;)</a>
	<div class="sep"><div></div></div>
	{if $_system_url_params[1] eq 'personal' && $_system_url_params[2] eq ''}
	<div class="act"><a href="/personal/">Мой паспорт</a></div>
	{else}
	<a href="/personal/">Мой паспорт</a>
	{/if}
    <div class="sep"><div></div></div>
	{if $_system_url_params[1] eq 'personal' && $_system_url_params[2] eq 'mythemes'}
	<div class="act"><a href="/personal/mythemes/">Мои темы</a></div>
	{else}
	<a href="/personal/mythemes/">Мои темы</a>
	{/if}
	{*
	if $COMMERCIAL_ON eq 1}
        {if $is_mobile neq 'mobile'}
                <div class="sep"><div></div></div>

                {if $_system_url_params[2] eq 'listmyactions'}
                    <div class='act'>
                {/if}
                    <a href="/personal/listmyactions/">Платежи</a>
                {if $_system_url_params[2] eq 'listmyactions'}
                    </div>
                {/if}
        {/if}
	{/if
	*}
{if $is_mobile neq 'mobile'}
	<div class="sep"><div></div></div>
	{if $_system_url_params[1] eq 'personal' && $_system_url_params[2] eq 'blacklist'}
	    <div class="act"><a href="/personal/blacklist/">Черный список</a></div>
	{else}
	    <a href="/personal/blacklist/">Черный список</a>
	{/if}
{else}
    <div class="sep"><div></div></div>
    {if $_system_url_params[1] eq 'personal' && $_system_url_params[2] eq 'blacklist'}
        <div class="act"><a href="/personal/blacklist/">Ч.С.</a></div>
        {else}
        <a href="/personal/blacklist/">Ч.С.</a>
    {/if}
{/if}
	<div class="sep"><div></div></div>
	{if $_system_url_params[1] eq 'personal' && $_system_url_params[2] eq 'favorites'}
	<div class="act"><a href="/personal/favorites/">Избранное (<span id='fav'>{favorites user=$_system_user.userID}</span>)</a></div>
	{else}
	<a href="/personal/favorites/">Избранное (<span id='fav'>{favorites user=$_system_user.userID}</span>)</a>
	{/if}

</div>
{/if}
{if $_system_user.userID >0}
{literal}
<script type="text/javascript">
    function updatePager(repeat){
        $.getJSON('/pager/getMess',
                function (response){
                    if (response.errors) {
                        $(document).find('.error').empty();
                        $('#newmess').html("error");
                        $('#newmess').css("color","red");
                    } else {
                        var newmess = response.newmess;
                        var allmess = response.allmess;
                        if (newmess>0) {
                            $('#newmess').html(newmess);
                            $('#newmess').css("color","red");
                            $('#allmess').html(allmess);
                            setInterval(function() {
                                var box = $('#newmess');
                                if (box.css('color') == 'red') {
                                    box.css({'color':'white'});
                                }
                                else {
                                    box.css({'color':'red'});
                                }
                            }, 1000);

                        }
                    }
                });
        if (repeat == true) {
            setTimeout("updatePager(1);",180000);
        }
    }
    $(document).ready(function() {
        updatePager(1);
    });
</script>
{/literal}
{/if}