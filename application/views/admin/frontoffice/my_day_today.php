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
    
    .custom-text    {
        text-decoration: underline;
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

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <button type="submit" name="search" value="search" id="AddLateStudent" class="btn btn-primary btn-sm pull-right checkbox-toggle"></i> <?php echo $this->lang->line('submit'); ?></button> -->
                                        <button type="button" name="search" value="search" id="AddLateStudent" class="btn btn-primary btn-sm pull-right checkbox-toggle"></i> Fetch</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                    <?php if (!empty($mydaytoday_list)) { ?>
                        <div class="">
                            <div class="box-header ptbnull"></div>
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('my_day_today'); ?></h3>
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


                                                <th>Father Name</th>

                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>



                                            <?php 
                                            $count = 1;
                                            foreach ($mydaytoday_list as $val) {
                                                // echo "<pre>";
                                                // print_r($late_entries);exit;
                                            ?>

                                                <tr>

                                                    <td><?php echo $count; ?></td>
                                                    <td><?php echo $val['firstname'] . " " . $val['lastname'] ?></td>
                                                    <td><?php echo $val['admission_no']; ?></td>
                                                    <td><?php echo $val['class']; ?></td>
                                                    <td><?php echo $val['section']; ?></td>
                                                    <td><?php echo $val['father_name']; ?></td>


                                                    <td><?php echo date('Y-m-d', strtotime($val['date'])); ?></td>
                                                    
                                                    <td class="mailbox-date pull-right">
                                                        <span data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><a class="btn btn-default btn-xs" onclick="mydaytoday(<?php echo $val['id']; ?>);" title="" data-target="#mydaytoday" data-toggle="modal">
                                                                <i class="fa fa-reorder"></i></a></span>
                                                        <span data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">        
                                                            <a data-id="<?php echo $val['id'] ?>" class="btn btn-default btn-xs deletemydaytoday" data-toggle="tooltip" title=""><i class="fa fa-remove"></i></a>     
                                                        </span>        
                                                    </td>
                                                </tr>


                                            <?php 
                                            $count++;
                                            } 
                                            ?>



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
                                        <input type="hidden" name="newadmission_no" id="newadmission_no" value="">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="section" class="col-sm-3 control-label">I Was</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" checked="" value="1" autocomplete="off"> Happy
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="2" autocomplete="off"> Chatty
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="3" autocomplete="off"> Curious
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="4" autocomplete="off"> Quiet
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="5" autocomplete="off"> Sleepy
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="6" autocomplete="off"> Busy
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="iwas" value="7" autocomplete="off"> Grumpy
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <h4 class="custom-text">I Drank</h4>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">When</label>
                                                <input id="when1" name="when1" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">When</label>
                                                <input id="when2" name="when2" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">When</label>
                                                <input id="when3" name="when3" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">When</label>
                                                <input id="when4" name="when4" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">How Much</label>
                                                <input id="howmuch1" name="howmuch1" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">How Much</label>
                                                <input id="howmuch2" name="howmuch2" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">How Much</label>
                                                <input id="howmuch3" name="howmuch3" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">How Much</label>
                                                <input id="howmuch4" name="howmuch4" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <h4 class="custom-text">I Slept</h4>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">When</label>
                                                <input id="slept_when" name="slept_when" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">How Long</label>
                                                <input id="slept_howlong" name="slept_howlong" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                
                                <div class="col-sm-12">
                                    <h4 class="custom-text">I Ate</h4>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">My Snack</label>
                                                <select name="my_snack" class="form-control" autocomplete="off">
                                                    <option value="">Select</option>
                                                    <option value="1">Completed</option>
                                                    <option value="2">Almost</option>
                                                </select>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">My Lunch</label>
                                                <select name="my_lunch" class="form-control" autocomplete="off">
                                                    <option value="">Select</option>
                                                    <option value="1">Completed</option>
                                                    <option value="2">Almost</option>
                                                </select>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="custom-text">I Had Fun</h4>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">We Time</label>
                                                <input id="we_time" name="we_time" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Gross Motor</label>
                                                <input id="gross_motor" name="gross_motor" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Fine Motor</label>
                                                <input id="fine_motor" name="fine_motor" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Free Play</label>
                                                <input id="free_play" name="free_play" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Study Time</label>
                                                <input id="study_time" name="study_time" placeholder="" type="text" class="form-control" value="" />
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <h4 class="custom-text">I Went</h4>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee1" checked="" value="1" autocomplete="off"> Poo
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee1" value="2" autocomplete="off"> Pee
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee1" value="3" autocomplete="off"> Both
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">When</label>
                                                            <input id="poo_pee_text1" name="poo_pee_text1" placeholder="" type="text" class="form-control" value="" />
                                                            <span class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee2" checked="" value="1" autocomplete="off"> Poo
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee2" value="2" autocomplete="off"> Pee
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee2" value="3" autocomplete="off"> Both
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">When</label>
                                                            <input id="poo_pee_text2" name="poo_pee_text2" placeholder="" type="text" class="form-control" value="" />
                                                            <span class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee3" checked="" value="1" autocomplete="off"> Poo
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee3" value="2" autocomplete="off"> Pee
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee3" value="3" autocomplete="off"> Both
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">When</label>
                                                            <input id="poo_pee_text3" name="poo_pee_text3" placeholder="" type="text" class="form-control" value="" />
                                                            <span class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee4" checked="" value="1" autocomplete="off"> Poo
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee4" value="2" autocomplete="off"> Pee
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="poo_pee4" value="3" autocomplete="off"> Both
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">When</label>
                                                            <input id="poo_pee_text4" name="poo_pee_text4" placeholder="" type="text" class="form-control" value="" />
                                                            <span class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>    
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="custom-text">I Need</h4>
                                            <div>
                                                <div class="form-group">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="need1" checked="" value="1" autocomplete="off"> Diaper
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="need1" value="2" autocomplete="off"> Clothes
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="need1" value="3" autocomplete="off"> Wipes
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="need1" value="4" autocomplete="off"> Baby Cream
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary addvisitorentry">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="mydaytoday" tabindex="-1" role="dialog" aria-labelledby="mydaytoday" style="padding-left: 0 !important">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('my_day_today'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="mydaytoday_details">
            </div>
        </div>
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
                url: base_url + "admin/my_day_today/getStudentData",
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
                        $('#newadmission_no').val(data.studentData.admission_no);
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



    })

    $(document).ready(function() {
        $(document).on('submit', '#addvisitorsudentEntry', function(e) {
            e.preventDefault(); // Prevent the default form submission
    
            var form = $(this)[0]; // Get the form element
            var formData = new FormData(form); // Create FormData object
            
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
    
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('admin/my_day_today/index'); ?>",
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
    
    function mydaytoday(id) {

        $('#evaluation_details').html("");
        $.ajax({
            url: baseurl + 'admin/my_day_today/mydaytoday_detail/' + id,
            success: function(data) {
                $('#mydaytoday_details').html(data);
            },
            error: function() {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }
    
    $(document).ready(function(e) {

        $('#mydaytoday').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

    });
    
    
    $('.deletemydaytoday').click(function() {
            var mydaytodayid = $(this).attr('data-id');
            if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/my_day_today/remove',
                    type: "POST",
                    data: {
                        mydaytodayid: mydaytodayid
                    },
                    dataType: 'json',
                    success: function(res) {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                });
            }
        });
</script>