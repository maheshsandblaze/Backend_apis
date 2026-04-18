<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    .collect_grp_fees {
        font-size: 15px;
        font-weight: 600;
        padding-bottom: 15px;
    }

    .fees-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .fees-list>.item {
        border-radius: 3px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        padding: 10px 0;
        background: #fff;
    }

    .fees-list>.item:before,
    .fees-list>.item:after {
        content: " ";
        display: table;
    }

    .fees-list>.item:after {
        clear: both;
    }

    .fees-list .product-img {
        float: left;
    }

    .fees-list .product-img img {
        width: 50px;
        height: 50px;
    }

    .fees-list .product-info {
        margin-left: 0px;
    }

    .fees-list .product-title {
        font-weight: 600;
        font-size: 15px;
        display: inline;
    }

    .fees-list .product-title span {

        font-size: 15px;
        display: inline;
        font-weight: 100 !important;
    }

    .fees-list .product-description {
        display: block;
        color: #999;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .fees-list-in-box>.item {
        -webkit-box-shadow: none;
        box-shadow: none;
        border-radius: 0;
        border-bottom: 1px solid #f4f4f4;
    }

    .fees-list-in-box>.item:last-of-type {
        border-bottom-width: 0;
    }

    .fees-footer {
        border-top-color: #f4f4f4;
    }

    .fees-footer {
        padding: 15px 0px 0px 0px;
        text-align: right;
        border-top: 1px solid #e5e5e5;
    }
</style>

<div class="table-container">
    <div class="row">
        <form id="getchallandetails" method="post" class="" enctype="multipart/form-data">
        
            <input  name="student_id" type="hidden" value="<?php echo $student_session_id; ?>" autocomplete="off">
            <div class="col-lg-3">
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('date'); ?> <small class="req"> *</small></label>
                    <div class="col-sm-9">
                        <!--<input id="date" name="collected_date" placeholder="" type="text" class="form-control date_fee" value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>" readonly="readonly" autocomplete="off">-->
                        <select autofocus="" id="due_date" name="due_date" class="form-control" >
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php
                            foreach ($student_duedates as $duedates) {
                            ?>
                            <option value="<?php echo $duedates['due_date'] ?>" <?php if (set_value('due_date') == $duedates['due_date']) echo "selected=selected" ?>><?php echo $duedates['due_date']; ?></option>
                            <?php
                                $count++;
                            }
                            ?>
                        </select>
                        <span id="form_collection_collected_date_error" class="text text-danger"></span>
                    </div>
                </div>
            </div>
            

            <div class="col-lg-7">
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 control-label"> <?php echo $this->lang->line('bank') ?></label>
                    <div class="col-sm-6">

                        <!--<input type="text" name="reference_no" id="reference_no" class="form-control">-->
                        <select class="form-control" name="bankdetails" id="bankdetails">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php
                            foreach ($bank_data as $data) {
                            ?>
                            <option value="<?php echo $data['id'] ?>" <?php if (!empty($bankdetails) && $bankdetails == $data['id']) echo 'selected="selected"'; ?>><?php echo $data['account_name'] . " (" . $data['account_number'] . ")"; ?></option>
                            <?php
                                $count++;
                            }
                            ?>
                            
                        </select>

                    </div>

             
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label for="exampleInputEmail1"></label>
                    <button type="submit" class="btn btn-info" id="submit">Fetch</button>
                </div>
            </div>
            
        
        </form>
        </div>
        
        <?php
        
    if (isset($resultlist_data) && !empty($resultlist_data)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <form id="formadd" method="post" class="ptt10" enctype="multipart/form-data">
                <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover example table-fixed-header">
                <thead class="header">
                    <tr>
                        <th style="width: 10px"><input type="checkbox" id="challan_fees" /></th>
                        <th><?php echo $this->lang->line('fees_group'); ?></th>
                        <th><?php echo $this->lang->line('fees_type'); ?></th>
                        <th>Invoice Date</th>
                        
                        
                        <th><?php echo $this->lang->line('amount') ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                                 
                            
                            <input  name="student_id" type="hidden" value="<?php echo $student_session_id; ?>">

                            <?php
                            if (empty($resultlist_data)) {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                </tr>
                                <?php
                            } else {
                                
                                foreach ($resultlist_data as $data) {
                                    ?>

                                

                                <tr class="">
                                <td>
                                    <input class="checkbox" type="checkbox" name="fees[]" value="<?php echo $data['fee_group_name']; ?>"
                                            data-fee_group="<?php echo $data['fee_group_name']; ?>"
                                            data-fee_type="<?php echo $data['feestype']; ?>"
                                            data-due_date="<?php echo $data['due_date']; ?>"
                                            data-amount="<?php echo $data['fee_amount']; ?>"
                                            data-student_id="<?php echo $student_session_id; ?>"
                                            data-bankdetails="<?php echo $bankdetails; ?>"
                                            />
                                </td>                               
                                <td><?php echo $data['fee_group_name']; ?></td>
                                <td><?php echo $data['feestype'];?></td>
                                <td><?php echo $data['due_date']; ?></td>
                                <td><?php echo amountFormat($data['fee_amount']); ?></td>
                                
                                                                
                                

                      
                                </tr> 
                                <?php
                            }
                        }
                        ?>
                        </tbody>
            </table>
        </div>    
        
        <div class="modal-footer clearboth mx-nt-lr-15 pb0">
            <button type="submit" class="btn btn-info pull-right" id="submit"><?php echo $this->lang->line('save') ?></button>
        </div>
        
        </form>             

            </div>
        </div>
            <?php
    } else {
        ?>

        <div class="alert alert-info">
            <?php echo $this->lang->line('no_record_found'); ?>
        </div>
        <?php
    }
    ?>
    
