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
                        <h3 class="box-title"><?php echo $this->lang->line('fees_collection'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('studentfee/index'); ?>">
                                <a class="<?php echo set_Submenu('studentfee/index'); ?>" href="<?php echo base_url(); ?>studentfee"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/cf.png') ?>" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('collect_fees'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('search_fees_payment', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('studentfee/searchpayment'); ?>">
                                <a class="<?php echo set_Submenu('studentfee/searchpayment'); ?>" href="<?php echo base_url(); ?>studentfee/searchpayment"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/sfp.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('search_fees_payment'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('search_due_fees', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('studentfee/feesearch'); ?>">
                                <a class="<?php echo set_Submenu('studentfee/feesearch'); ?>" href="<?php echo base_url(); ?>studentfee/feesearch"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/sdf.png') ?>" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('search_due_fees'); ?></a>
                            </li>

                        <?php
                        }

                        if ($this->rbac->hasPrivilege('fees_master', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/feemaster'); ?>">
                                <a class="<?php echo set_Submenu('admin/feemaster'); ?>" href="<?php echo base_url(); ?>admin/feemaster"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fm.png') ?>" alt="icon4" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_master'); ?></a>
                            </li>
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('fees_group', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/feegroup'); ?>">
                                <a class="<?php echo set_Submenu('admin/feegroup'); ?>" href="<?php echo base_url(); ?>admin/feegroup"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fg.png') ?>" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_group'); ?></a>
                            </li>
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('fees_type', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('feetype/index'); ?>">
                                <a class="<?php echo set_Submenu('feetype/index'); ?>" href="<?php echo base_url(); ?>admin/feetype"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/ft.png') ?>" alt="icon6" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_type'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('fees_discount', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('admin/feediscount'); ?>">
                                <a class="<?php echo set_Submenu('admin/feediscount'); ?>" href="<?php echo base_url(); ?>admin/feediscount"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fd.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_discount'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('fees_carry_forward', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('feesforward/index'); ?>">
                                <a class="<?php echo set_Submenu('feesforward/index'); ?>" href="<?php echo base_url('admin/feesforward'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fcf.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_carry_forward'); ?></a>
                            </li>
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('fees_reminder', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('feereminder/setting'); ?>">
                                <a class="<?php echo set_Submenu('feereminder/setting'); ?>" href="<?php echo site_url('admin/feereminder/setting'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fr.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_reminder'); ?></a>
                            </li>
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('collect_fees', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('feesreceipt/feesreceipt_24'); ?>">
                                <a class="<?php echo set_Submenu('feesreceipt/feesreceipt_24'); ?>" href="<?php echo site_url('admin/feesreceipt/feesreceipt_24'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fr24.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_receipt_24'); ?></a>
                            </li>
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('feediscount_report', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('feediscount_report/index'); ?>">
                                <a class="<?php echo set_Submenu('feediscount_report/index'); ?>" href="<?php echo site_url('admin/feediscount_report'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fd.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_discount_report'); ?></a>
                            </li>
                        <?php
                        }
                        ?>
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
                                <h4 class="text-center pb-10 fee-summary-title">Fee Summary</h4>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Students Fees<span class="pull-right">Rs. <?php echo amountFormat($feesummarData['totalfee']) ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstartmobile-->
                                    </div><!--./col-md-3-->
                                
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Paid Fees<span class="pull-right">Rs.<?php echo amountFormat($feesummarData['deposit']) ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstartmobile-->
                                    </div><!--./col-md-3-->
                                
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="topprograssstartmobile">
                                            
                                                <div class="progress-group">
                                                    
                                                    <p class="mt5 clearfix">Total Balance Fees<span class="pull-right">Rs.<?php echo amountFormat($feesummarData['balance']) ?></span></p>
                                                    
                                                </div>
                                            
                                        </div><!--./topprograssstartmobile-->
                                    </div><!--./col-md-3-->
                                
                            </div>
                        </div>    
                    
                    <div>


                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('today_collection'); ?> </h3>
                            <div class="box-tools pull-right"></div>
                        </div>

                        <div class="box-body">


                            <?php

                            $card = 0;
                            $cash = 0;
                            $upi = 0;
                            $total = 0;
                            if (!empty($fees_data)) { ?>

                                <?php
                                foreach ($fees_data as $val) {
                                    $total += $val['total_amount']; ?>

                                    <?php if ($val['mode'] == "Cash") {
                                        $cash = amountFormat($val['total_amount']);
                                    } ?>
                                    <?php if ($val['mode'] == "upi") {
                                        $upi = amountFormat($val['total_amount']);
                                    } ?>
                                    <?php if ($val['mode'] == "card") {
                                        $card =  amountFormat($val['total_amount']);
                                    } ?>


                                <?php    } ?>



                            <?php } else { ?>


                            <?php  } ?>



                            <div class="row">
                                <div class="col-md-3">
                                    <div class="cb9854 info-box">
                                        <a href="#">
                                            <span class="back-none info-box-icon">
                                                <img class="width25 img-fluid" src="https://newlayout.wisibles.com/backend/images/sidebar/22/18.png">
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text font-weight-bold">Cash</span>
                                                <span class="info-box-number"><?php echo $currency_symbol . " " . $cash; ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="cb9854 info-box">
                                        <a href="#">
                                            <span class="back-none info-box-icon">
                                                <img class="width25 img-fluid" src="https://newlayout.wisibles.com/backend/images/sidebar/22/20.png">
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text font-weight-bold">Card</span>
                                                <span class="info-box-number"><?php echo $currency_symbol . " " . $card; ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="cb9854 info-box">
                                        <a href="#">
                                            <span class="back-none info-box-icon">
                                                <img class="width25 img-fluid" src="https://newlayout.wisibles.com/backend/images/sidebar/22/21.png">
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text font-weight-bold">UPI</span>
                                                <span class="info-box-number"><?php echo $currency_symbol . " " . $upi; ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="cb9854 info-box">
                                        <a href="#">
                                            <span class="back-none info-box-icon">
                                                <img class="width25 img-fluid" src="https://newlayout.wisibles.com/backend/images/sidebar/3.png">
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text font-weight-bold">Total</span>
                                                <span class="info-box-number"><?php echo $currency_symbol . " " . amountFormat($total); ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>





                            </div>
                            
                        </div>
                       

                    </div>
                    
                    <div class="d-flex justify-content-center">
                        <a href="<?php echo site_url() ?>financereports/finance" class="btn-check-now btn btn-primary">View More Reoprts</a>
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