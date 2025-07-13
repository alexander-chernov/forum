<?php
function smarty_modifier_smiles($string)
{
    $string = str_replace(':)','<img src="/images/smiles/1.png" class="png">',$string);
    $string = str_replace(':))','<img src="/images/smiles/12.png" class="png">',$string);
    $string = str_replace(':)))','<img src="/images/smiles/12.png" class="png">',$string);
    $string = str_replace('>:(','<img src="/images/smiles/3.png" class="png">',$string);
    $string = str_replace('&gt;:(','<img src="/images/smiles/3.png" class="png">',$string);
    $string = str_replace(':(','<img src="/images/smiles/2.png" class="png">',$string);
    $string = str_replace('0_o','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('o_0','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('O_o','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('o_O','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('О_о','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('о_О','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('O_O','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('0_0','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace('О_О','<img src="/images/smiles/4.png" class="png">',$string);
    $string = str_replace(':sad:','<img src="/images/smiles/5.png" class="png">',$string);
    $string = str_replace(':kiss:','<img src="/images/smiles/6.png" class="png">',$string);
    $string = str_replace(':eye:','<img src="/images/smiles/7.png" class="png">',$string);
    $string = str_replace(':tong:','<img src="/images/smiles/8.png" class="png">',$string);
    $string = str_replace(':hungry:','<img src="/images/smiles/9.png" class="png">',$string);
    $string = str_replace(':sleep:','<img src="/images/smiles/10.png" class="png">',$string);
    $string = str_replace(':ugly:','<img src="/images/smiles/11.png" class="png">',$string);
    $string = str_replace(':smile:','<img src="/images/smiles/12.png" class="png">',$string);
    $string = str_replace(':flower:','<img src="/images/smiles/13.png" class="png">',$string);
    $string = str_replace(':devel:','<img src="/images/smiles/14.png" class="png">',$string);
    $string = str_replace(':glass:','<img src="/images/smiles/15.png" class="png">',$string);
    $string = str_replace(':sunglass:','<img src="/images/smiles/16.png" class="png">',$string);
    $string = str_replace(':lazy:','<img src="/images/smiles/17.png" class="png">',$string);
    $string = str_replace(':crazy:','<img src="/images/smiles/18.png" class="png">',$string);
    $string = str_replace(':anger:','<img src="/images/smiles/19.png" class="png">',$string);
    $string = str_replace(':love:','<img src="/images/smiles/20.png" class="png">',$string);
    $string = str_replace(':cry:','<img src="/images/smiles/21.png" class="png">',$string);
    $string = str_replace(':bigcry:','<img src="/images/smiles/22.png" class="png">',$string);
    $string = str_replace(':fun:','<img src="/images/smiles/23.png" class="png">',$string);
    $string = str_replace(':cool:','<img src="/images/smiles/24.png" class="png">',$string);
    $string = str_replace(':pnone:','<img src="/images/smiles/25.png" class="png">',$string);
    $string = str_replace(':angel:','<img src="/images/smiles/26.png" class="png">',$string);
	return $string;
}
?>