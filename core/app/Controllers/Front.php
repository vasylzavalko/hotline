<?php

namespace App\Controllers;

class Front extends BaseController
{
    
    public function __construct()
    {
        
		$this->db = \Config\Database::connect();
        $this->prefix = $_ENV['DataBasePrefix'];
		$this->time = time();
    }
    
	public function index()
	{
        $data['time']=$this->time;
        $data['pageTitleSeo'] = lang('Front.title_main');
        $data['page'] = "home";
        
		echo view('front/_header',$data);
		echo view('front/home',$data);
		echo view('front/_footer');
	}
    
	public function appeal($id=0)
	{
        
        $appeal = [];
        $status = [];
        $perPage = 10;
        $appealTotal = 0;

        $builder = $this->db->table($this->prefix.'status');
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $status[$item->id] = $item->title;
        }

        if ($id==0) {
            
            $builder = $this->db->table($this->prefix.'appeal');
            $builder->select('id');
            $builder->where('status > 2');
            $appealTotal = $builder->countAllResults();
            $appealIds = [];
            
			$builder = $this->db->table($this->prefix.'appeal'); 
			$builder->where('status > 2');
			$builder->orderBy('date_add', 'DESC');
			
			$page = 0;
			$group = "default";
			$pager = \Config\Services::pager(null, null, false);
			$page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
			$this->pager = $pager->store($group, $page, $perPage, $appealTotal);
			$offset = ($page - 1) * $perPage;
			$builder->limit($perPage,$offset);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
				foreach ($item as $key=>$value){
					$appeal[$item->id][$key] = $value;
                    $appeal[$item->id]['rating'] = 0;
                    $appealIds[] = $item->id;
				}
			}
			$pagination = $this->pager;
			if($appealTotal>$perPage){
				$data['pagination'] = preg_replace('/\?page=1\b/','', $pagination->links());
			}else{
				$data['pagination'] = "";
			}
            
            if(!empty($appealIds)){
                $builder = $this->db->table($this->prefix.'appeal_rating'); 
                $builder->whereIn('appeal_id', $appealIds);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $appeal[$item->appeal_id]['rating'] = $item->rating;
                }
            }
            
        } else {
            
			$builder = $this->db->table($this->prefix.'appeal'); 
			$builder->where('id', $id);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
				foreach ($item as $key=>$value){
					$appeal[$key] = $value;
				}
                $appeal['rating'] = 0;
                $appeal['date'] = 0;
                $appeal['date_comment'] = "";
                $appeal['gallery'] = [];
                $appeal['work'] = [];
                $appeal['implementer'] = "";
			}
            
            if (empty($appeal)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

			$builder = $this->db->table($this->prefix.'appeal_rating'); 
			$builder->where('appeal_id', $id);
            $builder->limit(1);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
                $appeal['rating'] = $item->rating;
			}

			$builder = $this->db->table($this->prefix.'appeal_date'); 
			$builder->where('appeal_id', $id);
            $builder->where('approved', 1);
            $builder->orderBy('date_add', 'DESC');
            $builder->limit(1);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
                $appeal['date'] = $item->date;
                $appeal['date_comment'] = $item->comment;
			}

			$builder = $this->db->table($this->prefix.'appeal_gallery'); 
			$builder->where('appeal_id', $id);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
                $appeal['gallery'][$item->id] = "/assets/upload/appeal/".$id."/".$item->image;
			}
            
			$builder = $this->db->table($this->prefix.'appeal_user');
            $builder->join($this->prefix.'user', $this->prefix.'user.id = '.$this->prefix.'appeal_user.user_id');
			$builder->where($this->prefix.'appeal_user.appeal_id', $id);
            $builder->where($this->prefix.'appeal_user.group_id', 3);
			$query = $builder->get();
			foreach ($query->getResult() as $item){
                $appeal['implementer'] = $item->first_name." ".$item->last_name.", ".$item->title;
                if(!empty($item->comment_user)){
                    $appeal['implementer'] .= ", ".$item->comment_user;
                }
			}

            if($appeal['status']==5){
                $builder = $this->db->table($this->prefix.'appeal_work'); 
                $builder->where('appeal_id', $id);
                $builder->orderBy('date_add', 'DESC');
                $builder->limit(1);
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    foreach ($item as $key => $value){
                        $appeal['work'][$key] = $value;
                    }
                    $appeal['work']['gallery'] = [];
                    $appeal['work']['gallery_dir'] = "/assets/upload/work/".$id."/";
                }
                
                $builder = $this->db->table($this->prefix.'appeal_work_file'); 
                $builder->where('appeal_id', $id);
                $builder->where('photo', 1);
                $builder->orderBy('date_add', 'DESC');
                $query = $builder->get();
                foreach ($query->getResult() as $item){
                    $appeal['work']['gallery'][] = $item->file;
                }
            }
        }
        
        $data['appeal'] = $appeal;
        $data['appealTotal'] = $appealTotal;
        $data['status'] = $status;
        
        $data['time']=$this->time;
        $data['pageTitleSeo'] = lang('Front.title_main');
        $data['page'] = "appeal";
        
		echo view('front/_header',$data);
        if ($id==0) {
            echo view('front/appeal');
        } else {
            echo view('front/appeal_view');
        }
		echo view('front/_footer');
	}

	public function beforeAfter()
	{
        
        $data['time']=$this->time;
        $data['pageTitleSeo'] = lang('Front.title_main');
        $data['page'] = "before_after";
        
		echo view('front/_header',$data);
		echo view('front/before_after',$data);
		echo view('front/_footer');
	}

	public function help()
	{
        
        $data['time']=$this->time;
        $data['pageTitleSeo'] = lang('Front.title_main');
        $data['page'] = "help";
        
		echo view('front/_header',$data);
		echo view('front/help',$data);
		echo view('front/_footer');
	}    

    public function error404()
    {
        header("HTTP/1.1 404 Not Found");
        $data['time']=$this->time;
        $data['pageTitleSeo'] = lang('Front.title_404');
        $data['page'] = "help";
        
		echo view('front/_header',$data);
		echo view('front/error404',$data);
		echo view('front/_footer');
        die();
    }
}