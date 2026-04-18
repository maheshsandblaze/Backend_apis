<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>backend/js/ckeditor_config.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/adapters/jquery.js"></script>
<?php
$language      = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<style>
     @media print {
               .noprint {
                  visibility: hidden;
               }
            }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom theme-shadow">
                    <div class="box-header ptbnull">
                            <h3 class="box-title titlefix pt5">  Video List</h3>
                            <?php if ($this->rbac->hasPrivilege('online_course', 'can_add')) {
    ?>
                            <button class="btn btn-primary btn-sm pull-right question-btn" data-recordid="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_video'); ?></button>
                        <?php
}
?>
                    </div>
                    <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                    <div class="box-body p0">
                        <div class="mailbox-messages">
                            <div class="table-responsive overflow-visible">
                                 <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('online_exam_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>
                                        <th class="pull-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                               <tbody>
										<?php 
										//print_r($category_list);exit;
										$s = 0; 
										foreach($video_list as $video){ 
											$s++;
										?>
											<tr>
												<td><?php echo $video['title']; ?></td>
<?php
$youtube_url = $video['url'];
$video_id = substr(parse_url($youtube_url, PHP_URL_PATH), 1); 
?>
												<!--<td>
												<iframe 
													width="560" 
													height="315" 
													src="https://sandblazedigitals.com/whatsapp/pragna/c49ba5b225e6d2412c7eb25631a9e0c4%20(1).mp4" 
													title="YouTube video player" 
													frameborder="0" 
													allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
													allowfullscreen>
												</iframe>
												</td>-->
												 
												<td class="pull-right noExport">
												<a href="#" class="view-video-btn" data-toggle="modal" data-target="#viewVideoModal" data-url="<?php echo $video['url']; ?>"><i class="fa fa-eye"></i></a>&nbsp; &nbsp;
												<a href="javascript:void(0);" class="edit-video-btn" data-id="<?php echo $video['id']; ?>"><i class="fa fa-pencil"></i></a>&nbsp; &nbsp;
												<a href="javascript:void(0);" class="delete-video-btn" data-id="<?php echo $video['id']; ?>">
													<i class="fa fa-trash"></i>
												</a>

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
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>

<?php

function findOption($questionOpt, $find)
{
    foreach ($questionOpt as $quet_opt_key => $quet_opt_value) {
        if ($quet_opt_key == $find) {
            return $quet_opt_value;
        }
    }
    return false;
}
?>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Video</h4>
            </div>
            <form action="<?php echo site_url('admin/onlinecourse/add_video'); ?>" method="POST" id="formsubject">
                <div class="modal-body">
                    
                    <div class="row">
					<input type="hidden" class="form-control" id="category_id" name="category_id" value="<?php echo $category_id; ?>">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="exam">Video Title</label><small class="req"> *</small>
                            <input type="text" class="form-control" id="title" name="title">
                            <span class="text text-danger title_error"></span>
                        </div>
                     </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="exam">Video Url</label><small class="req"> *</small>
                            <input type="url" class="form-control" id="url" name="url">
                            <span class="text text-danger url_error"></span>
                        </div>
                     </div>
					</div>
				  </div>
                   
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('saving') ?>"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
           </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Video</h4>
            </div>
            <form action="<?php echo site_url('admin/onlinecourse/add_video'); ?>" method="POST" id="formsubject">
                <div class="modal-body">
                    
                    <div class="row">
					<input type="hidden" class="form-control" id="category_id" name="category_id" value="<?php echo $category_id; ?>">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="exam">Video Title</label><small class="req"> *</small>
                            <input type="text" class="form-control" id="title" name="title">
                            <span class="text text-danger title_error"></span>
                        </div>
                     </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="exam">Video Url</label><small class="req"> *</small>
                            <input type="url" class="form-control" id="url" name="url">
                            <span class="text text-danger url_error"></span>
                        </div>
                     </div>
					</div>
				  </div>
                   
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('saving') ?>"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
           </div>
    </div>
</div>

<!-- Edit Video Modal -->
<div id="editVideoModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Video</h4>
            </div>
            <form action="" method="POST" id="editVideoForm">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="edit_category_id" name="category_id" value="<?php echo $category_id; ?>">
                    <input type="hidden" class="form-control" id="edit_video_id" name="video_id">
                    
                    <div class="form-group">
                        <label for="edit_title">Video Title</label><small class="req"> *</small>
                        <input type="text" class="form-control" id="edit_title" name="title">
                        <span class="text text-danger title_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_url">Video URL</label><small class="req"> *</small>
                        <input type="url" class="form-control" id="edit_url" name="url">
                        <span class="text text-danger url_error"></span>
                    </div>
                </div>
                   
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveEdit" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--view modal-->
<div class="modal fade" id="viewVideoModal" tabindex="-1" role="dialog" aria-labelledby="viewVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewVideoModalLabel">View Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9"> <
                    <iframe id="videoIframe" class="embed-responsive-item" src="" allowfullscreen></iframe> 
                </div>
            </div>
        </div>
    </div>
</div>



<script>

// $(document).ready(function(){
//     $('#viewVideoModal').on('show.bs.modal', function (e) {
//         var button = $(e.relatedTarget); 
//         var url = button.data('url'); 
//         var modal = $(this);
//         modal.find('#videoIframe').attr('src', url); 
//     });

//     $('#viewVideoModal').on('hide.bs.modal', function () {
//         $(this).find('#videoIframe').attr('src', ''); 
//     });
// });

$(document).ready(function(){
    $('#viewVideoModal').on('show.bs.modal', function (e) {
        var button = $(e.relatedTarget); 
        var url = button.data('url'); 
        
        // Convert youtu.be URL to embed URL
        if (url.includes('youtu.be')) {
            var videoId = url.split('/').pop();
            url = 'https://www.youtube.com/embed/' + videoId;
        } else if (url.includes('youtube.com/watch')) {
            var videoId = new URL(url).searchParams.get("v");
            url = 'https://www.youtube.com/embed/' + videoId;
        }
        
        var modal = $(this);
        modal.find('#videoIframe').attr('src', url); 
    });

    $('#viewVideoModal').on('hide.bs.modal', function () {
        $(this).find('#videoIframe').attr('src', ''); 
    });
});

$(document).on('click', '.edit-video-btn', function() {
    var videoId = $(this).data('id');

    $('#editVideoForm').attr('action', "<?php echo site_url('admin/onlinecourse/edit_video/'); ?>" + videoId);

    $.ajax({
        url: "<?php echo site_url('admin/onlinecourse/get_video_details_by_id'); ?>/" + videoId,
        type: "GET",
        dataType: "json",
        success: function(data) {
            $('#edit_video_id').val(data.id);
            $('#edit_title').val(data.title);
            $('#edit_url').val(data.url);
            $('#editVideoModal').modal('show');
        }
    });

    $('#editVideoForm').submit(function(event) {
        event.preventDefault(); 

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status == 1) {
                    window.location.reload();
                } else {
                    $('.title_error').text(response.error.title);
                    $('.url_error').text(response.error.url);
                }
            }
        });
    });
});

