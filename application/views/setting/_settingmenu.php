<div class="col-md-2">
    <div class="box border0">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $this->lang->line('system_setting'); ?></h3>
        </div>
        <ul class="tablists">
            <?php
                            if ($this->rbac->hasPrivilege('general_setting', 'can_view')) {
                                ?>
            <li class="<?php echo set_SubSubmenu('schsettings/index'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/index'); ?>" href="<?php echo site_url('schsettings') ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/1.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('general_setting'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('session_setting', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('sessions/index'); ?>">
                <a class="<?php echo set_Submenu('sessions/index'); ?>" href="<?php echo base_url(); ?>sessions"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/2.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('session_setting'); ?></a>
            </li>

            <?php
                            }
                            if ($this->rbac->hasPrivilege('notification_setting', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('notification/setting'); ?>">
                <a class="<?php echo set_Submenu('notification/setting'); ?>" href="<?php echo base_url(); ?>admin/notification/setting"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/3.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('notification_setting'); ?></a>
            </li> 
            
            <?php
                            }



                            if ($this->rbac->hasPrivilege('sms_setting', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('smsconfig/index'); ?>">
                <a class="<?php echo set_Submenu('smsconfig/index'); ?>" href="<?php echo base_url(); ?>smsconfig"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/4.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('sms_setting'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('email_setting', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('emailconfig/index'); ?>">
                <a class="<?php echo set_Submenu('emailconfig/index'); ?>" href="<?php echo base_url(); ?>emailconfig"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/5.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('email_setting'); ?></a>
            </li>
            <?php
                            }

                            if ($this->rbac->hasPrivilege('payment_methods', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('admin/paymentsettings'); ?>">
                <a class="<?php echo set_Submenu('admin/paymentsettings'); ?>" href="<?php echo base_url(); ?>admin/paymentsettings"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/6.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('payment_methods'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('print_header_footer', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('admin/print_headerfooter'); ?>">
                <a class="<?php echo set_Submenu('admin/print_headerfooter'); ?>" href="<?php echo base_url(); ?>admin/print_headerfooter"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/7.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('print_headerfooter'); ?></a>
            </li>
            <?php
                            }
                            if ($this->module_lib->hasActive('front_cms')) {
                                if ($this->rbac->hasPrivilege('front_cms_setting', 'can_view')) {
                                    ?>
            <li class="<?php echo set_Submenu('admin/frontcms/index'); ?>">
                <a class="<?php echo set_Submenu('admin/frontcms/index'); ?>" href="<?php echo base_url(); ?>admin/frontcms"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/8.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('front_cms_setting'); ?></a>
            </li>
            <?php
                                }
                            }
                            ?>
            <?php if ($this->rbac->hasPrivilege('superadmin')) { ?>                
            <li class="<?php echo set_Submenu('admin/roles'); ?>">
                <a class="<?php echo set_Submenu('admin/roles'); ?>" href="<?php echo base_url(); ?>admin/roles"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/10.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('roles_permissions'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('backup', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('admin/backup'); ?>">
                <a class="<?php echo set_Submenu('admin/backup'); ?>" href="<?php echo base_url(); ?>admin/admin/backup"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/11.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('backup_restore'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('languages', 'can_add')) {
                                ?>
            <li class="<?php echo set_Submenu('language/index'); ?>">
                <a class="<?php echo set_Submenu('language/index'); ?>" href="<?php echo base_url(); ?>admin/language"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/12.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('languages'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('user_status')) {
                                ?>
            <li class="<?php echo set_Submenu('users/index'); ?>">
                <a class="<?php echo set_Submenu('users/index'); ?>" href="<?php echo base_url(); ?>admin/users"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/14.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('users'); ?></a>
            </li>
            <?php
                            }
                            if ($this->rbac->hasPrivilege('superadmin')) {
                                ?>
            <li class="<?php echo set_Submenu('System Settings/module'); ?>">
                <a class="<?php echo set_Submenu('System Settings/module'); ?>" href="<?php echo base_url(); ?>admin/module"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/15.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('modules'); ?></a>
            </li>
            <?php
                            } 
                            if ($this->rbac->hasPrivilege('custom_fields', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('System Settings/customfield'); ?>">
                <a class="<?php echo set_Submenu('System Settings/customfield'); ?>" href="<?php echo base_url(); ?>admin/customfield"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/16.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('custom_fields'); ?></a>
            </li>
            <?php } 
                             if ($this->rbac->hasPrivilege('superadmin')) {
                            ?>
            <li class="<?php echo set_Submenu('System Settings/captcha'); ?>">
                <a class="<?php echo set_Submenu('System Settings/captcha'); ?>" href="<?php echo base_url(); ?>admin/captcha"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/17.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('captcha_setting'); ?></a>
            </li>
            <?php }
                            if ($this->rbac->hasPrivilege('system_fields', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('System Settings/systemfield'); ?>">
                <a class="<?php echo set_Submenu('System Settings/systemfield'); ?>" href="<?php echo base_url(); ?>admin/systemfield"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/18.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('system') . " " . $this->lang->line('fields'); ?></a>
            </li>
            <?php
                            } if ($this->rbac->hasPrivilege('student_profile_update', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('System Settings/profilesetting'); ?>">
                <a class="<?php echo set_Submenu('System Settings/profilesetting'); ?>" href="<?php echo base_url(); ?>student/profilesetting"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/19.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('student') . " " . $this->lang->line('profile') . " " . $this->lang->line('update'); ?></a>
            </li>
            <?php
                            } if ($this->rbac->hasPrivilege('online_admission', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('onlineadmission/admissionsetting'); ?>">
                <a class="<?php echo set_Submenu('onlineadmission/admissionsetting'); ?>" href="<?php echo base_url(); ?>admin/onlineadmission/admissionsetting"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/20.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('online_admission'); ?></a>
            </li>
            <?php
                                }
                                     if ($this->rbac->hasPrivilege('superadmin')) {
                                    ?>
            <li class="<?php echo set_Submenu('System Settings/filetype'); ?>">
                <a class="<?php echo set_Submenu('System Settings/filetype'); ?>" href="<?php echo site_url('admin/admin/filetype'); ?>"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/22.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('file_types'); ?></a>
            </li>         
                        <?php
                            } if ($this->rbac->hasPrivilege('sidebar_menu', 'can_view')) {
                                ?>
            <li class="<?php echo set_Submenu('sidemenu/index'); ?>">
                <a class="<?php echo set_Submenu('sidemenu/index'); ?>" href="<?php echo base_url(); ?>admin/sidemenu"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/21.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('sidebar_menu'); ?></a>
            </li>
            <?php
                    }
                                if ($this->rbac->hasPrivilege('superadmin')) {
                                    ?>
            <!--<li class="<?php echo set_Submenu('System Settings/updater'); ?>">-->
            <!--    <a class="<?php echo set_Submenu('System Settings/updater'); ?>" href="<?php echo base_url(); ?>admin/updater"><img src="<?php echo base_url('backend/images/sidebar/submenu/system_settings/23.png') ?>" alt="icon7" class="img-fluid" style="width:20px"> <?php echo $this->lang->line('system_update'); ?></a>-->
            <!--</li>-->
             <?php
                    }
                      
                    ?>
        </ul>
    </div>
</div><!--./col-md-3--> 

<div class="col-md-2">
    <div class="box border0">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $this->lang->line('system_setting'); ?></h3>
        </div>
        <ul class="tablists">
            <li class="<?php echo set_SubSubmenu('schsettings/index'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/index'); ?>" href="<?php echo site_url('schsettings') ?>"><?php echo $this->lang->line('general_setting'); ?></a>
            </li>            
            <li class="<?php echo set_SubSubmenu('schsettings/logo'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/logo'); ?>" href="<?php echo site_url('schsettings/logo') ?>"><?php echo $this->lang->line('logo'); ?></a>
            </li>


            <li class="<?php echo set_SubSubmenu('schsettings/login_page_background'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/login_page_background'); ?>" href="<?php echo site_url('schsettings/login_page_background') ?>"><?php echo $this->lang->line('login_page_background'); ?></a>
            </li> 
            
            
            <li class="<?php echo set_SubSubmenu('schsettings/backendtheme'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/backendtheme'); ?>" href="<?php echo site_url('schsettings/backendtheme') ?>"><?php echo $this->lang->line('backend_theme'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/mobileapp'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/mobileapp'); ?>" href="<?php echo site_url('schsettings/mobileapp') ?>"><?php echo $this->lang->line('mobile_app'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/studentguardianpanel'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/studentguardianpanel'); ?>" href="<?php echo site_url('schsettings/studentguardianpanel') ?>"><?php echo $this->lang->line('student_guardian_panel'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/fees'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/fees'); ?>" href="<?php echo site_url('schsettings/fees') ?>"><?php echo $this->lang->line('fees'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/idautogeneration'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/idautogeneration'); ?>" href="<?php echo site_url('schsettings/idautogeneration') ?>"><?php echo $this->lang->line('id_auto_generation'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/attendancetype'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/attendancetype'); ?>" href="<?php echo site_url('schsettings/attendancetype') ?>"><?php echo $this->lang->line('attendance_type'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/maintenance'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/maintenance'); ?>" href="<?php echo site_url('schsettings/maintenance') ?>"><?php echo $this->lang->line('maintenance'); ?></a>
            </li>
            <li class="<?php echo set_SubSubmenu('schsettings/miscellaneous'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/miscellaneous'); ?>" href="<?php echo site_url('schsettings/miscellaneous') ?>"><?php echo $this->lang->line('miscellaneous'); ?></a>
            </li>
        </ul>
    </div>
</div><!--./col-md-3-->