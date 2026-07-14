{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="/forum/">Example Forum</A> &nbsp;/&nbsp; <a href="/pager/">Пейджер</a>
    </div>
</div>
<div class="line1"></div>

<form name="pagerform" action="" method="POST">
<input type="hidden" name="event" id="event" value="forumpagerdialogdelete" />
<table class="userstable">
<tr>
	<th>&nbsp;</th>
	<th align="left">Пользователь</th>
	<th>Сообщений</th>
</tr>
{if count($__errors) > 0}
	<tr>
	{foreach item=curErr from=$__errors}
		<td colspan="4" align="center" style="color: rgb(255,0,0)"><b>ОШИБКА</b>: {$curErr}</td>
	{/foreach}
	</tr>
{/if}

{if count($pagers) > 0}
<tr>
	<td colspan="4" align="right">
		<input type="submit" value="Удалить переписку" />
	</td>
</tr>
{/if}
{section name="p" loop="$pagers"}
<tr>
	<td width="10"><input type="checkbox" name="userto[]" value="{$pagers[p].userlist}" /></td>
	
	<td>
	{php}
	$filename = HOME_DIR.'upload/resized-'.$this->_tpl_vars['pagers'][$this->_sections['p']['index']]['userlist'].'.jpg';
	$small_filename = HOME_DIR.'upload/avatar-'.$this->_tpl_vars['pagers'][$this->_sections['p']['index']]['userlist'].'.jpg';
	$full_file = '/upload/resized-'.$this->_tpl_vars['pagers'][$this->_sections['p']['index']]['userlist'].'.jpg';
	$small_file = '/upload/avatar-'.$this->_tpl_vars['pagers'][$this->_sections['p']['index']]['userlist'].'.jpg';
	if (file_exists($filename)) {
	    echo '<a href="'.$full_file.'" targer=_blank>';
	    if (file_exists($small_filename)) {
    	    echo '<img src="'.$small_file.'" align="left" class="user_info_img">';
	    } else {
	        echo '<img src="'.$full_file.'" width=72 align="left" class="user_info_img">';
	    }
	    echo '</a>';
    } else {
        echo '<img src="/images/user_pic.gif" align="left" class="user_info_img">';
    }
	{/php}
	<br>
	<h3><a href="#" onclick="LoadPassport({$pagers[p].userlist});">{$pagers[p].username}</a></h3>
	<a href="#" onclick="ShowUserDialog('{$pagers[p].userlist}');">Просмотр сообщений</a><br><br>
	</td>
	<td align="center" width="50"><a href="#" onclick="ShowUserDialog('{$pagers[p].userlist}');">{if $pagers[p].newmess > 0}<b class="msgnew" style="color:red;">{$pagers[p].newmess}</b>/{$pagers[p].allmess}{else}{$pagers[p].allmess}{/if}</a></td>
</tr>
{/section}
{if count($pagers) > 0}
<tr>
	<td colspan="4" align="right">
		<input type="submit" value="Удалить переписку" />
	</td>
</tr>
{/if}
</table>
</form>

<div class="line1"></div>
{include file="forum/footer.tpl"}