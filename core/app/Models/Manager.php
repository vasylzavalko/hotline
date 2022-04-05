<?php namespace App\Models;
use CodeIgniter\Model;

class Manager extends Model
{
	 
	function __construct() {
        
        $this->nameViber = $_ENV['BotName'];
        $this->tokenViber = $_ENV['ViberKey'];
		$this->token = $_ENV['TelegramKey'];
        $this->bgColor = "#ffcc33";
		$this->prefix = $_ENV['DataBasePrefix'];
		$this->db = \Config\Database::connect();
		$this->method = "sendMessage";
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
	
	function isLogin($segment){
		
		$user = [];
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
				foreach ($item as $key=>$value) {
					$user[$key] = $value;
				}
				$user['group_slug'] = $user_group[$user['group_id']];
				unset($user['pass']);
				unset($user['code_login']);
				unset($user['code_recovery']);
				unset($user['date_recovery']);
			}
			
			if ($user['group_id']==0) {
				header("Location: /");
				exit();	
			}			
			if ($segment!=$user['group_slug']) {
				header("Location: /".$user['group_slug']);
				exit();	
			}
			
		}else{
			header("Location: /login");
			exit();	
		}
		return $user;
	}
	
	function sendMessage($dataMessage){
		
		if(isset($dataMessage['method'])){
			$method = $dataMessage['method'];
		}else{
			$method = "sendMessage";
		}
		
		$data = [
			'chat_id' => $dataMessage['chat_id'],
			'text' => $dataMessage['message'],
			'parse_mode' => 'html',
		];
		
        if(isset($dataMessage['reply_markup'])){
            $data['reply_markup'] = $dataMessage['reply_markup'];
        }
        
		$this->sendTelegram($method,$data);
		
	}
	
	function sendTelegram($method = "sendMessage", $data = array()) {
		
		$curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token .  '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $out;	
	
	}	
	
    function sendMessageViber($sender_id, $text, $type="text", $keyboard=[])
    {
        
        $data['min_api_version'] = 3; 
        $data['auth_token'] = $this->tokenViber;
        $data['sender']['name'] = $this->nameViber;
        $data['receiver'] = $sender_id;
        $data['type'] = $type;
        if (count($keyboard)>0) {
            $data['keyboard'] = [ "Type" => "keyboard", "Buttons" => $keyboard ];              
        }
        if($text !== Null) {
            $data['text'] = $text;
        }
        
        $this->sendRequestViber($data);
        
    }    
    
    
    function sendRequestViber($request)
    {
        
        $request_data = json_encode($request);
        $ch = curl_init("https://chatapi.viber.com/pa/send_message");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if($err) {
            return $err;
        }
        else {
            return $response;
        }
        
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
	
}