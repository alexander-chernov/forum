{if $result.user_name}
<b>Бан пользователя:</b> {$result.user_name}<br>
<b>Причина:</b>&nbsp;<br>
Нарушение пункта правил "{$result.rule}".<br>
<b>Комментарии:</b>&nbsp;<br>
{$result.admin_comment}<br>
<b>Дата бана:</b>&nbsp;{$result.when_banned|date_format:"%e.%m.%Y"}<br>
<b>Период:</b>&nbsp;{if $result.ban_end != $result.when_banned}{$result.ban_end|date_format:"%e.%m.%Y"}{else}Навсегда{/if}<br>
{else}
Данный пользователь в списке банов не числится.
{/if}