<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?>
        </h1>

    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form id='feediscount_assign' action="<?php echo site_url('admin/feediscount_assign/index') ?>" method="post" >
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                            <div class="btn-group pull-right mml15">
                                <button onclick="window.history.back(); " class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back</button>
                            </div>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if ($this->session->flashdata('msg')) { ?>
                                        <?php echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg'); ?>
                                    <?php } ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select id="class_id" name="class_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                            ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
                                            <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <button type="submit" name="action" value="search" class="btn btn-primary pull-right"><?php echo $this->lang->line('search'); ?></button>
                            </div>


            </form>
        </div>


        <?php
        if (isset($student_due_fee)) {
        ?>
            <div class="box-header ptbnull"></div>
            <div class="">
                <div class="box-header with-border">
                    <h3 class="box-title titlefix"><?php echo $this->lang->line('student_list'); ?></h3>

                </div>
                <div class="box-body">
                    <?php
                    if (!empty($student_due_fee)) {
                    ?>


                        <div class="row">
                            <div class="col-md-12">

                            </div>
                            <div class="row col-xs-12">
                                <div class="col-md-4">

                                </div>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th class="text text-left"><?php echo $this->lang->line('student_name'); ?></th>
                                            <th class="text text-left"><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th class="text text-left"><?php echo $this->lang->line('fee_type'); ?></th>
                                            <th class="text text-left"><?php echo $this->lang->line('total'); ?></th>
                                            <th class="text text-left"><?php echo $this->lang->line('discount'); ?></th>
                                            <th class="text text-left"><?php echo $this->lang->line('paid'); ?></th>

                                            <th class="text text-left"><?php echo $this->lang->line('balance'); ?></th>

                                            <th class="text text-left"><?php echo $this->lang->line('status'); ?></th>

                                            <th class="text-right"><?php echo $this->lang->line('action'); ?> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($student_due_fee as $due_fee_key => $fee_dis) {

                                            // echo "<pre>";

                                            // print_r($fee_dis);

                                        ?>
                                            <tr class="dark-gray">
                                                <td><?php echo $fee_dis->name; ?></td>
                                                <td><?php echo $fee_dis->admission_no; ?></td>
                                                <td></td>
                                                <td><?php echo $fee_dis->totalfee; ?></td>
                                                <td><?php echo $fee_dis->discount; ?></td>
                                                <td><?php echo $fee_dis->deposit; ?></td>
                                                <td><?php echo $fee_dis->balance; ?></td>
                                                <td></td>
                                                <td></td>






                                            </tr>
                                            <?php if ($fee_dis->fees) {

                                                $total_amount  = 0;


                                                foreach ($fee_dis->fees as $fee_value) {
                                                    $fee_paid         = 0;
                                                    $fee_discount     = 0;
                                                    $fee_fine         = 0;
                                                    $fees_fine_amount = 0;
                                                    $feetype_balance  = 0;
                                                    if (!empty($fee_value->amount_detail)) {
                                                        $fee_deposits = json_decode(($fee_value->amount_detail));

                                                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                            $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                            $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                            $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                                        }
                                                    }
                                                    if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                        $fees_fine_amount       = $fee_value->fine_amount;
                                                        // $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                                                    }

                                                    // $total_amount += $fee_value->amount;
                                                    // $total_discount_amount += $fee_discount;
                                                    // $total_deposite_amount += $fee_paid;
                                                    // $total_fine_amount += $fee_fine;
                                                    // $feetype_balance = $fee_value->amount - ($fee_paid + $fee_discount);
                                                    // $total_balance_amount += $feetype_balance;

                                                    $feebalance =     $fee_value->amount - ($fee_paid + $fee_discount)


                                            ?>

                                                    <tr>


                                                        <td></td>
                                                        <td></td>
                                                        <td><?php echo $fee_value->name . '(' . $fee_value->type . ')'; ?></td>
                                                        <td><?php echo $fee_value->amount; ?></td>
                                                        <td><?php echo $fee_discount ?></td>
                                                        <td><?php echo $fee_paid; ?></td>
                                                        <td><?php echo $fee_value->amount - ($fee_paid + $fee_discount) ?></td>
                                                        <td><?php if ($fee_value->discountstatus != "" && $fee_value->discountstatus == "yes") {
                                                                echo "<span class='label label-success'>" . $this->lang->line('approved') . "</span>";
                                                            } else if ($fee_value->discountstatus != "" &&  $fee_value->discountstatus == "no") {
                                                                echo "<span class='label label-danger'>" . $this->lang->line('not_approved') . "</span>";
                                                            }  ?></td>
                                                        <td class="text-right">

                                                            <?php if ($fee_value->discountstatus != "" && $fee_value->feediscountID != 0) { ?>

                                                                <!-- <button type="button" data-student_session_id="<?php echo $fee_value->student_session_id; ?>" data-student_fees_master_id="<?php echo $fee_value->id; ?>" data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>" data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>" data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>" class="btn btn-xs btn-default approve_discount_button" data-fee-category="fees" title="<?php echo $this->lang->line('approve_discount'); ?>" data-fee-category="fees" data-trans_fee_id="0" data-dicount_id=<?php echo $fee_value->feediscountID; ?>><i class="fa fa-check"></i></button> -->


                                                            <?php } else if ($feebalance != 0) { ?>

                                                                <button type="button" data-student_session_id="<?php echo $fee_value->student_session_id; ?>" data-student_fees_master_id="<?php echo $fee_value->id; ?>" data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>" data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>" data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>" class="btn btn-xs btn-default dis_apply_button" data-toggle="modal" data-target="#myFeesModal" data-fee-category="fees" title="Assign Discount" data-fee-category="fees" data-trans_fee_id="0"><i class="fa fa-plus"></i></button>


                                                        <?php }
                                                        } ?>

                                                        </td>


                                                    </tr>


                                            <?php

                                            }
                                        } ?>





                                        <?php
                                        $i++;
                                    }
                                    // exit;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    <?php


                } else if (isset($discount_assign_students)) { ?>


                        <div class="box-header ptbnull"></div>
                        <div class="">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"><?php echo $this->lang->line('student_list'); ?></h3>

                            </div>
                            <div class="box-body">
                                <?php
                                if (!empty($discount_assign_students)) {
                                ?>


                                    <div class="row">
                                        <div class="col-md-12">

                                        </div>
                                        <div class="row col-xs-12">
                                            <div class="col-md-4">

                                            </div>
                                        </div>
                                        <div class="col-xs-12 table-responsive">
                                            <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>
                                            <table class="table table-striped table-bordered table-hover example">
                                                <thead>
                                                    <tr>
                                                        <th class="text text-left"><?php echo $this->lang->line('student_name'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('admission_no'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('fee_type'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('total'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('discount'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('discount_type'); ?></th>

                                                        <th class="text text-left"><?php echo $this->lang->line('date'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('assigned_by'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('description'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('status'); ?></th>

                                                        <th class="text-right"><?php echo $this->lang->line('action'); ?> </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($discount_assign_students as $due_fee_key => $fee_dis) {

                                                        // echo "<pre>";

                                                        // print_r($fee_dis);

                                                    ?>
                                                        <tr class="dark-gray">
                                                            <td><?php echo $fee_dis->name; ?></td>
                                                            <td><?php echo $fee_dis->admission_no; ?></td>
                                                            <td></td>
                                                            <td><?php echo $fee_dis->totalfee; ?></td>
                                                            <td><?php echo $fee_dis->discount; ?></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>






                                                        </tr>
                                                        <?php if ($fee_dis->fees) {

                                                            $total_amount  = 0;


                                                            foreach ($fee_dis->fees as $fee_value) {
                                                                $fee_paid         = 0;
                                                                $fee_discount     = 0;
                                                                $fee_fine         = 0;
                                                                $fees_fine_amount = 0;
                                                                $feetype_balance  = 0;
                                                                $discount_type = "";
                                                                $approve_id = $fee_value->discount_approve_id;

                                                                if (!empty($fee_value->amount_detail)) {
                                                                    $fee_deposits = json_decode(($fee_value->amount_detail));

                                                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                        $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                                        $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                                        $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;

                                                                        $payment_date = $fee_deposits_value->date;
                                                                        $collected_by = $fee_deposits_value->collected_by;
                                                                        $note = $fee_deposits_value->description;
                                                                        $discount_type = $fee_deposits_value->discount_type;
                                                                    }

                                                        ?>

                                                                    <tr>


                                                                        <td></td>
                                                                        <td></td>
                                                                        <td><?php echo $fee_value->name . '(' . $fee_value->type . ')'; ?></td>
                                                                        <td><?php echo (int)$fee_value->amount; ?></td>
                                                                        <td><?php echo $fee_discount ?></td>
                                                                        <td><?php echo $discount_type; ?></td>

                                                                        <td><?php if ($payment_date != "") {
                                                                                echo  date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($payment_date));
                                                                            } ?></td>
                                                                        <td><?php echo $collected_by ?></td>
                                                                        <td><?php echo $note; ?></td>

                                                                        <td class="text text-left width85"><?php if ($fee_value->discount_status == "yes") {
                                                                                                                echo "<span class='label label-success'>" . $this->lang->line('approved') . "</span>";
                                                                                                            } else if ($fee_value->discount_status == "no") {
                                                                                                                echo "<span class='label label-danger'>" . $this->lang->line('not_approved') . "</span>";
                                                                                                            }  ?></td>
                                                                        <td class="text-right">

                                                                            <?php

                                                                            if ($this->rbac->hasPrivilege('approve_discount', 'can_add')) {

                                                                                if ($fee_value->id != "" && $fee_value->id != 0 && $fee_value->discount_status == "no") {

                                                                            ?>

                                                                                    <button type="button" data-student_session_id="<?php echo $fee_value->student_session_id; ?>" data-student_fees_master_id="<?php echo $fee_value->id; ?>" data-fee_groups_feetype_id="<?php echo $fee_value->fee_groups_feetype_id; ?>" data-group="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")" : $fee_value->name . " (" . $fee_value->type . ")"; ?>" data-type="<?php echo ($fee_value->is_system) ? $this->lang->line($fee_value->type) : $fee_value->code; ?>" data-discount_type="" class="btn btn-xs btn-default approve_discount_button" data-fee-category="fees" title="<?php echo $this->lang->line('approve_discount'); ?>" data-fee-category="fees" data-trans_fee_id="0" data-date="<?php echo date($this->customlib->getSchoolDateFormat($payment_date)); ?>" data-collected_by="<?php echo $collected_by; ?>" data-description="<?php echo $note; ?>" data-discount_amount="<?php echo $fee_discount; ?>" data-discount_approve_id="<?php echo $approve_id;  ?>"><i class="fa fa-check"></i></button>




                                                                            <?php }
                                                                            } ?>

                                                                            <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_delete')) { ?>
                                                                                <!-- <button class="btn btn-default btn-xs" data-invoiceno="<?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?>" data-main_invoice="<?php echo $fee_value->student_fees_deposite_id ?>" data-sub_invoice="<?php echo $fee_deposits_value->inv_no ?>" data-toggle="modal" data-target="#confirm-delete" title="<?php echo $this->lang->line('cancel'); ?>">
                                                                                    <i class="fa fa-undo"> </i>
                                                                                </button> -->
                                                                            <?php } ?>




                                                                        </td>


                                                                    </tr>
                                                                <?php } else {

                                                                    $payment_date = '';
                                                                    $collected_by = '';
                                                                    $note = '';
                                                                }
                                                                if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                                    $fees_fine_amount       = $fee_value->fine_amount;
                                                                    // $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                                                                }

                                                                // $total_amount += $fee_value->amount;
                                                                // $total_discount_amount += $fee_discount;
                                                                // $total_deposite_amount += $fee_paid;
                                                                // $total_fine_amount += $fee_fine;
                                                                // $feetype_balance = $fee_value->amount - ($fee_paid + $fee_discount);
                                                                // $total_balance_amount += $feetype_balance;


                                                                ?>




                                                            <?php
                                                            } ?>


                                                    <?php

                                                        }
                                                    } ?>





                                                <?php
                                                $i++;
                                            }
                                            // exit;
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                                        <div class="box-footer">
                                                            <button type="submit" name="action" value="fee_submit" class="btn btn-primary pull-right"><?php echo $this->lang->line('save'); ?></button>
                                                        </div>
                                                    </div> -->


                                <?php   } else {
                                ?>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php
                            }
                                ?>
                            </div>
                        </div>
                </div>
                <?php

                ?>
            </div>

</div>
</section>
</div>




<div class="modal fade" id="myFeesModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <input type="hidden" class="form-control" id="std_id" value="" readonly="readonly" />
                        <input type="hidden" class="form-control" id="parent_app_key" value="" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_phone" value="" readonly="readonly" />
                        <input type="hidden" class="form-control" id="guardian_email" value="" readonly="readonly" />
                        <input type="hidden" class="form-control" id="student_fees_master_id" value="0" readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_groups_feetype_id" value="0" readonly="readonly" />
                        <input type="hidden" class="form-control" id="transport_fees_id" value="0" readonly="readonly" />
                        <input type="hidden" class="form-control" id="fee_category" value="" readonly="readonly" />
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <input id="date" name="admission_date" placeholder="" type="text" class="form-control date_fee" value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" readonly="readonly" />
                                <span class="text-danger" id="date_error"></span>
                            </div>
                        </div>

                        <?php
                        $role_array     = $this->customlib->getStaffRole();
                        $role           = json_decode($role_array);
                        $staff_role     = $role->name;
                        if ($staff_role == 'Super Admin') {
                        ?>
                            <div class="form-group" style="display: none;">
                                <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('discount_group'); ?></label>
                                <!-- <div class="col-sm-9">
                                    <select class="form-control modal_discount_group" id="discount_group">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger" id="amount_error"></span>
                                </div> -->
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)<small class="req"> *</small></label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5">
                                            <div class="">
                                                <input type="text" class="form-control" id="amount_discount" value="0">

                                                <span class="text-danger" id="amount_discount_error"></span>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-2 col-sm-2 ltextright">
                                            <label for="inputPassword3" class="control-label"><?php echo $this->lang->line('fine'); ?> (<?php echo $currency_symbol; ?>)<small class="req">*</small></label>
                                        </div>
                                        <div class="col-md-5 col-sm-5">
                                            <div class="">
                                                <input type="text" class="form-control" id="amount_fine" value="0">
                                                <span class="text-danger" id="amount_fine_error"></span>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group" style="display: none;">
                                <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('discount_group'); ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control modal_discount_group" id="discount_group">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger" id="amount_error"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)<small class="req"> *</small></label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5">
                                            <div class="">
                                                <input type="text" class="form-control" id="amount_discount" value="0">

                                                <span class="text-danger" id="amount_discount_error"></span>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-2 col-sm-2 ltextright">
                                            <label for="inputPassword3" class="control-label"><?php echo $this->lang->line('fine'); ?> (<?php echo $currency_symbol; ?>)<small class="req">*</small></label>
                                        </div> -->
                                        <!-- <div class="col-md-5 col-sm-5">
                                            <div class="">
                                                <input type="text" class="form-control" id="amount_fine" value="0">
                                                <span class="text-danger" id="amount_fine_error"></span>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        <?php  } ?>
                        <div class="form-group" style="display: none;">
                            <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('discount_type'); ?></label>
                            <div class="col-sm-9">
                                <select class="form-control modal_discount_group" id="discount_type">


                                    <option value="">Option</option>
                                    <option value="Early Bird offer">Early Bird offer</option>
                                    <option value="Management ">Management</option>
                                    <option value="Student union">Student union</option>
                                    <option value="MLA">MLA</option>
                                    <option value="MP">MP</option>
                                    <option value="Police">Police</option>


                                </select>
                                <span class="text-danger" id="discount_type"></span>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label for="inputPassword3" class="col-sm-3 control-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode_fee" value="discount" checked="checked"><?php echo $this->lang->line('discount'); ?>
                                </label>

                                <span class="text-danger" id="payment_mode_error"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label"><?php echo $this->lang->line('note'); ?></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" id="description" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn cfees save_button" id="load" data-action="collect" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $currency_symbol; ?> Assign Discount </button>
            </div>
        </div>
    </div>
</div>


<div class="delmodal modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo $this->lang->line('are_you_sure_want_to_delete'); ?> <b class="invoice_no"></b> <?php echo $this->lang->line('invoice_this_action_is_irreversible') ?></p>
                <p><?php echo $this->lang->line('do_you_want_to_proceed') ?></p>
                <p class="debug-url"></p>
                <input type="hidden" name="main_invoice" id="main_invoice" value="">
                <input type="hidden" name="sub_invoice" id="sub_invoice" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                <a class="btn btn-danger discountbtn-ok"><?php echo $this->lang->line('cancel'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $('#myFeesModal','#confirm-delete').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id', 0) ?>';
        var hostel_id = $('#hostel_id').val();
        var hostel_room_id = '<?php echo set_value('hostel_room_id', 0) ?>';

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
        $.extend($.fn.dataTable.defaults, {
            searching: true,
            ordering: true,
            paging: false,
            retrieve: true,
            destroy: true,
            info: false
        });
    });
</script>

<script>
    // $(document).on('click', '.dis_apply_button', function(e) {
    //     var $this = $(this);
    //     $this.button('loading');
    //     // alert('comming');
    //     // return false;
    //     var discount_payment_id = $('#discount_payment_id').val();
    //     var student_fees_discount_id = $('#student_fees_discount_id').val();
    //     var dis_description = $('#dis_description').val();

    //     $.ajax({
    //         url: '<?php echo site_url("admin/feediscount/applydiscount") ?>',
    //         type: 'post',
    //         data: {
    //             discount_payment_id: discount_payment_id,
    //             student_fees_discount_id: student_fees_discount_id,
    //             dis_description: dis_description
    //         },
    //         dataType: 'json',
    //         success: function(response) {
    //             $this.button('reset');
    //             if (response.status === "success") {
    //                 location.reload(true);
    //             } else if (response.status === "fail") {
    //                 $.each(response.error, function(index, value) {
    //                     var errorDiv = '#' + index + '_error';
    //                     $(errorDiv).empty().append(value);
    //                 });
    //             }
    //         }
    //     });
    // });
</script>

<script type="text/javascript">
    $("#myFeesModal").on('shown.bs.modal', function(e) {
        e.stopPropagation();
        var discount_group_dropdown = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        var data = $(e.relatedTarget).data();
        // console.log(data);

        var modal = $(this);
        var type = data.type;
        var amount = data.amount;
        var group = data.group;
        var fee_groups_feetype_id = data.fee_groups_feetype_id;
        var student_fees_master_id = data.student_fees_master_id;
        var student_session_id = data.student_session_id;
        var fee_category = data.feeCategory;
        var trans_fee_id = data.trans_fee_id;

        $('.fees_title').html("");
        $('.fees_title').html("<b>" + group + ":</b> " + type);
        $('#fee_groups_feetype_id').val(fee_groups_feetype_id);
        $('#student_fees_master_id').val(student_fees_master_id);
        $('#transport_fees_id').val(trans_fee_id);
        $('#fee_category').val(fee_category);

        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/geBalanceFee") ?>',
            dataType: 'JSON',
            data: {
                'fee_groups_feetype_id': fee_groups_feetype_id,
                'student_fees_master_id': student_fees_master_id,
                'student_session_id': student_session_id,
                'fee_category': fee_category,
                'trans_fee_id': trans_fee_id
            },
            beforeSend: function() {
                $('#discount_group').html("");
                $("span[id$='_error']").html("");
                $('#amount').val("");
                $('#amount_discount').val("0");
                // $('#amount_fine').val("0");
            },
            success: function(data) {

                if (data.status === "success") {
                    fee_amount = data.balance;
                    fee_type_amount = data.student_fees;
                    // $('#amount_discount').val(data.balance);
                    $('#amount_fine').val(data.remain_amount_fine);
                    $.each(data.discount_not_applied, function(i, obj) {
                        discount_group_dropdown += "<option value=" + obj.student_fees_discount_id + " data-disamount=" + obj.amount + " data-type=" + obj.type + " data-percentage=" + obj.percentage + ">" + obj.code + "</option>";
                    });
                    // $('#discount_group').append(discount_group_dropdown);

                }
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function() {}
        });
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.save_button', function(e) {
        var $this = $(this);
        var action = $this.data('action');
        $this.button('loading');
        var form = $(this).attr('frm');
        var feetype = $('#feetype_').val();
        var date = $('#date').val();
        var student_session_id = $('#std_id').val();
        // var amount = $('#amount').val();
        var amount_discount = $('#amount_discount').val();


        var student_fees_master_id = $('#student_fees_master_id').val();
        var fee_groups_feetype_id = $('#fee_groups_feetype_id').val();
        var transport_fees_id = $('#transport_fees_id').val();
        var fee_category = $('#fee_category').val();
        var payment_mode = $('input[name="payment_mode_fee"]:checked').val();
        var description = $('#description').val();
        var discount_type = $('#discount_type').val();


        // var student_fees_discount_id = $('#discount_group').val();


        $.ajax({
            url: '<?php echo site_url("studentfee/assignstudentdiscountfee") ?>',
            type: 'post',
            data: {
                action: action,
                student_session_id: student_session_id,
                date: date,
                type: feetype,
                amount_discount: amount_discount,
                student_fees_master_id: student_fees_master_id,
                fee_groups_feetype_id: fee_groups_feetype_id,
                fee_category: fee_category,
                transport_fees_id: transport_fees_id,
                payment_mode: payment_mode,
                description: description,
                discount_type: discount_type
            },
            dataType: 'json',
            success: function(response) {
                $this.button('reset');
                if (response.status === "success") {
                    if (action === "collect") {

                        location.reload(true);
                    }
                } else if (response.status === "fail") {
                    $.each(response.error, function(index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            }
        });
    });
</script>

<script>
    $(document).on('click', '.approve_discount_button', function(e) {
        var $this = $(this);
        var fee_master_id = $(this).data('student_fees_master_id');
        var student_session_id = $(this).data('student_session_id');
        var fee_session_group_id = $(this).data('fee_session_group_id');
        var fee_groups_feetype_id = $(this).data('fee_groups_feetype_id');
        var trans_fee_id = $(this).data('trans_fee_id');
        var fee_category = $(this).data('feeCategory');
        var date = $(this).data('date');
        var collected_by = $(this).data('collected_by');
        var discount_amount = $(this).data('discount_amount');
        var description = $(this).data('description');
        var discount_approve_id = $(this).data('discount_approve_id');
        var discount_type = $(this).data('discount_type');


        // Confirmation alert
        if (!confirm("Are you sure you want to approve this discount?")) {
            return; // Exit the function if the user cancels
        }

        $this.button('loading');

        $.ajax({
            url: '<?php echo site_url("admin/feediscount_assign/approvediscount") ?>',
            type: 'post',
            data: {
                student_fees_master_id: fee_master_id,
                student_session_id: student_session_id,
                fee_groups_feetype_id: fee_groups_feetype_id,
                fee_session_group_id: fee_session_group_id,
                discount_approve_id: discount_approve_id,
                fee_category: fee_category,
                date: date,
                collected_by: collected_by,
                amount_discount: discount_amount,
                description: description,
                discount_type: discount_type


            },
            dataType: 'json',
            success: function(response) {
                $this.button('reset');
                if (response.status === "success") {
                    location.reload(true);
                } else if (response.status === "fail") {
                    $.each(response.error, function(index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            }
        });
    });
</script>
<script>




    $('#confirm-delete').on('show.bs.modal', function(e) {
        $('.invoice_no', this).text("");
        $('#main_invoice', this).val("");
        $('#sub_invoice', this).val("");
        $('.invoice_no', this).text($(e.relatedTarget).data('invoiceno'));
        $('#main_invoice', this).val($(e.relatedTarget).data('main_invoice'));
        $('#sub_invoice', this).val($(e.relatedTarget).data('sub_invoice'));
    });

    $('#confirm-discountdelete').on('show.bs.modal', function(e) {
        $('.discount_title', this).text("");
        $('#discount_id', this).val("");
        $('.discount_title', this).text($(e.relatedTarget).data('discounttitle'));
        $('#discount_id', this).val($(e.relatedTarget).data('discountid'));
    });

    $('#confirm-delete').on('click', '.discountbtn-ok', function(e) {
        var $modalDiv = $(e.delegateTarget);
        var main_invoice = $('#main_invoice').val();
        var sub_invoice = $('#sub_invoice').val();

        $modalDiv.addClass('modalloading');
        $.ajax({
            type: "post",
            url: '<?php echo site_url("studentfee/deleteDiscountFee") ?>',
            dataType: 'JSON',
            data: {
                'main_invoice': main_invoice,
                'sub_invoice': sub_invoice
            },
            success: function(data) {
                $modalDiv.modal('hide').removeClass('modalloading');
                location.reload(true);
            }
        });
    });


</script>