<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('my_day_today'); ?></h3>
                    </div>
                    <div class="box-body table-responsive overflow-visible-lg">
                        <div class="download_label"> <?php echo $this->lang->line('my_day_today'); ?></div>
                        <div >
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('section'); ?></th>
                                        
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <th><?php echo $this->lang->line('mobile'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        
                                        <!--<th width="30%"><?php echo $this->lang->line('reason'); ?></th>-->
                                        <!--<th><?php echo $this->lang->line('status'); ?></th>-->
                                        <th class="pull-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $value) {
    ?>
                                        <tr>
                                            <td><?php echo $value['class']; ?></td>
                                            <td><?php echo $value['section']; ?></td>
                                            <td><?php echo $value['father_name']; ?></td>
                                            <td><?php echo $value['mobileno']; ?></td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($value['date'])); ?></td>
                                            
                                            <td class="mailbox-date pull-right">
                                                <span data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><a class="btn btn-default btn-xs" onclick="mydaytoday(<?php echo $value['id']; ?>);" title="" data-target="#mydaytoday" data-toggle="modal">
                                                                <i class="fa fa-reorder"></i></a></span>
                                            </td>
                                            
                                        </tr>
                                        <?php
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<div class="modal fade" id="mydaytoday" tabindex="-1" role="dialog" aria-labelledby="mydaytoday" style="padding-left: 0 !important">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('my_day_today'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="mydaytoday_details">
            </div>
        </div>
    </div>
</div>

<!-- -->
<script type="text/javascript">
    function get(id) {
        $.ajax({
            url: "<?php echo site_url("user/apply_leave/get_details") ?>/" + id,
            type: "POST",
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,

            success: function (res)
            {
                $('#apply_date').val(res.apply_date);
                $('#from_date').val(res.from_date);
                $('#to_date').val(res.to_date);
                $('#message').html(res.reason);
                $('#leave_id').val(res.id);
                $('#student_session_id').val(res.student_session_id);
                $('#title').html('<?php echo $this->lang->line('edit_leave'); ?>');
                $('#homework_docs').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
        });
    }

    function add_leave() {
        $('#title').html('<?php echo $this->lang->line('add_leave'); ?>');
        $('#homework_docs').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    $(document).ready(function () {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });

    $("#addleave_form").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: "<?php echo site_url("user/apply_leave/add") ?>",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    }));
    
    function mydaytoday(id) {

        $('#evaluation_details').html("");
        $.ajax({
            url: baseurl + 'user/my_day_today/mydaytoday_detail/' + id,
            success: function(data) {
                $('#mydaytoday_details').html(data);
            },
            error: function() {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }
    
    $(document).ready(function(e) {

        $('#mydaytoday').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

    });
</script>