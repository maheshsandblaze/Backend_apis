<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<style type="text/css">
    .borderwhite {
        border-top-color: #fff !important;
    }

    .box-header>.box-tools {
        display: none;
    }

    .sidebar-collapse #barChart {
        height: 100% !important;
    }

    .sidebar-collapse #lineChart {
        height: 100% !important;
    }

    /*.fc-day-grid-container{overflow: visible !important;}*/
    .tooltip-inner {
        max-width: 135px;
    }

    @media (max-width:768px) {
        .row-reverse {
            display: flex;
            flex-direction: column-reverse !important;
        }

        .hide-on-mobile {
            display: none;
        }
    }
</style>

<?php
$role    = $this->customlib->getStaffRole();
$role_id = json_decode($role)->id;
?>

<?php if ($role_id != 8 && $role_id != 2 && $role_id != 9) { ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="">

                <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
                    <div class="alert alert-danger">
                        Wisibles ERP may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
                    </div>
                <?php } ?>

                <?php
                $show    = false;
                $role    = $this->customlib->getStaffRole();
                $role_id = json_decode($role)->id;
                foreach ($notifications as $notice_key => $notice_value) {

                    if ($role_id == 7) {
                        $show = true;
                    } elseif (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notice_value->publish_date))) {
                        $show = true;
                    }
                    if ($show) {
                ?>
                        <!--<div class="dashalert alert alert-success alert-dismissible" role="alert">-->
                        <!--    <button type="button" class="alertclose close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>-->
                        <!--    <a href="<?php echo site_url('admin/notification') ?>"><?php echo $notice_value->title; ?></a>-->
                        <!--</div>-->
                <?php
                    }
                }
                ?>
            </div>



            <div class="row hello-div row-reverse">
                <?php
                $bar_chart = true;

                if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                    if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) {

                        $div_rol  = 3;
                        $userdata = $this->customlib->getUserData();
                ?>
                        <div class="box-body-pt col-lg-9 col-md-9 col-sm-12">
                            <div class="search">
                                <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>

                                    <form id="header_search_form" class="search-dashboard navbar-form navbar-left search-form" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="input-group">
                                            <input type="text" value="<?php echo set_value('search_text1'); ?>" name="search_text1" id="search_text1" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                            <span class="input-group-btn">
                                                <button type="submit" name="search" id="search-btn" onclick="getstudentlist()" style="" class="btn btn-flat topsidesearchbtn"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>

                                    </form>
                                <?php } ?>

                            </div>
                            <?php
                            $file   = "";
                            $result = $this->customlib->getUserData();

                            $image = $result["image"];
                            $role  = $result["user_type"];
                            $id    = $result["id"];
                            if (!empty($image)) {

                                $file = "uploads/staff_images/" . $image . img_time();
                            } else {
                                if ($result['gender'] == 'Female') {
                                    $file = "uploads/staff_images/default_female.jpg" . img_time();
                                } else {
                                    $file = "uploads/staff_images/default_male.jpg" . img_time();
                                }
                            }
                            ?>
                            <div class="pt-80 row">

                                <div class="border-radius-20 box box-primary borderwhite">
                                    <div class="box-header-one box-header with-border ">
                                        <div class="col-lg-7 col-md-7 col-sm-6">
                                            <h3 class="hello-text box-title">Hello <?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                            <h4>Check your daily fee statements</h4>
                                            <a href="<?php echo site_url() ?>financereports/reportdailycollection" class="btn-check-now mt-10 btn btn-primary">Check Now</a>

                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-6">
                                            <img class="hell-img img-fluid" src="<?php echo site_url('backend/images/sidebar/hello_img.png') ?>">
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">

                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstart">
                                            <p class="text-blur"><?php echo date("M j, Y");; ?></p>
                                            <p class="mt5 clearfix font-16">Student Attendance<br>
                                                <span class="font12">Today</span>
                                            </p>
                                            <div class="box-header with-border">
                                                <div class="progress-group">
                                                    <div class="progress progress-minibar">
                                                        <div class="progress-bar progress-bar-maroon" style="width: <?php if ($total_students > 0) {
                                                                                                                        echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                                                    } ?>%"></div>
                                                    </div>
                                                    <p class="mt5 clearfix">Progress<span class="pull-right"> <?php if ($total_students > 0) {

                                                                                                                    $attendance_progress = (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);


                                                                                                                    echo number_format($attendance_progress, 2);
                                                                                                                } ?>%</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt8">
                                                <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                <a class="d-inline mml20" href="<?php echo site_url('attendencereports/daily_attendance_report') ?>"><i class="fa fa-plus ftlayer-maroon"></i></a>
                                                <span class="pull-right text-maroon-bg"> <?php echo 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present']; ?>/<?php echo $total_students; ?></span>

                                            </div>
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->
                            <?php }
                    }
                            ?>

                            <?php
                            //if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) {
                            ?>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="topprograssstart">
                                    <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                    <p class="mt5 clearfix font-16">Staff Attendance<br>
                                        <span class="font12">Today</span>
                                    </p>
                                    <div class="box-header with-border">
                                        <div class="progress-group">
                                            <div class="progress progress-minibar">
                                                <div class="progress-bar progress-bar-blue" style="width: <?php echo $percentTotalStaff_data; ?>%"></div>
                                            </div>



                                            <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo number_format($percentTotalStaff_data, 2); ?>%</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt8">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <a class="d-inline mml20" href="<?php echo site_url('attendencereports/staffattendancereport') ?>"><i class="fa fa-plus ftlayer-blue"></i></a>
                                        <span class="pull-right text-blue-bg"> <?php echo $Staffattendence_data + 0; ?>/<?php echo $getTotalStaff_data; ?></span>

                                    </div>
                                </div><!--./topprograssstart-->
                            </div><!--./col-md-3-->
                            <?php
                            // }

                            ?>


                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="topprograssstart">
                                    <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                    <p class="mt5 clearfix font-16">Fee Collection<br>
                                        <span class="font12">Today</span>
                                    </p>
                                    <div class="box-header with-border">
                                        <div class="progress-group">
                                            <div class="progress progress-minibar">
                                                <div class="progress-bar progress-bar-orange" style="width: <?php echo $fessprogressbar; ?>%"></div>
                                            </div>
                                            <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo number_format($fessprogressbar, 2); ?>%</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt8">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <a class="d-inline mml20" href="<?php echo site_url('studentfee') ?>"><i class="fa fa-plus ftlayer-orange"></i></a>
                                        <span class="pull-right text-orange-bg"> <?php echo $total_paid; ?>/<?php echo $total_fees ?></span>
                                    </div>
                                </div><!--./topprograssstart-->
                            </div><!--./col-md-3-->


                                </div><!--./row-->
                            </div>


                            <!-- admission intake  -->
                            <div class="col-lg-12 col-md-12 col-sm-12 ">
                                <div class="border-radius-20 box box-primary">
                                    <h4 class="text-center pb-10 fee-summary-title"><?php echo $this->lang->line('school_vacancies'); ?></h4>


                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('class'); ?></th>
                                                <th><?php echo $this->lang->line('section'); ?></th>
                                                <th><?php echo $this->lang->line('intake'); ?></th>
                                                <th><?php echo $this->lang->line('admitted'); ?></th>

                                                <th><?php echo $this->lang->line('vacancies'); ?></th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            foreach ($admissionintakes as $intake) {

                                                $vacanciescount = $intake['vacancies'] - $intake['intakes'];
                                            ?>
                                                <tr>
                                                    <td class="mailbox-name"><?php echo $intake['class'] ?></td>
                                                    <td class="mailbox-name"><?php echo $intake['section'] ?></td>
                                                    <td class="mailbox-name"><?php echo $intake['vacancies'] ?></td>
                                                    <td class="mailbox-name"><?php echo $intake['intakes']; ?></td>
                                                    <td class="mailbox-name"><?php echo $vacanciescount; ?></td>


                                                </tr>
                                            <?php
                                            }
                                            $count++;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <!-- admission intake end -->


                        </div><!--./col-lg-7-->







                        <div class="mt-10  col-lg-3 col-md-3 col-sm-12">
                            <div class="hide-on-mobile border-radius-20 div-user-infomain mt-15 box box-primary">
                                <div class="box-widget widget-user-2 mb0">
                                    <div class="text-right  admin-edit">
                                        <a href="<?php echo base_url() . "admin/staff/edit/" . $id ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-pencil"></i></a>

                                    </div>

                                    <div class="widget-user-header overflow-hidden">

                                        <div class="div-user-info">
                                            <h5 class="ml-0 widget-user-desc mb5"><?php echo $role; ?></h5>

                                            <h3 class="ml-0 widget-user-username"><?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                            <h5 class="ml-0 mt-20 view-profile widget-user-desc"><a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>" style="color:#9854cb;">View Profile <i class="ml fa fa-check-square-o"></i></a></h5>
                                        </div>

                                        <div class="widget-user-image">
                                            <img style="box-shadow: none;" class="profile-user-img img-responsive img-rounded" src="<?php echo site_url('backend/images/sidebar/admin-orange-img.png') ?>" alt="User profile picture">
                                        </div>

                                    </div>
                                </div>


                            </div>
                            <div class="border-radius-20 box box-primary pt-10 ptb-20">
                                <h4 class="text-center pb-10 fee-summary-title">Fee Summary</h4>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/12.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Students Fees</span>
                                            <span class="info-box-number">Rs. <?php echo amountFormat($feesummarData['totalfee']) ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/13.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Paid Fees</span>
                                            <span class="info-box-number">Rs.<?php echo amountFormat($feesummarData['deposit']) ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/14.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Balance Fees</span>
                                            <span class="info-box-number">Rs.<?php echo amountFormat($feesummarData['balance']) ?></span>
                                        </div>
                                    </a>
                                </div>

                            </div>


                        </div>


            </div><!--./col-lg-5-->


    </div><!--./row-->




    </div>


    <div id="newEventModal" class="modal fade " role="dialog">
        <div class="modal-dialog modal-dialog2 modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line("add_new_event"); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form role="form" id="addevent_form" method="post" enctype="multipart/form-data" action="">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                    <input class="form-control" name="title" id="input-field">
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('description'); ?></label>
                                    <textarea name="description" class="form-control" id="desc-field"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-12 col-sm-12">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('event_color'); ?></label>
                                    <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php
                                    $i      = 0;
                                    $colors = '';
                                    foreach ($event_colors as $color) {
                                        $color_selected_class = 'cpicker-small';
                                        if ($i == 0) {
                                            $color_selected_class = 'cpicker-big';
                                        }
                                        $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                        $i++;
                                    }
                                    echo '<div class="cpicker-wrapper">';
                                    echo $colors;
                                    echo '</div>';
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pt15 displayblock overflow-hidden w-100"><?php echo $this->lang->line('event_type'); ?></label>
                                    <label class="radio-inline w-xs-45">
                                        <input type="radio" name="event_type" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                                    </label>
                                    <label class="radio-inline w-xs-45">
                                        <input type="radio" name="event_type" value="private" checked id="private"><?php echo $this->lang->line('private'); ?>
                                    </label>
                                    <label class="radio-inline w-xs-45 ml-xs-0">
                                        <input type="radio" name="event_type" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo json_decode($role)->name; ?>
                                    </label>
                                    <label class="radio-inline w-xs-45">
                                        <input type="radio" name="event_type" value="protected" id="public"><?php echo $this->lang->line('protected'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <input type="submit" class="btn btn-primary submit_addevent pull-right" value="<?php echo $this->lang->line('save'); ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="viewEventModal" class="modal fade " role="dialog">
        <div class="modal-dialog modal-dialog2 modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('edit_event'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form role="form" method="post" id="updateevent_form" enctype="multipart/form-data" action="">
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title') ?></label>
                                <input class="form-control" name="title" placeholder="" id="event_title">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('description') ?></label>
                                <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="eventid" id="eventid">
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color') ?></label>
                                <input type="hidden" name="eventcolor" autocomplete="off" placeholder="Event Color" id="event_color" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <?php
                                $i      = 0;
                                $colors = '';
                                foreach ($event_colors as $color) {
                                    $colorid              = trim($color, "#");
                                    $color_selected_class = 'cpicker-small';
                                    if ($i == 0) {
                                        $color_selected_class = 'cpicker-big';
                                    }
                                    $colors .= "<div id=" . $colorid . " class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                    $i++;
                                }
                                echo '<div class="cpicker-wrapper selectevent">';
                                echo $colors;
                                echo '</div>';
                                ?>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type') ?></label>
                                <label class="radio-inline">
                                    <input type="radio" name="eventtype" value="public" id="public"><?php echo $this->lang->line('public') ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="eventtype" value="private" id="private"><?php echo $this->lang->line('private') ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="eventtype" value="sameforall" id="public"><?php echo $this->lang->line('all') ?> <?php echo json_decode($role)->name; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="eventtype" value="protected" id="public"><?php echo $this->lang->line('protected') ?>
                                </label>
                            </div>
                            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                                <input type="submit" class="btn btn-primary submit_update pull-right" value="<?php echo $this->lang->line('save'); ?>">
                            </div>
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                                <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) { ?>
                                    <input type="button" id="delete_event" class="btn btn-primary submit_delete pull-right" value="<?php echo $this->lang->line('delete'); ?>">
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#viewEventModal,#newEventModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
        });
    </script>

    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script type="text/javascript">
        <?php if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view') && ($this->module_lib->hasActive('income'))) {
        ?>
            new Chart(document.getElementById("doughnut-chart"), {
                type: 'doughnut',
                data: {
                    labels: [<?php foreach ($incomegraph as $value) { ?> "<?php echo $value['income_category']; ?>", <?php } ?>],
                    datasets: [{
                        label: "Income",
                        backgroundColor: [<?php $s = 1;
                                            foreach ($incomegraph as $value) {
                                            ?> "<?php echo incomegraphColors($s++); ?>", <?php
                                                                                            if ($s == 8) {
                                                                                                $s = 1;
                                                                                            }
                                                                                        }
                                                                                            ?>],
                        data: [<?php $s = 1;
                                foreach ($incomegraph as $value) {
                                ?><?php echo $value['total']; ?>, <?php } ?>]
                    }]
                },
                options: {
                    responsive: true,
                    circumference: Math.PI,
                    rotation: -Math.PI,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        <?php
        }
        if (($this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) && ($this->module_lib->hasActive('expense'))) {
        ?>
            new Chart(document.getElementById("doughnut-chart1"), {
                type: 'doughnut',
                data: {
                    labels: [<?php foreach ($expensegraph as $value) { ?> "<?php echo $value['exp_category']; ?>", <?php } ?>],
                    datasets: [{
                        label: "Population (millions)",
                        backgroundColor: [<?php $ss = 1;
                                            foreach ($expensegraph as $value) {
                                            ?> "<?php echo expensegraphColors($ss++); ?>", <?php
                                                                                            if ($ss == 8) {
                                                                                                $ss = 1;
                                                                                            }
                                                                                        }
                                                                                            ?>],
                        data: [<?php foreach ($expensegraph as $value) { ?><?php echo $value['total']; ?>, <?php } ?>]
                    }]
                },
                options: {
                    responsive: true,
                    circumference: Math.PI,
                    rotation: -Math.PI,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        <?php
        }
        if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense')) || ($this->module_lib->hasActive('income'))) {
        ?>
            $(function() {
                var areaChartOptions = {
                    showScale: true,
                    scaleShowGridLines: false,
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    scaleGridLineWidth: 1,
                    scaleShowHorizontalLines: true,
                    scaleShowVerticalLines: true,
                    bezierCurve: true,
                    bezierCurveTension: 0.3,
                    pointDot: false,
                    pointDotRadius: 4,
                    pointDotStrokeWidth: 1,
                    pointHitDetectionRadius: 20,
                    datasetStroke: true,
                    datasetStrokeWidth: 2,
                    datasetFill: true,
                    maintainAspectRatio: true,
                    responsive: true
                };
                var bar_chart = "<?php echo $bar_chart ?>";
                var line_chart = "<?php echo $line_chart ?>";
                <?php
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) {
                ?>
                    if (line_chart) {

                        var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
                        var lineChart = new Chart(lineChartCanvas);
                        var lineChartOptions = areaChartOptions;
                        lineChartOptions.datasetFill = false;
                        var yearly_collection_array = <?php echo json_encode($yearly_collection) ?>;
                        var yearly_expense_array = <?php echo json_encode($yearly_expense) ?>;
                        var total_month = <?php echo json_encode($total_month) ?>;
                        var areaChartData_expense_Income = {
                            labels: total_month,
                            datasets: [
                                <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                        label: "Expense",
                                        fillColor: "rgba(215, 44, 44, 0.7)",
                                        strokeColor: "rgba(215, 44, 44, 0.7)",
                                        pointColor: "rgba(233, 30, 99, 0.9)",
                                        pointStrokeColor: "#c1c7d1",
                                        pointHighlightFill: "#fff",
                                        pointHighlightStroke: "rgba(220,220,220,1)",
                                        data: yearly_expense_array
                                    },
                                <?php } ?>
                                <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                        label: "Collection",
                                        fillColor: "rgba(102, 170, 24, 0.6)",
                                        strokeColor: "rgba(102, 170, 24, 0.6)",
                                        pointColor: "rgba(102, 170, 24, 0.9)",
                                        pointStrokeColor: "rgba(102, 170, 24, 0.6)",
                                        pointHighlightFill: "#fff",
                                        pointHighlightStroke: "rgba(60,141,188,1)",
                                        data: yearly_collection_array
                                    }
                                <?php } ?>
                            ]
                        };
                        lineChart.Line(areaChartData_expense_Income, lineChartOptions);
                    }

                    var current_month_days = <?php echo json_encode($current_month_days) ?>;
                    var days_collection = <?php echo json_encode($days_collection) ?>;
                    var days_expense = <?php echo json_encode($days_expense) ?>;
                    var areaChartData_classAttendence = {
                        labels: current_month_days,
                        datasets: [
                            <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                    label: "Electronics",
                                    fillColor: "rgba(102, 170, 24, 0.6)",
                                    strokeColor: "rgba(102, 170, 24, 0.6)",
                                    pointColor: "rgba(102, 170, 24, 0.6)",
                                    pointStrokeColor: "#c1c7d1",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(220,220,220,1)",
                                    data: days_collection
                                },
                            <?php }
                            if (($this->module_lib->hasActive('expense'))) { ?> {
                                    label: "Digital Goods",
                                    fillColor: "rgba(233, 30, 99, 0.9)",
                                    strokeColor: "rgba(233, 30, 99, 0.9)",
                                    pointColor: "rgba(233, 30, 99, 0.9)",
                                    pointStrokeColor: "rgba(233, 30, 99, 0.9)",
                                    pointHighlightFill: "rgba(233, 30, 99, 0.9)",
                                    pointHighlightStroke: "rgba(60,141,188,1)",
                                    data: days_expense
                                }
                            <?php } ?>
                        ]
                    };

                <?php }
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) { ?>
                    if (bar_chart) {
                        var current_month_days = <?php echo json_encode($current_month_days) ?>;
                        var days_collection = <?php echo json_encode($days_collection) ?>;
                        var days_expense = <?php echo json_encode($days_expense) ?>;

                        var areaChartData_classAttendence = {
                            labels: current_month_days,
                            datasets: [
                                <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                        label: "Electronics",
                                        fillColor: "rgba(102, 170, 24, 0.6)",
                                        strokeColor: "rgba(102, 170, 24, 0.6)",
                                        pointColor: "rgba(102, 170, 24, 0.6)",
                                        pointStrokeColor: "#c1c7d1",
                                        pointHighlightFill: "#fff",
                                        pointHighlightStroke: "rgba(220,220,220,1)",
                                        data: days_collection
                                    },
                                <?php } ?>
                                <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                        label: "Digital Goods",
                                        fillColor: "rgba(233, 30, 99, 0.9)",
                                        strokeColor: "rgba(233, 30, 99, 0.9)",
                                        pointColor: "rgba(233, 30, 99, 0.9)",
                                        pointStrokeColor: "rgba(233, 30, 99, 0.9)",
                                        pointHighlightFill: "rgba(233, 30, 99, 0.9)",
                                        pointHighlightStroke: "rgba(60,141,188,1)",
                                        data: days_expense
                                    }
                                <?php } ?>
                            ]
                        };
                        var barChartCanvas = $("#barChart").get(0).getContext("2d");
                        var barChart = new Chart(barChartCanvas);
                        var barChartData = areaChartData_classAttendence;
                        // barChartData.datasets[1].fillColor = "rgba(233, 30, 99, 0.9)";
                        // barChartData.datasets[1].strokeColor = "rgba(233, 30, 99, 0.9)";
                        // barChartData.datasets[1].pointColor = "rgba(233, 30, 99, 0.9)";
                        var barChartOptions = {
                            scaleBeginAtZero: true,
                            scaleShowGridLines: true,
                            scaleGridLineColor: "rgba(0,0,0,.05)",
                            scaleGridLineWidth: 1,
                            scaleShowHorizontalLines: false,
                            scaleShowVerticalLines: false,
                            barShowStroke: true,
                            barStrokeWidth: 2,
                            barValueSpacing: 5,
                            barDatasetSpacing: 1,
                            responsive: true,
                            maintainAspectRatio: true
                        };
                        barChartOptions.datasetFill = false;
                        barChart.Bar(barChartData, barChartOptions);
                    }
                <?php } ?>
            });
        <?php
        }
        ?>

        $(document).ready(function() {
            $(document).on('click', '.close_notice', function() {
                var data = $(this).data();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/notification/read",
                    data: {
                        'notice': data.noticeid
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.status == "fail") {

                            errorMsg(data.msg);
                        } else {
                            successMsg(data.msg);
                        }

                    }
                });
            });
        });
    </script>

