<div class="row">
    <div class="col-md-2 hide-mobile">
        <div class="box border0">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo $this->lang->line('reports'); ?></h3>
                <div class="btn-group pull-right hide-desktop">
                    <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button>
                </div>
            </div>
            <ul class="tablists">
                <?php
                if (($this->rbac->hasPrivilege('student_report', 'can_view') ||
                    $this->rbac->hasPrivilege('guardian_report', 'can_view') ||
                    $this->rbac->hasPrivilege('student_history', 'can_view') ||
                    $this->rbac->hasPrivilege('student_login_credential_report', 'can_view') ||
                    $this->rbac->hasPrivilege('class_subject_report', 'can_view') ||
                    $this->rbac->hasPrivilege('admission_report', 'can_view') ||
                    $this->rbac->hasPrivilege('sibling_report', 'can_view') ||
                    $this->rbac->hasPrivilege('evaluation_report', 'can_view') ||
                    $this->rbac->hasPrivilege('student_profile', 'can_view'))) {
                ?>
                    <li class="<?php echo set_Submenu('Reports/student_information'); ?>">
                        <a class="<?php echo set_Submenu('Reports/student_information'); ?>" href="<?php echo base_url(); ?>report/studentinformation"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_information'); ?></a>
                    </li>

                <?php
                }
                if (($this->rbac->hasPrivilege('fees_statement', 'can_view') ||
                    $this->rbac->hasPrivilege('balance_fees_report', 'can_view') ||
                    $this->rbac->hasPrivilege('fees_collection_report', 'can_view') ||
                    $this->rbac->hasPrivilege('online_fees_collection_report', 'can_view') ||
                    $this->rbac->hasPrivilege('income_report', 'can_view') ||
                    $this->rbac->hasPrivilege('expense_report', 'can_view') ||
                    $this->rbac->hasPrivilege('payroll_report', 'can_view') ||
                    $this->rbac->hasPrivilege('income_group_report', 'can_view') ||
                    $this->rbac->hasPrivilege('expense_group_report', 'can_view'))) {
                ?>
                    <li class="<?php echo set_Submenu('Reports/finance'); ?>">
                        <a class="<?php echo set_Submenu('Reports/finance'); ?>" href="<?php echo base_url(); ?>financereports/finance"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/2.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('finance'); ?></a>
                    </li>

                <?php
                }
                if (($this->rbac->hasPrivilege('attendance_report', 'can_view') ||
                    $this->rbac->hasPrivilege('staff_attendance_report', 'can_view'))) {
                ?>
                    <li class="<?php echo set_Submenu('Reports/attendance'); ?>">
                        <a class="<?php echo set_Submenu('Reports/attendance'); ?>" href="<?php echo base_url(); ?>attendencereports/attendance"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/3.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('attendance'); ?></a>
                    </li>

                <?php
                }
                if (($this->rbac->hasPrivilege('rank_report', 'can_view'))) {
                ?>
                    <li class="<?php echo set_Submenu('Reports/examinations'); ?>">
                        <a class="<?php echo set_Submenu('Reports/examinations'); ?>" href="<?php echo base_url(); ?>admin/examresult/examinations"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/4.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('examinations'); ?></a>
                    </li>
                    <?php
                }
                if ($this->module_lib->hasActive('online_examination')) {
                    if (($this->rbac->hasPrivilege('online_exam_wise_report', 'can_view') ||
                        $this->rbac->hasPrivilege('online_exams_report', 'can_view') ||
                        $this->rbac->hasPrivilege('online_exams_attempt_report', 'can_view') ||
                        $this->rbac->hasPrivilege('online_exams_rank_report', 'can_view')
                    )) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/online_examinations'); ?>">
                            <a class="<?php echo set_Submenu('Reports/online_examinations'); ?>" href="<?php echo base_url(); ?>admin/onlineexam/report"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/5.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('online') . " " . $this->lang->line('examinations'); ?></a>
                        </li>
                    <?php
                    }
                }

                if ($this->module_lib->hasActive('lesson_plan')) {
                    if (($this->rbac->hasPrivilege('syllabus_status_report', 'can_view') || $this->rbac->hasPrivilege('teacher_syllabus_status_report', 'can_view'))) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/lesson_plan'); ?>">
                            <a class="<?php echo set_Submenu('Reports/lesson_plan'); ?>" href="<?php echo base_url(); ?>report/lesson_plan"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/6.png') ?>" alt="icon6" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('lesson_plan'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('human_resource')) {
                    if (($this->rbac->hasPrivilege('staff_report', 'can_view') || $this->rbac->hasPrivilege('payroll_report', 'can_view'))) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/human_resource'); ?>">
                            <a class="<?php echo set_Submenu('Reports/human_resource'); ?>" href="<?php echo base_url(); ?>report/staff_report"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/7.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('human_resource'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('library')) {
                    if (($this->rbac->hasPrivilege('book_issue_report', 'can_view') ||
                        $this->rbac->hasPrivilege('book_due_report', 'can_view') ||
                        $this->rbac->hasPrivilege('book_inventory_report', 'can_view'))) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/library'); ?>">
                            <a class="<?php echo set_Submenu('Reports/library'); ?>" href="<?php echo base_url(); ?>report/library"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/9.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('library'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('inventory')) {
                    if ((
                        $this->rbac->hasPrivilege('stock_report', 'can_view') ||
                        $this->rbac->hasPrivilege('add_item_report', 'can_view') ||
                        $this->rbac->hasPrivilege('issue_inventory_report', 'can_view'))) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/inventory'); ?>">
                            <a class="<?php echo set_Submenu('Reports/inventory'); ?>" href="<?php echo base_url(); ?>report/inventory"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/10.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('inventory'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('transport')) {
                    if ($this->rbac->hasPrivilege('transport_report', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('reports/studenttransportdetails'); ?>">
                            <a class="<?php echo set_Submenu('reports/studenttransportdetails'); ?>" href="<?php echo base_url(); ?>admin/route/studenttransportdetails"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/11.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('transport'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('hostel')) {
                    if ($this->rbac->hasPrivilege('hostel_report', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('reports/studenthosteldetails'); ?>">
                            <a class="<?php echo set_Submenu('reports/studenthosteldetails'); ?>" href="<?php echo base_url(); ?>admin/hostelroom/studenthosteldetails"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/12.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('hostel'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->module_lib->hasActive('alumni')) {
                    if ($this->rbac->hasPrivilege('alumni_report', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('Reports/alumni_report'); ?>">
                            <a class="<?php echo set_Submenu('Reports/alumni_report'); ?>" href="<?php echo base_url(); ?>report/alumnireport"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/13.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('alumni'); ?></a>
                        </li>
                    <?php
                    }
                }
                if ($this->rbac->hasPrivilege('user_log', 'can_view')) {
                    ?>
                    <li class="<?php echo set_Submenu('Reports/userlog'); ?>">
                        <a class="<?php echo set_Submenu('Reports/userlog'); ?>" href="<?php echo base_url(); ?>admin/userlog"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/14.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('user_log'); ?></a>
                    </li>
                <?php
                }
                if ($this->rbac->hasPrivilege('audit_trail_report', 'can_view')) {
                ?>
                    <li class="<?php echo set_Submenu('audit/index'); ?>>">
                        <a class="<?php echo set_Submenu('audit/index'); ?>" href="<?php echo base_url(); ?>admin/audit"><img src="<?php echo base_url('backend/images/sidebar/submenu/reports/15.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('audit_trail_report'); ?></a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div><!--./col-md-3-->
    <div class="col-md-10">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('student_information_report'); ?></h3>
                <div class="btn-group pull-right hide-desktop">
                    <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button>
                </div>
            </div>
            <div class="">
                <ul class="reportlists">
                    <?php
                    if ($this->rbac->hasPrivilege('student_report', 'can_view')) {
                    ?>
                        <!--<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_report'); ?> "><a href="<?php echo base_url(); ?>report/studentreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_report'); ?></a></li>-->
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_master_report'); ?> "><a href="<?php echo base_url(); ?>report/studentmasterreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_report'); ?></a></li>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/classsectionreport'); ?>"><a href="<?php echo site_url('report/classsectionreport'); ?>"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('class_section_report'); ?></a></li>

                    <?php
                    }
                    if ($this->rbac->hasPrivilege('guardian_report', 'can_view')) {
                    ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/guardian_report'); ?>"><a href="<?php echo base_url(); ?>report/guardianreport"><i class="fa fa-file-text-o"></i><?php echo $this->lang->line('guardian_report'); ?></a></li>
                    <?php
                    }
                    if ($this->rbac->hasPrivilege('student_history', 'can_view')) {
                    ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_history'); ?>"><a href="<?php echo base_url() ?>report/admissionreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_history'); ?></a></li>
                    <?php } ?>

                    <?php if ($this->rbac->hasPrivilege('student_login_credential_report', 'can_view')) { ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_login_credential'); ?>"><a href="<?php echo base_url(); ?>report/logindetailreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_login_credential'); ?></a></li>
                    <?php } ?>

                    <?php if ($this->rbac->hasPrivilege('student_login_credential_report', 'can_view')) { ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/parent_login_credential'); ?>"><a href="<?php echo base_url(); ?>report/parentlogindetailreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('parent_login_credential'); ?></a></li>
                    <?php } ?>

                    <?php if ($this->rbac->hasPrivilege('class_subject_report', 'can_view')) {
                    ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/class_subject_report'); ?>"><a href="<?php echo base_url(); ?>report/class_subject"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('class_subject_report'); ?></a></li>
                    <?php }
                    if ($this->rbac->hasPrivilege('admission_report', 'can_view')) { ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/admission_report'); ?>"><a href="<?php echo base_url(); ?>report/admission_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('admission_report'); ?></a></li>
                    <?php }
                    if ($this->rbac->hasPrivilege('sibling_report', 'can_view')) { ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/sibling_report'); ?>"><a href="<?php echo base_url(); ?>report/sibling_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('sibling_report'); ?></a></li>
                    <?php
                    }
                    if ($this->rbac->hasPrivilege('student_profile', 'can_view')) {
                    ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_profile'); ?>"><a href="<?php echo base_url(); ?>report/student_profile"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_profile'); ?></a></li>
                    <?php } ?>

                    <?php if ($this->rbac->hasPrivilege('student_gender_ratio_report', 'can_view')) {   ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/boys_girls_ratio'); ?>"><a href="<?php echo base_url(); ?>report/boys_girls_ratio"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_gender_ratio_report'); ?></a></li>
                    <?php }
                    if ($this->rbac->hasPrivilege('student_teacher_ratio_report', 'can_view')) { ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_teacher_ratio'); ?>"><a href="<?php echo base_url(); ?>report/student_teacher_ratio"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_teacher_ratio_report'); ?></a></li>
                    <?php } ?>

                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/online_admission'); ?>"><a href="<?php echo base_url(); ?>report/online_admission_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('online_admission_report'); ?></a></li>

                    <?php if ($this->rbac->hasPrivilege('student_all_data_report', 'can_view')) {
                    ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/student_all_data_report'); ?> "><a href="<?php echo base_url(); ?>report/student_all_data_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_all_data_report'); ?></a></li>



                    <?php
                    } ?>

                    <?php if ($this->rbac->hasPrivilege('app_install_report', 'can_view')) {   ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/student_information/app_install_users'); ?>"><a href="<?php echo base_url(); ?>report/app_install_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('app_install_users_report'); ?></a></li>
                    <?php } ?>

                </ul>
            </div>
        </div>