<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>ТОМСКИЕ ФОРУМЫ | АДМИН | 
{if isset($_params.groupName)}
	{$_params.groupName|strip_tags}
	{if isset($_params.themeName)}
		/ {$_params.themeName|strip_tags}
	{/if}
{else}
	{$_pageTitle|strip_tags}
{/if}
</title>
<link href="/style/adm_style.css" rel="stylesheet" type="text/css">
<link href="/style/adm_main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/adm_main.js"></script>
</head>

<body>
	<div class="top">
		<div class="submenu">
			{if isset($_params.groupName)}
				{if isset($_params.groupParentId)}
					<a href="/.admin/forum/themes/index/{$_params.groupParentId}/">{$_params.groupName}</a>
				{else}
					{$_params.groupName}
				{/if}
				{if isset($_params.themeName)}
					/ 
					{if isset($_params.themeParentID)}
						<a href="/.admin/forum/messages/index/{$_params.themeParentID}/">{$_params.themeName}</a>
					{else}
						{$_params.themeName}
					{/if}
				{/if}
			{else}
				{$_pageTitle}
			{/if}
		</div>	
	</div>