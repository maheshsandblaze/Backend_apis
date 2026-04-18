<div class="content-wrapper">
	<section class="content pb0 hide-paddingon-mobile">
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

	
	</section>
</div>