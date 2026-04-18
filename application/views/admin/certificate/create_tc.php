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
                <div  id="edit-alert" class="alert alert-success text-left hide-div" style="position: absolute; top: 0; right: 0;"><?php echo $this->lang->line('update_message'); ?></div>
                    <form  id="submittc" name="employeeform">                       
                        <div class="">
                            <div class="bozero">
                                <h4 class="pagetitleh-whitebg">Transfer <?php echo $this->lang->line('certificate'); ?> </h4>
                                <div class="around10">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
                                    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
                                    <input type="hidden" id="admission_id" name="admission_id" value="<?php echo $student->admission_no; ?>">
                                    <input type="hidden" id="admission_date" name="admission_date" value="<?php echo $student->admission_date; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Student Full Name</label>
                                                <?php 
                                                $studentname = $student->firstname . " " . $student->lastname;
                                                if ($student->name) {
                                                    $studentname = $student->name; 
                                                } ?>
                                                <input id="student_name" name="student_name" placeholder="" type="text" class="form-control"  value="<?php echo $studentname; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('student_name'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php
                                            $dob = '';
                                            if($student->dob == "0000-00-00" || empty($student->dob)){
                                                $dob = "00-00-0000";
                                            }else{
                                                $dob = date_format(date_create($student->dob),"d-m-Y");
                                            }
                                        ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date of Birth</label>
                                                <input id="dob" name="dob" placeholder="" type="text" class="form-control"  value="<?php echo $dob; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('dob'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('religion'); ?></label>
                                                <input id="religion" name="religion" placeholder="" type="text" class="form-control"  value="<?php echo $student->religion; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('religion'); ?></span>
                                            </div>
                                        </div>
                                                <!-- ask about category -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('caste'); ?></label>
                                                <input id="category_id" name="category_id" placeholder="" type="text" class="form-control"  value="<?php if($student->category_id == 1){
                                                            echo "General";
                                                }else if($student->category_id == 2){
                                                            echo "OBC";
                                                }else if($student->category_id == 3){
                                                            echo "SC";
                                                }else if($student->category_id == 4){
                                                            echo "ST";
                                                }else if($student->category_id == 5){
                                                            echo "Others";
                                                } ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('category_id'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1"><?php echo $this->lang->line('fathers_name'); ?></label>-->
                                        <!--        <input id="father_name" name="father_name" placeholder="" type="text" class="form-control"  value="<?php echo $student->father_name; ?>" readonly />-->
                                        <!--        <span class="text-danger"><?php echo form_error('father_name'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1"><?php echo $this->lang->line('mother_name'); ?></label>-->
                                        <!--        <input id="mother_name" name="mother_name" placeholder="" type="text" class="form-control"  value="<?php echo $student->mother_name; ?>" readonly />-->
                                        <!--        <span class="text-danger"><?php echo form_error('mother_name'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('fathers_name'); ?></label>
                                                <input id="father_name" name="father_name" placeholder="" type="text" class="form-control"  value="<?php echo $student->father_name; ?>" readonly />
                                                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Residential Address</label>
                                                <input id="resd_address" name="resd_address" placeholder="" type="text" class="form-control"  value="<?php echo $student->current_address; ?>" />
                                                <span class="text-danger"><?php echo form_error('resd_address'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name of the School</label>
                                                <input id="school_name" name="school_name" placeholder="" type="text" class="form-control"  value="<?php echo $this->setting_model->getCurrentSchoolName(); ?>" />
                                                <span class="text-danger"><?php echo form_error('school_name'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Admission No.</label>
                                                <input id="admission_no" name="admission_no" placeholder="" type="text" class="form-control"  value="<?php echo $student->admission_no; ?>" />
                                                <span class="text-danger"><?php echo form_error('admission_no'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php
                                            $adm_date = '';
                                            if($student->admission_date == "0000-00-00" || empty($student->admission_date)){
                                                $adm_date = "00-00-0000";
                                            }else{
                                                $adm_date = date_format(date_create($student->admission_date),"d-m-Y");
                                            }
                                        ?>
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Date of First Admission</label>-->
                                        <!--        <input id="admission_date" name="admission_date" placeholder="" type="text" class="form-control"  value="<?php echo $adm_date; ?>" readonly />-->
                                        <!--        <span class="text-danger"><?php echo form_error('admission_date'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Date of First Admission (with class)</label>-->
                                        <!--        <input id="prev_class" name="prev_class" placeholder="" type="text" class="form-control"  value="<?php echo $student->prev_class; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('prev_class'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    <div class="row">
                                        
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Admission / Promation Data</label>
                                                <input id="admission_data" name="admission_data" placeholder="" type="text" class="form-control"  value="<?php echo $student->admission_data ?>" />
                                                <span class="text-danger"><?php echo form_error('admission_data'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Class - 1</label>
                                                <input id="class_one" name="class_one" placeholder="" type="text" class="form-control"  value="Class - 1" />
                                                <span class="text-danger"><?php echo form_error('class_one'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of Working days</label>
                                                <input id="class_one_working_days" name="class_one_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_one_working_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_one_working_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of days present</label>
                                                <input id="class_one_present_days" name="class_one_present_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_one_present_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_one_present_days'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Class - 2</label>
                                                <input id="class_two" name="class_two" placeholder="" type="text" class="form-control"  value="Class - 2" />
                                                <span class="text-danger"><?php echo form_error('class_two'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of Working days</label>
                                                <input id="class_two_working_days" name="class_two_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_two_working_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_two_working_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of days present</label>
                                                <input id="class_two_present_days" name="class_two_present_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_two_present_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_two_present_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 1:</label>-->
                                        <!--        <input id="sub_studied1" name="sub_studied1" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject1; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied1'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 2:</label>-->
                                        <!--        <input id="sub_studied2" name="sub_studied2" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject2; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied2'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 3:</label>-->
                                        <!--        <input id="sub_studied3" name="sub_studied3" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject3; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied3'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 4:</label>-->
                                        <!--        <input id="sub_studied4" name="sub_studied4" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject4; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied4'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Class - 3</label>
                                                <input id="class_three" name="class_three" placeholder="" type="text" class="form-control"  value="Class - 3" />
                                                <span class="text-danger"><?php echo form_error('class_three'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of Working days</label>
                                                <input id="class_three_working_days" name="class_three_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_three_working_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_three_working_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of days present</label>
                                                <input id="class_three_present_days" name="class_three_present_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_three_present_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_three_present_days'); ?></span>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 5:</label>-->
                                        <!--        <input id="sub_studied5" name="sub_studied5" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject5; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied5'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 6:</label>-->
                                        <!--        <input id="sub_studied6" name="sub_studied6" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject6; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied6'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 7:</label>-->
                                        <!--        <input id="sub_studied7" name="sub_studied7" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject7; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied7'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Subjects Studied - 8:</label>-->
                                        <!--        <input id="sub_studied8" name="sub_studied8" placeholder="" type="text" class="form-control"  value="<?php echo $student->subject8; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('sub_studied8'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Class - 4</label>
                                                <input id="class_four" name="class_four" placeholder="" type="text" class="form-control"  value="Class - 4" />
                                                <span class="text-danger"><?php echo form_error('class_four'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of Working days</label>
                                                <input id="class_four_working_days" name="class_four_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_four_working_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_four_working_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of days present</label>
                                                <input id="class_four_present_days" name="class_four_present_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_four_present_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_four_present_days'); ?></span>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Board / School Annual Exam last taken, with result:</label>-->
                                        <!--        <input id="annual_exam" name="annual_exam" placeholder="" type="text" class="form-control"  value="<?php echo $student->board_school_name; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('annual_exam'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Promotion to the next class Yes or No, if yes, to which class?</label>-->
                                        <!--        <input id="promote_class" name="promote_class" placeholder="" type="text" class="form-control"  value="<?php echo $student->promotion; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('promote_class'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Class - 5</label>
                                                <input id="class_five" name="class_five" placeholder="" type="text" class="form-control"  value="Class - 5" />
                                                <span class="text-danger"><?php echo form_error('class_five'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of Working days</label>
                                                <input id="class_five_working_days" name="class_five_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_five_working_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_five_working_days'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">No. of days present</label>
                                                <input id="class_five_present_days" name="class_five_present_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->class_five_present_days ?>" />
                                                <span class="text-danger"><?php echo form_error('class_five_present_days'); ?></span>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Month up to which school fees paid:</label>-->
                                        <!--        <input id="fees" name="fees" placeholder="" type="text" class="form-control"  value="<?php echo $student->school_fees_month ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('fees'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Total no.of working days:</label>-->
                                        <!--        <input id="working_days" name="working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->total_working_days ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('working_days'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Total no.of working days student attend the school:</label>-->
                                        <!--        <input id="school_working_days" name="school_working_days" placeholder="" type="text" class="form-control"  value="<?php echo $student->total_student_working_days ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('school_working_days'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Relieve Date</label>
                                                <input id="relieve_date" name="relieve_date" placeholder="" type="relieve_date" class="form-control date"  value="<?php echo $student->relieve_date; ?>" />
                                                <span class="text-danger"><?php echo form_error('relieve_date'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Develop (Progress)</label>
                                                <input id="progress" name="progress" placeholder="" type="text" class="form-control"  value="<?php echo $student->progress; ?>" />
                                                <span class="text-danger"><?php echo form_error('progress'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Conduct</label>
                                                <input id="conduct" name="conduct" placeholder="" type="text" class="form-control"  value="<?php echo $student->conduct; ?>" />
                                                <span class="text-danger"><?php echo form_error('conduct'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date of Completion of the Class:</label>
                                                <input id="completion_date" name="completion_date" placeholder="" type="completion_date" class="form-control date"  value="<?php echo $student->completion_date; ?>" />
                                                <span class="text-danger"><?php echo form_error('completion_date'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date of Leaving the School:</label>
                                                <input id="leaving_date" name="leaving_date" placeholder="" type="leaving_date" class="form-control date"  value="<?php echo $student->leaving_date; ?>" />
                                                <span class="text-danger"><?php echo form_error('leaving_date'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Identification Marks:</label>
                                                <input id="identification_marks" name="identification_marks" placeholder="" type="text" class="form-control"  value="<?php echo $student->identification_marks ?>" />
                                                <span class="text-danger"><?php echo form_error('identification_marks'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Date of application</label>-->
                                        <!--        <input id="apply_date" name="ncc" placeholder="" type="apply_date" class="form-control date"  value="<?php echo $student->date_of_application; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('apply_date'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Issue of certificate</label>-->
                                        <!--        <input id="issue_cer" name="issue_cer" placeholder="" type="apply_date" class="form-control date"  value="<?php echo $student->issue_of_certificate; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('issue_cer'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Reason for leaving</label>-->
                                        <!--        <input id="leaving_reason" name="leaving_reason" placeholder="" type="text" class="form-control"  value="<?php echo $student->reason ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('leaving_reason'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label for="exampleInputEmail1">Other Remarks</label>-->
                                        <!--        <input id="remarks" name="remarks" placeholder="" type="text" class="form-control"  value="<?php echo $student->remarks; ?>" />-->
                                        <!--        <span class="text-danger"><?php echo form_error('remarks'); ?></span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
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

$( "#submittc" ).on( "submit", function( event ) {
    event.preventDefault();
    var studentName = $('#student_name').val();
    var fatherName = $('#father_name').val();
    var religion = $('#religion').val();
    var categoryId = $('#category_id').val();
    var dob = $('#dob').val();
    var address = $('#resd_address').val();
    var school_name = $('#school_name').val();
    var admission_no = $('#admission_no').val();
    var admission_data = $('#admission_data').val();
    var class_one = $('#class_one').val();
    var class_one_working_days = $('#class_one_working_days').val();
    var class_one_present_days = $('#class_one_present_days').val();
    var class_two = $('#class_two').val();
    var class_two_working_days = $('#class_two_working_days').val();
    var class_two_present_days = $('#class_two_present_days').val();
    var class_three = $('#class_three').val();
    var class_three_working_days = $('#class_three_working_days').val();
    var class_three_present_days = $('#class_three_present_days').val();
    var class_four = $('#class_four').val();
    var class_four_working_days = $('#class_four_working_days').val();
    var class_four_present_days = $('#class_four_present_days').val();
    var class_five = $('#class_five').val();
    var class_five_working_days = $('#class_five_working_days').val();
    var class_five_present_days = $('#class_five_present_days').val();
    var relieve_date = $('#relieve_date').val();
    var progress = $('#progress').val();
    var conduct = $('#conduct').val();
    var completion_date = $('#completion_date').val();
    var leaving_date = $('#leaving_date').val();
    var identification_marks = $('#identification_marks').val();
    var admission_date = $('#admission_date').val();
    
    
    var getFormatDate = (dt) => {
        let dateArr = dt.split('-');
        return dateArr[2]+ '-' + dateArr[1] + '-' + dateArr[0];
    };
    
    // var newDate = getFormatDate(dob);
    // var relieve_date = getFormatDate(relieve_date);
    // var completion_date = getFormatDate(completion_date);
    // var leaving_date = getFormatDate(leaving_date);
    // var admDate = getFormatDate(admission_date);

    var data = {
        studentName : studentName,
        fatherName : fatherName,
        religion : religion,
        categoryId : categoryId,
        dob : dob,
        address : address,
        school_name : school_name,
        admission_no : admission_no,
        admission_data : admission_data,
        class_one : class_one,
        class_one_working_days : class_one_working_days,
        class_one_present_days : class_one_present_days,
        class_two : class_two,
        class_two_working_days : class_two_working_days,
        class_two_present_days : class_two_present_days,
        class_three : class_three,
        class_three_working_days : class_three_working_days,
        class_three_present_days : class_three_present_days,
        class_four : class_four,
        class_four_working_days : class_four_working_days,
        class_four_present_days : class_four_present_days,
        class_five : class_five,
        class_five_working_days : class_five_working_days,
        class_five_present_days : class_five_present_days,
        relieve_date : relieve_date,
        progress : progress,
        conduct : conduct,
        completion_date : completion_date,
        leaving_date : leaving_date,
        identification_marks : identification_marks,
        admission_date : admission_date
    };
    $.ajax({
        url: "<?php echo base_url('admin/studentcertificate/submit_tc') ?>",
        method: "post",
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data && data.success) {
                 if (data.is_edited) {
                    $("#edit-alert").removeClass("hide-div");
                    setTimeout(() => {
                        $("#edit-alert").addClass("hide-div");
                        window.location.href = "<?php echo base_url('admin/studentcertificate/preview_tc?admission_id='.$student->admission_no); ?>"
                    }, 3000);
                } else {
                    $("#success-alert").removeClass("hide-div");
                    setTimeout(() => {
                        $("#success-alert").addClass("hide-div");
                        window.location.href = "<?php echo base_url('admin/studentcertificate/preview_tc?admission_id='.$student->admission_no); ?>"
                    }, 3000);
                }
            } else {
                console.log(data);
                // window.location.href = "<?php echo base_url('admin/studentcertificate/preview_tc?admission_id='.$student->admission_no); ?>"
            }
        }    
    });





});

</script>

<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>