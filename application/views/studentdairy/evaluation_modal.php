<style type="text/css">
    .list-group-item.active,
    .list-group-item.active:focus,
    .list-group-item.active:hover {
        z-index: 2;
        color: #444;
        background-color: #fff;
        border-color: #ddd;
    }

    a:link {
        color: black;
        background-color: transparent;
    }
</style>

<div class="row row-eq h-85vh h-100vh-m">
    <?php
    $admin = $this->customlib->getLoggedInUserData();
    ?>
    <div class="col-lg-9 col-md-9 col-sm-9 paddlr">
        <!-- general form elements -->
        <form id="evaluation_data" method="post">

            <div class="row">

                <div class="scroll-area">
                    <div class="test">
                        <div class="">

                            <label><span><?php echo $this->lang->line('description'); ?></span>: <br /><?php echo $result['description']; ?></label>


                        </div>
                    </div>
                </div>
                <div class="sticky-footer">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div class="form-group">
                                    <?php
                                    $evl_date = $this->customlib->dateformat(date('Y-m-d'));
                                    if (!IsNullOrEmptyString($result['date'])) {
                                        $evl_date = $this->customlib->dateformat($result['date']);
                                    }
                                    ?>
                                    <!-- <input type="text" id="date" name="date" class="form-control modalddate97 date" value="<?php echo $evl_date; ?>" readonly=""> -->
                                    <input type="hidden" name="homework_id" value="<?php echo $result["id"] ?>">
                                    <?php
                                    if (!empty($report)) {
                                        foreach ($report as $key => $report_value) {
                                    ?>
                                            <input type="hidden" name="evalid[]" value="<?php echo $report_value["evalid"] ?>">
                                    <?php
                                        }
                                    }
                                    ?>
                                    <span class="text-danger" id="date_error"></span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <?php if ($this->rbac->hasPrivilege('homework_evaluation', 'can_add')) { ?>
                                    <div class="form-group">
                                        <!-- <button type="submit" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button> -->
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row-->
        </form>
    </div><!--/.col (left) -->
    <div class="col-lg-3 col-md-3 col-sm-3 col-eq">
        <div class="taskside scroll-area">
            <h4 class="mt0"><?php echo $this->lang->line('summary'); ?></h4>
            <hr class="taskseparator mt12" />
            <div class="task-info task-single-inline-wrap task-info-start-date">
                <h5><i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
                    <span><?php echo $this->lang->line('homework_date'); ?></span>:<?php echo ($this->customlib->dateformat($result['date'])); ?>
                </h5>
            </div>
            <div class="task-info task-single-inline-wrap task-info-start-date">

            </div>


            <div class="task-info task-single-inline-wrap ptt10">
                <label><span><?php echo $this->lang->line('created_by'); ?></span>: <?php echo $result['assigned_by']; ?></label>
                <label><span><?php echo $this->lang->line('class') ?></span>: <?php echo $result['class']; ?></label>
                <label><span><?php echo $this->lang->line('section') ?></span>: <?php echo $result['section']; ?></label>
                <?php 
                 if (!empty($result["document"])) { ?>
                    <label><span><?php echo "Documents"; ?></span>:<br> 
                    <?php echo $this->media_storage->fileview($result["document"]) ?>
                    <a data-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>" href="<?php echo site_url("studentdairy/download/" . $result["id"]) ?>"><i class="fa fa-download"></i></a></label>
                    <?php
                } ?>



            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        $('body').on('click', '.list-group .list-group-item', function() {
            $(this).removeClass('active');
            $(this).toggleClass('active');
        });

        $('.list-arrows a').click(function() {
            var $button = $(this),
                actives = '';
            if ($button.hasClass('move-left')) {
                actives = $('#hlist option.active');
                actives.clone().appendTo('#slist');
                actives.remove();
            } else if ($button.hasClass('move-right')) {

                actives = $('#slist option.active');
                actives.clone().appendTo('#hlist');
                actives.remove();

            }
        });

        $('.dual-list .selector').click(function() {

            var $checkBox = $(this);
            if (!$checkBox.hasClass('selected')) {
                $checkBox.addClass('selected').closest('.test').find('select option:not(.active)').addClass('list-group-item active');

                $checkBox.children('i').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
            } else {
                $checkBox.removeClass('selected').closest('.test').find('select option.active').removeClass('active');

                $checkBox.children('i').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
            }
        });

        $('[name="SearchDualList"]').keyup(function(e) {
            var code = e.keyCode || e.which;
            if (code == '9')
                return;
            if (code == '27')
                $(this).val(null);
            var $rows = $(this).closest('.dual-list').find('.list-group option');
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
    });
</script>
<script>
    function listbox_moveacross(sourceID, destID) {
        var src = document.getElementById(sourceID);
        var dest = document.getElementById(destID);

        for (var count = 0; count < src.options.length; count++) {

            if (src.options[count].selected == true) {
                var option = src.options[count];

                var newOption = document.createElement("option");
                newOption.value = option.value;
                newOption.text = option.text;
                newOption.selected = true;
                try {
                    dest.add(newOption, null); //Standard
                    src.remove(count, null);
                } catch (error) {
                    dest.add(newOption); // IE only
                    src.remove(count);
                }
                count--;
            }
        }
    }
</script>