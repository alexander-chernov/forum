{include file="forum/header.tpl"}
<div class="blacktr navigation">:: <a href="/forum/">Example Forum</a> &nbsp;/&nbsp; TOP 50 ГОРЯЧИХ ТЕМ</div><table class="themes">

<tbody>
{section name="t" loop="$themes"}
<tr><td class="tdw5"><img src="/images/e.gif" alt=""></td><td class="tdw1">{$themes[t].updated}</td><td class="tdw2"><a href="" title="">{$themes[t].caption}</a> [3&nbsp;-&nbsp;paul1960]</td><td class="tdw3">
{if $themes[t].authorID >0}
<a target="u" onclick="window.open('','u','scrollbars,width=530,height=500');" href="/forum/users/{$themes[t].authorID}/">{$themes[t].author}</a>
{else}
{$themes[t].author}
{/if}
</td></tr>
{/section}
</tbody></table>

<div class="blacktr"><form action="http://forum.site/forum.php" method="get" class="paging">
	<table class="navig">
	<tbody><tr><td class="navig1"><span class="navigation1">Страниц:</span></td>
		<td class="navig2">
			<select name="l" class="pagsel">
            <option label="1" value="1" selected="selected">1</option>
            <option label="2" value="2">2</option>

            <option label="3" value="3">3</option>
            <option label="4" value="4">4</option>
            <option label="5" value="5">5</option>
            <option label="6" value="6">6</option>
			</select>
        </td>
        <td class="navig5"><input value="&nbsp;" class="authorizbtn2" type="submit"></td>

		<td class="navig4"><span class="white">&nbsp;&nbsp;из&nbsp;22</span></td>
		<td class="navig3"><span class="navigation1">«&nbsp;вперёд</span></td>
		<td class="navig3"><a href="#top" class="navigation1">&nbsp;перейти вверх</a></td>
		<td class="navig3"><a href="http://forum.site/forum.php?l=2" class="navigation1">назад&nbsp;»</a></td>
	</tr>

	</tbody></table></form>
</div>
{include file="forum/footer.tpl"}