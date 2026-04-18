<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('student_assessment'); ?></h3>
  
                    </div>

                    <div class="box-body table-responsive">
                        <div>
                            <div class="download_label"><?php echo $this->lang->line('studentdairy_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class') ?></th>
                                        <th><?php echo $this->lang->line('section') ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $upload_docsButton = 0;

                                    foreach ($studentdairylist as $key => $studentdairy) {

                                    ?>
                                        <tr>
                                            <td><?php echo $studentdairy["class"] ?></td>
                                            <td><?php echo $studentdairy["section"] ?></td>

                                            <td><?php echo $this->customlib->dateformat($studentdairy['date']); ?></td>




                                            <td class="mailbox-date pull-right">
                                                <span data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><a class="btn btn-default btn-xs" onclick="evaluation(<?php echo $studentdairy['id']; ?>);" title="" data-target="#evaluation" data-toggle="modal" data-original-title="Evaluation">
                                                        <i class="fa fa-reorder"></i></a></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="evaluation" tabindex="-1" role="dialog" aria-labelledby="evaluation" style="padding-left: 0 !important">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('student_assessment'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="evaluation_details">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        $('#studentdairy_date,#submit_date,#studentdairydate,#submitdate').datepicker({
            format: date_format,
            autoclose: true
        });

        $("#btnreset").click(function() {
            $("#form1")[0].reset();
        });
    });
</script>

<script>
    $(function() {
        $("#compose-textarea,#desc-textarea").wysihtml5();
    });
</script>

<script type="text/javascript">
    function getRecord(id) {

        $.ajax({
            url: "<?php echo site_url("studentdairy/getRecord/") ?>" + id,
            type: "POST",
            dataType: 'json',

            success: function(res) {
                getSectionByClass(res.class_id, res.section_id);
                getSubjectByClassandSection(res.class_id, res.section_id, res.subject_id);
                $("#studentdairydate").val(new Date(res.studentdairy_date).toString("MM/dd/yyyy"));
                $("#submitdate").val(new Date(res.submit_date).toString("MM/dd/yyyy"));
                $("#desc-textarea").text(res.description);
                $('iframe').contents().find('.wysihtml5-editor').html(res.description);
                $('select[id="classid"] option[value="' + res.class_id + '"]').attr("selected", "selected");
                $("#studentdairyid").val(res.id);
                $("#document").val(res.document);
            }
        });
    }

    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#sectionid,#secid').html("");
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
                    $('#sectionid,#secid').append(div_data);
                }
            });
        }
    }

    function getSubjectByClassandSection(class_id, section_id, subject_id) {
        if (class_id != "" && section_id != "" && subject_id != "") {
            $('#subjectid,#subid').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "POST",
                url: base_url + "admin/teacher/getSubjctByClassandSection",
                data: {
                    'class_id': class_id,
                    'section_id': section_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (subject_id == obj.subject_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.subject_id + " " + sel + ">" + obj.name + " (" + obj.type + ")" + "</option>";
                    });

                    $('#subjectid,#subid').append(div_data);
                }
            });
        }
    }

    function evaluation(id) {

        $('#evaluation_details').html("");
        $.ajax({
            url: baseurl + 'user/studentdairy/studentdairy_detail/' + id,
            success: function(data) {
                $('#evaluation_details').html(data);
            },
            error: function() {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    function addstudentdairy() {
        $('iframe').contents().find('.wysihtml5-editor').html("");
    }

    $(document).ready(function(e) {

        $('#evaluation').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

    });
</script>