<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary pb20">
                    <div>


                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('late_entries'); ?> </h3>
                            <div class="btn-group pull-right">
                                <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                            </div>
                        </div>

                        <div class="box-body">
                            
                            <div class="col-md-2 hide-mobile">
                                <div class="box border0">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?php echo $this->lang->line('attendance'); ?></h3>
                                    </div>
                                    <ul class="tablists">
                                         <?php
                                            if (!is_subAttendence()) {
                                                if ($this->rbac->hasPrivilege('student_attendance', 'can_view')) {
                                                    ?>
                                        <li class="<?php echo set_Submenu('studentfee/index'); ?>">
                                            <a class="<?php echo set_Submenu('studentfee/index'); ?>" href="<?php echo base_url(); ?>admin/stuattendence"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_attendance'); ?></a>
                                        </li>     
                                        
                                        <?php
                                                }
                                                if ($this->rbac->hasPrivilege('attendance_by_date', 'can_view')) {
                                                    ?>
                                        <li class="<?php echo set_Submenu('stuattendence/attendenceReport'); ?>">
                                            <a class="<?php echo set_Submenu('stuattendence/attendenceReport'); ?>" href="<?php echo base_url(); ?>admin/stuattendence/attendencereport"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/3.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('attendance_by_date'); ?></a>
                                        </li>
                            
                                        <?php
                                                }
                                            } else {
                                                if ($this->rbac->hasPrivilege('student_attendance', 'can_view')) {
                                                    ?>
                                        <li class="<?php echo set_Submenu('subjectattendence/index'); ?>">
                                            <a class="<?php echo set_Submenu('subjectattendence/index'); ?>" href="<?php echo base_url(); ?>admin/subjectattendence"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/1.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('period') . " " . $this->lang->line('attendance'); ?></a>
                                        </li> 
                                        
                                        <?php
                                                }
                                                if ($this->rbac->hasPrivilege('attendance_by_date', 'can_view')) {
                                                    ?>
                                        <li class="<?php echo set_Submenu('subjectattendence/reportbydate'); ?>">
                                            <a class="<?php echo set_Submenu('subjectattendence/reportbydate'); ?>" href="<?php echo site_url('admin/subjectattendence/reportbydate'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/3.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('period') . " " . $this->lang->line('attendance') . " " . $this->lang->line('by') . " " . $this->lang->line('date'); ?></a>
                                        </li>
                                        <?php
                                                }
                                            }
                                            if ($this->rbac->hasPrivilege('approve_leave', 'can_view')) {
                                                ?>
                                        <li class="<?php echo set_Submenu('Attendance/approve_leave'); ?>">
                                            <a class="<?php echo set_Submenu('Attendance/approve_leave'); ?>" href="<?php echo base_url(); ?>admin/approve_leave"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/3.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('approve') . " " . $this->lang->line('leave'); ?></a>
                                        </li>
                                        <?php
                                            }
                                            if ($this->rbac->hasPrivilege('late_entries', 'can_view')) {
                                        ?>        
                                        
                                        <li class="<?php echo set_Submenu('late_entries/index'); ?>">
                                            <a class="<?php echo set_Submenu('late_entries/index'); ?>" href="<?php echo base_url(); ?>admin/late_entries/index"><img src="<?php echo base_url('backend/images/sidebar/submenu/attendance/4.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('late_entries'); ?></a>
                                        </li>
                                        <?php
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div><!--./col-md-3-->
                            
                            <div class="col-lg-10 col-md-10 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstart">
                                            <p class="text-blur"><?php echo date("M j, Y");; ?></p>
                                            <p class="mt5 clearfix font-16">Late Entries<br>
                                                <span class="font12">Today</span>
                                            </p>
                                            <div class="box-header with-border">
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">No.of Students Late<span class="pull-right"><?php echo $total_late_students; ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->

                                </div><!--./row-->
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <a href="<?php echo site_url() ?>attendencereports/late_entries_report" class="btn-check-now btn btn-primary">View More Reoprts</a>
                        </div>
                       

                    </div>

                </div>

    </section>
</div>

<script>
    $(document).ready(function() {
        emptyDatatable('student-list', 'fees_data');

    });
</script>
<script type="text/javascript">
    $(document).ready(function() {

        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id', 0) ?>';
        getSectionByClass(class_id, section_id);
    });

    $(document).on('change', '#class_id', function(e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, 0);
    });

    function getSectionByClass(class_id, section_id) {

        if (class_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                beforeSend: function() {
                    $('#section_id').addClass('dropdownloading');
                },
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                },
                complete: function() {
                    $('#section_id').removeClass('dropdownloading');
                }
            });
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("form.class_search_form button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });


        $(document).on('submit', '.class_search_form', function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $("button[type=submit][clicked=true]");
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            form_data.push({
                name: 'search_type',
                value: $this.attr('value')
            });
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data, // serializes the form's elements.
                beforeSend: function() {
                    $('[id^=error]').html("");
                    $this.button('loading');
                    resetFields($this.attr('name'));
                },
                success: function(response) { // your success handler
                console.log(response);
                    if (!response.status) {
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        initDatatable('student-list', 'studentfee/ajaxSearch', response.params, [], 100);
                    }
                },
                error: function() { // your error handler
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });

        });

    });

    function resetFields(search_type) {
        if (search_type == "keyword_search") {
            $('#class_id').prop('selectedIndex', 0);
            $('#section_id').find('option').not(':first').remove();
        } else if (search_type == "class_search") {

            $('#search_text').val("");
        }
    }
</script>