<?php } ?>


<!--- Director Dashboard Starts --->
<?php if ($role_id == 9) { ?>

    <style>
        .menu-items a {
            background-color: #6b85bb54;
            border-radius: 30px;
            padding: 5px 18px;
            border: 1px solid #6b85bb26;
            text-align: center;
            display: block;
            margin: 15px auto;
            width: fit-content;
            color: #333;
            font-weight: 400;
            font-size: 18px;
            font-family: "Segoe UI", "Helvetica Neue", "Helvetica", "Lucida Grande", Arial, "Ubuntu", "Cantarell", "Fira Sans", sans-serif
        }

        .info-box-text {
            text-transform: capitalize !important;
        }

        .main-row {
            /*margin-top: 100px;*/
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>

    <div class="content-wrapper hide-desktop" style="min-height: 946px;">
        <section class="content">

            <div class="row main-row">

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('studentfee/fees_analysis_report') ?>">
                            <span class="info-box-text">Fees</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/enquiry/admissions_analysis_report') ?>">
                            <span class="info-box-text">Student Admissions</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/admin/attendance_analysis') ?>">
                            <span class="info-box-text">Attendance</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <!--<a href="<?php echo site_url('cbseexam/exam/exam_analysis') ?>">-->
                        <!--    <span class="info-box-text">Examinations</span>-->
                        <!--</a>-->

                        <a href="<?php echo site_url('cbseexam/report/index') ?>">
                            <span class="info-box-text">Examinations</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/staff/recruitment') ?>">
                            <span class="info-box-text">Recruitment</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('attendencereports/late_entries_analysis') ?>">
                            <span class="info-box-text">Late Marking</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/notification') ?>">
                            <span class="info-box-text">Notice Board</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/staff/gallery') ?>">
                            <span class="info-box-text">Gallery</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <!--<a href="<?php echo site_url('admin/route/transport_analysis') ?>">-->
                        <!--    <span class="info-box-text">Transport</span>-->
                        <!--</a>-->
                        <a href="<?php echo site_url('admin/route/studenttransportdetails') ?>">
                            <span class="info-box-text">Transport</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('admin/staff') ?>">
                            <span class="info-box-text">Staff Directory</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="menu-items">
                        <a href="<?php echo site_url('student/student_analysis') ?>">
                            <span class="info-box-text">Student Information System</span>
                        </a>
                    </div>
                </div>

            </div>

        </section>
    </div>

    <div class="content-wrapper hide-mobile">
        <section class="content">
            <div class="">

                <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
                    <div class="alert alert-danger">
                        Wisibles ERP may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
                    </div>
                <?php } ?>

                <?php
                $show    = false;
                $role    = $this->customlib->getStaffRole();
                $role_id = json_decode($role)->id;
                foreach ($notifications as $notice_key => $notice_value) {

                    if ($role_id == 7) {
                        $show = true;
                    } elseif (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notice_value->publish_date))) {
                        $show = true;
                    }
                    if ($show) {
                ?>
                        <div class="dashalert alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="alertclose close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>
                            <a href="<?php echo site_url('admin/notification') ?>"><?php echo $notice_value->title; ?></a>
                        </div>
                <?php
                    }
                }
                ?>
            </div>



            <div class="row hello-div row-reverse">
                <?php
                $bar_chart = true;

                if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                    if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) {

                        $div_rol  = 3;
                        $userdata = $this->customlib->getUserData();
                ?>
                        <div class="box-body-pt col-lg-9 col-md-9 col-sm-12">
                            <div class="search">
                                <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>

                                    <form id="header_search_form" class="search-dashboard navbar-form navbar-left search-form" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="input-group">
                                            <input type="text" value="<?php echo set_value('search_text1'); ?>" name="search_text1" id="search_text1" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                            <span class="input-group-btn">
                                                <button type="submit" name="search" id="search-btn" onclick="getstudentlist()" style="" class="btn btn-flat topsidesearchbtn"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>

                                    </form>
                                <?php } ?>

                            </div>
                            <?php
                            $file   = "";
                            $result = $this->customlib->getUserData();

                            $image = $result["image"];
                            $role  = $result["user_type"];
                            $id    = $result["id"];
                            if (!empty($image)) {

                                $file = "uploads/staff_images/" . $image . img_time();
                            } else {
                                if ($result['gender'] == 'Female') {
                                    $file = "uploads/staff_images/default_female.jpg" . img_time();
                                } else {
                                    $file = "uploads/staff_images/default_male.jpg" . img_time();
                                }
                            }
                            ?>
                            <div class="pt-80 row">

                                <div class="border-radius-20 box box-primary borderwhite">
                                    <div class="box-header-one box-header with-border ">
                                        <div class="col-lg-7 col-md-7 col-sm-6">
                                            <h3 class="hello-text box-title">Hello <?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                            <h4>Check your daily fee statements</h4>
                                            <a href="<?php echo site_url() ?>financereports/reportdailycollection" class="btn-check-now mt-10 btn btn-primary">Check Now</a>

                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-6">
                                            <img class="hell-img img-fluid" src="<?php echo site_url('backend/images/sidebar/hello_img.png') ?>">
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">

                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstart">
                                            <p class="text-blur"><?php echo date("M j, Y");; ?></p>
                                            <p class="mt5 clearfix font-16">Student Attendance<br>
                                                <span class="font12">Today</span>
                                            </p>
                                            <div class="box-header with-border">
                                                <div class="progress-group">
                                                    <div class="progress progress-minibar">
                                                        <div class="progress-bar progress-bar-maroon" style="width: <?php if ($total_students > 0) {
                                                                                                                        echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                                                    } ?>%"></div>
                                                    </div>
                                                    <p class="mt5 clearfix">Progress<span class="pull-right"> <?php if ($total_students > 0) {
                                                                                                                    echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                                                } ?>%</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt8">
                                                <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                <a class="d-inline mml20" href="<?php echo site_url('attendencereports/daily_attendance_report') ?>"><i class="fa fa-plus ftlayer-maroon"></i></a>
                                                <span class="pull-right text-maroon-bg"> <?php echo 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present']; ?>/<?php echo $total_students; ?></span>

                                            </div>
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->
                            <?php }
                    }
                            ?>

                            <?php
                            //if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) {
                            ?>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="topprograssstart">
                                    <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                    <p class="mt5 clearfix font-16">Staff Attendance<br>
                                        <span class="font12">Today</span>
                                    </p>
                                    <div class="box-header with-border">
                                        <div class="progress-group">
                                            <div class="progress progress-minibar">
                                                <div class="progress-bar progress-bar-blue" style="width: <?php echo $percentTotalStaff_data; ?>%"></div>
                                            </div>
                                            <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo $percentTotalStaff_data; ?>%</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt8">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <a class="d-inline mml20" href="<?php echo site_url('attendencereports/staffattendancereport') ?>"><i class="fa fa-plus ftlayer-blue"></i></a>
                                        <span class="pull-right text-blue-bg"> <?php echo $Staffattendence_data + 0; ?>/<?php echo $getTotalStaff_data; ?></span>

                                    </div>
                                </div><!--./topprograssstart-->
                            </div><!--./col-md-3-->
                            <?php
                            // }

                            ?>


                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="topprograssstart">
                                    <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                    <p class="mt5 clearfix font-16">Fee Collection<br>
                                        <span class="font12">Today</span>
                                    </p>
                                    <div class="box-header with-border">
                                        <div class="progress-group">
                                            <div class="progress progress-minibar">
                                                <div class="progress-bar progress-bar-orange" style="width: <?php echo $fessprogressbar; ?>%"></div>
                                            </div>
                                            <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo number_format($fessprogressbar, 2); ?>%</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt8">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                        <a class="d-inline mml20" href="<?php echo site_url('studentfee') ?>"><i class="fa fa-plus ftlayer-orange"></i></a>
                                        <span class="pull-right text-orange-bg"> <?php echo $total_paid; ?>/<?php echo $total_fees ?></span>
                                    </div>
                                </div><!--./topprograssstart-->
                            </div><!--./col-md-3-->


                                </div><!--./row-->
                            </div>
                        </div><!--./col-lg-7-->


                        <div class="mt-10 col-lg-3 col-md-3 col-sm-12">
                            <div class="hide-on-mobile border-radius-20 div-user-infomain mt-15 box box-primary">
                                <div class="box-widget widget-user-2 mb0">
                                    <div class="text-right  admin-edit">
                                        <a href="<?php echo base_url() . "admin/staff/edit/" . $id ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-pencil"></i></a>

                                    </div>

                                    <div class="widget-user-header overflow-hidden">

                                        <div class="div-user-info">
                                            <h5 class="ml-0 widget-user-desc mb5"><?php echo $role; ?></h5>

                                            <h3 class="ml-0 widget-user-username"><?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                            <h5 class="ml-0 mt-20 view-profile widget-user-desc"><a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>" style="color:#9854cb;">View Profile <i class="ml fa fa-check-square-o"></i></a></h5>
                                        </div>

                                        <div class="widget-user-image">
                                            <img style="box-shadow: none;" class="profile-user-img img-responsive img-rounded" src="<?php echo site_url('backend/images/sidebar/admin-orange-img.png') ?>" alt="User profile picture">
                                        </div>

                                    </div>
                                </div>


                            </div>
                            <div class="border-radius-20 box box-primary pt-10 ptb-20">
                                <h4 class="text-center pb-10 fee-summary-title">Fee Summary</h4>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/12.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Students Fees</span>
                                            <span class="info-box-number">Rs. <?php echo amountFormat($feesummarData['totalfee']) ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/13.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Paid Fees</span>
                                            <span class="info-box-number">Rs.<?php echo amountFormat($feesummarData['deposit']) ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="info-box">
                                    <a href="#">
                                        <span class="back-none info-box-icon">
                                            <img class="width25 img-fluid" src="<?php echo site_url('backend/images/sidebar/14.png') ?>">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Balance Fees</span>
                                            <span class="info-box-number">Rs.<?php echo amountFormat($feesummarData['balance']) ?></span>
                                        </div>
                                    </a>
                                </div>

                            </div>


                        </div>
            </div><!--./col-lg-5-->

    </div><!--./row-->