$(document).on('click', '.delete-video-btn', function(e) {
    e.preventDefault(); 
    
    var videoId = $(this).data('id');
    var deleteUrl = "<?php echo site_url('admin/onlinecourse/delete_video'); ?>/" + videoId;

    if (confirm("Are you sure you want to delete this video?")) {
        $.ajax({
            url: deleteUrl,
            type: "POST",
            dataType: "json",
            success: function(response) {
                if (response.status == 1) {
                    $('a[data-id="' + videoId + '"]').closest('tr').remove();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while trying to delete the video.');
            }
        });
    }
});






    $(document).ready(function () {

        CKEDITOR.env.isCompatible = true;

          $('[id="description"]').ckeditor({
                     toolbar: 'Admin_Exam',
                     allowedContent : true,

                     enterMode : CKEDITOR.ENTER_BR,
                     shiftEnterMode: CKEDITOR.ENTER_P,
                     customConfig: baseurl+'/backend/js/ckeditor_config.js',
                });

        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>

<script type="text/javascript">
$(document).on('submit','#delete_question',function(e) {
    e.preventDefault();
    var form = $(this);
    var question_id=form.find("input[id='question_id']").val();
    var url = form.attr('action');
    var $this = form.find("button[type=submit]:focus");
    $this.button('loading');
    $.ajax({
    url: url,
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
      $('.question_row_'+question_id).remove();
      $this.button('reset');
      if (res.status == 1) {
      $('#mydeleteModal').modal('hide');
        successMsg(res.message);
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
    });

        $('#mydeleteModal').on('shown.bs.modal', function (e) {
          var question_id = $(e.relatedTarget).data('onlineexamQuestionId');
          $("#mydeleteModal input[id='question_id']").val(question_id);

        })

    $(document).ready(function () {
        var responseData = null;
        $(document).on('click', "#btnSubmitOnline", function (event) {
            event.preventDefault();
            var file_data = $('#my-file-selector-online').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            console.log(file_data);
            const url = "/admin/examgroup/bulkuploadfile";

            $.ajax({
                url: baseurl + url,
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
               contentType: false,
                cache: false,
                processData:false,
                 beforeSend: function () {
                    // $('.bulkonlinesubmit').button('loading');
                 },
                success: function (data) {
                    $('#fileUploadFormOnline')[0].reset();
                    const headings = Object.keys(data.student_marks[0]);
                    responseData = data.student_marks;
                    var tableHead = document.querySelector('#bulkData thead');
                    var tableBody = document.querySelector('#bulkData tbody');
                    var headingRow = document.createElement('tr');
                    for (var i = 0; i < headings.length; i++) {
                        var nameCell = document.createElement('td');
                        nameCell.textContent = headings[i];
                        headingRow.appendChild(nameCell);
                    }
                    tableHead.appendChild(headingRow);
                    for (var i = 0; i < data.student_marks.length; i++) {
                        var bodyRow = document.createElement('tr');
                        for (var j = 0; j < headings.length; j++) {
                            var nameCell = document.createElement('td');
                            nameCell.textContent = data.student_marks[i][headings[j]];
                            bodyRow.appendChild(nameCell);
                        }
                        bodyRow.appendChild(nameCell);
                        tableBody.append(bodyRow);
                    }
                },
                complete: function () {
                    $('#fileUploadFormOnline')[0].reset();
                }
            });
        });



        $(document).on('click', ".bulkonlinesubmit", function (event) {
            event.preventDefault();
            var form_data = new FormData();
            form_data.append('student_marks', JSON.stringify(responseData));
            form_data.append("record_id", $('#online_record_id').val());
            const url = "/admin/onlineexam/submitmarks";
            $.ajax({
                url: baseurl + url,
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function () {
                    $('.bulkonlinesubmit').button('loading');
                },
                success: function (data) {
                    console.log(data)
                    if (data.status == "0") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $('#bulkUploadOnline').modal('hide');
                    }
                },
                error: function () {
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
                complete: function () {
                    $('#bulkUploadOnline').modal('hide');
                    $('.bulkonlinesubmit').button('reset');
                }
            });
        });

        $('#bulkUploadOnline').on('shown.bs.modal', function (e) {
            $('#my-file-selector-online').dropify();
        });

        $('#myModal,#mydeleteModal,#myGenerateRankModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        })
        $('#myQuestionModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        })

        var date_format_js = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy']) ?>';

        $(function () {
             var dateNow = new Date();
            $('.timepicker').datetimepicker({
                format: 'HH:mm:ss',

             defaultDate:moment(dateNow).hours(0).minutes(0).seconds(0).milliseconds(0)
            });
        });

        $('#myModal').on('hidden.bs.modal', function () {
            $('.is_quiz').attr('checked', false);

            $(this).find(":input, select, textarea")
                    .not('input:checkbox,input:radio')
                    .val('')
                    .end()
                    .removeAttr('checked')
                    .removeAttr('selected')
                    .end();
        });

        $('#myGenerateRankModal').on('hidden.bs.modal', function () {
            $(".modal-body", this).html();
            $(".modal-title", this).html();
        });

        $(document).on('click', '.question-btn', function () {
            var recordid = $(this).data('recordid');
            $('input[name=recordid]').val(recordid);
            $('#myModal').modal('show');
        });

        $('#myQuestionModal').on('show.bs.modal', function (e) {

            //get data-id attribute of the clicked element
            var exam_id = $(e.relatedTarget).data('recordid');
            var is_quiz = $(e.relatedTarget).data('is_quiz');
            if(is_quiz == 1){
                  $("select#question_type option[value*='descriptive']").prop('disabled',true);
            }else{
                  $("select#question_type option[value*='descriptive']").prop('disabled',false);

            }
            $('#modal_exam_id').val(exam_id);
            $('#modal_is_quiz').val(is_quiz);

            //populate the textbox
            getQuestionByExam(1, exam_id,is_quiz);

        });

 $(document).on('click', '.generate_rank', function () {
     var $this = $(this);
     examid=$this.data('recordid');
     examtitle=$this.data('examTitle');
      $('#myGenerateRankModal').modal('show');

      var this_obj=$('#myGenerateRankModal');
      $('.modal-title',this_obj).html('<?php echo $this->lang->line('generate_exam_rank'); ?>  ('+examtitle+')');

   getRankRecord(examid,examtitle);
 });

        $('#myQuestionModal').on('hidden.bs.modal', function (e) {
                $(this).find("input,textarea,select").val('');
                $('.search_box_result').html("");
                $('.search_box_pagination').html("");
                  table.ajax.reload( null, false );
        });

 $(document).on('click', '.download_exam', function () {
            var $this=$(this);
            var recordid = $(this).data('recordid');
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/onlineexam/download_exam",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {

            Popup(data.page);
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }
            });
        });

        $(document).on('click', '.question-btn-edit', function () {
            var $this = $(this);
            var recordid = $this.data('recordid');
            $('input[name=recordid]').val(recordid);
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/onlineexam/getOnlineExamByID",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {

                    if (data.status) {
                        var date_exam_from = new Date(data.result.exam_from);
                        var newDate_exam_from = date_exam_from.toString(date_format_js);
                        var date_exam_to = new Date(data.result.exam_to);

                        if(data.result.auto_publish_date != null && data.result.auto_publish_date != "" && data.result.auto_publish_date != "0000-00-00"){
                          var date_auto_publish_date = new Date(data.result.auto_publish_date);

                         $('#auto_publish_date').data("DateTimePicker").date(date_auto_publish_date);
                        }else{
                            var newDate_auto_publish_date="";
                        }
                        $('#word_limit').val(data.result.answer_word_count);
                        $('#duration').val(data.result.duration);
                        $('#passing_percentage').val(data.result.passing_percentage);
                        $('#exam_to').data("DateTimePicker").date(date_exam_to);
                        $('#exam_from').data("DateTimePicker").date(date_exam_from);
                        $('#exam').val(data.result.exam);
                        $('#attempt').val(data.result.attempt);
                        CKEDITOR.instances['description'].setData(data.result.description);

                        var is_quiz=(data.result.is_quiz == 0)?false:true;

                        $('input[name=is_quiz]').prop('checked',is_quiz);

                        if(is_quiz){
                            $("input.publish_result").attr("disabled", true);

                            document.getElementById('auto_publish_date').disabled = true;
                        }

                        var chk_status=(data.result.is_active == 0)?false:true;

                        $('input[name=is_active]').prop('checked',chk_status);

                        var chk_is_marks_display=(data.result.is_marks_display == 0)?false:true;

                        $('input[name=is_marks_display]').prop('checked',chk_is_marks_display);

                           var chk_is_neg_marking=(data.result.is_neg_marking == 0)?false:true;

                        $('input[name=is_neg_marking]').prop('checked',chk_is_neg_marking);

                          var chk_result_status=(data.result.publish_result == 0)?false:true;

                        $('input[name=publish_result]').prop('checked',chk_result_status);

                        var chk_is_random_question=(data.result.is_random_question == 0)?false:true;
                        $('input[name=is_random_question]').prop('checked',chk_is_random_question);
                        $('#myModal').modal('show');
                    }
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }
            });
        });
    });

    $(document).on('submit',"form#saverank",function(e){

        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var url = form.attr('action');
        var submit_button = form.find(':submit');
        var post_params = form.serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {

                submit_button.button('loading');
            },
            success: function (data)
            {
                successMsg(data.message);
                var rank_modal_obj=$('#myGenerateRankModal');
                var examtitle=$('.modal-title',rank_modal_obj).html();
                var examid=$('#generate_exam_id',rank_modal_obj).val();
                getRankRecord(examid,examtitle);
            },
            error: function (xhr) { // if error occured
                submit_button.button('reset');
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
                submit_button.button('reset');
            }
        });
    });

    $("form#formsubject").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var url = form.attr('action');
        var submit_button = form.find(':submit');
        var post_params = form.serialize();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                $("[class$='_error']").html("");
                submit_button.button('loading');
            },
            success: function (data)
            {
                if (!data.status) {
                   var message = "";
            $.each(data.error, function (index, value) {
            message += value;
            });
            errorMsg(message);
            } else {
            successMsg(data.message);
            $('#myModal').modal('hide');
            table.ajax.reload( null, false );

                }
            },
            error: function (xhr) { // if error occured
                submit_button.button('reset');
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
                submit_button.button('reset');
            }
        });
    });

    function getQuestionByExam(page, exam_id,is_quiz) {
        var search = $("#search_box").val();
        var keyword = $('#form_search #keyword').val();
        var question_type = $('#form_search #question_type').val();
        var question_level = $('#form_search #question_level').val();
        var class_id = $('#form_search #class_id').val();
        var section_id = $('#form_search #section_id').val();
        $.ajax({
            type: "POST",
            url: base_url + 'admin/onlineexam/searchQuestionByExamID',
            data: {'page': page, 'exam_id': exam_id, 'search': search,'keyword':keyword,'question_type':question_type,'question_level': question_level,'class_id':class_id,'section_id':section_id,'is_quiz':is_quiz}, // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
            },
            success: function (data)
            {
                $('.search_box_result').html(data.content);
                $('.search_box_pagination').html(data.navigation);
                $('.row_from').html(data.show_from);
                $('.row_to').html(data.show_to);
                $('.row_count').html(data.total_display);
                if(data.show_to==0){
                    $('.search_box_result').html('<div class="alert alert-danger"><?php echo $this->lang->line("no_record_found"); ?></div>');
                }
            },
            error: function (xhr) { // if error occured
               alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {

            }
        });
    }

    /* Pagination Clicks   */
    $(document).on('click', '.search_box_pagination li.activee', function (e) {
        var _exam_id = $('#modal_exam_id').val();
        var _is_quiz = $('#modal_is_quiz').val();
        var page = $(this).attr('p');
        getQuestionByExam(page, _exam_id,_is_quiz);
    });

    $(document).on('click', '.post_search_submit', function (e) {
        var _exam_id = $('#modal_exam_id').val();
          var __is_quiz = $('#modal_is_quiz').val();
        getQuestionByExam(1, _exam_id,__is_quiz);
    });

    $(document).on('change', '.question_chk', function () {
        var _exam_id = $('#modal_exam_id').val();
        var ques_mark =$(this).closest('div.section-box').find("input[name='question_marks']").val();
        var ques_neg_mark =$(this).closest('div.section-box').find("input[name='question_neg_marks']").val();
        updateCheckbox($(this).val(), _exam_id,ques_mark,ques_neg_mark);
    });

    function updateCheckbox(question_id, exam_id,ques_mark,ques_neg_mark) {
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/onlineexam/questionAdd',
            dataType: 'JSON',
            data: {'question_id': question_id, 'onlineexam_id': exam_id,'ques_mark':ques_mark,'ques_neg_mark':ques_neg_mark},
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status) {
                    successMsg(data.message);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {

            },
        });
    }

    $('#myQuestionListModal').on('hidden.bs.modal', function () {
         table.ajax.reload( null, false );
        });

        $('#bulkUploadOnline').on('hidden.bs.modal', function () {
         table.ajax.reload( null, false );
        });

    $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, section_id);
    });

       function getSectionByClass(class_id, section_id) {
        if (class_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#section_id').addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                },
                complete: function () {
                    $('#section_id').removeClass('dropdownloading');
                }
            });
        }
    }

        $(document).on('click', '.exam_ques_list', function () {
            var $this=$(this);
            var recordid = $(this).data('recordid');
            $('input[name=recordid]').val(recordid);
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/onlineexam/getExamQuestions",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {

                $('#myQuestionListModal').modal('show');
                $('#myQuestionListModal .modal-title').html(data.exam.exam);
                $('#myQuestionListModal .question_list_result').html(data.result);
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }
            });
        });

        $(document).on('click', '.bulkuploadonline', function () {
            var $this=$(this);
            var recordid = $(this).data('recordid');
            $('input[name=recordid]').val(recordid);
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/onlineexam/getExamQuestions",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {
                    $('#bulkUploadOnline').modal('show');
                    $('#bulkUploadOnline .modal-title').html(data.exam.exam);
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }
            });
        });

        $(document).on('click', '.subject_pills li', function () {
            var $this=$(this);
            $this.addClass('active').siblings().removeClass('active');
            var subject_pill_selected=($this.find('a').data('subjectId'));
            if(subject_pill_selected != 0){

            $("div[class*='subject_div_']").css("display","none");
            $('.subject_div_'+subject_pill_selected).css("display","block");
            }else{
               $("div[class*='subject_div_']").css("display","block");
            }
        });

    function getRankRecord(examid,examtitle){
      var this_obj=$('#myGenerateRankModal');
        $.ajax({
            type: "POST",
            url: base_url+"/admin/onlineexam/rankgenerate",
            data: {"examid":examid}, // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
              this_obj.addClass('modal_loading');
            },
            success: function (data)
            {
                $('.modal-body',this_obj).html(data.page);
                this_obj.removeClass('modal_loading');
            },
            error: function (xhr) { // if error occured
                this_obj.removeClass('modal_loading');
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {
                  this_obj.removeClass('modal_loading');
            }
        });
}

