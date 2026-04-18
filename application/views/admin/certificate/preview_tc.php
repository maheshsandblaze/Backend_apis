<?php

$servername = "localhost";
$username = "hostsbds_newlayout";
$password = "newlayout@3233";
$dbname = "hostsbds_newlayout";
        
// $servername = "localhost";
// $username = "root";
// $password = "root";
// $dbname = "new_brach_testcase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$adm_id = $_GET['admission_id'];
$sql = "SELECT * FROM student_tc where admission_id= '$adm_id'";
$result = $conn->query($sql);

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

$year = $this->setting_model->getCurrentSessionName();

function formatDate($dt) {
    if ($dt == "0000-00-00" || empty($dt)) {
        return "00-00-0000";    
    } 
    return date_format(date_create($dt), 'd-m-Y');
    
}
?>

<style>
table, td, th {
  border: 1px solid #ccc;
  border-collapse: collapse;
  padding: 2px 15px;
  width: 100%;
  font-size: 8px;  
}
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary1">
                           <div class="row" style="margin-top:0px">
        <div class="col-md-12">
             <button id="btn-generate" class="btn btn-info btn-sm pull-right" type="button">Download Certiicate</button>
        </div>
    </div>
<?php 
    $count=0;
    while($row = $result->fetch_assoc())
    {
        $serial_number = $row['id'];
        
?>
<div style="width:580px; height:1200px; padding:20px; " id="pdf-content">                    
<div style="width:550px; height:550px; ">
    
    <div class="row">
        <!--<div class="col-md-3">-->
        <!--    <img src="<?php echo base_url(); ?>uploads/certificate/cbse-logo.png" width="60" height="auto">-->
        <!--</div>-->
        <div class="col-md-12">
            <h3 style="font-size:20px; text-align:center">RECORD SHEET APPENDIX 7 A.P.E.R</h3>
            <P style="font-size:15px; text-align:center">Rule No. 43 & 46 Chapter III</P>
        </div>
        <!--<div class="col-md-3" style="text-align:center">-->
        <!--    <img src="<?php echo base_url(); ?>uploads/certificate/logo.png" width="100" height="auto">-->
        <!--</div>-->
    </div>
    
    <div class="row" style="margin-top:10px">
        <div class="col-md-4">
            <table>
                <tr>
                    <td style="width:50%">Admission No.</td>
                    <td style="width:50%"><?php echo $row['admission_id']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-3">
            <table>
                <tr>
                    <td style="width:40%">SI.No.</td>
                    <td style="width:60%"><?php echo sprintf('%05s', $serial_number); ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-5">
            <table>
                <tr>
                    <td style="width:50%">Admission Date:</td>
                    <td style="width:50%"><?php echo $row['admission_date'] ?></td>
                </tr>
            </table>
        </div>
    </div> 
    
        <table style="margin-top:10px">
            <tr>
                <td style="width:5%">1</td>
                <td style="width:55%">Name of the Student in full</td>
                <td style="width:40%"><?php echo $row['name']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">2</td>
                <td style="width:55%">Date of birth</td>
                <td style="width:40%"><?php echo $row['dob']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">3</td>
                <td style="width:55%">Religion, Cast</td>
                <td style="width:40%"><?php echo $row['religion'] . "," . $row['category_id']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">4</td>
                <td style="width:55%">Father’s Name</td>
                <td style="width:40%"><?php echo $row['father_name']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">5</td>
                <td style="width:55%">Residential Address</td>
                <td style="width:40%"><?php echo $row['address']; ?></td>
            </tr>
            <!--<tr>-->
            <!--    <td style="width:5%">3</td>-->
            <!--    <td style="width:55%">Mother’s Name</td>-->
            <!--    <td style="width:40%"><?php echo $row['mother_name']; ?></td>-->
            <!--</tr>-->
            <!--<tr>-->
            <!--    <td style="width:5%">4</td>-->
            <!--    <td style="width:55%">Nationality</td>-->
            <!--    <td style="width:40%"><?php echo $row['religion']; ?></td>-->
            <!--</tr>-->
            <!--<tr>-->
            <!--    <td style="width:5%">5</td>-->
            <!--    <td style="width:55%">Caste SC/ST/OBC/General</td>-->
            <!--    <td style="width:40%"><?php echo $row['category_id']; ?></td>-->
            <!--</tr>-->
            <tr>
                <td style="width:5%">6</td>
                <td style="width:55%">Name of the School</td>
                <td style="width:40%"><?php echo $row['school_name'] ; ?></td>
            </tr>
            <!--<tr>-->
            <!--    <td style="width:5%">7</td>-->
            <!--    <td style="width:55%">Date of birth according to Admission register (in figures)</td>-->
            <!--    <td style="width:40%"><?php echo formatDate($row['dob']); ?></td>-->
            <!--</tr>-->
            <tr>
                <td style="width:5%">7</td>
                <td style="width:55%">Admission No.</td>
                <td style="width:40%"><?php echo $row['admission_id'] ; ?></td>
            </tr>
            <tr>
                <td style="width:5%">8</td>
                <td style="width:55%">Admission / Promation Data</td>
                <td style="width:40%"><?php echo $row['admission_data']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">9</td>
                <td style="width:55%">Class - 1</td>
                <td style="width:40%"><?php echo $row['class_one']; ?></td>
            </tr>
            <!--<tr>-->
            <!--    <td style="width:5%">10</td>-->
            <!--    <td style="width:55%">Class in which the student last studied (in words)</td>-->
            <!--    <td style="width:40%"><?php echo $row['last_class']; ?></td>-->
            <!--</tr>-->
            <tr>
                <td style="width:5%">10</td>
                <td style="width:55%">No. of Working days</td>
                <td style="width:40%"><?php echo $row['class_one_working_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">11</td>
                <td style="width:55%">No. of days present</td>
                <td style="width:40%"><?php echo $row['class_one_present_days'] ?></td>
            </tr>
            <tr>
                <td style="width:5%">12</td>
                <td style="width:55%">Class - 2</td>
                <td style="width:40%"><?php echo $row['class_two']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">13</td>
                <td style="width:55%">No. of Working days</td>
                <td style="width:40%"><?php echo $row['class_two_working_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">14</td>
                <td style="width:55%">No. of days present</td>
                <td style="width:40%"><?php echo $row['class_two_present_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">15</td>
                <td style="width:55%">Class - 3</td>
                <td style="width:40%"><?php echo $row['class_three']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">16</td>
                <td style="width:55%">No. of Working days</td>
                <td style="width:40%"><?php echo $row['class_three_working_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">17</td>
                <td style="width:55%">No. of days present</td>
                <td style="width:40%"><?php echo $row['class_three_present_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">18</td>
                <td style="width:55%">Class - 4</td>
                <td style="width:40%"><?php echo $row['class_four']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">19</td>
                <td style="width:55%">No. of Working days</td>
                <td style="width:40%"><?php echo $row['class_four_working_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">20</td>
                <td style="width:55%">No. of days present</td>
                <td style="width:40%"><?php echo $row['class_four_present_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">21</td>
                <td style="width:55%">Class - 5</td>
                <td style="width:40%"><?php echo $row['class_five']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">22</td>
                <td style="width:55%">No. of Working days</td>
                <td style="width:40%"><?php echo $row['class_five_working_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">23</td>
                <td style="width:55%">No. of days present</td>
                <td style="width:40%"><?php echo $row['class_five_present_days']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">24</td>
                <td style="width:55%">Relieve Date</td>
                <td style="width:40%"><?php echo date('d-m-Y', strtotime($row['relieve_date'])); ?></td>
            </tr>
            <tr>
                <td style="width:5%">25</td>
                <td style="width:55%">Develop (Progress)</td>
                <td style="width:40%"><?php echo $row['progress']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">26</td>
                <td style="width:55%">Conduct of the Student:</td>
                <td style="width:40%"><?php echo $row['conduct']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">27</td>
                <td style="width:55%">Date of Completion of the Class:</td>
                <td style="width:40%"><?php echo $row['completion_date']; ?></td>
            </tr>
            <tr>
                <td style="width:5%">28</td>
                <td style="width:55%">Date of Leaving the School:</td>
                <td style="width:40%"><?php echo date('d-m-Y', strtotime($row['leaving_date'])); ?></td>
            </tr>
            <tr>
                <td style="width:5%">29</td>
                <td style="width:55%">Identification Marks:</td>
                <td style="width:40%"><?php echo $row['identification_marks']; ?></td>
            </tr>
        </table>
        
        <div class="row" style="margin-top:60px; font-size: 8px;">
            <div class="col-md-12 text-right">
                <p>Signature of the Head Master/Head Mistress</p>
            </div>
        </div>                   
        
</div>
             
                  
                </div>   
                    <?php
    }
?>
            </div>
        </div> 
    </section>
    <script
	src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    
</div>

<script>
		var buttonElement = document.querySelector("#btn-generate");
		buttonElement.addEventListener('click', function() {
			const { jsPDF } = window.jspdf;
			var doc = new jsPDF("p", "pt", 'a4');
			var pdfContent = document.querySelector("#pdf-content");

			// Generate PDF from HTML using right id-selector
			doc.html(pdfContent, {
				callback: function(doc) {
				doc.save("download.pdf");
				},
				x: 10,
				y: 19
			});
		});
	</script>
<!--
<script>
function myFunction1() {
  var x = document.getElementById("pdf-content");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
<script>
setTimeout(function() {
  $('#pdf-content').fadeOut('fast');
}, 12000); // <-- time in milliseconds
</script>-->
