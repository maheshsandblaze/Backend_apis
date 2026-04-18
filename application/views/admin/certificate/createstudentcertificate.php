<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i>                          
                           Enter Admission Number
                        </h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form role="form" action="<?php echo site_url('admin/studentcertificate') ?>" method="post" class="form-inline">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label><?php echo $this->lang->line('admission_no'); ?>
                                            </label><small class="req"> *</small>
                                            <input autofocus="" id="admissionid" name="admissionid" placeholder="" type="text" class="form-control"  value="<?php echo $admissionid; ?>"/>
                                            <span class="text-danger"><?php echo form_error('admissionid'); ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group align-text-top">
                                        <div class="col-sm-12">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle mmius15"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>    
                    <?php
                    if (isset($student)) {
                        ?>
                        <div class="ptt10">
                        <?php //echo json_decode($student); ?>
                            <div class="box-header ptbnull"></div> 
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('student_details'); ?></h3>
                                <div class="box-tools pull-right"></div>
                            </div> 
                            <div class="box-body table-responsive">

                                <div class="download_label"><?php echo $this->lang->line('student_details'); ?></div>
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th>Admission Number</th>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Bonafide</th>
                                            <th>Transfer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($student)) {
                                                ?>
                                                <tr>
                                                    <td colspan="3" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                                </tr>
                                                <?php
                                            } else {
                            ?>
                                    <tr>
                                                <td>
                                                    <?php echo $student->admission_no ?>
                                                </td>
                                                <td>
                                                    <?php echo $student->firstname . $student->lastname; ?>
                                                </td>
                                                <td>
                                                    <?php echo $student->class ?>
                                                </td>
                                                <td>
                                                    <?php echo $student->section ?>
                                                </td>
                                                <td>
                                                <?php if ($student->id){ ?>
                                                    <a href="<?php echo base_url() . "admin/studentcertificate/create_bonafide?admission_id=".$student->admission_no ?>"  class="btn btn-info btn-xs" data-toggle="tooltip" title="" data-original-title="">
                                                        <?php echo $bonafidetext; ?>
                                                    </a>
                                                    <?php if ($bonafide->id){ ?>
                                                        <a href="<?php echo base_url() . "admin/studentcertificate/preview_bonafied?admission_id=".$student->admission_no ?>"  class="btn btn-info btn-xs" data-toggle="tooltip" title="" data-original-title="">
                                                           Generate
                                                        </a>
                                                    <?php }?>
                                                <?php }?>
                                                </td>
                                                <td>
                                                <?php if ($student->id){ ?>
                                                    <a href="<?php echo base_url(). "admin/studentcertificate/create_tc?admission_id=".$student->admission_no ?>"  class="btn btn-info btn-xs" data-toggle="tooltip" title="" data-original-title="">
                                                        <?php echo $tctext; ?>
                                                    </a>
                                                    <?php if ($tc && $tc->id){ ?>
                                                        <a href="<?php echo base_url(). "admin/studentcertificate/preview_tc?admission_id=".$student->admission_no ?>"  class="btn btn-info btn-xs" data-toggle="tooltip" title="" data-original-title="">
                                                           Generate
                                                        </a>
                                                    <?php }?>
                                                <?php }?>
                                                </td>
                                    </tr>           
                                    <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                        <?php
                    }
                    ?>

                </div>   
            </div>
        </div> 
    </section>
</div>