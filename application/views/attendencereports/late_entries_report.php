<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php //echo $this->lang->line('fees_collection'); 
                                        ?> <small> <?php //echo $this->lang->line('filter_by_name1'); 
                                                    ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('attendencereports/_attendance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form action="<?php echo site_url('attendencereports/late_entries_report') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_from'); ?> <small class="req"> *</small></label>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from') ?>" autocomplete="off">
                                        <input type="hidden" name="search_type" value="period">
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_to'); ?> <small class="req"> *</small></label>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to') ?>" autocomplete="off">
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
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
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('student'); ?></label>
                                        <select id="student_id" name="student_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('student_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                        </div>
                    </form>
                    <div class="row">

                        <div id="transfee">
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('late_entries_report'); ?></h3>
                            </div>
                            <div class="box-body hide-mobile">
                                <?php if (!empty($late_entries)) { ?>
                                    <div class="table-responsive">

                                        <div class="download_label"><?php echo $this->lang->line('late_entries_report') . " <br>";
                                                                    $this->customlib->get_postmessage();

                                                                    ?></div>
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <tr>
                                                    <th>S.no</th>
                                                    <th><?php echo $this->lang->line('name'); ?></th>

                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                    <th><?php echo $this->lang->line('class'); ?></th>
                                                    <th><?php echo $this->lang->line('section'); ?></th>



                                                    <th><?php echo $this->lang->line('date'); ?></th>
                                                    <th>Time</th>

                                                    <th><?php echo $this->lang->line('roll_number'); ?></th>
                                                    <!-- <th width="30%"><?php echo $this->lang->line('attendance'); ?></th> -->
                                                    <!-- <th class="noteinput"><?php echo $this->lang->line('note'); ?></th> -->
                                                </tr>
                                            </thead>
                                            <tbody>



                                                <?php $count = 1;
                                                foreach ($late_entries as $val) {

                                                ?>

                                                    <tr>

                                                        <td><?php echo $count; ?></td>
                                                        <td><?php echo $val['firstname'] . " " . $val['lastname'] ?></td>
                                                        <td><?php echo $val['admission_no']; ?></td>
                                                        <td><?php echo $val['class']; ?></td>
                                                        <td><?php echo $val['section']; ?></td>


                                                        <td><?php echo date('Y-m-d', strtotime($val['date'])); ?></td>
                                                        <td><?php echo date('H:i:s', strtotime($val['date'])); ?></td>

                                                        <td><?php echo $val['roll_no'] ?></td>

                                                    </tr>


                                                <?php $count++;
                                                } ?>



                                            </tbody>

                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                            
                            <div class="box-body hide-desktop">
                            
                            
                                <div class="col-md-12">
                                    <?php 
                                        if (!empty($late_entries)) {
                                                $count = 1;
                                                foreach ($late_entries as $val) {

                                                ?>
                                    <div class="bgtgray">
                                        <div class="col-sm-3 col-lg-2 col-md-3">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('name'); ?> : <span class="description-text"><?php echo $val['firstname'] . " " . $val['lastname']; ?></span></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 pull ">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('admission_no'); ?> : <span class="description-text"><?php echo $val['admission_no']; ?></span></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 pull ">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('class'); ?> : <span class="description-text"><?php echo $val['class']; ?></span></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-lg-4 col-md-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('section'); ?> :<span class="description-text"><?php echo $val['section']; ?></span></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-lg-2 col-md-2 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $this->lang->line('date'); ?> : <span class="description-text"><?php echo date('Y-m-d', strtotime($val['date'])); ?></span></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-lg-2 col-md-2 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">Time : <span class="description-text"><?php echo date('H:i:s', strtotime($val['date'])); ?></span></h5>
                                            </div>
                                        </div>
    
                                    </div>
                            <?php
                            $count++;
    }
                                        } else {
?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php } ?>
                                </div>
                            
                            </div>
                            
                        </div>

                    </div>

                </div>
            </div>
    </section>
</div>

<div id="collectionModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('collection_list'); ?> </h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id !== "" && section_id !== "") {
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
                        if (section_id === obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }

    $(document).ready(function() {
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function() {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

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
        $(document).on('change', '#section_id', function(e) {
            getStudentsByClassAndSection();
        });
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        if (class_id != "" || section_id != "") {
            postbackStudentsByClassAndSection(class_id, section_id);
        }
    });

    function getStudentsByClassAndSection() {

        $('#student_id').html("");
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var student_id = '<?php echo set_value('student_id') ?>';
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {
                'class_id': class_id,
                'section_id': section_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(i, obj) {
                    var sel = "";
                    if (section_id == obj.section_id) {
                        sel = "selected=selected";
                    }

                    if (obj.admission_no == "") {
                        div_data += "<option value=" + obj.id + ">" + obj.full_name + " </option>";
                    } else {
                        div_data += "<option value=" + obj.id + ">" + obj.full_name + " (" + obj.admission_no + ") </option>";
                    }

                });
                $('#student_id').append(div_data);
            }
        });
    }

    function postbackStudentsByClassAndSection(class_id, section_id) {
        $('#student_id').html("");
        var student_id = '<?php echo set_value('student_id') ?>';
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {
                'class_id': class_id,
                'section_id': section_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(i, obj) {
                    var sel = "";
                    if (student_id == obj.id) {
                        sel = "selected=selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.full_name + " (" + obj.admission_no + ") </option>";
                });
                $('#student_id').append(div_data);
            }
        });
    }
</script>