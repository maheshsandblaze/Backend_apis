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
        }
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo base_url('backend/dist/js/jquery.min.js'); ?>"></script>

<body>

    <div class="container">
        <div class="title">Marksheet Report</div>
        <p>Your marksheet is being prepared. Download will start automatically.</p>

        <!-- Hidden button (used only for data attributes) -->
        <button
            id="download_selected_btn"
            class="download-btn download_pdf"
            style="display:none"
            data-action="download"
            data-template_id="<?php echo $template_data['id']; ?>"
            data-student_session_id="<?php echo $student_id; ?>"
            data-admission_no="<?php echo $student['admission_no']; ?>"
            data-student_name="<?php echo $student['firstname']; ?>">
            Download
        </button>
    </div>

    <script>
        /* ================= GLOBAL LOCKS ================= */
        let pdfGenerating = false;
        let autoDownloadDone = false;

        /* ================= MAIN FUNCTION ================= */
        function generateMarksheetPdf($button_) {

            if (pdfGenerating) return;
            pdfGenerating = true;

            const canvas = document.createElement('canvas');
            canvas.width = 1200;
            canvas.height = 400;
            canvas.style.position = 'absolute';
            canvas.style.top = '-10000px';
            document.body.appendChild(canvas);

            const ctx = canvas.getContext('2d');

            const baseurl = "<?php echo base_url(); ?>";
            const template_id = $button_.data('template_id');
            const student_session_id = $button_.data('student_session_id');
            const admission_no = $button_.data('admission_no');
            const student_name = $button_.data('student_name');
            const action = $button_.data('action');

        $.ajax({
            type: 'POST',
                url: baseurl + 'welcome/getmarkssuraj',
            data: {
                    marksheet_template: template_id,
                'student_session_id[]': student_session_id,
                    type: action
            },
                success: function(res) {

                    res = JSON.parse(res);

                    const subjects = Object.values(res.labels);
                    const marks = Object.keys(res.marks).map(k => parseInt(res.marks[k]));
                    const maxMarks = Object.keys(res.maxmarks).map(k => parseInt(res.maxmarks[k]));
                    const avgMarks = Object.keys(res.total_marks).map(
                        k => parseInt(res.total_marks[k]) / Object.keys(res.admission_number).length
                    );

                    const maxY = Math.ceil(Math.max(...maxMarks) / 10) * 10;

                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: subjects,
                    datasets: [{
                                    data: marks,
                                    backgroundColor: '#ff6384'
                                },
                                {
                                    data: avgMarks,
                                    backgroundColor: '#90ee90'
                        },
                        {
                                    data: maxMarks,
                                    backgroundColor: '#2e3131'
                        }
                    ]
                        },
                    options: {
                            responsive: false,
                            animation: false,
                            devicePixelRatio: 2,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                        scales: {
                            y: {
                                beginAtZero: true,
                                    max: maxY
                                }
                            }
                        }
                    });

                    setTimeout(function() {

                        const img = canvas.toDataURL('image/png', 1);

                                $.ajax({
                                    type: 'POST',
                            url: baseurl + 'welcome/printexammarksheet',
                                    data: {
                                marksheet_template: template_id,
                                        'student_session_id[]': student_session_id,
                                type: action,
                                image: img
                                    },
                                    xhr: function() {
                                let xhr = new XMLHttpRequest();
                                        xhr.responseType = 'blob';
                                        return xhr;
                                    },
                            success: function(data) {

                                        const blob = new Blob([data], {
                                            type: 'application/pdf'
                                        });
                                const url = window.URL.createObjectURL(blob);

                                const a = document.createElement('a');
                                a.href = url;
                                a.download = student_name + '_' + admission_no + '.pdf';
                                a.target = '_self';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);

                                window.URL.revokeObjectURL(url);
                                    },
                                    complete: function() {
                                chart.destroy();
                                canvas.remove();
                                pdfGenerating = false;
                                    }
                                });

                    }, 300);
            },
            error: function() {
                    canvas.remove();
                    pdfGenerating = false;
            }
        });
        }

        /* ================= AUTO DOWNLOAD ON LOAD ================= */
        $(window).on('load', function() {

            if (autoDownloadDone) return;

            const $btn = $('#download_selected_btn');
            if (!$btn.length) return;

            autoDownloadDone = true;

            setTimeout(function() {
                generateMarksheetPdf($btn);
            }, 700); // iOS safe delay
    });
</script>

</body>

</html>