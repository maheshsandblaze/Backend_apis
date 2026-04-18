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
            <div class="col-md-2 hide-mobile">
                <div class="box border0">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('student_information'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php
                        if ($this->rbac->hasPrivilege('student', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('student/search'); ?>">
                                <a class="<?php echo set_Submenu('student/search'); ?>" href="<?php echo base_url(); ?>student/search"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_details'); ?></a>
                            </li>

                        <?php
                        }

                        if ($this->rbac->hasPrivilege('student', 'can_add')) {
                        ?>
                            <li class="<?php echo set_Submenu('student/create'); ?>">
                                <a class="<?php echo set_Submenu('student/create'); ?>" href="<?php echo base_url(); ?>student/create"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/2.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_admission'); ?></a>
                            </li>

                            <?php } ?><?php
                                        if ($this->module_lib->hasActive('online_admission')) {
                                            if ($this->rbac->hasPrivilege('online_admission', 'can_view')) {
                                        ?>
                            <li class="<?php echo set_Submenu('onlinestudent'); ?>">
                                <a class="<?php echo set_Submenu('onlinestudent'); ?>" href="<?php echo site_url('admin/onlinestudent'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/3.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('online_admission'); ?></a>
                            </li>

                        <?php
                                            }
                                        }

                                        if ($this->rbac->hasPrivilege('disable_student', 'can_view')) {
                        ?>
                        <li class="<?php echo set_Submenu('student/disablestudentslist'); ?>">
                            <a class="<?php echo set_Submenu('student/disablestudentslist'); ?>" href="<?php echo base_url(); ?>student/disablestudentslist"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/88.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('disabled_students'); ?></a>
                        </li>
                        <?php
                                        }
                                        if ($this->module_lib->hasActive('multi_class')) {
                                            if ($this->rbac->hasPrivilege('multi_class_student', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('student/multiclass'); ?>">
                                <a class="<?php echo set_Submenu('student/multiclass'); ?>" href="<?php echo base_url(); ?>student/multiclass"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/1.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('multiclass') . " " . $this->lang->line('student'); ?></a>
                            </li>
                        <?php
                                            }
                                        }
                                        if ($this->rbac->hasPrivilege('student', 'can_delete')) {
                        ?>
                        <li class="<?php echo set_Submenu('bulkdelete'); ?>">
                            <a class="<?php echo set_Submenu('bulkdelete'); ?>" href="<?php echo site_url('student/bulkdelete'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/5.png') ?>" alt="icon6" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('bulk_delete'); ?></a>
                        </li>
                    <?php
                                        }

                                        if ($this->rbac->hasPrivilege('student_categories', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('category/index'); ?>">
                            <a class="<?php echo set_Submenu('category/index'); ?>" href="<?php echo base_url(); ?>category"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/6.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_categories'); ?></a>
                        </li>
                    <?php }
                    ?>
                    <?php
                    if ($this->rbac->hasPrivilege('student_houses', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('admin/schoolhouse'); ?>">
                            <a class="<?php echo set_Submenu('admin/schoolhouse'); ?>" href="<?php echo base_url(); ?>admin/schoolhouse"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/7.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_house'); ?></a>
                        </li>
                    <?php
                    }

                    if ($this->rbac->hasPrivilege('disable_reason', 'can_view')) {
                    ?>
                        <li class="<?php echo set_Submenu('student/disable_reason'); ?>">
                            <a class="<?php echo set_Submenu('student/disable_reason'); ?>" href="<?php echo base_url(); ?>admin/disable_reason"><img src="<?php echo base_url('backend/images/sidebar/submenu/student_information/88.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('disable') . " " . $this->lang->line('reason'); ?></a>
                        </li>
                    <?php
                    }
                    ?>
                    </ul>
                </div>
            </div><!--./col-md-3-->
            
            <div class="col-md-10">
                <div class="box box-primary pb20">
                    <div>


                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> Students </h3>
                            <div class="btn-group pull-right">
                                <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">

                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstart">
                                            <p class="mt5 clearfix font-16">No.of Students Classwise</p>
                                            <div class="box-header with-border">
                                                <div class="progress-group">
                                                    <?php
                                                        $total_boys = $total_girls = $total_students = 0;
                                                        if(!empty($result)){
                                                        foreach ($result as $key => $value) {
                                                    ?>
                                                    <p class="mt5 clearfix"><?php echo $value['class'] . " (" . $value['section'] . ")"; ?><span class="pull-right"><?php echo $value['total_student']; ?></span></p>
                                                    <?php
                                                        }
                                                        } else {
                                                    ?>
                                                    <p class="mt5 clearfix">No Records Found</p>
                                                    <?php
                                                        }
                                                    ?>
                                                    <!--<p class="mt5 clearfix">Class 1 - A<span class="pull-right">10</span></p>-->
                                                    <!--<p class="mt5 clearfix">Class 2 - A<span class="pull-right">10</span></p>-->
                                                    <!--<p class="mt5 clearfix">Class 3 - A<span class="pull-right">10</span></p>-->
                                                    <!--<p class="mt5 clearfix">Class 4 - A<span class="pull-right">10</span></p>-->
                                                </div>
                                            </div>
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->

                                </div><!--./row-->
                            </div>
                        </div>
                       

                    </div>
                    
                    <div class="d-flex justify-content-center">
                        <a href="<?php echo site_url() ?>student/search" class="btn-check-now btn btn-primary">View More Reoprts</a>
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