<?php
use PHPMailer\PHPMailer\PHPMailer;

class MAILER_FACTORY
{
  protected static $mail;

  function __construct()
  {
    if (!isset(self::$mail)) {
      self::$mail = new PHPMailer(true);
      //   self::$mail->SMTPDebug = SMTP::DEBUG_SERVER;
      self::$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      self::$mail->SetLanguage('br', 'phpmailer/language/');
      //self::$mail->CharSet = "UTF-8";
      self::$mail->CharSet = "iso-8859-1";
      self::$mail->IsSMTP();
      self::$mail->SMTPAuth = true;
      self::$mail->Host = "smtp.hostinger.com";
      self::$mail->Port = 587;
      self::$mail->Username = "mdaaps@pexsys.info";
      self::$mail->Password = "4Uo1o0N81+";
      self::$mail->IsHTML(true);
      self::$mail->SetFrom(self::$mail->Username, Formatter::UTF8Decode("SGE-MDA/APS"));
      // self::$mail->AddReplyTo(self::$mail->Username, utf8_decode("Email automático do SGE-MDA/APS"));
    }
  }

  public static function Instance()
  {
    return new self();
  }

  public function Get()
  {
    return self::$mail;
  }
}

class Mail
{
  public static function Get()
  {
    return MAILER_FACTORY::Instance()->Get();
  }
}
