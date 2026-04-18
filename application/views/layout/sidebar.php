<?php
$role    = $this->customlib->getStaffRole();
$role_id = json_decode($role)->id;
?>

<aside class="main-sidebar" id="alert2">
    <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>
        <form class="navbar-form navbar-left search-form2" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="input-group ">

                <input type="text" name="search_text" class="form-control search-form" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
    <?php } ?>
    <section class="sidebar" id="sibe-box">
        <?php $this->load->view('layout/top_sidemenu'); ?>

        <?php if ($role_id != 8) { ?>
            <ul class="sidebar-menu verttop">

                <!-- //==================sidebar dynamic======================= -->
                <?php
                $side_list = side_menu_list(1);

                if (!empty($side_list)) {
                    foreach ($side_list as $side_list_key => $side_list_value) {

                        $module_permission = access_permission_sidebar_remove_pipe($side_list_value->access_permissions);
                        $module_access     = false;
                        if (!empty($module_permission)) {
                            foreach ($module_permission as $m_permission_key => $m_permission_value) {
                                $cat_permission = access_permission_remove_comma($m_permission_value);

                                if ($this->rbac->hasPrivilege($cat_permission[0], $cat_permission[1])) {
                                    $module_access = true;
                                    break;
                                }
                            }
                        }
                        if ($module_access) {
                            if ($this->module_lib->hasModule($side_list_value->short_code) && $this->module_lib->hasActive($side_list_value->short_code)) {

                ?>

                                <li class="treeview <?php echo activate_main_menu($side_list_value->activate_menu); ?>">

                                    <a href="<?php echo site_url().$side_list_value->url_path;?>">
                                        <img width="25px" src="<?php echo site_url('backend/images/sidebar/').$side_list_value->icon_image; ?>" alt="icon2" class="img-fluid">
                                       <span style="display: block; font-family: 'Roboto-Bold'; font-size: 11px; white-space: normal; word-wrap: break-word; text-align: center; max-width: 90px; overflow: hidden; padding-top:5px; "><?php echo $this->lang->line($side_list_value->lang_key); ?></span> 
                                    </a>

                                    <!-- <?php
                                    if (!empty($side_list_value->submenus)) {
                                    ?>
                                        <ul class="treeview-menu">
                                            <?php
                                            foreach ($side_list_value->submenus as $submenu_key => $submenu_value) {

                                                $sidebar_permission = access_permission_sidebar_remove_pipe($submenu_value->access_permissions);
                                                $sidebar_access     = false;

                                                if (!empty($sidebar_permission)) {
                                                    foreach ($sidebar_permission as $sidebar_permission_key => $sidebar_permission_value) {
                                                        $sidebar_cat_permission = access_permission_remove_comma($sidebar_permission_value);

                                                        if ($submenu_value->addon_permission != "") {
                                                            if (
                                                                $this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])
                                                                && $this->auth->addonchk($submenu_value->addon_permission, false)
                                                            ) {
                                                                $sidebar_access = true;
                                                                break;
                                                            }
                                                        } else {
                                                            if ($this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])) {
                                                                $sidebar_access = true;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }

                                                if ($sidebar_access) {
                                                    if (!empty($submenu_value->permission_group_id)) {
                                                        if (!$this->module_lib->hasActive($submenu_value->short_code)) {
                                                            continue;
                                                        }
                                                    }

                                            ?>

                                                    <li  class="<?php echo activate_submenu($submenu_value->activate_controller, explode(',', $submenu_value->activate_methods)); ?>"><a href="<?php echo site_url($submenu_value->url); ?>"><i class="fa fa-angle-double-right"></i><?php echo $this->lang->line($submenu_value->lang_key); ?></a></li>


                                            <?php
                                                }
                                            }

                                            ?>
                                        </ul>

                                    <?php

                                    }
                                    ?> -->
                                </li>
                <?php
                            }
                        }
                    }
                }
                ?>
                <!-- //==================sidebar dynamic======================= -->

            </ul>
        <?php } ?>


        <!--<?php if ($role_id == 7) { ?>-->
        <!--<ul class="sidebar-menu verttop">-->

        <!--<li class="<?php echo set_Submenu('fees_collection'); ?>"><a href="<?php echo base_url(); ?>studentfee"><i class="fa fa-angle-double-right"></i> Fee Received</a></li>-->

        <!--    <li class="treeview <?php echo set_Topmenu('Leads'); ?>">-->
        <!--            <a href="#">-->
        <!--                <i class="fa fa-user-plus ftlayer"></i> <span>Lead Management</span> <i class="fa fa-angle-left pull-right"></i>-->
        <!--            </a>-->
        <!--            <ul class="treeview-menu">-->

        <!--                <li class="<?php echo set_Submenu('Leads/assign'); ?>"><a href="<?php echo base_url(); ?>admin/leadmanagement"><i class="fa fa-angle-double-right"></i> Assign Leads</a></li>-->

        <!--                <li class="<?php echo set_Submenu('Leads/status'); ?>"><a href="<?php echo base_url(); ?>financereports/leadreport"><i class="fa fa-angle-double-right"></i> Leads Status</a></li>-->

        <!--            </ul>-->
        <!--    </li>-->
        <!--</ul>    -->
        <!--<?php } ?>-->

        <?php if ($role_id == 8) { ?>
            <ul class="sidebar-menu verttop">

                <li class="<?php echo set_Submenu('admin/admin/dashboard'); ?>">
                    <a href="<?php echo base_url(); ?>admin/admin/dashboard">
                        <i class="fa fa-line-chart ftlayer"></i>
                        <?php echo $this->lang->line('dashboard'); ?>
                    </a>
                </li>

                <li class="treeview <?php echo set_Topmenu('Student Information'); ?>">
                    <a href="#">
                        <i class="fa fa-user-plus ftlayer"></i> <span><?php echo $this->lang->line('student_information'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        if ($this->rbac->hasPrivilege('student', 'can_view')) {
                        ?>

                            <li class="<?php echo set_Submenu('student/search'); ?>"><a href="<?php echo base_url(); ?>student/search"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('student_details'); ?></a></li>

                        <?php
                        } ?>

                    </ul>
                </li>

                <!--<li class="treeview <?php echo set_Topmenu('Fees Collection'); ?>">-->
                <!--    <a href="#">-->
                <!--        <i class="fa fa-money ftlayer"></i> <span> <?php echo $this->lang->line('fees_collection'); ?></span> <i class="fa fa-angle-left pull-right"></i>-->
                <!--    </a>-->
                <!--    <ul class="treeview-menu">-->
                <!--        <?php if ($this->rbac->hasPrivilege('collect_fees', 'can_view')) { ?>-->
                <!--            <li class="<?php echo set_Submenu('studentfee/index'); ?>"><a href="<?php echo base_url(); ?>studentfee"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('collect_fees'); ?></a></li>-->
                <!--        <?php } ?>-->

                <!--    </ul>-->
                <!--</li>-->


                <li class="<?php echo set_Submenu('admin/admin/dashboard'); ?>">
                    <a href="<?php echo base_url(); ?>financereports/assignleadsstudent">
                        <i class="fa fa-ioxhost ftlayer"></i>
                        Fees Collection
                    </a>
                </li>
            </ul>
        <?php } ?>


    </section>
</aside>

<!-- New Bottom Navigation for Mobile View -->
<?php /* ?>
<nav class="mobile-bottom-nav" id="bottom-menu">
    <ul class="bottom-menu-list">
        <?php
        if (!empty($side_list)) {
            foreach ($side_list as $side_list_value) {
                $module_permission = access_permission_sidebar_remove_pipe($side_list_value->access_permissions);
                $module_access     = false;
                
                if (!empty($module_permission)) {
                    foreach ($module_permission as $m_permission_value) {
                        $cat_permission = access_permission_remove_comma($m_permission_value);
                        
                        // Ensure permissions are checked properly for mobile view
                        if ($this->rbac->hasPrivilege($cat_permission[0], $cat_permission[1])) {
                            $module_access = true;
                            break;
                        }
                    }
                }

                // Only show the menu if the user has the required permission
                if ($module_access) {
                    if ($this->module_lib->hasModule($side_list_value->short_code) && $this->module_lib->hasActive($side_list_value->short_code)) {
                        echo '<li>';
                        echo '<a href="'.site_url().$side_list_value->url_path.'" data-toggle="tooltip" data-original-title="'.$this->lang->line($side_list_value->lang_key).'">';
                        echo '<img width="25px" src="'.site_url('backend/images/sidebar/').$side_list_value->icon_image.'" alt="icon2">';
					  //echo '<span>'.$this->lang->line($side_list_value->lang_key).'</span>';
						echo '<span style="font-size: 11px; color: #fff; display: inline-block; white-space: normal; line-height: 1.2; word-wrap: break-word; max-width: 100px;">'.$this->lang->line($side_list_value->lang_key).'</span>';
                        echo '</a>';
                        echo '</li>';
                    }
                }
            }
        }
        ?>
    </ul>
</nav>
<?php */?>

<!-- New Bottom Navigation for Mobile View -->
<?php if ($role_id != 9) { ?>
<nav class="mobile-bottom-nav" id="bottom-menu">
    <ul class="bottom-menu-list" style="display: flex; justify-content: space-between;">

        <li class="treeview <?php echo set_Topmenu('dashboard'); ?>">
            <a href="<?php echo base_url(); ?>admin/admin/dashboard" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('dashboard'); ?>">

                <span class="info-box-iconn bg-none">
                    <img src="<?php echo site_url('backend/images/sidebar/1.png'); ?>" alt="icon2" class="img-fluid">
                    <span><?php echo $this->lang->line("dashboard"); ?></span>
                </span>
            </a>
        </li>

        <li class="<?php echo set_Topmenu('my_profile'); ?>">
            <a href="<?php echo base_url(); ?>studentfee" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('my_profile'); ?>">

                <span class="info-box-iconn bg-none">
                    <img src="<?php echo site_url('backend/images/sidebar/3.png'); ?>" alt="icon2" class="img-fluid">
                    <span><?php echo $this->lang->line('fees'); ?></span>
                </span>
            </a>
        </li>

        <li class="treeview <?php echo set_Topmenu('dashboard'); ?>">
            <a href="<?php echo base_url(); ?>admin/stuattendence" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('fees'); ?>">

                <span class="info-box-iconn bg-none">
                    <img src="<?php echo site_url('backend/images/sidebar/4.png'); ?>" alt="icon2" class="img-fluid">
                    <span><?php echo $this->lang->line('attendance'); ?></span>
                </span>
            </a>
        </li>

        <li class="<?php echo set_Topmenu('my_profile'); ?>">
            <a href="<?php echo base_url(); ?>admin/admin/sidebar_menus" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('my_profile'); ?>">

                <span class="info-box-iconn bg-none">
                    <img src="<?php echo site_url('backend/images/sidebar/dots.png'); ?>" alt="icon2" class="img-fluid">
                    <span>More</span>
                </span>
            </a>
        </li>
        
        <li class="<?php echo set_Topmenu('my_profile'); ?>">
            <a href="<?php echo base_url(); ?>site/logout" onclick="return confirmLogout();" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('logout'); ?>">

                <span class="info-box-iconn bg-none">
                    <img src="<?php echo site_url('backend/images/sidebar/logout.png'); ?>" alt="icon2" class="img-fluid">
                    <span><?php echo $this->lang->line('logout'); ?></span>
                </span>
            </a>
        </li>

    </ul>
</nav>
<?php } ?>

<script>
    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
</script>