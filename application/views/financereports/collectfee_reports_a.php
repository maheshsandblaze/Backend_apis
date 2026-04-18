<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
 
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php //echo $this->lang->line('fees_collection'); ?> <small> <?php //echo $this->lang->line('filter_by_name1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header ">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form action="<?php echo site_url('financereports/collectfee_report') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                      <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($section_list as $value) {
                                                ?>
                                                <option  <?php
                                                if ($value['section_id'] == $section_id) {
                                                    echo "selected";
                                                }
                                                ?> value="<?php echo $value['section_id']; ?>"><?php echo $value['section']; ?></option>
                                                    <?php
                                                }
                                                ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('search_type'); ?></label>
                                        <select  id="search_type" name="search_type" class="form-control" >
                                            <?php 
                                            foreach ($payment_type as $payment_key => $payment_value) {
                                            ?>
                                             <option value="<?php echo $payment_key; ?>" <?php echo set_select('search_type', $payment_key, set_value('search_type')); ?>><?php echo $payment_value; ?></option>
                                            <?php
                                            }
                                             ?>                                        
                                       </select>
                                        <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-2 col-lg-2 col-md-2">
                               <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_type'); ?></label>

                                            <select  id="feetype_id" name="feetype_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
foreach ($feetypeList as $feetype) {
    ?>
                                                    <option value="<?php echo $feetype['id'] ?>"<?php
if (set_value('feetype_id') == $feetype['id']) {
        echo "selected =selected";
    }
    ?>><?php echo $feetype['type'] ?></option>

                                                    <?php
$count++;
}
?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                        </div>
                            </div>
                            <div class="col-sm-2 col-lg-2 col-md-2">
                                <div class="form-group">
                                    <label>Assigned By</label>
                                    <select class="form-control"  name="assign_by" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php
