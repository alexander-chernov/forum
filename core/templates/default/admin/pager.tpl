<span class="paging">
	<b>Страница: </b>
	<select class="pagsel" name="p" id="admin_pager_{if $up eq 1}1{else}0{/if}">
		{literal}
		<script language="JavaScript">
			page_count = {/literal}{if ($__pages <1)}1{else}{$__pages*1}{/if}{literal};
			current_page_num = {/literal}{if ($__pages*1 <1)}1{else}{$__pages*1}{/if}{literal};
			plecho = 100;

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
			
			document.write('<option value=' + (1+page_count-page_num) + '>' + (1+page_count-page_num) + '</option>\n');
			}

			for (counter = tmp_start; counter > tmp_end; counter--) {
				page_num = counter+1;
			if ((1+page_count-page_num) == {/literal}{if ($__page <1)}1{else}{$__page*1}{/if}{literal}) {
				document.write('<option selected value=' + (1+page_count-page_num) + '>');
				} else {
				document.write('<option value=' + (1+page_count-page_num) + '>');
			}
			document.write((1+page_count-page_num) + '</option>\n');
			}

			if (tmp_end != -1) {
			counter = 0;
			page_num = counter+1;
			document.write('<option value=' + (1+page_count-page_num) + '>' + (1+page_count-page_num) + '</option>\n');
			}
		</script>
		{/literal}
	</select>
	<input class="btn_paging_sel" type="button" value="Перейти" onclick="location.href='?{$_params.pagerAddParams}p='+document.getElementById('admin_pager_'+{if $up eq 1}1{else}0{/if}).value" />
</span>
