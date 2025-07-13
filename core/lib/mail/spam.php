<?php
class Mail_Spam
{
	var $Form = null;
	var $Files = null;
	
	function onSpamSubmit($form)
	{
		$this->Form = $form;
		$this->Files = $_FILES;
		$this->Content = $this->Form->Request['message'];
		$this->Subject = $this->Form->Request['subject'];
		if ($this->Files)
		{
			$_filename = @$this->Files['spamlist']['name'];
			$_type = @$this->Files['spamlist']['type'];
			$_tmpname = @$this->Files['spamlist']['tmp_name'];
			$_ext = explode('.',$_filename,100);
			$_ext = array_pop($_ext);
			$handle = fopen ($_tmpname,"r");			
			$file_row = 0;
			$AuthManager = CreateObject("Auth_Manager");
			if ($AuthManager->User->Rang >=30)
			{
				while ($file_contents = @fgetcsv ($handle, filesize($_tmpname), ";"))
				{
					if ($this->SendMail($file_contents[0],$this->Subject,$this->Content,$this->Form->Request['from']) == true)
					{
						$mailers[] = true;
					}
				}
			}
			$_SESSION['mess_count'] = count($mailers);
		}
	}
	
	function PrepareDataSet($ds)
	{
		$ds->assign("count",@$_SESSION['mess_count']);
		unset($_SESSION['mess_count']);
	}
	
	function SendMail($mailto, $subject, $content, $mailfrom)
	{
		$result =  mail($mailto, $subject, $content, "From: $mailfrom\nReply-To: $mailfrom\nX-Mailer: PHP/" . phpversion());
		return $result;
	}
}
?>