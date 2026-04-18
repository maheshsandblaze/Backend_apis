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
$sql = "SELECT * FROM student_bonafide where admission_id= '$adm_id'";
$result = $conn->query($sql);

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
  <section class="content-header">
    <h1>
      <!-- <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?> -->
    </h1>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary1">
          <div class="row" style="margin-top:0px">
            <div class="col-md-12">
              <button id="print" onclick="printDiv()" class="btn btn-info btn-sm pull-right" style="margin-left:20px" type="button">Print</button>
              <!-- <button id="btn-generate" class="btn btn-info btn-sm pull-right" type="button">Download Certiicate</button> -->
            </div>
          </div>
          <?php
          $count = 0;
          while ($row = $result->fetch_assoc()) {
            $serial_number = '';
            $serial_number = $row['id'];
            // echo "<pre>";
            // print_r($row);exit;
          ?>

            <div id="pdf-content">
              <div style="width:1080px; height:735px; padding:5px; text-align:center; border: 10px solid #000; margin-top:5px;font-family:Times New Roman Italic;">
                <div style="width:1050px; height:700px; padding:10px; text-align:center; border: 5px solid #000">

                  <div style="text-align:center;font-size:22px;">


                  </div>

                  <div>

                    <img src="https://newlayout.wisibles.com/./uploads/print_headerfooter/BONOFIED-01.png" alt="" style="height: 80%;" width="100%">
                  </div>

                  <div style="margin-top:10px;font-family:Times New Roman, Times, serif;text-align:center;">
                    <!-- <h1 style="font-weight:bold;">BONAFIDE AND CONDUCT CERTIFICATE</h1> -->
                  </div>


                  <div style="margin-top:10px;font-family:roboto;">
                    <div style="text-align:left; font-size:18px;padding-left:10px;">
                       No : <b style="text-decoration: underline;font-weight:bold;"><?php echo $row['bonafide_no']; ?></b>
                    </div>
               
                  </div>

                  <div style="margin-top:10px;font-family:roboto">
                    <div style="text-align:left; font-size:18px;padding-left:10px;">
                      Admission No : <b style="text-decoration: underline;font-weight:bold;"><?php echo $row['admission_id']; ?></b>
                    </div>
                    <div style="text-align:right; font-size:18px;padding-right:10px;">
                      Date : <b style="text-decoration: underline;font-weight:bold;"><?php echo date("d/m/Y"); ?></b>
                    </div>
                  </div>


                  <div style="display: flex; margin-bottom:0px;font-size:18px;">
                    <p style="width: 40%;font-weight:600;text-align:right;">This is to certify that Master/Ms &nbsp;</p>
                    <p style="width: 60%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo $row['name']; ?></p>


                  </div>


                  <div style="display: flex; margin-bottom:0px;font-size:18px;">


                    <p style="width: 13%;font-weight:600;">S/o. D/o. Shri &nbsp;</p>
                    <p style="width: 87%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo $row['father_name']; ?></p>



                  </div>

                  <div style="display: flex; margin-bottom:0px;font-size:18px;">

                    <p style="width: 50%;font-weight:600;"> Was /is bonafide Student of this Isntitution during the academic &nbsp;</p>
                    <p style="width: 11%;font-weight:600;">year / years &nbsp;</p>
                    <p style="width: 39%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo $row['academicyear_to']; ?></p>


                  </div>

                  <div style="display: flex; margin-bottom:0px;font-size:18px;">

                    <p style="width: 33%;font-weight:600;">He / She has studied / is studying in class &nbsp;</p>
                    <p style="width: 47%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo $row['study_to']; ?>
                    </p>


                  </div>

                  <div style="display: flex; margin-bottom:0px;font-size:18px;">
                    <p style="width: 33%;font-weight:600;">His / Her date of birth as per the records is </p>
                    <p style="width: 67%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo date("d-m-Y", strtotime($row['dob'])); ?></p>

                  </div>

                  <div style="display: flex; margin-bottom:0px;font-size:18px;">
                    <p style="width: 9%;font-weight:600;"><?php echo "( In words" ?>&nbsp;</p>
                    <p style="width: 91%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:24px;"><?php echo $row['dob_words']; ?></p> <?php echo ")"; ?>
                  </div>


                  <div style="display: flex; margin-bottom:0px;font-size:18px;">



                    <p style="width: 27%;font-weight:600;">

                      His/Her conduct is / was Satisfied
                      &nbsp;</p>

                    <!-- <p style="width: 35%;border-bottom: 2px #333 dotted;font-weight: bold;padding-bottom:1px; text-align: center; font-size:18px;"><?php echo $row['issued_for'] . "."; ?></p> -->


                  </div>


                  <div style="margin-bottom:0px;">


                    <div style="float:right; text-align:right; font-size:18px;padding-bottom:5px;padding-right:10px;">
                      <br>
                      <b>HEAD OF THE INSTITUTION</b>
                    </div>
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
    const {
      jsPDF
    } = window.jspdf;
    var doc = new jsPDF("p", "pt", 'a4');
    var pdfContent = document.querySelector("#pdf-content");

    // Generate PDF from HTML using right id-selector
    doc.html(pdfContent, {
      callback: function(doc) {
        doc.save("download.pdf");
      },
      x: 14,
      y: 19
    });
  });



  document.getElementById("print").style.display = "block";

  function printDiv() {
    $(".no_print").css("display", "none");
    document.getElementById("print").style.display = "none";
    var divElements = document.getElementById('pdf-content').innerHTML;
    var oldPage = document.body.innerHTML;
    document.body.innerHTML =
      "<html><head><title></title></head><body>" +
      divElements + "</body>";
    window.print();
    document.body.innerHTML = oldPage;
    location.reload(true);
  }
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