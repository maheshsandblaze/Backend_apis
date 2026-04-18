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
                                        <button type="button" name="search" value="search" id="AddLateStudent" class="btn btn-primary btn-sm pull-right checkbox-toggle"></i> <?php echo $this->lang->line('submit'); ?></button>

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




                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th>Time</th>

                                                <th><?php echo $this->lang->line('roll_number'); ?></th>
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


                                                    <td><?php echo date('Y-m-d', strtotime($val['date'])); ?></td>
                                                    <td><?php echo date('H:i:s', strtotime($val['date'])); ?></td>

                                                    <td><?php echo $val['roll_no'] ?></td>

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
    <div class="modal-dialog">
        <form action="" method="POST" id="addLatesudent">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('late_entry'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="studentname" class="control-label col-sm-3">Student Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="studentname" id="studentname" value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="class" class="col-sm-3 control-label">Class</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="class" id="class" value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="section" class="col-sm-3 control-label">Section</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="section" id="section" value="" readonly>
                                <input type="hidden" name="admission_no" id="admission_no" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary addLatesudent">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</div>




<!-- grouppayment modal end -->



<script>
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
                url: base_url + "admin/late_entries/getStudentData",
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
                        $('#admission_no').val(admission_no);

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



        $(document).on('click', '.addLatesudent', function() {
            // e.preventDefault(); // avoid to execute the actual submit of the form.

            var $this = $(this);
            var admission_no = $('#admission_no').val();

            // alert(admission_no);return false;
            var url =  base_url + "admin/late_entries/index";
            // var smt_btn = $(this).find("button[type=submit]");
            $.ajax({
                type: "POST",
                url: url,
                // dataType: 'JSON',
                // data: form.serialize(), // serializes the form's elements.
                data: {
                    'newadmission_no': admission_no
                },
                // dataType: "JSON",
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(response) {
                    console.log(response); // Log the response to inspect it
                    if (response.status === 1) {
                        $("#listCollectionDisountModal").modal('hide');

                        console.log(response);

                        location.reload(true);
                    } else if (response.status === 0) {
                        location.reload(true);

                     
                    }
                },

                error: function(xhr) { // if error occured

                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                },
                complete: function() {
                    smt_btn.button('reset');
                }
            });

        });
    })
</script>