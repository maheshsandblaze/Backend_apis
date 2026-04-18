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
                        <h3 class="box-title"><?php echo $this->lang->line('transport'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('routes', 'can_view')) { ?>
                        <li class="<?php echo set_Submenu('route/index'); ?>">
                            <a class="<?php echo set_Submenu('route/index'); ?>" href="<?php echo base_url(); ?>admin/route"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/3.png" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('routes'); ?></a>
                        </li>     
                        
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('vehicle', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('vehicle/index'); ?>">
                            <a class="<?php echo set_Submenu('vehicle/index'); ?>" href="<?php echo base_url(); ?>admin/vehicle"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/4.png" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('vehicles'); ?></a>
                        </li>
            
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('assign_vehicle', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('vehroute/index'); ?>">
                            <a class="<?php echo set_Submenu('vehroute/index'); ?>" href="<?php echo base_url(); ?>admin/vehroute"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/5.png" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('assign_vehicle'); ?></a>
                        </li> 
                        
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('transport', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('pickuppoint/index'); ?>">
                            <a class="<?php echo set_Submenu('pickuppoint/index'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/2.png" alt="icon4" class="img-fluid" style="width:20px"><?php echo $this->lang->line('pickup_point'); ?></a>
                        </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('transport', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('pickuppoint/assign'); ?>">
                            <a class="<?php echo set_Submenu('pickuppoint/assign'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint/assign"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/6.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('route_pickup_point'); ?></a>
                        </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('transport', 'can_view')) {
                         ?>
                         <li class="<?php echo set_Submenu('transport/feemaster'); ?>">
                            <a class="<?php echo set_Submenu('transport/feemaster'); ?>" href="<?php echo base_url(); ?>admin/transport/feemaster"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/1.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_master'); ?></a>
                         </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('transport', 'can_view')) {
                        ?>
                        <li class="<?php echo set_Submenu('pickuppoint/student_fees'); ?>">
                            <a class="<?php echo set_Submenu('pickuppoint/student_fees'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint/student_fees"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/7.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_transport_fees'); ?></a>
                        </li>
                        <?php
                            }
                        ?>
                        
                    </ul>
                </div>
            </div><!--./col-md-3-->
            <div class="col-md-10">
                <div class="box box-primary">
                    <div>


                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> Transport </h3>
                            <div class="btn-group pull-right">
                                <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">

                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstart">
                                            <p class="mt5 clearfix font-16">Route Title</p>
                                            <div class="box-header with-border">
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Vehicle Number<span class="pull-right">0</span></p>
                                                    <p class="mt5 clearfix">Vehicle Model<span class="pull-right">0</span></p>
                                                    <p class="mt5 clearfix">Driver Name<span class="pull-right">Test</span></p>
                                                    <p class="mt5 clearfix">Driver Contact<span class="pull-right">0</span></p>
                                                </div>
                                            </div>
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->

                                </div><!--./row-->
                            </div>
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