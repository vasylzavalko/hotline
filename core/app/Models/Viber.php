<?php namespace App\Models;
use CodeIgniter\Model;

class Viber extends Model
{

	function __construct()
    {
        $this->token = $_ENV['ViberKey'];
        $this->name = $_ENV['BotName'];
		$this->prefix = $_ENV['DataBasePrefix'];
		$this->db = \Config\Database::connect();
        $this->bgColor = "#ffcc33";
        $this->greenColor = "#42f330";
        
        // Commands
		$this->comandAll = [
			'/start' => '/start',
			lang('Bot.ButtonStart') => '/start',
			lang('Bot.ButtonBackToStart') => '/start',
			lang('Bot.ButtonCancel') => '/start',
			'/list' => '/list',
			lang('Bot.ButtonList') => '/list',
			'/appeal' => '/appeal',
			lang('Bot.ButtonAddAppeal') => '/appeal',
			'/appealeditcontent' => '/appealeditcontent',
			lang('Bot.ButtonAppealEditContent') => '/appealeditcontent',
			'/appealeditaddres' => '/appealeditaddress',
			lang('Bot.ButtonAppealEditAddress') => '/appealeditaddress', 			
			lang('Bot.ButtonConfirm') => '/appealconfirm',
			'/my' => '/my',
			lang('Bot.ButtonMy') => '/my',
			'/myedit' => '/myedit',
			lang('Bot.ButtonMyEdit') => '/myedit',
			'/editfirstname' => '/editfirstname',
			lang('Bot.ButtonMyEditFirstName') => '/editfirstname',			
			'/editlastname' => '/editlastname',
			lang('Bot.ButtonMyEditLastName') => '/editlastname',
			'/editcity' => '/editcity',
			lang('Bot.ButtonMyEditCity') => '/editcity',
			'/editaddress' => '/editaddress',
			lang('Bot.ButtonMyEditAddress') => '/editaddress',
			'/editemail' => '/editemail',
			lang('Bot.ButtonMyEditEmail') => '/editemail',
			'/help' => '/help',
			lang('Bot.ButtonHelp') => '/help',
            '/appealcontent' => '/appealcontent',
            '/appealaddress' => '/appealaddress',
            '/appealemail' => '/appealemail',
            '/appealchat' => '/appealchat',
		];
        $this->commandList = array_keys($this->comandAll);
        
    }
    
	function getMessage() 
    {
		$data = file_get_contents('php://input');
		$data = json_decode($data, true);
		return $data;
	}
    
    function saveMessages($message)
    {
        
        $event = $message['event'];
        if ($event == 'message') {
            $type = $message['message']['type'];
            switch($type){
                case "text": 
                    $this->saveText($message);
                break;
                case "picture":
                    $this->savePicture($message);
                break;
                case "video":
                    
                break;
                case "contact":
                    
                break;
                case "URL":
                    
                break;
                case "location":
                    $this->saveLocation($message);
                break;
            }
        }
    }

    function saveText($message)
    {
        $senderId = $message['sender']['id'];
        $text = $message['message']['text'];
        
        if (in_array($text, $this->commandList)) {
            
            // Save Command
            $this->saveCommand($senderId, $text);
            
        } else {
 
            // Save Message
            $commandLast = $this->commandLast($senderId);
            
            switch($commandLast){
                case "/editfirstname":
                    $this->saveSenderData($senderId, "first_name", $text);
                break;
                case "/editlastname":
                    $this->saveSenderData($senderId, "last_name", $text);
                break;
                case "/editcity":
                    $this->saveSenderData($senderId, "city", $text);
                break;
                case "/editaddress":
                    $this->saveSenderData($senderId, "address", $text);
                break;
                case "/editemail":
                    $this->saveSenderData($senderId, "email", $text);
                break;
                case "/appeal":
//                    $this->saveAppealData($message, "content", $text);
                break;
                case "/appealcontent":
                    if($text!="/appealview"){
                        $this->saveAppealData($message, "content", $text);                    
                        $this->saveCommand($senderId, "/appeal");
                    }
                break;
                case "/appealaddress":
                    if($text!="/appealview"){
                        $this->saveAppealData($message, "address", $text);
                        $this->saveCommand($senderId, "/appeal");
                    }
                break;
                case "/appealemail":
                    $this->saveSenderData($senderId, "email", $text);
                break;
                default:
                    
                    if ( strpos($commandLast, '/chatmessage_') === 0 ) {
                        $textArray = explode("_",$commandLast);
                        if(isset($textArray[1]) AND count($textArray)==2){ 
                            $this->saveChatData($message, $senderId, $textArray[1], $text);
                        }
                    }
                    if ( strpos($commandLast, '/appealrating_') === 0 ) {
                        $textArray = explode("_",$commandLast);
                        if(isset($textArray[1]) AND count($textArray)==2){ 
                            $this->saveRating($senderId, $textArray[1], $text);
                        }
                    }
                break;
            }
            
        }
        
        $data = [
            'sender_id' => $senderId,
            'date_add' => time(),
            'message' => $text,
        ];
        $this->_insert($data,"viber_message");
        
    }    
    
    function savePicture($message)
    {
        $senderId = $message['sender']['id'];
        $checkUser = $this->checkUser($message);
        $appeal = $this->appealLast($checkUser['id']);
        $commandLast = $this->commandLast($senderId);
        
        if (isset($appeal['id'])) {
            if ( $commandLast=="/appeal" || $commandLast=="/appealcontent" || $commandLast=="/appealaddress" || $commandLast=="/appealview" ) {
                
                $media = $message['message']['media'];
                $fileName = $message['message']['file_name'];
                $ext = explode(".", $fileName);
                $size = $message['message']['size'];
                $fileName = time()."_".rand(100,999);
                $fileName = $fileName.".".$ext[1];
				$filePath =  $_SERVER["DOCUMENT_ROOT"]."/assets/upload/appeal/".$appeal['id'];
				if (!file_exists($filePath)) {
					mkdir($filePath, 0777, true);
				}
				$fileUpload =  $filePath."/".$fileName;
						
				if( copy($media, $fileUpload) ){
                    $data = [
                        'appeal_id' => $appeal['id'],
                        'file_size' => $size,
                        'image' => $fileName
                    ];
                    $this->_insert($data,"appeal_gallery");
				}
            }
        }
    }
    
    function saveLocation($message)
    {
        $senderId = $message['sender']['id'];
        $checkUser = $this->checkUser($message);
        $appeal = $this->appealLast($checkUser['id']);
        $commandLast = $this->commandLast($senderId);
        
        if (isset($appeal['id'])) {
            if ( $commandLast=="/appeal" || $commandLast=="/appealcontent" || $commandLast=="/appealaddress" || $commandLast=="/appealview" ) {
                $lat = $message['message']['location']['lat'];
                $lon = $message['message']['location']['lon'];
                $where = "id = ".$appeal['id'];
                $data['location_lat'] = $lat;
                $data['location_lng'] = $lon;
                $this->_update($data, $where, "appeal");
            }
        }
    }
    
