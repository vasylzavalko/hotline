<?php namespace App\Models;
use CodeIgniter\Model;

class Email extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->prefix = $_ENV['DataBasePrefix'];
    }
    
    static function sendEmail($data_mail) 
    {
        $year = date('Y');
        $from_email = $_ENV['EmailFrom'];
        $from_name = $_ENV['EmailName'];
        $to_email = $data_mail['to_email'];
        $subject = $data_mail['subject'];
        $message = $data_mail['message'];
        
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.lang('Email.EmailTitle').'</title>
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
                            <tr><td style="background:#FFFFFF; padding:15px 0px 15px 0px;" align="center">'.lang('Email.EmailTitle').'</td></tr>
                            <tr><td style="background:none; border:solid 1px #cccccc; border-width:1px 0 0 0; height:1px; width:100%; margin:0px; padding:0px;">&nbsp;</td></tr>
                            <tr><td style="padding:0px 0px 15px 0px;">'.$message.'</td></tr>
                            <tr><td style="background:none; border:solid 1px #cccccc; border-width:1px 0 0 0; height:1px; width:100%; margin:0px; padding:0px;">&nbsp;</td></tr>
                            <tr><td style="padding:0px 0px 15px 0px;color:#999999;font-size:12px;"><p style="margin:0px;padding:2px 0px;text-align:center">'.$_ENV['EmailName'].'<br>'.base_url().'</p></td></tr>
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
            'protocol'	=> $_ENV['EmailProtocol'],
            'SMTPHost'	=> $_ENV['EmailHost'],
            'SMTPPort'	=> $_ENV['EmailPort'],
            'SMTPUser'	=> $_ENV['EmailUser'],
            'SMTPPass'	=> $_ENV['EmailPass'],
            'SMTPCrypto'	=> $_ENV['EmailCrypto'],
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

    function sendInfo($type,$appealId){

        // Appeal
        $appeal = [];
        $builder = $this->db->table($this->prefix.'appeal as a');
        $builder->select('a.*, s.title as status_title');
        $builder->join($this->prefix.'status as s', 'a.status = s.id');
        $builder->where('a.id', $appealId);
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ( $item as $key=>$value ){
                $appeal[$key] = $value;
            }
        }
        
        // Users
        $appealUser = [];
        $appealUserEmail = "";
        $appealUserHead = "";
        $appealUserImplementer = "";
        $builder = $this->db->table($this->prefix.'appeal_user as au');
        $builder->select('au.*, u.email, u.title, u.first_name, u.last_name');
        $builder->join($this->prefix.'user as u', 'u.id = au.user_id');
        $builder->where('au.appeal_id', $appealId);
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            $appealUser[$item->group_id][$item->user_id]['user_id'] = $item->user_id;
            $appealUser[$item->group_id][$item->user_id]['group_id'] = $item->group_id;
            $appealUser[$item->group_id][$item->user_id]['email'] = $item->email;
            $name = (!empty($item->first_name))?$item->first_name." ":"";
            $name .= (!empty($item->last_name))?$item->last_name:"";
            $name .= (!empty($name) AND !empty($item->title))?", ".$item->title:"";
            $name .= (empty($name) AND !empty($item->title))?$item->title:"";
            $appealUser[$item->group_id][$item->user_id]['name'] = $name;
            if($item->group_id==2){
                $appealUserHead .= $name."; ";
            }
            if($item->group_id==3){
                $appealUserImplementer .= $name."; ";
            }
            $appealUserEmail .= $item->email.", ";
        }
        $appealUserEmail = trim($appealUserEmail, ", ");
        $appealUserHead = trim($appealUserHead, "; ");
        $appealUserImplementer = trim($appealUserImplementer, "; ");
 
        $data_mail['to_email'] = "vasylzavalko@gmail.com, levsha127@ukr.net";
        $data_mail['to_email'] = $appealUserEmail;
        $data_mail['subject'] = lang('Email.EmailAppealId').$appealId;
        
        $message = "";
        switch ($type) {
            case "add_date":
                $message .= "<p><b>".lang('Email.EmailAppealAddDate')."</b>";
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "add_status":
                $message .= "<p><b>".lang('Email.EmailAppealUpdateStatus')."</b>".$appeal['status_title'];
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "add_head":
                $message .= "<p><b>".lang('Email.EmailAppealAddHead')."</b>".$appealUserHead;
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "add_implementer":
                $message .= "<p><b>".lang('Email.EmailAppealAddImplementer')."</b>".$appealUserImplementer;
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "appeal_request":
                $message .= "<p><b>".lang('Email.EmailAppealRequest')."</b>";
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "appeal_request_approve":
                $message .= "<p><b>".lang('Email.EmailAppealRequestApprove')."</b>";
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
            case "appeal_request_reject":
                $message .= "<p><b>".lang('Email.EmailAppealRequestReject')."</b>";
                $message .= "</p><p>".lang('Email.EmailAppealId').$appealId;
                $message .= "<br>".lang('Email.EmailDate').date("Y-m-d H:i:s",time());
                $message .= "</p>";
                break;
        }
        $data_mail['message'] = $message;
        Email::sendEmail($data_mail);
        
    }

}