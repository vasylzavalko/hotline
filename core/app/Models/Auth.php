<?php namespace App\Models;
use CodeIgniter\Model;

class Auth extends Model
{
	 
	function __construct() {
		$this->prefix = $_ENV['DataBasePrefix'];
		$this->db = \Config\Database::connect();
	}
	
	function isLoginRedirect($segment="login"){
		$group_id=0;
		if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in']==TRUE ) {
			$builder = $this->db->table($this->prefix.'user_group');
			$query = $builder->get();
			foreach ($query->getResult() as $item) {
				$user_group[$item->id] = $item->slug;
			}
			$builder = $this->db->table($this->prefix.'user');
			$builder->where('id', $_SESSION['user_id']);
			$query = $builder->get();
			foreach ($query->getResult() as $item) {
				$group_id = $item->group_id;
			}
			if ($group_id==0) {				
				header("Location: /");
				exit();
			}else{
				header("Location: ".$user_group[$group_id] );
				exit();	
			}
			if ( $segment != "login-code" ){
				header("Location: /login" );
				exit();
			}
		} elseif ( isset($_SESSION['logged_code']) && $_SESSION['logged_code']==TRUE ) {
			if ( $segment != "login-code" ){
				header("Location: /login-code" );
				exit();
			}
		} elseif ( !isset($_SESSION['logged_code']) && !isset($_SESSION['logged_in']) && $segment == "login-code"  ) {
			header("Location: /login" );
			exit();
		}
		
	}

	function generateRandomString($length) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	function saveLlog($user,$message){
		$data = [
			'user_id' => $user,
			'date_add' => time(),
			'log' => $message,
			'ip' => $_SERVER['REMOTE_ADDR'],
		];
		$this->_insert($data,"user_log");
	}
	
	function _insert($data,$table) {
		$builder = $this->db->table($this->prefix.$table);
		$builder->insert($data);
		return $this->db->insertID();
	}
	
	function _update($data,$where,$table) {
		$builder = $this->db->table($this->prefix.$table);
		$builder->where($where);
		$builder->update($data);
	}

	function _delete($where,$table) {
		$builder = $this->db->table($this->prefix.$table);
		$builder->where($where);
		$builder->delete();
	}	

	function send_mail($data_mail) 
	{
		$year = date('Y');
		$from_email = EMAIL_FROM;
		$from_name = EMAIL_NAME;
		$to_email = $data_mail['to_email'];
		$subject = $data_mail['subject'];
		$message = $data_mail['message'];
		
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.EMAIL_NAME.'</title>
<style type="text/css">
body {margin: 0; padding: 0; min-width: 100%!important;}
.container {width: 100%; max-width: 600px;background:#ffffff;}
@media only screen and (min-device-width: 601px) { .container {width: 600px !important;} }
.content {padding:20px;background:#ff0000;}
a{color:#0066cc;}
h1{font-size:24px;font-weight:700;}
h2{font-size:18px;font-weight:700;}
hr{border-top: 1px solid #cccccc;}
</style>
</head>
<body yahoo bgcolor="#eeeeee"><table width="100%" bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0"><tr><td style="padding:15px;">
<!--[if (gte mso 9)|(IE)]>
<table width="600" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
    <tr>
        <td>
            <![endif]-->
            <table class="container" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
                <tr>
					<td style="padding:15px;">
						<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr><td style="background:#FFFFFF; padding:15px 0px 15px 0px;" align="center">'.EMAIL_NAME.'</td></tr>
							<tr><td style="background:none; border:solid 1px #cccccc; border-width:1px 0 0 0; height:1px; width:100%; margin:0px; padding:0px;">&nbsp;</td></tr>
							<tr><td style="padding:0px 0px 15px 0px;">'.$message.'</td></tr>
							<tr><td style="background:none; border:solid 1px #cccccc; border-width:1px 0 0 0; height:1px; width:100%; margin:0px; padding:0px;">&nbsp;</td></tr>
							<tr><td style="padding:0px 0px 15px 0px;color:#999999;font-size:12px;"><p style="margin:0px;padding:2px 0px;text-align:center">'.EMAIL_NAME.'<br>'.base_url().'</p></td></tr>
						</table>
					</td>
				</tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
        </td>
    </tr>
</table>
<![endif]-->
</td></tr></table></body></html>';		
		
		$config = Array(
			'protocol'	=> EMAIL_PROTOCOL,
			'SMTPHost'	=> EMAIL_SMTP_HOST,
			'SMTPPort'	=> EMAIL_SMTP_PORT,
			'SMTPUser'	=> EMAIL_SMTP_USER,
			'SMTPPass'	=> EMAIL_SMTP_PASS,
			'SMTPCrypto'	=> EMAIL_SMTP_CRYPTO,
			'mailType'	=> 'html',
			'charset'   => 'utf-8',
			'CRLF'		=> '\r\n',
			'newline'	=> '\r\n'
		);
		
		$email = \Config\Services::email();
		$email->initialize($config);
		$email->setFrom($from_email, $from_name);
		$email->setTo($to_email);
		$email->setSubject($subject);
		$email->setMessage($html);
		if(isset($data_mail['file'])){$email->attach($data_mail['file']);}
		$email->send();
		
	}
	

	
}