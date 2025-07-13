<div class="paging">
	<span class="next">{if $__page > 1}<a href="?p={$__pagePrev}">&laquo; Вперед</a>{else}&laquo; Вперед{/if}</span>
	<span class="prev">{if $__page neq 10}<a href="?p={$__pageNext}">Назад &raquo;</a>{else}Назад &raquo;{/if}</span>
	{if $__up eq 1}
		<a name="ftop"></a><span class="up_down"><a href="#bottom">Вниз</a></span>
	{else}
		<a name="bottom"></a><span class="up_down"><a href="#ftop">Вверх</a></span>
	{/if}
</div>
