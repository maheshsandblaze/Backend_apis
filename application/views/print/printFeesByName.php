<?php 
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

function convertToWords($amount) {
    $units = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine");
    $teens = array("", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen");
    $tens = array("", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety");
    $rupees = "Rupees Only";
    
    $denominations = array("", "Thousand", "Million", "Billion", "Trillion", "Quadrillion");

    $amount = (int)$amount;

    if ($amount == 0) {
        return "Zero " . $rupees;
    }

    $words = array();
    $i = 0;

    while ($amount > 0) {
        $chunk = $amount % 1000;
        
        if ($chunk > 0) {
            $chunk_words = array();

            $hundreds = floor($chunk / 100);
            if ($hundreds > 0) {
                $chunk_words[] = $units[$hundreds] . " Hundred";
            }

            $tens_units = $chunk % 100;
            if ($tens_units > 0) {
                if ($tens_units == 10) {
                    $chunk_words[] = "Ten";
                } elseif ($tens_units < 10) {
                    $chunk_words[] = $units[$tens_units];
                } elseif ($tens_units < 20) {
                    $chunk_words[] = $teens[$tens_units - 10];
                } else {
                    $tens_digit = floor($tens_units / 10);
                    $units_digit = $tens_units % 10;
                    $chunk_words[] = $tens[$tens_digit];
                    if ($units_digit > 0) {
                        $chunk_words[] = $units[$units_digit];
                    }
                }
            }

            if (!empty($chunk_words)) {
                $chunk_words[] = $denominations[$i];
            }

            $words = array_merge($chunk_words, $words);
        }

        $amount = floor($amount / 1000);
        $i++;
    }

    $result = implode(" ", $words) . " " . $rupees;

    return $result;
}

?>
<style type="text/css">
    .page-break { display: block; page-break-before: always; }
    @media print {
        .page-break { display: block; page-break-before: always; }
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 {
            width: 100%;
        }
        .col-sm-11 {
            width: 91.66666667%;
        }
        .col-sm-10 {
            width: 83.33333333%;
        }
        .col-sm-9 {
            width: 75%;
        }
        .col-sm-8 {
            width: 66.66666667%;
        }
        .col-sm-7 {
            width: 58.33333333%;
        }
        .col-sm-6 {
            width: 50%;
        }
        .col-sm-5 {
            width: 41.66666667%;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-3 {
            width: 25%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
        }
        .col-sm-pull-12 {
            right: 100%;
        }
        .col-sm-pull-11 {
            right: 91.66666667%;
        }
        .col-sm-pull-10 {
            right: 83.33333333%;
        }
        .col-sm-pull-9 {
            right: 75%;
        }
        .col-sm-pull-8 {
            right: 66.66666667%;
        }
        .col-sm-pull-7 {
            right: 58.33333333%;
        }
        .col-sm-pull-6 {
            right: 50%;
        }
        .col-sm-pull-5 {
            right: 41.66666667%;
        }
        .col-sm-pull-4 {
            right: 33.33333333%;
        }
        .col-sm-pull-3 {
            right: 25%;
        }
        .col-sm-pull-2 {
            right: 16.66666667%;
        }
        .col-sm-pull-1 {
            right: 8.33333333%;
        }
        .col-sm-pull-0 {
            right: auto;
        }
        .col-sm-push-12 {
            left: 100%;
        }
        .col-sm-push-11 {
            left: 91.66666667%;
        }
        .col-sm-push-10 {
            left: 83.33333333%;
        }
        .col-sm-push-9 {
            left: 75%;
        }
        .col-sm-push-8 {
            left: 66.66666667%;
        }
        .col-sm-push-7 {
            left: 58.33333333%;
        }
        .col-sm-push-6 {
            left: 50%;
        }
        .col-sm-push-5 {
            left: 41.66666667%;
        }
        .col-sm-push-4 {
            left: 33.33333333%;
        }
        .col-sm-push-3 {
            left: 25%;
        }
        .col-sm-push-2 {
            left: 16.66666667%;
        }
        .col-sm-push-1 {
            left: 8.33333333%;
        }
        .col-sm-push-0 {
            left: auto;
        }
        .col-sm-offset-12 {
            margin-left: 100%;
        }
        .col-sm-offset-11 {
            margin-left: 91.66666667%;
        }
        .col-sm-offset-10 {
            margin-left: 83.33333333%;
        }
        .col-sm-offset-9 {
            margin-left: 75%;
        }
        .col-sm-offset-8 {
            margin-left: 66.66666667%;
        }
        .col-sm-offset-7 {
            margin-left: 58.33333333%;
        }
        .col-sm-offset-6 {
            margin-left: 50%;
        }
        .col-sm-offset-5 {
            margin-left: 41.66666667%;
        }
        .col-sm-offset-4 {
            margin-left: 33.33333333%;
        }
        .col-sm-offset-3 {
            margin-left: 25%;
        }
        .col-sm-offset-2 {
            margin-left: 16.66666667%;
        }
        .col-sm-offset-1 {
            margin-left: 8.33333333%;
        }
        .col-sm-offset-0 {
            margin-left: 0%;
        }
        .visible-xs {
            display: none !important;
        }
        .hidden-xs {
            display: block !important;
        }
        table.hidden-xs {
            display: table;
        }
        tr.hidden-xs {
            display: table-row !important;
        }
        th.hidden-xs,
        td.hidden-xs {
            display: table-cell !important;
        }
        .hidden-xs.hidden-print {
            display: none !important;
        }
        .hidden-sm {
            display: none !important;
        }
        .visible-sm {
            display: block !important;
        }
        table.visible-sm {
            display: table;
        }
        tr.visible-sm {
            display: table-row !important;
        }
        th.visible-sm,
        td.visible-sm {
            display: table-cell !important;
        }
        .print_header {
            border:0.5px solid;
            border-radius:8px;
            padding:5px 10px;
        }
        .print_footer {
            border:0.5px solid;
            border-radius:8px;
            padding:5px 10px;
            margin-left:-10px;
            width:98%;
            font-size: 8pt;
        }
    }
</style>

<html lang="en">
    <head>
        <title><?php echo $this->lang->line('fees_receipt'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
    </head>
    <body>
      <?php 
$print_copy=explode(',', $settinglist[0]['is_duplicate_fees_invoice']);
         ?>
        <div class="container">
            <div class="row">
                

                        
                    <div class="col-sm-6">
                        <div class="row header ">
                            <div class="col-sm-12">                                

                                <img  src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/'.$this->setting_model->get_receiptheader());?>" style="height: 100px;width: 100%;">
                                
                            </div>

                        </div>
 
                            <div class="row">
                                <div class="col-md-12 text text-center">
                                    <?php echo $this->lang->line('office_copy'); ?>
                                </div>
                            </div>

                        <div class="row table table-striped table-bordered">
                            <div class="col-xs-6 text-left">
                                <br/>
                                <div class="row">
                                    <div class="col-sm-6">
                                        Receipt No:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo sprintf('%05s', $feeList->id) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        Admn No:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $feeList->admission_no; ?>
                                    </div>
                                </div>  
                                <div class="row">
                                    <div class="col-sm-6">
                                        Student's Name:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $this->customlib->getFullName($feeList->firstname, $feeList->middlename, $feeList->lastname, $sch_setting->middlename, $sch_setting->lastname); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php echo $this->lang->line('fathers_name'); ?>:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $student['father_name']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        Class & Sec:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $feeList->class . " (" . $feeList->section . ")"; ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-xs-6 text-left">
                                <br/>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php echo $this->lang->line('date'); ?>:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
$date = date('d-m-Y');

echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php echo $this->lang->line('roll_no'); ?>:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $student['roll_no']; ?>
                                    </div>
                                </div>
                                
                    <?php  
                        if (isJSON($feeList->amount_detail)) {
                            $fee    = json_decode($feeList->amount_detail);
                            $record = $fee->{$sub_invoice_id};
                            
                            
                        }
                    ?>                    
                    
                            </div>
                        </div>
                        <hr style="margin-top: 0px;margin-bottom: 0px;" />
                        <div class="row">
                            <?php
if (!empty($feeList)) {
    ?>

                                <table class="table table-striped table-bordered" style="font-size: 8pt; width:95%">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px">S.No.</th>
                                            <th colspan="4">Particulars</th>
                                            <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
$amount    = 0;
    $discount  = 0;
    $fine      = 0;
    $total     = 0;
    $grd_total = 0;
    if (empty($feeList)) {
        ?>
                                            <tr>
                                                <td colspan="11" class="text-danger text-center">
                                                    <?php echo $this->lang->line('no_transaction_found'); ?>
                                                </td>
                                            </tr>
                                            <?php
} else {
        $count = 1;

        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $count; ?>
                                                </td>
                                                <td colspan="4">                                                     
                                                    <?php
                                                    // if ($feeList->is_system) {
                                                    //     echo $this->lang->line($feeList->name) . " (" . $this->lang->line($feeList->type) . ")";
                                                    // } else {
                                                    //     echo $feeList->name . " (" . $feeList->type . ")";
                                                    // }
                                                    ?>
                                                    Tuition Fee
                                                </td>
                                                
                                                <td class="text text-right">
                                                    <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="6">
                                                    <?php echo $this->lang->line('remarks'); ?>: <?php echo $record->description; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6">
                                                    Received Amount: <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td colspan="4" class="text text-right">                                                     
                                                    <?php
                                                        echo $this->lang->line('total');
                                                    ?>
                                                </td>
                                                
                                                <td class="text text-right">
                                                    <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>

                                            </tr>
                                            <?php
}
    ?>
                                    </tbody>
                                </table>
                                <?php
}
?>

                        </div>
                        <div class="print_footer">
                            <div class="row header">
                                <div class="col-sm-12">
                                    In Words:  <?php
    $amount = $record->amount;
            echo convertToWords($amount);
            ?>
                                </div>
                            </div>
                            
                            <div class="row header" style="margin-top: 10px">
                                <div class="col-sm-8">
                                    <?php echo $this->lang->line('note');?>: Fee once paid is not refundable
                                </div>
                                <div class="col-sm-4 text-right">
                                    Signature
                                </div>
                            </div>
                        </div>
                    </div>

    

                    
    
        
                        
                        
                        
                        <div class="col-sm-6">
                            <div class="row header ">
                                <div class="col-sm-12">
                                    <?php
?>

                                    <img  src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/'.$this->setting_model->get_receiptheader());?>" style="height: 100px;width: 100%;">
                                    <?php ?>
                                </div>

                            </div>
             
                                <div class="row">
                                    <div class="col-md-12 text text-center">
                                        <?php echo $this->lang->line('student_copy'); ?>
                                    </div>
                                </div>
    
                            <div class="row table table-striped table-bordered">
                                <div class="col-xs-6 text-left">
                                    <br/>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Receipt No:
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo sprintf('%05s', $feeList->id) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Admn No:
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $feeList->admission_no; ?>
                                        </div>
                                    </div>  
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Student's Name:
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $this->customlib->getFullName($feeList->firstname, $feeList->middlename, $feeList->lastname, $sch_setting->middlename, $sch_setting->lastname); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php echo $this->lang->line('fathers_name'); ?>:
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $student['father_name']; ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Class & Sec:
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $feeList->class . " (" . $feeList->section . ")"; ?>
                                        </div>
                                    </div>
                                    
                                </div>
                            <div class="col-xs-6 text-left">
                                <br/>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php echo $this->lang->line('date'); ?>:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
$date = date('d-m-Y');

echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php echo $this->lang->line('roll_no'); ?>:
                                    </div>
                                    <div class="col-sm-6">
                                        <?php echo $student['roll_no']; ?>
                                    </div>
                                </div>
                                
                    <?php  
                        if (isJSON($feeList->amount_detail)) {
                            $fee    = json_decode($feeList->amount_detail);
                            $record = $fee->{$sub_invoice_id};
                            
                            
                        }
                    ?>                    
                    
                            </div>
                        </div>
                            <hr style="margin-top: 0px;margin-bottom: 0px;" />
                            <div class="row">
                                <?php
                                    if (!empty($feeList)) {
                                 ?>
                                    <table class="table table-striped table-bordered" style="font-size: 8pt; width:95%">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px">S.No.</th>
                                                <th colspan="4">Particulars</th>
                                                <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
$amount    = 0;
        $discount  = 0;
        $fine      = 0;
        $total     = 0;
        $grd_total = 0;
        if (empty($feeList)) {
            ?>
                                                <tr>
                                                    <td colspan="11" class="text-danger text-center">
                                                        <?php echo $this->lang->line('no_transaction_found'); ?>
                                                    </td>
                                                </tr>
                                                <?php
} else {
            $count = 1;

            $a      = json_decode($feeList->amount_detail);
            $record = $a->{$sub_invoice_id};
            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $count; ?>
                                                </td>
                                                <td colspan="4">                                                     
                                                    <?php
                                                    // if ($feeList->is_system) {
                                                    //     echo $this->lang->line($feeList->name) . " (" . $this->lang->line($feeList->type) . ")";
                                                    // } else {
                                                    //     echo $feeList->name . " (" . $feeList->type . ")";
                                                    // }
                                                    ?>
                                                    Tuition Fee
                                                </td>
                                                
                                                <td class="text text-right">
                                                    <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="6">
                                                    <?php echo $this->lang->line('remarks'); ?>: <?php echo $record->description; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6">
                                                    Received Amount: <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td colspan="4" class="text text-right">                                                     
                                                    <?php
                                                        echo $this->lang->line('total');
                                                    ?>
                                                </td>
                                                
                                                <td class="text text-right">
                                                    <?php
$amount = $record->amount;
        echo $currency_symbol . (amountFormat($amount));
        ?>
                                                </td>

                                            </tr>
                                                <?php
}
        ?>
                                        </tbody>
                                    </table>
                                    <?php
}
    ?>

                            </div>
                            <div class="print_footer">
                                <div class="row header">
                                    <div class="col-sm-12">
                                        In Words:  <?php
        $amount = $record->amount;
                echo convertToWords($amount);
                ?>
                                    </div>
                                </div>
                            
                                <div class="row header" style="margin-top: 10px">
                                    <div class="col-sm-8">
                                        <?php echo $this->lang->line('note');?>: Fee once paid is not refundable
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        Signature
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                 

                
            </div>
        </div>
        <div class="clearfix"></div>
        <footer>
        </footer>
    </body>
</html>