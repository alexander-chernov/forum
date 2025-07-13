{include file="forum/header.tpl"}
<div class="navigation">
	<div class="box_path">
		:: <a href="#">Томские форумы</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; Пополнение баланса
    </div>
</div>
<div class="box_pasport">
	<div class="box_pasport_bg">
{if $_auth_infos.register ne ''}
	<h3>{$_auth_infos.register}</h3>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
{else}
    <p align="center"><h3>Пополнить баланс</h3><br>
    На этой странице Вы можете выбрать удобный Вам вариант пополнения баланса.
    <br><b style="color:#990000;">ВНИМАНИЕ! Пополняя баланс Вы автоматически принимаете условия <a href="/pages/confirm/" style="color:#990000;"><u>ПОЛЬЗОВАТЕЛЬСКОГО СОГЛАШЕНИЯ</u></a></b>
    <br><br><br>
    <table cellpadding=3 cellspacing="3">
<!--
    <tr valign="top">
        <td><a href="/personal/payment/w1/"><img src="/images/payment/w1.jpg" border="0"></a></td>
        <td><p align="justify" style="color:#000000;"><a href="/personal/payment/w1/"><b>Оплатить online (W1)</b></a><br>
                Теперь Вы можете пополнить счет с помощью  платежной системы WalletOne - W1.
            </p><br />
        </td>
    </tr>
-->
    <tr valign="top">
        <td><a href="/personal/payment/regplat/"><img src="/images/payment/regplat.jpg" border="0"></a></td>
        <td><p align="justify" style="color:#000000;"><a href="/personal/payment/regplat/"><b>Оплатить online (Regplat)</b></a><br>
    Теперь Вы можете пополнить счет с помощью платежной системы RegPlat.
    </p><br />
    </td>
    </tr>

    <tr valign="top">
        <td><a href="/personal/payment/"><img src="/images/payment/pay.jpg" border="0"></a></td>
        <td><p align="left" style="color:#000000;"><b>В офисе компании "Рекламный дайджест"</b><br>
                Вы можете пополнить свой баланс, произведя оплату в офисе компании, предварительно позвонив по телефону 52-10-01 доп. 360.<br>
                Комиссия отсутствует.<br></p>
        </td>
    </tr>


{*
<tr valign="top">
	<td><br /><a href="/personal/payment/robokassa/"><img src="/images/payment/other.jpg" border="0"></a></td>
	<td><p align="justify" style="color:#000000;"><a href="/personal/payment/robokassa/"><b>Оплатить online</b></a><br>
Теперь Вы можете пополнить счет с помощью пластиковой карты, счета мобильного телефона, а так же банковского перевода.
</p><br />
</td>
</tr>

*}

{*
<tr valign="top">
	<td><a href="/personal/payment/wm/"><img src="/images/payment/webmoney.jpg" border="0"></a></td>
	<td><p align="justify" style="color:#000000;"><b>Через систему Webmoney.</b><br>

Срок зачисления - от несколько часов до суток. <br>	
Комиссия - 0.8%. <br>
<a href="/personal/payment/wm/"><u><b>Оплатить</b></u></a><br></p>
</td>
</tr>
*}
{*
<tr>
	<td><a href="/personal/payment/ym/"><img src="/images/payment/yandex.jpg" border="0"></a></td>
	<td><p align="justify" style="color:#000000;">
	<b>Через систему Яндекс.Деньги.</b><br>
		Срок зачисления - несколько часов . <br>
		Комиссия - 0.5%. <br>
<a href="/personal/payment/ym/"><b><u>Оплатить</u></b><br>
</a></p></td>	
</tr>
<tr valign="top">
<td><img src="/images/payment/sms.jpg" border="0"></td>
	<td><p align="justify" style="color:#000000;">
	<b>С помощью SMS сообщения.</b><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Срок зачисления - моментально<br>
		Комиссия - 50%. (не самый экономичный способ)<br>
		<a href="/personal/payment/sms/"><b><u>Оплатить</u></b><br>
</p></td>
</tr>
*}
{*
<tr>
<td><img src="/images/payment/other.jpg" border="0"></td>
	<td style="color: rgb(175, 175, 175);"><br>
	<b>Через любую другую платежную систему.</b><br>
	
		Срок зачисления - несколько часов.<br>
		 
		Комиссия отсутствует. (точнее за наш счет)<br>

		Временно не работает<br>
		
</td>
</tr>
*}
    </tbody></table>
    </p>
{/if}
        <h3>Последние 50 транзакций</h3>
        <div style="width:700px;height:100px;overflow:auto;text-align: left;">
        <table class=themes2 style="width:600px;">
        {foreach key=key item=curItem from=$_dataGrid}
        <tr class="tr2">
            <td class="tdw3" >{$curItem.timestamp|date_format:"%H:%M %e/%m"}</td>
            <td class="tdw3">{$curItem.note}</td>
        </tr>
        {/foreach}
        </table>
        </div>


	</div>
	
</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}