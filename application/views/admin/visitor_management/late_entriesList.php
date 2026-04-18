<style type="text/css">
    .radio {
        padding-left: 20px;
    }

    .radio label {
        display: inline-block;
        vertical-align: middle;
        position: relative;
        padding-left: 5px;
    }

    .radio label::before {
        content: "";
        display: inline-block;
        position: absolute;
        width: 17px;
        height: 17px;
        left: 0;
        margin-left: -20px;
        border: 1px solid #cccccc;
        border-radius: 50%;
        background-color: #fff;
        -webkit-transition: border 0.15s ease-in-out;
        -o-transition: border 0.15s ease-in-out;
        transition: border 0.15s ease-in-out;
    }

    .radio label::after {
        display: inline-block;
        position: absolute;
        content: " ";
        width: 11px;
        height: 11px;
        left: 3px;
        top: 3px;
        margin-left: -20px;
        border-radius: 50%;
        background-color: #555555;
        -webkit-transform: scale(0, 0);
        -ms-transform: scale(0, 0);
        -o-transform: scale(0, 0);
        transform: scale(0, 0);
        -webkit-transition: -webkit-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -moz-transition: -moz-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -o-transition: -o-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        transition: transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
    }

    .radio input[type="radio"] {
        opacity: 0;
        z-index: 1;
    }

    .radio input[type="radio"]:focus+label::before {
        outline: thin dotted;
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px;
    }

    .radio input[type="radio"]:checked+label::after {
        -webkit-transform: scale(1, 1);
        -ms-transform: scale(1, 1);
        -o-transform: scale(1, 1);
        transform: scale(1, 1);
    }

    .radio input[type="radio"]:disabled+label {
        opacity: 0.65;
    }

    .radio input[type="radio"]:disabled+label::before {
        cursor: not-allowed;
    }

    .radio.radio-inline {
        margin-top: 0;
    }

    .radio-primary input[type="radio"]+label::after {
        background-color: #337ab7;
    }

    .radio-primary input[type="radio"]:checked+label::before {
        border-color: #337ab7;
    }

    .radio-primary input[type="radio"]:checked+label::after {
        background-color: #337ab7;
    }

    .radio-danger input[type="radio"]+label::after {
        background-color: #d9534f;
    }

    .radio-danger input[type="radio"]:checked+label::before {
        border-color: #d9534f;
    }

    .radio-danger input[type="radio"]:checked+label::after {
        background-color: #d9534f;
    }

    .radio-info input[type="radio"]+label::after {
        background-color: #5bc0de;
    }

    .radio-info input[type="radio"]:checked+label::before {
        border-color: #5bc0de;
    }

    .radio-info input[type="radio"]:checked+label::after {
        background-color: #5bc0de;
    }

    @media (max-width:767px) {
        .radio.radio-inline {
            display: inherit;
        }
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line(' '); ?> <small><?php echo $this->lang->line('by_date1'); ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button>
                        </div>
                    </div>
                    <form id='form1' action="" method="post" accept-charset="utf-8">
                        <!-- <?php echo site_url('admin/late_entries/index') ?> -->
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <?php if ($this->session->flashdata('msg')) {
                                ?>
                                    <?php echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg'); ?>
                                <?php } ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('admission_no'); ?></label><small class="req"> *</small>

                                        <input type="text" name="admission_no" id="admission_no" class="form-control" value="<?php set_value('admission_no'); ?>" autofocus>


                                        <span class="text-danger"><?php echo form_error('admission_no'); ?></span>
                                    </div>
                                </div>

                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">
                                            <?php echo $this->lang->line('attendance_date'); ?>
                                        </label><small class="req"> *</small>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div> -->

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <button type="submit" name="search" value="search" id="AddLateStudent" class="btn btn-primary btn-sm pull-right checkbox-toggle"></i> <?php echo $this->lang->line('submit'); ?></button> -->
                                        <button type="button" name="search" value="search" id="AddLateStudent" class="btn btn-primary btn-sm pull-right checkbox-toggle"></i> Fetch</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                    <?php if (!empty($late_entries)) { ?>
                        <div class="">
                            <div class="box-header ptbnull"></div>
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                            <div class="box-body">

                                <div class="table-responsive ptt10">
                                    <table class="table table-hover table-striped example">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th><?php echo $this->lang->line('name'); ?></th>

                                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                <th><?php echo $this->lang->line('class'); ?></th>
                                                <th><?php echo $this->lang->line('section'); ?></th>


                                                <th>Visitor Name</th>

                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th>Time</th>

                                                <!--<th><?php echo $this->lang->line('roll_number'); ?></th>-->
                                                <th><?php echo $this->lang->line('purpose'); ?></th>
                                                <!-- <th width="30%"><?php echo $this->lang->line('attendance'); ?></th> -->
                                                <!-- <th class="noteinput"><?php echo $this->lang->line('note'); ?></th> -->
                                            </tr>
                                        </thead>
                                        <tbody>



                                            <?php $count = 1;
                                            foreach ($late_entries as $val) {
                                                // echo "<pre>";
                                                // print_r($late_entries);exit;
                                            ?>

                                                <tr>

                                                    <td><?php echo $count; ?></td>
                                                    <td><?php echo $val['firstname'] . " " . $val['lastname'] ?></td>
                                                    <td><?php echo $val['admission_no']; ?></td>
                                                    <td><?php echo $val['class']; ?></td>
                                                    <td><?php echo $val['section']; ?></td>
                                                    <td><?php echo $val['guardian_name']; ?></td>


                                                    <td><?php echo date('Y-m-d', strtotime($val['date'])); ?></td>
                                                    <td><?php echo $val['time']; ?></td>

                                                    <!--<td><?php echo $val['roll_no'] ?></td>-->
                                                    <td><?php echo $val['purpose']; ?></td>

                                                </tr>


                                            <?php $count++;
                                            } ?>



                                        </tbody>

                                    </table>
                                </div>


                            </div>




                        </div>


                    <?php  } ?>
                </div>

    </section>
</div>


<!-- grouppayment modal  start -->


<div id="listCollectionDisountModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="addvisitorsudentEntry" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('late_entry'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="studentname" class="control-label">Student Name</label>
                                        <input type="text" class="form-control" name="studentname" id="studentname" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="class" class="col-sm-3 control-label">Class</label>
                                        <input type="text" class="form-control" name="class" id="class" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="section" class="col-sm-3 control-label">Section</label>
                                        <input type="text" class="form-control" name="section" id="section" value="" readonly>
                                        <input type="hidden" name="newadmission_no" id="newadmission_no" value="0">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="section" class="col-sm-3 control-label">Visitor Relation</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="visitor_relation" checked="" value="father" autocomplete="off"> Father
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="visitor_relation" value="mother" autocomplete="off"> Mother
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="visitor_relation" value="other" autocomplete="off"> Other
                                        </label>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label>
                                        <input id="guardian_name" name="guardian_name" placeholder="" type="text" class="form-control" value="" />
                                        <span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('phone'); ?></label><small class="req"> *</small>
                                        <input id="guardian_phone" name="guardian_phone" placeholder="" type="text" class="form-control guardian_phone" value="" />
                                        <span class="text-danger"><?php echo form_error('guardian_phone'); ?></span>

                                        <span class="text-danger" id="guardian_phone_replace"></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Relation</label>
                                        <input id="guardian_relation" name="guardian_relation" placeholder="" type="text" class="form-control" value="" />
                                        <span class="text-danger"><?php echo form_error('guardian_relation'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-3" id="other_relation_upload">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('photo'); ?></label>
                                        <div>
                                            <input class="filestyle form-control" type='file' name='file' value=""/>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-md-3" id="guardian_photo">
                                    <div class="form-group">
                                        <p class="text-center"><?php echo $this->lang->line('guardian_photo'); ?></p>
                                        <div class="widget-user-image">
                                            <img id="guardian_image" class="profile-user-img img-responsive img-rounded" src="<?php echo base_url('uploads/student_images/default_male.jpg'); ?>" alt="Guardian Image">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('purpose'); ?></label></label><small class="req"> *</small>
                                        <input id="purpose" name="visitorpurpose" placeholder="" type="text" class="form-control" value="" />
                                        <!-- <span class="text-danger"><?php echo form_error('visitorpurpose'); ?></span> -->
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="time" class="form-control timepicker" id="stime_" value="<?php echo set_value('time'); ?>">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                        <input type="text" id="date" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" name="date" readonly="">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control" id="description" name="note" name="note" rows="3"></textarea>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary addvisitorentry">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</div>




<!-- grouppayment modal end -->

<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script>
    $(function() {
        $(".timepicker").timepicker({

        });
    });
    
    $(document).ready(function () {
        $('input[name="visitor_relation"]').change(function () {
            if ($(this).val() === 'other') {
                $('#other_relation_upload').show(); // Show file upload
                $('#guardian_photo').hide(); // Hide guardian image
            } else {
                $('#other_relation_upload').hide(); // Hide file upload
                $('#guardian_photo').show(); // Show guardian image
            }
        });
    
        // Trigger change on page load in case "Other" is pre-selected
        $('input[name="visitor_relation"]:checked').trigger('change');
    });

    $(document).ready(function() {

        // $('#listCollectionDisountModal').modal({
        //     backdrop: 'static',
        //     keyboard: false,
        //     show: false
        // });

        $(document).on('click', '#AddLateStudent', function() {
            var $this = $(this);
            var admission_no = $('#admission_no').val();

            $.ajax({
                type: 'POST',
                url: base_url + "admin/visitor_management/getStudentData",
                data: {
                    'admission_no': admission_no
                },
                dataType: "JSON",
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(data) {
                    if (data.status === 'success') {
                        // Populate the modal fields with student data
                        $('#studentname').val(data.studentData.firstname);
                        $('#class').val(data.studentData.class);
                        $('#section').val(data.studentData.section);
                        $('#admission_no').val(data.studentData.admission_no);
                        $('#student_session_id').val(data.studentData.student_session_id)

                        // Show the modal
                        $("#listCollectionDisountModal").modal('show');
                    } else {
                        alert(data.message);
                    }
                    $this.button('reset');
                },
                error: function(xhr) {
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });
        });


        $(document).ready(function () {
    var base_url = "<?php echo base_url(); ?>"; // Define base_url for AJAX requests

    function updateGuardianDetails(selectedRelation) {
        var admission_no = $('#admission_no').val(); // Get admission number

        if (!admission_no) {
            alert("Please enter admission number first.");
            return;
        }

        $.ajax({
            type: 'POST',
            url: base_url + "admin/visitor_management/getStudentData",
            data: { admission_no: admission_no },
            dataType: "JSON",
            success: function (data) {
                if (data.status === 'success') {
                    $('#newadmission_no').val(admission_no);
                    var imageUrl = base_url + "uploads/student_images/default_placeholder.jpg"; // Default image

                    if (selectedRelation === 'father') {
                        $('#guardian_name').val(data.studentData.father_name);
                        $('#guardian_phone').val(data.studentData.father_phone);
                        $('#guardian_relation').val("<?php echo $this->lang->line('father'); ?>");
                        imageUrl = data.studentData.father_pic 
                                   ? data.studentData.father_pic 
                                   : base_url + "uploads/student_images/default_male.jpg";
                    } 
                    else if (selectedRelation === 'mother') {
                        $('#guardian_name').val(data.studentData.mother_name);
                        $('#guardian_phone').val(data.studentData.mother_phone);
                        $('#guardian_relation').val("<?php echo $this->lang->line('mother'); ?>");
                        imageUrl = data.studentData.mother_pic 
                                   ? data.studentData.mother_pic 
                                   : base_url + "uploads/student_images/default_female.jpg";
                    } 
                    else {
                        $('#guardian_name').val('');
                        $('#guardian_phone').val('');
                        $('#guardian_relation').val('');
                    }

                    // ✅ Update the image dynamically
                    $('#guardian_image').attr('src', imageUrl);

                } else {
                    alert(data.message);
                    resetGuardianDetails(); // Reset fields if error occurs
                }
            },
            error: function () {
                alert("Error fetching guardian data. Please try again.");
                resetGuardianDetails();
            }
        });
    }

    // Reset function for clearing fields
    function resetGuardianDetails() {
        $('#guardian_name, #guardian_phone, #guardian_relation').val('');
        $('#guardian_image').attr('src', base_url + "uploads/student_images/default_male.jpg");
    }

    // ✅ Handle radio button change event
    $('input[name="visitor_relation"]').change(function () {
        updateGuardianDetails($(this).val());
    });

    // ✅ Set default relation when modal opens
    $('#listCollectionDisountModal').on('shown.bs.modal', function () {
        $('input[name="visitor_relation"][value="father"]').prop('checked', true);
        updateGuardianDetails('father');
    });
});



    })

    $(document).ready(function() {
        $(document).on('submit', '#addvisitorsudentEntry', function(e) {
            e.preventDefault(); // Prevent the default form submission
    
            var form = $(this)[0]; // Get the form element
            var formData = new FormData(form); // Create FormData object
            
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
    
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('admin/visitor_management/index'); ?>",
                data: formData, // Send FormData instead of serialize
                contentType: false, // Important: Prevent jQuery from setting contentType
                processData: false, // Important: Prevent jQuery from processing data
                dataType: "JSON",
                beforeSend: function() {
                    $('.addvisitorentry').prop('disabled', true); // Disable the submit button
                },
                success: function(response) {
                    $('#listCollectionDisountModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr) {
                    $('#listCollectionDisountModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                complete: function() {
                    $('.addvisitorentry').prop('disabled', false); // Re-enable the submit button
                }
            });
        });
    });
</script>