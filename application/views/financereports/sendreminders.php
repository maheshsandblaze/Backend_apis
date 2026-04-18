<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>
                    <form action="<?php echo site_url('financereports/send_reminders') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($section_list as $value) {
                                            ?>
                                                <option <?php
                                                        if ($value['section_id'] == $section_id) {
                                                            echo "selected";
                                                        }
                                                        ?> value="<?php echo $value['section_id']; ?>"><?php echo $value['section']; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
							 <div class="col-md-3">

                            <button type="submit" class="btn btn-primary btn-sm "><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>




                            </div>
                        </div>
                        
                    </form>


                    <div class="row">

                        <?php
                         if (isset($student_due_fee) && !empty($student_due_fee)) {
                        ?>

                            <div class="" id="transfee">
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"><i class="fa fa-users"></i>Un-paid Fees Students List</h3>
									<button id="sendReminderBtn" type="button" class="btn btn-primary btn-sm pull-right">Send Reminder</button>
                                </div>
                                <div class="box-body table-responsive">
                                    <div class="download_label"><?php
                                                                echo $this->lang->line('balance_fees_report');
                                                                ?></div>
                                   <!-- <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()"><i class="fa fa-print"></i></a> <button class="btn btn-default btn-xs pull-right" id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </button>-->
                                    <table class="table table-striped table-hover " id="headerTable">
                                        <!-- example  -->
                                        <thead>
                                            <tr>
                                                <th class="text text-left">Select All</br><input type="checkbox" id="select-all"></th>
                                                <th class="text text-left"><?php echo $this->lang->line('s_no'); ?></th>
                                                <?php if ($sch_setting->roll_no) { ?>
                                                    <!-- <th class="text text-left"><?php echo $this->lang->line('roll_number'); ?></th> -->
                                                <?php } ?>
                                                <th class="text text-left"><?php echo "Ad no"; ?></th>
                                                <th ><?php echo $this->lang->line('class'); ?></th>
                                                <!-- <th><?php echo $this->lang->line('section'); ?></th> -->

                                                <th><?php echo $this->lang->line('roll_no'); ?></th>

                                                <th class="text text-left"><?php echo $this->lang->line('student_name'); ?></th>



                                                <?php if ($sch_setting->father_name) { ?>
                                                    <th class="text text-left"><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php } ?>

                                                <th class="text text-left"><?php echo $this->lang->line('father_phone'); ?></th>


                                                <?php if (!empty($feeTypes)) {
                                                    foreach ($feeTypes as $ftype => $type) {  ?>

                                                        <th class="text text-left"><?php echo $ftype; ?></th>

                                                <?php   }
                                                } ?>
                                                <!-- <th class="text text-left">JUN</th>
                                                <th class="text text-left">JUL</th>
                                                <th class="text text-left">AUG</th>
                                                <th class="text text-left">SEP</th>
                                                <th class="text text-left">OCT</th>
                                                <th class="text text-left">NOV</th>
                                                <th class="text text-left">DEC</th>
                                                <th class="text text-left">JAN</th>
                                                <th class="text text-left">FEB</th>
                                                <th class="text text-left">MAR</th>
                                                <th class="text text-left">APR</th> -->
                                                <!-- <th class="text-right" width="9%"><?php echo $this->lang->line('total_fees'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th> -->
                                                <!-- <th class="text-right" width="8%"><?php echo $this->lang->line('paid_fees'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th> -->

                                                <!-- <th class="text text-right" width="8%"><?php echo $this->lang->line('discount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th> -->
                                                <!-- <th class="text text-right"><?php echo $this->lang->line('fine'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th> -->

                                                <th class="text-right">Balance<span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($resultarray)) {
                                                $totalfeelabel = 0;
                                                $depositfeelabel = 0;
                                                $discountlabel = 0;
                                                $finelabel = 0;

                                                $grdtotalfeelabel = array();
                                                // $depositfeelabel = array();
                                                // $discountlabel = array();
                                                // $finelabel = array();

                                                foreach ($resultarray as $key => $section) {


                                                    // 
                                                    $balancelabel = 0;




                                                    foreach ($section['result'] as $stukey =>  $data) {

                                                        $subbalancelabel = array();






                                            ?>


                                                        <tr>
                                                            <td style="font-weight:bold;" colspan="3"><?php echo  $stukey; ?></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                            <td></td>
                                                            <!-- <td></td> -->


                                                            <!-- <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td> -->

                                                            <?php if (!empty($feeTypes)) {
                                                                foreach ($feeTypes as $ftype => $type) {  ?>

                                                                    <td></td>

                                                            <?php   }
                                                            } ?>

                                                            <!-- <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td> -->


                                                        </tr>



                                                        <?php

                                                        $count = 1;
//echo '<pre>'; print_r($data);exit;

                                                        foreach ($data as $students) {

                                                            // echo "<pre>";
                                                            // print_r($students);exit;

                                                            // $totalfeelabel[] = number_format($students->totalfee, 2, '.', '');
                                                            // $depositfeelabel[] = number_format($students->deposit, 2, '.', '');
                                                            // $discountlabel[] = number_format($students->discount, 2, '.', '');
                                                            // $finelabel[] = number_format($students->fine, 2, '.', '');
                                                            // $balancelabel[] = number_format($students->balance, 2, '.', '');


                                                            $totalfeelabel += $students->totalfee;
                                                            $depositfeelabel += $students->deposit;
                                                            $discountlabel += $students->discount;
                                                            $finelabel += $students->fine;
                                                            $balancelabel += $students->balance;

                                                            $subbalancelabel[] = number_format($students->balance, 2, '.', '');

                                                        ?>

                                                            <tr>
                                                                <!--<td><input type="checkbox" name="stu_session_id" value="<?php echo $students->student_session_id; ?>" class="student-checkbox"></td>-->
											<td>
                                                <input type="checkbox" name="stu_session_ids[]" value="<?php echo $students->student_session_id; ?>" class="student-checkbox">
                                                <input type="hidden" name="father_phone[<?php echo $students->student_session_id; ?>]" value="<?php echo $students->father_phone; ?>">
                                                <input type="hidden" name="balance[<?php echo $students->student_session_id; ?>]" value="<?php echo $students->balance; ?>">
                                                <input type="hidden" name="name[<?php echo $students->student_session_id; ?>]" value="<?php echo $students->name; ?>">
                                                <input type="hidden" name="adno[<?php echo $students->student_session_id; ?>]" value="<?php echo $students->admission_no; ?>">
                                                <input type="hidden" name="class[<?php echo $students->student_session_id; ?>]" value="<?php echo $students->class; ?>">
                                            </td>
																
                                                                <td><?php echo $count; ?></td>
                                                           
                                                         
                                                                <td><?php echo $students->admission_no; ?></td>

                                                                <td><?php echo $students->class."( ".$students->section. " )"; ?></td>
                                                    



                                                                <td><?php echo $students->roll_no; ?></td>


                                                                <td><?php echo $students->name; ?></td>


                                                                <?php
                                                                if ($sch_setting->father_name) { ?>
                                                                    <td><?php echo $students->father_name; ?></td>
                                                                <?php } ?>
                                                                <td><?php echo $students->father_phone; ?></td>

                                                                <?php
                                                                foreach ($students->feetypeBalances as $type => $feeBalance) { ?>
                                                                    <td><?php echo amountFormat($feeBalance); ?></td>
                                                                <?php } ?>
                                                                <!-- <td class="text-right"><?php echo amountFormat($students->totalfee); ?></td> -->

                                                                <!-- <td class="text-right"><?php echo amountFormat($students->deposit); ?></td> -->

                                                                <!-- <td class="text-right"><?php echo amountFormat($students->discount); ?></td> -->

                                                                <!-- <td class="text-right"><?php echo amountFormat($students->fine); ?></td> -->

                                                                <td class="text-right"><?php echo amountFormat($students->balance); ?></td>
                                                            </tr>
                                                        <?php

                                                            $count++;
                                                        }




                                                        ?>
        





                                                    <?php

                                                        $grdtotalfeelabel[] = array_sum($subbalancelabel);
                                                    }

                                                    ?>

                                                    
                                                <?php
                                                } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </div>

            <?php
                                            }
                                        }
            ?>



                </div>
            </div>
    </section>
</div>

<script>
    $(document).ready(function() {
    // Send Reminder Button Click
    $('#sendReminderBtn').click(function() {
        var selectedStudents = [];
        
        // Loop through each checked checkbox
        $('input.student-checkbox:checked').each(function() {
            var session_id = $(this).val();
            var father_phone = $('input[name="father_phone[' + session_id + ']"]').val();
            var balance = $('input[name="balance[' + session_id + ']"]').val();
            var name = $('input[name="name[' + session_id + ']"]').val();
            var adno = $('input[name="adno[' + session_id + ']"]').val();
            var class_name = $('input[name="class[' + session_id + ']"]').val();

            // Log collected data to verify it's correct
            console.log('Session ID:', session_id);
            console.log('Father Phone:', father_phone);
            console.log('Balance:', balance);
            console.log('Name:', name);
            console.log('Admission No:', adno);
            console.log('Class:', class_name);

            // Push the collected values into the selectedStudents array
            selectedStudents.push({
                stu_session_id: session_id,
                father_phone: father_phone,
                balance: balance,
                name: name,
                adno: adno,
                class: class_name
            });
        });

        // Check if no students are selected
        if (selectedStudents.length === 0) {
            alert("Please select at least one student.");
            return;
        }

        // Send AJAX request to the controller
        $.ajax({
            url: "<?php echo site_url('financereports/sendReminder'); ?>",
            type: 'POST',
            data: { students: selectedStudents },
            success: function(response) {
                alert("Reminders sent successfully!");
            },
            error: function() {
                alert("An error occurred while sending reminders.");
            }
        });
    });
});

</script>

<script type="text/javascript">
    function removeElement() {
        document.getElementById("imgbox1").style.display = "block";
    }

    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
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
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').html(div_data);
                }
            });
        }
    }
	
	$(document).ready(function() {
    // Handle 'Select All' checkbox change
    $('#select-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        
        // Check or uncheck all student checkboxes
        $('.student-checkbox').prop('checked', isChecked);
    });

    // Optional: Uncheck 'Select All' if any checkbox is unchecked
    $('.student-checkbox').on('change', function() {
        if (!$(this).is(':checked')) {
            $('#select-all').prop('checked', false);
        }

        // Check if all checkboxes are selected
        if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
            $('#select-all').prop('checked', true);
        }
    });
});

	
    $(document).ready(function() {
        $(document).on('change', '#class_id', function(e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });

                    $('#section_id').html(div_data);
                }
            });
        });
        $(document).on('change', '#section_id', function(e) {
            getStudentsByClassAndSection();
        });
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
    });

    function getStudentsByClassAndSection() {
        $('#student_id').html("");
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {
                'class_id': class_id,
                'section_id': section_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(i, obj) {
                    div_data += "<option value=" + obj.id + ">" + obj.firstname + " " + obj.lastname + "</option>";
                });
                $('#student_id').append(div_data);
            }
        });
    }

    $(document).ready(function() {
        $("ul.type_dropdown input[type=checkbox]").each(function() {
            $(this).change(function() {
                var line = "";
                $("ul.type_dropdown input[type=checkbox]").each(function() {
                    if ($(this).is(":checked")) {
                        line += $("+ span", this).text() + ";";
                    }
                });
                $("input.form-control").val(line);
            });
        });
    });
    $(document).ready(function() {
        $.extend($.fn.dataTable.defaults, {
            ordering: false,
            paging: false,
            bSort: false,
            info: false
        });
    });