</script>

<script type="text/javascript">
    $(".is_quiz").change(function() {
        if(this.checked) {
            $("input.publish_result").attr("disabled", true);
            $("input#auto_publish_date").val("").attr("disabled", true);
        }else{
            $("input.publish_result").removeAttr("disabled");
            $("input#auto_publish_date").removeAttr("disabled");
        }
    });
</script>

<script>
       $(document).ready(function () {
         initDatatable('exam-list','admin/onlineexam/getexamlist',[],[],100); // for upcoming exam datatable will be loaded by default

        $("a[href='#tab_3']").on('shown.bs.tab', function (e) {
            initDatatable('closed-exam-list','admin/onlineexam/getclosedexamlist',[],[],100); // for closed exam
        });
    });
</script>

<script type="text/javascript">
    $("#deletebulk").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var checkCount = $("input[name='exam[]']:checked").length;

        if (checkCount == 0)
        {
            alert("<?php echo $this->lang->line('atleast_one_student_should_be_select'); ?>");

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
                    beforeSend: function () {
                        submit_button.button('loading');
                    },
                    success: function (data)
                    {
                        var message = "";
                        if (!data.status) {
                            $.each(data.error, function (index, value) {
                                message += value;
                            });

                            errorMsg(message);

                        } else {
                            successMsg(data.message);
                            location.reload();
                        }
                    },
                    error: function (xhr) { // if error occured
                        submit_button.button('reset');
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    },
                    complete: function () {
                        submit_button.button('reset');
                    }
                });
            }
        }
    });

   $("input[name='checkAll']").click(function () {
       $("input[name='exam[]']").not(this).prop('checked', this.checked);
   });

    function Popup(data)
    {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
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
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);

        return true;
    }
</script>