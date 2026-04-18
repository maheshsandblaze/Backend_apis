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
        <?php $this->load->view('financereports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form action="<?php echo site_url('financereports/reportdaycollection') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_from'); ?> <small class="req"> *</small></label>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from') ?>" autocomplete="off">
                                        <input type="hidden" name="search_type" value="period">
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date_to'); ?> <small class="req"> *</small></label>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to') ?>" autocomplete="off">
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
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
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('day_collection_report'); ?></h3>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($fees_data)) { ?>
                                    <div class="table-responsive">
                               
                                        <div class="download_label"><?php echo $this->lang->line('day_collection_report') . " <br>";
                                        $this->customlib->get_postmessage();
                                        
                                        ?></div>
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <tr>
                                                    <th style="width:10%;">S.No</th>
                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                    <th><?php echo $this->lang->line('student_name'); ?></th>
                                                    <th><?php echo $this->lang->line('class'); ?></th>
                                                    <th style="width: 10%;">Receipt No</th>
                                                    <th style=""><?php echo $this->lang->line('reference_no'); ?></th>


                                                    <th style="max-width: 10px;"><?php echo $this->lang->line('mode'); ?></th>
                                                    <th><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                                    <th><?php echo $this->lang->line('payment_date'); ?></th>
                                                    <th style="width: 15%;"><?php echo $this->lang->line('collected_by'); ?></th>
                                                    <th style="text-align: left;"><?php echo $this->lang->line('fee_type'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $grdamountLabel = array();
                                                foreach ($fees_data as $mode => $fees) {
                                                    $amountLabel = array();
                                                ?>
                                                    <tr class="modeheading">
                                                        <td style="font-weight: bold;text-align:left;">
                                                            <?php echo $mode === 'Cash' || $mode === 'DD' || $mode === 'Cheque' ? $mode : $this->lang->line($mode); ?>
                                                        </td>
                                                        <td></td>
                                                        <td></td>


                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                    $count = 1;

                                                    $academic_session = $this->customlib->getCurrentSession();

                                                    $asession = $this->customlib->getAcademicSession($academic_session['session']);
                    
                    
                                                    // echo "<pre>";
                                                    // print_r($asession);exit;
                                                    $end_year = $asession['end_year'];
                                                    $start_year = $asession['start_year'];
                    
                                                    
                                                    $receipt_prefix = $end_year;
                                                    foreach ($fees as $fee) {
                                                        $amountLabel[] = number_format($fee->amount, 2, '.', '');
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $count; ?></td>
                                                            <td><?php echo $fee->admission_no; ?></td>
                                                            <td><?php echo $this->customlib->getFullName($fee->firstname, $fee->middlename, $fee->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></td>
                                                            <td><?php echo $fee->class . " (" . $fee->section . ")"; ?></td>
                                                            <td><?php echo $receipt_prefix . sprintf('%05s', $fee->id); ?></td>
                                                            <td><?php  echo $fee->reference_no;?></td>
                                                            <td style="max-width: 10px;"><?php echo $fee->mode === 'Cash' ? $fee->mode : $this->lang->line($fee->mode); ?></td>
                                                            <td class="text text-right"><?php echo $currency_symbol . $fee->amount; ?></td>
                                                            <td><?php echo date('d/m/Y', strtotime($fee->created_at)); ?></td>
                                                            <td><?php echo $fee->collected_by; ?></td>
                                                            <td style="text-align: left;"><?php echo $fee->fee_types; ?></td>
                                                        </tr>
                                                    <?php $count++;
                                                    } ?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>

                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="font-weight:bold;width: 10%;"><?php echo $this->lang->line('sub_total'); ?></td>
                                                        <td class="text text-right" style="font-weight:bold"><?php echo amountFormat(array_sum($amountLabel)); ?></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                <?php $grdamountLabel[] = array_sum($amountLabel);
                                                } ?>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>

                                                    <td></td>
                                                    <td style="font-weight:bold;max-width:10px;"><?php echo $this->lang->line('grand_total'); ?></td>
                                                    <td class="text text-right" style="font-weight:bold"><?php echo amountFormat(array_sum($grdamountLabel)); ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php } ?>
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
    // $(document).ready(function() {
    //     $('#collectionModal').modal({
    //         backdrop: 'static',
    //         keyboard: false,
    //         show: false
    //     });
    // });

    // $(document).on('click', '.fee_collection', function() {
    //     var $this = $(this);
    //     var date = $this.data('date');

    //     $.ajax({
    //         type: 'POST',
    //         url: baseurl + "financereports/feeCollectionStudentDeposit",
    //         data: {
    //             'date': date,
    //             'fees_id': $this.data('depositeId')
    //         },
    //         dataType: 'JSON',
    //         beforeSend: function() {
    //             $this.button('loading');
    //         },
    //         success: function(data) {
    //             $('#collectionModal .modal-body').html(data.page);
    //             $('#collectionModal').modal('show');
    //             $this.button('reset');
    //         },
    //         error: function(xhr) { // if error occured
    //             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
    //             $this.button('reset');
    //         },
    //         complete: function() {
    //             $this.button('reset');
    //         }
    //     });
    // });
</script>