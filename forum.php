<?
$_add_url = '';
if (isset($_REQUEST['g']) && (int)$_REQUEST['g'] > 0 && isset($_REQUEST['t']) && (int)$_REQUEST['t'] > 0){
		$_add_url = (int)$_REQUEST['g'] . '/' . (int)$_REQUEST['t'] . '/';
}elseif (isset($_REQUEST['g']) && (int)$_REQUEST['g'] > 0) {
	$_add_url = (int)$_REQUEST['g'] . '/';
}
header('Location: /forum/' . $_add_url);
exit();