    function saveSenderData($senderId, $field, $value)
    {
        if ($field=="email") {
            if (filter_var($value, FILTER_VALIDATE_EMAIL) ){
                $data[$field] = $value;
                $data['emailskip'] = 0;
                $where = "sender_id = '".$senderId."'";
                $this->_update($data, $where, "bot_user");
            }
        } else {
            $data[$field] = $value;
            $where = "sender_id = '".$senderId."'";
            $this->_update($data, $where, "bot_user");
        }
    }

    function saveAppealData($message, $field, $text)
    {
        $checkUser = $this->checkUser($message);
		$appeal = $this->appealLast($checkUser['id']);
        $where = "id = ".$appeal['id'];
        $data[$field] = $text;
        $this->_update($data, $where, "appeal");
    }

    function saveChatData($message,$senderId, $appealId, $text)
    {
     
        if ( strpos($text, '/') === 0 ) {
            
        }else{
            $checkUser = $this->checkUser($message);
            $data = [
                'appeal_id' => $appealId,
                'user_id' => $checkUser['id'],
                'date_add' => time(),
                'message' => $text,
                'who' => 1,
            ];
            $this->_insert($data, "appeal_chat");
            $this->saveCommandAll($senderId, "/chat_".$appealId);
            $this->saveCommand($senderId, "/chat_".$appealId);
        }
    }

