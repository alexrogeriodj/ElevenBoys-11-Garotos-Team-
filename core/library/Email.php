<?php
final class Email {
	
	private $loader;
	private $mail;
	
	function __construct($loader) {
		$this->loader = $loader;
	}
	
	public function getTemplateMail($name, $data, $theme='default') {
		
		$config = $this->loader->get('config');
		$config->databaseLoad(array('site-url'));
		
		$dirTheme = $config->get('DIR_THEME');
		$dirTemplate = $this->loader->getRootPath() . "/{$dirTheme}/{$theme}/mail";
		
		$html = file_get_contents("{$dirTemplate}/template.tpl");
		$cont = file_get_contents("{$dirTemplate}/{$name}.html");
		
		$html = str_replace("{CONTENT}", $cont, $html);
		
		foreach($data as $k => $v) 
			$html = str_replace('{' . $k . '}', $v, $html);
		
		$html = str_replace("{SITEURL}", $config->get('site-url'), $html);
		$html = str_replace("{DIRTHEME}", $dirTheme, $html);
		
		return $html;
		
	}
	
	public function setMailer($key='default') {
		
		$this->loader->load('class.phpmailer', 'library/mail');
		
		$this->mail = new PHPMailer();
		
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "select " .
				"	mailer, " .
				"	authenticate, " .
				"	charset, " .
				"	port, " .
				"	security, " .
				"	host, " .
				"	username, " .
				"	password, " .
				"	from_email, " .
				"	from_name " .
				"from " .
				"	config_mail " .
				"where " .
				"	name = '$key'";
		$cfg = $database->getObject($database->query($sql));
		
		switch($cfg->mailer) {
			case 'smtp'    : $this->mail->IsSMTP(); break;
			case 'sendmail': $this->mail->IsSendmail(); break;
			case 'qmail'   : $this->mail->IsQmail(); break;
			default        : $this->mail->IsMail();
		}
		
		$this->mail->SMTPAuth   = $cfg->authenticate == 'S' ? true : false;
		$this->mail->CharSet    = $cfg->charset;
		$this->mail->Port       = $cfg->port;
		$this->mail->SMTPSecure = $cfg->security;
		$this->mail->Host       = $cfg->host;
		$this->mail->Username   = $cfg->username;
		$this->mail->Password   = $cfg->password;
		
		$this->mail->SetFrom($cfg->from_email, $cfg->from_name);
		
		return $this->mail;
		
	}
	
	public function getMail() {
		if(is_null($this->mail)) $this->setMailer();
		return $this->mail;
	}  
	
	public function addAttachment($path) {
		if(is_null($this->mail)) $this->setMailer();
		return $this->mail->AddAttachment($path);
	}
	
	public function addReplyTo($address, $name='') {
		if(is_null($this->mail)) $this->setMailer();
		return $this->mail->AddReplyTo($address, $name);
	}
	
	public function setReplyTo($address, $name='') {
		if(is_null($this->mail)) $this->setMailer();
		$this->mail->ClearReplyTos();
		return $this->mail->AddReplyTo($address, $name);
	}
	
	public function addCC($address, $name='') {
		if(is_null($this->mail)) $this->setMailer();
		return $this->mail->AddCC($address, $name);
	}

	public function addBCC($address, $name='') {
		if(is_null($this->mail)) $this->setMailer();
		return $this->mail->AddBCC($address, $name);
	}
	
	public function sendMail($to, $subject, $content, $attachment=null, $cc=null, $bcc=null) {
		
		if(is_null($this->mail)) $this->setMailer();
		
		$this->mail->Subject = $subject;
		
		// Destinatario(s)
		if(is_array($to)) {
			foreach($to as $k => $v) {
				if(is_numeric($k)) $this->mail->AddAddress($v);
				else $this->mail->AddAddress($k, $v);
			}
		}
		else $this->mail->AddAddress($to);
		
		// Conteudo HTML
		$this->mail->MsgHTML($content);
		
		// Anexo(s)
		if(!is_null($attachment)) {
			if(is_array($attachment)) {
				foreach($attachment as $k => $v) {
					$this->mail->AddAttachment($v);
				}
			}
			else $this->mail->AddAttachment($attachment);
		}
		
		// Copia(s)
		if(!is_null($cc)) {
			if(is_array($cc)) {
				foreach($cc as $k => $v) {
					if(is_numeric($k)) $this->mail->AddCC($v);
					else $this->mail->AddCC($k, $v);
				}
			}
			else $this->mail->AddCC($cc);
		}
		
		// Copia(s) coculta(s)
		if(!is_null($bcc)) {
			if(is_array($bcc)) {
				foreach($bcc as $k => $v) {
					if(is_numeric($k)) $this->mail->AddBCC($v);
					else $this->mail->AddBCC($k, $v);
				}
			}
			else $this->mail->AddBCC($bcc);
		}
		
		$send = $this->mail->Send();
		
		$this->mail->ClearAllRecipients();
		$this->mail->ClearAttachments();
		
		return $send;
		
	}
	
}
?>