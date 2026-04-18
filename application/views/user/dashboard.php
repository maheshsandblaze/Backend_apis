    <style>
        
        .stdimg {
            position: absolute;
            top: -20px;
        }
        
        .stdimg img {
            width:80px;
            border-radius: 50%;
        }
        
        .stdinfo    {
            margin-top: 30px;
            padding: 0px 30px;
            background-color: #ccc;
        }
        .stdname p  {
            margin: 0 0px 0px;
            font-size: 11px;
        }
        .stdname h4 {
            margin-bottom: 5px;
        }
        .mt-3   {
            margin-top: 3px;
        }
        .box-header {
            padding: 8px;
        }
        .icons  {
            margin-top:40px;
        }
        .icons1 {
            margin-top: 0px;
        }
        .iconscnt   {
            padding: 10px 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 71px;
        }
        .iconscnt img   {
            width: 30px;
        }
        .iconscnt a {
            color: #333;
        }
        .iconscnt p {
            font-weight: 800;
            margin-top: 3px;
            white-space: normal; 
            word-wrap: break-word;
            font-size: 11px;
            margin: 0 0 0px;
        }
    </style>

<div class="content-wrapper">
	<section class="content pb0 hide-paddingon-mobile">
	    <div class="hide-mobile">
		<div class="row">
			<div class="box-body-pt col-lg-8 col-md-8 col-sm-12">
				
				<div class="pt-80 row">

					<div class="border-radius-20 box box-primary borderwhite">
						<div class="box-header-one box-header with-border ">
							<div class="col-lg-7 col-md-7 col-sm-6">
								<h3 class="hello-text box-title">Hello <?php echo $this->customlib->getStudentSessionUserName(); ?></h3>
								<h4>Check your personalized profile</h4>
								<a href="<?php echo base_url() ?>/user/user/profile" class="btn-check-now mt-10 btn btn-primary">Check Now</a>

							</div>
							<div class="col-lg-5 col-md-5 col-sm-6">
								<img class="hell-img img-fluid" src="<?php echo base_url() ?>backend/images/sidebar/hello_img.png">
							</div>

						</div>

					</div>
				</div>

			</div>
			<div class="pt-80 col-lg-4 col-md-4 col-sm-12">
				<div class="box box-primary borderwhite">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $this->lang->line('notice_board'); ?></h3>
					</div>
					<div class="box-body pb0">
						<?php if (!empty($notificationlist)) { ?>
							<ul class="user-progress ps mb0">
								<?php for ($i = 0; $i < 4; $i++) {
									$notification = array();
									if (!empty($notificationlist[$i])) {
										$notification = $notificationlist[$i];
									}
								?>
									<?php if (!empty($notification)) { ?>
										<li class="doc-file-type">
											<div class="set-flex">
												<div class="media-title"><?php if (!empty($notification)) { ?>
														<a href="<?php echo base_url(); ?>user/notification" class="displayinline text-muted" target="_blank">

															<?php if ($notification['notification_id'] == "read") { ?>
																<img src="<?php echo base_url() ?>/backend/images/read_one.png">
															<?php } else { ?>
																<img src="<?php echo base_url() ?>backend/images/unread_two.png">
															<?php } ?>

															&nbsp;<?php echo $notification['title']; ?> (<?php if (!empty($notification)) {
																												echo "<i class='fa fa-clock-o text-aqua'></i>" . ' ' . date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date']));
																											} ?>)
														</a><?php } ?>
												</div>

											</div>

										</li><!-- /.item -->
								<?php }
								} ?>

							</ul>
						<?php } else { ?>
							<img src="https://smart-school.in/ssappresource/images/addnewitem.svg" width="150" class="center-block mt20">
						<?php } ?>
					</div>
				</div>
			</div><!--./col-lg-6-->
		</div><!--./row-->

		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-12">
				<div class="box box-primary borderwhite">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $this->lang->line('subject_progress'); ?></h3>
					</div>
					<div class="box-body direct-chat-messages">
						<div class="table-responsive">
							<?php if (!empty($subjects_data)) {  ?>
								<table class="table table-striped table-hover">
									<tr class="active">
										<th><?php echo $this->lang->line('subject'); ?></th>
										<th><?php echo $this->lang->line('progress'); ?></th>
										<!-- <th>Duration</th> -->
									</tr>
									<?php
									foreach ($subjects_data as $key => $value) {
									?>
										<tr>
											<td><?php echo $value['lebel']; ?></td>
											<td><?php echo $value['complete']; ?>%
												<div class="progress progress-minibar">
													<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow=""
														aria-valuemin="0" aria-valuemax="100" style="width:<?php if ($value['complete'] != 0) {
																												echo $value['complete'];
																											} ?>%">
													</div>
												</div>
											</td>
											<!-- <td>2 Months</td> -->
										</tr>
									<?php }  ?>

								</table>
							<?php } else {  ?>
								<img src="https://smart-school.in/ssappresource/images/addnewitem.svg" width="150" class="center-block mt20">
							<?php } ?>
						</div>
					</div>
				</div>
			</div><!--./col-lg-4-->

			<div class="col-lg-4 col-md-4 col-sm-12">
				<div class="box box-primary borderwhite">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $this->lang->line('upcomming_class'); ?></h3>
					</div>
					<div class="box-body direct-chat-messages">

						<?php if (!empty($timetable)) { ?>
							<ul class="user-progress">

								<?php
								foreach ($timetable as $tm_key => $tm_value) {

									if (!$timetable[$tm_key]) {
								?>
										<?php } else {
										for ($i = 0; $i < 5; $i++) {

											$timetablelist = array();
											if (!empty($timetable[$tm_key][$i])) {

												$timetablelist = $timetable[$tm_key][$i];
											}

											if (!empty($timetablelist)) { ?>
												<li class="lecture-list">

													<?php $profile_pic = '';
													if ($timetablelist->image != '') {
														$profile_pic = 'uploads/staff_images/' . $timetablelist->image;
													} else {
														if ($timetablelist->gender == 'Male') {
															$profile_pic = 'uploads/staff_images/default_male.jpg';
														} else {
															$profile_pic = 'uploads/staff_images/default_female.jpg';
														}
													} ?>
													<img src="<?php echo base_url(); ?><?php echo $profile_pic . img_time(); ?>" alt="" class="img-circle msr-3 object-fit-cover fit-image-40" width="40" height="40">

													<div class="set-flex">
														<div class="media-title bmedium"><?php echo $timetablelist->name . ' ' . $timetablelist->surname . ' (' . $timetablelist->employee_id . ')'; ?>
														</div>
														<div class="text-muted mb0">
															<?php
															if (!empty($timetablelist)) {
																echo $timetablelist->subject_name;
																if ($timetablelist->code != '') {
																	echo " (" . $timetablelist->code . ")";
																}
															}
															?>
														</div>
													</div>
													<div class="ms-auto">
														<div class="bmedium"><?php echo $this->lang->line('room_no'); ?>:<?php echo $timetablelist->room_no; ?></div>
														<div class="text-muted mb0"><?php echo $timetablelist->time_from ?>-<?php echo $timetablelist->time_to; ?></div>
													</div>
												</li>
								<?php }
										}
									}
								}  ?>

							</ul>
						<?php } else {  ?>
							<img src="https://smart-school.in/ssappresource/images/addnewitem.svg" width="150" class="center-block mt20">
						<?php } ?>
					</div>
				</div>
			</div><!--./col-lg-4-->

			<div class="col-lg-4 col-md-4 col-sm-12">
				<div class="box box-primary borderwhite">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $this->lang->line('homework'); ?></h3>
					</div>

					<div class="box-body direct-chat-messages">

						<?php if (!empty($homeworklist)) { ?>
							<ul class="user-progress ps">
								<?php for ($i = 0; $i < 5; $i++) {
									$homework = array();
									if (!empty($homeworklist[$i])) {
										$homework = $homeworklist[$i];
									}
								?>
									<?php if (!empty($homework)) { ?>
										<li class="doc-file-type">
											<div class="set-flex">
												<div class="media-title font-16"><?php if (!empty($homework)) { ?><a href="<?php echo base_url(); ?>user/homework" class="displayinline text-muted" target="_blank"><?php echo $homework['subject_name']; ?> (<?php echo $homework['subject_code']; ?>)
														</a><?php } ?></div>
												<div class="text-muted mb0"><?php if (!empty($homework)) {
																				echo $this->lang->line('homework_date') . ': ' . date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($homework['homework_date'])) . ',';
																			} ?> <?php if (!empty($homework)) {
																																																																							echo $this->lang->line('submission_date') . ': ' . date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($homework['submit_date'])) . ',';
																																																																						} ?> <?php if (!empty($homework)) {

																																																																																																																										if ($homework["status"] == 'submitted') {
																																																																																																																											$status_class = "class= 'label label-warning'";
																																																																																																																											$status_homework = $this->lang->line('submitted');
																																																																																																																										} else {
																																																																																																																											$status_class = "class= 'label label-danger'";
																																																																																																																											$status_homework = $this->lang->line("pending");
																																																																																																																										}

																																																																																																																										if ($homework["homework_evaluation_id"] != 0) {

																																																																																																																											$status_class = "class= 'label label-success'";
																																																																																																																											$status_homework = $this->lang->line("evaluated");
																																																																																																																										}

																																																																																																																										echo $this->lang->line('status') . ': ';
																																																																																																																									?>
														<label <?php echo $status_class; ?>><?php echo $status_homework; ?></label>
													<?php
																																																																																																																									}
													?>

												</div>
											</div>
										</li><!-- /.item -->
								<?php }
								} ?>
							</ul>
						<?php } else { ?>
							<img src="https://smart-school.in/ssappresource/images/addnewitem.svg" width="150" class="center-block mt20">
						<?php } ?>
					</div>
				</div>
			</div><!--./col-lg-4-->
		</div><!--./row-->
		</div>
		
		<div class="hide-desktop">
		    <div class="row stdinfo">
                
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <?php
                        $sch_setting = $this->setting_model->getSetting();
                        $student_data = $this->customlib->getLoggedInUserData();

                                if (!empty($student_data["image"])) {
                                    if ($student_data['role'] == 'guest') {
                                        $file = base_url() . "uploads/guest_images/" . $student_data["image"] . img_time();
                                    } else {
                                        $file = base_url() . $student_data["image"] . img_time();
                                    }
                                } else {
                                    if ($student_data['gender'] == 'Female') {
                                        $file = base_url() . "uploads/student_images/default_female.jpg" . img_time();
                                    } elseif ($student_data['gender'] == 'Male') {
                                        $file = base_url() . "uploads/student_images/default_male.jpg" . img_time();
                                    } else {
                                        $file = base_url() . "uploads/student_images/no_image.png";
                                    }
                                }
                    ?>
                    
                    <div class="stdimg">
                                                    
                                                        <?php if ($sch_setting->student_photo) {
                                                        ?>
                                                            <img src="<?php echo $file . img_time(); ?>" alt="User Image">
                                                <?php } ?>

                                                </div>
                </div>
                
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <div class="stdname">
                        <h4 style="text-transform: capitalize; font-weight:800"><?php echo $this->customlib->getStudentSessionUserName(); ?></h4>
                        <div class="box-header">
                            <p class="sspass">Adm No: <span><?php echo $student['admission_no']; ?></span></p>
                            <div class="box-tools pull-right mt-3">
                                <p class="sspass"><?php echo $this->lang->line('class'); ?>: <span><?php echo $student['class'] . " (" . $student['section'] . ")"; ?></span></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <div class="row icons">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/homework">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_assignment.png') ?>" alt="icons">
                            <p>Homework</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/attendence">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_nav_attendance.png') ?>" alt="icons">
                            <p>Attendance</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/user/getfees">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_nav_fees.png') ?>" alt="icons">
                            <p>Fees</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/apply_leave">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_leave.png') ?>" alt="icons">
                            <p>Leave</p>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row icons1">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/timetable">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_calender.png') ?>" alt="icons">
                            <p>Timetable</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/syllabus">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_lessonplan.png') ?>" alt="icons">
                            <p>Lesson</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/content/list">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_downloadcenter.png') ?>" alt="icons">
                            <p>Download</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/notification">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_notice.png') ?>" alt="icons">
                            <p>Notice</p>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row icons1">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/route">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_nav_transport.png') ?>" alt="icons">
                            <p>Transport</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="iconscnt box box-primary">
                        <a href="<?php echo base_url(); ?>user/cbse/exam/result">
                            <img src="<?php echo site_url('uploads/student_images/parent_app/ic_nav_reportcard.png') ?>" alt="icons">
                            <p>Examination</p>
                        </a>
                    </div>
                </div>
            </div>
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/content/gallery') ?>">-->
                <!--            <span class="info-box-text">Gallery</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/user/getfees') ?>">-->
                <!--            <span class="info-box-text">Fees</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/timetable') ?>">-->
                <!--            <span class="info-box-text">Class Timetable</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                        <!--<a href="<?php echo site_url('cbseexam/exam/exam_analysis') ?>">-->
                        <!--    <span class="info-box-text">Examinations</span>-->
                        <!--</a>-->
                        
                <!--        <a href="<?php echo site_url('user/syllabus') ?>">-->
                <!--            <span class="info-box-text">Lesson Plan</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/syllabus/status') ?>">-->
                <!--            <span class="info-box-text">Syllabus Status</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/homework') ?>">-->
                <!--            <span class="info-box-text">Homework</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/onlinecourse') ?>">-->
                <!--            <span class="info-box-text">Courses</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/apply_leave') ?>">-->
                <!--            <span class="info-box-text">Apply Leave</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                        <!--<a href="<?php echo site_url('admin/route/transport_analysis') ?>">-->
                        <!--    <span class="info-box-text">Transport</span>-->
                        <!--</a>-->
                <!--        <a href="<?php echo site_url('user/visitors') ?>">-->
                <!--            <span class="info-box-text">Visitor Book</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/content/list') ?>">-->
                <!--            <span class="info-box-text">Download Center</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/attendence') ?>">-->
                <!--            <span class="info-box-text">Attendance</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/notification') ?>">-->
                <!--            <span class="info-box-text">Notice Board</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/teacher') ?>">-->
                <!--            <span class="info-box-text">Teachers Reviews</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/book') ?>">-->
                <!--            <span class="info-box-text">Library</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="col-md-3 col-sm-6">-->
                <!--    <div class="menu-items">-->
                <!--        <a href="<?php echo site_url('user/route') ?>">-->
                <!--            <span class="info-box-text">Transport Routes</span>-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->

            
		</div>

	
	</section>
</div>