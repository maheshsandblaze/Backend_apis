<style type="text/css">
    .page-break {
        display: block;
        page-break-before: always;
    }

    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }

        .col-sm-1,
        .col-sm-2,
        .col-sm-3,
        .col-sm-4,
        .col-sm-5,
        .col-sm-6,
        .col-sm-7,
        .col-sm-8,
        .col-sm-9,
        .col-sm-10,
        .col-sm-11,
        .col-sm-12 {
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
            border: 0.5px solid;
            border-radius: 8px;
            padding: 10px 10px;
        }

        .print_footer {
            border: 0.5px solid;
            border-radius: 8px;
            padding: 10px 10px;
            margin-left: -10px;
            width: 98%;
            font-size: 8pt;
        }
    }
</style>
<?php

function convertToWords($amount)
{
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


if (!empty($student_details)) {
    $count = 0;
    foreach ($student_details as $student_key => $student_value) {






?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row header">
                        <div class="col-sm-12 ">

                            <img src="<?php echo $this->media_storage->getImageURL('/uploads/cbseexam/template/header_image/abadb354d30c61d71659d65cc64cf95e.png'); ?>" style="height: 100px;width: 100%;">

                            <!-- <h2>SRIGURU JUNIOR COLLEGE</h2>
                        <h3>SRI GURU</h3>
                        <h4>ITJEEMAINNEET</h4>
                        <p>Survey.No:188,188AA,Koheda Road AVNlET Adjacent To NEHRU ORR Service Road.Pin 501510</p> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="text-align: center;">
                            <h1 style="font-weight:lighter;text-decoration:underline;">No Due Certificate</h1>
                        </div>
                    </div>


                    <div class="row" style="margin-top: 20px;">
                        <div class="col-sm-6">
                            <p style="font-size:16px;">Admission No : <b><?php echo $student_value->admission_no; ?></b></p>
                        </div>
                        <div class="col-sm-6 text-right" style="display: flex; justify-content: flex-end;">
                            <p style="font-size:16px;">Date: <b><?php echo date('d-m-Y'); ?></b></p>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-sm-12">
                            <p style="font-size:18px;">This is to certify that <b><?php echo " " . $student_value->name; ?></b> S/O/D/O Mr . <b><?php echo " " . $student_value->father_name; ?></b> is a Bonafide student of our institution studying <?php echo " " . $student_value->class; ?> has paid Tuition Fee Rs. <?php echo " " . amountFormat($student_value->deposit) . "/"; ?> <?php echo " (" .  convertToWords($student_value->deposit) . ") "; ?> for the year <?php echo " " . $sch_setting->session; ?>. </p>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 40px;">
                        <div class="col-sm-6">
                            <p style="font-size:16px;font-weight:bold;"></p>
                        </div>
                        <div class="col-sm-6 text-right;">
                            <p style="font-size:16px;font-weight:bold;text-align:right;"></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>



        <div class="pagebreak" style="padding-bottom: 50px;"> </div>
<?php
        $count++;

        if ($count % 2 == 0 && $count != count($student_details)) {
            echo '<div class="pagebreak"></div>';
        }
    }
}
?>