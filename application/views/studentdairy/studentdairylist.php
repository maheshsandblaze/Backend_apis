<?php
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-flask"></i> <?php echo $this->lang->line('studentdairy'); ?>
        </h1>
    </section>
    <section class="content">
        <div class="row">

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                        <div class="btn-group pull-right">
                            <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button>
                        </div>
                    </div>
                    <form class="assign_teacher_form" action="<?php echo base_url(); ?>studentdairy/searchvalidation" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if ($this->session->flashdata('msg')) { ?>
                                        <?php
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                        ?>
                                    <?php } ?>
                                    <?php echo $this->customlib->getCSRF(); ?>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="searchclassid" name="class_id" onchange="getSectionByClass(this.value, 0, 'secid')" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                            ?>
                                                <option <?php
                                                        if ($class_id == $class["id"]) {
                                                            echo "selected";
                                                        }
                                                        ?> value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_class_id"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="secid" name="section_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="section_id_error text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3 col-sm-6" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('subject_group'); ?></label>
                                        <select id="subject_group_id" name="subject_group_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="section_id_error text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3 col-sm-6" style="display: none;">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('subject'); ?></label>
                                        <select id="subid" name="subject_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="section_id_error text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="search_filter" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>
                    <div class="">
                        <div class="nav-tabs-custom theme-shadow">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('studentdairy_list'); ?></h3>
                                <?php if ($this->rbac->hasPrivilege('studentdairy', 'can_add')) { ?>
                                    <div class="box-tools pull-right">
                                        <button onclick="addform()" type="button" class="btn btn-sm btn-primary modal_form" data-method_call="add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="box-body table-responsive">
                                        <div class="download_label"> <?php echo $this->lang->line('studentdairy_list'); ?></div>
                                        <div>
                                            <table class="table table-striped table-bordered table-hover studentdairy-list" data-export-title="<?php echo $this->lang->line('studentdairy_list'); ?>">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('class') ?></th>
                                                        <th><?php echo $this->lang->line('section') ?></th>
                                                        <th><?php echo $this->lang->line('date'); ?></th>
                                                        <!-- <th><?php echo $this->lang->line('homework_details'); ?></th> -->
                                                        <th><?php echo $this->lang->line('created_by'); ?></th>
                                                        <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close close_btn" data-dismiss="modal">&times;</button>
                <h4 class="modal-title box-title">Add Student Classwise Homework</h4>
            </div>
            <form id="formadd" method="post" class="ptt10" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <input type="hidden" id="modal_record_id" value="0" name="record_id">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('class') ?></label><small class="req"> *</small>
                                        <select class="form-control modal_class_id" name="modal_class_id" id="modal_class_id">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($classlist as $key => $value) {
                                            ?>
                                                <option value="<?php echo $value["id"] ?>"><?php echo $value["class"] ?></option>

                                            <?php } ?>

                                        </select>
                                        <span id="name_add_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('section') ?></label><small class="req"> *</small>
                                        <select class="form-control modal_section_id" name="modal_section_id" id="modal_section_id">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span id="name_add_error" class="text-danger"></span>
                                    </div>
                                </div>


                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="date" class="form-control" id="date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                        <span id="date_add_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('submission_date'); ?></label><small class="req"> *</small>
                                        <input type="text" id="submit_date" name="submit_date" class="form-control" value="<?php echo set_value('follow_up_date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                    </div>
                                </div> -->

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" id="file" name="userfile" class="form-control filestyle">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                                        <textarea name="description" id="compose-textarea" class="form-control"><?php echo set_value('address'); ?>
                                        </textarea>
                                    </div>
                                </div>
                            </div><!--./row-->
                        </div><!--./col-md-12-->
                    </div><!--./row-->
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade pr-0" id="evaluation" tabindex="-1" role="dialog" aria-labelledby="evaluation" style="padding-left: 0 !important">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('evaluate_studentdairy'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="evaluation_details">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="studentdairy_docs" tabindex="-1" role="dialog" aria-labelledby="evaluation" style="padding-left: 0 !important">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('studentdairy_assignments'); ?></h4>
            </div>
            <div class="modal-body pb0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <div class="table-responsive overflow-visible-lg">
                                <div>
                                    <table class="table table-hover table-striped table-bordered all-list" data-export-title="<?php echo $this->lang->line('studentdairy_assignments'); ?>">
                                        <thead>
                                            <tr>
                                                <th class="white-space-nowrap"><?php echo $this->lang->line('name') ?></th>
                                                <th><?php echo $this->lang->line('message') ?></th>

                                                <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentdairy_docs_result">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- -->
<script type="text/javascript">
    $('#myModal').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
    });

    var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'mmm' => 'M', 'Y' => 'yyyy']) ?>';

    $(document).ready(function() {
        $("#studentdairy_date").datepicker({
            format: date_format,
            autoclose: true,
            weekStart: start_week,
            language: '<?php echo $language_name ?>',
        }).on('changeDate', function(selected) {

            var minDate = new Date(selected.date);
            $('#submit_date').datepicker('setStartDate', minDate);
        });

        $("#submit_date").datepicker({
            format: date_format,
            autoclose: true,
            weekStart: start_week,
            language: '<?php echo $language_name ?>',
        }).on('changeDate', function(selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#studentdairy_date').datepicker('setEndDate', minDate);
        });
    });

    $(document).ready(function() {

        $('#studentdairydate,#submitdate').datepicker({
            format: date_format,
            autoclose: true,
            language: '<?php echo $language_name ?>'
        });

        $("#btnreset").click(function() {
            $("#form1")[0].reset();
        });

    });

    function studentdairy_docs(id) {
        $('#studentdairy_docs').modal('show');
        initDatatable('all-list', 'studentdairy/studentdairy_docs/' + id, '', [], 100);
    }

    function addform() {
        $('#modal_class_id').val('');
        $('#modal_section_id').val('');
        $('#modal_subject_group_id').val('');
        $('#modal_subject_id').val('');
        $('#studentdairy_marks').val('');
        $('#compose-textarea').val('');
    }
