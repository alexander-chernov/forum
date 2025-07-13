<div class="paging">
	<form class="paging_sel" action="">
		<b>Страница: </b>&nbsp;
		<select class="pagsel" name="p">
{literal}
<script language="JavaScript">
<!--
    page_count = {/literal}{if ($__pages <1)}1{else}{$__pages*1}{/if}{literal};
    current_page_num = {/literal}{if ($__pages*1 <1)}1{else}{$__pages*1}{/if}{literal};
    plecho = {/literal}{if ($__pages <1)}1{else}{$__pages*1}{/if}{literal};

    tmp_start = page_count - 1;
    if ((page_count - current_page_num) > plecho) {
       tmp_start = current_page_num + plecho;
    }

    tmp_end = -1;
    if ((current_page_num - plecho) > -1) {
       tmp_end = current_page_num - plecho - 1;
    }

    if (tmp_start != (page_count - 1)) {
	counter = page_count - 1;
	page_num = counter+1;
	
	document.write('<option value=' + (1+page_count-page_num) + '>' + (page_num) + '</option>\n');
    }

    for (counter = tmp_start; counter > tmp_end; counter--) {
        page_num = counter+1;
	if ((1+page_count-page_num) == {/literal}{if ($__page <1)}1{else}{$__page*1}{/if}{literal}) {
	    document.write('<option selected value=' + (1+page_count-page_num) + '>');
        } else {
	    document.write('<option value=' + (1+page_count-page_num) + '>');
	}
	document.write((page_num) + '</option>\n');
    }

    if (tmp_end != -1) {
	counter = 0;
	page_num = counter+1;
	document.write('<option value=' + (1+page_count-page_num) + '>' + (page_num) + '</option>\n');
    }
//-->
</script>
{/literal}
		</select>
		<input class="btn_paging_sel" type="submit" value="&nbsp;">	
		&nbsp;из {if ($__pages <1)}1{else}{$__pages}{/if}
	</form>

    <div style="float:right">
	<span class="prev">{if $__page < $__pages}<a href="?p={$__page+1}"><img src="/images/v_1_1/r_b.gif" title="Назад" alt="Назад" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/r_y.gif'" onmouseout="this.src = '/images/v_1_1/r_b.gif'"></a>{else}<img src="/images/v_1_1/r_w.gif" title="Назад" alt="Назад" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/r_y.gif'" onmouseout="this.src = '/images/v_1_1/r_w.gif'">{/if}</span>
	{if $__up eq 1}
		<a name="ftop"></a><span class="up_down"><a href="#bottom"><img src="/images/v_1_1/d_b.gif" title="Вниз" alt="Вниз" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/d_y.gif'" onmouseout="this.src = '/images/v_1_1/d_b.gif'"></a></span>
	{else}
		<a name="bottom"></a><span class="up_down"><a href="#ftop"><img src="/images/v_1_1/u_b.gif" title="Вверх" alt="Вверх" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/u_y.gif'" onmouseout="this.src = '/images/v_1_1/u_b.gif'"></a></span>
	{/if}
	<span class="next">{if $__page >1}<a href="?p={$__page-1}"><img src="/images/v_1_1/l_b.gif" title="Вперед" alt="Вперед" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/l_y.gif'" onmouseout="this.src = '/images/v_1_1/l_b.gif'"></a>{else}<img src="/images/v_1_1/l_w.gif" title="Вперед" alt="Вперед" border="0" class="to_head" onmouseover="this.src = '/images/v_1_1/l_y.gif'" onmouseout="this.src = '/images/v_1_1/l_w.gif'">{/if}</span><!-- -->
	</div>
 	{if $isheader eq 1}
        {if $is_mobile neq 'mobile'}
	 	<form action="/search/" method="post" style="float:right; padding-right: 20px;">
                <input type="hidden" value="forumsearch" name="event"/>
                <input type="text" name="query" value="{if $_query ne ''}{$_query}{else}Искать по разделу{/if}" id="search" class="inp_text_name" style="width:210px;" onfocus="this.value = ''"/>
                <input type="hidden" name="sListThemes[{$current_group.groupID}]" value="{$current_group.groupID}">
                <input class="btn_paging_sel" type="submit" value="&nbsp;">
            </form>
        {/if}
 	{/if}
	
</div>

