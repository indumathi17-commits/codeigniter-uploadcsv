<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model{
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->database();
    }
    /**
     * method import_studentdata()
     * inserting student details into database
     */
	public function import_studentdata($studentData) 
	{
        if(isset($studentData)){
            for($i=0;$i<count($studentData);$i++){

                $date = explode("-",$studentData[$i]['dob']);
                $date = array($date[2], $date[1], $date[0]);
                $dob  = implode("-", $date);

                $data= array(
                    "student_id" => $studentData[$i]['student_id'],
                    "name" => $studentData[$i]['name'],
                    'dob' => $dob,
                    'class' => $studentData[$i]['class'],
                    'sub_1' => $studentData[$i]['sub_1'],
                    'sub_2' => $studentData[$i]['sub_2'],
                    'sub_3' => $studentData[$i]['sub_3'], 
                    'sub_4' => $studentData[$i]['sub_4'],
                    'sub_5' => $studentData[$i]['sub_5'],
                    'total' => $studentData[$i]['total'],
                    'result' => $studentData[$i]['result']
                );
                $query = $this->db->insert('student_data_table',$data);
            }
            if($query){
                return true;
            }else{
                return false;
            }
        } 
	}
	
}
?>