<?php namespace App\Models;
use CodeIgniter\Model;

class Telegram extends Model
{
 
    function __construct() 
    {
        $this->db = \Config\Database::connect();
        
        $this->token = $_ENV['TelegramKey'];
        $this->startSticker = (isset($_ENV['TelegramSticker']))?$_ENV['TelegramSticker']:"";
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->method = "sendMessage";
        
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
        ];
        $this->commandList = array('/start','/list','/appeal','/help','/phone','/location','/my','/myedit','/editfirstname','/editlastname','/editcity','/editaddress','/editemail');
        $this->commandListMyEdit = array('/editfirstname','/editlastname','/editcity','/editaddress','/editemail');
        $this->commandListAppealEdit = array('/appealeditcontent','/appealeditaddress');
        
        // Buttons
        $this->keyboard = ['type' => 'keyboard'];
        $this->inline_keyboard = ['type' => 'inline_keyboard'];
        $this->remove_keyboard = ['type' => 'remove_keyboard'];
        $this->keyboardAddAppeal = [ 'text' => lang('Bot.ButtonAddAppeal'), 'callback_data'=>'/appeal' ];
        $this->keyboardAppealEditContent = [ 'text' => lang('Bot.ButtonAppealEditContent'), 'callback_data'=>'/appealeditcontent' ];
        $this->keyboardAppealEditAddress = [ 'text' => lang('Bot.ButtonAppealEditAddress'), 'callback_data'=>'/appealeditaddress' ];
        $this->keyboardList = [ 'text' => lang('Bot.ButtonList'), 'callback_data'=>'/list' ];
        $this->keyboardHelp = [ 'text' => lang('Bot.ButtonHelp'), 'callback_data'=>'/help' ];
        $this->keyboardMy = [ 'text' => lang('Bot.ButtonMy'), 'callback_data'=>'/my' ];
        $this->keyboardMyEdit = [ 'text' => lang('Bot.ButtonMyEdit'), 'callback_data'=>'/myedit' ];
        $this->keyboardMyEditFirstName = [ 'text' => lang('Bot.ButtonMyEditFirstName'), 'callback_data'=>'/editfirstname' ];
        $this->keyboardMyEditLastName = [ 'text' => lang('Bot.ButtonMyEditLastName'), 'callback_data'=>'/editlastname' ];
        $this->keyboardMyEditCity = [ 'text' => lang('Bot.ButtonMyEditCity'), 'callback_data'=>'/editcity' ];
        $this->keyboardMyEditAddress = [ 'text' => lang('Bot.ButtonMyEditAddress'), 'callback_data'=>'/editaddress' ];
        $this->keyboardMyEditEmail = [ 'text' => lang('Bot.ButtonMyEditEmail'), 'callback_data'=>'/editemail' ];
        $this->keyboardBackToMyEdit = [ 'text' => lang('Bot.ButtonBackToMyEdit'), 'callback_data'=>'/myedit' ];
        $this->keyboardEditFirstName = [ 'text' => lang('Bot.ButtonMyEditFirstName'), 'callback_data'=>'/editfirstname' ];
        $this->keyboardEditLastName = [ 'text' => lang('Bot.ButtonMyEditLastName'), 'callback_data'=>'/editlastname' ];
        $this->keyboardEditCity = [ 'text' => lang('Bot.ButtonMyEditCity'), 'callback_data'=>'/editcity' ];
        $this->keyboardEditAddress =  [ 'text' => lang('Bot.ButtonMyEditAddress'), 'callback_data'=>'/editaddress' ];
        $this->keyboardEditEmail = [ 'text' => lang('Bot.ButtonMyEditEmail'), 'callback_data'=>'/editemail' ];
        $this->keyboardBackToMy = [ 'text' => lang('Bot.ButtonBack'), 'callback_data'=>'/my' ];
        $this->keyboardStartRegister = [ 'text' => lang('Bot.ButtonStartRegister'), 'request_contact' => true ];
        $this->keyboardStart = [ 'text' => lang('Bot.ButtonStart') ];
        $this->keyboardConfirm = [ 'text' => lang('Bot.ButtonConfirm') ];
        $this->keyboardBackToStart =  [ 'text' => lang('Bot.ButtonBackToStart'), 'callback_data'=>'/start' ];
        
    }
    
    function getMessage()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        return $data;
    }	

    function callbackQuery($message)
    {
        $callback_id = $message['callback_query']['id'];
        file_get_contents("https://api.telegram.org/bot".$this->token."/answerCallbackQuery?callback_query_id=".$callback_id);
    }
    
    function callbackQueryMessage($message)
    {
        $callback_data = $message['callback_query']['data'];
        $callback_chat_id = $message['callback_query']['from']['id'];
        $message = [];
        $message['message']['from']['id'] = $callback_chat_id;
        $message['message']['chat']['id'] = $callback_chat_id;
        $message['message']['date'] = time();
        $message['message']['text'] = $callback_data;        
        return $message;
    }
    
    function saveMessage($message=array())
    {
        if(isset($message['callback_query'])) {
            $this->callbackQuery($message);
            $message = $this->callbackQueryMessage($message);
        }
        
        $messageText = "";
        $firstName = "";
        $fromId = $message['message']['from']['id'];
        $chatId = $message['message']['chat']['id'];
        $checkUser = $this->checkUser($message);
        
        if (isset($message['message']['text'])) {
            
            $messageText = $message['message']['text'];
            $messageText = trim($messageText);
            
            if( $checkUser['active']==0 AND !empty($messageText) ){
                
                $messageText = trim($messageText,"!@#$%^&*_-*,.<>?/");
                $data = [];
                if( $checkUser['step']==2 ){
                    $data = [ 'first_name' => $messageText ];
                }
                if( $checkUser['step']==3 ){
                    $data = [ 'last_name' => $messageText ];
                }
                if( $checkUser['step']==4 ){
                    $data = [ 'city' => $messageText ];
                }
                if( $checkUser['step']==5 ){
                    $data = [ 'address' => $messageText ];
                }
                if( $checkUser['step']==6 ){
                    if (filter_var($messageText, FILTER_VALIDATE_EMAIL) ){
                        $data = [ 'email' => $messageText ];
                    }
                }
                if( count($data)>0 ){
                    $where = "from_id = ".$fromId; 
                    $this->_update($data,$where,"bot_user");
                }
            }
        }
        
        if (isset($message['message']['contact'])) {
            
            $messageText = $message['message']['contact']['phone_number'];
            $messageText = trim($messageText,"+");
            $messageText = "+".$messageText;
            
            $builder = $this->db->table($this->prefix."bot_user");
            $builder->where('phone',$messageText);
            $query = $builder->get();            
            if ($query->getResult()) {
                    
                // Якщо є телефон у базі, то оновлюємо From_id
                foreach ($query->getResult() as $item) {
                    $userId = $item->id;
                }
                $data['from_id'] = $fromId;
                $data['chat_id'] = $chatId;
                $where = "id = ".$userId;
                $this->_update($data,$where,"bot_user");
                    
            } else {
                    
                // реєструємо нового користувача
                $data = array(
                    'from_id' => $fromId,
                    'chat_id' => $chatId,
                    'from_first_name' => $firstName,
                    'phone' => $messageText,
                    'date_add' => time(),
                    'date_update' => time(),
                );
                $this->_insert($data,"bot_user");
            }
        }
            
        if (isset($message['message']['location'])) {
            
            $messageText = lang('Bot.LocationNotAdded');

            $commandLast = $this->commandLast($chatId);
            $appealLast = $this->appealLast($checkUser['id']);
    
            if($commandLast=="/appeal"){
                if(count($appealLast)>0){

                    $messageText = $message['message']['location']['latitude'];
                    $messageText .= ", ".$message['message']['location']['longitude'];

                    $data = array(
                        'location_lat' => $message['message']['location']['latitude'],
                        'location_lng' => $message['message']['location']['longitude'],
                    );	
                    
                    $where = "id = ".$appealLast['id']; 
                    $this->_update($data,$where,"appeal");
                    
                }
            }
        }
        
        if (isset($message['message']['photo'])) {
            
            $commandLast = $this->commandLast($chatId);
            $appealLast = $this->appealLast($checkUser['id']);
            $messageText = lang('Bot.PhotoNotAdded');
            
            if($commandLast=="/appeal"){
                if(count($appealLast)>0){
                    
                    $photo = $message['message']['photo'][count($message['message']['photo'])-1];
                    
                    $fileData = [ 'file_id' => $photo['file_id'] ];
                    $fileGet = $this->request("getFile",$fileData);
                    
                    if(isset($fileGet['result']['file_path'])){
                        
                        $fileName = time()."_".rand(100,999);
                        $filePath = $fileGet['result']['file_path'];
                        $ext = explode(".", $filePath);
                        $fileName = $fileName.".".$ext[1];						
                        $fileTelegram = "https://api.telegram.org/file/bot".$this->token."/".$filePath;
                        
                        $filePath =  $_SERVER["DOCUMENT_ROOT"]."/assets/upload/appeal/".$appealLast['id'];
                        if (!file_exists($filePath)) {
                            mkdir($filePath, 0777, true);
                        }
                        $fileUpload =  $filePath."/".$fileName;
                        
                        if( copy($fileTelegram, $fileUpload) ){
                            
                            $data = [
                                'appeal_id' => $appealLast['id'],
                                'image' => $fileName,
                                'file_id' => $photo['file_id'],
                                'file_unique_id' => $photo['file_unique_id'],
                                'file_size' => $photo['file_size'],
                                'width' => $photo['width'],
                                'height' => $photo['height'],
                                'image' => $fileName
                            ];
                            $this->_insert($data,"appeal_gallery");

                            $data = [ 'test' => $fileTelegram ];
                            $this->_insert($data,"test");
                            
                        }
                    }
                    $messageText = lang('Bot.PhotoAdded');
                }
            }
        }
    
        $this->saveMessageBase($chatId,$messageText);
        
    }
    
    function saveMessageBase($chatId,$messageText)
    {
        
        if(!empty($messageText)){
            
            if( array_key_exists($messageText, $this->comandAll) ){
                $messageText = $this->comandAll[$messageText];
                $this->saveTelegramCommand($chatId, $messageText);
            }
            
            if( strpos($messageText, '/appeal_') === 0 ){
                $messageTextArray = explode("_",$messageText);
                if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                    if(is_numeric($messageTextArray[1])){
                        $this->saveTelegramCommand($chatId, $messageText);
                    }
                }
            }

            if( strpos($messageText, '/chat_') === 0 ){
                $messageTextArray = explode("_",$messageText);
                if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                    if(is_numeric($messageTextArray[1])){
                        $this->saveTelegramCommand($chatId, $messageText);
                    }
                }
            }

            if( strpos($messageText, '/rating_') === 0 ){
                $messageTextArray = explode("_",$messageText);
                if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                    if(is_numeric($messageTextArray[1])){
                        $this->saveTelegramCommand($chatId, $messageText);
                    }
                }
            }

            if( strpos($messageText, '/status_') === 0 ){
                $messageTextArray = explode("_",$messageText);
                if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                    if(is_numeric($messageTextArray[1])){
                        $this->saveTelegramCommand($chatId, $messageText);
                    }
                }
            }
            $this->saveTelegramMessage($chatId, $messageText);
        }
        
    }
        
    function processMessages($message=array())
    {
        
        if(isset($message['callback_query'])){
            $message = $this->callbackQueryMessage($message);
        }
        
        $return = [];
        $fromId = $message['message']['from']['id'];
        $chatId = $message['message']['chat']['id'];
        $text = (isset($message['message']['text']))?$message['message']['text']:"";
        $checkUser = $this->checkUser($message);
        
        if( $checkUser['step']>5 AND $checkUser['active']==0 AND $text==lang('Bot.ButtonStart') ) {
            $this->saveSenderData($chatId, "active", 1);
            $this->saveSenderData($chatId, "emailskip", 1);
            $checkUser['active']=1;
            $message['message']['text']= "/start";
        }
        
        if($checkUser['active']==1){
            $return = $this->userMessage($message,$checkUser);
        }else{
            $return = $this->userRegistration($message,$checkUser);
        }
        return $return;
    }

    function userMessage($message,$checkUser,$commandLast=NULL)
    {
 
        $fromId = $message['message']['from']['id'];
        $chatId = $message['message']['chat']['id'];
        $return = [];
        
        if (isset($message['message']['text'])) {
            
            if($commandLast==NULL){
                $messageText = $message['message']['text'];
            }else{
                $messageText = $commandLast;
            }
            $messageText = trim($messageText);
            
            switch ($messageText) {
                
                case "/start": case lang('Bot.ButtonStart'): case lang('Bot.ButtonBackToStart'):
                    if(!empty($this->startSticker)){
                        $this->request($method = "sendSticker", ['chat_id' => $chatId, 'sticker' => $this->startSticker ] );
                    }
                    $return = $this->commandStart($chatId);
                    break;
                    
                case "/list": case lang('Bot.ButtonList'):
                    $return = $this->commandList($chatId,$checkUser);
                    break;
                    
                case "/appeal": case lang('Bot.ButtonAddAppeal'):
                    $return = $this->commandAppeal($chatId,$checkUser,$message['message']['text']);
                    break;

                case "/appealeditcontent": case lang('Bot.ButtonAppealEditContent'):
                    $command = "Content";
                    $return = $this->commandAppealEdit($fromId,$chatId,$command);
                    break;

                case "/appealeditaddress": case lang('Bot.ButtonAppealEditAddress'):
                    $command = "Address";
                    $return = $this->commandAppealEdit($fromId,$chatId,$command);
                    break;
                    
                case "/appealconfirm": case lang('Bot.ButtonConfirm'):
                    $return = $this->commandAppealConfirm($chatId,$checkUser);
                    break;
                    
                case "/my": case lang('Bot.ButtonMy'):
                    $return = $this->commandMy($fromId,$chatId);
                    break;

                case "/myedit": case lang('Bot.ButtonMyEdit'):
                    $command = "Start";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;

                case "/editfirstname": case lang('Bot.ButtonMyEditFirstName'):
                    $command = "FirstName";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;

                case "/editlastname": case lang('Bot.ButtonMyEditLastName'):
                    $command = "LastName";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;

                case "/editcity": case lang('Bot.ButtonMyEditCity'):
                    $command = "City";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;
                
                case "/editaddress": case lang('Bot.ButtonMyEditAddress'):
                    $command = "Address";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;

                case "/editemail": case lang('Bot.ButtonMyEditEmail'):
                    $command = "Email";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                    break;

                case "/help": case lang('Bot.ButtonHelp'):
                    $return = $this->commandHelp($chatId);
                    break;
                
                default:
                    
                    $commandLast = $this->commandLast($chatId);
                    
                    if( strpos($messageText, '/appeal_') === 0 ){
                        
                        // Вивід звернення
                        $messageTextArray = explode("_",$messageText);
                        if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                            if(is_numeric($messageTextArray[1])){
                                $return = $this->commandAppealView($chatId,$checkUser,$messageTextArray[1],$commandLast);
                            }else{
                                $return = $this->commandBad($chatId);
                            }
                        }else{
                            $return = $this->commandBad($chatId);
                        }
                        
                    } elseif ( strpos($messageText, '/chat_') === 0 ) {
                        
                        // Вивід чату звернення
                        $messageTextArray = explode("_",$messageText);
                        if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                            if(is_numeric($messageTextArray[1])){
                                $return = $this->commandAppealChat($chatId,$checkUser,$messageTextArray[1],$commandLast,$message['message']['text']);
                            }else{
                                $return = $this->commandBad($chatId);
                            }
                        }else{
                            $return = $this->commandBad($chatId);
                        }
                        
                    } elseif ( strpos($messageText, '/rating_') === 0 ) {
                        
                        // Вивід оцінка звернення
                        $messageTextArray = explode("_",$messageText);
                        if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                            if(is_numeric($messageTextArray[1])){
                                $return = $this->commandAppealRating($chatId,$checkUser,$messageTextArray[1],$commandLast,$message['message']['text']);
                            }else{
                                $return = $this->commandBad($chatId);
                            }
                        }else{
                            $return = $this->commandBad($chatId);
                        }
                        
                    } elseif ( strpos($messageText, '/status_') === 0 ) {
                        
                        // Вивід статусів звернення
                        $messageTextArray = explode("_",$messageText);
                        if(isset($messageTextArray[1]) AND count($messageTextArray)==2){
                            if(is_numeric($messageTextArray[1])){
                                $return = $this->commandAppealStatus($chatId,$checkUser,$messageTextArray[1],$commandLast,$message['message']['text']);
                            }else{
                                $return = $this->commandBad($chatId);
                            }
                        }else{
                            $return = $this->commandBad($chatId);
                        }
                        
                    } elseif ( $messageText[0]=="/" && !array_key_exists($messageText, $this->comandAll) ){
                        
                        // Невідома команда
                        $return = $this->commandBad($chatId);
                        
                    } else {
                        if( in_array($commandLast,$this->commandListMyEdit) ){
                            $return = $this->myEdit($fromId,$chatId,$commandLast,$messageText);
                        } elseif ( in_array($commandLast,$this->commandListAppealEdit )){
                            $return = $this->appealEdit($fromId,$chatId,$checkUser,$commandLast,$messageText);
                        }else{
                            // Якщо невідомий текст то перекідає на останню команду
                            $return = $this->userMessage($message,$checkUser,$commandLast);
                        }
                    }
                    break;
                    
            }
        }

        if (isset($message['message']['photo'])) {
            $commandLast = $this->commandLast($chatId);
            if($commandLast=="/appeal"){
                $return = $this->commandAppealPhoto($chatId,$checkUser);
            }else{
                $this->request($method = "sendSticker", ['chat_id' => $chatId, 'sticker' => 'CAACAgIAAxkBAAECXa1gtmAAAR6s59Zzm5nKzKgsUw5OkPcAAuENAAKbAbFJxAPS6ayLVo0fBA'] );
                $return = $this->commandStart($chatId);
            }
        }
    
        if (isset($message['message']['location'])) {
            $return = $this->commandAppealLocation($chatId,$checkUser);
        }
    
        if (isset($message['message']['contact'])) {
            $requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
            $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
            $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
            $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
            $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
            $requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
            $requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n";
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardStart ]] );           
            $return = [
                'method' => $this->method,
                'chat_id' => $chatId,
                'text' => $requestText,
                'keyboard' => $keyboard,
            ];
        }
    
        return $return;
        
    }

    function userRegistration($message,$checkUser)
    {

        $fromId = $message['message']['from']['id'];
        $chatId = $message['message']['chat']['id'];
        
        switch ($checkUser['step']) {
        
            case 1:
                
                // Start Register
                if(!empty($this->startSticker)){
                    $this->request($method = "sendSticker", ['chat_id' => $chatId, 'sticker' => $this->startSticker ] );
                }
                $requestText = "\xF0\x9F\x93\x9D ".lang('Bot.MessageStartTitle')."\n\n";
                $requestText .= lang('Bot.MessageStartDescription')."\n\n";
                $requestText .= "\xF0\x9F\x93\xA2 ".lang('Bot.MessageStartInform')."\n\n";
                $requestText .= "\xE2\x9D\x97 ".lang('Bot.MessageStartWarning')."\n\n";
                $requestText .= lang('Bot.MessageStartRegister');
                $keyboard = array_merge($this->keyboard, [[ $this->keyboardStartRegister ]] );
                break;
                            
            case 2: // No First Name
                $requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterFirstName');
                $keyboard = $this->remove_keyboard;
                break;

            case 3: // No Last Name
                $requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterLastName');
                $keyboard = $this->remove_keyboard;
                break;
            
            case 4: // No City
                $requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
                $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterCity');
                $keyboard = $this->remove_keyboard;
                break;
                
            case 5: // No Address
                $requestText = "\xE2\x9D\x97 ".lang('Bot.MessageRegisterNeedFinish')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
                $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
                $requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterAddress');
                $keyboard = $this->remove_keyboard;
                break;
            
            case 6: // No Email
                $requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
                $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
                $requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
                $requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n\n";
                $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageEnterEmail')."\n\n";
                $requestText .= lang('Bot.MessageEnterEmailSkip');
                
                $keyboard = array_merge($this->keyboard, [[ $this->keyboardStart ]] );
                break;
            case 7: // All Added
                $requestText = "\xE2\x9C\x85 ".lang('Bot.MessageRegisterSucc')."\n\n";
                $requestText .= "<b>".lang('Bot.MessageYourData')."</b>\n";
                $requestText .= "- ".lang('Bot.MessageFirstName').": ".$checkUser['first_name']."\n";
                $requestText .= "- ".lang('Bot.MessageLastName').": ".$checkUser['last_name']."\n";
                $requestText .= "- ".lang('Bot.MessagePhone').": ".$checkUser['phone']."\n";
                $requestText .= "- ".lang('Bot.MessageCity').": ".$checkUser['city']."\n";
                $requestText .= "- ".lang('Bot.MessageAddress').": ".$checkUser['address']."\n";
                $requestText .= "- ".lang('Bot.MessageEmail').": ".$checkUser['email'];
                
                $keyboard = array_merge($this->keyboard, [[ $this->keyboardStart ]] );				
                $data = [ 'active' => 1 ];
                $where = "from_id = ".$fromId; 
                $this->_update($data,$where,"bot_user");
                break;

        }		
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];
        
        return $return;
        
    }

    function commandStart($chatId)
    {
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageStartTitle')."</strong> \n\n";
        $requestText .= lang('Bot.MessageStartDescription')."\n\n";
        $requestText .= "\xE2\x9D\x97 ".lang('Bot.MessageStartWarning');
        
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardAddAppeal, $this->keyboardList ]], [[ $this->keyboardMy, $this->keyboardHelp]] );
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }
    
    function commandHelp($chatId)
    {
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageHelpTitle')."</strong> \n\n";
        $requestText .= lang('Bot.MessageStartDescription')."\n\n";
        $requestText .= lang('Bot.MessageHelpDescription')."\n";
        $requestText .= "/start - ".lang('Bot.MessageCommandStart')."\n";
        $requestText .= "/appeal - ".lang('Bot.MessageCommandAppeal')."\n";
        $requestText .= "/list - ".lang('Bot.MessageCommandList')."\n";
        $requestText .= "/my - ".lang('Bot.MessageCommandMy')."\n";
        $requestText .= "/myedit - ".lang('Bot.MessageCommandMyEdit')."\n";
        $requestText .= "/help - ".lang('Bot.MessageCommandHelp')."\n";
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }

    function commandList($chatId,$checkUser)
    {
        
        $appealList = $this->appealList($checkUser);
        $appealButton = [];
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageListTitle')."</strong> \n\n";
        $requestText .= lang('Bot.MessageListDescription');
        
        foreach($appealList as $key => $value){
            $appealButton[] = [[ 'text' => lang('Bot.MessageAppeal')." #".$key.", від ".date("Y-m-d H:i",$value['date_add']), 'callback_data'=>'/appeal_'.$key ]];
        }
        if(count($appealList)==0){
            $requestText .= lang('Bot.MessageAppealNoFound');
        }
        
        $keyboard = array_merge($this->inline_keyboard, $appealButton, [[ $this->keyboardBackToStart ]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }
    
    function commandAppeal($chatId,$checkUser,$messageText) 
    {

        $timeLeave = time()+3600;
        $appeal = $this->appealLast($checkUser['id']);

        if( $messageText!="/appeal" AND $messageText!=lang('Bot.ButtonAddAppeal')){
            
            if(count($appeal)>0){
                
                $filed = "";
                
                if ( empty($appeal['content']) ){
                    $appealStep = 4; // Введено опис
                    $filed = "content";					
                } elseif( empty($appeal['address']) ){
                    $appealStep = 5; // Введено адрес
                    $filed = "address";
                } else{
                    $appealStep = 1;
                }
                
                $appealGallery = $this->appealGallery($appeal['id']);
                $appealGalleryCount = count($appealGallery);
                
                if(!empty($filed)){
                    $data = [ $filed => $messageText ];
                    $where = "id = ".$appeal['id'];
                    $this->_update($data,$where,"appeal");
                }
                if( $appealStep==3 ){
                    $data = [ $filed => $messageText ];
                    $where = "id = ".$checkUser['id'];
                    $this->_update($data,$where,"bot_user");					
                }
                
                // Введено текст
                $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
                
                switch($appealStep){
                    case 1:
                        $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
                        $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealAddress')." ".$appeal['address']."\n\n";
                        if($appealGalleryCount>0){
                            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
                        }else{
                            $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }
                        if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                            $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }else{
                            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
                        }
                        $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
                        $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
                        $keyboard = array_merge($this->keyboard, [[ $this->keyboardAppealEditContent, $this->keyboardAppealEditAddress ]], [[ $this->keyboardConfirm, $this->keyboardBackToStart]]);
                        break;
                        
                    case 2:
                        $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageMyEditEmailNoCorrect')."\n";
                        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
                        break;
                        
                    case 3:
                        $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep2')."\n";
                        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
                        break;
                        
                    case 4:
                        $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep3')."\n";
                        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
                        break;
                        
                    case 5:
                        $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
                        $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealAddress')." ".$messageText."\n\n";
                        if($appealGalleryCount>0){
                            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
                        }else{
                            $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }
                        if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                            $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
                        }else{
                            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
                        }
                        $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
                        $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
                        $keyboard = array_merge($this->keyboard, [[ $this->keyboardAppealEditContent, $this->keyboardAppealEditAddress ]], [[ $this->keyboardConfirm, $this->keyboardBackToStart ]]);
                        break;
                }
                
            }else{
                $requestText = lang('Bot.MessageBadCommand');
            }
            
        }else{
            
            // Введено команду
            $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppealDescStart')."\n\n";
            
            if( empty($checkUser['email']) ){
                $requestText .= "\x31\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep2')."\n";
                $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep3')."\n";
                $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep4')."\n";
                $requestText .= "\x34\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep5')."\n\n";				
            }else{
                $requestText .= "\x31\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep2')."\n";
                $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep3')."\n";
                $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep4')."\n";
                $requestText .= "\x34\xE2\x83\xA3 ".lang('Bot.MessageAppealDescStep5')."\n\n";
            }
            
            $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealDescStep2')."\n";
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
            
            if(count($appeal)>0){
                
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
                
            }else{
                
                $data = [
                    'user_id' => $checkUser['id'],
                    'date_add' => time(),
                    'date_update' => time(),
                    'date_leave' => $timeLeave
                ];
                $this->_insert($data,"appeal");
                
            }
            
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }
    
    function commandAppealEdit($fromId,$chatId,$command)
    {
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealEdit'.$command.'Title')."</strong> \n\n";
        $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageAppealEdit'.$command);

        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];
        
        return $return;		
        
    }
    
    function commandAppealPhoto($chatId,$checkUser)
    {
        
        $appeal = $this->appealLast($checkUser['id']);
        
        if(isset($appeal['id'])){
            
            $appealGallery = $this->appealGallery($appeal['id']);
            $appealGalleryCount = count($appealGallery);
            
            $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealContent')." ".$appeal['content']."\n\n";
            $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealAddress')." ".$appeal['address']."\n\n";
            if($appealGalleryCount>0){
                $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
            }else{
                $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }
            if(empty($appeal['location_lat']) || empty($appeal['location_lng'])){
                $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealLocation')." ".$appeal['location_lat'].", ".$appeal['location_lng']."\n\n";
            }
            $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
            $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardAppealEditContent, $this->keyboardAppealEditAddress ]], [[$this->keyboardConfirm, $this->keyboardBackToStart]]);
            
        }else{
            
            $requestText = "\xE2\xAD\x90 NO<strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
            
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;		
        
    }
    
    function commandAppealLocation($chatId,$checkUser)
    {

        $appealLast = $this->appealLast($checkUser['id']);
        if(isset($appealLast['id'])){
            
            $appealGallery = $this->appealGallery($appealLast['id']);
            $appealGalleryCount = count($appealGallery);
            
            $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
            if(empty($appealLast['content'])){
                $requestText .= "\x31\xE2\x83\xA3 ".lang('Bot.MessageAppealContent')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealContent')." ".$appealLast['content']."\n\n";
            }
            if($appealGalleryCount>0){
                $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealPhotoCount')." ".$appealGalleryCount."\n\n";
            }else{
                $requestText .= "\x32\xE2\x83\xA3 ".lang('Bot.MessageAppealPhoto')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }
            if(empty($appealLast['location_lat']) || empty($appealLast['location_lng'])){
                $requestText .= "\x33\xE2\x83\xA3 ".lang('Bot.MessageAppealLocation')." ".lang('Bot.MessageAppealNoValue')."\n\n";
            }else{
                $requestText .= "\xE2\x9C\x85 ".lang('Bot.MessageAppealLocation')." ".$appealLast['location_lat'].", ".$appealLast['location_lng']."\n\n";
            }
            
            $requestText .= "".lang('Bot.MessageAppealDescUpdate')."\n\n";
            $requestText .= "".lang('Bot.MessageAppealDescEnd')."\n\n";
            $keyboard = array_merge($this->keyboard, [[$this->keyboardConfirm, $this->keyboardBackToStart]]);
            
        }else{
            $requestText = "\xE2\xAD\x90 NO<strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
        }

        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;	
        
    }
    
    function commandAppealConfirm($chatId,$checkUser)
    {
    
        $appeal = $this->appealLast($checkUser['id']);
        
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
    
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealConfirmTitle')."</strong> \n\n";
        $requestText .= lang('Bot.MessageAppealConfirmDesc');
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        // Інформування менеджерів
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
        
        return $return;
    
    }
    
    function commandAppealView($chatId,$checkUser,$appealId,$commandLast)
    {
        
        $appeal = [];
        $appealChat = [];
        $appealWork = [];
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
        
        if(count($appeal)>0){
                        
            // Header
            $appealLocationStr = "";
            if( !empty($appeal['location_lat']) && !empty($appeal['location_lng']) ){
                $appealLocationStr = $appeal['location_lat'].", ".$appeal['location_lng'];
            }
            
            $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppeal')." №".$appeal['id']."</strong> \n";
            $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add'])."\n\n";
            
            $requestText .= "<strong>".lang('Bot.MessageAppealContent')."</strong>. ".$appeal['content']."\n\n";
            $requestText .= "<strong>".lang('Bot.MessageAppealAddress')."</strong>. ".$appeal['address']."\n\n";
            if(!empty($appealLocationStr)){
                $requestText .= "<strong>".lang('Bot.MessageAppealLocation')."</strong>. ".$appealLocationStr."\n\n";
            }
            
            // Status
            $directoryStatus = $this->directoryStatus();
            $lastStatus = $this->lastStatus($appealId);
            $requestText .= "<strong>".lang('Bot.MessageAppealStatusActual')."</strong>. ".$directoryStatus[$lastStatus];
            
            // Implementer
            if(count($implementer)>0){
                $implementerStr = "";
                $implementerStr .= $implementer['first_name']." ".$implementer['last_name'];
                $implementerStr .= ", ".$implementer['title'];
                if(!empty($implementer['comment_user'])) {
                    $implementerStr .= ", ".$implementer['comment_user'];
                }
                $requestText .= "\n\n <strong>".lang('Bot.Implementer')."</strong>. ".$implementerStr;
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
                $requestText .= "\n\n <strong>".lang('Bot.MessageAppealRating')."</strong>. ".$ratingStar." (".$appealRating.")";
            } 
            
            $data = [
                'chat_id' => $chatId,
                'text' => $requestText,
                'method' => $this->method
            ];
            $this->sendMessage($data);

        
            // галерея
            $appealGallery = $this->appealGallery($appealId);			
            if(count($appealGallery)>0){
                foreach($appealGallery as $value){
                    $images[] = [
                        'type' => 'photo',
                        'media' => base_url()."/assets/upload/appeal/".$appeal['id']."/".$value['image'],
                    ];
                }				
                $method = "sendMediaGroup";
                $data = [
                    'chat_id' => $chatId,
                    'media' => json_encode($images)
                ];
                $this->request($method, $data);
            }
            
            $requestText = "\n\n\xF0\x9F\x9A\xA9 <b><a href='".base_url()."/appeal/".$appealId."'>".lang('Bot.ReadMoreSite')."</a></b> \n\n";
            $requestText .= lang('Bot.MessageAppealControl');
            
            if ( $appeal['status']==5 && $appealRating==0 ) {
                $button[] = [[ 'text' => lang('Bot.ButtonAppealRating'), 'callback_data'=>'/rating_'.$appealId ]];
            }            
            
            $button[] = [[ 'text' => lang('Bot.ButtonAppealStatus'), 'callback_data'=>'/status_'.$appealId ]];
            if(count($appealChat)>0){
                $button[] = [[ 'text' => lang('Bot.ButtonAppealChat'), 'callback_data'=>'/chat_'.$appealId ]];    
            }

            $keyboard = array_merge($this->inline_keyboard, $button, [[ $this->keyboardList ]], [[ $this->keyboardBackToStart ]]);
            
        }else{
        
            $requestText = "\xE2\x9D\x97 <strong>".lang('Bot.MessageAppealNoFoundTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppealNoFoundDesc');
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
        
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;		
        
    }
    
    function commandAppealChat($chatId,$checkUser,$appealId,$commandLast,$message)
    {

        $appeal = [];
        $appealChat = [];
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
        
    
        if(count($appeal)>0){

            // Чат
            if(count($appealChat)>0){
                
                if ( strpos($message, "/chat")===false ) {
                    $newMessage = [
                        'appeal_id' => $appealId,
                        'user_id' => $checkUser['id'],
                        'date_add' => time(),
                        'message' => $message,
                        'who'=>1,
                    ];
                    $messageId = $this->_insert($newMessage,'appeal_chat');
                    $newMessage['id'] = $messageId;
                    $appealChat[$messageId] = $newMessage;
                
                }

                
                $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealChatTitle')."</strong> \n\n";
                $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
                $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add']);
                $data = [
                    'chat_id' => $chatId,
                    'text' => $requestText,
                    'method' => $this->method
                ];
                $this->sendMessage($data);
                
                foreach($appealChat as $value){
                    $requestText = lang('Bot.MessageId').": ".$value['id']."\n";
                    $requestText .= lang('Bot.MessageAppealChatDate')." ".date("Y.m.d H:i:s",$value['date_add'])."\n";
                    $requestText .= lang('Bot.MessageAppealChatSender'.$value['who'])."\n\n";
                    
                    $requestText .= lang('Bot.Message').": ".$value['message'];
                    $data = [
                        'chat_id' => $chatId,
                        'text' => $requestText,
                        'method' => $this->method
                    ];
                    $this->sendMessage($data);
                }
                
                $requestText = "\xE2\x9C\x8F ".lang('Bot.MessageAppealChatSendMessage');
                
                $button[] = [[ 'text' => lang('Bot.MessageViewAppealBack'), 'callback_data'=>'/appeal_'.$appealId ]];
                $keyboard = array_merge($this->inline_keyboard, $button);

            } else {

                $requestText = "\xE2\x9D\x97 <strong>".lang('Bot.MessageAppealChatNoFoundTitle')."</strong> \n\n";
                $requestText .= lang('Bot.MessageAppealChatNoFoundDesc');
                $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
                
            }
            
        } else {
            
            $requestText = "\xE2\x9D\x97 <strong>".lang('Bot.MessageAppealNoFoundTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppealNoFoundDesc');
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
            
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;	
     
    }
    
    function commandAppealRating($chatId,$checkUser,$appealId,$commandLast,$message)
    {
        
        $appeal = [];
        $appealRating = [];
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
        
        if(count($appeal)>0){

            if ( strpos($message, "/rating")===false ) {
                if ( $message>0 && $message<6 ) {
                    $appealRating = [
                        'appeal_id' => $appealId,
                        'rating' => $message,
                        'date_add' => time()
                    ];
                    $appealRating['id'] = $this->_insert($appealRating,'appeal_rating');
                }
            }

            $button[] = [[ 'text' => lang('Bot.MessageViewAppealBack'), 'callback_data'=>'/appeal_'.$appealId ]];
            
            if(count($appealRating)==0){
                
                $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealRatingTitle')."</strong> \n";
                $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
                $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add'])."\n\n";
                $requestText .= lang('Bot.MessageAppealRatingDesc');
                
                $buttonRating = [];
                for($n=1;$n<6;$n++){
                    $buttonRating[] = [ 'text' => $n, 'callback_data' => $n ];
                }                
                
                $keyboard = array_merge($this->inline_keyboard, [ $buttonRating ], $button );

            } else {

                $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealRatingTitle')."</strong> \n\n";
                $requestText .= lang('Bot.MessageAppealRatingAdded');
                $keyboard = array_merge($this->inline_keyboard, $button );
                
            }
            
        } else {
            
            $requestText = "\xE2\x9D\x97 <strong>".lang('Bot.MessageAppealNoFoundTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppealNoFoundDesc');
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
            
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
        
        
    }
    
    function commandAppealStatus($chatId,$checkUser,$appealId,$commandLast,$message)
    {
        
        $appeal = [];
        $appealStatus = [];
        $appealStatusName = [];
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
                
    
        if(count($appeal)>0){

            $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealStatusTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppeal')." №".$appeal['id']."\n";
            $requestText .= lang('Bot.MessageAppealDateFrom')." ".date("Y.m.d H:i",$appeal['date_add']);
            $data = [
                'chat_id' => $chatId,
                'text' => $requestText,
                'method' => $this->method
            ];
            $this->sendMessage($data);
            
            $c=0;
            foreach($appealStatus as $value){
                $c++;
                $requestText = "";
                //$requestText = lang('Bot.StatusId').": ".$value['id']."\n";
                $requestText .= lang('Bot.MessageAppealChatDate')." ".date("Y.m.d H:i:s",$value['date_add'])."\n";
                $requestText .= lang('Bot.MessageAppealStatus').": ".$appealStatusName[$value['status_id']]."\n";
                if (!empty($value['comment'])) {
                    $requestText .= lang('Bot.MessageAppealComment').": ".$value['comment'];
                }
                $data = [
                    'chat_id' => $chatId,
                    'text' => $requestText,
                    'method' => $this->method
                ];
                if(count($appealStatus)!=$c){
                    $this->sendMessage($data);
                }

            }
            $button[] = [[ 'text' => lang('Bot.MessageViewAppealBack'), 'callback_data'=>'/appeal_'.$appealId ]];
            $keyboard = array_merge($this->inline_keyboard, $button);
            
        } else {
            
            $requestText = "\xE2\x9D\x97 <strong>".lang('Bot.MessageAppealNoFoundTitle')."</strong> \n\n";
            $requestText .= lang('Bot.MessageAppealNoFoundDesc');
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardList, $this->keyboardBackToStart]]);
            
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;	
      
    }
    
    function commandMy($fromId,$chatId)
    {
        
        $myData = $this->myData($fromId);
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageMyTitle')."</strong> \n\n";
        $requestText .= "- ".lang('Bot.MessageFirstName').": ".$myData['first_name']."\n";
        $requestText .= "- ".lang('Bot.MessageLastName').": ".$myData['last_name']."\n";
        $requestText .= "- ".lang('Bot.MessagePhone').": ".$myData['phone']."\n";
        $requestText .= "- ".lang('Bot.MessageEmail').": ";
        $requestText .= (empty($myData['email']))? lang('Bot.MessageAppealNoValue') : $myData['email'];
        $requestText .= "\n";
        $requestText .= "- ".lang('Bot.MessageCity').": ".$myData['city']."\n";
        $requestText .= "- ".lang('Bot.MessageAddress').": ".$myData['address']."\n";
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardMyEdit, $this->keyboardBackToStart]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }	
    
    function commandMyEdit($fromId,$chatId,$command)
    {
        
        $myData = $this->myData($fromId);
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageMyEditTitle')."</strong> \n\n";
        $requestText .= "- ".lang('Bot.MessageFirstName').": ".$myData['first_name']."\n";
        $requestText .= "- ".lang('Bot.MessageLastName').": ".$myData['last_name']."\n";
        $requestText .= "- ".lang('Bot.MessagePhone').": ".$myData['phone']."\n";
        $requestText .= "- ".lang('Bot.MessageEmail').": ";
        $requestText .= (empty($myData['email']))? lang('Bot.MessageAppealNoValue') : $myData['email'];
        $requestText .= "\n";
        $requestText .= "- ".lang('Bot.MessageCity').": ".$myData['city']."\n";
        $requestText .= "- ".lang('Bot.MessageAddress').": ".$myData['address']."\n\n";
        
        if($command=="Start"){
            $requestText .= lang('Bot.MessageMyEdit'.$command);
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardEditFirstName, $this->keyboardEditLastName ]], [[ $this->keyboardEditCity, $this->keyboardEditAddress ]], [[ $this->keyboardEditEmail, $this->keyboardBackToStart]] );
        } elseif ($command=="Save"){
            $requestText .= "\xE2\x9C\x85 <strong>".lang('Bot.MessageMyEdit'.$command)."</strong>";
            $keyboard = array_merge($this->keyboard, [[ $this->keyboardEditFirstName, $this->keyboardEditLastName ]], [[ $this->keyboardEditCity, $this->keyboardEditAddress ]], [[ $this->keyboardEditEmail, $this->keyboardBackToStart]] );
        }else{
            $requestText .= "\xE2\x9C\x8F ".lang('Bot.MessageMyEdit'.$command);
            $keyboard = array_merge($this->keyboard, [[$this->keyboardMy]]);
        }
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }
    
    function commandOther($chatId,$commandLast)
    {
        
        $requestText = lang('Bot.MessageOther');
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }
    
    function commandBad($chatId)
    {
        
        $requestText = lang('Bot.MessageBadCommand');
        $keyboard = array_merge($this->keyboard, [[ $this->keyboardBackToStart ]]);
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];		
        
        return $return;
        
    }	
    
    function commandLast($chatId)
    {
        
        $return = NULL;
        $builder = $this->db->table($this->prefix."telegram_command");
        $builder->where('chat_id',$chatId);
        $builder->orderBy('date_add', 'DESC');
        $builder->limit(1);
        $query = $builder->get();		
        foreach ($query->getResult() as $item){
            $return = $item->command;
        }
        
        return $return;
        
    }
    
    function appealLast($userId)
    {
        
        $appeal = [];
        $builder = $this->db->table($this->prefix."appeal");
        $builder->where('user_id',$userId);
        $builder->where('date_leave > ', time());
        $builder->where('status', 0);
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
    
    function appealEdit($fromId,$chatId,$checkUser,$commandLast,$messageText)
    {
        
        $appeal = $this->appealLast($checkUser['id']);
        if(count($appeal)>0){
            if($commandLast=="/appealeditcontent"){
                $field = "content";
            }elseif($commandLast=="/appealeditaddress"){
                $field = "address";
            }
            
            $data = [ $field => $messageText ];
            $where = "id = ".$appeal['id'];
            $this->_update($data,$where,"appeal");
        }
        
        $requestText = "\xE2\xAD\x90 <strong>".lang('Bot.MessageAppealTitle')."</strong> \n\n";
        $requestText .= "!!!";

        $keyboard = array_merge($this->keyboard, [[ $this->keyboardAppealEditContent, $this->keyboardAppealEditAddress ]], [[$this->keyboardConfirm, $this->keyboardBackToStart]]);
        
        $return = [
            'method' => $this->method,
            'chat_id' => $chatId,
            'text' => $requestText,
            'keyboard' => $keyboard,
        ];
        
        $data = [
            'chat_id' => $chatId,
            'date_add' => time(),
            'command' => "/appeal",
        ];
        $this->_insert($data,"telegram_command");		
        
        return $this->commandAppeal($chatId,$checkUser,$messageText);
        
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
    
    function myEdit($fromId,$chatId,$commandLast,$messageText)
    {
        
        $command = "Save";
        switch ($commandLast) {
            
            case '/editfirstname':
                $this->myEditSave($fromId,$chatId,"first_name",$messageText);
                $return = $this->commandMyEdit($fromId,$chatId,$command);
            break;

            case '/editlastname':
                $this->myEditSave($fromId,$chatId,"last_name",$messageText);
                $return = $this->commandMyEdit($fromId,$chatId,$command);
            break;

            case '/editcity':
                $this->myEditSave($fromId,$chatId,"city",$messageText);
                $return = $this->commandMyEdit($fromId,$chatId,$command);
            break;
            
            case '/editaddress':
                $this->myEditSave($fromId,$chatId,"address",$messageText);
                $return = $this->commandMyEdit($fromId,$chatId,$command);
            break;

            case '/editemail':
                if (filter_var($messageText, FILTER_VALIDATE_EMAIL)){
                    $this->myEditSave($fromId,$chatId,"email",$messageText);
                    $return = $this->commandMyEdit($fromId,$chatId,$command);					
                }else{
                    $command = "EmailNoCorrect";
                    $return = $this->commandMyEdit($fromId,$chatId,$command);
                }
            break;
            
        }		
        
        return $return;
        
    }
    
    function myEditSave($fromId,$chatId,$field,$messageText)
    {
        
        $messageText = trim($messageText);
        $data[$field] = $messageText;
        $where = "from_id = ".$fromId;
        $this->_update($data,$where,"bot_user");
        
        $data = [];
        $data['chat_id'] = $chatId;
        $data['date_add'] = time();
        $data['message'] = "/myedit";
        $this->_insert($data,"telegram_message");

        $data = [];
        $data['chat_id'] = $chatId;
        $data['date_add'] = time();
        $data['command'] = "/myedit";
        $this->_insert($data,"telegram_command");
        
    }	

    function myData($fromId)
    {
        
        $myData = [];
        $builder = $this->db->table($this->prefix."bot_user");
        $builder->where('from_id',$fromId);
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
    
    function checkUser($message)
    {
        
        $active = $step = $fromId = 0;
        if (isset($message['message'])) {
            $fromId = $message['message']['from']['id'];
            $chatId = $message['message']['chat']['id'];
        }
        
        if (isset($message['callback_query'])) {
            $fromId = $message['callback_query']['from']['id'];
            $chatId = $message['callback_query']['from']['id'];
        }
        
        $builder = $this->db->table($this->prefix."bot_user");
        $builder->where('from_id',$fromId);
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
                    $where = "from_id = ".$fromId;
                    $this->_update($data,$where,"bot_user");
                }
                if( $active==1 AND $item->active==0 ){
                    $data['active'] = 1;
                    $where = "from_id = ".$fromId;
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
            
        }else{
            
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

    function sendMessage($messages)
    {
        
        if(!empty($messages)){
        
            $data = array(
                'chat_id' => $messages['chat_id'],
                'text' => $messages['text'],
                'parse_mode' => 'html',
            );
            
            if(isset($messages['keyboard']) AND count($messages['keyboard'])>0){
                        
                $keyboardType = $messages['keyboard']['type'];
                unset($messages['keyboard']['type']);
                        
                $keyboard = array(
                    $keyboardType => $messages['keyboard'],
                    'one_time_keyboard' => true,
                    'resize_keyboard' => true
                );
                    
                if( $keyboardType=="remove_keyboard" ){
                    $keyboard = ['remove_keyboard' => true];
                }
                $data['reply_markup'] = json_encode($keyboard);
                
            }else{
                $keyboard = ['remove_keyboard' => true];
                $data['reply_markup'] = json_encode($keyboard);
            }
            
            $this->request($messages['method'],$data);
        }
    }

    function request($method = "sendMessage", $data = array())
    {
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
    
    function saveTelegramMessage($chatId, $messageText) 
    {
        $data = array(
            'chat_id' => $chatId,
            'date_add' => time(),
            'message' => $messageText,
        );			
        $this->_insert($data,"telegram_message");
    }

    function saveTelegramCommand($chatId, $messageText) 
    {
        $data = array(
            'chat_id' => $chatId,
            'date_add' => time(),
            'command' => $messageText,
        );			
        $this->_insert($data,"telegram_command");
    }
    
    function saveSenderData($chatId, $field, $value)
    {
        if ($field=="email") {
            if (filter_var($value, FILTER_VALIDATE_EMAIL) ){
                $data[$field] = $value;
                $data['emailskip'] = 0;
                $where = "chat_id = '".$chatId."'";
                $this->_update($data, $where, "bot_user");
            }
        } else {
            $data[$field] = $value;
            $where = "chat_id = '".$chatId."'";
            $this->_update($data, $where, "bot_user");
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
    
    function _testSend($chatId, $text) 
    {
        $message = [
            'method' => 'sendMessage',
            'chat_id' => $chatId,
            'text' => $text,
        ];
        $this->sendMessage($message);
        die();
    }
    
}