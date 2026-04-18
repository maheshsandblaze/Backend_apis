<div class="row row-eq">
    <?php
    $admin = $this->customlib->getLoggedInUserData();
    ?>
    <div class="col-lg-8 col-md-8 col-sm-8 paddlr">
        <!-- general form elements -->
        <form id="upload" method="post" class="ptt10" style="min-height: 500px;">
            <div class="scroll-area">
                <div class="form-group">
                    <label><?php echo $this->lang->line('description'); ?></label>
                </div>
                <p><?php echo $result['description']; ?></p>
                <hr>
                <!-- <div class="row">
                    <div class="col-sm-12">
                        <div class="row">





                        </div>
                    </div>
                </div> -->

            </div>

        </form>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-eq">
        <div class="scroll-area">
            <div class="taskside">
                <h4><?php echo $this->lang->line('summary'); ?></h4>
                <div class="box-tools pull-right">
                </div><!-- /.box-tools -->
                <h5 class="pt0 task-info-created">

                </h5>
                <hr class="taskseparator" />


                <div class="task-info task-single-inline-wrap task-info-start-date">
                    <h5><i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
                        <span>
                            <?php echo $this->lang->line('date'); ?></span>:
                        <?php
                        $evl_date = "";
                        if (!IsNullOrEmptyString($result['date'])) {
                            echo $this->customlib->dateformat($result['date']);
                        }
                        ?>
                    </h5>
                </div>

                <div class="task-info task-single-inline-wrap ptt10">

                    <label><span><?php echo $this->lang->line("class") ?></span>: <?php echo $result['class']; ?></label>
                    <label><span><?php echo $this->lang->line("section") ?></span>: <?php echo $result['section']; ?></label>

                    <?php

                    if (!empty($result["document"])) { ?>
                        <label><?php echo $this->lang->line('documents'); ?></label>

                        <ul class="list-group content-share-list">
                            <li class="overflow-hidden mb5">

                                <img src="<?php echo base_url('backend/images/upload-file.png'); ?>">
                                <?php echo $this->media_storage->fileview($result['document']) ?>
                                <a href="<?php echo base_url() . "user/studentdairy/download/" . $result["id"] ?>" data-toggle="tooltip" data-original-title=""><i class="fa fa-download"></i></a>

                            </li>
                        </ul>

                    <?php } ?>



                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('.filestyle').dropify();
</script>
<script>
    $(document).ready(function() {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        $('#evaluation_date').datepicker({
            format: date_format,
            autoclose: true
        });
    });

    $(document).ready(function() {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        $('#follow_date_of_call').datepicker({
            format: date_format,
            autoclose: true
        });

        $("#modaltable").DataTable({
            dom: "Bfrtip",
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',

                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'

                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                    customize: function(win) {
                        $(win.document.body)
                            .css('font-size', '10pt');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
    });
</script>

<script>
    $("#upload").on('submit', (function(e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");

        $.ajax({
            url: "<?php echo site_url("user/homework/upload_docs") ?>",
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
                    window.location.reload(true);
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
</script>