

<div class="container"> 
   <form nmae="fileUploadForm" id="fileUploadForm" enctype="multipart/form-data" method="post" >
      <div class="row">
        <div class="col-md-12">     
        <h2> Upload Student Data</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">     
          <label for="student_data">Upload Your File : </label>
          <input type="file" name="student_data" id="student_data" required >
          <span style="color:#ff1a1a">*Please upload only csv file</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12"><br>
          <input type="submit" class="btn btn-primary" value="Submit" name="submitbtn" id="submitbtn">
        </div>
      </div>
    </form><br>

    <div class="row">
      <div class="col-md-12" id="error_msg" style="color:#ff1a1a">
     </div>
    </div>
   
    <div class="row">
      <div class="col-md-12" id="datatable_div">
      <table id="example" class="display">
          <thead>
             <tr style="">
              <th>Student ID</th>
              <th>Name</th>
              <th>DOB</th>
              <th>Class</th>
              <th>Subject 1</th>
              <th>Subject 2</th>
              <th>Subject 3</th>
              <th>Subject 4</th>
              <th>Subject 5</th>
              <th>Total</th>
              <th>Result</th>
              <th>PDF</th>
            </tr>
           </thead>
           <tbody>
           </tbody>
        </table>
      </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>

<script type="text/javascript">
   $("#example").DataTable({destroy: true,});
</script>
<script type="text/javascript">
$(document).ready(function() {
  $.ajax({
    type: "post",
    url: base_url+"UploadCSV/onload_student_data",
    success: function(response){
      var jsonObj = JSON.parse(response);
      if(jsonObj.status == 1){
          var tdata = JSON.parse(jsonObj.data);
            $("#example").DataTable({
              destroy: true,
              data  : tdata,
              columns :  [
                { data : "student_id" },
                { data : "name" },
                { data : "dob" },
                { data : "class" },
                { data : "sub_1" },
                { data : "sub_2" },
                { data : "sub_3" },
                { data : "sub_4" },
                { data : "sub_5" },
                { data : "total" },
                { data : "result" },
                { data : "download" },
              ]
          });
       }else{
      }
    }
});
});
</script>
<script type="text/javascript">
var base_url = '<?=base_url()?>';
$("#fileUploadForm").submit(function(e){
    e.preventDefault();
    var form = $('#fileUploadForm')[0];
    var formData = new FormData(form);
        $.ajax({
            type: "post",
            url: base_url+"UploadCSV/uploaded_check_file_exists",
            data: formData,
            processData: false,
            contentType: false,
            success: function(data){
              if(data == 1){
                if(confirm("The file already exists. Do you want to replace it?")){
                  $.ajax({
                      type: "post",
                      url: base_url+"UploadCSV/delete_exist_file_data",
                      data: formData,
                      processData: false,
                      contentType: false,
                      success: function(data){
                          if(data == 1){
                            import_csv_data();
                          }else{
                            $("#error_msg").show();
                            $("#error_msg").html(data);
                          }
                      }
                  });
                }
              }else{
                import_csv_data();
              }
            }
        });
    return true;
});
function import_csv_data()
{
  var form = $('#fileUploadForm')[0];
  var formData = new FormData(form);
      $.ajax({
          type: "post",
          url: base_url+"UploadCSV/upload_student_data",
          data: formData,
          processData: false,
          contentType: false,
          success: function(response){
            var jsonObj = JSON.parse(response);
            if(jsonObj.status == 1){
               $('#fileUploadForm')[0].reset();;
               $("#error_msg").hide();
               var tdata = JSON.parse(jsonObj.data);
               $("#example").DataTable({
                    destroy: true,
                    data  : tdata,
                    columns :  [
                      { data : "student_id" },
                      { data : "name" },
                      { data : "dob" },
                      { data : "class" },
                      { data : "sub_1" },
                      { data : "sub_2" },
                      { data : "sub_3" },
                      { data : "sub_4" },
                      { data : "sub_5" },
                      { data : "total" },
                      { data : "result" },
                      { data : "download" },
                    ]
                });
            }else{
              $("#error_msg").show();
              $('#fileUploadForm')[0].reset();;
              $("#error_msg").html(jsonObj.data);
            }
          }
      });
}
</script>