</script>
<script>
    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";

    function printDiv() {
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        var divElements = document.getElementById('transfee').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
            "<html><head><title></title></head><body>" +
            divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        document.getElementById("print").style.display = "block";
        document.getElementById("btnExport").style.display = "block";

        location.reload(true);
    }


    function fnExcelReport() {
        var htmls = "";
        let hashid = "#headerTable";
        var uri = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
        var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };

        var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };

        htmls = $(hashid).html();

        var ctx = {
            worksheet: 'Worksheet',
            table: htmls
        }


        var link = document.createElement("a");
        link.download = "export.xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }
</script>



<script>
    $(document).ready(function() {
        // Toggle the dropdown on click
        $("#custom-select").click(function() {
            $("#custom-select-option-box").toggle();
        });

        // Close the dropdown if clicked outside
        $(document).click(function(event) {
            if (!$(event.target).closest("#checkbox-dropdown-container").length) {
                $("#custom-select-option-box").hide();
            }
        });

        // Select/Deselect all checkboxes
        $("#selectAll").change(function() {
            var isChecked = $(this).is(":checked");
            $(".custom-select-option-checkbox").prop("checked", isChecked);
        });

        // Update the Select All checkbox state based on individual checkboxes
        $(".custom-select-option-checkbox").change(function() {
            var allChecked = $(".custom-select-option-checkbox:checked").length === $(".custom-select-option-checkbox").length;
            $("#selectAll").prop("checked", allChecked);
        });
    });
</script>