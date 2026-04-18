<?php

function dobToWords($dob)
{
    $dateTime = new DateTime($dob);

    $year = $dateTime->format('Y');
    $month = $dateTime->format('m');
    $day = $dateTime->format('d');

    $months = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    );
    $days = array(
        1 => 'First',
        2 => 'Second',
        3 => 'Third',
        4 => 'Fourth',
        5 => 'Fifth',
        6 => 'Sixth',
        7 => 'Seventh',
        8 => 'Eighth',
        9 => 'Ninth',
        10 => 'Tenth',
        11 => 'Eleventh',
        12 => 'Twelth',
        13 => 'Thirteenth',
        14 => 'Fourteenth',
        15 => 'Fifteenth',
        16 => 'Sixteenth',
        17 => 'Seventeenth',
        18 => 'Eighteenth',
        19 => 'Nineteenth',
        20 => 'Twentieth',
        21 => 'Twenty-First',
        22 => 'Twenty-Second',
        23 => 'Twenty-Third',
        24 => 'Twenty-Fourth',
        25 => 'Twenty-Fifth',
        26 => 'Twenty-Sixth',
        27 => 'Twenty-Seventh',
        28 => 'Twenty-Eighth',
        29 => 'Twenty-Ninth',
        30 => 'Thirtieth',
        31 => 'Thirty-First'
    );

    function numberToWords($number)
    {
        $words = array(
            '0' => 'Zero',
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
            '4' => 'Four',
            '5' => 'Five',
            '6' => 'Six',
            '7' => 'Seven',
            '8' => 'Eight',
            '9' => 'Nine',
            '10' => 'Ten',
            '11' => 'Eleven',
            '12' => 'Twelve',
            '13' => 'Thirteen',
            '14' => 'Fourteen',
            '15' => 'Fifteen',
            '16' => 'Sixteen',
            '17' => 'Seventeen',
            '18' => 'Eighteen',
            '19' => 'Nineteen',
            '20' => 'Twenty',
            '30' => 'Thirty',
            '40' => 'Forty',
            '50' => 'Fifty',
            '60' => 'Sixty',
            '70' => 'Seventy',
            '80' => 'Eighty',
            '90' => 'Ninety'
        );

        if ($number < 20) {
            return $words[$number];
        } elseif ($number < 100) {
            return $words[10 * floor($number / 10)] . ($number % 10 != 0 ? '-' . $words[$number % 10] : '');
        } elseif ($number < 1000) {
            return $words[floor($number / 100)] . ' Hundred' . ($number % 100 != 0 ? ' and ' . numberToWords($number % 100) : '');
        } else {
            return numberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 != 0 ? ' ' . numberToWords($number % 1000) : '');
        }
    }

    $yearInWords = numberToWords((int)$year);
    $monthInWords = $months[(int)$month];
    $dayInWords = $days[(int)$day];

    $dobInWords = "$dayInWords $monthInWords $yearInWords";

    return $dobInWords;
}

// Example usage
// echo dobToWords('1990-07-20'); // Outputs: "Twentieth July One Thousand Nine Hundred and Ninety"



?>

