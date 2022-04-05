<?php
namespace App\Controllers;

class Manager extends BaseController
{
    
    public function __construct()
    {	

        $this->bgColor = "#ffcc33";
        $this->db = \Config\Database::connect();
        $this->model = model('App\Models\Manager', false);
        $this->email = model('App\Models\Email', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->time = time();
        $this->uri = service('uri');
        $this->managerUrl = $this->uri->getSegment(1);
        $this->queryUrl = $this->uri->getQuery();
        if(!empty($this->queryUrl)){
            parse_str($this->queryUrl, $this->queryUrl);
        }else{
            $this->queryUrl = [];
        }
        
        $this->session = session();
        $this->login_user = $this->model->isLogin($this->managerUrl);
        
        $this->menu['bashboard'] = ['title'=>lang('Manager.homepage'),'url'=>$this->managerUrl,'class'=>'uk-hidden'];
        $this->menu['appeal'] = ['title'=>lang('Manager.appeal'),'url'=>$this->managerUrl.'/appeal','class'=>''];
        $this->menu['applicant'] = ['title'=>lang('Manager.applicants'),'url'=>$this->managerUrl.'/applicant','class'=>''];
        if ($this->login_user['group_id']==1 || $this->login_user['group_id']==2 ) {
            $this->menu['statistic'] = ['title'=>lang('Manager.stat'),'url'=>$this->managerUrl.'/statistic','class'=>''];
            $this->menu['message'] = ['title'=>lang('Manager.message_packages'),'url'=>$this->managerUrl.'/message','class'=>''];
        }
        if ($this->login_user['group_id']==1) {
            $this->menu['library'] = ['title'=>lang('Manager.libraries'),'url'=>$this->managerUrl.'/library','class'=>''];
            $this->menu['library']['childs']['statuses'] = ['title'=>lang('Manager.statuses'),'url'=>$this->managerUrl.'/statuses','class'=>''];
            $this->menu['library']['childs']['users'] = ['title'=>lang('Manager.users'),'url'=>$this->managerUrl.'/users','class'=>''];
            $this->menu['library']['childs']['logs'] = ['title'=>lang('Manager.log_users'),'url'=>$this->managerUrl.'/logs','class'=>''];
            $this->menu['library']['childs']['appeal-request'] = ['title'=>lang('Manager.appeal_request'),'url'=>$this->managerUrl.'/appeal-request','class'=>''];
        }
        
    } 
    
    public function index()
    {
        return redirect()->to( current_url(true)."/appeal" ); 
    }
    
    public function appeal($id=0)
    {
       
        $this->login_user = $this->model->isLogin($this->managerUrl);
       
        $submit = $this->request->getVar('submit');
        $appealAccess = 1;
        
        if ($id == 0) {
            
            $filterQuery = 'inwork';
            $uri = current_url(true);
            $uri_query = $uri->getQuery();
            if(!empty($uri_query)){
                parse_str($uri_query, $uri_param);
                $filterQuery = (isset($uri_param['filter']))?$uri_param['filter']:$filterQuery;
            }
            
            $appealAccess = 0;
            $perPage = 20;
            $template = "appeal";
            
            $entries = array();
            $artriesTotal = 0;
            $entryIds = array();
            $userIds = array();
            $users = array();
            $status[0] = lang('Manager.status_leave');
            
            $status = [];
            $statusStat[0] = 0;
            $date_start = strtotime('01.01.2022 00:00:01', time());
            $date_end = strtotime('last day of this month 23:59:59', time());
            $statusQuery = "notselect";
            $ratingQuery = "notselect";
            $implementerQuery = "notselect";
            $userHeadQuery = "notselect";
            $overdueQuery = 0;
            $uri = current_url(true);
            $uri_query = $uri->getQuery();
            
            if(!empty($uri_query)){
                parse_str($uri_query, $uri_param);            
                $date_start = (isset($uri_param['start']))?strtotime($uri_param['start']." 00:00:01"):$date_start;
                $date_end = (isset($uri_param['end']))?strtotime($uri_param['end']." 23:59:59"):$date_end;
                $statusQuery = (isset($uri_param['status']))?$uri_param['status']:$statusQuery;
                $ratingQuery = (isset($uri_param['rating']))?$uri_param['rating']:$ratingQuery;
                $implementerQuery = (isset($uri_param['implementer']))?$uri_param['implementer']:$implementerQuery;
                $userHeadQuery = (isset($uri_param['user_head']))?$uri_param['user_head']:$userHeadQuery;
                $overdueQuery = (isset($uri_param['overdue']))?$uri_param['overdue']:$overdueQuery;
            }            

            // User Head
            $userHead = [];
            $builder = $this->db->table($this->prefix.'user');
            $query = $builder->where('group_id',2);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $userHead[$item->id] = [
                    'name' => $item->first_name." ".$item->last_name,
                    'title' => $item->title,
                ];
            }

            // Implementer
            $implementer = [];
            $builder = $this->db->table($this->prefix.'user');
            $query = $builder->where('group_id',3);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $implementer[$item->id] = [
                    'name' => $item->first_name." ".$item->last_name,
                    'title' => $item->title,
                ];
            }

            // Status Title
            $builder = $this->db->table($this->prefix.'status');
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $status[$item->id] = $item->title;
                $statusStat[$item->id] = 0;
            }
            unset($status[3]);
            
            $builder = $this->db->table($this->prefix.'appeal');
            $builder->select($this->prefix.'appeal.id, '.$this->prefix.'appeal.status');
            $builder->where($this->prefix.'appeal.date_add >', $date_start);
            $builder->where($this->prefix.'appeal.date_add <', $date_end);                    
            if(is_numeric($statusQuery)){
                $builder->where($this->prefix.'appeal.status', $statusQuery);
            }else{
                $builder->where($this->prefix.'appeal.status > 0');
            }
            if(is_numeric($ratingQuery)){
                $builder->join($this->prefix.'appeal_rating', $this->prefix.'appeal_rating.appeal_id = '.$this->prefix.'appeal.id', 'left');
                if($ratingQuery<6){
                    $builder->where($this->prefix.'appeal_rating.rating', $ratingQuery);
                } else {
                    $builder->where($this->prefix.'appeal_rating.rating > 0');
                }
            }
            
