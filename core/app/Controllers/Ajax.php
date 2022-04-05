<?php
namespace App\Controllers;

class Ajax extends BaseController
{
    
    public function __construct()
    {	

        $this->db = \Config\Database::connect();
        $this->model = model('App\Models\Manager', false);
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->time = time();
        
    } 
    
    public function index()
    {

    }
    
    public function upload()
    {

        $extensionsImage = ["jpeg","jpg","png"];
        $extensionsDoc = ["docx","doc","pdf"];
        $id = $this->request->getVar('id');
        $work = $this->request->getVar('work');
        $user = $this->request->getVar('user');
        
        if ( $uploadFile = $this->request->getFiles() ) {
            $upladDir = "/assets/upload/work/".$id."/";
            $uploadPath = $_SERVER['DOCUMENT_ROOT'].$upladDir;
            if(!is_dir($uploadPath)) { mkdir($uploadPath, 0755, true); }
            foreach ($uploadFile['files'] as $img) {
                $ext = $img->guessExtension();
                $newName = $img->getRandomName();
                // images
                if(in_array($ext,$extensionsImage) === true){
                    if ($img->isValid() && ! $img->hasMoved()) {
                        if ($img->move($uploadPath, $newName)) {
                            $image_size = getimagesize($uploadPath."/".$newName);
                            $image_width = $image_size[0];
                            $image_height = $image_size[1];
                            $image = \Config\Services::image();
                            $newNameMedium = str_replace(".", "_medium.", $newName);
                            if($image_width>800){
                                $image->withFile($uploadPath.'/'.$newName)->resize(800, 1200, true, 'width')->save($uploadPath.'/'.$newNameMedium,90);
                            }else{
                                $image->withFile($uploadPath.'/'.$newName)->save($uploadPath.'/'.$newNameMedium,90);
                            }
                            $data = [
                                'appeal_id' => $id,
                                'work_id' => $work,
                                'date_add' => time(),
                                'user_id' => $user,
                                'photo' => 1,
                                'file' => $newName,
                            ];
                            $this->model->_insert($data,'appeal_work_file');
                        }						
                    }
                }
                // doc
                /*
                if(in_array($ext,$extensionsDoc) === true){
                    if ($img->isValid() && ! $img->hasMoved()) {
                        if ($img->move($uploadPath, $newName)) {
                            $data = [
                                'appeal_id' => $id,
                                'work_id' => $work,
                                'date_add' => time(),
                                'user_id' => $user,
                                'doc' => 1,
                                'file' => $newName,
                            ];
                            $this->model->_insert($data,'appeal_work_file');
                        }
                    }
                }
                */
            }
            $this->model->saveLlog($user,lang('Log.appeal_implementation_doc_add',[$id,$work]));
        }
    }	

    public function workPhoto($id=0)
    {
        $return = [];
        $builder = $this->db->table($this->prefix.'appeal_work_file'); 
        $builder->where('work_id', $id);
        $builder->where('photo', 1);
        $builder->orderBy('date_add', 'DESC');
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $return[$item->id]['image'] = str_replace(".", "_medium.", $item->file);
            $return[$item->id]['completed'] = $item->completed;
        }
        header('Content-Type: application/json');
        echo json_encode($return);
    }

    public function workDoc($id=0)
    {
        $return = [];
        $builder = $this->db->table($this->prefix.'appeal_work_file'); 
        $builder->where('work_id', $id);
        $builder->where('doc', 1);
        $builder->orderBy('date_add', 'DESC');
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $return[$item->id] = $item->file;
        }
        header('Content-Type: application/json');
        echo json_encode($return);
    }	

    public function fileDel()
    {	
        $id = $this->request->getVar('id');
        $where = "id = ".$id;
        $this->model->_delete($where,'appeal_work_file'); 
    }

    public function filecompleted()
    {	
        $id = $this->request->getVar('id');
        $builder = $this->db->table($this->prefix.'appeal_work_file'); 
        $builder->where('id', $id);
        $query = $builder->get();
        foreach ($query->getResult() as $item){
            $completed = $item->completed;
        }
        $completed = ($completed==0)?1:0;
        $where = "id = ".$id;
        $data['completed'] = $completed;
        $this->model->_update($data,$where,'appeal_work_file'); 
    }

}
