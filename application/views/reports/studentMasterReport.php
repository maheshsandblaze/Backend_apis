<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-line-chart"></i> <?php //echo $this->lang->line('reports'); ?> <small> <?php //echo $this->lang->line('filter_by_name1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content" >
        <?php $this->load->view('reports/_studentinformation');?>
        
                <div class="box removeboxmius">
                    
                        <div class="">
                            <div class="box-header ptbnull"></div>
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('student_report'); ?></h3>
                            </div>
                            <div class="box-body table-responsive">
                                    <div class="download_label"> <?php echo $this->lang->line('student_report'); ?></div>
                            <div >
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('student_report'); ?>">
                                    <thead>
                                        <tr>
                                            <th>SNO</th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('roll_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <?php if ($sch_setting->mobile_no) { ?>
                                                <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                                            <th><?php echo $this->lang->line('caste'); ?></th>
                                            <th>Aadhar Number</th>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <?php if ($sch_setting->father_name) {?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php }?>
                                            
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                            <th><?php echo $this->lang->line('current_address'); ?></th>
                                            <th>Child ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $count = 1;
                                            foreach ($student_data as $data) {
                                        ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $data['class'] . "(" . $data['section'] . ")"; ?></td>
                                            <td><?php echo $data['roll_no']; ?></td>
                                            <td>
                                                <?php echo $this->customlib->getFullName($data['firstname'],$data['middlename'],$data['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>
                                            </td>
                                            <td><?php echo $data['father_phone']; ?></td>
                                            <td><?php echo $data['gender']; ?></td>
                                            <td><?php echo $data['admission_no']; ?></td>
                                            <td>
                                                <?php
                                                        if ($data["admission_date"] != null && $data["admission_date"]!='0000-00-00') {
                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($data['admission_date']));
                                                        }
                                                        ?>
                                            </td>
                                            <td><?php echo $data['cast']; ?></td>
                                            <td><?php echo $data['adhar_no']; ?></td>
                                            <td>
                                                <?php
                                                        if ($data["dob"] != null && $data["dob"]!='0000-00-00') {
                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($data['dob']));
                                                        }
                                                        ?>
                                            </td>
                                            
                                            <td><?php echo $data['father_name']; ?></td>
                                            
                                            
                                            <td><?php echo $data['mother_name']; ?></td>
                                            <td><?php echo $data['current_address']; ?></td>
                                            
                                            <td><?php echo $data['child_id']; ?></td>
                                        </tr>
                                        <?php 
                                        $count++;
                                            }
                                        
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

<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }

    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>