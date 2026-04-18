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
                        <h3 class="box-title"><?php echo $this->lang->line('front_office'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_view')) { ?>
                        <li class="<?php echo set_Submenu('admin/enquiry'); ?>">
                            <a class="<?php echo set_Submenu('admin/enquiry'); ?>" href="<?php echo base_url(); ?>admin/enquiry"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('admission_enquiry'); ?></a>
                        </li>     
                        
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('visitor_book', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/visitors'); ?>">
                            <a class="<?php echo set_Submenu('admin/visitors'); ?>" href="<?php echo base_url(); ?>admin/visitors"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/2.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('visitor_book'); ?></a>
                        </li>
            
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('phone_call_log', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/generalcall'); ?>">
                            <a class="<?php echo set_Submenu('admin/generalcall'); ?>" href="<?php echo base_url(); ?>admin/generalcall"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/3.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('phone_call_log'); ?></a>
                        </li> 
                        
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/dispatch'); ?>">
                            <a class="<?php echo set_Submenu('admin/dispatch'); ?>" href="<?php echo base_url(); ?>admin/dispatch"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/4.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('postal_dispatch'); ?></a>
                        </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('postal_receive', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/receive'); ?>">
                            <a class="<?php echo set_Submenu('admin/receive'); ?>" href="<?php echo base_url(); ?>admin/receive"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/5.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('postal_receive'); ?></a>
                        </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('complaint', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/complaint'); ?>">
                            <a class="<?php echo set_Submenu('admin/complaint'); ?>" href="<?php echo base_url(); ?>admin/complaint"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/6.png') ?>" alt="icon6" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('complain'); ?></a>
                        </li>
                        <?php
                            }
                            if ($this->rbac->hasPrivilege('setup_font_office', 'can_view')) {
                                ?>
                        <li class="<?php echo set_Submenu('admin/visitorspurpose'); ?>">
                            <a class="<?php echo set_Submenu('admin/visitorspurpose'); ?>" href="<?php echo base_url(); ?>admin/visitorspurpose"><img src="<?php echo base_url('backend/images/sidebar/submenu/front_office/1.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('setup_front_office'); ?></a>
                        </li>
                        <?php } ?>
                        
                    </ul>
                </div>
            </div><!--./col-md-3-->
            
            <div class="col-md-10">
                <div class="box box-primary pb20">
                    <div class="box-header">
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>
                    <div class="box-body">
                    <div class="">
                                <h4 class="text-center pb-10 fee-summary-title">Admissions Summary</h4>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Leads<span class="pull-right"><?php echo $total_leads; ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstart-->
                                    </div><!--./col-md-3-->
                                
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Passive Leads<span class="pull-right"><?php echo $total_passive; ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstart-->
                                </div><!--./col-md-3-->
                                
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Won Leads<span class="pull-right"><?php echo $total_won; ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstart-->
                                </div><!--./col-md-3-->
                                
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Dead Leads<span class="pull-right"><?php echo $total_dead; ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstart-->
                                </div><!--./col-md-3-->
                                
                            </div>
                        </div>    
                        
                        <div class="d-flex justify-content-center">
                            <a href="<?php echo site_url() ?>report/admission_report" class="btn-check-now btn btn-primary">View More Reoprts</a>
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