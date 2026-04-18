<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('fees_type', 'can_add') || $this->rbac->hasPrivilege('fees_type', 'can_edit')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_bank_details'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/bankdetails/edit/" . $id) ?>"  id="employeeform" enctype="multipart/form-data" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                    ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>   
                                <?php echo $this->customlib->getCSRF(); ?>                       
                                <input name="id" type="hidden" class="form-control"  value="<?php echo set_value('id', $feetype['id']); ?>" />
                                
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('account_name'); ?></label> <small class="req">*</small>
                                    <input autofocus="" id="account_name" name="account_name" type="text" class="form-control"  value="<?php echo set_value('account_name', $feetype['account_name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('account_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('account_no'); ?></label> <small class="req">*</small>
                                    <input id="account_no" name="account_no" type="text" class="form-control"  value="<?php echo set_value('account_no', $feetype['account_number']); ?>" />
                                    <span class="text-danger"><?php echo form_error('account_no'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('bank_name'); ?></label> <small class="req">*</small>
                                    <input id="bank_name" name="bank_name" type="text" class="form-control"  value="<?php echo set_value('bank_name', $feetype['bank_name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('branch_name'); ?></label> <small class="req">*</small>
                                    <input id="branch_name" name="branch_name" type="text" class="form-control"  value="<?php echo set_value('branch_name', $feetype['branch_name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('branch_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('ifsc_code'); ?></label> <small class="req">*</small>
                                    <input id="ifsc_code" name="ifsc_code" type="text" class="form-control"  value="<?php echo set_value('ifsc_code', $feetype['ifsc_code']); ?>" />
                                    <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('bank_header_image'); ?></label>
                                    <input id="logo_img" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="logo_img">
                                    <input type="hidden" name="old_logo_img" value="<?php echo $feetype['header_image']; ?>">
                                    <span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                     <?php if(!empty($feetype['header_image'])){
                                        ?>
                                        <div class="logo_image">
                                        <div class="fadeheight-sms">
                                         <p class=""> <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete_background_image'); ?>"><i class="fa fa-trash-o" onclick="removelogo_image()"></i></a><?php echo $feetype['header_image'] ;?>
                                         </p>
                                        </div>
                                    </div>                                    
                                        <?php }?>
                                </div>
                                
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('fees_type', 'can_add') || $this->rbac->hasPrivilege('fees_type', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('fees_type_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('fees_type_list'); ?></div>
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('account_name'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('account_no'); ?></th>
                                        <th><?php echo $this->lang->line('bank_name'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($feetypeList as $feetype) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['account_name']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['account_number']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['bank_name']; ?>
                                            </td>
                                            <td class="mailbox-date pull-right">
                                                <?php
                                                if ($this->rbac->hasPrivilege('fees_type', 'can_edit')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/bankdetails/edit/<?php echo $feetype['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php
                                                if ($this->rbac->hasPrivilege('fees_type', 'can_delete')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/bankdetails/delete/<?php echo $feetype['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
    
    function removelogo_image(){
       var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");
        if (result) {
            $('.logo_image').html('<input type="hidden" name="removelogo_image" value="1">');
        } 
    }
</script>