    function saveRating($senderId, $appealId, $text)
    {
        if ( is_numeric($text) ) {
            $ratingId = 0;
            $builder = $this->db->table($this->prefix."appeal_rating");
            $builder->where('appeal_id',$senderId);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $ratingId = $item->id;
            }
            if($ratingId==0){
                $data = [
                    'appeal_id' => $appealId,
                    'rating' => $text,
                    'date_add' => time()
                ];
                $this->_insert($data,"appeal_rating");
            }
            $this->saveCommandAll($senderId, "/rating_".$appealId);            
        }
    }
    
    function saveCommand($senderId, $command)
    {
        if (in_array($command, $this->commandList)) {
            $command = $this->comandAll[$command];
            $data = [
                'sender_id' => $senderId,
                'date_add' => time(),
                'command' => $command,
            ];
            $this->_insert($data, "viber_command");
        } elseif ( $command=="/save" ) {
            $data = [
                'sender_id' => $senderId,
                'date_add' => time(),
                'command' => $command,
            ];
            $this->_insert($data, "viber_command");
        }
    }
    
    function saveCommandAll($senderId, $command)
    {
        $data = [
            'sender_id' => $senderId,
            'date_add' => time(),
            'command' => $command,
        ];
        $this->_insert($data, "viber_command");
    }
    
    function processMessages($message)
    {
        
        $event = $message['event'];
        
        if ($event == 'webhook') {
            
            // Activation Webhook
            $webhook_response['status']=0;
            $webhook_response['status_message']="ok";
            $webhook_response['event_types']=["delivered", "seen"];
            echo json_encode($webhook_response);
            die;
        } elseif ($event == "subscribed") {
            
            // User Subscribed
            $senderId = $message['user']['id'];
            $this->processStart($senderId);
            
        } elseif ($event == "conversation_started") {
        
            // User Start
            $senderId = $message['user']['id'];
            $this->processStart($senderId);
            
        } elseif ($event == "message") {
            
            // User Send Message
            $checkUser = $this->checkUser($message);
            $senderId = $message['sender']['id'];
            $type = $message['message']['type'];
            $text = ($type == "text")?$message['message']['text']:"";
            
            if( $checkUser['step']>5 AND $checkUser['active']==0 AND $text=="/startskipemail" ) {
                $this->saveCommand($senderId, "/start");                
                $this->saveSenderData($senderId, "active", 1);
                $this->saveSenderData($senderId, "emailskip", 1);
                $checkUser['active']=1;
                $message['message']['text'] = "/start";
            }
            
            if ( $checkUser['step']==1 ){
                
                // Not Registered
                $this->processRegisterStart($message,$checkUser);
                
            } elseif ( $checkUser['active']==0 ) {
                
                // Registration Not Completed
                $this->processRegister($message,$checkUser);
                
            } else {
                
                // Ready to work
                switch($type){       
                    case "text": 
                        $this->messageText($message,$checkUser);
                    break;
                    case "picture":
                        $this->messagePicture($message,$checkUser);
                    break;
                    case "video":
                        $this->messageVideo($message,$checkUser);
                    break;
                    case "contact":
                        $this->messageContact($message,$checkUser);
                    break;
                    case "URL":
                        $this->messageURL($message,$checkUser);
                    break;
                    case "location":
                        $this->messageLocation($message,$checkUser);
                    break;
                }
            }
        }
    }

    function processStart($senderId)
    {
        $requestText = "\xF0\x9F\x93\x9D *".lang('Bot.MessageStartTitle')."*\n\n";
        $requestText .= lang('Bot.MessageStartDescription')."\n\n";
//        $requestText .= lang('Bot.MessageStartTesting_1')."\n\n";
//        $requestText .= lang('Bot.MessageStartTesting_2')."\n\n";
        
        $type = "text";
        $keyboard = [
            [
                "ActionBody" => "/start",
                "Text" => "<b>".lang('Bot.ButtonStart')."</b>",
                "Columns" => 6,
                "Rows" => 1,
                "BgColor" => $this->bgColor,
            ],
        ];
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        $this->saveCommand($senderId, "/start");
    }
    
    function processRegisterStart($message,$checkUser)
    {

        $senderId = $message['sender']['id'];
        $senderName = $message['sender']['name'];
        
        $type = $message['message']['type'];
        switch($type){       
            case "text": 
                $requestText = "\xF0\x9F\x93\x9D **".lang('Bot.MessageStartTitle')."**\n\n";
//                $requestText .= lang('Bot.MessageStartTesting_1')."\n\n";
//                $requestText .= lang('Bot.MessageStartTesting_2')."\n\n";
                $requestText .= lang('Bot.MessageStartDescription')."\n\n";
                $requestText .= "\xF0\x9F\x93\xA2 ".lang('Bot.MessageStartInform')."\n\n";
                $requestText .= "\xE2\x9D\x97 ".lang('Bot.MessageStartWarning')."\n\n";
                $requestText .= lang('Bot.MessageStartRegister');
                $type = "text";
                $keyboard = [
                    [
                        "ActionType" => "share-phone",
                        "ActionBody" => "reply",
                        "Text" => "<b>".lang('Bot.ButtonStartRegister')."</b>",
                        "Columns" => 6,
                        "Rows" => 1,
                        "BgColor" => $this->bgColor,
                    ],
                ];
                $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            break;
            case "contact": 
            
                $phoneNumber = $message['message']['contact']['phone_number'];
                $phoneNumber = $this->clearPhone($phoneNumber);

                $builder = $this->db->table($this->prefix."bot_user");
                $builder->where('phone',$phoneNumber);
                $query = $builder->get();
                if ($query->getResult()) {
                    
                    // Update Sender ID If Isset Phone
                    foreach ($query->getResult() as $item) {
                        $userId = $item->id;
                    }
                    $data['sender_id'] = $senderId;
                    $where = "id = ".$userId;
                    $this->_update($data,$where,"bot_user");
                    
                } else {
                    
                    $this->_test($phoneNumber);
                    
                    // Register New User
                    $data = array(
                        'sender_id' => $senderId,
                        'from_first_name' => $senderName,
                        'phone' => $phoneNumber,
                        'date_add' => time(),
                        'date_update' => time(),
                    );			
                    $this->_insert($data, "bot_user");
                    
                }
                
                $this->processMessages($message);
            break;
        }
    }

    function processRegister($message,$checkUser)
    {
        
        $senderId = $message['sender']['id'];
        $step = $checkUser['step'];
        switch ($step) {
            case 2: // No First Name
				$requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
				$requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterFirstName');
                $this->saveCommand($senderId, "/editfirstname");
            break;
            case 3: // No Last Name
				$requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
				$requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterLastName');
                $this->saveCommand($senderId, "/editlastname");
            break;
            case 4: // No City
				$requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
				$requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
				$requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterCity');
                $this->saveCommand($senderId, "/editcity");
            break;
            case 5: // No Address
				$requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
				$requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
				$requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n\n";
				$requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterAddress');
                $this->saveCommand($senderId, "/editaddress");
            break;
            case 6: // No Email
				$requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
				$requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
				$requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
				$requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterEmail')."\n\n";
                $requestText .= lang('Bot.MessageEnterEmailSkip');
                $keyboard = [
                    [
                        "ActionBody" => "/startskipemail",
                        "Text" => "<b>".lang('Bot.ButtonStart')."</b>",
                        "Columns" => 6,
                        "Rows" => 1,
                        "BgColor" => $this->bgColor,
                    ],
                ];
                $this->saveCommand($senderId, "/editemail");
            break;
            case 7:
				$requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
				$requestText .= "**".lang('Bot.MessageYourData')."**\n";
				$requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
				$requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
				$requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
				$requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
				$requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n";
                $requestText .= "- ".lang('Bot.MessageEmail').": ".$checkUser['email'];

                $keyboard = [
                    [
                        "ActionBody" => "/start",
                        "Text" => "<b>".lang('Bot.ButtonStart')."</b>",
                        "Columns" => 6,
                        "Rows" => 1,
                        "BgColor" => $this->bgColor,
                    ],
                ];              
                $this->saveCommand($senderId, "/start");                
                $this->saveSenderData($senderId, "active", 1);
            break;
            
            
        }
        if ( isset($keyboard) ){
            $this->sendRequest($this->sendMessage($senderId, $requestText, "text", $keyboard));
        }else{
            $this->sendRequest($this->sendMessage($senderId, $requestText));
        }
        
     
    }
    
    function messageText($message, $checkUser, $commandLast=NULL)
    {

        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];

        switch($text){       
            case "/start": case "/startskipemail":
                $this->commandStart($message, $checkUser);
            break;
            case "/help": 
                $this->commandHelp($message, $checkUser);
            break;
            case "/appeal": 
                $this->commandAppeal($message, $checkUser);
            break;
            case "/appealcontent":
                $command = "Content";
                $this->commandAppealEdit($message, $checkUser, $command);
                break;
            case "/appealaddress":
                $command = "Address";
                $this->commandAppealEdit($message, $checkUser, $command);
                break;
            case "/appealconfirm":
                $this->commandAppealConfirm($message, $checkUser);
                break;
            case "/list": 
                $this->commandList($message, $checkUser);
            break;
            case "/my": 
                $this->commandMy($message, $checkUser);
            break;
            case "/myedit": 
                $this->commandMyEdit($message, $checkUser);
            break;
            case "/editfirstname":
                $command = "FirstName";
                $this->commandMyEdit($message, $checkUser, $command);
                break;
            case "/editlastname":
                $command = "LastName";
                $this->commandMyEdit($message, $checkUser, $command);
                $this->saveCommand($senderId, "/editlastname");
                break;
            case "/editcity":
                $command = "City";
                $this->commandMyEdit($message, $checkUser, $command);
                break;
            case "/editaddress":
                $command = "Address";
                $this->commandMyEdit($message, $checkUser, $command);
                break;
            case "/editemail":
                $command = "Email";
                $this->commandMyEdit($message, $checkUser, $command);
                break;
            default:
                
                $commandLast = $this->commandLast($senderId);
                
                switch($commandLast){
                   case "/editfirstname":
                        $this->saveSenderData($senderId, "first_name", $text);
                        $this->saveCommand($senderId, "/myedit");
                        $this->commandMyEdit($message, $checkUser);
                        break;
                    case "/editlastname":
                        $this->saveSenderData($senderId, "last_name", $text);
                        $this->saveCommand($senderId, "/myedit");
                        $this->commandMyEdit($message, $checkUser);
                        break;
                    case "/editcity":
                        $this->saveSenderData($senderId, "city", $text);
                        $this->saveCommand($senderId, "/myedit");
                        $this->commandMyEdit($message, $checkUser);
                        break;
                    case "/editaddress":
                        $this->saveSenderData($senderId, "address", $text);
                        $this->saveCommand($senderId, "/myedit");
                        $this->commandMyEdit($message, $checkUser);
                        break;
                    case "/editemail":
                        $this->saveSenderData($senderId, "email", $text);
                        $this->saveCommand($senderId, "/myedit");
                        $this->commandMyEdit($message, $checkUser);
                        break;
                    case "/appeal": case "/appealcontent": case "/appealaddress": case "/appealemail": case "/appealview":
                        $this->commandAppeal($message, $checkUser);
                        break;
                        
                    default:
                    
                        if ( strpos($text, '/appeal_') === 0 ) {
                            $textArray = explode("_",$text);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealView($message, $checkUser, $textArray[1], $commandLast);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));
                            }
                            
                        } elseif ( strpos($text, '/status_') === 0 ) {
                            $textArray = explode("_",$text);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealStatus($message, $checkUser, $textArray[1], $commandLast);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));
                            }
                            
                        } elseif ( strpos($text, '/chat_') === 0 ) {
                            
                            $textArray = explode("_",$text);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealChat($message, $checkUser, $textArray[1], $commandLast);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));

                            }
                            
                        } elseif ( strpos($commandLast, '/chat_') === 0 ) {
                            
                            $textArray = explode("_",$commandLast);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealChat($message, $checkUser, $textArray[1], $commandLast);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));
                            }
                            
                        } elseif ( strpos($text, '/rating_') === 0 ) {
                            
                            $textArray = explode("_",$text);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealRating($message, $checkUser, $textArray[1], $text);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));
                            }
                            
                        } elseif ( strpos($commandLast, '/rating_') === 0 ) {
                            
                            $textArray = explode("_",$commandLast);
                            if(isset($textArray[1]) AND count($textArray)==2){
                                $this->commandAppealRating($message, $checkUser, $textArray[1], $commandLast);
                            } else {
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $commandLast, $type, $keyboard));
                            }
                            
                        } else {
                            if ( strpos($text, 'http') === 0 ){
                                
                            }else{
                                $keyboard = [$this->button("/start", "ButtonBackToStart", 6, 1)];
                                $text = lang('Bot.MessageBadCommand');
                                $this->sendRequest($this->sendMessage($senderId, $text, $type, $keyboard));  
                            }
                        }
                    break;
                }
            break;
        }
    }
    
    function messagePicture($message,$checkUser)
    {
      
        $senderId = $message['sender']['id'];
        $checkUser = $this->checkUser($message);
        $appeal = $this->appealLast($checkUser['id']);
        $commandLast = $this->commandLast($senderId);
        $type = "text";
        
        if (isset($appeal['id']) && ( $commandLast=="/appeal" || $commandLast=="/appealcontent" || $commandLast=="/appealaddress" || $commandLast=="/appealview" ) ) {

			$appealGallery = $this->appealGallery($appeal['id']);
			$appealGalleryCount = count($appealGallery);
            
            $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealTitle')."* \n\n";
            if (empty($appeal['content'])) {
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealContent')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
            }
            if (empty($appeal['address'])) {
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealAddress')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealAddress')." ".$appeal['address']."\n\n";
            }
            if($appealGalleryCount>0){
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }
            if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
            }
            $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
            $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
            
            $keyboard = [
                $this->button("/appealconfirm", "ButtonConfirm", 3, 1),
                $this->button("/appealcontent", "ButtonAppealEditContent", 3, 1),
                $this->button("/appealaddress", "ButtonAppealEditAddress", 3, 1),
                $this->button("/start", "ButtonBackToStart", 3, 1),
            ];
            
        } else {
            
            $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageError')."* \n\n";
            $requestText .= lang('Bot.MessageErrorPicture');
            $keyboard = [ $this->button("/start", "ButtonBackToStart", 6, 1) ];
            
        }
        
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
    }

    function messageLocation($message,$checkUser)
    {
      
        $senderId = $message['sender']['id'];
        $checkUser = $this->checkUser($message);
        $appeal = $this->appealLast($checkUser['id']);
        $commandLast = $this->commandLast($senderId);
        $type = "text";
        
        if (isset($appeal['id']) && ( $commandLast=="/appeal" || $commandLast=="/appealcontent" || $commandLast=="/appealaddress" || $commandLast=="/appealview" ) ) {

			$appealGallery = $this->appealGallery($appeal['id']);
			$appealGalleryCount = count($appealGallery);
            
            $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealTitle')."* \n\n";
            if (empty($appeal['content'])) {
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealContent')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
            }
            if (empty($appeal['address'])) {
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealAddress')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealAddress')." ".$appeal['address']."\n\n";
            }
            if($appealGalleryCount>0){
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }
            if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
            }
            $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
            $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
            
            $keyboard = [
                $this->button("/appealconfirm", "ButtonConfirm", 3, 1),
                $this->button("/appealcontent", "ButtonAppealEditContent", 3, 1),
                $this->button("/appealaddress", "ButtonAppealEditAddress", 3, 1),
                $this->button("/start", "ButtonBackToStart", 3, 1),
            ];

        } else {
            
            $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageError')."* \n\n";
            $requestText .= lang('Bot.MessageErrorPicture');
            $keyboard = [ $this->button("/start", "ButtonBackToStart", 6, 1) ];
            
        }
        
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
    }   

    function messageVideo($message,$checkUser)
    {
        $senderId = $message['sender']['id'];
        $sender_name = $message['sender']['name'];
        $type = "text";
        $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageError')."* \n\n";
        $requestText .= lang('Bot.MessageErrorPicture');
        $keyboard = [ $this->button("/start", "ButtonBackToStart", 6, 1) ];        
        $sendMessage = $this->sendMessage($senderId, $requestText, $type);
        $this->sendRequest($sendMessage);
    }

    function messageContact($message,$checkUser)
    {
        
        $senderId = $message['sender']['id'];
        $requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
        $requestText .= "**".lang('Bot.MessageYourData')."**\n";
        $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
        $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
        $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
        $requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
        $requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n";
        $keyboard = [ $this->button("/start", "ButtonStart", 6, 1) ];
        $this->sendRequest($this->sendMessage($senderId, $requestText, "text", $keyboard));
        $this->saveCommand($senderId, "/start");
        $this->saveSenderData($senderId, "active", 1);

    }

    function checkUser($message)
    {
        
        $active = $step = 0;
        $senderId = $message['sender']['id'];

		$builder = $this->db->table($this->prefix."bot_user");
		$builder->where('sender_id',$senderId);
		$query = $builder->get();
		if($query->getResult()){
            
            foreach ($query->getResult() as $item){
                
                if( empty($item->phone) ){
                    $step = 1;
                } elseif( empty($item->first_name) ){
                    $step = 2;
                } elseif( empty($item->last_name) ){
                    $step = 3;
                } elseif( empty($item->city) ){
                    $step = 4;
                } elseif( empty($item->address) ){
                    $step = 5;
                } elseif( empty($item->email) ){
                    $step = 6;
                    if( $item->emailskip==1 ){ 
                        $step = 0;
                        $active = 1;
                    }
                } elseif( $item->active == 0 ){
                    $step = 7;
                    if( $item->emailskip==1 ){ 
                        $step = 0;
                        $active = 1;
                    }
                } else {
                    $active = 1;
                }
                
                if($step>0 AND $step<6){
                    $data['active'] = 0;
                    $where = "sender_id = '".$senderId."'";
                    $this->_update($data, $where, "bot_user");
                }
                if( $active==1 AND $item->active==0 ){
                    $data['active'] = 1;
                    $where = "sender_id = '".$senderId."'";
                    $this->_update($data, $where, "bot_user");
                }

                
				$return = array(
					'id' => $item->id,
					'phone' => $item->phone,
					'email' => $item->email,
					'first_name' => $item->first_name,
					'last_name' => $item->last_name,
					'city' => $item->city,
					'address' => $item->address,
					'active' => $active,
					'step' => $step,
				); 
                
            }
            
        } else {
            
			$return = array(
				'id' => 0,
				'phone' => NULL,
				'email' => NULL,
				'first_name' => NULL,
				'last_name' => NULL,
				'city' => NULL,
				'address' => NULL,
				'active' => 0,
				'step' => 1,
			);
            
        }
        
        return $return;
        
    }

    function commandStart($message, $checkUser)
    {
       
        $senderId = $message['sender']['id'];
        
        $requestText = "\xF0\x9F\x93\x9D **".lang('Bot.MessageStartTitle')."**\n\n";
//        $requestText .= "*".lang('Bot.MessageStartTesting_1')."*\n\n";
//        $requestText .= lang('Bot.MessageStartTesting_3')."\n\n";
        $requestText .= lang('Bot.MessageStartDescription')."\n\n";
        $requestText .= "\xE2\x9D\x97 ".lang('Bot.MessageStartWarning');
        
        $type = "text";
        $keyboard = [
            $this->button("/appeal", "ButtonAddAppeal", 3, 1),
            $this->button("/list", "ButtonList", 3, 1),
            $this->button("/my", "ButtonMy", 3, 1),
            $this->button("/help", "ButtonHelp", 3, 1),
        ];
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
       
    }

    function commandHelp($message, $checkUser)
    { 
        $senderId = $message['sender']['id'];
		$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageHelpTitle')."*\n\n";
		$requestText .= lang('Bot.MessageStartDescription')."\n\n";
		$requestText .= lang('Bot.MessageHelpDescription')."\n";
		$requestText .= "*/start* - ".lang('Bot.MessageCommandStart')."\n";
		$requestText .= "*/appeal* - ".lang('Bot.MessageCommandAppeal')."\n";
		$requestText .= "*/list* - ".lang('Bot.MessageCommandList')."\n";
		$requestText .= "*/my* - ".lang('Bot.MessageCommandMy')."\n";
		$requestText .= "*/myedit* - ".lang('Bot.MessageCommandMyEdit')."\n";
		$requestText .= "*/help* - ".lang('Bot.MessageCommandHelp')."\n";
        $type = "text";
        $keyboard = [ $this->button("/start", "ButtonBackToStart", 6, 1) ];
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
    }

    function commandList($message, $checkUser)
    {
        $appealList = $this->appealList($checkUser);
        $senderId = $message['sender']['id'];       
		$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageListTitle')."*\n\n";
        
        if(count($appealList)==0){
            $requestText .= lang('Bot.MessageAppealNoFound');
        } else {
            $requestText .= lang('Bot.MessageListDescription');
        }

        $type = "text";

        foreach ($appealList as $value) {
            $keyboard[] = [
                "ActionBody" => "/appeal_".$value['id'],
                "Text" => "<b>".lang('Bot.MessageAppeal')." #".$value['id']."</b><br>від ".date("Y-m-d H:i",$value['date_add']),
                "Columns" => 3,
                "Rows" => 1,
                "BgColor" => $this->bgColor,                
            ];
        }
        
        if( count($keyboard) % 2 === 0) {
            $columns = 6;
        } else {
            $columns = 3;
        }        
        
        $keyboard[] = $this->button("/start", "ButtonBackToStart", $columns, 1);
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
    }

    function commandAppeal($message, $checkUser)
    {
        
		$timeLeave = time()+3600;
		$appeal = $this->appealLast($checkUser['id'],1);        
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
        
        if ( $text!="/appeal" AND $text!=lang('Bot.ButtonAddAppeal')){
            
            $filed = "";
            
            if ( empty($appeal['content']) ){
                $appealStep = 4; // Description Added
            } elseif( empty($appeal['address']) ){
                $appealStep = 5; // Address Added
            } else{
                $appealStep = 1; // All Added
            }   
            
            if ( $text=="/appealview"){ 
                $appealStep = 1;
            }
            
			$appealGallery = $this->appealGallery($appeal['id']);
			$appealGalleryCount = count($appealGallery);
            
			$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealTitle')."* \n\n";
  
            switch($appealStep){
                case 1:
                    if ($appeal['status']==0) {
                        $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
                        $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealAddress')." ".$appeal['address']."\n\n";
                        if($appealGalleryCount>0){
                            $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
                        }else{
                            $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }
                        if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                            $requestText .= "\xE2\x9C\x96 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }else{
                            $requestText .= "\xE2\x9C\x94 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
                        }
                        $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
                        $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
                        $keyboard = [
                            $this->button("/appealcontent", "ButtonAppealEditContent", 3, 1),
                            $this->button("/appealaddress", "ButtonAppealEditAddress", 3, 1),
                            $this->button("/appealconfirm", "ButtonConfirm", 3, 1),
                            $this->button("/start", "ButtonBackToStart", 3, 1),
                        ];
                    } else {
                        $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealConfirmTitle')."* \n\n";
                        $requestText .= lang('Bot.MessageAppealConfirmDesc');
                        $keyboard = [
                            $this->button("/list", "ButtonList", 6, 1),
                            $this->button("/start", "ButtonBackToStart", 6, 1),
                        ];
                    }
                    break;
                    
                case 2:
                    $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageMyEditEmailNoCorrect')."\n";
                    $this->saveCommand($senderId, "/appealemail");
                    $keyboard = [
                        $this->button("/start", "ButtonBackToStart", 6, 1),
                    ];
                    break;
                    
                case 3:
                    $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep2')."\n";
                    $this->saveCommand($senderId, "/appealcontent");
                    $keyboard = [
                        $this->button("/start", "ButtonBackToStart", 6, 1),
                    ];
                    break;
                    
                case 4:
                    $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep2')."\n";
                    $this->saveCommand($senderId, "/appealcontent");
                    $keyboard = [
                        $this->button("/start", "ButtonBackToStart", 6, 1),
                    ];
                    break;

                case 5:
                    $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep3')."\n";
                    $this->saveCommand($senderId, "/appealaddress");
                    $keyboard = [
                        $this->button("/start", "ButtonBackToStart", 6, 1),
                    ];
                    break;
            }
        } else {
            
			// Command
			$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealTitle')."* \n\n";
			$requestText .= lang('Bot.MessageAppealDescStart')."\n\n";
			
			if( empty($checkUser['email']) ){
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep2')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep3')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep4')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep5')."\n\n";				
			}else{
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep2')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep3')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep4')."\n";
				$requestText .= "\xE2\x96\xAA ".lang('Bot.MessageAppealDescStep5')."\n\n";
			}           
            $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep2')."\n";
            $this->saveCommand($senderId, "/appealcontent");
            
			if(count($appeal)>0){
				if ($appeal['status']==0) {
                    $data = [
                        'content' => "",
                        'email' => "",
                        'address' => "",
                        'photo' => 0,
                        'location_lat' => "",
                        'location_lng' => "",
                    ];
                    $where = "id = ".$appeal['id'];
                    $this->_update($data,$where,"appeal");
                    
                    $where = "appeal_id = ".$appeal['id'];
                    $this->_delete($where,"appeal_gallery");
                } else {
                    $data = [
                        'user_id' => $checkUser['id'],
                        'date_add' => time(),
                        'date_update' => time(),
                        'date_leave' => $timeLeave
                    ];
                    $this->_insert($data,"appeal");                    
                }
				
			}else{
				
				$data = [
					'user_id' => $checkUser['id'],
					'date_add' => time(),
					'date_update' => time(),
					'date_leave' => $timeLeave
				];
				$this->_insert($data,"appeal");
				
			}
            
            $keyboard = [ $this->button("/start", "ButtonBackToStart", 6, 1) ];
            
        }
        
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
    }

    function commandAppealEdit($message, $checkUser, $command)
    {
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
        
        $this->_test("Контент");
        
		$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealEdit'.$command.'Title')."* \n\n";
		$requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealEdit'.$command);

        $keyboard = [ $this->button("/appealview", "ButtonBackToMyEdit", 6, 1) ];
		$this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
    }

	function commandAppealConfirm($message, $checkUser)
    {
	
    	$appeal = $this->appealLast($checkUser['id']);        
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
		
		$data = [
			'status' => 1
		];
		$where = "id = ".$appeal['id'];
		$this->_update($data,$where,"appeal");
		
		$dataStatus = [
			'status_id' => 1,
			'appeal_id' => $appeal['id'],
			'date_add' => time(),
		];
		$appealStatus = $this->_insert($dataStatus,"appeal_status");		
	
		$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealConfirmTitle')."* \n\n";
		$requestText .= lang('Bot.MessageAppealConfirmDesc');
        $keyboard = [
            $this->button("/list", "ButtonList", 6, 1),
            $this->button("/start", "ButtonBackToStart", 6, 1),
        ];
		
		$this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
        // Send Email Manager
        $emailSend = "";
		$builder = $this->db->table($this->prefix."user");
		$builder->where('group_id',1);
        $builder->where('email_send',1);
		$query = $builder->get();		
		foreach ($query->getResult() as $item){
			$emailSend .= $item->email.", ";
		}
        $emailSend = trim($emailSend, " ");
        $emailSend = trim($emailSend, ",");
        if (!empty($emailSend)) {
            $data_mail['to_email'] = $emailSend;
            $data_mail['subject'] = lang('Email.EmailNewAppealSubject');
            $data_mail['message'] = lang('Email.EmailNewAppealMessage').$appeal['id'];
            Email::sendEmail($data_mail);
        }
        
	}

    function commandAppealView($message, $checkUser, $appealId, $commandLast)
    {
      
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
      
		$appeal = [];
        $appealChat = [];
        $appealWork = [];
        $appealRating = [];
        $implementer = [];
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$checkUser['id']);
		$builder->where('id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appeal[$key]=$value;
				}
			}
		}
		$builder = $this->db->table($this->prefix."appeal_chat");
		$builder->where('appeal_id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealChat['$item->id'][$key]=$value;
				}
			}
		}
        
        $builder = $this->db->table($this->prefix."appeal_user as a");
        $builder->select('a.user_id, a.comment, a.comment_user, u.first_name, u.last_name, u.title');
        $builder->join($this->prefix.'user as u', 'u.id = a.user_id');        
        $builder->where('a.appeal_id',$appealId);
        $builder->where('a.group_id',3);
        $query = $builder->get();		
        if($query->getResult()){
            foreach ($query->getResult() as $item){
                $implementer['user_id'] = $item->user_id;
                $implementer['first_name'] = $item->first_name;
                $implementer['last_name'] = $item->last_name;
                $implementer['title'] = $item->title;
                $implementer['comment'] = $item->comment;
                $implementer['comment_user'] = $item->comment_user;
            }
        } 
        
        if (count($appeal)>0) {
            
			// Header
			$appealLocationStr = "";
			if( !empty($appeal['location_lat']) && !empty($appeal['location_lng']) ){
				$appealLocationStr = $appeal['location_lat'].", ".$appeal['location_lng'];
			}
			
			$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppeal')." №".$appeal['id']."* \n";
			$requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add'])."\n\n";
			
			$requestText .= "*".lang('Bot.MessageAppealContent')."*. ".$appeal['content']."\n\n";
			$requestText .= "*".lang('Bot.MessageAppealAddress')."*. ".$appeal['address']."\n\n";
			if(!empty($appealLocationStr)){
				$requestText .= "*".lang('Bot.MessageAppealLocation')."*. ".$appealLocationStr."\n\n";
			}

			// Status
			$directoryStatus = $this->directoryStatus();
            $lastStatus = $this->lastStatus($appealId);
			$requestText .= "*".lang('Bot.MessageAppealStatusActual')."*. ".$directoryStatus[$lastStatus];
            
            // Implementer
            if(count($implementer)>0){
                $implementerStr = "";
                $implementerStr .= $implementer['first_name']." ".$implementer['last_name'];
                $implementerStr .= ", ".$implementer['title'];
                if(!empty($implementer['comment_user'])) {
                    $implementerStr .= ", ".$implementer['comment_user'];
                }
                $requestText .= "\n\n *".lang('Bot.Implementer')."*. ".$implementerStr;
            }            
            
            // Rating
            $appealRating = 0;
            $builder = $this->db->table($this->prefix."appeal_rating");
            $builder->where('appeal_id',$appealId);
            $query = $builder->get();		
            if($query->getResult()){
                foreach ($query->getResult() as $item){
                    $appealRating=$item->rating;
                }
            }
            if($appealRating>0){
                $ratingStar = "";
                for($n=1;$n<=$appealRating;$n++){
                    $ratingStar .= "\xE2\xAD\x90";
                }
                $requestText .= "\n\n *".lang('Bot.MessageAppealRating')."*. ".$ratingStar." (".$appealRating.")";
            }
            
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type));
                  

            // Gallery
			$appealGallery = $this->appealGallery($appealId);			
			if(count($appealGallery)>0){
				foreach($appealGallery as $value){
                    $buttons[] = [
                        "Columns" => 6,
                        "Rows" => 7,
                        "ActionType" => "none",
                        "ActionBody" => base_url()."/assets/upload/appeal/".$appeal['id']."/".$value['image'],
                        "Image" => base_url()."/assets/upload/appeal/".$appeal['id']."/".$value['image'],
                    ];
                }
                $richMedia = [
                    "Type" => "rich_media",
                    "ButtonsGroupColumns" => 6,
                    "ButtonsGroupRows" => 7,
                    "BgColor" => "#FFFFFF",
                    "Buttons" => $buttons,
                ];
                
                $this->sendRequest($this->sendRichMedia($senderId, $richMedia));
                
            }
            
            // Link to Site
            $buttons = [];
            $buttons[] = [
                "Columns" => 6,
                "Rows" => 1,
                "ActionType" => "open-url",
                "ActionBody" => base_url()."/appeal/".$appeal['id'],
                "Text" => "<font color=#".$this->bgColor.">".lang('Bot.ReadMoreSite')."</font>",
                "TextVAlign" => "middle",
                "TextHAlign" => "middle"
            ];
            $richMedia = [
                "Type" => "rich_media",
                "ButtonsGroupColumns" => 6,
                "ButtonsGroupRows" => 1,
                "BgColor" => "#FFFFFF",
                "Buttons" => $buttons,
            ];
            $this->sendRequest($this->sendRichMedia($senderId, $richMedia)); 
                  
            // Buttons
            $requestText = lang('Bot.MessageAppealControl');
            $keyboard = [];

            if ( $appeal['status']==5 && $appealRating==0 ) {
                $keyboard[] = $this->button("/rating_".$appealId, "ButtonAppealRating", 3, 1);
            }
            
            $keyboard[] = $this->button("/status_".$appealId, "ButtonAppealStatus", 3, 1);
            
            if(count($appealChat)>0){
                $keyboard[] = $this->button("/chat_".$appealId, "ButtonAppealChat", 3, 1);
            }

            $keyboard[] = $this->button("/list", "ButtonList", 3, 1);
            $columns = ( count($keyboard) % 2 === 0)?$columns = 6:$columns = 3;
            $keyboard[] = $this->button("/start", "ButtonBackToStart", $columns, 1);
            
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        } else {
            
			$requestText = "\xE2\x9D\x97 *".lang('Bot.MessageAppealNoFoundTitle')."* \n\n";
			$requestText .= lang('Bot.MessageAppealNoFoundDesc');
            
            $keyboard = [ $this->button("/list", "ButtonList", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        }
    }

    function commandAppealStatus($message, $checkUser, $appealId, $commandLast)
    {
      
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
       
		$appeal = [];
        $appealChat = [];
        $appealWork = [];
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$checkUser['id']);
		$builder->where('id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appeal[$key]=$value;
				}
			}
		}
		$builder = $this->db->table($this->prefix."appeal_status");
		$builder->where('appeal_id',$appealId);
        $builder->where('status_id < 6');
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealStatus[$item->id][$key]=$value;
				}
			}
		}
		$builder = $this->db->table($this->prefix."status");
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				$appealStatusName[$item->id]=$item->title;
			}
		}
        
        if (count($appeal)>0) {
            
            $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealStatusTitle')."* \n";
            $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
            $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add']);

            foreach($appealStatus as $value){
                //$requestText .= "\n\n".lang('Bot.StatusId').": *".$value['id']."*\n";
                $requestText .= "\n\n".lang('Bot.MessageAppealChatDate')." ".date("Y.m.d H:i:s",$value['date_add'])."\n";
                $requestText .= lang('Bot.MessageAppealStatus').": *".$appealStatusName[$value['status_id']]."*";
                if (!empty($value['comment'])) {
                    $requestText .= "\n".lang('Bot.MessageAppealComment').": ".$value['comment'];
                }
            } 

            $keyboard = [ $this->button("/appeal_".$appealId, "MessageViewAppealBack", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        } else {
            
			$requestText = "\xE2\x9D\x97 *".lang('Bot.MessageAppealNoFoundTitle')."* \n\n";
			$requestText .= lang('Bot.MessageAppealNoFoundDesc');
            
            $keyboard = [ $this->button("/list", "ButtonList", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        }
    }

    function commandAppealChat($message, $checkUser, $appealId, $commandLast)
    {
      
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
       
		$appeal = [];
        $appealChat = [];
        $appealWork = [];
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$checkUser['id']);
		$builder->where('id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appeal[$key]=$value;
				}
			}
		}
		$builder = $this->db->table($this->prefix."appeal_chat");
		$builder->where('appeal_id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealChat[$item->id][$key]=$value;
				}
			}
		}
        
        if (count($appeal)>0) {
            
            if (count($appealChat)>0) {
                
                $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealChatTitle')."* \n";
                $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
                $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add']);
                
                foreach($appealChat as $value){
                    $requestText .= "\n\n".lang('Bot.MessageId').": *".$value['id']."*\n";
                    $requestText .= lang('Bot.MessageAppealChatDate')." ".date("Y.m.d H:i:s",$value['date_add'])."\n";
                    $requestText .= lang('Bot.MessageAppealChatSender'.$value['who'])."\n";
                    $requestText .= lang('Bot.Message').": ".$value['message'];
                }
                
                $requestText .= "\n\n\xE2\x9C\x8F ".lang('Bot.MessageAppealChatSendMessage');
                $this->saveCommandAll($senderId, "/chatmessage_".$appeal['id']);
                
            } else {
                
                $requestText = "\xE2\x9D\x97 *".lang('Bot.MessageAppealChatNoFoundTitle')."* \n\n";
                $requestText .= lang('Bot.MessageAppealChatNoFoundDesc');
                
            }

            $keyboard = [ $this->button("/appeal_".$appealId, "MessageViewAppealBack", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        } else {
            
			$requestText = "\xE2\x9D\x97 *".lang('Bot.MessageAppealNoFoundTitle')."* \n\n";
			$requestText .= lang('Bot.MessageAppealNoFoundDesc');
            
            $keyboard = [ $this->button("/list", "ButtonList", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        } 
    }

    function commandAppealRating($message, $checkUser, $appealId, $commandLast)
    {
        
        $senderId = $message['sender']['id'];
        $type = $message['message']['type'];
        $text = $message['message']['text'];
       
		$appeal = [];
        $appealRating = [];
        $keyboard = [];
        
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$checkUser['id']);
		$builder->where('id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appeal[$key]=$value;
				}
			}
		}
		$builder = $this->db->table($this->prefix."appeal_rating");
		$builder->where('appeal_id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealRating[$key]=$value;
				}
			}
		}
        
        if (count($appeal)>0) {
            
            if (count($appealRating)==0) {
                
                $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealRatingTitle')."* \n";
                $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
                $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add'])."\n\n";
                $requestText .= lang('Bot.MessageAppealRatingDesc');
                
                for($n=1;$n<6;$n++){
                    $column = ($n==5)?2:1;
                    $keyboard[] =
                    [
                        "ActionBody" => $n,
                        "Text" => $n,
                        "Columns" => $column,
                        "Rows" => 1,
                        "BgColor" => $this->bgColor,
                    ];
                }
                $this->saveCommandAll($senderId, "/appealrating_".$appeal['id']);
                
            } else {
                
                $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageAppealRatingTitle')."* \n\n";
                $requestText .= lang('Bot.MessageAppealRatingAdded');
                
            }
            $keyboard = [ $this->button("/appeal_".$appealId, "MessageViewAppealBack", 6, 1) ];            
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
            
        } else {
        
			$requestText = "\xE2\x9D\x97 *".lang('Bot.MessageAppealNoFoundTitle')."* \n\n";
			$requestText .= lang('Bot.MessageAppealNoFoundDesc');
            $keyboard = [ $this->button("/list", "ButtonList", 6, 1) ];
            $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
        }
    }

    function commandMy($message, $checkUser)
    {
        $senderId = $message['sender']['id'];
		$myData = $this->myData($senderId);
		$requestText = "\xE2\xAD\x90 *".lang('Bot.MessageMyTitle')."*\n\n";
		$requestText .= "- ".lang('Bot.MessageFirstName').": ".$myData['first_name']."\n";
		$requestText .= "- ".lang('Bot.MessageLastName').": ".$myData['last_name']."\n";
		$requestText .= "- ".lang('Bot.MessagePhone').": ".$myData['phone']."\n";
		$requestText .= "- ".lang('Bot.MessageEmail').": ";
		$requestText .= (empty($myData['email']))? lang('Bot.MessageAppealNoValue') : $myData['email'];
		$requestText .= "\n";
		$requestText .= "- ".lang('Bot.MessageCity').": ".$myData['city']."\n";
		$requestText .= "- ".lang('Bot.MessageAddress').": ".$myData['address']."\n";
        $type = "text";
        $keyboard = [
            $this->button("/myedit", "ButtonMyEdit", 6, 1),
            $this->button("/start", "ButtonBackToStart", 6, 1),
        ];
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
    }

    function commandMyEdit($message, $checkUser, $command="start")
    {
        $senderId = $message['sender']['id'];
        $myData = $this->myData($senderId);        
        $requestText = "\xE2\xAD\x90 *".lang('Bot.MessageMyTitle')."*\n\n";
        
        $this->_test($command);
        
        if($command=="start"){
            $requestText .= "- ".lang('Bot.MessageFirstName').": ".$myData['first_name']."\n";
            $requestText .= "- ".lang('Bot.MessageLastName').": ".$myData['last_name']."\n";
            $requestText .= "- ".lang('Bot.MessagePhone').": ".$myData['phone']."\n";
            $requestText .= "- ".lang('Bot.MessageEmail').": ";
            $requestText .= (empty($myData['email']))? "*".lang('Bot.MessageAppealNoValue')."*" : $myData['email'];
            $requestText .= "\n";
            $requestText .= "- ".lang('Bot.MessageCity').": ".$myData['city']."\n";
            $requestText .= "- ".lang('Bot.MessageAddress').": ".$myData['address']."\n";
        } else {
            $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageMyEdit'.$command);
        }
        
        $type = "text";
        
        if($command=="start"){        
            $keyboard = [
                $this->button("/editfirstname", "ButtonMyEditFirstName", 3, 1),
                $this->button("/editlastname", "ButtonMyEditLastName", 3, 1),
                $this->button("/editcity", "ButtonMyEditCity", 3, 1),
                $this->button("/editaddress", "ButtonMyEditAddress", 3, 1),
                $this->button("/editemail", "ButtonMyEditEmail", 3, 1),
                $this->button("/start", "ButtonBackToStart", 3, 1),
            ];
        } else {
            $keyboard = [
                $this->button("/myedit", "ButtonBackToMyEdit", 6, 1),
            ];
        }
        
        $this->sendRequest($this->sendMessage($senderId, $requestText, $type, $keyboard));
        
    } 

	function commandLast($senderId)
    {
		
		$return = NULL;
		$builder = $this->db->table($this->prefix."viber_command");
		$builder->where('sender_id',$senderId);
		$builder->orderBy('id', 'DESC');
		$builder->limit(1);
		$query = $builder->get();		
		foreach ($query->getResult() as $item){
			$return = $item->command;
		}
		return $return;
		
	}

    function appealLast($userId, $status=0)
    {
		
		$appeal = [];
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$userId);
        if ($status==0) {
    		$builder->where('status', 0);
        }
		$builder->orderBy('date_add', 'DESC');
		$builder->limit(1);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appeal[$key] = $value;
				}
			}	
		}
		return $appeal;
		
	}

    function button($body, $text, $column=6, $rows=1)
    {
        $return = [
            "ActionBody" => $body,
            "Text" => "<b>".lang('Bot.'.$text)."</b>",
            "Columns" => $column,
            "Rows" => $rows,
            "BgColor" => $this->bgColor,
        ];
        return $return;
    }

    function sendMessage($sender_id, $text, $type="text", $keyboard=[])
    {
        
        $data['min_api_version'] = 3; 
        $data['auth_token'] = $this->token;
        $data['sender']['name'] = $this->name;
        $data['receiver'] = $sender_id;
        $data['type'] = $type;
        if (count($keyboard)>0) {
            $data['keyboard'] = [ "Type" => "keyboard", "Buttons" => $keyboard ];              
        }
        if($text !== Null) {
            $data['text'] = $text;
        }
        return $data;
        
    }
    
    function sendRichMedia($sender_id, $richMedia)
    {
        $data['min_api_version'] = 7; 
        $data['auth_token'] = $this->token;
        $data['sender']['name'] = $this->name;
        $data['receiver'] = $sender_id;
        $data['type'] = "rich_media";
        $data['rich_media'] = $richMedia;              
        return $data;
    }
    
	function appealList($checkUser)
    {
		
		$appealList = [];
		$builder = $this->db->table($this->prefix."appeal");
		$builder->where('user_id',$checkUser['id']);
		$builder->where('status > ', 0);
		$builder->orderBy('date_add', 'DESC');
		$builder->limit(10);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealList[$item->id][$key] = $value;
				}
			}	
		}
		return $appealList;		
		
	}    
    
	function appealGallery($appealId)
    {
		
		$appealGallery = [];
		$builder = $this->db->table($this->prefix."appeal_gallery");
		$builder->where('appeal_id',$appealId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$appealGallery[$item->id][$key] = $value;
				}
			}	
		}
		return $appealGallery;
		
	}    
    
	function directoryStatus()
    {
		$return = [];
		$builder = $this->db->table($this->prefix."status");
		$query = $builder->get();		
		foreach ($query->getResult() as $item){
			$return[$item->id] = $item->title;
		}
		return $return;
	}	
    
    function lastStatus($appealId)
    {
        $return = 1;
        $builder = $this->db->table($this->prefix."appeal_status");
        $builder->where('appeal_id', $appealId);
        $builder->where('status_id < 6');
        $builder->orderBy('date_add', 'DESC');
        $builder->limit(1);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $return = $item->status_id;
        }
        return $return;        
    }
    
    function myData($senderId) 
    {
		
		$myData = [];
		$builder = $this->db->table($this->prefix."bot_user");
		$builder->where('sender_id',$senderId);
		$query = $builder->get();		
		if($query->getResult()){
			foreach ($query->getResult() as $item){
				foreach ($item as $key => $value){
					$myData[$key]=$value;
				}
			}
		}
		return $myData;
		
	}
    
    function sendRequest($request)
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
    
    function clearPhone($phoneNumber)
    {
        $phoneNumber = trim($phoneNumber);
        $phoneNumber = trim($phoneNumber, "+");
        $phoneNumber = "+".$phoneNumber;
        return $phoneNumber;
    }
    
    function setWebhook($webhook)
    {
        $jsonData = 
        '{
            "auth_token": "'.$this->token.'",
            "url": "'.$webhook.'",
            "event_types": ["conversation_started", "subscribed", "unsubscribed", "delivered", "message", "seen"]
        }';
        $ch = curl_init('https://chatapi.viber.com/pa/set_webhook');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo($err);
        } else {
            echo($response);
        }
    }
    
	function _insert($data,$table)
    {
		$builder = $this->db->table($this->prefix.$table);
		$builder->insert($data);
		return $this->db->insertID();
	}
	
	function _update($data,$where,$table)
    {
		$builder = $this->db->table($this->prefix.$table);
		$builder->where($where);
		$builder->update($data);
	}

	function _delete($where,$table)
    {
		$builder = $this->db->table($this->prefix.$table);
		$builder->where($where);
		$builder->delete();
	}

    function _test($test) 
    {
        $data_update['test'] = $test;
        $this->_insert($data_update,'test');
    }

    function _testSend($senderId, $requestText) 
    {
        $this->sendRequest($this->sendMessage($senderId, $requestText));
        die();
    }

}