            if(is_numeric($implementerQuery)){
                $builder->join($this->prefix.'appeal_user as u1', 'u1.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where('u1.user_id', $implementerQuery);
            }            

            if(is_numeric($userHeadQuery)){
                $builder->join($this->prefix.'appeal_user as u2', 'u2.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where('u2.user_id', $userHeadQuery);
            }
            
            if($overdueQuery==1){
                if(is_numeric($statusQuery)){
                    if($statusQuery==5){
                        $builder->where('FROM_UNIXTIME ('.$this->prefix.'appeal.date_appeal, "%Y%m%d") < ', 'FROM_UNIXTIME ('.$this->prefix.'appeal.date_update , "%Y%m%d")');
                    }else{
                        $builder->where($this->prefix.'appeal.date_appeal < ', mktime(23,59,59,date("m",time()),date("d",time()),date("Y",time())) );
                    }
                }
            }
            if ($this->login_user['group_id']>1) {
                $builder->join($this->prefix.'appeal_user', $this->prefix.'appeal_user.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where($this->prefix.'appeal_user.user_id', $this->login_user['id']);
            }
            
            $artriesTotal = $builder->countAllResults();
            
            $builder = $this->db->table($this->prefix.'appeal'); 
            $select = $this->prefix.'appeal.*';
            $select .= ', FROM_UNIXTIME ('.$this->prefix.'appeal.date_appeal , "%Y%m%d") as test';
     
            $builder->select($select);
            $builder->where($this->prefix.'appeal.date_add >', $date_start);
            $builder->where($this->prefix.'appeal.date_add <', $date_end);
             
            if(is_numeric($statusQuery)){
                $builder->where($this->prefix.'appeal.status', $statusQuery);
            }else{
                $builder->where($this->prefix.'appeal.status > 0');
            }
            if(is_numeric($ratingQuery)){
                $builder->join($this->prefix.'appeal_rating', $this->prefix.'appeal_rating.appeal_id = '.$this->prefix.'appeal.id', 'left');
                if($ratingQuery<6){
                    $builder->where($this->prefix.'appeal_rating.rating', $ratingQuery);
                } else {
                    $builder->where($this->prefix.'appeal_rating.rating > 0');
                }
            }
            
            if(is_numeric($implementerQuery)){
                $builder->join($this->prefix.'appeal_user as u1', 'u1.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where('u1.user_id', $implementerQuery);
            }            

            if(is_numeric($userHeadQuery)){
                $builder->join($this->prefix.'appeal_user as u2', 'u2.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where('u2.user_id', $userHeadQuery);
            }
            
            if($overdueQuery==1){
                if(is_numeric($statusQuery)){
                    if($statusQuery==5){
                        $builder->where('FROM_UNIXTIME ('.$this->prefix.'appeal.date_appeal, "%Y%m%d") < ', 'FROM_UNIXTIME ('.$this->prefix.'appeal.date_update , "%Y%m%d")');
                    }else{
                        $builder->where($this->prefix.'appeal.date_appeal < ', mktime(23,59,59,date("m",time()),date("d",time()),date("Y",time())) );
                    }
                }
            }
            
            if ($this->login_user['group_id']>1) {
                $builder->join($this->prefix.'appeal_user', $this->prefix.'appeal_user.appeal_id = '.$this->prefix.'appeal.id');
                $builder->where($this->prefix.'appeal_user.user_id', $this->login_user['id']);
            }
            $builder->orderBy('date_add', 'DESC');
            
            $page = 0;
            $group = "default";
            $pager = \Config\Services::pager(null, null, false);
            $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
            $this->pager = $pager->store($group, $page, $perPage, $artriesTotal);
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage,$offset);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ($item as $key=>$value){
                    if ( $key=="content" ) {
                        $value = mb_strimwidth($value, 0, 300, "...");
                    }
                    $entries[$item->id][$key] = $value;
                }
                $entries[$item->id]['appeal_date'] = null;
                $entries[$item->id]['appeal_date_approved'] = null;
                $entries[$item->id]['appeal_request'] = 0;
                $entries[$item->id]['appeals'] = 0;
                $entries[$item->id]['rating'] = 0;
                $entries[$item->id]['implementer'] = [];
                $entries[$item->id]['head'] = [];
                $userIds[] = $item->user_id;
                $entryIds[] = $item->id;
            }
            $userIds = array_unique($userIds);
            
            $pagination = $this->pager;
            if($artriesTotal>$perPage){
                $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
            }else{
                $data['pagination'] = "";
            }

            if(count($userIds)>0){
                
                $builder = $this->db->table($this->prefix.'appeal_date');
                $builder->whereIn('appeal_id', $entryIds);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $entries[$item->appeal_id]['appeal_date'] = $item->date;
                    $entries[$item->appeal_id]['appeal_date_approved'] = $item->approved;
                }                
                
                $builder = $this->db->table($this->prefix.'bot_user');
                $builder->whereIn('id', $userIds);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key=>$value){
                        $users[$item->id][$key] = $value;
                    }
                }
                
                $builder = $this->db->table($this->prefix.'appeal_rating');
                $builder->whereIn('appeal_id', $entryIds);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $entries[$item->appeal_id]['rating'] = $item->rating;
                }
                
