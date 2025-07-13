{include file="forum/header.tpl"}
<div class="navigation">
    <div class="box_path">
        :: <a href="#">Томские форумы</A> &nbsp;/&nbsp; <a href="/personal/">Паспорт</a> &nbsp;/&nbsp; <a href="/personal/">Пополнение баланса</a>&nbsp;/&nbsp; W1
    </div>
</div>
<div class="box_pasport">
    <div class="box_pasport_bg">
        {if $_auth_infos.register ne ''}
            <h3>{$_auth_infos.register}</h3>
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        {else}
            <h3>Пополнение баланса пользователя {$_system_user.user_name} на сумму {$amount_w1} руб. </h3>
            <div style="display:none;" id='crc_div'></div>
            <form action='https://merchant.w1.ru/checkout/default.aspx' method=POST id='sendform' accept-charset="UTF-8">

                <input type=hidden name=WMI_PAYMENT_AMOUNT value="{$amount_w1}">
                <input type=hidden name=WMI_MERCHANT_ID value="{$id_merchant_w1}">
                <input type=hidden name=WMI_CURRENCY_ID value="{$id_currency_w1}">
                <input type=hidden name=WMI_DESCRIPTION value="{$converted_desc_w1}">
                <input type=hidden name=WMI_PAYMENT_NO value="{$id_order_w1}">
                <input type=hidden name=WMI_SUCCESS_URL value="{$success_w1}">
                <input type=hidden name=WMI_FAIL_URL value="{$fail_w1}">
                <input type=hidden name=WMI_SIGNATURE value="{$signature_w1}">
                <input type=submit value='Перейти на форму оплаты'>
            </form>
            </td>
            </tr>
            </table>

        {/if}
    </div>

</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}