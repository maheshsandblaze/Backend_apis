<style type="text/css">
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bus"></i> <?php //echo $this->lang->line('transport'); 
                                        ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('transport'); ?></h3>
                    </div>
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('routes', 'can_view')) { ?>
                            <li class="<?php echo set_Submenu('route/index'); ?>">
                                <a class="<?php echo set_Submenu('route/index'); ?>" href="<?php echo base_url(); ?>admin/route"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/3.png" alt="icon1" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('routes'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('vehicle', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('vehicle/index'); ?>">
                                <a class="<?php echo set_Submenu('vehicle/index'); ?>" href="<?php echo base_url(); ?>admin/vehicle"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/4.png" alt="icon2" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('vehicles'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('assign_vehicle', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('vehroute/index'); ?>">
                                <a class="<?php echo set_Submenu('vehroute/index'); ?>" href="<?php echo base_url(); ?>admin/vehroute"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/5.png" alt="icon3" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('assign_vehicle'); ?></a>
                            </li>

                        <?php
                        }
                        if ($this->rbac->hasPrivilege('pickup_point', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('pickuppoint/index'); ?>">
                                <a class="<?php echo set_Submenu('pickuppoint/index'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/2.png" alt="icon4" class="img-fluid" style="width:20px"><?php echo $this->lang->line('pickup_point'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('route_pickup_point', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('pickuppoint/assign'); ?>">
                                <a class="<?php echo set_Submenu('pickuppoint/assign'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint/assign"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/6.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('route_pickup_point'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('transport_fees_master', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('transport/feemaster'); ?>">
                                <a class="<?php echo set_Submenu('transport/feemaster'); ?>" href="<?php echo base_url(); ?>admin/transport/feemaster"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/1.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('fees_master'); ?></a>
                            </li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('student_transport_fees', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('pickuppoint/student_fees'); ?>">
                                <a class="<?php echo set_Submenu('pickuppoint/student_fees'); ?>" href="<?php echo base_url(); ?>admin/pickuppoint/student_fees"><img src="<?php echo base_url(); ?>/backend/images/sidebar/submenu/transport/7.png" alt="icon5" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student_transport_fees'); ?></a>
                            </li>
                        <?php
                        }
                        ?>

                    </ul>
                </div>
            </div><!--./col-md-3-->
            <?php if ($this->rbac->hasPrivilege('routes', 'can_add')) {
            ?>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('create_route'); ?></h3>
                        </div>
                        <form id="form1" action="<?php echo site_url('admin/route/create') ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg'); ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('route_title'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="route_title" name="route_title" placeholder="" type="text" class="form-control" value="<?php echo set_value('route_title'); ?>" />
                                    <span class="text-danger"><?php echo form_error('route_title'); ?></span>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-<?php
                                if ($this->rbac->hasPrivilege('routes', 'can_add')) {
                                    echo "6";
                                } else {
                                    echo "10";
                                }
                                ?>">
                <div class="box box-primary" id="route">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('route_list'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                            <div class="pull-right">
                            </div>
                        </div>
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('route_list'); ?></div>
                            <div class="table-responsive overflow-visible">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('route_title'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($listroute)) {
                                        ?>

                                            <?php
                                        } else {
                                            $count = 1;
                                            foreach ($listroute as $data) {
                                            ?>
                                                <tr>
                                                    <td class="mailbox-name"> <?php echo $data['route_title'] ?></td>
                                                    <td class="mailbox-date pull-right no-print">
                                                        <?php if ($this->rbac->hasPrivilege('routes', 'can_edit')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/route/edit/<?php echo $data['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php }
                                                        if ($this->rbac->hasPrivilege('routes', 'can_delete')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/route/delete/<?php echo $data['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                            $count++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#btnreset").click(function() {
            $("#form1")[0].reset();
        });
    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';

    function printDiv(elem) {
        Popup(jQuery(elem).html());
    }

    function Popup(data) {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({
            "position": "absolute",
            "top": "-1000000px"
        });
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
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);

        return true;
    }
</script>