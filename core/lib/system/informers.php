<?php
// informer с Pogodavtomske
function informer_pogoda() {
	$collect = array();

	$data = file_get_contents(CACHE_DIR.'tomorrow.txt');
	$data = split(" ", $data);
	$collect[] = $data[0];
	
	return $collect;
}

function informer_date() {
	static $months = array(
		1 => 'января',
		2 => 'февраля',
		3 => 'марта',
		4 => 'апреля',
		5 => 'мая',
		6 => 'июня',
		7 => 'июля',
		8 => 'августа',
		9 => 'сентября',
		10 => 'октября',
		11 => 'ноября',
		12 => 'декабря',
	);
	
	return date("d")." ".$months[(int)date("n")]." ".date("Y");
}

function informer_day() {
	static $days = array(
		0 => 'воскресенье',
		1 => 'понедельник',
		2 => 'вторник',
		3 => 'среда',
		4 => 'четверг',
		5 => 'пятница',
		6 => 'суббота',
	);
	
	return $days[(int)date("w")];
}
?>