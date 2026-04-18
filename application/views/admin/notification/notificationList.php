<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-bullhorn"></i> <?php //echo $this->lang->line('communicate'); 
                                            ?> <small><?php //echo $this->lang->line('student_fee1'); 
                                                                                                    ?></small>
        </h1>
    </section>
    <section class="content">
        <div class="row mt-20">
            <?php
            $role    = $this->customlib->getStaffRole();
            $role_id = json_decode($role)->id;
            ?>

            <div class="col-md-2 hide-mobile">
                <div class="box border0">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('communicate'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('notice_board', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('notification/index'); ?>">
                                <a class="<?php echo set_Submenu('notification/index'); ?>" href="<?php echo base_url(); ?>admin/notification">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/1.png') ?>" alt="icon1" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('notice_board'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('email', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('Communicate/mailsms/compose'); ?>">
                                <a class="<?php echo set_Submenu('Communicate/mailsms/compose'); ?>" href="<?php echo base_url(); ?>admin/mailsms/compose">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/2.png') ?>" alt="icon2" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('send') . " " . $this->lang->line('email'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('sms', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('mailsms/compose_sms'); ?>">
                                <a class="<?php echo set_Submenu('mailsms/compose_sms'); ?>" href="<?php echo base_url(); ?>admin/mailsms/compose_sms">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/3.png') ?>" alt="icon3" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('send') . " " . $this->lang->line('sms'); ?>
                                </a>
                            </li>
                        <?php } ?>
                        
                        <?php if ($this->rbac->hasPrivilege('whatsapp', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('Communicate/sendwhatsapp/compose_whatsapp'); ?>">
                                <a class="<?php echo set_Submenu('Communicate/sendwhatsapp/compose_whatsapp'); ?>" href="<?php echo base_url(); ?>admin/sendwhatsapp/compose_sms">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/3.png') ?>" alt="icon2" class="img-fluid" style="width:20px"> 
                                    <?php echo $this->lang->line('send_whatsapp'); ?>
                                </a>
                            </li>
                        <?php } ?>
                        
                        <?php if ($this->rbac->hasPrivilege('notice_board', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('notification_class/index'); ?>">
                                <a class="<?php echo set_Submenu('notification_class/index'); ?>" href="<?php echo base_url(); ?>admin/notification_class">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/1.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> 
                                    <?php echo $this->lang->line('circular'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('email_sms_log', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('mailsms/index'); ?>">
                                <a class="<?php echo set_Submenu('mailsms/index'); ?>" href="<?php echo base_url(); ?>admin/mailsms/index">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/4.png') ?>" alt="icon4" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('email_sms_log'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('schedule_email_sms_log', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('mailsms/schedule'); ?>">
                                <a class="<?php echo set_Submenu('mailsms/schedule'); ?>" href="<?php echo base_url(); ?>admin/mailsms/schedule">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/5.png') ?>" alt="icon5" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('schedule_email_sms_log'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('login_credentials_send', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('student/bulkmail'); ?>">
                                <a class="<?php echo set_Submenu('student/bulkmail'); ?>" href="<?php echo base_url(); ?>student/bulkmail">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/6.png') ?>" alt="icon6" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('login_credentials_send'); ?>
                                </a>
                            </li>
                        <?php } ?>
                        
                        <?php if ($this->rbac->hasPrivilege('send_reminders', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('financereports/send_reminders'); ?>">
                                <a class="<?php echo set_Submenu('financereports/send_reminders'); ?>" href="<?php echo base_url(); ?>financereports/send_reminders">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/fees/fr.png') ?>" alt="icon6" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('send_reminders'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('email_template', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('mailsms/email_template'); ?>">
                                <a class="<?php echo set_Submenu('mailsms/email_template'); ?>" href="<?php echo base_url(); ?>admin/mailsms/email_template">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/7.png') ?>" alt="icon7" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('email_template'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('sms_template', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('mailsms/sms_template'); ?>">
                                <a class="<?php echo set_Submenu('mailsms/sms_template'); ?>" href="<?php echo base_url(); ?>admin/mailsms/sms_template">
                                    <img src="<?php echo base_url('backend/images/sidebar/submenu/communication/8.png') ?>" alt="icon7" class="img-fluid" style="width:20px">
                                    <?php echo $this->lang->line('sms_template'); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div><!-- ./col-md-3 -->


            <div class="col-md-10">
                <div class="box box-solid1 box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-commenting-o"></i> <?php echo $this->lang->line('notice_board'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('notice_board', 'can_add')) { ?>
                                <a href="<?php echo base_url() ?>admin/notification/add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('post_new_message'); ?></a>
                            <?php } ?>
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs mright5 hide-desktop"> <i class="fa fa-arrow-left"></i> Back</button>
                        </div>

                    </div>
                    <div class="box-body pt0">
                        <?php
                        $this->session->unset_userdata('msg'); ?>
                        <?php

                        if (empty($notificationlist)) {
                        ?>
                            <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                            <?php
                        } else {
                            foreach ($notificationlist as $key => $notification) {

                            ?>
                                <div class="email-info d-flex">
                                    <a href="#" class="navbar-toggle2 force-visible mail-sidebar w-100" data-id="<?php echo $notification['id']; ?>">
                                        <h4 class="h4-title"><i class="fa fa-envelope-o"></i><?php echo $notification['title']; ?></h4>
                                        <div class="email-discription"><?php //echo $notification['message']; 
                                                                        ?></div>
                                    </a>
                                    <div class="d-flex ptt10 hover-show">

                                        <?php if ($notification["created_id"] == $user_id) { ?>
                                            <a href="<?php echo base_url() ?>admin/notification/edit/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>

                                        <?php } elseif ($this->rbac->hasPrivilege('notice_board', 'can_edit')) { ?>

                                            <a href="<?php echo base_url() ?>admin/notification/edit/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>

                                        <?php } ?>


                                        <?php if ($notification["created_id"] == $user_id) { ?>

                                            <a href="<?php echo base_url() ?>admin/notification/delete/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                <i class="fa fa-remove"></i>
                                            </a>

                                        <?php } elseif ($this->rbac->hasPrivilege('notice_board', 'can_delete')) { ?>

                                            <a href="<?php echo base_url() ?>admin/notification/delete/<?php echo $notification['id'] ?>" class="" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                <i class="fa fa-remove"></i>
                                            </a>

                                        <?php } ?>

                                    </div>
                                </div>
                        <?php }
                        } ?>

                    </div>
                    <aside class="sidebar-container" role="dialog">
                        <article class="email-collection">
                            <a href="#" class="mail-sidebar mail-close-btn"><i class="fa fa-times fs-2"></i></a>
                            <div id="notificationdata"></div>
                        </article>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $('.mail-sidebar').on('click', function(e) {
        $('.sidebar-container, .email-collection').toggleClass("open");
        $('.mail-close-btn').toggleClass("open");
        e.preventDefault();

        var message_id = $(this).attr('data-id');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/notification/notification',
            method: 'post',
            data: {
                message_id: message_id
            },
            dataType: 'json',
            success: function(response) {
                $('#notificationdata').html(response.page);
            }
        })
    })
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function() {
                return $(this).closest('li').find('.fee_detail_popover').html();
            }
        });
    });
</script>

<script>
    $(function() {
        $("#compose-textarea").wysihtml5();
    });
</script>