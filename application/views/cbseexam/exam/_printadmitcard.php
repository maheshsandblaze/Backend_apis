<style type="text/css">
    @media print {
        .pagebreak {
            display: none;
        }

        /* page-break-after works, as well */
    }

    * {
        padding: 0;
        margin: 0;
    }

    /*body{padding: 0; margin:0; font-family: arial; color: #000; font-size: 14px; line-height: normal;}*/
    .tableone {}

    .tableone td {
        padding: 5px 10px
    }

    table.denifittable {
        border: 1px solid #999;
        border-collapse: collapse;
    }

    .denifittable th {
        padding: 5px 5px;
        font-weight: normal;
        border-collapse: collapse;
        border-right: 1px solid #999;
        border-bottom: 1px solid #999;
    }

    .denifittable td {
        padding: 5px 5px;
        font-weight: bold;
        border-collapse: collapse;
        border-left: 1px solid #999;
    }

    .mark-container {
        width: 1000px;
        position: relative;
        z-index: 2;
        margin: 0 auto;
        padding: 20px 30px;
    }

    .tcmybg {
        background: top center;
        background-size: 100% 100%;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1;
    }

    .tablemain {
        position: relative;
        z-index: 2
    }
</style>
<?php
if (!empty($student_details)) {
    $count = 0;
    foreach ($student_details as $student_key => $student_value) {
?>
        <div class="mark-container">

            <!--<img src="<?php echo base_url('uploads/admit_card/' . $admitcard->background_img); ?>" class="tcmybg" width="100%" height="100%" />-->

            <table cellpadding="0" cellspacing="0" width="100%" class="tablemain">

                <tr>
                    <td valign="top">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <img src="<?php echo base_url('uploads/cbseexam/template/header_image/abadb354d30c61d71659d65cc64cf95e.png'); ?>" width="100%" height="100%" />

                            </tr>
                        </table>
                    </td>
                </tr>
                <!--<tr>-->
                <!--    <td valign="top">-->
                <!--        <table cellpadding="0" cellspacing="0" width="100%" style="text-transform: uppercase;">-->
                <!--            <tr>-->
                <!--                <td valign="top" style="font-weight: bold;padding-bottom: 10px;"><?php echo $student_value->name . " (" . $this->setting_model->getCurrentSessionName() . ")"; ?></td>-->
                <!--                <td valign="top" style="font-weight: bold;padding-bottom: 10px;">Hall - Ticket</td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--    </td>-->
                <!--</tr>-->



                <tr>
                    <td valign="top" height="10"></td>
                </tr>
                <tr>
                    <td valign="top">
                        <table cellpadding="0" cellspacing="0" width="100%" style="text-transform: uppercase;">
                            <tr>
                                <td valign="top">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td valign="top" width="20%" style="font-weight: bold;padding-bottom: 10px;"></td>
                                            <td valign="top" width="35%" style="font-weight: bold;padding-bottom: 10px;"></td>
                                            <td valign="top" style="font-weight: bold;padding-bottom: 10px;text-align:right;">Hall - Ticket</td>

                                            <td valign="top" style="padding-bottom: 10px;"> </td>

                                        </tr>
                                        <tr>
                                            <td valign="top" width="20%" style="font-weight: bold;padding-bottom: 10px;"></td>
                                            <td valign="top" width="35%" style="font-weight: bold;padding-bottom: 10px;"></td>
                                            <td valign="top" colspan="2" style="font-weight: bold;padding-bottom: 10px;text-align:left;"><?php echo $student_value->name . " (" . $this->setting_model->getCurrentSessionName() . ")"; ?></td>
                                            <!-- <td valign="top" style="padding-bottom: 10px;"> </td> -->
                                        </tr>
                                        <tr>
                                            <td valign="top" width="20%" style="padding-bottom: 10px;"><?php echo $this->lang->line('admission_no') ?></td>
                                            <td valign="top" width="35%" style="font-weight: bold;padding-bottom: 10px;"><?php echo $student_value->admission_no; ?></td>

                                            <td valign="top" style="padding-bottom: 10px;"> <?php echo $this->lang->line('class'); ?></td>
                                            <td valign="top" style="text-transform: uppercase; font-weight: bold;padding-bottom: 10px;"><?php echo $student_value->class . "(" . $student_value->section . ")"; ?></td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="padding-bottom: 10px;"><?php echo $this->lang->line('candidates') . " " . $this->lang->line('name') ?></td>
                                            <td valign="top" style="text-transform: uppercase; font-weight: bold;padding-bottom: 10px;"><?php echo $this->customlib->getFullName($student_value->firstname, $student_value->middlename, $student_value->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></td>

                                            <td valign="top" width="25%" style="padding-bottom: 10px;"><?php echo $this->lang->line('roll_number') ?></td>
                                            <td valign="top" width="30%" style="font-weight: bold;padding-bottom: 10px;">
                                                <?php
                                                echo $student_value->roll_no; ?>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="padding-bottom: 10px;"><?php echo $this->lang->line('father_name'); ?></td>
                                            <td valign="top" style="text-transform: uppercase; font-weight: bold;padding-bottom: 10px;"><?php echo $student_value->father_name; ?></td>
                                        </tr>

                                    </table>
                                </td>

                                <td valign="top" width="25%" align="right">
                                    <?php
                                    if ($student_value->image != '') {
                                    ?>
                                        <img src="<?php echo  base_url($student_value->image); ?>" width="100" height="100" style="border: 2px solid #fff;
                                                 outline: 1px solid #000000;">
                                    <?php } else { ?>
                                        <img src="<?php
                                                    if ($student_value->gender == 'Female') {
                                                        echo base_url("uploads/student_images/default_female.jpg");
                                                    } elseif ($student_value->gender == 'Male') {
                                                        echo base_url("uploads/student_images/default_male.jpg");
                                                    }
                                                    ?>" width="100" height="100" style="border: 2px solid #fff;
                                                 outline: 1px solid #000000;">
                                    <?php } ?>

                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="10"></td>
                </tr>
                <tr>
                    <td valign="top">
                        <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
                            <tr>
                                <th valign="top" style="text-align: center; text-transform: uppercase;"><?php echo $this->lang->line('subject'); ?></th>
                                <?php
                                foreach ($exam_subjects as $subject_key => $subject_value) {
                                ?>
                                    <th valign="top" style="text-align: center; text-transform: uppercase;"><?php echo $subject_value->subject_name; ?></th>
                                <?php } ?>
                            </tr>

                            <tr>
                                <th valign="top" style="text-align: center; text-transform: uppercase;"><?php echo $this->lang->line('date'); ?></th>
                                <?php
                                foreach ($exam_subjects as $subject_key => $subject_value) {
                                ?>
                                    <th valign="top" style="text-align: center;"><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($subject_value->date)); ?></th>
                                <?php
                                }
                                ?>
                            </tr>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="50px"></td>
                </tr>
                <tr>
                    <td valign="top">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td valign="top" style="padding-bottom: 10px;"> Sign of the Class Teacher / Incharge</td>
                                        </tr>
                                    </table>
                                </td>

                                <td valign="top" width="25%" align="right">
                                    Sign of the Principal
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </div>
        <div class="pagebreak"> </div>
<?php
        $count++;

        if ($count % 3 == 0 && $count != count($student_details)) {
            echo '<div class="pagebreak"></div>';
        }
    }
}
?>