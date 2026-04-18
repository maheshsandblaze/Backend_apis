<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php //echo $this->lang->line('fees_collection'); 
                                        ?> <small> <?php //echo $this->lang->line('filter_by_name1'); 
                                                    ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('financereports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form action="<?php echo site_url('financereports/studentacademicfeereceipt') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?><small class="req"> *</small></label>
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
                                <div class="col-md-4">
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

                                <div class="col-md-4" style="display: none;">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('due_date'); ?><small class="req"> *</small></label>
                                        <input id="due_date" name="due_date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('due_date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('due_date'); ?></span>
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="box-footer">

                            <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>


                    <div class="row">

                        <?php
                        if (isset($student_due_fee) && !empty($student_due_fee)) {
                        ?>

                            <div class="" id="transfee">

                                <div class="box-body table-responsive">
                                    <div class="download_label"><?php
                                                                echo $this->lang->line('studentacademicfeereceipt');
                                                                ?></div>

                                    <form method="post" action="<?php echo base_url('financereports/printbalancefeereceipt') ?>" id="printCard">

                                        <div class="box-header ptbnull">
                                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('studentacademicfeereceipt'); ?></h3>
                                            <button class="btn btn-info btn-sm printSelected pull-right" type="submit" name="generate" title="<?php echo $this->lang->line('generate_multiple_admit_card'); ?>"><?php echo $this->lang->line('generate'); ?></button>

                                        </div>

                                        <!-- <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()"><i class="fa fa-print"></i></a> <button class="btn btn-default btn-xs pull-right" id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </button> -->
                                        <table class="table table-striped table-hover " id="headerTable">
                                            <!-- example  -->
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select_all" /></th>

                                                    <th class="text text-left"><?php echo "Ad no"; ?></th>

                                                    <th class="text text-left"><?php echo $this->lang->line('class'); ?></th>


                                                    <th class="text text-left"><?php echo $this->lang->line('student_name'); ?></th>



                                                    <?php if ($sch_setting->father_name) { ?>
                                                        <th class="text text-left"><?php echo $this->lang->line('father_name'); ?></th>
                                                    <?php } ?>

                                                    <th class="text text-left"><?php echo $this->lang->line('father_phone'); ?></th>




                                                    <th class="text-right">Pending Amount<span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
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





                                                            <?php

                                                            $count = 1;


                                                            foreach ($data as $students) {




                                                                $totalfeelabel += $students->totalfee;
                                                                $depositfeelabel += $students->deposit;
                                                                $discountlabel += $students->discount;
                                                                $finelabel += $students->fine;
                                                                $balancelabel += $students->balance;

                                                                $subbalancelabel[] = number_format($students->balance, 2, '.', '');

                                                            ?>

                                                                <tr>
                                                                    <td class="text-center">
                                                                        <input type="checkbox" class="checkbox center-block" name="student_session_id[]" value="<?php echo $students->student_session_id ?>" />
                                                                        <input type="hidden" name="due_date" value="<?php echo $due_date ?>" />

                                                                    </td>
                                                                    <td><?php echo $students->admission_no; ?></td>

                                                                    <td><?php echo $students->class . "(" . $students->section . ")"; ?></td>


                                                                    <td><?php echo $students->name; ?></td>


                                                                    <?php
                                                                    if ($sch_setting->father_name) { ?>
                                                                        <td><?php echo $students->father_name; ?></td>
                                                                    <?php } ?>
                                                                    <td><?php echo $students->father_phone; ?></td>



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

                                                        <tr class="box box-solid total-bg">



                                                            <td></td>
                                                            <td></td>
                                                            <td></td>



                                                            <?php if ($sch_setting->roll_no) { ?>
                                                                <!-- <td></td> -->
                                                            <?php }

                                                            if ($sch_setting->father_name) {
                                                            ?>
                                                                <td></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td></td>
                                                            <td><?php echo $this->lang->line('total'); ?></td>

                                                            <td class="text-right"><?php echo amountFormat(array_sum($grdtotalfeelabel)); ?></td>
                                                        </tr>
                                                    <?php
                                                    } ?>
                                            </tbody>
                                        </table>

                                    </form>
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


<script type="text/javascript">
    $(document).on('submit', 'form#printCard', function(e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serializeArray();
        var list_selected = $('form#printCard input[name="student_session_id[]"]:checked').length;
        console.log(list_selected);
        if (list_selected > 0) {

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: formdata, // serializes the form's elements.
                dataType: "JSON", // serializes the form's elements.
                beforeSend: function() {
                    subsubmit_button.button('loading');
                },
                success: function(response) {
                    Popup(response.page);

                },
                error: function(xhr) { // if error occured

                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    subsubmit_button.button('reset');
                },
                complete: function() {
                    subsubmit_button.button('reset');
                }
            });
        } else {
            confirm("<?php echo $this->lang->line('please_select_student'); ?>");
        }

    });

    $(document).on('click', '#select_all', function() {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';

    function Popup(data) {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";

        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);

        return true;
    }
</script>