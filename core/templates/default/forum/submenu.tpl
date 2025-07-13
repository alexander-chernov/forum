{if $isheader eq 1}
<form class="group_sel" action="">
    <select onchange="document.location='/forum/'+this.value+'/';" name=g id="gg">
    <option selected>Группы тем:
    <option>-----------------------
    {section name="g" loop="$groups"}
    <option value={$groups[g].groupID}>&nbsp;{$groups[g].caption}</option>
    {/section}
    </select>
    <input class="btn_group_sel" type="submit" value="&nbsp;">
</form>
{/if}
	<div class="menu">
		{if $_system_url_params[2] eq 'groups'}
		    <div class='act'><a href="/forum/groups/">Группы</a></div>
		{else}
		    <a href="/forum/groups/">Группы</a>
		{/if}
				
		{if $_system_url_params[2] neq 'groups' && $_nav_hack neq 1}
		    <div class="sep"><div></div></div>
		{/if}
		
		{if $_system_url_params[1] eq 'forum' && $_system_url_params[2] eq ''}
		    <div class='act'><a href="/forum/">Горячее</a></div>
		{else}
		    <a href="/forum/">Горячее</a>
		{/if}

		{if $_system_url_params[2] neq 'top' && $_system_url_params[2] neq '' || $_system_url_params[1] neq 'forum'}
		    <div class="sep"><div></div></div>
		{/if}
		
		{if $_system_url_params[2] eq 'top'}
		    <div class='act'><a href="/forum/top/">Топ общения</a></div>
		{else}
		    <a href="/forum/top/">Топ общения</a>
		{/if}
		
		{if $_system_url_params[2] neq 'top' && $_system_url_params[1] neq 'search'}
		    <div class="sep"><div></div></div>
		{/if}
		
		{if $_system_url_params[1] eq 'search'}
		    <div class='act'><a href="/search/">Поиск</a></div>
		{else}
		    <a href="/search/">Поиск</a>
		{/if}

        {if $is_mobile neq 'mobile'}
            {if $_system_user.userID > 0}
                {if
                    ($_system_url_params[2] neq 'sendquestion' && $_system_url_params[1] neq 'search' && $COMMERCIAL_ON neq 1)
                }
                    <div class="sep"><div>1</div></div>
                {/if}
                {if
                    ($_system_url_params[2] neq 'sendquestion' && $_system_url_params[2] neq 'tarification' && $COMMERCIAL_ON eq 1)
                }
                    <div class="sep"><div>2</div></div>
                {/if}
                {if $_system_url_params[2] eq 'sendquestion'}
                    <div class='act'>
                {/if}
                    <a href="/forum/sendquestion/">Обратная связь</a>
                {if $_system_url_params[2] eq 'sendquestion'}
                    </div>
                {/if}
            {/if}
        {/if}

	</div>
