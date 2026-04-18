<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('school_class_vacancies'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if (($this->rbac->hasPrivilege('school_vacancies', 'can_add')) || ($this->rbac->hasPrivilege('school_vacancies', 'can_edit'))) {
            ?>
            
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $title; ?></h3>
                        </div>

                        <?php
                        $url = "";
                        if (!empty($house_name)) {
                            $url = base_url() . "admin/school_vacancies/edit/" . $id;
                        } else {
                            $url = base_url() . "admin/school_vacancies/create";
                        }
                        ?>
                        <form id="form1" action="<?php echo $url ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php
                                if ($this->session->flashdata('msg')) {
                                    echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg');
                                } ?>
                                <?php echo $this->customlib->getCSRF(); ?>


                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label> <small class="req"> *</small>
                                    <select autofocus="" id="class_id" name="class_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        $count = 0;
                                        foreach ($classlist as $class) {
                                        ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id',$class_id) == $class['id']) {
                                                                                            echo "selected=selected";
                                                                                        }
                                                                                        ?>><?php echo $class['class'] ?></option>
                                        <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger" id="error_class_id"></span>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                    <select id="section_id" name="section_id" class="form-control">
                                    <?php
                                        $count = 0;
                                        if(!empty($sectionslist)) {
                                        foreach ($sectionslist as $section) {
                                        ?>
                                            <option value="<?php echo $section['section_id'] ?>" <?php if (set_value('section_id',$section_id) == $section['section_id']) {
                                                                                            echo "selected=selected";
                                                                                        }
                                                                                        ?>><?php echo $section['section'] ?></option>
                                        <?php
                                            $count++;
                                        } }
                                        else {
                                        ?>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php  } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('intake'); ?></label> <small class="req"> *</small>
                                    <input autofocus="" id="vacancies" name="vacancies" placeholder="No.of Intake" type="number" class="form-control" value="<?php echo set_value('vacancies',$vacancies) ?>" />
                                    <span class="text-danger"><?php echo form_error('vacancies'); ?></span>
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
                                if (($this->rbac->hasPrivilege('school_vacancies', 'can_add')) || ($this->rbac->hasPrivilege('school_vacancies', 'can_edit'))) {
                                    echo "8";
                                } else {
                                    echo "12";
                                }
                                ?>">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('vacancies_positions'); ?></h3>
                                                                                            <div class="btn-group pull-right mml15">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back</button> 
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('vacancies_positions'); ?></div>
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <?php if ($this->session->flashdata('msgdelete')) {
                            ?>
                                <?php echo $this->session->flashdata('msgdelete');
                                $this->session->unset_userdata('msgdelete'); ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('section'); ?></th>
                                        <th><?php echo $this->lang->line('intake'); ?></th>
                                        <th><?php echo $this->lang->line('admitted'); ?></th>

                                        <th><?php echo $this->lang->line('vacancies'); ?></th>
                                

                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($houselist as $house) {

                                        $vacanciescount = $house['vacancies'] - $house['intakes'];
                                    ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $house['class'] ?></td>
                                            <td class="mailbox-name"><?php echo $house['section'] ?></td>
                                            <td class="mailbox-name"><?php echo $house['vacancies'] ?></td>
                                            <td class="mailbox-name"><?php echo $house['intakes']; ?></td>
                                            <td class ="mailbox-name"><?php echo $vacanciescount; ?></td>

                                            <td class="mailbox-date pull-right">
                                                <?php if ($this->rbac->hasPrivilege('school_vacancies', 'can_edit')) { ?>
                                                    <a href="<?php echo base_url(); ?>admin/school_vacancies/edit/<?php echo $house['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('school_vacancies', 'can_delete')) { ?>
                                                    <a href="<?php echo base_url(); ?>admin/school_vacancies/delete/<?php echo $house['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    $count++;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

    $(document).ready(function() {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function(e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });

    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }
</script>