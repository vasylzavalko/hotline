<?php
namespace App\Controllers;

class Auth extends BaseController
{
    
    public function __construct()
    {	
        
        $this->db = \Config\Database::connect();
        $this->model = model('App\Models\Auth', false);
        $this->email = model('App\Models\Email', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->authVerification = $_ENV['AuthVerification'];
        $this->time = time();
        $this->session = session();
        
        $this->uri = service('uri');
        $this->segment_1 = $this->uri->getSegment(1);
        
    }
    
    public function index()
    {
        return redirect()->to( '/login');
    }
    
    public function loginForm()
    {

        $user = [];
        $this->model->isLoginRedirect($this->segment_1);
        $submit = $this->request->getVar('submit');
        
        if ($submit=='Submit_login') {
            
            $userCode = $this->model->generateRandomString(6);
            $builder = $this->db->table($this->prefix.'user');
            $builder->where('email', $this->request->getVar('email'));
            $query = $builder->get();
            foreach ($query->getResult() as $item) {
                foreach ($item as $key => $value) {
                    $user[$key] = $value;
                }
            }
            if ($user) {				
                if ( password_verify($this->request->getVar('password'),$user['pass']) ) {
                    
                    if( $this->authVerification==0 ){
                        $newdata = [
                            'user_id'		=> $user['id'],
                            'logged_in'		=> true
                        ];
                        $this->session->set($newdata);
                        $this->model->saveLlog($user['id'],lang('Log.login_confirm'));
                    } else {
                        $newdata = [
                            'user_id'		=> $user['id'],
                            'user_code'		=> $userCode,
                            'logged_code'		=> true
                        ];
                        $this->session->set($newdata);
                        $data = [
                            'user_id' => $user['id'],
                            'code' => $userCode,
                            'date_add' => time(),
                        ];
                        $this->model->_insert($data,"user_login");
                        $data_mail['to_email'] = $user['email'];
                        $data_mail['subject'] = lang('Auth.email_login_confirm_subject');
                        $data_mail['message'] = lang('Auth.email_login_confirm',[$userCode]);
                        $this->email->sendEmail($data_mail);
                        $this->model->saveLlog($user['id'],lang('Log.send_login_code'));
                    }
                    
                } else {
                    $this->model->saveLlog($user['id'],lang('Log.login_pass_error'));
                }
            } else {
                $this->model->saveLlog(0,lang('Log.login_email_error'));
            }
            return redirect()->to( current_url(true) );
        }	

        $data['pageTitleSeo'] = lang('Auth.title_auth_login');
        $data['time'] = $this->time;
        
        echo view('auth/_header',$data);
        echo view('auth/login',$data);
        echo view('auth/_footer');
        
    }	

    public function loginConfirm()
    {

        $this->model->isLoginRedirect($this->segment_1);
        
        $timeСonfirm = 120;
        $lastСonfirm = [];
        $leftTime = 0;
        $user = [];
        
        $builder = $this->db->table($this->prefix.'user_login');
        $builder->where('user_id', $_SESSION['user_id']);
        $builder->where('status', 0);
        $builder->orderBy('date_add', 'DESC');
        $builder->limit(1);
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ($item as $key => $value) {
                $lastСonfirm[$key] = $value;
            }
            $leftTime = $lastСonfirm['date_add'] + $timeСonfirm - time();
        }
        
        if ($leftTime<1) {
            $data = [ 'status' => 2 ];
            $where = "id = ".$lastСonfirm['id'];
            $this->model->_update($data,$where,"user_login");
            $this->model->saveLlog($lastСonfirm['user_id'],lang('Log.login_code_left'));
            unset($_SESSION['user_id']);
            unset($_SESSION['user_code']);
            unset($_SESSION['logged_in']);
            unset($_SESSION['logged_code']);
            return redirect()->to( "/login" );
        }
        
        $submit = $this->request->getVar('submit');
        if ($submit=='Submit_code') {
            if ( $this->request->getVar('code') == $lastСonfirm['code'] ) {
                unset($_SESSION['user_id']);
                unset($_SESSION['user_code']);
                unset($_SESSION['logged_in']);
                unset($_SESSION['logged_code']);
                $newdata = [
                    'user_id'		=> $lastСonfirm['user_id'],
                    'logged_in'		=> true
                ];
                $this->session->set($newdata);
                $data = [ 'status' => 1 ];
                $where = "id = ".$lastСonfirm['id'];
                $this->model->_update($data,$where,"user_login");
                $this->model->saveLlog($lastСonfirm['user_id'],lang('Log.login_confirm'));
            } else {
                $this->model->saveLlog($lastСonfirm['user_id'],lang('Log.login_code_error'));
            }
            return redirect()->to( current_url(true) );
        }		
        
        $data['leftTime'] = $leftTime;
        $data['pageTitleSeo'] = lang('Auth.title_auth_login');
        $data['time'] = $this->time;
        
        echo view('auth/_header',$data);
        echo view('auth/login_confirm',$data);
        echo view('auth/_footer');
        
    }

    public function logout()
    {
        if ( isset($_SESSION['user_id']) ){
            $this->model->saveLlog($_SESSION['user_id'],lang('Log.logout'));
        } else {
            $this->model->saveLlog(0,lang('Log.logout'));
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_code']);
        unset($_SESSION['logged_in']);
        unset($_SESSION['logged_code']);
        return redirect()->to( '/login');
    }
    
}
