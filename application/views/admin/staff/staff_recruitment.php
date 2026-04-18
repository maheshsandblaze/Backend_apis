<style>
	.p-3 {
    padding: 20px;
	}
	.mb-4 {
    margin-bottom: 20px;
	}

</style>


<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
	<?php
        $role = $this->customlib->getStaffRole(); 
            $rname = json_decode($role)->name;
        ?>
		<?php if($rname == "Super Admin" || $rname == "Director"){ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                        <div class="box-tools pull-right">
                            <small class="pull-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addRecruitmentModal">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php  } ?>

        <!-- Recruitment List - Desktop -->
        <div class="row hide-mobile">
            <div class="col-md-2">
                <div class="box border0">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('front_office'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('staff', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('HR/staff'); ?>">
                                <a class="<?php echo set_Submenu('HR/staff'); ?>" href="<?php echo base_url(); ?>admin/staff"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('staff_directory'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/staffattendance'); ?>">
                                <a class="<?php echo set_Submenu('admin/staffattendance'); ?>" href="<?php echo base_url(); ?>admin/staffattendance"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/2.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('staff_attendance'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/payroll'); ?>">
                                <a class="<?php echo set_Submenu('admin/payroll'); ?>" href="<?php echo base_url(); ?>admin/payroll"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/3.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('payroll'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/leaverequest/leaverequest'); ?>">
                                <a class="<?php echo set_Submenu('admin/leaverequest/leaverequest'); ?>" href="<?php echo base_url(); ?>admin/leaverequest/leaverequest"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/4.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('approve_leave_request'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('apply_leave', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/staff/leaverequest'); ?>">
                                <a class="<?php echo set_Submenu('admin/staff/leaverequest'); ?>" href="<?php echo base_url(); ?>admin/staff/leaverequest"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/5.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('apply_leave'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('leave_types', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/leavetypes'); ?>">
                                <a class="<?php echo set_Submenu('admin/leavetypes'); ?>" href="<?php echo base_url(); ?>admin/leavetypes"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/6.png') ?>" alt="icon6" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('leave_type'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('teachers_rating', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('HR/rating'); ?>">
                                <a class="<?php echo set_Submenu('HR/rating'); ?>" href="<?php echo base_url(); ?>admin/staff/rating"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/7.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('teachers') . " " . $this->lang->line('rating'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('department', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/department/department'); ?>">
                                <a class="<?php echo set_Submenu('admin/department/department'); ?>" href="<?php echo base_url(); ?>admin/department/department"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/8.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('department'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('designation', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/designation/designation'); ?>">
                                <a class="<?php echo set_Submenu('admin/designation/designation'); ?>" href="<?php echo base_url(); ?>admin/designation/designation"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/9.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('designation'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('disable_staff', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('HR/staff/disablestafflist'); ?>">
                                <a class="<?php echo set_Submenu('HR/staff/disablestafflist'); ?>" href="<?php echo base_url(); ?>admin/staff/disablestafflist"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/88.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('disabled_staff'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('HR/staff/recruitment'); ?>">
                                <a class="<?php echo set_Submenu('HR/staff/recruitment'); ?>" href="<?php echo base_url(); ?>admin/staff/recruitment"><img src="<?php echo base_url('backend/images/sidebar/submenu/human_resource/1.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> Staff Recruitment</a>
                            </li>
                        <?php
                        }
                        ?>

                    </ul>
                </div>
            </div><!--./col-md-3-->
            
            <div class="col-md-10">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recruitment Openings</h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($recruitmentresult)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>No. of Openings</th>
                                            <th>Description</th>
                                            <th>Status</th>
											<?php
												$role = $this->customlib->getStaffRole(); 
													$rname = json_decode($role)->name;
												?>
												<?php  if($rname == "Super Admin"){ ?>
                                            <th>Action</th>
											
											<?php  } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $counter = 1; ?>
                                        <?php foreach ($recruitmentresult as $row) { ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                                <td><?php echo htmlspecialchars($row['openings']); ?></td>
                                                <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                                                <td>
													<?php 
													if ($row['status'] == 1) {
														echo '<span class="text-success">Open</span>';
													} elseif ($row['status'] == 0) {
														echo '<span class="text-danger">Closed</span>';
													} elseif ($row['status'] == 2) {
														echo '<span class="text-primary">Pending</span>';
													}
													?>
												</td>
											<?php
												$role = $this->customlib->getStaffRole(); 
													$rname = json_decode($role)->name;
												?>
												<?php  if($rname == "Super Admin"){ ?>
											   <td>
													<a href="<?php echo base_url('admin/staff/recruitment_delete/' . $row['id']); ?>" 
														class="btn btn-danger btn-sm" 
														onclick="return confirm('Are you sure you want to delete this recruitment?');">
														<i class="fa fa-trash"></i>
													</a>
												</td>
												<?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-center">No recruitment openings available.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- Mobile -->
		<div class="row hide-desktop">
			<div class="col-md-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Recruitment Openings</h3>
						<div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
					</div>
					<div class="box-body">
						<?php if (!empty($recruitmentresult)) { ?>
							<div class="row">
								<?php foreach ($recruitmentresult as $row) { ?>
									<div class="col-md-4 p-4">
										<div class="card shadow-sm border rounded p-3 mb-4">
											<h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($row['name']); ?></h5>
											<p class="card-text">
												<strong>Position:</strong> <?php echo htmlspecialchars($row['position']); ?><br>
												<strong>Openings:</strong> <?php echo htmlspecialchars($row['openings']); ?><br>
												<strong>Description:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?>
											</p>
											<p>
												<strong>Status:</strong> 
												<?php 
												if ($row['status'] == 1) {
													echo '<span class="text-success">Open</span>';
												} elseif ($row['status'] == 0) {
													echo '<span class="text-danger">Closed</span>';
												} elseif ($row['status'] == 2) {
													echo '<span class="text-primary">Pending</span>';
												}
												?>
											</p>
											<?php  
											$role = $this->customlib->getStaffRole(); 
											$rname = json_decode($role)->name;
											if($rname == "Super Admin"){ ?>
												<a href="<?php echo base_url('admin/staff/recruitment_delete/' . $row['id']); ?>" 
													class="btn btn-danger btn-sm" 
													onclick="return confirm('Are you sure you want to delete this recruitment?');">
													<i class="fa fa-trash"></i> Delete
												</a>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<p class="text-center">No recruitment openings available.</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>


		
    </section>
</div>

<!--  Modal -->
<div class="modal fade" id="addRecruitmentModal" tabindex="-1" role="dialog" aria-labelledby="addRecruitmentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addRecruitmentModalLabel">Add Recruitment</h4>
            </div>
            <form action="<?php echo base_url('admin/staff/recruitment'); ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                    </div>

                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" class="form-control" id="position" name="position" placeholder="Enter Position" required>
                    </div>

                    <div class="form-group">
                        <label for="no_of_openings">No. of Openings</label>
                        <input type="number" class="form-control" id="no_of_openings" name="no_of_openings" placeholder="Enter Number of Openings" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter Description" required></textarea>
                    </div>
					
					<div class="form-group">
						<label for="status">Status</label>
						<select class="form-control" id="status" name="status" required>
							<option value="1">Open</option>
							<option value="0">Close</option>
							<option value="2">Pending</option>
						</select>
					</div>
					
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
