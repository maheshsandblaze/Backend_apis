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
                    <form action="<?php echo site_url('financereports/studentdayacademicreport') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_from'); ?> <small class="req"> *</small></label>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from') ?>" autocomplete="off">
                                        <input type="hidden" name="search_type" value="period">
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_to'); ?> <small class="req"> *</small></label>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to') ?>" autocomplete="off">
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div>

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
                                <!-- <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('search_type'); ?></label>
                                        <select id="search_type" name="search_type" class="form-control">
                                            <?php
                                            foreach ($payment_type as $payment_key => $payment_value) {
                                            ?>
                                                <option value="<?php echo $payment_key; ?>" <?php echo set_select('search_type', $payment_key, set_value('search_type')); ?>><?php echo $payment_value; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                    </div>
                                </div> -->

                                <!-- <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('acadamic_type'); ?></label>
                                        <select id="acadamic_type" name="acadamic_type" class="form-control">

                                            <option value="">select</option>

                                            <option value="1" <?php if (set_value('acadamic_type') == 1) echo "selected=selected" ?>>Academic</option>
                                            <option value="2" <?php if (set_value('acadamic_type') == 2) echo "selected=selected" ?>>Non-Academic</option>


                                        </select>
                                        <span class="text-danger"><?php echo form_error('acadamic_type'); ?></span>
                                    </div>
                                </div> -->
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
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('studentdayacademicreport'); ?></h3>
                                </div>
                                <div class="box-body table-responsive">
                                   
                                    <div class="download_label">


                                        <?php echo $this->lang->line('studentdayacademicreport') . " <br>";
                                                                // $this->customlib->get_date();
                                        ?>
                                    </div>
                                    <!-- <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()"><i class="fa fa-print"></i></a> <button class="btn btn-default btn-xs pull-right" id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </button> -->
                                    <table class="table table-striped table-hover example" id="headerTable">
                                        <thead>
                                            <tr>
                                                <th class="text text-left">S No</th>
                                                <th class="text text-left" width="8%">Class</th>
                                                <th class="text text-left">Student Name</th>
                                                <th class="text text-left">Payment ID</th>
                                                <?php foreach ($feeTypes as $type) { ?>
                                                    <th class="text text-center"><?php echo $type; ?></th>
                                                <?php } ?>
                                                <th class="text-right" width="9%">Total Fees</th>
                                                <th>Mode</th>
                                                <th>Collected By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalFees = 0;
                                            $grandTotal = array_fill_keys($feeTypes, 0);
                                                    $sno = 1;

                                            foreach ($student_due_fee as $student) {
                                            ?>
                                                        <tr>
                                                    <td><?php echo $sno++; ?></td>
                                                    <td><?php echo $student['class'] . " (" . $student['section'] . ")"; ?></td>
                                                    <td><?php echo $student['firstname'] . " " . $student['lastname']; ?></td>
                                                    <td><?php echo $student['id'] . "/" . $student['inv_no']; ?></td>
                                                            <?php
                                                    $totalStudentFee = 0;
                                                    foreach ($feeTypes as $type) {
                                                        $paidAmount = isset($student['feetypePaidAmount'][$type]) ? $student['feetypePaidAmount'][$type] : 0;
                                                        echo "<td class='text-center'>" . number_format($paidAmount, 2) . "</td>";
                                                        $totalStudentFee += $paidAmount;
                                                        $grandTotal[$type] += $paidAmount;
                                                    }
                                                    ?>
                                                    <td class="text-right"><?php echo number_format($totalStudentFee, 2); ?></td>
                                                    <td><?php echo ($student['payment_mode'] !="Cash")?$this->lang->line($student['payment_mode']):$student['payment_mode']; ?></td>
                                                    <td><?php echo strtoupper($student['received_byname']['name']); ?></td>
                                                        </tr>
                                            <?php
                                                $totalFees += $totalStudentFee;
                                            }
                                            ?>
                                            <tr class="box box-solid total-bg">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">Total</td>
                                                <?php
                                                foreach ($feeTypes as $type) {
                                                    echo "<td class='text-center'>" . number_format($grandTotal[$type], 2) . "</td>";
                                                }
                                                ?>
                                                <td class="text-right"><?php echo number_format($totalFees, 2); ?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>



                                </div>
                            </div>
                    </div>

                <?php
                            //     }
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
    // document.getElementById("print").style.display = "block";
    // document.getElementById("btnExport").style.display = "block";

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