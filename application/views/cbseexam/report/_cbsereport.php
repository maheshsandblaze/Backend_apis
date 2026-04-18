<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i>  <?php echo $this->lang->line('reports') ?></h3>
                <div class="btn-group pull-right">
                    <button onclick="window.history.back(); " class="btn btn-primary btn-xs"> <i class="fa fa-arrow-left"></i> Back</button> 
                </div>
            </div>
            <div class="">
                <ul class="reportlists">                 

                                  
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('cbse_exam/examsubject'); ?>"><a href="<?php echo site_url('cbseexam/report/examsubject'); ?>"><i class="fa fa-file-text-o"></i><?php echo $this->lang->line('subject_marks_report'); ?></a></li>                       
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('cbse_exam/templatewise'); ?>"><a href="<?php echo site_url('cbseexam/report/templatewise'); ?>"><i class="fa fa-file-text-o"></i><?php echo $this->lang->line('template_marks_report'); ?></a></li>                        
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('cbse_exam/getClassSectionExamResults'); ?>"><a href="<?php echo site_url('cbseexam/report/getClassSectionExamResults'); ?>"><i class="fa fa-file-text-o"></i>Consolidated Report</a></li>                        
                   
                </ul>
            </div>
        </div> 
    </div>
</div>