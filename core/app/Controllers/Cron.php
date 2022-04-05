<?php
namespace App\Controllers;

class Cron extends BaseController
{
    public function __construct()
    {	
        $this->db = \Config\Database::connect();
        $this->model = model('App\Models\Manager', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->bgColor = "#ffcc33";
    }
    
    public function index()
    {

        $start = microtime(true);
        set_time_limit(65);

        $package = [];
        $builder = $this->db->table($this->prefix.'message_package');
        $builder->where('status',1);
        $builder->orderBy('id', 'DESC');
        $builder->limit(1);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            foreach ($item as $key=>$value){
                $package[$key] = $value;
            }
        }
        
        if(count($package)>0){
            
            $users = [];
            $builder = $this->db->table($this->prefix.'message_needsend as n');
            $builder->select('n.id, n.user_id, u.chat_id, u.sender_id');
            $builder->where('n.package_id', $package['id']);
            $builder->join($this->prefix.'bot_user as u', 'u.id = n.user_id');
            $builder->limit(60);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $users[] = [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'chat_id' => $item->chat_id,
                    'sender_id' => $item->sender_id,
                ];
            }            
            
            for ($i = 0; $i < 59; ++$i) {
                if( !isset($users[$i]) ){
                    $this->chekPackage($package);
                    break;
                }
                $time = $start + $i + 1;
                $this->saveTest($users[$i]['user_id']."-".$time);
                $this->sendMessage($package, $users[$i]);
                time_sleep_until($time);
            }
        }

    }
    
    public function sendMessage($package, $user)
    {
        $message = $package['message']."\n\n".lang('Bot.MessageNotNeedAnswer');
        if(!empty($user['chat_id'])){
            $button[] = [[ 'text' => lang('Bot.ButtonBackToStart'), 'callback_data'=>'/start']];
            $keyboard = [
                'inline_keyboard' => $button,
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ];
            $dataMessage = [
                'chat_id' => $user['chat_id'],
                'method' => 'sendMessage',
                'message' => $message,
                'reply_markup' => json_encode($keyboard),
            ];                            
            $this->model->sendMessage($dataMessage);
        }
        if(!empty($user['sender_id'])){
            $keyboard = [
                [
                    "ActionBody" => "/start",
                    "Text" => "<b>".lang('Bot.ButtonBackToStart')."</b>",
                    "Columns" => 6,
                    "Rows" => 1,
                    "BgColor" => $this->bgColor,
                ],
            ];
            $this->model->sendMessageViber($user['sender_id'], $message, "text", $keyboard);
        }
        $where = "id = ".$user['id'];
        $this->model->_delete($where,'message_needsend');        
    }

    public function chekPackage($package)
    {
        $builder = $this->db->table($this->prefix.'message_needsend');
        $builder->where('package_id', $package['id']);
        $builder->limit(1);
        $users = $builder->countAllResults();
        if($users==0){
            $data['status'] = 2;
            $data['date_done'] = time();
            $where = "id = ".$package['id'];
            $this->model->_update($data,$where,'message_package');            
        }
    }
    
    public function saveTest($test)
    {
        $data['test'] = $test;
        $this->model->_insert($data,'test');
    }
     
}