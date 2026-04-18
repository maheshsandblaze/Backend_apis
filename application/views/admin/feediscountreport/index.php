<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat();?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('fees_discount'); ?></h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive overflow-visible">
                            <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('offline_bank_payments'); ?>">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <!-- <th>Receipt No</th> -->
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <!-- <th><?php echo $this->lang->line('class'); ?></th> -->
                                        <th><?php echo $this->lang->line('payment_date'); ?></th>
                                        <th>
                                        <?php echo $this->lang->line('discount_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th style="text-align: left;">
                                            <?php echo $this->lang->line('description') ?>
                                        </th>
                                        <!-- <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if (empty($fee_disount_payments)) {

                                            ?>
                                    <?php
                                        } else {
                                            $count = 1;
                                            $receipt_prefix = 23;
                                            foreach ($fee_disount_payments as $key => $fee_value) {

                                              

                                                ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $fee_value['admission_no']; ?></td>
                                            <td>
                                            <?php echo $this->customlib->getFullName($fee_value['firstname'],$fee_value['middlename'],$fee_value['lastname'],$sch_setting->middlename,$sch_setting->lastname);  ?>
                                            </td>
                                            <!-- <td>
                                                <?php echo $fee_value->class . " (" . $fee_value->section . ")"; ?>
                                            </td> -->
                                            <td>
                                                <?php
                                                    echo date('d/m/Y', strtotime($fee_value['date']));
                                                    ?>
                                            </td>
                                            <td>
                                                <?php echo $currency_symbol.amountFormat((float) $fee_value['amount_discount'], 2, '.', ''); ?>
                                            </td>
                                            <!-- <td>
                                                <?php echo $fee_value->fee_types; ?>
                                            </td> -->
                                            <td style="text-align: left;">
                                                <?php echo $fee_value['description']; ?>
                                            </td>
                                            <!-- <td class="text-right noExport">
                                                <button  class="btn btn-xs btn-default printDoc" data-receipt_id="<?php echo $fee_value->id; ?>" data-student_id="<?php echo $fee_value->student_session_id; ?>" data-class_id="<?php echo $fee_value->class_id; ?>" data-section_id="<?php echo $fee_value->section_id; ?>" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i> </button>
                                            </td> -->
                                        </tr>
                                        <?php
                                            $count++;
                                            }
                                        }
                                    ?>                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script type="text/javascript">
$(document).ready(function () {    
$(document).on('click', '.printDoc', function () {
            var receipt_id = $(this).data('receipt_id');
            var student_id = $(this).data('student_id');
            var class_id = $(this).data('class_id');
            var section_id = $(this).data('section_id');
            console.log(receipt_id);
            console.log(student_id);
            console.log(class_id);
            console.log(section_id);
                $.ajax({
                    url: '<?php echo site_url("admin/feesreceipt/printStudentGroupFees") ?>',
                    type: 'post',
                    dataType:"JSON",
                    data: {'student_id': student_id, 'class_id': class_id, 'section_id': section_id, 'receipt_id': receipt_id},
                    success: function (response) {

                        Popup(response.page);
                    }
                });
        });
    });
</script>

<script>
    var base_url = '<?php echo base_url() ?>';

    function Popup(data, winload = false)
    {
        var frame1 = $('<iframe />').attr("id", "printDiv");
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
        document.getElementById('printDiv').contentWindow.focus();
        document.getElementById('printDiv').contentWindow.print();
        $("#printDiv", top.document).remove();
            if (winload) {
                window.location.reload(true);
            }
        }, 500);

        return true;
    }
</script>