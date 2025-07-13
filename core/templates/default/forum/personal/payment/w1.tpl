{include file="forum/header.tpl"}
{literal}
    <script type="text/javascript">
        function changeRub (myNum){
            if(parseInt(myNum)<>0) {
                alert('Введите корректное знаение');
                document.getElementById("amount_rub").focus();
            }
        }

    </script>
{/literal}
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
            <h3>Пополнение баланса пользователя {$_system_user.user_name}</h3>
            <div style="display:none;" id='crc_div'></div>
            <form action='/personal/payment/w1/form/' method=POST id='sendform' accept-charset="UTF-8">
                <table align="center" width="380">
                    <tr>
                        <td><b style="color:#000000;">Сумма платежа</b></td>
                        <td><input type="text" name="amount_rub" id="amount_rub" style="width:50px;" value="5" onkeyup="changeRub(this.value)"><b style="color:#000000;">руб.</b>

                        <td><input type=submit value='Пополнить'></td>
                    </tr>
                </table>
            </form>
            </td>
            </tr>
            </table>

        {/if}
    </div>

</div>

<div class="line1"></div>

{include file="forum/footer.tpl"}