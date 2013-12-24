<?php

namespace Tools;

use \Tools as T;
use \Consts as C;
use \Components as Com;

/**
 * Tondarweb SMTP
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class SendMail extends PHPMailer\PHPMailer {

	/**
	 * $SMTP = T\SendMail::GetConfiguredMailSender();<br/>
	 * $SMTP->AddAddress('tondarweb@gmail.com', 'Pouya');<br/>
	 * $SMTP->Subject = 'Test';<br/>
	 * $SMTP->MsgHTML('This is a test msg');<br/>
	 * if (!$SMTP->Send())<br/>
	 * 	 \Err::DebugBreakPoint('failed');
	 * @param type $FromTitle
	 * @param string $From
	 * @param boolean $IsHTML
	 * @param string $Charset
	 * @param mixed $Debug
	 * false = disabled
	 * 1 = errors and messages
	 * 2 = messages only
	 * @return \Tools\PHPMailer\PHPMailer PHPMailer Configured SMTP Object
	 */
	static function GetConfiguredMailSender($FromTitle = NULL, $From = NULL, $IsHTML = true, $Charset = T\DB::CharsetLevel2, $Debug = \Conf::Err_TraceMode) {
		$Params = \Conf::SMTP_GetParams();
		$SMTP = new static; //my instance or my extended instance

		$SMTP->IsSMTP();
		$SMTP->SMTPDebug = $Debug;
		$SMTP->SMTPAuth = true;
		$SMTP->Host = $Params['Server'];
		$SMTP->SMTPSecure = strtolower($Params['Encryption']);
		$SMTP->Port = $Params['Port'];
		$SMTP->Username = $Params['Username'];
		$SMTP->Password = $Params['Password'];

		if (is_bool($IsHTML))
			$SMTP->IsHTML($IsHTML);
		if ($IsHTML)
			$SMTP->AltBody = $Params['HTMLAltTxt'];
		if (is_string($Charset))
			$SMTP->CharSet = $Charset;
		$SMTP->SetFrom(
				$From ? $From : $Params['From']
				, $FromTitle ? $FromTitle : $Params['FromTitle']
		);

		return $SMTP;
	}

	/**
	 * This is same as PHPMailer::MsgHTML but this one forces you to set the Subject
	 * Evaluates the message and returns modifications for inline images and backgrounds and sets the result inside in the email body
	 * @param type $Subject required email subject
	 * @param type $HTMLBody Text to be HTML modified
	 * @param string $basedir baseline directory for path
	 * @return string $message
	 */
	function send2($Subject, $HTMLBody, $basedir = '') {
		$this->Subject = $Subject;
		return $this->MsgHTML($HTMLBody);
	}

//	static function GetConfiguredMandrill() {
//		static $Mandrill = null;
//		if (!$Mandrill) {
//			\Yii::import('application.extensions.Mandrill.Mandrill');
//			$Mandrill = new \Mandrill(\Conf::MandrillAPIKey);
//		}
//		return $Mandrill;
//	}
//	public $Params = NULL;
//	public $MIMEVersion = 'MIME-Version: 1.0';
//	public $ContentType = 'Content-type: text/html; charset=utf-8';
//
//	/**
//	 * @var bool $AvoidHeaderCR //Avoid Carriage Return for poor quiality UNIX mail agents
//	 */
//	public $AvoidHeaderCR = false;
//	public $WordWrap = true;
//	public $WordWrapLen = 70;
//	public $SkipFullStopStartLine = \Conf::SMTP_SkipFullStopStartLine;
//
//	public function __construct($Params = NULL) {
//		;
//	}

	/**
	 * @param arr/str $mixedTo	//array('To'=>'...', 'Cc'=>'...', 'Bcc'=>'...')
	 * @param str $Subject
	 * @param str $Content
	 * @param str $ReplyTo
	 * @param str $From
	 */
//	function Send(
//	$mixedTo
//	, $Subject
//	, $Content
//	, $ReplyTo = NULL
//	, $From = NULL
//	) {
////		$Params['Server'], $Params['Port'], $Params['Encryption'], $Params['Username'], $Params['Password'];
////		-----------
//		$Params = &\Conf::$SMTP_ConnParams;
//
//		if (!is_array($mixedTo))
//			$mixedTo = array('To' => $mixedTo);
//
//		if (strpos($Subject, '{{@') >= 0)
//			$Subject = \F3::resolve($Subject);
//
//		if (strpos($Content, '{{@') >= 0)
//			$Content = \F3::resolve($Content);
//		if ($this->SkipFullStopStartLine)
//			$Content = str_replace('\n.', '\n..', $Content);
//		if ($this->WordWrap)
//			$Content = wordwrap($Content, $this->WordWrapLen);
//
//		$Headers = array();
//		if ($mixedTo['Cc'])
//			$Headers[] = 'Cc: ' . $mixedTo['Cc'];
//		if ($mixedTo['Bcc'])
//			$Headers[] = 'Bcc: ' . $mixedTo['Bcc'];
//		$Headers[] = "From: " . $From ? $From : $Params['From'];
//		if ($ReplyTo)
//			$Headers[] = "Reply-To: $ReplyTo";
//		if ($this->ContentType) {
//			$Headers[] = $this->MIMEVersion;
//			$Headers[] = $this->ContentType;
//		}
//		$Headers[] = "X-Mailer: PHP/" . phpversion();
//		$Headers = implode($this->AvoidHeaderCR ? '\n' : '\r\n', $Headers);
//
//		mail($mixedTo['To'], $Subject, $Content, $Headers);
//	}

	/**
	 * @param str $To
	 * @param str $Subject
	 * @param str $From
	 * @return \SMTP
	 */
//	static function GetObj($To = NULL, $Subject = NULL, $From = NULL) {
//		$Params = &\Conf::$SMTP_ConnParams;
//		$Mail = new \SMTP($Params['Server'], $Params['Port'], $Params['Encryption'], $Params['Username'], $Params['Password']);
//		if (!$From)
//			$From = $Params['From'];
//		$Mail->set('from', $From);
//		if ($To)
//			$Mail->set('to', $To);
//		if (isset($Subject))
//			$Mail->set('subject', $Subject);
//		return $Mail;
//	}
//	static function SendMail(
//	$To
//	, $Subject
//	, $Content
//	, $From = NULL
//	) {
//		return self::GetObj($To, $Subject, $From)->send($Content);
//	}
}

?>
