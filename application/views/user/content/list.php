<style>
.description-block > .description-header {
    margin: 0;
    padding: 0;
    font-family: "Roboto";
    font-size: 12px;
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-download"></i> <?php //echo $this->lang->line('download_center'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line("content_list"); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                        <div class="btn-group pull-right hide-desktop">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>
                    <div class="box-body hide-mobile">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line("content_list"); ?></div>
                                          <div class="table-responsive mailbox-messages overflow-visible">
                                 <table class="table table-striped table-bordered table-hover content-list" data-export-title="<?php echo $this->lang->line('content_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>
                                        <th><?php echo $this->lang->line('share_date'); ?></th>
                                        <th><?php echo $this->lang->line('valid_upto'); ?></th>
                                        <th><?php echo $this->lang->line('shared_by'); ?></th>
                                        <th class="pull-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                        </div>
                    </div>
                    
                    <div class="box-body hide-desktop">
                            
                            
                            <div class="col-md-12">
                                <?php
if (!empty($results)) {
foreach ($results as $value) {
    ?>
    
                                <div class="box box-primary borderwhite">
                                    <div class="box-header ptbnull bgtgray">
                                        <h3 class="box-title titlefix"><span><?php echo $value['title']; ?></span></h3>
                                        <div class="box-tools pull-right">
                                            <a href="<?php echo site_url('user/content/view/' . $value['id']); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('view'); ?>"><i class='fa fa-eye'></i> </a>
                                        </div>
                                    </div>
                                    
                                    <div class="box-body">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('share_date'); ?> : <span class="description-text"><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($value['share_date'])); ?></span></h5>
                                            </div>
                                            
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('valid_upto'); ?> : <span class="description-text"><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($value['valid_upto'])); ?></span></h5>
                                            </div>
                                        
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('shared_by'); ?> : <span class="description-text"><?php echo $value['name'] . " " . $value['surname'] . " (" . $value['employee_id'] . ")"; ?></span></h5>
                                            </div>
                                    </div>
                                </div>
                            <?php
    }
} else {
?>
                            <div class="alert alert-info">
                                <?php echo $this->lang->line('no_record_found'); ?>
                            </div>
                        <?php } ?>            
                            </div>
                            
                        </div>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            </div>
        </div>
    </section>
</div>

<script>
    ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('content-list','user/content/getsharelist',[],[],100,
            [                
                { "bSortable": true, "aTargets": [ 1,2,3 ] ,'sClass': 'dt-body-left',"sWidth": "20%"}
            ]);
    });
} ( jQuery ) )

</script>