</script>
<script>
    $(function() {
        $("#compose-textarea,#desc-textarea").wysihtml5();
    });
</script>
<script type="text/javascript">
    $(document).ready(function(e) {
        getSectionByClass("<?php echo $class_id ?>", "<?php echo $section_id ?>", 'secid');
        getSubjectGroup("<?php echo $class_id ?>", "<?php echo $section_id ?>", "<?php echo $subject_group_id ?>", 'subject_group_id')
        getsubjectBySubjectGroup("<?php echo $class_id ?>", "<?php echo $section_id ?>", "<?php echo $subject_group_id ?>", "<?php echo $subject_id ?>", 'subid');
    });

    $(document).ready(function(e) {
        $("#formedit").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("studentdairy/edit") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);

                    } else {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                }
            });
        }));
    });

    function evaluation(id) {
        $('#evaluation').modal('show');
        $('#evaluation_details').html("");
        $.ajax({
            url: '<?php echo base_url(); ?>studentdairy/evaluation/' + id,
            success: function(data) {
                $('#evaluation_details').html(data);
                initDatatable('evaluation-list', 'studentdairy/dtevaluationlist/' + id, '', [], 100);
            },
            error: function() {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    function addstudentdairy() {
        $('iframe').contents().find('.wysihtml5-editor').html("");
    }

    $(document).on('submit', 'form#evaluation_data', function(e) {

        $("#hlist").find('option.active').attr("selected", "selected");
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");

        $.ajax({
            url: "<?php echo site_url("studentdairy/add_evaluation") ?>",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $this.button('loading');
            },
            success: function(res) {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    $('#evaluation').modal('hide');
                    $('.studentdairy-list').DataTable().ajax.reload(null, false);
                    $('.studentdairy-list-close').DataTable().ajax.reload(null, false);
                }
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function() {
                $this.button('reset');
            }
        });
    });
</script>
<script type="text/javascript">
    var save_method; //for save method string
    var update_id; //for save method string

    function getSectionByClass(class_id, section_id, select_control) {
        if (class_id != "") {
            $('#' + select_control).html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                beforeSend: function() {
                    $('#' + select_control).addClass('dropdownloading');
                },
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#' + select_control).append(div_data);
                },
                complete: function() {
                    $('#' + select_control).removeClass('dropdownloading');
                }
            });
        }
    }

    $(document).ready(function() {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });

    $(document).on('click', '.modal_form', function() {
        save_method = $(this).data('method_call');
        $('#formadd:input').val('');
        if (save_method == "edit") {
            update_id = $(this).data('record_id');
            $('#myModal').modal('show');
            $('#myModal .box-title').text('<?php echo $this->lang->line('edit_studentdairy'); ?>');
        } else if (save_method == "add") {
            $('iframe').contents().find('.wysihtml5-editor').html("");
            $('#modal_record_id').val(0);
            $('#myModal .box-title').text('<?php echo $this->lang->line('add_studentdairy'); ?>');
            $('#myModal').modal('show');
        } else {

        }
    });

    $(document).on('change', '#modal_section_id', function() {
        var class_id = $('.modal_class_id').val();
        var section_id = $(this).val();
        getSubjectGroup(class_id, section_id, 0, 'modal_subject_group_id');
    });

    $(document).on('change', '#secid', function() {
        var class_id = $('#searchclassid').val();
        var section_id = $(this).val();
        getSubjectGroup(class_id, section_id, 0, 'subject_group_id');
    });

    $(document).on('change', '#modal_subject_group_id', function() {
        var class_id = $('.modal_class_id').val();
        var section_id = $('.modal_section_id').val();
        var subject_group_id = $(this).val();
        getsubjectBySubjectGroup(class_id, section_id, subject_group_id, 0, 'modal_subject_id');
    });

    $(document).on('change', '#subject_group_id', function() {
        var class_id = $('#searchclassid').val();
        var section_id = $('#secid').val();
        var subject_group_id = $(this).val();
        getsubjectBySubjectGroup(class_id, section_id, subject_group_id, 0, 'subid');
    });

    $("#formadd").on('submit', (function(e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");

        $.ajax({
            url: "<?php echo site_url("studentdairy/create") ?>",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $this.button('loading');
            },
            success: function(res) {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {

                    // $('.studentdairy-list').DataTable().ajax.reload(null, false);
                    //   ?  $('.studentdairy-list-close').DataTable().ajax.reload(null, false);
                    successMsg(res.message);
                    $('#myModal').modal('hide');
                    $this.button('reset');
                }
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function() {
                $this.button('reset');
            }
        });
    }));

    $(document).on('change', '.modal_class_id', function() {
        var modal_class_id = $('.modal_class_id').val();
        var modal_section_id = $('.modal_section_id').val();
        getSectionByClass(modal_class_id, 0, 'modal_section_id');
    });

    function getSubjectGroup(class_id, section_id, subjectgroup_id, subject_group_target) {
        if (class_id != "" && section_id != "") {

            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: 'POST',
                url: base_url + 'admin/subjectgroup/getGroupByClassandSection',
                data: {
                    'class_id': class_id,
                    'section_id': section_id
                },
                dataType: 'JSON',
                beforeSend: function() {
                    // setting a timeout
                    $('#' + subject_group_target).html("").addClass('dropdownloading');
                },
                success: function(data) {

                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (subjectgroup_id == obj.subject_group_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.subject_group_id + " " + sel + ">" + obj.name + "</option>";
                    });
                    $('#' + subject_group_target).append(div_data);
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                },
                complete: function() {
                    $('#' + subject_group_target).removeClass('dropdownloading');
                }
            });
        }
    }

    function getsubjectBySubjectGroup(class_id, section_id, subject_group_id, subject_group_subject_id, subject_target) {
        if (class_id != "" && section_id != "" && subject_group_id != "") {

            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: 'POST',
                url: base_url + 'admin/subjectgroup/getGroupsubjects',
                data: {
                    'subject_group_id': subject_group_id
                },
                dataType: 'JSON',
                beforeSend: function() {
                    // setting a timeout
                    $('#' + subject_target).html("").addClass('dropdownloading');
                },
                success: function(data) {

                    $.each(data, function(i, obj) {
                        var code = '';
                        if (obj.code) {
                            code = " (" + obj.code + ") ";
                        }

                        var sel = "";
                        if (subject_group_subject_id == obj.id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + code + "</option>";
                    });
                    $('#' + subject_target).append(div_data);
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
                complete: function() {
                    $('#' + subject_target).removeClass('dropdownloading');
                }
            });
        }
    }

    $('#myModal').on('shown.bs.modal', function() {

        if (save_method == "edit") {
            $.ajax({
                url: base_url + "studentdairy/getRecord",
                type: "POST",
                data: {
                    id: update_id
                },
                dataType: 'json',

                beforeSend: function() {
                    $('#myModal').addClass('modal_loading');
                },
                success: function(res) {
                    $('#modal_record_id').val(res.id);
                    $("#date").datepicker("update", new Date(res.date));
                    $('.modal_class_id').val(res.class_id);
                    $('.wysihtml5-sandbox').contents().find('.wysihtml5-editor').html(res.description);
                    $('.modal_class_id option[value=' + res.class_id + ']').attr('selected', 'selected');
                    getSectionByClass(res.class_id, res.section_id, 'modal_section_id');
                    $("#document").val(res.document);


                    $('#myModal').removeClass('modal_loading');
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $('#myModal').removeClass('modal_loading');
                },
                complete: function() {
                    $('#myModal').removeClass('modal_loading');
                }
            });
        }
    })
