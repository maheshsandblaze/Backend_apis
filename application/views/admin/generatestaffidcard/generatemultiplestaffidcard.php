
<?php $i=0; ?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <?php
        foreach ($staffs as $staff_value) {
            $i++;
            ?>
            <td>
                <div style="height:300px">
                    <img src="<?php echo base_url(); ?>uploads/staff_id_card/background/sis_hyd_bg.jpeg" style="width:190px; position:absolute;z-index:-1; height:299px" >
            
                    <div class="front-logo-section" style="padding-top:10px; padding-bottom:10px">
                        <img class="id-photo3" src="<?php echo base_url('uploads/staff_id_card/logo/' . $id_card[0]->logo); ?>" style="width: 70px; display: block;  margin-left: auto; margin-right: auto;">
                    </div>
                    
                    <div style="margin:0 50px" class="text-center">
                        <?php if(!empty($staff_value->image)){ ?>
                                <img src="<?php echo base_url(); ?>uploads/staff_images/<?php echo $staff_value->image ?>" style="width:95px; height:95px;" />
                            <?php }else{ ?>
                                <img src="<?php echo base_url(); ?>uploads/student_images/no_image.png" style="width:95px; height:95px;" />
                            <?php } ?>
                            <h3 style="margin-top:2px; font-size: 12px; font-weight: 900; text-transform:uppercase; word-spacing: 2px;"><?php echo $staff_value->name; ?> <?php echo $staff_value->surname; ?></h3>
                            <h5 style="margin-top:-10px; font-size: 10px; font-weight: 700; text-transform:uppercase; word-spacing: 2px; color:#ff0000"><?php echo $staff_value->designation; ?></h5>
                    </div>
            
                    <div class="text-center" style="margin:0 50px">
<!--
                        <img id='barcode' class="id-details1" style="width:70px; height:45px" src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $staff_value->employee_id; ?>&amp;size=100x100" alt="" title="<?php $staff_value->employee_id;?>" />
   
                        <script type="text/javascript">
                            function generateBarCode() 
                            {
                                var nric = $('#text').val();
                                var url = 'https://api.qrserver.com/v1/create-qr-code/?data=' + nric + '&amp;size=50x50';
                                $('#barcode').attr('src', url);
                            }
                        </script>
-->
                        <!--<img id='barcode' class="id-details1" style="width:100px; height:25px" src="<?php echo base_url(); ?>uploads/staff_id_card/barcodes/barcode.png" alt="" title="<?php $staff_value->employee_id;?>" />-->
                        <!--<p style="font-size: 8px; font-weight: 100"><?php echo $staff_value->employee_id; ?></p>-->
                    </div>
                    
                    <div style="padding:20px 0px; padding-left:20px">
                        <div class="row">
                            <div class="col-md-5">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Employee Id</p>
                            </div>
                            <div class="col-md-1">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    
                            </div>
                            <div class="col-md-6">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $staff_value->employee_id; ?></p>
                            </div>
                        </div>    
                    
                        <div class="row">
                            <div class="col-md-5">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Blood Group</p>
                            </div>
                            <div class="col-md-1">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    
                            </div>
                            <div class="col-md-6">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">B +</p>
                            </div>
                        </div>    
                    
                        <div class="row">
                            <div class="col-md-5">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Emergency No</p>
                            </div>
                            <div class="col-md-1">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    
                            </div>
                            <div class="col-md-6">
                                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $staff_value->contact_no; ?></p>
                            </div>
                        </div>    
                    </div>
                    
                    <div class="branch-address">
                        <!--<h3 style="font-size: 8px; font-weight: 100; text-decoration: underline" class="text-center text-uppercase">Branch</h3>-->
                        <p style="font-size: 8px; font-weight: 100; margin:30px 0 0 0px; text-align:center;">
                        Wisibles School<br>
                        HIG 448, 5th Floor, beside Santhosh Dabha<br>
                        K P H B Phase 6, Hyderabad<br>
                        Telangana 500072
                        </p>
                    </div>
                    
                </div>
    
                <!--<div style="height:300px">-->
                <!--    <img src="<?php echo base_url(); ?>uploads/student_id_card/background/id_back_new.png" style="width:190px; position:absolute; z-index:-1;">-->
                    
                <!--    <div style="padding:20px 0px; background-color:#f6f5e6; padding-left:20px">-->
                <!--        <div class="row">-->
                <!--            <div class="col-md-5">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Employee Id</p>-->
                <!--            </div>-->
                <!--            <div class="col-md-1">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    -->
                <!--            </div>-->
                <!--            <div class="col-md-6">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $staff_value->employee_id; ?></p>-->
                <!--            </div>-->
                <!--        </div>    -->
                    
                <!--        <div class="row">-->
                <!--            <div class="col-md-5">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Blood Group</p>-->
                <!--            </div>-->
                <!--            <div class="col-md-1">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    -->
                <!--            </div>-->
                <!--            <div class="col-md-6">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">B +</p>-->
                <!--            </div>-->
                <!--        </div>    -->
                    
                <!--        <div class="row">-->
                <!--            <div class="col-md-5">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Emergency No</p>-->
                <!--            </div>-->
                <!--            <div class="col-md-1">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    -->
                <!--            </div>-->
                <!--            <div class="col-md-6">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px"><?php echo $staff_value->contact_no; ?></p>-->
                <!--            </div>-->
                <!--        </div>    -->
                    
                <!--        <div class="row">-->
                <!--            <div class="col-md-5">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">Valid Upto</p>-->
                <!--            </div>-->
                <!--            <div class="col-md-1">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">:</p>    -->
                <!--            </div>-->
                <!--            <div class="col-md-6">-->
                <!--                <p class="text-uppercase" style="font-size: 8px; font-weight: 100; line-height:8px">31-03-2025</p>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>  -->
                    
                <!--    <div class="back-logo-section">-->
                <!--        <img class="id-photo3" src="<?php echo base_url('uploads/staff_id_card/logo/' . $id_card[0]->logo); ?>" style="width: 70px; display: block;  margin-left: auto; margin-right: auto;">-->
                <!--    </div>-->
                    
                <!--    <div class="branch-address">-->
                        <!--<h3 style="font-size: 8px; font-weight: 100; text-decoration: underline" class="text-center text-uppercase">Branch</h3>-->
                <!--        <p style="font-size: 8px; font-weight: 100; margin:3px 0 0 0px; text-align:center;">-->
                <!--        Wisibles School<br>-->
                <!--        HIG 448, 5th Floor, beside Santhosh Dabha<br>-->
                <!--        K P H B Phase 6, Hyderabad<br>-->
                <!--        Telangana 500072-->
                <!--        </p>-->
                <!--    </div>-->
                    
                <!--</div>-->
                





        
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