<style type="text/css">
    .hide-div {
        display: none;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div id="success-alert" class="alert alert-success text-left hide-div" style="position: absolute; top: 0; right: 0;"><?php echo $this->lang->line('success_message'); ?></div>
                    <div id="edit-alert" class="alert alert-success text-left hide-div" style="position: absolute; top: 0; right: 0;"><?php echo $this->lang->line('update_message'); ?></div>
                    <form id="bonafideform" name="bonafideform" accept-charset="utf-8">
                        <div class="">

                            <div class="bozero">

                                <h4 class="pagetitleh-whitebg">Bonafide Certificate </h4>
                                <div class="around10">

                                    <?php if (isset($error_message)) { ?>
                                        <div class="alert alert-warning"><?php echo $error_message; ?></div>
                                    <?php } ?>
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
                                    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
                                    <input type="hidden" id="admission_id" name="admission_id" value="<?php echo $student->admission_no; ?>">
                                    <input type="hidden" id="student_id" name="student_id" value="<?php echo $student->id ?>">
                                    <input type="hidden" id="gender" name="gender" value="<?php echo $student->gender; ?>">


                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Student Name</label> <small class="req"> *</small>
                                                <?php
                                                $student_id = $student->id;
                                                $session_id = $this->setting_model->getCurrentSession();
                                                $student_current_class = $this->student_model->currentClassSectionById($student_id, $session_id);
                                                $class_id = $student_current_class['class_id'];
                                                $section_id = $student_current_class['section_id'];
                                                $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);


                                                if(isset($student->classname))
                                                {
                                                    $classname = $student->classname;
                                                }
                                                else{
                                                    $classname = $student->class;
                                                }





                                                $class = '';
                                                $section = '';
                                                foreach ($resultlist as $data) {
                                                    $class = $data['class'];
                                                    $section = $data['section'];
                                                }

                                                $current_session_name = $this->setting_model->getCurrentSessionName()
                                                ?>
                                                <?php
                                                $studentname = $student->firstname . " " . $student->lastname;
                                                if ($student->name) {
                                                    $studentname = $student->name;
                                                }

                                    
                                                
                                                ?>
                                                <input autofocus="" id="student_name" name="student_name" placeholder="" type="text" class="form-control" value="<?php echo $studentname; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('student_name'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('father_name'); ?></label>
                                                <input id="father_name" name="father_name" placeholder="" type="text" class="form-control" value="<?php echo $student->father_name; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                            </div>
                                        </div>


                                        <?php if (isset($student->bno)) {
                                        ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('no'); ?></label>
                                                    <input id="no" name="no" placeholder="" type="text" class="form-control" value="<?php echo $student->bno; ?>" />
                                                <span class="text-danger"><?php echo form_error('no'); ?></span>
                                            </div>
                                        </div>

                                        <?php
                                        } ?>


                                    </div>
                                    <?php
                                    $year_of_admission = date('Y', strtotime($student->admission_date));
                                    $full_year = $year_of_admission + 1;
                                    $last_two_digits = substr($full_year, -2);
                                    $display_year = $year_of_admission . "-" . $last_two_digits;

                                    // echo "<pre>";
                                    // echo $year_of_admission;exit;
                                    ?>


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Academic Year From</label>

                                                <input type="text" name="academicyear_from" id="academicyear_from" value="<?php echo $display_year; ?>" class="form-control">
                                                <!-- <span class="text-danger"><?php echo form_error('academicyear_from'); ?></span> -->
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Academic Year To</label>
                                                <input type="text" name="academicyear_to" id="academicyear_to" value="<?php echo  $current_session_name; ?>" class="form-control">
                                                <!-- <span class="text-danger"><?php echo form_error('academicyear_to'); ?></span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">From</label>

                                                <!--<input autofocus="" id="from" name="from" placeholder="" type="text" class="form-control"  value="<?php echo $student->study_from ?? "";; ?>" />-->

                                                <input autofocus="" id="from" name="from" placeholder="" type="text" class="form-control" value="<?php echo $student->class_of_admission; ?>" />
                                                <span class="text-danger"><?php echo form_error('from'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">To</label>
                                                <!--<input id="to" name="to" placeholder="" type="text" class="form-control"  value="<?php echo $student->study_to ?? ""; ?>" />-->
                                                <input id="to" name="to" placeholder="" type="text" class="form-control" value="<?php echo $classname ?> " />
                                                <span class="text-danger"><?php echo form_error('to'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $dob = '';
                                    if ($student->dob == "0000-00-00" || empty($student->dob)) {
                                        $dob = "00-00-0000";
                                    } else {
                                        $dob = date_format(date_create($student->dob), "d-m-Y");
                                    }
                                    ?>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                                <input id="dob" name="dob" placeholder="" type="text" class="form-control" value="<?php echo $dob; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('dob'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_birth'); ?> (in words) </label>
                                                <input id="dob_words" name="dob_words" placeholder="" type="text" class="form-control" value="<?php echo dobToWords($dob) ?? ""; ?>" />
                                                <span class="text-danger"><?php echo form_error('dob_words'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Conduct</label>
                                                <input id="issued_for" name="issued_for" placeholder="" type="text" class="form-control" value="<?php echo $student->issued_for ?? ""; ?>" />
                                                <span class="text-danger"><?php echo form_error('issued_for'); ?></span>
                                            </div>
                                        </div>
                                    </div>




                                </div>
                            </div>


                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
</div>
</section>
</div>

<script type="text/javascript">
    $("#bonafideform").on("submit", function(event) {
        event.preventDefault();
        var studentName = $('#student_name').val();
        var fatherName = $('#father_name').val();
        var gradeFrom = $('#grade_from').val();
        var gradeTo = $('#grade_to').val();
        var from = $('#from').val();
        var to = $('#to').val();
        var dob = $('#dob').val();
        var dobWords = $('#dob_words').val();
        var issuedFor = $('#issued_for').val();
        var admissionId = $('#admission_id').val();
        var gender = $('#gender').val();
        var academicyear_from = $('#academicyear_from').val();
        var academicyear_to = $("#academicyear_to").val();
        var no = $('#no').val();


        var dateArr = dob.split('-');
        var newDate = dateArr[2] + '-' + dateArr[1] + '-' + dateArr[0];

        var data = {
            student_name: studentName,
            father_name: fatherName,
            grade_from: gradeFrom,
            grade_to: gradeTo,
            to: to,
            from: from,
            dob: newDate,
            dob_words: dobWords,
            issued_for: issuedFor,
            admission_id: admissionId,
            gender: gender,
            academicyear_from: academicyear_from,
            academicyear_to: academicyear_to,
            no: no
        };
        $.ajax({
            url: "<?php echo base_url('admin/studentcertificate/submit_bonafide') ?>",
            method: "post",
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data && data.success) {
                    if (data.is_edited) {
                        $("#edit-alert").removeClass("hide-div");
                        setTimeout(() => {
                            $("#edit-alert").addClass("hide-div");
                            window.location.href = "<?php echo base_url('admin/studentcertificate/preview_bonafied?admission_id=' . $student->admission_no); ?>"
                        }, 3000);
                    } else {
                        $("#success-alert").removeClass("hide-div");
                        setTimeout(() => {
                            $("#success-alert").addClass("hide-div");
                            window.location.href = "<?php echo base_url('admin/studentcertificate/preview_bonafied?admission_id=' . $student->admission_no); ?>"
                        }, 3000);
                    }
                } else {
                    window.location.href = "<?php echo base_url('admin/studentcertificate/preview_bonafied?admission_id=' . $student->admission_no); ?>"
                }
            }
        });





    });
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>