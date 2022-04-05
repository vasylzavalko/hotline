<?php
namespace App\Controllers;

class Viber extends BaseController
{
    
    function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->modelViber = model('App\Models\Viber', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->name = $_ENV['BotName'];
    }
    
    public function index()
    {
        echo '<a href="viber://pa?chatURI='.$this->name.'&context=start">Viber Bot</a>';
    }

    public function webhook()
    {
        $message = $this->modelViber->getMessage();
        $this->modelViber->saveMessages($message);
        $this->modelViber->processMessages($message);
    }

    public function setWebhook()
    {
        $webhook = base_url()."/viber/webhook";
        $this->modelViber->setWebhook($webhook);
    }
}
