<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('cbseexam/report/_cbsereport'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('template_marks_report'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('cbseexam/report/getClassSectionExamResults'); ?>" method="post" class="row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                    <select id="class_id" name="class_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($classlist as $class) {
                                        ?>
                                            <option value="<?php echo $class['id']; ?>" <?php
                                                                                        if (set_value('class_id') == $class['id']) {
                                                                                            echo "selected=selected";
                                                                                        }
                                                                                        ?>><?php echo $class['class']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                    <select id="section_id" name="section_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <!-- Options for sections will be dynamically loaded here if applicable -->
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Result Table : Display only if there are results available -->
                    <?php if (!empty($students)) { ?>

                        <div class="box-body">

                            <div class="table-responsive" id="div_print">
                                <div class="btn-group pb10" role="group" aria-label="First group">
                                    <button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv('div_print')"><i class="fa fa-print"></i></button>
                                    <button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('download_excel'); ?>" onclick="exportToExcel('div_print')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                </div>
                                <h4 id="print_title">Consolidated Report</h4>
                                <table class="table table-bordered table-striped table-b vertical-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Student Name</th>
                                            <th class="text-center"><?php echo $this->lang->line('admission_no'); ?></th>
                                            <?php

                                            $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'ART & CRAFT'];


                                            foreach ($subject_array as $subject_id => $subject_name) {

                                                if (!in_array($subject_name, $excluded_subjects)) {


                                            ?>
                                                    <th colspan="<?php echo count($exam_term_assessment); ?>" class="text-center">
                                                        <?php echo $subject_name; ?>
                                                    </th>
                                            <?php }
                                            } ?>
                                            <th class="text-center">Total Marks</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <?php foreach ($subject_array as $subject_id => $subject_name) {

                                                if (!in_array($subject_name, $excluded_subjects)) {

                                            ?>
                                                    <?php foreach ($exam_term_assessment as $term_id => $term) { ?>
                                                        <th class="text-center"><?php echo $term['term_name']; ?></th>
                                                    <?php } ?>
                                            <?php }
                                            } ?>
                                            <th class="text-center"></th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // List of subjects to exclude
                                        $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'ART & CRAFT'];
                                        // $excluded_subjects = ['ICT ()', 'Moral Values ()', 'G.K ()', 'Drawing ()', 'Rhymes ()'];

                                        // echo "<pre>"; print_r($students); exit;
                                        foreach ($students as $student) {
                                            $student_total = 0;
                                            $student_max_total = 0;

                                            // echo "<pre>";
                                            // print_r($student);
                                        ?>
                                            <tr>
                                                <td><?php echo $student['firstname'] . ' ' . $student['lastname']; ?></td>
                                                <td><?php echo $student['admission_no']; ?></td>

                                                <?php foreach ($subject_array as $subject_id => $subject_name) {

                                                    if (!in_array($subject_name, $excluded_subjects)) {




                                                ?>
                                                        <?php foreach ($exam_term_assessment as $term_id => $term) { ?>
                                                            <td class="text-center">
                                                                <?php
                                                                $term_total = 0;
                                                                $term_max_total  = 0;
                                                                if (isset($student['exams'][$term_id]['subjects'][$subject_id]['assessments'])) {
                                                                    foreach ($student['exams'][$term_id]['subjects'][$subject_id]['assessments'] as $assessment) {

                                                                        // if($subject_name != "BIOLOGY SCIENCE" || $subject_name != "PHYSICAL SCIENCE")
                                                                        // {
                                                                        //     $term_total += $assessment['marks'];
                                                                        //     $term_max_total += $assessment['maximum_marks'];

                                                                        // }
                                                                        // else {

                                                                        //     $term_total = $term_total + $assessment['marks'];
                                                                        //     $term_max_total = $term_max_total + $assessment['maximum_marks'];

                                                                        // }

                                                                        $term_total += $assessment['marks'];
                                                                        $term_max_total += $assessment['maximum_marks'];
                                                                    }
                                                                }
                                                                //  echo "<pre>";
                                                                // print_r($term_total);



                                                                // if (in_array($term['term_name'], ['FA 1', 'FA 2', 'FA 3', 'FA 4'])) {
                                                                //     $term_total =  $term_total / 2;
                                                                //     $term_max_total = $term_max_total / 2;
                                                                // }

                                                                echo $term_total > 0 ? $term_total : '-';






                                                                if (!in_array($subject_name, $excluded_subjects)) {
                                                                    $student_total += $term_total;
                                                                    $student_max_total += $term_max_total;
                                                                }
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                <?php }
                                                }




                                                ?>
                                                <td class="text-center">
                                                    <strong>
                                                        <?php


                                                        echo $student_total . ' / ' . $student_max_total;
                                                        ?>
                                                    </strong>
                                                </td>

                                            </tr>
                                        <?php }

                                        // exit;



                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).on('change', '#class_id', function(e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, <?php echo set_value('section_id', 0); ?>);
    });

    function getSectionByClass(class_id, section_id) {
        if (class_id != "") {
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
                beforeSend: function() {
                    $('#section_id').addClass('dropdownloading');
                },
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id == obj.id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                },
                complete: function() {
                    $('#section_id').removeClass('dropdownloading');
                }
            });
        }
    }
</script>



<script type="text/javascript">
    function printDiv(tagid) {
        let hashid = "#" + tagid;

        var tagname = $(hashid).prop("tagName").toLowerCase();
        var attributes = "";
        var attrs = document.getElementById(tagid).attributes;
        $.each(attrs, function(i, elem) {
            attributes += " " + elem.name + " ='" + elem.value + "' ";
        })
        var divToPrint = $(hashid).html();
        var head = "<html><head>" + $("head").html() + "</head>";
        var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" + divToPrint + "</" + tagname + ">" + "</body></html>";


        var allcontent = head + "<body>" + "<" + tagname + attributes + ">" + divToPrint + "</" + tagname + ">" + "</body></html>";
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({
            "position": "absolute",
            "top": "-1000000px"
        });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();

        frameDoc.document.write(allcontent);

        frameDoc.document.close();
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);



    }


    function exportToExcel(tagid) {
        var htmls = "";
        let hashid = "#" + tagid;
        var uri = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
        var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };

        var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };

        htmls = $(hashid).html();

        var ctx = {
            worksheet: 'Worksheet',
            table: htmls
        }


        var link = document.createElement("a");
        link.download = "export.xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }
</script>