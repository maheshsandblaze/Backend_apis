<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>


<div>
    <?php if (!empty($studentData)) { ?>

        <!-- <div class="col-lg-12">
            <div class="form-horizontal">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('date'); ?> <small class="req"> *</small></label>
                    <div class="col-sm-9">
                        <input id="date" name="date" placeholder="" type="text" class="form-control date_fee" value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" readonly="readonly" autocomplete="off">
                        <span id="form_collection_collected_date_error" class="text text-danger"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="studentname" class="col-sm-3 control-label">Student Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="studentname" value="<?php echo $studentData->firstname; ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="class" class="col-sm-3 control-label">Class</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="class" value="<?php echo $studentData->class; ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="section" class="col-sm-3 control-label">Section</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="section" value="<?php echo $studentData->section; ?>" readonly>
                    </div>
                </div>
            </div>
        </div> -->
        <div class=" ">
    <div class="col-lg-12">
        <div class="form-horizontal">
            <div class="col-lg-12">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('date'); ?> <small class="req"> *</small></label>
                    <div class="col-sm-9">
                        <input id="date" name="collected_date" placeholder="" type="text" class="form-control date_fee" value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" readonly="readonly" autocomplete="off">
                        <span id="form_collection_collected_date_error" class="text text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('payment_mode'); ?></label>
                    <div class="col-sm-9">
                        <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="Discount" class="payment_mode"> <?php echo $this->lang->line('discount'); ?></label>
                        <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="Cash" checked="checked" class="payment_mode"> <?php echo $this->lang->line('cash'); ?></label>
                        <!-- <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="Cheque" class="payment_mode"> <?php echo $this->lang->line('cheque'); ?></label> -->
                        <!-- <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="DD" class="payment_mode"><?php echo $this->lang->line('dd'); ?></label> -->
                        <!-- <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="bank_transfer" class="payment_mode"><?php echo $this->lang->line('bank_transfer'); ?>
                        </label> -->

                        <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="card" class="payment_mode"><?php echo $this->lang->line('card'); ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="payment_mode_fee" value="upi" class="payment_mode"><?php echo $this->lang->line('upi'); ?>
                        </label>
                        <span class="text-danger" id="payment_mode_error"></span>
                    </div>
                    <span id="form_collection_payment_mode_fee_error" class="text text-danger"></span>
                </div>
            </div>

            <div class="col-lg-12"  >
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('reference_no') ?></label>
                    <div class="col-sm-6">

                        <input type="text" name="reference_no" id="reference_no" class="form-control">

                    </div>

             
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('note') ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" rows="3" name="fee_gupcollected_note" id="description" placeholder=""></textarea>
                        <span id="form_collection_fee_gupcollected_note_error" class="text text-danger"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

    <?php } else { ?>

        <div class="col-lg-12">
            <div class="form-group row">
                <div class="alert alert-info">
                    <?php echo $this->lang->line('no_record_found'); ?>
                </div>
            </div>
        </div>

    <?php } ?>
</div>
