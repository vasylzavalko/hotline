<?php
namespace App\Controllers;

require APPPATH.'../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends BaseController
{
    
    public function __construct()
    {	
        $this->prefix = $_ENV['DataBasePrefix'];
        $this->model = model('App\Models\Manager', false);
        $this->db = \Config\Database::connect();
        $this->uri = service('uri');
        $this->managerUrl = $this->uri->getSegment(1);
        $this->session = session();
        $this->login_user = $this->model->isLogin($this->managerUrl);
    } 
    
    public function index()
    {

    }

    public function statisticExport()
    {
        $artriesTotal = 0;
        $rating = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, 'total' => 0,];
        $date_start = strtotime('first day of this month 00:00:01', time());
        $date_end = strtotime('last day of this month 23:59:59', time());
        $statusQuery = "notselect";
        $ratingQuery = "notselect";
        $implementerQuery = "notselect";
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
            $overdueQuery = (isset($uri_param['overdue']))?$uri_param['overdue']:$overdueQuery;
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
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $array = [];
        $array['1'] = [ 'A' => 'Від дати', 'B' => date("d-m-Y H:i",$date_start) ];
        $array['2'] = [ 'A' => 'До дати', 'B' => date("d-m-Y H:i",$date_end) ];
        $array['3'] = [ 'A' => 'Звернень', 'B' => $artriesTotal ];
        $array['4'] = [ 'A' => 'Оцінених', 'B' => $rating['total'] ];
        
        $c = 4;
        foreach($status as $statusId => $statusTitle){
            $c++;
            $array[$c] = [ 'A' => $statusTitle, 'B' => $statusStat[$statusId] ];
        }

        foreach ($array as $key => $item){
            foreach($item as $itemKey => $value){
                $sheet->setCellValue($itemKey.$key, $value);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "stat.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');        
        exit;
        
    }
}