</div>


    <script>
        function updateTotalPay() {
            try {
                var total = 0;
                var inputs = document.querySelectorAll('[name^="fee_amount_"]');
                inputs.forEach(function(input) {
                    total += parseFloat(input.value) || 0;
                });
                document.getElementById('total_pay').innerHTML = '<span class="pull-right"><?php echo $currency_symbol; ?>' + total.toFixed(2) + '</span>';
                document.getElementById('total_amount').value = total.toFixed(2);
            } catch (error) {
                console.error('Error calculating total pay:', error);
            }
        }

        var inputs = document.querySelectorAll('[name^="fee_amount_"]');
        inputs.forEach(function(input) {
            input.addEventListener('input', updateTotalPay);
        });

        updateTotalPay();

        function validateAmount(input, originalAmount) {
            var enteredAmount = parseFloat(input.value) || 0;
            if (enteredAmount > originalAmount) {
                input.value = originalAmount;
            }
        }
        
        $("#getchallandetails").on('submit', function (e) {
            e.preventDefault(); 
    
            var due_date = $("#due_date").val();
            var bankdetails = $("#bankdetails").val();
            var student_id = $("input[name='student_id']").val();
    
            $.ajax({
                url: "<?php echo site_url('studentfee/getchallanfee'); ?>",
                type: "POST",
                data: {
                    due_date: due_date,
                    bankdetails: bankdetails,
                    student_id: student_id
                },
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if (res.status === "0") {
                        var message = "";
                        $.each(res.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        $(".table-container").html(res.view);
    				    
    				}
                },
                error: function (xhr) {
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
            });
        });
    </script>
    
    <script>
        $(document).ready(function () {
            $("#formadd").on("submit", function (e) {
                e.preventDefault();
        
                var student_id = $("input[name='student_id']").val();
                var total_amount = 0;
                var fee_groups = "";
                var fee_types = "";
                var due_date = "";
                var bank_details = "";
        
                var selectedFees = $(".checkbox:checked");
                if (selectedFees.length === 0) {
                    alert("Please select at least one fee.");
                    return;
                }
        
                selectedFees.each(function () {
                    var fee_group = $(this).data("fee_group");
                    var fee_type = $(this).data("fee_type");
                    var amount = parseFloat($(this).data("amount"));
                    due_date = $(this).data("due_date"); // Assume all have the same due date
                    bank_details = $(this).data("bankdetails"); // Assume all have the same bank details
        
                    total_amount += amount;
        
                    // Concatenate fee groups & types uniquely
                    if (!fee_groups.includes(fee_group)) {
                        fee_groups = fee_groups ? fee_groups + ", " + fee_group : fee_group;
                    }
                    fee_types = fee_types ? fee_types + ", " + fee_type : fee_type;
                });
        
                var collected_by = "<?php echo $this->customlib->getAdminSessionUserName() . ' (' . $staff_record['employee_id'] . ')'; ?>";
                var current_date = new Date().toISOString().slice(0, 10); // Today's Date
        
                // Prepare final data
                var finalData = {
                    student_id: student_id,
                    amount: total_amount.toFixed(2),
                    fee_groups: fee_groups,
                    fee_types: fee_types,
                    generated_by: collected_by,
                    due_date: due_date,
                    date: current_date,
                    bank: bank_details
                };
        
                // Send AJAX request
                $.ajax({
                    url: "<?php echo site_url('studentfee/save_challan_fees'); ?>",
                    type: "POST",
                    data: finalData,
                    dataType: "json",
                    success: function (response) {
                        if (response.status == "success") {
                            alert("Generate fee challan successfully.");
                            location.reload(); // Refresh the page after submission
                        } else {
                            alert("Error saving fees.");
                        }
                    }
                });
            });
        });

    </script>