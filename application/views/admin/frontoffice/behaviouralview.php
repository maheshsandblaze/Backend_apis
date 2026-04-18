<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?></h1>
    </section>
    <?php $call_type = $this->customlib->getCalltype(); ?>
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('behavioural_note', 'can_add')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('add_behavioural_note'); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/behavioural_note') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <div class="box-body">
                                <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg'); ?>

                                <div class="form-group studentDiv">
                                    <label><?php echo $this->lang->line('class'); ?></label> <small class="req"> *</small>
                                    <select autofocus="" id="class_id" name="class_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        $count = 0;
                                        foreach ($classlist as $class) {
                                        ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
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

                                <div class="form-group studentDiv">
                                    <label><?php echo $this->lang->line('section'); ?></label>
                                    <select id="section_id" name="section_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>

                                <div class="form-group studentDiv">
                                    <button type="button" name="searchbutton" id="searchbutton" value="search_full" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>



                                <div class="form-group studentDiv" style="display: none;">
                                    <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
                                    <input type="text" name="search_text" id="search_text" class="form-control" value="<?php echo set_value('search_text'); ?>" placeholder="Search By Student Name">
                                    <p id="searchTag"></p>
                                </div>

                                <div class="form-group studentDiv" id="studentlist">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('student'); ?></label><small class="req"> *</small>

                                    <!-- <select id="name" name="studentName" class="form-control select2"> -->
                                    <select id="name" name="name" class="form-control ">
                                        <option value="">select</option>

                                    </select>

                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>



                                <div class="form-group">
                                    <label for="pwd"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>


                                <div class="form-group">
                                    <label for="email"><?php echo "Handwriting"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_1" rows="3"><?php echo set_value('parameter_1'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_1'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Listening"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_2" rows="3"><?php echo set_value('parameter_2'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_2'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Behaviour In Class Room"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_3" rows="3"><?php echo set_value('parameter_3'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_3'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Behaviour With Teachers"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_4" rows="3"><?php echo set_value('parameter_4'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_4'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Behaviour With Classmates / Elders And Youngers"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_5" rows="3"><?php echo set_value('parameter_5'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_5'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Behavour In Campus"; ?><small class="req"> *</small></label>
                                    <textarea class="form-control" name="parameter_6" rows="3"><?php echo set_value('parameter_6'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('parameter_6'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo "Bike"; ?></label>
                                    <textarea class="form-control" name="parameter_7" rows="3"><?php echo set_value('parameter_7'); ?></textarea>
                                    <!-- <span class="text-danger"><?php echo form_error('parameter_7'); ?></span> -->

                                </div>






                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                </div><!--/.col (right) -->
                <!-- left column -->
                <div class="col-md-<?php
                                    if ($this->rbac->hasPrivilege('behavioural_note', 'can_add')) {
                                        echo "8";
                                    } else {
                                        echo "12";
                                    }
                                    ?>">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><?php echo $this->lang->line('behavioural_list'); ?></h3>
                            <div class="btn-group pull-right">
                                <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button>
                            
                            </div>
                            <div class="box-tools pull-right">
                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="mailbox-messages table-responsive overflow-visible-lg">
                                <table class="table table-striped table-bordered table-hover call-list" data-export-title="<?php echo $this->lang->line('behavioural_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('section'); ?></th>
                                            <th><?php echo $this->lang->line('name'); ?></th>
                                            <th><?php echo $this->lang->line('staff'); ?></th>


                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <!-- <th><?php echo $this->lang->line('note'); ?></th> -->
                                            <th class="text-right noExport "><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table><!-- /.table -->
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                    </div>
                </div><!--/.col (left) -->
                <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- new END -->
<div id="calldetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details') ?></h4>
            </div>
            <div class="modal-body" id="getdetails">

            </div>
        </div>
    </div>
</div>
</div><!-- /.content-wrapper -->

<script type="text/javascript">
    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/behavioural_note/details/' + id,
            success: function(result) {
                $('#getdetails').html(result);
            }
        });
    }