                $builder = $this->db->table($this->prefix.'appeal_user');
                $builder->whereIn('appeal_id', $entryIds);
                $builder->join($this->prefix.'user', $this->prefix.'user.id = '.$this->prefix.'appeal_user.user_id');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    if($item->group_id==2){
                        $entries[$item->appeal_id]['head'][] = [
                            'user_id' => $item->user_id,
                            'first_name' => $item->first_name,
                            'last_name' => $item->last_name,
                        ];
                    }
                    if($item->group_id==3){
                        $entries[$item->appeal_id]['implementer'][] = [
                            'user_id' => $item->user_id,
                            'first_name' => $item->first_name,
                            'last_name' => $item->last_name,
                            'title' => $item->title,
                            'last_name' => $item->last_name,
                            'comment' => $item->comment,
                            'comment_user' => $item->comment_user,
                        ];
                    }
                }               
                
                $builder = $this->db->table($this->prefix.'appeal_request');
                $builder->whereIn('appeal_id', $entryIds);
                $builder->where('approved', 0);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $entries[$item->appeal_id]['appeal_request'] = 1;
                }
                
                
            }
            
            $builder = $this->db->table($this->prefix.'status');
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $status[$item->id] = $item->title;
            }
            
            $data['filterQuery'] = $filterQuery;
            $data['users'] = $users;
            $data['status'] = $status;
            $data['entries'] = $entries;
            $data['artriesTotal'] = $artriesTotal;
            $data['pageTitle'] = $this->menu['appeal']['title'];
            
            $data['statusQuery'] = $statusQuery;
            $data['ratingQuery'] = $ratingQuery;
            $data['implementerQuery'] = $implementerQuery;
            $data['userHeadQuery'] = $userHeadQuery;
            $data['overdueQuery'] = $overdueQuery;
            $data['date_start'] = $date_start;
            $data['date_end'] = $date_end;
            
            $data['implementer'] = $implementer;
            $data['userHead'] = $userHead;
            
        }
        
        if ($id > 0) {
            
            $template = "appeal_view_".$this->login_user['group_id'];
            $entries = [];
            $status = [];
            $statusSelect = [];
            $statuses = [];
            $user = [];
            $userManager = [];
            $userGroup = [];
            $userGroupName = [];
            $gallery = [];
            $appealDate = [];
            $appealWork = [];
            $appealUser = [];
            $appealRequest = [];
            $appealRequestType = [];
            $appealChat = [];
            $appealActive = 0;
            $appealDone = 0;
            
            $builder = $this->db->table($this->prefix.'status');
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $status[$item->id] = $item->title;
            }
            $statusSelect=$status;
            if($this->login_user['group_id']==1){
                $statusHidden = [1,3,5];
                foreach ($statusHidden as $value) {
                    unset($statusSelect[$value]);
                }
            }
            if($this->login_user['group_id']==2){
                $statusHidden = [1,2,3,4,5,6];
                foreach ($statusHidden as $value) {
                    unset($statusSelect[$value]);
                }
            }
            if($this->login_user['group_id']==3){
                $statusHidden = [1,2,3,5];
                foreach ($statusHidden as $value) {
                    unset($statusSelect[$value]);
                }
            }
            
            // Заявка
            $builder = $this->db->table($this->prefix.'appeal');
            $builder->where('id', $id);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ( $item as $key=>$value ){
                    $entries[$key] = $value;
                }
                if ($this->login_user['group_id']==1) {
                    $appealAccess = 0;
                }
                $entries['rating'] = 0;
            }
            
            if (isset($entries['id'])) {
                
                if($this->login_user['group_id']==1){
                    if($entries['status']==1){
                        $statusHidden = [4,6];
                    } else {
                        $statusHidden = [6,7];
                    }
                    foreach ($statusHidden as $value) {
                        unset($statusSelect[$value]);
                    }
                }
                
                $builder = $this->db->table($this->prefix.'appeal_rating');
                $builder->where('appeal_id', $entries['id']);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $entries['rating'] = $item->rating;
                }
                
                // Заявник
                $builder = $this->db->table($this->prefix.'bot_user');
                $builder->where('id', $entries['user_id']);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ( $item as $key=>$value ){
                        $user[$key] = $value;
                    }
                }
                
                if($submit=='Submit_add_status'){
                    
                    $notify = ( isset($_POST['notify']) AND $_POST['notify'] == 1 )?1:0;
                    $data['appeal_id'] = $this->request->getVar('id');
                    $data['status_id'] = $this->request->getVar('status');
                    $data['comment'] = $this->request->getVar('comment');
                    $data['date_add'] = time();
                    $data['notify'] = $notify;
                    $this->model->_insert($data,'appeal_status');

                    $data_update['status'] = $this->request->getVar('status');
                    $data_update['date_update'] = time();
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data_update,$where,'appeal');
                    
                    if($notify==1 AND $data['status_id']!=7){
                        
                        if(!empty($user['chat_id'])){
                            $message = "\xF0\x9F\x93\xA3 <strong>".lang('Bot.MessageAppealUpdateTitle')."</strong> \n\n";
                            $message .= lang('Bot.MessageAppeal')." #".$this->request->getVar('id')."\n";
                            $message .= lang('Bot.MessageNewStatus')." ".$status[$this->request->getVar('status')]."\n";
                            if(!empty($this->request->getVar('comment'))){
                                $message .= lang('Bot.MessageComment')." ".$this->request->getVar('comment')."\n";
                            }
                            if($data['status_id']==5){
                                $message .= '<a href="'.base_url().'/appeal/'.$this->request->getVar('id').'">'.lang('Bot.ReadMoreSite').'</a>';
                            }
                            
                            $button[] = [[ 'text' => lang('Bot.MessageViewAppeal'), 'callback_data'=>'/appeal_'.$this->request->getVar('id') ]];
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
                           
                            $message = "\xF0\x9F\x93\xA3 *".lang('Bot.MessageAppealUpdateTitle')."* \n\n";
                            $message .= lang('Bot.MessageAppeal')." #".$this->request->getVar('id')."\n";
                            $message .= lang('Bot.MessageNewStatus')." ".$status[$this->request->getVar('status')]."\n";
                            if(!empty($this->request->getVar('comment'))){
                                $message .= lang('Bot.MessageComment')." ".$this->request->getVar('comment')."\n";
                            }
                            
                            $linkToSite = [
                                "ActionType" => "open-url",
                                "ActionBody" => base_url()."/appeal/".$this->request->getVar('id'),
                                "Text" => "<b>".lang('Bot.ReadMoreSite')."</b>",
                                "Columns" => 6,
                                "Rows" => 1,
                                "BgColor" => $this->bgColor,
                            ];                            
                            $linkToAppeal = [
                                "ActionBody" => "/appeal_".$this->request->getVar('id'),
                                "Text" => "<b>".lang('Bot.MessageViewAppeal')."</b>",
                                "Columns" => 6,
                                "Rows" => 1,
                                "BgColor" => $this->bgColor,
                            ];
                            
                            if($data['status_id']==5){
                                $keyboard = [$linkToAppeal,$linkToSite];                                
                            } else {
                                $keyboard = [$linkToAppeal];
                            }
                            
                            $this->model->sendMessageViber($user['sender_id'], $message, "text", $keyboard);
                           
                        }
                    }
                    
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_add_status',[$this->request->getVar('id'),$this->request->getVar('status')]));
                    $this->email->sendInfo("add_status",$this->request->getVar('id'));
                    return redirect()->to( current_url(true) ); 
                        
                }
                
                
                if($submit=='Submit_add_date'){
                    $data['appeal_id'] = $this->request->getVar('id');
                    $data['date'] = strtotime($this->request->getVar('date'));
                    $data['comment'] = $this->request->getVar('comment');
                    $data['user_id'] = $this->login_user['id'];
                    $data['group_id'] = $this->login_user['group_id'];
                    $data['date_add'] = time();
                    if($this->login_user['group_id']==2){
                        $data['approved'] = 1;
                    }
                    $this->model->_insert($data,'appeal_date');
                    
                    if($this->login_user['group_id']==2){
                        $dataAppeal['date_appeal'] = strtotime($this->request->getVar('date'));
                        $where = "id = ".$this->request->getVar('id');;
                        $this->model->_update($dataAppeal,$where,'appeal');
                    }
                    
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_add_date',[$this->request->getVar('id'),$this->request->getVar('date')]));
                    $this->email->sendInfo("add_date",$this->request->getVar('id'));
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_approved_date'){
                    $builder = $this->db->table($this->prefix.'appeal_date');
                    $builder->where('id', $this->request->getVar('id'));
                    $query = $builder->get();
                    foreach ($query->getResult() as $item){
                        foreach ( $item as $key=>$value ){
                            $approvDate[$key] = $value;
                        }
                    }
                    $data['approved'] = 1;
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data,$where,'appeal_date');
                    
                    $dataAppeal['date_appeal'] = $approvDate['date'];
                    $where = "id = ".$approvDate['appeal_id'];
                    $this->model->_update($dataAppeal,$where,'appeal');
                    
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_approved_date',[$approvDate['appeal_id'],date("Y/m/d H:i",$approvDate['date'])]));
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_add_head'){
                    
                    if (isset($_POST['user'])) {
                        $usersIds = "";
                        $where = "appeal_id = ".$this->request->getVar('id');
                        $where .= " AND group_id = 2";
                        $this->model->_delete($where,'appeal_user');
                        foreach($_POST['user'] as $value){
                            $data = [
                                'appeal_id' => $this->request->getVar('id'),
                                'user_id' => $value,
                                'group_id' => 2,
                            ];
                            $this->model->_insert($data,'appeal_user');
                            $usersIds .= $value.", ";
                        }
                        $usersIds = trim($usersIds,", ");
                        $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_add_head',[$this->request->getVar('id'),$usersIds]));
                        $this->email->sendInfo("add_head",$this->request->getVar('id'));
                    } else {
                        $where = "appeal_id = ".$this->request->getVar('id');
                        $where .= " AND group_id = 2";
                        $this->model->_delete($where,'appeal_user');
                    }
                    return redirect()->to( current_url(true) );
                }

                if($submit=='Submit_add_implementer'){
                    
                    if (isset($_POST['user'])) {
                        $usersIds = "";
                        $where = "appeal_id = ".$this->request->getVar('id');
                        $where .= " AND group_id = 3";
                        $this->model->_delete($where,'appeal_user');
                        foreach($_POST['user'] as $value){
                            $data = [
                                'appeal_id' => $this->request->getVar('id'),
                                'comment' => $this->request->getVar('comment'),
                                'user_id' => $value,
                                'group_id' => 3,
                            ];
                            $this->model->_insert($data,'appeal_user');
                            $usersIds .= $value.", ";
                        }
                        $usersIds = trim($usersIds,", ");
                        $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_add_implementer',[$this->request->getVar('id'),$usersIds]));
                        $this->email->sendInfo("add_implementer",$this->request->getVar('id'));
                    } else {
                        $where = "appeal_id = ".$this->request->getVar('id');
                        $where .= " AND group_id = 3";
                        $this->model->_delete($where,'appeal_user');
                    }
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_add_implementer_user'){
                    $data_update['comment'] = $this->request->getVar('comment');
                    $data_update['comment_user'] = $this->request->getVar('comment_user');
                    $where = "appeal_id = ".$this->request->getVar('id');
                    $where .= " AND group_id = 3";
                    $this->model->_update($data_update,$where,'appeal_user');                   
                    return redirect()->to( current_url(true) );
                }
                
                
                if($submit=='Submit_appeal_request'){
                    $data = [
                        'appeal_id' => $this->request->getVar('id'),
                        'user_id' => $this->login_user['id'],
                        'date_add' => time(),
                        'comment' => $this->request->getVar('comment'),
                        'type' => $_POST['type'],
                    ];
                    $this->model->_insert($data,'appeal_request');
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_add',[$this->request->getVar('id'),$_POST['type']]));
                    $this->email->sendInfo("appeal_request",$this->request->getVar('id'));
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_appeal_request_approve'){
                    $data_update['approved'] = 1;
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data_update,$where,'appeal_request');
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_approve',[$id,$this->request->getVar('id')]));
                    $this->email->sendInfo("appeal_request_approve",$id);
                    return redirect()->to( current_url(true) );				
                }
                
                if($submit=='Submit_appeal_request_reject'){
                    $data_update['approved'] = 2;
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data_update,$where,'appeal_request');
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_reject',[$id,$this->request->getVar('id')]));
                    $this->email->sendInfo("appeal_request_reject",$id);
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_appeal_implementation'){
                    $data = [
                        'appeal_id' => $this->request->getVar('id'),
                        'user_id' => $this->login_user['id'],
                        'date_add' => time(),
                        'title' => $this->request->getVar('title'),
                        'comment' => $this->request->getVar('comment'),
                    ];
                    $appealImplementation = $this->model->_insert($data,'appeal_work');
                    
                    $statusId = 6;
                    $dataStatus['appeal_id'] = $this->request->getVar('id');
                    $dataStatus['status_id'] = $statusId;
                    $dataStatus['comment'] = "";
                    $dataStatus['date_add'] = time();
                    $dataStatus['notify'] = 0;
                    $this->model->_insert($dataStatus,'appeal_status');
                    $dataAppeal['status'] = $statusId;
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($dataAppeal,$where,'appeal');

                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_add_status',[$this->request->getVar('id'),$this->request->getVar('status')]));
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_implementation_add',[$this->request->getVar('id'),$appealImplementation]));
                    
                    $this->email->sendInfo("add_status",$this->request->getVar('id'));
                    
                    return redirect()->to( current_url(true) );
                }
                
                if($submit=='Submit_appeal_implementation_edit'){
                    $data = [
                        'title' => $this->request->getVar('title'),
                        'comment' => $this->request->getVar('comment'),
                    ];
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data,$where,'appeal_work');
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_implementation_edit',[$id,$this->request->getVar('id')]));
                    return redirect()->to( current_url(true) );
                }			
                
                if($submit=='Submit_appeal_chat'){
                    $data = [
                        'appeal_id' => $this->request->getVar('id'),
                        'user_id' => $this->login_user['id'],
                        'date_add' => time(),
                        'message' => $this->request->getVar('message'),
                        'who'=>0,
                    ];
                    $chatId = $this->model->_insert($data,'appeal_chat');
                    $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_chat_add',[$id,$chatId]));
                    
                    if(!empty($user['chat_id'])){ 

                        $message = "\xF0\x9F\x93\xA3 <strong>".lang('Bot.MessageAppealUpdateTitle')." # ".$this->request->getVar('id')."</strong> \n\n";
                        $message .= lang('Bot.MessageNewChat')."\n".$this->request->getVar('message')."\n\n";
                        $message .= lang('Bot.MessageNewChatHelp')."\n\n";
                        
                        $button[] = [[ 'text' => lang('Bot.MessageViewAppeal'), 'callback_data'=>'/appeal_'.$this->request->getVar('id') ]];
                        $button[] = [[ 'text' => lang('Bot.ButtonAppealChat'), 'callback_data'=>'/chat_'.$this->request->getVar('id') ]];
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
                    
                        $message = "\xF0\x9F\x93\xA3 *".lang('Bot.MessageAppealUpdateTitle')."* #".$this->request->getVar('id')." \n\n";
                        $message .= lang('Bot.MessageNewChat')."\n".$this->request->getVar('message')."\n\n";
                        $message .= lang('Bot.MessageNewChatHelp');
                        $keyboard = [
                            [
                                "ActionBody" => "/appeal_".$this->request->getVar('id'),
                                "Text" => "<b>".lang('Bot.MessageViewAppeal')."</b>",
                                "Columns" => 6,
                                "Rows" => 1,
                                "BgColor" => $this->bgColor,
                            ],
                            [
                                "ActionBody" => "/chat_".$this->request->getVar('id'),
                                "Text" => "<b>".lang('Bot.ButtonAppealChat')."</b>",
                                "Columns" => 6,
                                "Rows" => 1,
                                "BgColor" => $this->bgColor,
                            ],
                        ];
                        $this->model->sendMessageViber($user['sender_id'], $message, "text", $keyboard); 
                    
                    }
                    
                    return redirect()->to( current_url(true) );
                }
                
                // Галерея заявника
                $builder = $this->db->table($this->prefix.'appeal_gallery');
                $builder->where('appeal_id', $entries['id']);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ( $item as $key=>$value ){
                        $gallery[$item->id][$key] = $value;
                    }
                    $gallery[$item->id]['path'] = "assets/upload/appeal/".$item->appeal_id."/";
                } 
                
                // Статуси
                $builder = $this->db->table($this->prefix.'appeal_status');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key=>$value){
                        $statuses[$item->id][$key] = $value;
                    }
                    if($item->status_id==3){
                        $appealActive = 1;
                    }
                    if($item->status_id==5){
                        $appealDone = 1;
                    }
                }
                if( $appealActive==0 AND ( $this->login_user['group_id']==1 OR $this->login_user['group_id']==3) ){
                    $statusHidden = [4,6];
                    foreach ($statusHidden as $value) {
                        unset($statusSelect[$value]);
                    }
                }                
                
                // Заявка Дати
                $builder = $this->db->table($this->prefix.'appeal_date');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key=>$value){
                        $appealDate[$item->id][$key] = $value;
                    }
                }

                // Заявка Етапи Роботи
                $builder = $this->db->table($this->prefix.'appeal_work');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key=>$value){
                        $appealWork[$item->id][$key] = $value;
                    }
                }			

                // Заявка Приєднані Користувачі
                $builder = $this->db->table($this->prefix.'appeal_user');
                $builder->where('appeal_id', $entries['id']);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key=>$value){
                        $appealUser[$item->group_id][$item->user_id][$key] = $value;
                    }
                    if ($item->user_id == $this->login_user['id']) {
                        $appealAccess = 0;
                    }
                }
                
                // Менеджери
                $builder = $this->db->table($this->prefix.'user');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $userManager[$item->group_id][$item->id]['id'] = $item->id;
                    $userManager[$item->group_id][$item->id]['email'] = $item->email;
                    $userManager[$item->group_id][$item->id]['first_name'] = $item->first_name;
                    $userManager[$item->group_id][$item->id]['last_name'] = $item->last_name;
                    $userManager[$item->group_id][$item->id]['title'] = $item->title;
                    $userGroup[$item->id] = $item->group_id;
                }
                
                // Групи менеджерів
                $builder = $this->db->table($this->prefix.'user_group');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $userGroupName[$item->id][$key] = $value;
                    }
                }
                
                // Запити особам які приймють рішення
                $builder = $this->db->table($this->prefix.'appeal_request');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $appealRequest[$item->id][$key] = $value;
                    }
                }			

                // Типи запитів особам які приймють рішення
                $builder = $this->db->table($this->prefix.'appeal_request_type'); 
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $appealRequestType[$item->id][$key] = $value;
                    }
                }
                
                // Виконана робота
                $builder = $this->db->table($this->prefix.'appeal_work');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $appealWork[$item->id][$key] = $value;
                    }
                }
                $appealWorkLast = (count($appealWork)>0)?$appealWork[array_keys($appealWork)[count($appealWork)-1]]:[];
                
                // Чат
                $builder = $this->db->table($this->prefix.'appeal_chat');
                $builder->where('appeal_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $appealChat[$item->id][$key] = $value;
                    }
                }
                
                $data['gallery'] = $gallery;
                $data['user'] = $user;
                $data['status'] = $status;
                $data['statuses'] = $statuses;
                $data['statusSelect'] = $statusSelect;
                $data['entries'] = $entries;
                $data['pageTitle'] = $this->menu['appeal']['title'];
                
                $data['appealDate'] = $appealDate;
                $data['appealWork'] = $appealWork;
                $data['appealWorkLast'] = $appealWorkLast;
                $data['appealUser'] = $appealUser;
                $data['userManager'] = $userManager;
                $data['userGroup'] = $userGroup;
                $data['userGroupName'] = $userGroupName;
                $data['appealRequest'] = $appealRequest;
                $data['appealRequestType'] = $appealRequestType; 
                $data['appealChat'] = $appealChat; 
                $data['appealAccess'] = $appealAccess;
                $data['appealActive'] = $appealActive;
                $data['appealDone'] = $appealDone;
                
            } else {
                
                $appealAccess=1;
                
            }
        
        }		
        
        $data['managerUrl'] = $this->managerUrl;
        $this->menu['appeal']['class'] = "active";
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['loginUser'] = $this->login_user;
        $data['pageTitle'] = $this->menu['appeal']['title'];
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        if ( $appealAccess==1 ) {
            echo view('manager/appeal_not_view');
        } else {
            echo view('manager/'.$template,$data);
        }
        echo view('manager/_footer');
    }

    public function applicant($id=0)
    {
        
        $submit = $this->request->getVar('submit');
        $appealAccess = 1;
        
        if($id == 0){
            
            $uri = current_url(true);
            $uri_query = $uri->getQuery();
            $search = "";
            if(!empty($uri_query)){
                parse_str($uri_query, $uri_param);            
                $search = (isset($uri_param['s']))?$uri_param['s']:"";
            }                    
            
            $appealAccess = 0;
            $perPage = 20;
            $template = "applicant";
            
            $entries = array();
            $entries_ids = array();
            $artriesTotal = 0;
            if ($this->login_user['group_id']>1) {
                $builder = $this->db->table($this->prefix.'appeal_user');
                $builder->select($this->prefix.'appeal_user.appeal_id');
                $builder->join($this->prefix.'appeal', $this->prefix.'appeal.id = '.$this->prefix.'appeal_user.appeal_id');
                $builder->join($this->prefix.'bot_user', $this->prefix.'bot_user.id = '.$this->prefix.'appeal.user_id');
                $builder->where($this->prefix.'appeal_user.user_id', $this->login_user['id']);
            } else {
                $builder = $this->db->table($this->prefix.'bot_user');
                $builder->select('article.id');                
            }
            if ($search!="") {
                $builder->like('first_name', $search);
                $builder->orLike('last_name', $search);
                $builder->orLike('email', $search);
                $builder->orLike('phone', $search);
            }
            $artriesTotal = $builder->countAllResults();

            if ($this->login_user['group_id']>1) {
                $builder = $this->db->table($this->prefix.'appeal_user');
                $builder->select($this->prefix.'bot_user.*');
                $builder->join($this->prefix.'appeal', $this->prefix.'appeal.id = '.$this->prefix.'appeal_user.appeal_id');
                $builder->join($this->prefix.'bot_user', $this->prefix.'bot_user.id = '.$this->prefix.'appeal.user_id');
                $builder->where($this->prefix.'appeal_user.user_id', $this->login_user['id']);
            } else {
                $builder = $this->db->table($this->prefix.'bot_user');
                $builder->select('*');
            }
            if ($search!="") {
                $builder->like('first_name', $search);
                $builder->orLike('last_name', $search);
                $builder->orLike('email', $search);
                $builder->orLike('phone', $search);
            }
            
            $page = 0;
            $group = "default";
            $pager = \Config\Services::pager(null, null, false);
            $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
            $this->pager = $pager->store($group, $page, $perPage, $artriesTotal);
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage,$offset);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ($item as $key=>$value){
                    $entries[$item->id][$key] = $value;
                }
                $entries[$item->id]['appeals'] = 0;
                $entries_ids[] = $item->id;
            }
            
            $pagination = $this->pager;
            if($artriesTotal>$perPage){
                $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
            }else{
                $data['pagination'] = "";
            }
            
            if(count($entries_ids)>0){
                
                $builder = $this->db->table($this->prefix.'appeal');
                $builder->select( 'COUNT(id) as count, user_id' );
                $builder->groupBy('user_id');
                $builder->whereIn('user_id', $entries_ids);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $entries[$item->user_id]['appeals'] = $item->count;
                }
                
            }
            
            $data['entries'] = $entries;
            $data['artries_total'] = $artriesTotal;
            $data['pageTitle'] = $this->menu['applicant']['title'];
            $data['search'] = $search;
            
        }else{
            if ($this->login_user['group_id']==1) {
                $appealAccess = 0;
            } else {
                $appealAccess = 1;
            }
            $template = "applicant_view";
            $entries = array();
            $status = array();
            $appeal = array();
            $appealTotal = 0;
            $message = array();
            $messageTotal = 0;
            
            $builder = $this->db->table($this->prefix.'bot_user');
            $builder->where('id', $id);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ( $item as $key=>$value ){
                    $entries[$key] = $value;
                }
            }
            
            if (isset($entries['id'])) {
            
                if($submit=='Submit_add_message'){
                        
                    $data['user_id'] = $this->request->getVar('id');
                    $data['message'] = $this->request->getVar('message');
                    $data['date_add'] = time();
                    
                    if(!empty($this->request->getVar('message'))){
                        
                        $message = $this->request->getVar('message')."\n\n".lang('Bot.MessageNotNeedAnswer');
                        if(!empty($entries['chat_id'])){
                            $button[] = [[ 'text' => lang('Bot.ButtonBackToStart'), 'callback_data'=>'/start']];
                            $keyboard = [
                                'inline_keyboard' => $button,
                                'one_time_keyboard' => true,
                                'resize_keyboard' => true
                            ];
                            
                            $dataMessage = [
                                'chat_id' => $entries['chat_id'],
                                'method' => 'sendMessage',
                                'message' => $message,
                                'reply_markup' => json_encode($keyboard),
                            ];                            
                            
                            $this->model->sendMessage($dataMessage);
                        }
                        if(!empty($entries['sender_id'])){
                            $keyboard = [
                                [
                                    "ActionBody" => "/start",
                                    "Text" => "<b>".lang('Bot.ButtonBackToStart')."</b>",
                                    "Columns" => 6,
                                    "Rows" => 1,
                                    "BgColor" => $this->bgColor,
                                ],
                            ];
                            $this->model->sendMessageViber($entries['sender_id'], $message, "text", $keyboard);
                           
                        }
                        $messageId = $this->model->_insert($data,'message');
                        $this->model->saveLlog($this->login_user['id'],lang('Log.chat_message_add',[$this->request->getVar('id'),$messageId]));
                    }                    
                    return redirect()->to( current_url(true) );
                }			
    
                $builder = $this->db->table($this->prefix.'appeal'); 
                $builder->select($this->prefix.'appeal.*');
                $builder->select($this->prefix.'appeal.*, '.$this->prefix.'appeal_date.date as appeal_date, '.$this->prefix.'appeal_date.approved as appeal_date_approved, ');
                $builder->join($this->prefix.'appeal_date', $this->prefix.'appeal_date.appeal_id = '.$this->prefix.'appeal.id', 'left');
                if ($this->login_user['group_id']>1) {
                    $builder->join($this->prefix.'appeal_user', $this->prefix.'appeal_user.appeal_id = '.$this->prefix.'appeal.id' );
                }
                $builder->where($this->prefix.'appeal.user_id', $entries['id']);
                $builder->where($this->prefix.'appeal.status > ', 0);
                $builder->orderBy($this->prefix.'appeal.date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ( $item as $key=>$value ){
                        $appeal[$item->id][$key] = $value;
                    }
                }
            
                $builder = $this->db->table($this->prefix.'appeal');
                $builder->select($this->prefix.'appeal.id');
                if ($this->login_user['group_id']>1) {
                    $builder->join($this->prefix.'appeal_user', $this->prefix.'appeal_user.appeal_id = '.$this->prefix.'appeal.id' );
                }
                $builder->where($this->prefix.'appeal.user_id', $entries['id']);
                $builder->where($this->prefix.'appeal.status > ', 0);
                $appealTotal = $builder->countAllResults();
                if ($appealTotal>0){
                    $appealAccess=0;
                }
    
                $builder = $this->db->table($this->prefix.'message');
                $builder->where('user_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');
                $builder->limit(10);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ( $item as $key=>$value ){
                        $message[$item->id][$key] = $value;
                    }
                }
            
                $builder = $this->db->table($this->prefix.'message');
                $builder->where('user_id', $entries['id']);
                $builder->select('article.id');
                $messageTotal = $builder->countAllResults();
                
                $builder = $this->db->table($this->prefix.'status');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $status[$item->id] = $item->title;
                }                
            }
            
            $data['entries'] = $entries;
            $data['appeal'] = $appeal;
            $data['appealTotal'] = $appealTotal;
            $data['message'] = $message;
            $data['messageTotal'] = $messageTotal;
            $data['status'] = $status;
            $data['pageTitle'] = lang('Manager.applicant');
            
            
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $this->menu['applicant']['class'] = "active";
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['loginUser'] = $this->login_user;
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        if ( $appealAccess==1 ) {
            echo view('manager/applicant_not_view');
        } else {
            echo view('manager/'.$template,$data);
        }
        echo view('manager/_footer');
    }
    
    public function message($id=0)
    {
        
        $submit = $this->request->getVar('submit');
        $appealAccess = 0;
        
        if($id > 0){
            
            if ($this->login_user['group_id']==1) {
                $appealAccess = 0;
            } else {
                $appealAccess = 1;
            }
            
            $template = "applicant_message";
            $entries = array();
            $status = array();
            $appeal = array();
            $appealTotal = 0;
            $message = array();
            $messageTotal = 0;
            
            $builder = $this->db->table($this->prefix.'bot_user');
            $builder->where('id', $id);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ( $item as $key=>$value ){
                    $entries[$key] = $value;
                }
            }
            
            if (isset($entries['id'])) {
                $appealAccess=1;
                if($submit=='Submit_add_message'){
                        
                    $data['user_id'] = $this->request->getVar('id');
                    $data['message'] = $this->request->getVar('message');
                    $data['date_add'] = time();
                    
                    if(!empty($this->request->getVar('message'))){
                        
                        $message = $this->request->getVar('message')."\n\n".lang('Bot.MessageNotNeedAnswer');
                        if(!empty($entries['chat_id'])){
                            $button[] = [[ 'text' => lang('Bot.ButtonBackToStart'), 'callback_data'=>'/start']];
                            $keyboard = [
                                'inline_keyboard' => $button,
                                'one_time_keyboard' => true,
                                'resize_keyboard' => true
                            ];
                            
                            $dataMessage = [
                                'chat_id' => $entries['chat_id'],
                                'method' => 'sendMessage',
                                'message' => $message,
                                'reply_markup' => json_encode($keyboard),
                            ];                            
                            
                            $this->model->sendMessage($dataMessage);
                        }
                        if(!empty($entries['sender_id'])){
                            $keyboard = [
                                [
                                    "ActionBody" => "/start",
                                    "Text" => "<b>".lang('Bot.ButtonBackToStart')."</b>",
                                    "Columns" => 6,
                                    "Rows" => 1,
                                    "BgColor" => $this->bgColor,
                                ],
                            ];
                            $this->model->sendMessageViber($entries['sender_id'], $message, "text", $keyboard);
                           
                        }
                        $messageId = $this->model->_insert($data,'message');
                        $this->model->saveLlog($this->login_user['id'],lang('Log.chat_message_add',[$this->request->getVar('id'),$messageId]));
                    }                    
                    return redirect()->to( current_url(true) );
                }			
    
                $perPage = 20;
                
                $builder = $this->db->table($this->prefix.'message');
                $builder->where('user_id', $entries['id']);
                $builder->select('id');
                $messageTotal = $builder->countAllResults();    
                
                $builder = $this->db->table($this->prefix.'message');
                $builder->where('user_id', $entries['id']);
                $builder->orderBy('date_add', 'DESC');

                $page = 0;
                $group = "default";
                $pager = \Config\Services::pager(null, null, false);
                $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
                $this->pager = $pager->store($group, $page, $perPage, $messageTotal);
                $offset = ($page - 1) * $perPage;
                $builder->limit($perPage,$offset);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ( $item as $key=>$value ){
                        $message[$item->id][$key] = $value;
                    }
                }
                
                $pagination = $this->pager;
                if($messageTotal>$perPage){
                    $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
                }else{
                    $data['pagination'] = "";
                }
                
                $builder = $this->db->table($this->prefix.'status');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $status[$item->id] = $item->title;
                }                
            }
            
            $data['entries'] = $entries;
            $data['message'] = $message;
            $data['messageTotal'] = $messageTotal;
            $data['status'] = $status;
            $data['pageTitle'] = lang('Manager.applicant');
            $this->menu['applicant']['class'] = "active";
            
        } else {
            
            if($submit=='Submit_add_package'){
                if(!empty($this->request->getVar('message'))){
                    
                    $userIds = [];
                    $builder = $this->db->table($this->prefix.'bot_user');
                    $totalUsers = $builder->countAllResults();
                    
                    $builder = $this->db->table($this->prefix.'bot_user');
                    $query = $builder->get();
                    foreach ($query->getResult() as $item){
                        $userIds[] = $item->id;
                    }

                    $data['message'] = $this->request->getVar('message');
                    $data['date_add'] = time();
                    $data['users'] = $totalUsers;
                    $packageId = $this->model->_insert($data,'message_package');
                    
                    foreach($userIds as $value){
                        $dataNeed['user_id'] = $value;
                        $dataNeed['package_id'] = $packageId;
                        $this->model->_insert($dataNeed,'message_needsend');
                    }
                    
                    $this->model->saveLlog($this->login_user['id'],lang('Log.message_package_add',[$packageId]));
                }                     
                return redirect()->to( current_url(true) );
            }
            
            if($submit=='Submit_del'){
                if(!empty($this->request->getVar('id'))){
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_delete($where,'message_package');
                    $where = "package_id = ".$this->request->getVar('id');
                    $this->model->_delete($where,'message_needsend');
                }                     
                return redirect()->to( current_url(true) );
            }

            if($submit=='Submit_edit'){
                if(!empty($this->request->getVar('id')) && !empty($this->request->getVar('message'))){
                    $data['message'] = $this->request->getVar('message');
                    $where = "id = ".$this->request->getVar('id');
                    $this->model->_update($data,$where,'message_package');
                }                     
                return redirect()->to( current_url(true) );
            }
            
            if($submit=='Submit_start'){
                $data['status'] = 1;
                $where = "id = ".$this->request->getVar('id');
                $this->model->_update($data,$where,'message_package');
                return redirect()->to( current_url(true) );
            }
            
            
            $appealAccess=1;
            $template = "message_package";
            $entries = ['id'=>'0'];
            $messagePackage = [];
            $perPage = 20;
            
            $builder = $this->db->table($this->prefix.'message_package');
            $builder->select('id');
            $messageTotal = $builder->countAllResults();    
            
            $builder = $this->db->table($this->prefix.'message_package');
            $builder->orderBy('date_add', 'DESC');

            $page = 0;
            $group = "default";
            $pager = \Config\Services::pager(null, null, false);
            $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
            $this->pager = $pager->store($group, $page, $perPage, $messageTotal);
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage,$offset);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ( $item as $key=>$value ){
                    $messagePackage[$item->id][$key] = $value;
                }
            }
            
            $pagination = $this->pager;
            if($messageTotal>$perPage){
                $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
            }else{
                $data['pagination'] = "";
            }            
            
            $data['messagePackage'] = $messagePackage;
            $data['messageTotal'] = $messageTotal;
            $data['pageTitle'] = lang('Manager.message_package_title');
            $this->menu['message']['class'] = "active";
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['loginUser'] = $this->login_user;
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        if ( $appealAccess==0 || !isset($entries['id'])) {
            echo view('manager/applicant_not_view');
        } else {
            echo view('manager/'.$template,$data);
        }
        echo view('manager/_footer');
    }    
    
    public function statuses()
    {
                
        $submit = $this->request->getVar('submit');
        if ($submit=='Submit_add') {
            $data['title'] = $this->request->getVar('title');
            $statusId = $this->model->_insert($data,'status');
            $this->model->saveLlog($this->login_user['id'],lang('Log.status_add',[$statusId,$this->request->getVar('title')]));
            return redirect()->to( current_url(true) );		
        }
        
        if ($submit=='Submit_edit') {
            $data['title'] = $this->request->getVar('title');
            $where = "id = ".$this->request->getVar('id');
            $this->model->_update($data,$where,'status');
            $this->model->saveLlog($this->login_user['id'],lang('Log.status_update',[$this->request->getVar('id'),$this->request->getVar('title')]));
            return redirect()->to( current_url(true) ); 
        }
        
        if ($submit=='Submit_del') {
            $where = "id = ".$this->request->getVar('id');
            $this->model->_delete($where,'status');
            $this->model->saveLlog($this->login_user['id'],lang('Log.status_del',[$this->request->getVar('id')]));
            return redirect()->to( current_url(true) );
        }
        
        $data['pageTitle'] = $this->menu['library']['childs']['statuses']['title'];
            
        $builder = $this->db->table($this->prefix.'status');
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ($item as $key => $value) {
                $status[$item->id][$key] = $value;
            }
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $data['status'] = $status;
        $data['loginUser'] = $this->login_user;
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        echo view('manager/library',$data);
        echo view('manager/_footer');
        
    }
    
    public function appealRequest()
    {
                
        $submit = $this->request->getVar('submit');
        if ($submit=='Submit_add') {
            $data['title'] = $this->request->getVar('title');
            $statusId = $this->model->_insert($data,'appeal_request_type');
            $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_type_add',[$statusId,$this->request->getVar('title')]));
            return redirect()->to( current_url(true) );		
        }
        
        if ($submit=='Submit_edit') {
            $data['title'] = $this->request->getVar('title');
            $data['comment'] = $this->request->getVar('comment');
            $where = "id = ".$this->request->getVar('id');
            $this->model->_update($data,$where,'appeal_request_type');
            $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_type_update',[$this->request->getVar('id'),$this->request->getVar('title')]));
            return redirect()->to( current_url(true) ); 
        }
        
        if ($submit=='Submit_del') {
            $where = "id = ".$this->request->getVar('id');
            $this->model->_delete($where,'appeal_request_type');
            $this->model->saveLlog($this->login_user['id'],lang('Log.appeal_request_type_del',[$this->request->getVar('id')]));
            return redirect()->to( current_url(true) );
        }
        
        $data['pageTitle'] = $this->menu['library']['childs']['appeal-request']['title'];
        
        $requestType = [];
        $builder = $this->db->table($this->prefix.'appeal_request_type');
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ($item as $key => $value) {
                $requestType[$item->id][$key] = $value;
            }
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $data['status'] = $requestType;
        $data['loginUser'] = $this->login_user;
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        echo view('manager/library',$data);
        echo view('manager/_footer');
        
    }
    
    public function users($id=0)
    {
        $session = session();
        $alert = (isset($_SESSION["alert"])) ? $_SESSION["alert"] : "" ;
        $user = [];
        $userGroup = [0=>['id' => '0','title' => lang('Manager.user_group_not_selected'),'slug' => '']];
        $activeGroup = 0;
        
        $submit = $this->request->getVar('submit');
        if ($submit=='Submit_add') {
            if ( $this->request->getVar('password')==$this->request->getVar('password_repeat') ) {
                $data['pass'] = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
                $data['email'] = $this->request->getVar('email');
                $data['date_add'] = time();
                $userId = $this->model->_insert($data,'user');
                $this->model->saveLlog($this->login_user['id'],lang('Log.user_add',[$userId,$this->request->getVar('email')]));
                return redirect()->to( current_url(true)."/".$userId );
            }
            return redirect()->to( current_url(true) );
        }
        
        if ($submit=='Submit_edit') {
            
            if ( !empty($this->request->getVar('password')) ) {
                if ( $this->request->getVar('password')==$this->request->getVar('password_repeat') ) {
                    $data['pass'] = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
                }else{
                    $session->setFlashdata('alert', 'enter_password_repeat');
                }
            }
            
            $data['email'] = $this->request->getVar('email');
            $data['phone'] = $this->request->getVar('phone');
            $data['first_name'] = $this->request->getVar('first_name');
            $data['last_name'] = $this->request->getVar('last_name');
            $data['group_id'] = $this->request->getVar('group');
            $data['title'] = $this->request->getVar('title');
            $data['description'] = $this->request->getVar('description');
            $data['comment'] = $this->request->getVar('comment');
            $data['email_send'] = $this->request->getVar('email_send');
            $where = "id = ".$this->request->getVar('id');
            $this->model->_update($data,$where,'user');
            $this->model->saveLlog($this->login_user['id'],lang('Log.user_update',[$this->request->getVar('id'),$this->request->getVar('email')]));
            return redirect()->to( current_url(true) ); 
        }
        
        if ($submit=='Submit_del') {
            $where = "id = ".$this->request->getVar('id');
            $this->model->_delete($where,'user');
            $where = "user_id = ".$this->request->getVar('id');
            $this->model->_delete($where,'user_log');
            $this->model->_delete($where,'user_login');
            
            $this->model->saveLlog($this->login_user['id'],lang('Log.user_del',[$this->request->getVar('id')]));
            return redirect()->to( '/'.$this->managerUrl.'/users' );
        }

        if (isset($this->queryUrl['g'])) {
            $activeGroup = $this->queryUrl['g'];
        }
        
        // групи
        $builder = $this->db->table($this->prefix.'user_group');
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ($item as $key => $value) {
                $userGroup[$item->id][$key] = $value;
            }
        }
        
        $builder = $this->db->table($this->prefix.'user');
        if ($id>0) {
            $builder->where('id', $id);
        } elseif ($activeGroup>0) {
            $builder->where('group_id', $activeGroup);
        }
        
        $builder->orderBy('id', 'ASC');
        $query = $builder->get();
        foreach ($query->getResult() as $item) {
            foreach ($item as $key => $value) {
                $user[$item->id][$key] = $value;
            }
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $data['userGroup'] = $userGroup;
        $data['activeGroup'] = $activeGroup;
        $data['user'] = $user;
        $data['id'] = $id;
        $data['alert'] = $alert;
        
    
        $data['loginUser'] = $this->login_user;
        $data['pageTitle'] = $this->menu['library']['childs']['users']['title'];
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        
        echo view('manager/_header',$data);
        if ($id>0) {
            echo view('manager/users_view',$data);
        }else{
            echo view('manager/users',$data);
        }
        echo view('manager/_footer');
    
    }

    public function logs($id=0)
    {
        $user = [];
        $logs = [];
        $logIds = [];
        $userLogIds = [];
        if ($id==0) {
            $builder = $this->db->table($this->prefix.'user');
            $builder->orderBy('id', 'ASC');
            $query = $builder->get();
            foreach ($query->getResult() as $item) {
                foreach ($item as $key => $value) {
                    $logs[$item->id][$key] = $value;
                }
                $logIds[] = $item->id;
                $logs[$item->id]['log'] = "";
                $logs[$item->id]['date_log'] = 0;
            }
            if (count($logIds)>0) {
                $builder = $this->db->table($this->prefix.'user_log');
                $builder->select('max(id) as id');
                $builder->whereIn("user_id",$logIds);
                $builder->groupBy("user_id");
                $query = $builder->get();
                foreach ($query->getResult() as $item) {
                    $userLogIds[] = $item->id;
                }
                $builder = $this->db->table($this->prefix.'user_log');
                $builder->whereIn("id",$userLogIds);
                $query = $builder->get();
                foreach ($query->getResult() as $item) {
                    $logs[$item->user_id]['log'] = $item->log;
                    $logs[$item->user_id]['date_log'] = $item->date_add;
                }				
            }
            
        } else {
            
            $builder = $this->db->table($this->prefix.'user');
            $builder->where('id', $id);
            $query = $builder->get();
            foreach ($query->getResult() as $item) {
                foreach ($item as $key => $value) {
                    $user[$key] = $value;
                }
            }
            
            $perPage = 20;
            
            $logs = array();
            $logsTotal = 0;
            
            $builder = $this->db->table($this->prefix.'user_log');
            $builder->where('user_id', $id);
            $logsTotal = $builder->countAllResults();
            
            $builder = $this->db->table($this->prefix.'user_log');
            $builder->where('user_id', $id);
            $builder->orderBy('date_add', 'DESC');
            
            $page = 0;
            $group = "default";
            $pager = \Config\Services::pager(null, null, false);
            $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
            $this->pager = $pager->store($group, $page, $perPage, $logsTotal);
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage,$offset);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                foreach ($item as $key=>$value){
                    $logs[$item->id][$key] = $value;
                }
            }
            
            $pagination = $this->pager;
            if($logsTotal>$perPage){
                $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
            }else{
                $data['pagination'] = "";
            }
            $data['logsTotal'] = $logsTotal;
            
        }

        $data['loginUser'] = $this->login_user;
        $data['managerUrl'] = $this->managerUrl;
        $data['pageTitle'] = lang('Manager.log_user');
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        $data['logs'] = $logs;
        $data['user'] = $user;
        
        echo view('manager/_header',$data);
        if ($id>0) {
            echo view('manager/logs_user',$data);
        }else{
            echo view('manager/logs',$data);
        }
        echo view('manager/_footer');
        
    }
    
    public function statistic()
    {
        
        $entryAllIds = [];
        $entryIds = [];
        $entries = [];
        $rating = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, 'total' => 0,];
        $artriesTotal = 0;
        $perPage = 20;
        $status[0] = lang('Manager.status_leave');
        $status = [];
        $statusStat[0] = 0;
        $date_start = strtotime('first day of this month 00:00:01', time());
        $date_end = strtotime('last day of this month 23:59:59', time());
        $statusQuery = "notselect";
        $ratingQuery = "notselect";
        $implementerQuery = "notselect";
        $userHeadQuery = "notselect";
        $overdueQuery = 0;        
		$uri = current_url(true);
		$uriQuery = $uri->getQuery();
        
        if(!empty($uriQuery)){
            parse_str($uriQuery, $uri_param);            
            $date_start = (isset($uri_param['start']))?strtotime($uri_param['start']." 00:00:01"):$date_start;
            $date_end = (isset($uri_param['end']))?strtotime($uri_param['end']." 23:59:59"):$date_end;
            $statusQuery = (isset($uri_param['status']))?$uri_param['status']:$statusQuery;
            $ratingQuery = (isset($uri_param['rating']))?$uri_param['rating']:$ratingQuery;
            $implementerQuery = (isset($uri_param['implementer']))?$uri_param['implementer']:$implementerQuery;
            $userHeadQuery = (isset($uri_param['user_head']))?$uri_param['user_head']:$userHeadQuery;
            $overdueQuery = (isset($uri_param['overdue']))?$uri_param['overdue']:$overdueQuery;
        }

        // User Head
        $userHead = [];
        $builder = $this->db->table($this->prefix.'user');
        $query = $builder->where('group_id',2);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $userHead[$item->id] = [
                'name' => $item->first_name." ".$item->last_name,
                'title' => $item->title,
            ];
        }        
        
        // Status Implementer
        $implementer = [];
        $builder = $this->db->table($this->prefix.'user');
        $query = $builder->where('group_id',3);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $implementer[$item->id] = [
                'name' => $item->first_name." ".$item->last_name,
                'title' => $item->title,
            ];
        }
 
        // Status Title
        $builder = $this->db->table($this->prefix.'status');
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $status[$item->id] = $item->title;
            $statusStat[$item->id] = 0;
        }
        unset($status[3]);

        // All Stat
        $artriesTotalAll = 0;
        $artriesInWorkAll = 0;
        $artriesDoneAll = 0;
        
        $builder = $this->db->table($this->prefix.'appeal');
        $builder->select($this->prefix.'appeal.id');
        $builder->where($this->prefix.'appeal.status > 0');
        $artriesTotalAll = $builder->countAllResults();

        $builder = $this->db->table($this->prefix.'appeal');
        $builder->select($this->prefix.'appeal.id, '.$this->prefix.'appeal.status');
        $builder->where($this->prefix.'appeal.status = 4');
        $artriesInWorkAll = $builder->countAllResults();

        $builder = $this->db->table($this->prefix.'appeal');
        $builder->select($this->prefix.'appeal.id, '.$this->prefix.'appeal.status');
        $builder->where($this->prefix.'appeal.status = 5');
        $artriesDoneAll = $builder->countAllResults();

        // Entries Date
        $builder = $this->db->table($this->prefix.'appeal');
        $builder->select($this->prefix.'appeal.id, '.$this->prefix.'appeal.status');
        $builder->where($this->prefix.'appeal.date_add >', $date_start);
        $builder->where($this->prefix.'appeal.date_add <', $date_end);
        $builder->where($this->prefix.'appeal.status > 0');
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $artriesTotal++;
            $statusStat[$item->status]++;
            $entryAllIds[] = $item->id;
        }
        if(count($entryAllIds)>0){
            $builder = $this->db->table($this->prefix.'appeal_rating');
            $builder->whereIn('appeal_id', $entryAllIds);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $rating['total']++;
                $rating[$item->rating]++;
            }            
        }
        
        // Entries Filter
        $builder = $this->db->table($this->prefix.'appeal');
        $builder->select($this->prefix.'appeal.id, '.$this->prefix.'appeal.status');
        $builder->where($this->prefix.'appeal.date_add >', $date_start);
        $builder->where($this->prefix.'appeal.date_add <', $date_end);        
        if(is_numeric($statusQuery)){
            $builder->where($this->prefix.'appeal.status', $statusQuery);
        }else{
            $builder->where($this->prefix.'appeal.status > 0');
        }
        if(is_numeric($ratingQuery)){
            $builder->join($this->prefix.'appeal_rating', $this->prefix.'appeal_rating.appeal_id = '.$this->prefix.'appeal.id', 'left');
            if($ratingQuery<6){
                $builder->where($this->prefix.'appeal_rating.rating', $ratingQuery);
            } else {
                $builder->where($this->prefix.'appeal_rating.rating > 0');
            }
        }
        
        if(is_numeric($implementerQuery)){
            $builder->join($this->prefix.'appeal_user as u1', 'u1.appeal_id = '.$this->prefix.'appeal.id');
            $builder->where('u1.user_id', $implementerQuery);
        }            

        if(is_numeric($userHeadQuery)){
            $builder->join($this->prefix.'appeal_user as u2', 'u2.appeal_id = '.$this->prefix.'appeal.id');
            $builder->where('u2.user_id', $userHeadQuery);
        }
        
        if($overdueQuery==1){
            if(is_numeric($statusQuery)){
                if($statusQuery==5){
                    $builder->where('FROM_UNIXTIME ('.$this->prefix.'appeal.date_appeal, "%Y%m%d") < ', 'FROM_UNIXTIME ('.$this->prefix.'appeal.date_update , "%Y%m%d")');
                }else{
                    $builder->where($this->prefix.'appeal.date_appeal < ', mktime(23,59,59,date("m",time()),date("d",time()),date("Y",time())) );
                }
            }
        }        
        
        $artriesTotalFilter = $builder->countAllResults();
        
         
        $builder = $this->db->table($this->prefix.'appeal'); 
        $select = $this->prefix.'appeal.*';        
        $builder->select($select);
        $builder->where($this->prefix.'appeal.date_add >', $date_start);
        $builder->where($this->prefix.'appeal.date_add <', $date_end);
        
        if(is_numeric($statusQuery)){
            $builder->where($this->prefix.'appeal.status', $statusQuery);
        }else{
            $builder->where($this->prefix.'appeal.status > 0');
        }
        if(is_numeric($ratingQuery)){
            $builder->join($this->prefix.'appeal_rating', $this->prefix.'appeal_rating.appeal_id = '.$this->prefix.'appeal.id', 'left');
            if($ratingQuery<6){
                $builder->where($this->prefix.'appeal_rating.rating', $ratingQuery);
            } else {
                $builder->where($this->prefix.'appeal_rating.rating > 0');
            }
        }

        if(is_numeric($implementerQuery)){
            $builder->join($this->prefix.'appeal_user as u1', 'u1.appeal_id = '.$this->prefix.'appeal.id');
            $builder->where('u1.user_id', $implementerQuery);
        }            

        if(is_numeric($userHeadQuery)){
            $builder->join($this->prefix.'appeal_user as u2', 'u2.appeal_id = '.$this->prefix.'appeal.id');
            $builder->where('u2.user_id', $userHeadQuery);
        }
        
        if($overdueQuery==1){
            if(is_numeric($statusQuery)){
                if($statusQuery==5){
                    $builder->where('FROM_UNIXTIME ('.$this->prefix.'appeal.date_appeal, "%Y%m%d") < ', 'FROM_UNIXTIME ('.$this->prefix.'appeal.date_update , "%Y%m%d")');
                }else{
                    $builder->where($this->prefix.'appeal.date_appeal < ', mktime(23,59,59,date("m",time()),date("d",time()),date("Y",time())) );
                }
            }
        }
          
        $builder->orderBy('date_add', 'DESC');
            
        $page = 0;
        $group = "default";
        $pager = \Config\Services::pager(null, null, false);
        $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
        $this->pager = $pager->store($group, $page, $perPage, $artriesTotalFilter);
        $offset = ($page - 1) * $perPage;
        $builder->limit($perPage,$offset);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            foreach ($item as $key=>$value){
                if ( $key=="content" ) {
                    $value = mb_strimwidth($value, 0, 300, "...");
                }
                $entries[$item->id][$key] = $value;
            }
            $entries[$item->id]['appeal_date'] = null;
            $entries[$item->id]['appeal_date_approved'] = null;
            $entries[$item->id]['appeals'] = 0;
            $entries[$item->id]['rating'] = 0;
            $entries[$item->id]['implementer'] = [];
            $entryIds[] = $item->id;
        }
            
        $pagination = $this->pager;
        if($artriesTotalFilter>$perPage){
            $data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
        }else{
            $data['pagination'] = "";
        }
        
        if(count($entryIds)>0){

            $builder = $this->db->table($this->prefix.'appeal_date');
            $builder->whereIn('appeal_id', $entryIds);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $entries[$item->appeal_id]['appeal_date'] = $item->date;
                $entries[$item->appeal_id]['appeal_date_approved'] = $item->approved;
            }
            
            $builder = $this->db->table($this->prefix.'appeal_rating');
            $builder->whereIn('appeal_id', $entryIds);
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                $entries[$item->appeal_id]['rating'] = $item->rating;
            }
            
            $builder = $this->db->table($this->prefix.'appeal_user');
            $builder->whereIn('appeal_id', $entryIds);
            $builder->join($this->prefix.'user', $this->prefix.'user.id = '.$this->prefix.'appeal_user.user_id');
            $query = $builder->get();
            foreach ($query->getResult() as $item){
                if($item->group_id==3){
                    $entries[$item->appeal_id]['implementer'][] = [
                        'user_id' => $item->user_id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'title' => $item->title,
                        'last_name' => $item->last_name,
                        'comment' => $item->comment,
                        'comment_user' => $item->comment_user,
                    ];
                }
            }             
            
        }
        
        $data['managerUrl'] = $this->managerUrl;
        $this->menu['statistic']['class'] = "active";
        $data['loginUser'] = $this->login_user;
        $data['pageTitleSeo'] = lang('Manager.title_main');
        $data['headerView'] = "view";
        $data['pageTitle'] = $this->menu['statistic']['title'];
        $data['time'] = $this->time;
        $data['menu'] = $this->menu;
        $data['statusQuery'] = $statusQuery;
        $data['ratingQuery'] = $ratingQuery;
        $data['implementerQuery'] = $implementerQuery;
        $data['userHeadQuery'] = $userHeadQuery;
        $data['overdueQuery'] = $overdueQuery;
        
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['entries'] = $entries;
        $data['artriesTotal'] = $artriesTotal;
        $data['artriesTotalFilter'] = $artriesTotalFilter;
        $data['status'] = $status;
        $data['statusStat'] = $statusStat;
        $data['rating'] = $rating;
        
        $data['artriesTotalAll'] = $artriesTotalAll;
        $data['artriesInWorkAll'] = $artriesInWorkAll;
        $data['artriesDoneAll'] = $artriesDoneAll;
        
        $data['implementer'] = $implementer;
        $data['userHead'] = $userHead;
        
        $data['uriQuery'] = $uriQuery;
        
        echo view('manager/_header',$data);
        echo view('manager/stat',$data);
        echo view('manager/_footer');
    }
 
}