<?php } ?>

<!--- Director Dashboard Ends --->

<!--- Fee agent Dashboard Starts --->


<?php if ($role_id == 8) { ?>

    <div class="content-wrapper" style="min-height: 946px;">
        <section class="content">

            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Fee Agent Dashboard</h3>
                <hr style="border-top:1px solid #333">
            </div>

            <div class="row mt20">
                <div class="col-md-4 col-sm-6">
                    <div class="info-box">
                        <a href="#">
                            <span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">First Term</span>
                                <span class="info-box-number"><?php echo $result['first_term']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="info-box">
                        <a href="#">
                            <span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Second Term</span>
                                <span class="info-box-number"><?php echo $result['second_term']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="info-box">
                        <a href="#">
                            <span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Third Term</span>
                                <span class="info-box-number"><?php echo $result['third_term']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="info-box">
                        <a href="#">
                            <span class="info-box-icon bg-red"><i class="fa fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid Students</span>
                                <span class="info-box-number"><?php echo $result['paid']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="info-box">
                        <a href="#">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Unpaid Students</span>
                                <span class="info-box-number"><?php echo $result['unpaid']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

        </section>
    </div>

<?php } ?>

<!--- Fee agent Dashboard Ends --->


<!-- teacher dashboard -->
<?php if ($role_id == 2) { ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="">

                <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
                    <div class="alert alert-danger">
                        Wisibles ERP may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
                    </div>
                <?php } ?>

                <?php
                $show    = false;
                $role    = $this->customlib->getStaffRole();
                $role_id = json_decode($role)->id;
                foreach ($notifications as $notice_key => $notice_value) {

                    if ($role_id == 7) {
                        $show = true;
                    } elseif (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notice_value->publish_date))) {
                        $show = true;
                    }
                    if ($show) {
                ?>
                        <div class="dashalert alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="alertclose close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>
                            <a href="<?php echo site_url('admin/notification') ?>"><?php echo $notice_value->title; ?></a>
                        </div>
                <?php
                    }
                }
                ?>
            </div>



            <div class="row hello-div">
                <?php
                $bar_chart = true;



                $div_rol  = 3;
                $userdata = $this->customlib->getUserData();
                ?>
                <div class="box-body-pt col-lg-9 col-md-9 col-sm-12">
                    <div class="search">
                        <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>

                            <form id="header_search_form" class="search-dashboard navbar-form navbar-left search-form" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="input-group">
                                    <input type="text" value="<?php echo set_value('search_text1'); ?>" name="search_text1" id="search_text1" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                    <span class="input-group-btn">
                                        <button type="submit" name="search" id="search-btn" onclick="getstudentlist()" style="" class="btn btn-flat topsidesearchbtn"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>

                            </form>
                        <?php } ?>

                    </div>
                    <?php
                    $file   = "";
                    $result = $this->customlib->getUserData();

                    $image = $result["image"];
                    $role  = $result["user_type"];
                    $id    = $result["id"];
                    if (!empty($image)) {

                        $file = "uploads/staff_images/" . $image . img_time();
                    } else {
                        if ($result['gender'] == 'Female') {
                            $file = "uploads/staff_images/default_female.jpg" . img_time();
                        } else {
                            $file = "uploads/staff_images/default_male.jpg" . img_time();
                        }
                    }
                    ?>
                    <div class="pt-80 row">

                        <div class="border-radius-20 box box-primary borderwhite">
                            <div class="box-header-one box-header with-border ">
                                <div class="col-lg-7 col-md-7 col-sm-6">
                                    <h3 class="hello-text box-title">Hello <?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                    <h4>Check your daily fee statements</h4>
                                    <a href="<?php echo site_url() ?>financereports/reportdailycollection" class="btn-check-now mt-10 btn btn-primary">Check Now</a>

                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-6">
                                    <img class="hell-img img-fluid" src="<?php echo site_url('backend/images/sidebar/hello_img.png') ?>">
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">


                            <?php /*
                                    //if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) {
                                    ?>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="topprograssstart">
                                                <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                                <p class="mt5 clearfix font-16">Staff Attendance<br>
                                                    <span class="font12">Today</span>
                                                </p>
                                                <div class="box-header with-border">
                                                    <div class="progress-group">
                                                        <div class="progress progress-minibar">
                                                            <div class="progress-bar progress-bar-blue" style="width: <?php echo $percentTotalStaff_data; ?>%"></div>
                                                        </div>
                                                        <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo $percentTotalStaff_data; ?>%</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="mt8">
                                                    <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                    <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                    <a class="d-inline mml20" href="<?php echo site_url('attendencereports/staffattendancereport') ?>"><i class="fa fa-plus ftlayer-blue"></i></a>
                                                    <span class="pull-right text-blue-bg"> <?php echo $Staffattendence_data + 0; ?>/<?php echo $getTotalStaff_data; ?></span>

                                                </div>
                                            </div><!--./topprograssstart-->
                                        </div><!--./col-md-3-->
                                    <?php */
                            //}

                            ?>

                            <?php /*
                                    //if ($this->module_lib->hasActive('fees_collection')) {
                                    //    if ($this->rbac->hasPrivilege('fees_awaiting_payment_widegts', 'can_view')) {
                                    ?>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="topprograssstart">
                                                    <p class="text-blur"><?php echo date("M j, Y"); ?></p>
                                                    <p class="mt5 clearfix font-16">Fee Collection<br>
                                                        <span class="font12">Today</span>
                                                    </p>
                                                    <div class="box-header with-border">
                                                        <div class="progress-group">
                                                            <div class="progress progress-minibar">
                                                                <div class="progress-bar progress-bar-orange" style="width: <?php echo $fessprogressbar; ?>%"></div>
                                                            </div>
                                                            <p class="mt5 clearfix">Progress<span class="pull-right"> <?php echo $fessprogressbar; ?>%</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mt8">
                                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                        <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                                                        <a class="d-inline mml20" href="<?php echo site_url('studentfee') ?>"><i class="fa fa-plus ftlayer-orange"></i></a>
                                                        <span class="pull-right text-orange-bg"> <?php echo $total_paid; ?>/<?php echo $total_fees ?></span>
                                                    </div>
                                                </div><!--./topprograssstart-->
                                            </div><!--./col-md-3-->
                                    <?php */
                            //    }
                            //}
                            ?>

                        </div><!--./row-->
                    </div>
                </div><!--./col-lg-7-->

                <?php
                //if ($this->module_lib->hasActive('income')) {
                //    if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view')) {
                ?>
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="border-radius-20 div-user-infomain mt-15 box box-primary">
                        <div class="box-widget widget-user-2 mb0">
                            <div class="text-right  admin-edit">
                                <a href="<?php echo base_url() . "admin/staff/edit/" . $id ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-pencil"></i></a>

                            </div>

                            <div class="widget-user-header overflow-hidden">

                                <div class="div-user-info">
                                    <h5 class="ml-0 widget-user-desc mb5"><?php echo $role; ?></h5>

                                    <h3 class="ml-0 widget-user-username"><?php echo $this->customlib->getAdminSessionUserName(); ?></h3>
                                    <h5 class="ml-0 mt-20 view-profile widget-user-desc"><a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>" style="color:green;">View Profile <i class="ml fa fa-check-square-o"></i></a></h5>
                                </div>

                                <div class="widget-user-image">
                                    <img style="box-shadow: none;" class="profile-user-img img-responsive img-rounded" src="<?php echo site_url('backend/images/sidebar/admin-orange-img.png') ?>" alt="User profile picture">
                                </div>

                            </div>
                        </div>


                    </div>

                    <div class="topprograssstart">
                        <p class="text-blur"><?php echo date("M j, Y");; ?></p>
                        <p class="mt5 clearfix font-16">Student Attendance<br>
                            <span class="font12">Today</span>
                        </p>
                        <div class="box-header with-border">
                            <div class="progress-group">
                                <div class="progress progress-minibar">
                                    <div class="progress-bar progress-bar-maroon" style="width: <?php if ($total_students > 0) {
                                                                                                    echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                                } ?>%"></div>
                                </div>
                                <p class="mt5 clearfix">Progress<span class="pull-right"> <?php if ($total_students > 0) {
                                                                                                echo (0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'] / $total_students * 100);
                                                                                            } ?>%</span>
                                </p>
                            </div>
                        </div>
                        <div class="mt8">
                            <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                            <img src="https://newlayout.wisibles.com/uploads/staff_images/default_male.jpg?1738403429" class="user-image-overlap" alt="User Image">
                            <a class="d-inline mml20" href="<?php echo site_url('attendencereports/daily_attendance_report') ?>"><i class="fa fa-plus ftlayer-maroon"></i></a>
                            <span class="pull-right text-maroon-bg"> <?php echo 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present']; ?>/<?php echo $total_students; ?></span>

                        </div>
                    </div><!--./topprograssstart-->





                </div>
            </div><!--./col-lg-5-->
            <?php
            //        }
            //    }
            ?>
    </div><!--./row-->

<?php } ?>