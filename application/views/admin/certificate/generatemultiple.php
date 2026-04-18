<?php
$school = $sch_setting[0];
$i = 0;
?>

<table cellpadding="0" cellspacing="0">
    <tr>
        <?php
        foreach ($students as $student) {
            $i++;

        ?>

            <td>
                <div style="height:300px">
                    <img src="<?php echo base_url(); ?>uploads/student_id_card/background/newidcardfront.png" style="width:190px; position:absolute;z-index:-1;">

                    <table>
                        <tr align="center">
                            <td>
                                <img class="id-photo3" src="<?php echo $this->media_storage->getImageURL('uploads/student_id_card/logo/' . $id_card[0]->logo); ?>" style="width: 100px; padding-top:30px">
                                <p style="margin-top:10px; text-transform:uppercase; font-size: 8px; width:190px;color:#000;">Academic Session : <?php echo $this->setting_model->getCurrentSessionName(); ?></p>
                            </td>
                        </tr>

                        <tr align="center">
                            <td>
                                <!--<img class="id-photo" src="<?php echo base_url($student->image); ?>" style="width:60px; border-radius: 30px; height:60px;">-->
                                <img class="id-photo" src="<?php
                                                            if (!empty($student->image)) {
                                                                echo base_url() . $student->image;
                                                            } else {
                                                                echo base_url() . "uploads/student_images/no_image.png";
                                                            }
                                                            ?>" style="width:60px; border-radius: 30px; height:60px; margin-top:5px">
                                <h3 style="margin-top:2px; font-size: 7px; font-weight: 100; text-transform:uppercase; word-spacing: 2px;color:#000;"><?php echo $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_settingdata->middlename, $sch_settingdata->lastname); ?></h3>
                            </td>
                        </tr>
                    </table>


                    <div style="margin-left: 30px; clear:both">
                        <div style="float:left; width:45%;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('class'); ?> / <?php echo $this->lang->line('section'); ?>&#160;&#160;&#160;:</p>
                        </div>
                        <div style="float:left; width:55%;;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->class . ' / ' . $student->section; ?></p>
                        </div>
                    </div>

                    <div style="margin-left: 30px; clear:both;">
                        <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px">Admission No&#160;&#160;&#160;&#160;&#160;:</p>
                        </div>
                        <div style="float:left; width:55%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->admission_no; ?></p>
                        </div>
                    </div>

                    <div style="margin-left: 30px; clear:both">
                        <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('mother_name') ?>&#160;&#160;&#160;:</p>
                        </div>
                        <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo mb_strimwidth($student->mother_name, 0, 17, '...'); ?></p>
                        </div>
                    </div>

                    <!--
            <table>
                <tr>
                    <td style="font-size: 8px; font-weight: 100; padding-left: 30px;">
                        <?php echo $this->lang->line('mother_name') ?>
                    </td>
                    <td align="center" style="font-size: 8px; font-weight: 100; padding:0px 10px">:</td>
                    <td style="font-size: 8px; font-weight: 100;">
                        <?php echo $student->mother_name; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 8px; font-weight: 100; padding-left: 30px;">
                        <?php echo $this->lang->line('father_name') ?>
                    </td>
                    <td align="center" style="font-size: 8px; font-weight: 100; padding:0px 10px">:</td>
                    <td style="font-size: 8px; font-weight: 100;">
                        <?php echo $student->father_name; ?>
                    </td>
                </tr>
            </table>
