<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .description {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }

        .download-btn {
            display: inline-block;
            padding: 14px 28px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background: #007bff;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .download-btn:hover {
            background: #0056b3;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .container {
                width: 90%;
                margin: 20px auto;
                padding: 18px;
            }

            .title {
                font-size: 18px;
            }

            .description {
                font-size: 14px;
            }

            .download-btn {
                width: 100%;
                padding: 15px;
                font-size: 17px;
            }
        }
    </style>
</head>

<script src="<?php echo base_url('backend/dist/js/moment.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<body>

    <div class="container">
        <div class="title">Marksheet Report</div>
        <div class="description">Click below to download the student marksheet PDF.</div>

        <!-- Your download button -->

        <button type="button" class="download-btn download_pdf" id="download_selected_btn" data-action="download" data-toggle="tooltip" data-original-title="download" data-template_id=" <?php echo $template_data['id']; ?>" data-student_session_id="<?php echo $student_id; ?>" data-admission_no="<?php echo $student['admission_no'] ?>" data-student_name="<?php echo $student['firstname']; ?>" data-template_type="<?php echo $template_data['marksheet_type']; ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-download"> </i> Download Marksheet</button>


    </div>

    <div id="chartdiv" style="width: 500px; height: 200px;">
        <canvas id="examChart"></canvas>
    </div>


</body>


</html>

<script type="text/javascript">
    $(document).on('click', '.download_pdf', function() {
        $('#chartdiv').append('<canvas id="examChart" height="50" width="200"></canvas>');

        let $button_ = $(this);
        let template_id = $button_.data('template_id');
        let student_session_id = $button_.data('student_session_id');
        let admission_no = $button_.data('admission_no');
        let student_name = $button_.data('student_name');
        let action = $button_.data('action');
        let template_type = $button_.data('template_type');
        let baseurl = "<?php echo base_url(); ?>";

        $('#loading').show();

        $.ajax({
            type: 'POST',
            url: baseurl + '/cbseexam/result/getmarkssuraj',
            data: {
                'marksheet_template': template_id,
                'student_session_id[]': student_session_id,
                'type': action
            },
            success: function(response) {
                response = JSON.parse(response);

                const subjectkeys = Object.keys(response.labels);
                const totalStudents = Object.keys(response.admission_number);

                const averageMarks = subjectkeys.map(function(key) {
                    return parseInt(response.total_marks[key], 10) / totalStudents.length;
                });

                const values = subjectkeys.map(function(key) {
                    return parseInt(response.marks[key], 10);
                });

                const maxvalues = subjectkeys.map(function(key) {
                    return parseInt(response.maxmarks[key], 10);
                });

                const data = {
                    labels: Object.values(response.labels),
                    datasets: [{
                            label: "Student Marks",
                            backgroundColor: "rgba(255, 99, 132, 0.5)",
                            borderColor: "rgba(255, 99, 132, 1)",
                            borderWidth: 1,
                            data: values
                        },
                        {
                            label: "Average Marks",
                            backgroundColor: "rgba(144, 238, 144, 0.5)",
                            borderColor: "rgba(144, 238, 144, 1)",
                            borderWidth: 1,
                            data: averageMarks
                        },
                        {
                            label: "Max Marks",
                            backgroundColor: "rgba(46, 49, 49, 0.5)",
                            borderColor: "rgba(46, 49, 49, 1)",
                            borderWidth: 1,
                            data: maxvalues
                        }
                    ]
                };

                const ctx = document.getElementById('examChart').getContext('2d');

                const myChart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,

                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 10, // scaleStepWidth
                                    max: 100 // scaleOverrideMax
                                }
                            }
                        },

                        animation: {
                            onComplete: function() {
                                // After animation, convert to image and send to server
                                const canvas = document.getElementById('examChart');
                                const base64Image = canvas.toDataURL('image/png');

                                $.ajax({
                                    type: 'POST',
                                    url: baseurl + '/cbseexam/result/printmarksheet',
                                    data: {
                                        'marksheet_template': template_id,
                                        'student_session_id[]': student_session_id,
                                        'type': action,
                                        'image': base64Image
                                    },
                                    beforeSend: function() {
                                        $button_.button('loading');
                                    },
                                    xhr: function() {
                                        const xhr = new XMLHttpRequest();
                                        xhr.responseType = 'blob';
                                        return xhr;
                                    },
                                    success: function(data, textStatus, jqXHR) {
                                        const blob = new Blob([data], {
                                            type: 'application/pdf'
                                        });
                                        const link = document.createElement('a');

                                        link.href = window.URL.createObjectURL(blob);
                                        link.download = student_name + '_' + admission_no + ".pdf";

                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);

                                        $button_.button('reset');
                                    },
                                    error: function() {
                                        $("#examChart").remove();
                                        $button_.button('reset');
                                    },
                                    complete: function() {
                                        $button_.button('reset');
                                        $("#examChart").remove();
                                        $('#loading').hide();
                                    }
                                });
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#loading').hide();
                $("#examChart").remove();
                $button_.button('reset');
            }
        });
    });
</script>