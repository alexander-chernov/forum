{if $result.init_ip}
<b>Бан на подсеть:</b> {$result.init_ip}-{$result.end_ip}<br>
<b>Причина:</b>&nbsp;<br>
Нарушение пункта правил "{$result.rule}".<br>
<b>Дата бана:</b> {$result.banned_time|date_format:"%e.%m.%Y"}<br/>
<b>Период:</b> {if $result.banned_time != $result.date_end}{$result.date_end|date_format:"%e.%m.%Y"}{else}Навсегда{/if}<br/>
<b>Комментарии:</b>&nbsp;<br>
{$result.comments}
{else}
Данный адрес в списке банов не числится.
{/if}