-->


                    <div style="margin-left: 30px; clear:both">
                        <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('father_name'); ?>&#160;&#160;&#160;&#160;:</p>
                        </div>
                        <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                            <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo mb_strimwidth($student->father_name, 0, 17, '...'); ?></p>
                        </div>
                    </div>



                    <!--<div style="margin-left: 30px; clear:both">-->
                    <!--    <div style="float:left; width:45%; margin-top: -5px;color:#000;">-->
                    <!--        <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('blood_group'); ?>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;:</p>-->
                    <!--    </div>-->
                    <!--    <div style="float:left; width:55%; margin-top: -5px;color:#000;">-->
                    <!--        <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->blood_group; ?></p>-->
                    <!--    </div>-->
                    <!--</div>-->



                    <!--<div style="margin-left: 30px; clear:both">-->
                    <!--    <div style="float:left; width:45%; margin-top: -5px;color:#000;">-->
                    <!--        <p style="font-size: 8px; font-weight: 100; line-height:8px">Bus Route No.&#160;&#160;&#160;&#160;:</p>-->
                    <!--    </div>-->
                    <!--    <div style="float:left; width:55%; margin-top: -5px;color:#000;">-->
                    <!--        <p style="font-size: 8px; font-weight: 100; line-height:8px">-->

                    <!--        </p>-->
                    <!--    </div>-->
                    <!--</div>-->

                </div>


                <div style="height:300px">
                    <img src="<?php echo base_url(); ?>uploads/student_id_card/background/id_back_new.png" style="width:190px; position:absolute; z-index:-1;">


                    <div style="padding:10px 0px; background-color:#f6f5e6">
                        <div style="margin: 0 20px;">
                            <div style="float:left; width:45%;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('gender'); ?>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;:</p>
                            </div>
                            <div style="float:left; width:55%;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->gender; ?></p>
                            </div>
                        </div>

                        <div style="margin: 0 20px; clear:both;">
                            <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $this->lang->line('d_o_b'); ?>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;:</p>
                            </div>
                            <div style="float:left; width:55%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->dob)); ?></p>
                            </div>
                        </div>

                        <div style="margin: 0 20px; clear:both">
                            <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px">Res.Add.&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;:</p>
                            </div>
                            <div style="float:left; width:55%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->current_address; ?></p>
                            </div>
                        </div>

                        <!--
            <table>
                <tr>
                    <td style="font-size: 8px; font-weight: 100; padding-left: 30px;">
                        <?php echo $this->lang->line('mother_name') ?>
                    </td>
                    <td align="center" style="font-size: 8px; font-weight: 100; padding:0px 10px">:</td>
                    <td style="font-size: 8px; font-weight: 100;">
                        <?php echo $student->mother_name; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 8px; font-weight: 100; padding-left: 30px;">
                        <?php echo $this->lang->line('father_name') ?>
                    </td>
                    <td align="center" style="font-size: 8px; font-weight: 100; padding:0px 10px">:</td>
                    <td style="font-size: 8px; font-weight: 100;">
                        <?php echo $student->father_name; ?>
                    </td>
                </tr>
            </table>
-->


                        <div style="margin: 0 20px; clear:both">
                            <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px">Mob.No.Mother&#160;:</p>
                            </div>
                            <div style="float:left; width:55%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->mother_phone; ?></p>
                            </div>
                        </div>



                        <div style="margin: 0 20px; clear:both">
                            <div style="float:left; width:45%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px">Mob.No.Father&#160;&#160;:</p>
                            </div>
                            <div style="float:left; width:55%; margin-top: -5px;color:#000;">
                                <p style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $student->father_phone; ?></p>
                            </div>
                        </div>

                    </div>



                    <table class="id-details" style="padding-top:20px; margin: 0 35px">

                        <tr>
                            <td>
                                <img class="id-details2" style="width:50px; height:50px" src="<?php
                                                                                                if (!empty($student->father_pic)) {
                                                                                                    echo base_url() . $student->father_pic;
                                                                                                } else {
                                                                                                    echo base_url() . "uploads/student_images/no_image.png";
                                                                                                }
                                                                                                ?>">

                            </td>
                            <td style="padding-left:15px">
                                <img class="id-details2" style="width:50px; height:50px" src="<?php
                                                                                                if (!empty($student->mother_pic)) {
                                                                                                    echo base_url() . $student->mother_pic;
                                                                                                } else {
                                                                                                    echo base_url() . "uploads/student_images/no_image.png";
                                                                                                }
                                                                                                ?>">

                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h3 style="margin-top: 3px; width:60px; font-size: 8px; font-weight: 100; text-align:center;color:#000;">
                                    <?php
                                    if (!empty($student->father_name)) {
                                        echo $student->father_name;
                                    } else {
                                        echo "Father's Name";
                                    }

                                    ?>
                                </h3>
                            </td>
                            <td>
                                <h3 style="margin-top: 3px; width:60px; font-size: 8px; font-weight: 100; text-align:center; margin-left:10px;color:#000;">
                                    <?php
                                    if (!empty($student->mother_name)) {
                                        echo $student->mother_name;
                                    } else {
                                        echo "Mother's Name";
                                    }

                                    ?>
                                </h3>
                            </td>
                        </tr>

                    </table>

                    <div class="id-photo1">
                        <img class="id-photo3" src="<?php echo base_url(); ?>uploads/student_id_card/logo/logo.png" style="width: 70px; display: block;  margin-left: auto; margin-right: auto;">

                        <p style="font-size: 8px; font-weight: 100; margin:3px 0 0 0px; text-align:center;">
                            Wisibles School<br>
                            HIG 448, 5th Floor, beside Santhosh Dabha<br>
                            K P H B Phase 6, Hyderabad<br>
                            Telangana 500072
                        </p>

                    </div>


                </div>






            </td>


            <?php
            if ($i == 1) {
                // three items in a row. Edit this to get more or less items on a row
            ?>
    </tr>
    <tr>
<?php
                $i = 0;
            }
        }
?>
    </tr>

</table>