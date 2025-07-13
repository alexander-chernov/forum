{if $_completed eq 'success'}
	Ваш запрос успешно выполнен!
{elseif $_completed eq 'repeat'}
	Вы уже жаловались на это сообщение!
{elseif $_completed eq 'rule'}
	Произошла ошибка, повторите запрос позднее
{else}
	Произошла ошибка, повторите запрос позднее
{/if}