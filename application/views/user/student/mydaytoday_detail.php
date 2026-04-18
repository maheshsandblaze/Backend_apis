<style>
    .custom-text    {
        text-decoration: none;
        background-color: #cccccc1c;
        margin: 0;
        padding: 10px;
    }
</style>
    <?php
    $admin = $this->customlib->getLoggedInUserData();
    ?>
    
    
<table class="table table-hover">
    <tbody>
        <!-- I Was Section -->
        <tr>
            <th>I Was</th>
            <td>
                <p>
                    <?php
                    if (!empty($result['iwas'])) {    
                        $iwas = $result['iwas'];
                        $iwas_array = array(
                            1   => 'Happy',
                            2   => 'Chatty',
                            3   => 'Curious',
                            4   => 'Quiet',
                            5   => 'Sleepy',
                            6   => 'Busy',
                            7   => 'Grumpy'    
                        );
                        echo $iwas_array[$iwas];
                    } else {
                        echo "-";
                    }
                    ?>
                </p>
            </td>
        </tr>

        <!-- I Drank Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Drank</h4></th>
        </tr>
        <tr>
            <th>When</th>
            <td><p>
                <?php
                if (!empty($result['drank_when1'])) {
                    echo $result['drank_when1'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
            <th>How Much</th>
            <td><p>
                <?php
                if (!empty($result['drank_howmuch1'])) {
                    echo $result['drank_howmuch1']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>When</th>
            <td><p>
                <?php
                if (!empty($result['drank_when2'])) {
                    echo $result['drank_when2'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
            <th>How Much</th>
            <td><p>
                <?php
                if (!empty($result['drank_howmuch2'])) {
                    echo $result['drank_howmuch2']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>When</th>
            <td><p>
                <?php
                if (!empty($result['drank_when3'])) {
                    echo $result['drank_when3'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
            <th>How Much</th>
            <td><p>
                <?php
                if (!empty($result['drank_howmuch3'])) {
                    echo $result['drank_howmuch3']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>When</th>
            <td><p>
                <?php
                if (!empty($result['drank_when4'])) {
                    echo $result['drank_when4'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
            <th>How Much</th>
            <td><p>
                <?php
                if (!empty($result['drank_howmuch4'])) {
                    echo $result['drank_howmuch4']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>

        <!-- I Slept Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Slept</h4></th>
        </tr>
        <tr>
            <th>When</th>
            <td><p>
                <?php
                if (!empty($result['slept_when'])) {
                    echo $result['slept_when']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
            <th>How Long</th>
            <td><p>
                <?php
                if (!empty($result['slept_howlong'])) {
                    echo $result['slept_howlong']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>

        <!-- I Ate Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Ate</h4></th>
        </tr>
        <tr>
            <th>My Snack</th>
            <td colspan="3">
                <p>
                    <?php 
                    if (!empty($result['my_snack'])) {
                        $my_snack = $result['my_snack'];
                        $my_snack_array = array(1 => 'Completed', 2 => 'Almost');
                        echo $my_snack_array[$my_snack];
                    } else {
                        echo "-";
                    }
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th>My Lunch</th>
            <td colspan="3">
                <p>
                    <?php 
                    if (!empty($result['my_lunch'])) {
                        $my_lunch = $result['my_lunch'];
                        $my_lunch_array = array(1 => 'Completed', 2 => 'Almost');
                        echo $my_lunch_array[$my_lunch];
                    } else {
                        echo "-";
                    }
                    ?>
                </p>
            </td>
        </tr>

        <!-- I Had Fun Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Had Fun</h4></th>
        </tr>
        <tr>
            <th>We Time</th>
            <td colspan="3"><p>
                <?php 
                if (!empty($result['we_time'])) {
                    echo $result['we_time']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Gross Motor</th>
            <td colspan="3"><p>
                <?php 
                if (!empty($result['gross_motor'])) {
                    echo $result['gross_motor']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Fine Motor</th>
            <td colspan="3"><p>
                <?php 
                if (!empty($result['fine_motor'])) {
                    echo $result['fine_motor']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Free Play</th>
            <td colspan="3"><p>
                <?php 
                if (!empty($result['free_play'])) {
                    echo $result['free_play']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Study Time</th>
            <td colspan="3"><p>
                <?php 
                if (!empty($result['study_time'])) {
                    echo $result['study_time']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>

        <!-- I Went Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Went</h4></th>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <p>
                                                            <?php 
                                                            if (!empty($result['poo_pee1'])) {
                                                                $poo_pee1 = $result['poo_pee1'];
                                                                $poo_pee1_array = array(
                                                                    1   =>  'Poo',
                                                                    2   =>  'Pee',
                                                                    3   =>  'Both'
                                                                );
                                                                echo $poo_pee1_array[$poo_pee1];
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>
                                                        </p>
            </td>
            <th>When</th>
            <td><p>
                <?php 
                if (!empty($result['poo_pee_text1'])) {
                    echo $result['poo_pee_text1'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <p>
                                                             <?php 
                                                            if (!empty($result['poo_pee2'])) {
                                                                $poo_pee2 = $result['poo_pee2'];
                                                                $poo_pee2_array = array(
                                                                    1   =>  'Poo',
                                                                    2   =>  'Pee',
                                                                    3   =>  'Both'
                                                                );
                                                                echo $poo_pee2_array[$poo_pee2];
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>
                                                        </p>
            </td>
            <th>When</th>
            <td><p>
                <?php 
                if (!empty($result['poo_pee_text2'])) {
                    echo $result['poo_pee_text2'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <p>
                                                            <?php 
                                                            if (!empty($result['poo_pee3'])) {
                                                                $poo_pee3 = $result['poo_pee3'];
                                                                $poo_pee3_array = array(
                                                                    1   =>  'Poo',
                                                                    2   =>  'Pee',
                                                                    3   =>  'Both'
                                                                );
                                                                echo $poo_pee3_array[$poo_pee3];
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>
                                                        </p>
            </td>
            <th>When</th>
            <td><p>
                <?php 
                if (!empty($result['poo_pee_text3'])) {
                    echo $result['poo_pee_text3'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <p>
                                                            <?php 
                                                            if (!empty($result['poo_pee4'])) {
                                                                $poo_pee4 = $result['poo_pee4'];
                                                                $poo_pee4_array = array(
                                                                    1   =>  'Poo',
                                                                    2   =>  'Pee',
                                                                    3   =>  'Both'
                                                                );
                                                                echo $poo_pee4_array[$poo_pee4];
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>
                                                        </p>
            </td>
            <th>When</th>
            <td><p>
                <?php 
                if (!empty($result['poo_pee_text4'])) {
                    echo $result['poo_pee_text4'];
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>

        <!-- I Need Section -->
        <tr>
            <th colspan="4" class="text-center bg-light"><h4 class="custom-text">I Need</h4></th>
        </tr>
        <tr>
            <th>Item</th>
            <td colspan="3">
                <p>
                    <?php 
                    if (!empty($result['need'])) {
                        $need = $result['need'];
                        $need_array = array(1 => 'Diaper', 2 => 'Clothes', 3 => 'Wipes', 4 => 'Baby Cream');
                        echo $need_array[$need];
                    } else {
                        echo "-";
                    }
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th>Note</th>
            <td colspan="3"><p>
                <?php
                if (!empty($result['note'])) {
                    echo $result['note']; 
                } else {
                    echo "-";
                }
                ?>
                </p></td>
        </tr>
    </tbody>
</table>



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