foreach ($collect_by as $key => $value) {
    ?>
                                            <option value="<?php echo $key ?>" <?php
if ((isset($received_by)) && ($received_by == $key)) {
        echo "selected";
    }
    ?> ><?php echo $value ?></option>
                                                <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('assign_by'); ?></span>
                                </div>
                            </div>            

                            </div>

                                            

                        <div class="box-footer">

                            <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>   </div>
                    </form>


                    <div class="row">

                        <?php
                        if (isset($student_due_fee) && !empty($student_due_fee)) {
                            ?>

                            <div class="" id="transfee">
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('balance_fees_report'); ?></h3>
                                    <div class="pull-right">
                                        <button type="submit" name="search" value="saveattendence" class="btn btn-primary btn-sm pull-right checkbox-toggle"> Assign Students </button>
                                    </div>
                                </div>  
                                                            
                                <div class="box-body table-responsive">
                                    <div class="download_label"><?php
                            echo $this->lang->line('balance_fees_report') . "<br>";
                            $this->customlib->get_postmessage();
                            ?></div> 
                                    <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a> <button class="btn btn-default btn-xs pull-right" id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </button>  
                                    <table class="table table-striped table-hover" id="headerTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="checkbox mb0 mt0">
                                                        <label class="labelbold"><input type="checkbox" id="select_all"/> <?php echo $this->lang->line('all'); ?></label>
                                                    </div>
                                                </th>
                                                <th class="text text-left"><?php echo $this->lang->line('student_name'); ?></th>
                                                <th class="text text-left"><?php echo $this->lang->line('class'); ?></th>

                                                <th class="text text-left"><?php echo $this->lang->line('admission_no'); ?></th>
                                                <?php if ($sch_setting->roll_no) { ?>
                                                    <th class="text text-left"><?php echo $this->lang->line('roll_number'); ?></th>
                                                <?php } if ($sch_setting->father_name) { ?>
                                                    <th class="text text-left"><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php } ?>
                                                                  

                                                
                                                
                                            </tr>
                                        </thead>  
                                        <tbody> 
                                            <?php
                                            if (!empty($resultarray)) {
                                                        $totalfeelabel = 0;
                                                        $depositfeelabel = 0;
                                                        $discountlabel = 0;
                                                        $finelabel = 0;
                                                        $balancelabel = 0;                                       
                                                foreach ($resultarray as $key => $section) {                                                   
                                                     
                                                        foreach ($section['result'] as $students) {                                                            
                                                            $totalfeelabel += $students->totalfee;
                                                            $depositfeelabel += $students->deposit;
                                                            $discountlabel += $students->discount;
                                                            $finelabel += $students->fine;
                                                            $balancelabel += $students->balance;
                                                                    ?>                                            
                                                      <tr>

                                                            <td>     
                                                                <input class="checkbox" type="checkbox" name="student_session_id[]"  value="<?php echo $student['student_session_id']; ?>" <?php echo $sel; ?>/>
                                                            </td>
                                                            <td><?php echo $students->name;?></td>
                                                            <td><?php echo $students->class." (".$students->section.")";?></td>
                                                            <td><?php echo $students->admission_no;?></td>
                                                            <td><?php echo $students->roll_no;?></td>
                                                            <td><?php echo $students->father_name;?></td>

                                                            

                                                               

                                                            </tr>
                                                                <?php
                                                        }
                                                            ?>
                                                               
                                                        <?php                                
                                                          } ?>
                                            </tbody> 
                                        </table>
                                    </div>                            
                                </div>                 
                            </div>

                            <?php
                        }
                    } 
?>



                </div>
            </div>
    </section>
</div>

<div class="modal fade" id="follow_up" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Follow Up Fee Enquiry</h4>
            </div>
            <div class="modal-body pt0 pb0 pr-xl-1" id="getdetails_follow_up">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //select all checkboxes
    $("#select_all").change(function () {  //"select all" change 
        $(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if (false == $(this).prop("checked")) { //if this item is unchecked
            $("#select_all").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#select_all").prop('checked', true);
        }
    });
</script>

<script type="text/javascript">
    function removeElement() {
        document.getElementById("imgbox1").style.display = "block";
    }
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').html(div_data);
                }
            });
        }
    }
    $(document).ready(function () {
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });

                    $('#section_id').html(div_data);
                }
            });
        });
        $(document).on('change', '#section_id', function (e) {
            getStudentsByClassAndSection();
        });
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
    });
    function getStudentsByClassAndSection() {
        $('#student_id').html("");
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {'class_id': class_id, 'section_id': section_id},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value=" + obj.id + ">" + obj.firstname + " " + obj.lastname + "</option>";
                });
                $('#student_id').append(div_data);
            }
        });
    }

    $(document).ready(function () {
        $("ul.type_dropdown input[type=checkbox]").each(function () {
            $(this).change(function () {
                var line = "";
                $("ul.type_dropdown input[type=checkbox]").each(function () {
                    if ($(this).is(":checked")) {
                        line += $("+ span", this).text() + ";";
                    }
                });
                $("input.form-control").val(line);
            });
        });
    });
    $(document).ready(function () {
        $.extend($.fn.dataTable.defaults, {
            ordering: false,
            paging: false,
            bSort: false,
            info: false
        });
    });
</script>
<script>

    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";

    function printDiv() {
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        var divElements = document.getElementById('transfee').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
                "<html><head><title></title></head><body>" +
                divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;

        location.reload(true);
    }

    function fnExcelReport()
    {
        var tab_text = "<table border='2px'><tr >";
        var textRange;
        var j = 0;
        tab = document.getElementById('headerTable'); // id of table

        for (j = 0; j < tab.rows.length; j++)
        {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            //tab_text=tab_text+"</tr>";
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
        } else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

        return (sa);
    }


    function update() {
        window.location.reload(true);
    }


    function follow_up(student_id, agent_id) {

        console.log('suraj test ');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/fee_enquiry/' + student_id + '/' + agent_id,
            dataType: 'JSON',
            success: function (data) {
                console.log(data.enquiry_id)
                $('#getdetails_follow_up').html(data.page);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/fee_followup_list/' + data.enquiry_id,
                    success: function (data) {
                        console.log(data)
                        $('#timeline').html(data);
                    },
                    error: function () {
                        alert("<?php echo $this->lang->line('fail'); ?>");
                    }
                });
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }
</script>