</script>
<script>
    $(document).ready(function() {
        emptyDatatable('studentdairy-list');
    });

    $(document).ready(function() {
        emptyDatatable('studentdairy-list-close');
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('submit', '.assign_teacher_form', function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $(this).find("button[type=submit]:focus");
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data, // serializes the form's elements.
                beforeSend: function() {
                    $('[id^=error]').html("");
                    $this.button('loading');
                    resetFields($this.attr('name'));
                },
                success: function(response) { // your success handler

                    if (!response.status) {
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });

                        $('#delete_btn_id').addClass('hide');
                    } else {
                        $('#delete_btn_id').removeClass('hide');
                        initDatatable('studentdairy-list', 'studentdairy/dtstudentdairylist', response.params, [], 100);
                        // initDatatable('studentdairy-list-close', 'studentdairy/closestudentdairylist', response.params, [], 100);
                    }
                },
                error: function() { // your error handler
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });
        });
    });

    function resetFields(search_type) {
        if (search_type == "keyword_search") {
            $('#class_id').prop('selectedIndex', 0);
            $('#section_id').find('option').not(':first').remove();
        } else if (search_type == "class_search") {
            $('#search_text').val("");
        }
    }

    $("input[name='checkAll']").click(function() {
        $("input[name='delete_studentdairy[]']").not(this).prop('checked', this.checked);
    });

    $("#deletebulk").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var checkCount = $("input[name='delete_studentdairy[]']:checked").length;

        if (checkCount == 0) {
            alert("<?php echo $this->lang->line('atleast_one_studentdairy_should_be_select'); ?>");

        } else {
            if (confirm("<?php echo $this->lang->line('are_you_sure_you_want_to_delete'); ?>")) {

                var form = $(this);
                var url = form.attr('action');
                var submit_button = form.find(':submit');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    dataType: "JSON", // serializes the form's elements.
                    beforeSend: function() {
                        submit_button.button('loading');
                    },
                    success: function(data) {
                        var message = "";
                        if (!data.status) {
                            $.each(data.error, function(index, value) {
                                message += value;
                            });

                            errorMsg(message);

                        } else {
                            successMsg(data.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) { // if error occured
                        submit_button.button('reset');
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                    },
                    complete: function() {
                        submit_button.button('reset');
                    }
                });
            }
        }
    });

    $(document).ready(function() {
        $('#delete_btn_id').hide();
    })

    $('#search_filter').click(function() {
        $('#delete_btn_id').show();
    })

    $('.close_btn').click(function() {
        $(".dropify-clear").click();
    })
</script>