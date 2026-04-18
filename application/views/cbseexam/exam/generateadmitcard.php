<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>

<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    
                    
                        
                        <?php

if (isset($studentList)) {
if(!empty($studentList)){    
  
              ?>
    
     <?php

?>
     <!--<form method="post" action="<?php echo base_url('cbseexam/exam/examrankgenerate') ?>" id="rankgenerate">-->
     <form method="post" action="<?php echo base_url('cbseexam/exam/printadmitcard') ?>" id="printCard">
         <input type="hidden" name="exam_id" value="<?php echo set_value('exam_id',$exam_id); ?>">
        <div class="box-header ptbnull">
            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
            <button  class="btn btn-info btn-sm printSelected pull-right" type="submit" name="generate" title="<?php echo $this->lang->line('generate_multiple_admit_card'); ?>"><?php echo $this->lang->line('generate'); ?></button>  
                                
        </div>
        <div class="box-body">
            <div class="download_label"><?php echo $this->lang->line('print_admit_card'); ?></div>
   <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                     <thead>
                         <tr>
                             <th><input type="checkbox" id="select_all" /></th>   
                             <th><?php echo $this->lang->line('admission_no'); ?></th>
                             <th><?php echo $this->lang->line('student_name'); ?></th>
                             <th><?php echo $this->lang->line('class'); ?></th>     
                             <th><?php echo $this->lang->line('father_name'); ?></th>
                             <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                             <th><?php echo $this->lang->line('gender'); ?></th>                                             
                             <th class=""><?php echo $this->lang->line('mobile_no'); ?></th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                         if (empty($studentList)) {
                             ?>

                             <?php
                         } else {
                             $count = 1;
                             foreach ($studentList as $student_key => $student_value) {
                             
                                 ?>
                                 <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="checkbox center-block" name="student_session_id[]" value="<?php echo $student_value->student_session_id?>"/>
                                    </td>
                                     <td>  
                               
                                         <?php echo $student_value->admission_no; ?></td>
                                     <td>
<a href="<?php echo base_url(); ?>student/view/<?php echo $student_value->id; ?>"><?php echo $this->customlib->getFullName($student_value->firstname,$student_value->middlename,$student_value->lastname,$sch_setting->middlename,$sch_setting->lastname); ?>
                                         </a>
                                     </td>
                                     <td><?php echo $student_value->class."(".$student_value->section.")"; ?></td>
                                     <td><?php echo $student_value->father_name; ?></td>
                                     <td><?php 
                                         if (!empty($student_value->dob) && $student_value->dob != '0000-00-00') {
                                         echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student_value->dob)); }?></td>
                                     <td><?php echo $this->lang->line(strtolower($student_value->gender)); ?></td>                  
                                     <td><?php echo $student_value->mobileno; ?></td>
                                    
                                 </tr>
                                 <?php
                                 $count++;
                             }
                         }
                         ?>
                     </tbody>
                 </table>
         <!--   <div class="col-sm-12">-->
         <!--       <div class="form-group">-->
         <!--           <button type="submit" name="search"  class="btn btn-primary pull-right btn-sm checkbox-toggle" autocomplete="off"><i class="fa fa-search"></i> <?php echo $this->lang->line('generate_rank'); ?></button>-->
         <!--       </div>-->
         <!--</div>     -->
         </div>
       
     </form>
 
 <?php

}else{
 ?>
 <div class="box-body row">
     <div class="col-md-12">                            
<div class="alert alert-danger">
<?php echo $this->lang->line('no_record_found');?>
</div>
     </div>
 </div>
 <?php
}
}
?>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>


<script type="text/javascript">
 
$(document).on('submit', 'form#printCard', function (e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serializeArray();
        var list_selected =  $('form#printCard input[name="student_session_id[]"]:checked').length;
        console.log(list_selected);
      if(list_selected > 0){

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: formdata, // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                subsubmit_button.button('loading');
            },
            success: function (response)
            {
                Popup(response.page);

            },
            error: function (xhr) { // if error occured

                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                subsubmit_button.button('reset');
            },
            complete: function () {
                subsubmit_button.button('reset');
            }
        });
    }else{
         confirm("<?php echo $this->lang->line('please_select_student'); ?>");
    }

    });

    $(document).on('click', '#select_all', function () {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
    });
</script>

<script type="text/javascript">

    var base_url = '<?php echo base_url() ?>';
    function Popup(data)
    {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";

        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
//Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
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