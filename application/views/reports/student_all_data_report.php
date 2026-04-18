<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-line-chart"></i> <?php //echo $this->lang->line('reports'); 
                                                ?> <small> <?php //echo $this->lang->line('filter_by_name1'); 
                                                                                                    ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_studentinformation'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <!-- <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div> -->
           
          
                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('student_all_data_report'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"> <?php echo $this->lang->line('student_report'); ?></div>
                            <div>
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('student_report'); ?>">
                                    <thead>
                                        <tr>
                                            <th>SNO</th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('roll_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>

                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <th><?php echo $this->lang->line('religion'); ?></th>
                                            <th><?php echo $this->lang->line('caste'); ?></th>
                                            <th><?php echo $this->lang->line('blood_group') ?></th>
                                            <th><?php echo $this->lang->line('height') ?></h>
                                            <th><?php echo $this->lang->line('weight') ?></th>
                                            <th><?php echo $this->lang->line('previous_school') ?></th>

                                            <th>Aadhar Number</th>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                        
                                            <th><?php echo $this->lang->line('father_name'); ?></th>

                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                            <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                            <th><?php echo $this->lang->line('current_address'); ?></th>
                                            <th>Child ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($student_data as $data) {
                                            
                                            // echo "<pre>";
                                            // print_r($data);
                                  

                                        
                                        ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $data['class'] . "(" . $data['section'] . ")"; ?></td>
                                                <td><?php echo $data['roll_no']; ?></td>
                                                <td>
                                                    <?php echo $data['firstname'] ." ".$data['middlename']." ".$data['lastname']; ?>
                                                </td>
                                                <td><?php echo $data['father_phone']; ?></td>
                                                <td><?php echo $data['gender']; ?></td>
                                                <td><?php echo $data['admission_no']; ?></td>
                                                    <td>
                                                        <?php
                                                    if ($data["admission_date"] != null && $data["admission_date"] != '0000-00-00') {
                                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($data['admission_date']));
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $data['category']; ?></td>
                                                <td><?php echo $data['religion']; ?></td>
                                                <td><?php echo $data['cast']; ?></td>
                                                <td><?php echo  $data['blood_group']; ?></td>
                                                <td><?php echo  $data['height']; ?></td>
                                                <td><?php echo  $data['weight']; ?></td>
                                                <td><?php echo $data['previous_school'] ?></td>

                                                <td><?php echo $data['adhar_no']; ?></td>
                                                <td>
                                                    <?php
                                                    if ($data["dob"] != null && $data["dob"] != '0000-00-00') {
                                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($data['dob']));
                                                    }
                                                        ?>
                                                    </td>

                                                <td><?php echo $data['father_name']; ?></td>


                                                <td><?php echo $data['mother_name']; ?></td>
                                                <td><?php echo $data['guardian_name'] ?></td>
                                                <td><?php echo $data['current_address']; ?></td>

                                                <td><?php echo $data['child_id']; ?></td>
                                                </tr>
                                        <?php
                                            $count++;
                                        }

                                        // exit;

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--./box box-primary -->
                </div><!-- ./col-md-12 -->
            </div>
        </div>
    </section>
</div>

