<?php
	class Money_Webmoney
	{
		var $error = -989898;
		public function __construct ($args = array())
		{
			if (isset($args[0]['DbManager']))
				$this->DbManager = $args[0]['DbManager'];
		}
		
		public function checkWM($wmid, $payee_purse, $payment_no, $secret_key, $proxy = '127.0.0.1:3128'){
			$payee_purse = preg_match_all('/^([A-z]{1,1})([0-9]{4,})/ims', trim($payee_purse), $_res);
			$payee_purse = $_res[0][0];

			$url = 'https://merchant.webmoney.ru/conf/xml/XMLTransGet.asp';
			$xml  = "<merchant.request>
						<wmid>" . $wmid . "</wmid>
						<lmi_payee_purse>" . $payee_purse . "</lmi_payee_purse>
						<lmi_payment_no>" . $payment_no . "</lmi_payment_no>
						<sign></sign>
						<secret_key>" . $secret_key . "</secret_key>
					</merchant.request>";

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			if ($proxy != '')
				curl_setopt($ch, CURLOPT_PROXY, $proxy);
			
			$result = curl_exec($ch);
			if(curl_errno($ch) == 0) {
				preg_match_all("/(<retval>)(.*?)(<\/retval>)/ims", $result, $_res);
				$_res = $_res[2][0];
			}else{
				$_res = $this->error;
			}
			curl_close($ch);
			return $_res;
		}
	}