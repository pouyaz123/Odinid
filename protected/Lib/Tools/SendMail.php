<?php

namespace Tools;

use \Tools as T;
use \Consts as C;
use \Components as Com;

/**
 * Tondarweb SMTP
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb portal migrated to Odinid cg network
 * @version 2
 * @copyright (c) Odinid
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
//		$SMTP->IsSMTP();	//depracated
		$SMTP->Mailer = 'smtp';
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
	 * This method uses \Yii::app()->controller to render the template so a controller must be exist in the execution
	 * @param string $TemplateName currently we have used simple templates so<br/>
	 * this is same as the filename of the template<br/>
	 * but later we will use a database model like webdesignir named.com and this will be same as the logicname
	 * @param string $Lang the language code in template files model will be the name of parent directory of the template file<br/>
	 * default lang has been got via \t2::GetDefaultLang()->LangCode
	 * @return string email template html contents
	 */
	static function GetEmailTemplate($TemplateName, $Lang = null, $Data = null) {
		if (!$Lang)
			$Lang = \t2::GetDefaultLang()->LangCode;
		$TemplateFilePath = "Site.views.layouts.email_templates.$Lang.$TemplateName";
		if (!is_file(\Yii::getPathOfAlias($TemplateFilePath) . '.php'))
			\Err::ErrMsg_Method(__METHOD__, "The email template doesn't exist", array($TemplateFilePath, func_get_args()));
		return \Yii::app()->controller->renderPartial($TemplateFilePath, $Data, true);
	}

	/**
	 * This method forces you to set the Subject in the side of content
	 * uses ->Subject and ->MsgHTML($HTMLBody) and ->Send() sequentially
	 * @param type $Subject required email subject
	 * @param type $HTMLBody Text to be HTML modified
	 * @param string $basedir baseline directory for path
	 * @return bool result of ->Send()
	 */
	function Send2($Subject, $HTMLBody, $basedir = '') {
		$this->Subject = $Subject;
		$this->MsgHTML($HTMLBody, $basedir);
		return $this->Send();
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
