<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadCSV extends CI_Controller {

    public function __construct()
	{
        parent :: __construct();
        $this->load->model("Student_model");
        $this->load->database();
    }
	/**
	 * method index()
     * To view the file upload page
	 *
	 */
	public function index()
	{
         $this->load->view('include/header');
         $this->load->view('view_upload_file');
         $this->load->view('include/footer');
    }
     /**
	 * method upload_student_data()
     * To upload the csv file and import data into database
	 *
	 */
    public function uploaded_check_file_exists()
    {
        $uploded_file = $_FILES['student_data']['name'];
        $path = 'assets/uploads/csv/';
        if(file_exists($path.$uploded_file)){
           echo 1;
        }else{    
           echo 0;
        }
    }
     /**
	 * method delete_exist_file_data()
     * To delete existing data from database
	 *
	 */
    public function delete_exist_file_data()
    {
        $uploded_file = $_FILES['student_data']['name'];
        $path = 'assets/uploads/csv/';
        unlink($path.$uploded_file);
        $query= $this->db->truncate('student_data_table');
        if($query){
            echo 1;
        }else{
           echo "Oops! something went wrong";
        }
    }
    /**
	 * method upload_student_data()
     * To upload the csv file and import data into database
	 *
	 */
    public function upload_student_data()
    {
        $uploded_file = $_FILES['student_data']['name'];
        $path = 'assets/uploads/csv/';
        $config= array(
            'upload_path' => $path,
            'allowed_types' => 'csv',
            'file_name' => $uploded_file
        );        
        //Load upload library 
        $this->load->library('upload',$config);
        if($this->upload->do_upload('student_data')){
        $uploadData = $this->upload->data();
        $filename = $uploadData['file_name'];
        $result= $this->get_csvdata($filename);
            if($result['status'] == 1)
            {
                $studentData= $result['data'];
                $insert_data= $this->Student_model->import_studentdata($studentData);    
                if($insert_data){
                    $get_data = $this->fetch_student_data();
                    echo json_encode(array("status" => 1, "data" => $get_data));
                }else{
                    $error = "<p>*Oop! something went wrongp>";
                    echo json_encode(array("status" => 0, "data" => $error));
                }
            }else{
                echo json_encode(array("status" => 0, "data" => $result['data']));
            }
        }else{
            $error = $this->upload->display_errors();
            echo json_encode(array("status" => 0, "data" => $error));
        }
    }
    /**
	 * method get_csvdata()
     * To get csv data
	 *
	 */
    public function get_csvdata($filename)
    {  
        $studentdate_arr= [];
        $error_msg= "";

        $preg_numeric = '/[0-9]/';
        $preg_specialchars = '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';

        $filename= "assets/uploads/csv/".$filename;
        $file = fopen($filename, "r");
        $n=1;
        while (($column = fgetcsv($file, ",")) !== FALSE) {
            if($n>1){
                $temp= [];
                $sl_no = $column[0];
                if (isset($column[1])) {
                    if(is_numeric($column[1])){
                        $temp['student_id'] = $column[1];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 2 it should be number <br>";
                    }
                }
                if (isset($column[2])) {
                    $chk_numeric = preg_match($preg_numeric,  $column[2]);
                    $chk_specialchars = preg_match($preg_specialchars,  $column[2]);
                    if($chk_numeric  || $chk_specialchars){
                        $error_msg.= "In row ".$sl_no.", column 3 it should be string <br>";
                    }else{
                        $temp['name'] =  $column[2];
                    }
                }
                if (isset($column[3])) {
                    if(preg_match("/\d{2}\-\d{2}-\d{4}/", $column[3])) {
                        $dob= explode("-",$column[3]);
                        $day= $dob[0];
                        $month= $dob[1];
                        $year= $dob[2];
                        if(checkdate($month, $day, $year)){
                            $temp['dob'] =  $column[3];
                        }else{
                            $error_msg.= "In row ".$sl_no.", column 4 invalid month or date or year <br>";
                        }
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 4 it should be dd-mm-yyyy <br>";
                    }
                }
                if (isset($column[4])) {
                    if(is_numeric($column[4])){
                        $temp['class'] = $column[4];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 5 it should be number <br>";
                    }
                }
                if (isset($column[5])) {
                    if(is_numeric($column[5]) && $column[5]<=100){
                        $temp['sub_1'] = $column[5];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 6 it should be number <br>";
                    }
                }
                if (isset($column[6])) {
                    if(is_numeric($column[6]) && $column[6]<=100){
                        $temp['sub_2'] = $column[6];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 7 it should be number <br>";
                    }  
                }
                if (isset($column[7])) {
                    if(is_numeric($column[7]) && $column[7]<=100){
                        $temp['sub_3'] = $column[7];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 8 it should be number <br>";
                    }
                }
                if (isset($column[8])) {
                    if(is_numeric($column[8]) && $column[8]<=100){
                        $temp['sub_4'] = $column[8];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 9 it should be number <br>";
                    }
                }
                if (isset($column[9])) {
                    if(is_numeric($column[9]) && $column[9]<=100){
                        $temp['sub_5'] = $column[9];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 10 it should be number and less than or equal to 100 <br>";
                    }
                }
                if (isset($column[10])) {
                    if(is_numeric($column[10])){
                        $temp['total'] = $column[10];
                    }else{
                        $error_msg.= "In row ".$sl_no.", column 11 it should be number <br>";
                    }  
                }
                if (isset($column[11])) {
                    $chk_numeric = preg_match($preg_numeric,  $column[11]);
                    $chk_specialchars = preg_match($preg_specialchars,  $column[11]);
                    if($chk_numeric  || $chk_specialchars){
                        $error_msg.= "In row ".$sl_no.", column 12 it should be string <br>";
                    }else{
                        $temp['result'] = $column[11];
                    }
                }
                array_push($studentdate_arr, $temp);
            }
            $n++;
        }
        fclose($file);
        if($error_msg ==""){
            $result_array= array("status" =>1,"data" => $studentdate_arr);
        }else{
            $result_array= array("status" =>0,"data" => $error_msg);
        }
        return $result_array;
    }
     /**
	 * method onload_student_data()
     * To view the student data on page load
	 *
	 */
    public function onload_student_data()
    {
        $get_data = $this->fetch_student_data();
        if($get_data){
            echo json_encode(array("status" => 1, "data" => $get_data));
        }else{
            echo 0;
        }
    }
   /**
	 * method fetch_student_data()
     * To get all students data
	 *
	 */
    public function fetch_student_data()
    {
        $query= $this->db->select('*')->from('student_data_table')->get();
        $result= $query->result();
        if($query->num_rows() > 0){
           $response= [];
          foreach($result as $res)
          {
            $row =[];
            $date = explode("-",$res->dob);
            $date = array($date[2], $date[1], $date[0]);
            $dob  = implode("/", $date);
            
            $row['student_id']= $res->student_id;
            $row['name']= $res->name;
            $row['dob']= $dob;
            $row['class']= $res->class;
            $row['sub_1']= $res->sub_1;
            $row['sub_2']= $res->sub_2;
            $row['sub_3']= $res->sub_3;
            $row['sub_4']= $res->sub_4;
            $row['sub_5']= $res->sub_5;
            $row['total']= $res->total;
            $row['result']= $res->result;
            $row['download']= "<a href='".base_url()."download_student_marks_pdf/".$res->id."' class='btn btn-success' target='_blank'>Download</a>";
            array_push($response, $row);
          }
          return json_encode($response);
        }else{
            return false;
        }
    }
    /**
	 * method download_student_marks_pdf()
     * To download student marks details
 	 *@param student id
	 */
    public function download_student_marks_pdf($sid)
    {
        $id= $sid;
        $query= $this->db->select('*')->from('student_data_table')->where("id",$id)->get();
        $data= $query->result();
        $stud_id="";
        $html='';
        $html.='<!DOCTYPE html><html><body>
                 <center><h2>Exam Result</h2></center>';
        foreach($data as $row)
        {
          $stud_id.= $row->student_id;
          $date = explode("-",$row->dob);
          $date = array($date[2], $date[1], $date[0]);
          $dob  = implode("/", $date);

          $html.= '<label>Student ID: </label>'.$row->student_id.'<br>';
          $html.= '<label>Name: </label>'.$row->name.'<br>';
          $html.= '<label>DOB: </label>'.$dob.'<br>';
          $html.= '<label>Class: </label>'.$row->class.'<br>';
          $html.= '<br>';
          $html.='<table border="1px" style="border-collapse: collapse;">';
          $html.='<tr><th>Subjects Name</th><th>Total Marks</th><th>Marks Obtained</th></tr>';
          $html.= '<tr><td>Subject 1</td><td>100</td><td>'.$row->sub_1.'</td></tr>';
          $html.= '<tr><td>Subject 2</td><td>100</td><td>'.$row->sub_2.'</td></tr>';
          $html.= '<tr><td>Subject 3</td><td>100</td><td>'.$row->sub_3.'</td></tr>';
          $html.= '<tr><td>Subject 4</td><td>100</td><td>'.$row->sub_4.'</td></tr>';
          $html.= '<tr><td>Subject 5</td><td>100</td><td>'.$row->sub_5.'</td></tr>';
          $html.= '<tr><td colspan="3"><label>Total:</label> '.$row->total.'</td></tr>';
          $html.= '<tr><td colspan="3"><label>Result:</label> '.$row->result.'</td></tr>';
          $html.= '</table>';
        }
        $html.='</body></html>';
       // Load pdf library
        $this->load->library('pdf');
    
        // Load HTML content
        $this->dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation
        $this->dompdf->setPaper('A4', 'landscape');
        
        // Render the HTML as PDF
        $this->dompdf->render();
        
        // Output the generated PDF (1 = download and 0 = preview)
        $pdf_name= "student_".$stud_id.".pdf";
        $this->dompdf->stream($pdf_name, array("Attachment"=>0));
    }
}
?>