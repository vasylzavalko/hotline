<?php
namespace App\Controllers;

class Telegram extends BaseController
{ 
    
    public function __construct()
    {		
        $this->db = \Config\Database::connect();
        $this->modelTelegram = model('App\Models\Telegram', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->name = $_ENV['BotName'];
    }	

    public function index()
    {
        echo '<a href="https://t.me/'.$this->name.'?start">Telegram Bot</a>';
    }

    public function webhook()
    { 
        $message = $this->modelTelegram->getMessage();
        $this->modelTelegram->saveMessage($message);
        $message = $this->modelTelegram->processMessages($message);
        $this->modelTelegram->sendMessage($message);		
    }
    
    public function setwebhook()
    {
        $method = "setWebhook";
        $data = "url=". base_url() ."/telegram/webhook";
        var_dump( $this->modelTelegram->request($method,$data) );
    }
}