</script>

<script>
    (function($) {
        'use strict';
        $(document).ready(function() {
            initDatatable('call-list', 'admin/behavioural_note/getcalllist', [], [], 100);
        });
    }(jQuery))
</script>

<script>
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


    $(document).on('submit', '.class_search_form', function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var $this = $("button[type=submit][clicked=true]");
        var form = $(this);
        var url = form.attr('action');
        var form_data = form.serializeArray();
        form_data.push({
            name: 'search_type',
            value: $this.attr('value')
        });
        $.ajax({
            url: url,
            type: "POST",
            dataType: 'JSON',
            data: form_data, // serializes the form's elements.
            beforeSend: function() {
                $('[id^=error]').html("");
                $this.button('loading');
                resetFields($this.attr('value'));
            },
            success: function(response) { // your success handler

                if (!response.status) {
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {

                    if ($.fn.DataTable.isDataTable('.student-list')) { // if exist datatable it will destrory first
                        $('.student-list').DataTable().destroy();
                    }
                    table = $('.student-list').DataTable({

                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'copy',
                                text: '<i class="fa fa-files-o"></i>',
                                titleAttr: 'Copy',
                                className: "btn-copy",
                                title: $('.student-list').data("exportTitle"),
                                exportOptions: {
                                    columns: ["thead th:not(.noExport)"]
                                }
                            },
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel-o"></i>',
                                titleAttr: 'Excel',
                                className: "btn-excel",
                                title: $('.student-list').data("exportTitle"),
                                exportOptions: {
                                    columns: ["thead th:not(.noExport)"]
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="fa fa-file-text-o"></i>',
                                titleAttr: 'CSV',
                                className: "btn-csv",
                                title: $('.student-list').data("exportTitle"),
                                exportOptions: {
                                    columns: ["thead th:not(.noExport)"]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf-o"></i>',
                                titleAttr: 'PDF',
                                className: "btn-pdf",
                                title: $('.student-list').data("exportTitle"),
                                exportOptions: {
                                    columns: ["thead th:not(.noExport)"]
                                },

                            },
                            {
                                extend: 'print',
                                text: '<i class="fa fa-print"></i>',
                                titleAttr: 'Print',
                                className: "btn-print",
                                title: $('.student-list').data("exportTitle"),
                                customize: function(win) {

                                    $(win.document.body).find('th').addClass('display').css('text-align', 'center');
                                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                                    $(win.document.body).find('h1').css('text-align', 'center');
                                },
                                exportOptions: {
                                    columns: ["thead th:not(.noExport)"]

                                }

                            }
                        ],

                        "columnDefs": [{
                            "targets": -1,
                            "orderable": false
                        }],


                        "language": {
                            processing: '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span> '
                        },
                        "pageLength": 100,
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": baseurl + "student/dtstudentlist2",
                            "dataSrc": 'data',
                            "type": "POST",
                            'data': response.params,

                        },
                        "drawCallback": function(settings) {

                            $('.detail_view_tab').html("").html(settings.json.student_detail_view);
                        }

                    });
                    //=======================
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
</script>

<script>
    $('#searchbutton').click(function() {
        var base_url = '<?php echo base_url() ?>';



        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var searchText = $('#search_text').val();



        $.ajax({
            type: "post",
            url: base_url + "admin/behavioural_note/getStudentDetails",
            data: {
                'classID': class_id,
                'sectionID': section_id,
                'searchText': searchText
            },
            dataType: "json",
            success: function(response) {

                $('#name').empty();




                $.each(response, function(index, student) {
                    var fullName = student.firstname + ' ' + student.lastname;
                    $('#name').append('<option value="' + student.studentID + '">' + fullName + '</option>');
                });


            },
            error: function(xhr, status, error) {
                console.error('Error fetching student data:', error);
            }
        });
    })
</script>