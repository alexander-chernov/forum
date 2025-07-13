<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3c.org/tr/1999/REC-html401-19991224/loose.dtd">
<html prefix="og: http://ogp.me/ns#">
<head>
<title>ТОМСКИЕ ФОРУМЫ | {if $title}{$title}{/if} {$title_part}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="/favicon.ico" rel="shortcut icon">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name='yandex-verification' content='5fc97dda37552e7c' />
    <meta property="og:title" content="ТОМСКИЕ ФОРУМЫ | {if $title}{$title}{/if} {$title_part}" />
    <meta property="og:description" content="Томские форумы. Общение на любые темы." />
    <meta property="og:image" content="http://forum.site/images/logo.png" />

    <link href="/style/style.css?time={$smarty.now}" type="text/css" rel="stylesheet">

{if $is_mobile eq 'mobile'}
    <link href="/style/mobile.css" type="text/css" rel="stylesheet">
    <meta name="viewport" content="width=510px; initial-scale=1.0, minimum-scale=0.25, maximum-scale=2.0">
{/if}

{* if $is_mobile eq 'mobile'}
    <link href="/style/mobile.css?t={php} echo date('dmY');{/php}" type="text/css" rel="stylesheet">
{/if *}

<link href="/style/jquery.lightbox-0.5.css" type="text/css" rel="stylesheet">
<link href="/style/autocomplete.css" type="text/css" rel="stylesheet">

<script type="text/javascript" src="/js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="/js/script.js"></script>
<script type="text/javascript" src="/js/AC_OETags.js"></script>
<script type="text/javascript" src="/js/pasport.js"></script>
<script type="text/javascript" src="/js/auth.js?time={php} echo time() {/php}"></script>
<script type="text/javascript" src="/js/add_favorites.js"></script>
<script type="text/javascript" src="/js/check_ip.js"></script>
<script type="text/javascript" src="/js/add_favorites.js"></script>
<script type="text/javascript" src="/js/restore_passw.js"></script>
<script type="text/javascript" src="/js/complaint.js"></script>
<script type="text/javascript" src="/js/testbbcode.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.lightbox.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="/js/jquery.qtip-1.0.0-rc3.js"></script>
<script type="text/javascript" src="/js/share.js"></script>

    {if $is_mobile neq 'mobile'}
    {literal}
    <script language="javascript">
        function ResizeMenu() {

            var d = $(window).width();
            var w = $('#body_main').outerWidth();
            var l = 0;
            var r = 0;
            var b = 1250;
            var tmp = Math.round(($(window).width()-798)/2)-206;
            var s = d - w + 20;
            if (d < 1250) {
                $('#body_main').css('width',b+'px');
                l = 20;
                r = s;
                if ($(window).scrollLeft()>0){
                    l = l - $(window).scrollLeft();
                    r = r + $(window).scrollLeft();
                }
            } else {
                $('#body_main').css('width',d+'px');
                l = tmp;
                r = tmp;
            }
            //$("#head_balance").text($(window).width());
            //$("#karma_head").text($(window).scrollLeft());
            $(".left_banner_context").css('left',l+'px');
            $(".right_banner_context").css('right',r+'px');
        }
        $(document).ready(function(){
            $('.zoomin').lightBox();
        });
        $(window).resize(function() {
            ResizeMenu();
        });
        $(window).ready(function() {
            ResizeMenu();
        });
        $(window).scroll(function() {
            //alert($('#body_main').scrollLeft());
            ResizeMenu();
        });

    </script>
    {/literal}
    {/if}
    {literal}
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-41114943-1', 'forum.site');
        ga('send', 'pageview');
        </script>
    {/literal}
    {*
    <script>
        var script = document.createElement('script');
        script.src = 'http://effad.ru/effad.js';
        script.type = 'text/javascript';
        document.getElementsByTagName('head')[0].appendChild(script);
    </script>
    *}
</head>
<body id="body_main">


<div id="overlay"></div>
{include file="forum/authform.tpl"}
{include file="forum/checkipform.tpl"}
{include file="forum/restorepassw.tpl"}
{include file="forum/complaint.tpl"}
<div id="user_info_box"></div>

<A name=top></A>
{if $is_mobile neq 'mobile'}
<div style="width: 760px;margin: 0 auto;position: relative">
    <div class="right_banner_context">
        <div id="header_text_brn_right" class="box_brn">




        </div>
        <div class="box_brn">
            <h1 class="corner">Интересные новости</h1>
            <br style="clear:both">
            <div id="news_tomsk"></div>




        </div>
    </div>

    <div class="left_banner_context">
        <div id="header_text_brn_left" class="box_brn">

        </div>
        <div class="box_brn">
            <h1 class="corner">Акции Томска</h1>
            <br style="clear:both">
            <div id="actions"></div>
        {literal}
            <script>
                function loadactions(){
                    $('#actions').load('/actions.php');
                }
                //loadactions();
            </script>
        {/literal}
        </div>
        <br style="clear:both">
        <div id="header_text_brn_left" class="box_brn">

        </div>
    </div>
</div>
{else}

{/if}
<div class="block_menu">

    <div class="text_ban">
	{if $readonly eq 1}
        {if $ban_type eq 'ip'}
            <span>Ваш IP забанен!</span>
        {else}
            <span>Ваш ник забанен!</span>
        {/if}
        <a href="#" onclick="pasteDataAndSubmitCheck('{$ban_element}')">Подробнее...</a>
	{/if}
    </div>
	<div class="menu">
		{include file="forum/up_menu.tpl"}
	</div>
</div>


{include file="forum/authpanel.tpl"}

<div class="block_logo">

    <div class="weather">
		{include file="forum/weather_informer.tpl"}
	</div>
	<div class="logo">
	    <a href="/forum/"><img src="/images/logo.png" alt="Томские Форумы" title="Томские Форумы" border="0"></a>
	</div>
</div>

    <div class="top_block">
        {if $is_mobile neq 'mobile'}
            {include file="forum/banner/header_txt.tpl"}
            {include file="forum/banner/header.tpl"}
            {include file="forum/banner/top_txt.tpl"}
        {/if}
    </div>

<div class="block_menu_m">
    {include file="forum/submenu.tpl" isheader=1}
</div>


