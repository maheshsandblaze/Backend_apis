<?php
$student_allover_rank = [];
$subject_rank = [];
foreach ($result as $student_key => $student_value) {
  $total_max_marks = 0;
  $total_gain_marks = 0;

  foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
    foreach ($student_exam_value['subjects'] as $subject_key => $subject_value) {
      $subject_total = 0;
      $subject_max_total = 0;

      foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
        $subject_total += $assessment_value['marks'];
        $subject_max_total += $assessment_value['maximum_marks'];

        $total_gain_marks += $assessment_value['marks'];
        $total_max_marks += $assessment_value['maximum_marks'];
      }
      if (!array_key_exists($subject_key, $subject_rank)) {
        $subject_rank[$subject_key] = [];
      }

      $subject_rank[$subject_key][] = [
        'student_session_id' => $student_value['student_session_id'],
        'rank_percentage'    => $subject_total,
        'rank' => 0

      ];
    }
  }

  $exam_percentage = getPercent($total_max_marks, $total_gain_marks);

  $student_allover_rank[$student_value['student_session_id']] = [
    'student_session_id' => $student_value['student_session_id'],
    'firstname' => $student_value['firstname'],
    'rank_percentage' => $exam_percentage,
    'rank' => 0,
  ];
}

//-=====================start term calculation Rank=============

$rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

$term_rank_allover_list = unique_array($student_allover_rank, "rank_percentage");

foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {

  $student_allover_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
}

//-=====================end term calculation Rank=============

foreach ($subject_rank as $subject_term_key => $subject_term_value) {

  $rank_overall_subject = array_column($subject_rank[$subject_term_key], 'rank_percentage');

  array_multisort($rank_overall_subject, SORT_DESC, $subject_rank[$subject_term_key]);

  $subject_rank_allover_list = unique_array($subject_rank[$subject_term_key], "rank_percentage");

  foreach ($subject_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

    $subject_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
  }
}
?>

<?php

$count_result = count($result);
$student_increment = 0;

// echo "<pre>";
// print_r($bimage);exit;

$count = 0;

foreach ($result as $student_key => $student_value) {
  $student_increment++;
  $total_max_marks = 0;
  $total_gain_marks = 0;
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

  </head>

  <body>
    <div style="width: 100%; margin: 0 auto;">
      <?php

      if ($template['header_image'] != "") {
      ?>

        <img width="100%" max-width="100%" src="<?php echo base_url("/uploads/cbseexam/template/header_image/" . $template['header_image']) ?>" />

      <?php
      }
      ?>
      <table cellpadding="0" cellspacing="0" width="100%" style="margin-right:10px">
        <!-- <tr>
          <td valign="top" style="height:180px"></td>
        </tr> -->
        <tr>
          <td valign="top">
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top" style="padding-bottom: 0px; padding-top: 5px; width: 100%; font-weight: bold; text-align: center; font-size:20px;">
                  <?php // echo $this->lang->line('report_card'); 
                  ?>
                  <?php echo $template['name'] ?>
                </td>
              </tr>
              <tr>
                <td valign="top" style="padding-bottom: 20px; padding-top: 2px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">
                  Academic Year : <?php echo $current_setting['session']; ?>

                </td>
              </tr>

            </table>
          </td>
        </tr>
        <tr>
          <td valign="top" style="height:10px"></td>
        </tr>
        <tr>
          <td valign="top">
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top">
                  <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td valign="middle" width="80%">
                        <table cellpadding="0" cellspacing="0" width="100%">
                          <tr>
                            <?php
                            if ($template['is_admission_no']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px;width:100px"><?php echo $this->lang->line('admission_no'); ?>.</td>
                              <td valign="top" style="font-size:12px;">: <?php echo $student_value['admission_no']; ?></td>
                            <?php
                            }
                            ?>
                            <?php

                            if ($template['is_roll_no']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px;width:100px"><?php echo $this->lang->line('roll_no'); ?></td>
                              <td valign="top" style="margin-left:0px;font-size:12px;">: <?php echo $student_value['roll_no']; ?></td>
                            <?php
                            }
                            ?>

                          </tr>
                          <tr>
                            <?php

                            if ($template['is_name']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px;"><?php echo $this->lang->line('students_name'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo   $this->customlib->getFullName($student_value['firstname'], $student_value['middlename'], $student_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>

                            <?php
                            }
                            ?>
                            <?php
                            if ($template['is_dob']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px;"><?php echo $this->lang->line('date_of_birth'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $this->customlib->dateformat($student_value['dob']); ?></td>

                            <?php
                            }
                            ?>
                          </tr>
                          <tr>
                            <?php

                            if ($template['is_father_name']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('fathers_name'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $student_value['father_name']; ?> </td>

                            <?php
                            }
                            ?>
                            <?php

                            if ($template['is_mother_name']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px;"><?php echo $this->lang->line('mothers_name'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $student_value['mother_name']; ?></td>
                            <?php
                            }
                            ?>

                          </tr>
                          <tr>

                            <?php

                            if ($template['is_class'] && $template['is_section']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $cs = $student_value['class'] . " (" . $student_value['section'] . ")"; ?></td>
                            <?php
                            } else if ($template['is_class']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $student_value['class']; ?></td>
                            <?php
                            } else if ($template['is_section']) {
                            ?>
                              <td valign="top" style="font-size:12px;font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">: <?php echo $student_value['section']; ?></td>
                            <?php
                            }
                            ?>
                            <!--<td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('school_name'); ?></td>-->
                            <!--  <td valign="top">: <?php echo $template['school_name'] ?></td>                   -->
                            <!-- <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('result_declaration_date'); ?></td>-->
                            <!--<td valign="top">: <?php echo $this->customlib->dateformat(date('Y-m-d')); ?></td>                -->
                          </tr>
                          <!--<tr><td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('exam_center'); ?></td>-->
                          <!--  <td valign="top">:  <?php echo $template['exam_center'] ?></td> -->

                          <!--<td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('result_declaration_date'); ?></td>-->
                          <!--<td valign="top">: <?php echo $this->customlib->dateformat(date('Y-m-d')); ?></td>                -->

                          <!--</tr>-->
                        </table>
                      </td>
                      <?php
                      if ($template['is_photo']) {
                      ?>

                        <td valign="top" align="right" width="20%">
                          <?php

                          if (!empty($student_value["student_image"])) {
                            $student_image = base_url() . $student_value["student_image"];
                          } else {
                            if ($student_value['gender'] == 'Female') {
                              $student_image = base_url() . "uploads/student_images/default_female.jpg";
                            } elseif ($student_value['gender'] == 'Male') {
                              $student_image = base_url() . "uploads/student_images/default_male.jpg";
                            }
                          }
                          ?>
                          <img src="<?php echo $student_image; ?>" width="125" height="130" style="border:1px solid #000">
                        </td>
                      <?php
                      }
                      ?>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td valign="top" style="height:10px"></td>
        </tr>
        <tr>
          <td valign="top">

            <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
              <thead>
                <!--<tr><td colspan="8" style="text-align:center;height:30px;font-size:14px;border:1px solid #858585;border-bottom:none;padding:5px;"> <?php echo $template['name'] ?></td></tr>-->
                <tr>


                  <td valign="middle" style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;"><?php echo $this->lang->line('subject'); ?>s</td>
                  <!--<?php if ($student_value['class'] == 'UKG') { ?>-->
                  <!--  <td valign="middle" class="text-center" style="background-color:#ccc;width:160px;">Maximum Marks </td>  -->
                  <!--  <td valign="middle" class="text-center" style="background-color:#ccc;width:160px;">Minmum Marks</td>  -->
                  <!--  <?php } ?>-->
                  <?php

                  foreach ($student_value['term']['exams'] as $exam_key => $exam_value) {

                    reset($exam_value['subjects']);
                    echo $subject_first_key = key($exam_value['subjects']);
                    foreach ($exam_value['subjects'][$subject_first_key]['exam_assessments'] as $subject_assesment_key => $subject_assesment_value) {

                  ?>


                      <td valign="middle" class="text-center" style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;padding:5px;text-align:center">

                        <?php
                        $subject_assesment_value['cbse_exam_assessment_type_name'] . "-" . " (" . $subject_assesment_value['cbse_exam_assessment_type_code'] . ")";
                        echo $subject_assesment_value['cbse_exam_assessment_type_name'] . "";
                        //echo "<br/>";
                        $subject_assesment_value['maximum_marks'];
                        ?></td><?php
                              }
                            }
                                ?>


                  <td valign="middle" class="text-center" style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center"><?php echo $this->lang->line('total'); ?></td>
                  <td valign="middle" class="text-center" style="font-size:12px;background-color:#ccc;width:50px;border:1px solid #858585;border-right:none;padding:5px;text-align:center"><?php echo $this->lang->line('grade'); ?></td>
                  <!--<td valign="middle" class="text-center"><?php echo $this->lang->line('rank'); ?></td>-->
                  <td valign="middle" class="text-center" style="font-size:12px;background-color:#ccc;width:50px;border:1px solid #858585;padding:5px;text-align:center"> Points</td>

                </tr>
              </thead>
              <tbody>

                <?php

                foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
                  $gpa_points = [];
                  foreach ($student_exam_value['subjects'] as $exam_key => $exam_value) {
                ?>
                    <tr>
                      <td valign="top" style="font-size:12px;border:1px solid #858585;border-right:none;border-top:none;padding:5px;"><?php echo $exam_value['subject_name'] . " "; ?>
                      </td>
                      <!-- <?php if ($student_value['class'] == 'UKG') { ?>-->
                      <!--<td valign="middle" class="text-center" style="width:80px;">100</td>  -->
                      <!--<td valign="middle" class="text-center" style="width:80px;">0</td> -->
                      <!-- <?php } ?>-->
                      <?php
                      $subject_total = 0;
                      $subject_max_total = 0;
                      foreach ($exam_value['exam_assessments'] as $assessment_key => $assessment_value) {
                        $subject_total += $assessment_value['marks'];
                        $subject_max_total += $assessment_value['maximum_marks'];
                        $total_gain_marks += $assessment_value['marks'];
                        $total_max_marks += $assessment_value['maximum_marks'];
                      ?>
                        <td valign="top" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;text-align:center">
                          <?php
                          if (is_null($assessment_value['marks'])) {
                            echo "N/A";
                          } else {
                            echo ($assessment_value['is_absent']) ? $this->lang->line('abs') : $assessment_value['marks'];
                            //   echo "/";
                            //   echo  $assessment_value['maximum_marks']; 
                          }
                          ?>
                        </td>
                      <?php
                      }


                      ?>


                      <?php if ($exam_value['subject_name'] == 'Physical Science' || $exam_value['subject_name'] == 'Biology') {

                        $subject_max_total = $subject_max_total - 10;

                        $total_max_marks = $total_max_marks - 10;
                      } ?>






                      <td valign="top" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;text-align:center"><?php echo $subject_total; ?> / <?php echo $subject_max_total; ?>

                      </td>




                      <td valign="top" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;text-align:center">
                        <?php
                        $subject_percentage = getPercent($subject_max_total, $subject_total);
                        echo $grade = getGrade($exam, $subject_percentage);
                        ?>
                      </td>
                      <td valign="top" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;padding:5px;text-align:center">

                        <!--<?php echo searchSubjectRank($student_value['subject_rank'], $exam_value['subject_id']); ?>-->

                        <?php
                        $gpa_point = 0;
                        if ($grade == "A1") {
                          $gpa_point = 10;
                        } elseif ($grade == "A2") {
                          $gpa_point = 9;
                        } elseif ($grade == "B1") {
                          $gpa_point = 8;
                        } elseif ($grade == "B2") {
                          $gpa_point = 7;
                        } elseif ($grade == "C1") {
                          $gpa_point = 6;
                        } elseif ($grade == "C2") {
                          $gpa_point = 5;
                        } elseif ($grade == "D") {
                          $gpa_point = 4;
                        } else {
                          $gpa_point = 0;
                        }
                        echo $gpa_point;

                        $gpa_points[] = $gpa_point;
                        ?>

                      </td>

                    </tr>
                <?php
                  }
                }

                // Calculate overall GPA
                $total_gpa_points = array_sum($gpa_points);
                $total_subjects = count($gpa_points);
                $overall_gpa = $total_subjects ? $total_gpa_points / $total_subjects : 0;

                $exam_percentage = getPercent($total_max_marks, $total_gain_marks);

                ?>

              </tbody>
            </table>

          </td>
        </tr>
        <tr>
          <td valign="top" style="height:10px"></td>
        </tr>
        <tr>
          <td>
            <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
              <tbody>
                <tr>
                  <td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">Overall Result</td>
                  <td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;;border-top:none;padding:5px;"><?php echo $this->lang->line('total'); ?> : <?php echo two_digit_float($total_gain_marks, 2) . "/" . $total_max_marks ?></td>
                  <td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;;border-top:none;padding:5px;"><?php echo $this->lang->line('percentage'); ?> : <?php echo $p = two_digit_float($exam_percentage, 2); ?></td>
                  <td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;;border-top:none;padding:5px;"><?php echo $this->lang->line('grade'); ?> : <?php echo $fgrade = getGrade($exam, $exam_percentage) ?></td>
                  <td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;;border-top:none;padding:5px;">GPA : <?php echo number_format($overall_gpa, 1);


                                                                                                                                                ?> </td>
                  <!--<td><?php echo $this->lang->line('rank'); ?> : <?php echo $student_value['rank']; ?></td>  -->

                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <!--<tr><td valign="top" style="height:10px"></td></tr>-->
        <!--      <tr> -->
        <!--<td valign="top" style="font-size:12px;" class="text-left" colspan="<?php echo count($exam_assessments) + 4 ?>">-->
        <?php //echo $this->lang->line('grading_scale'); 
        ?> : <?php

              //     echo implode(', ', array_map(
              //     function($k)  {
              //         return $k->name." (".$k->maximum_percentage . "% - " . $k->minimum_percentage."%)";
              //     },
              //     ($exam->grades)

              //     )
              // );
              ?>
        <!--</td>-->
        <!--</tr>-->
        <tr>
          <td valign="top" style="height:30px"></td>
        </tr>

        <tr>
          <td>

            <?php if ($bimage) { ?>
              <table style="margin-left: auto;">
                <tr>
                  <td>
                    <div class="square" style="width: 20px; height: 10px;background-color: #FFCC00;"> </div>
                  </td>
                  <!--<td style="background-color: rgb(255, 99, 132);" colspan="1">-->
                  <!--    Student Score-->
                  <!--</td>-->
                  <!--<td style="background-color: #90EE90;" colspan="1">-->
                  <!--  Class Average Score-->
                  <!--</td>-->
                  <!--<td style="background-color: #2E3131; color: white" colspan="1">-->
                  <!--  Maximum Score-->
                  <!--</td>-->

                  <td style="background-color: rgb(255, 99, 132);" colspan="1">
                    Student Marks
                  </td>
                  <td style="background-color: #90EE90;" colspan="1">
                    Subject Avg
                  </td>
                  <td style="background-color: #2E3131; color: white" colspan="1">
                    Subject Max
                  </td>
                </tr>
              </table>
            <?php } ?>

            <table>
              <tr width="100%">
                <td>
                  <img src="<?php echo $bimage[$count]; ?>" id="myChartImage" />
                </td>
              </tr>
            </table>

          </td>
        </tr>
        <tr>
          <td valign="top" style="height:30px"></td>
        </tr>
        <tr>
          <td valign="top" width="100%" align="center">
            <table cellpadding="0" cellspacing="0" width="100%" style=" margin-bottom:10px;">
              <tr>
                <td valign="top" width="60%" style="text-align:left;font-size:12px;">

                  <!--<b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b> <?php echo $student_value['remark']; ?>-->
                  <b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b>

                  <?php


                  $finalGrade = getGrade($exam, $exam_percentage) 



                  ?>
                  <?php

                  $student_reamrk = "";


                  $studentGrade = $finalGrade;

                  if ($studentGrade == "A1") {
                    $student_reamrk = "Excellent work! Consistently demonstrated exceptional understanding and skills. Showed remarkable progress and dedication.";
                  } else if ($studentGrade == "A2") {
                    $student_reamrk = "Outstanding performance! Displayed remarkable knowledge and critical thinking skills.";
                  } else if ($studentGrade == "B1") {
                    $student_reamrk = "Good effort! Demonstrated a solid understanding of concepts and skills. Exhibited a good grasp of knowledge and application skills.";
                  } else if ($studentGrade == "B2") {
                    $student_reamrk = "Good work! Showed steady progress and improvement.";
                  } else if ($studentGrade == "C1") {
                    $student_reamrk = "Fair effort! Needs to improve understanding and application of concepts. Should focus on developing skills and knowledge.";
                  } else if ($studentGrade == "C2") {
                    $student_reamrk = "Average performance! Needs to work on consistency and quality of work.";
                  } else if ($studentGrade == "D") {
                    $student_reamrk = "Needs improvement! Requires extra support and practice. Must focus on developing fundamental skills and knowledge.";
                  } else if ($studentGrade == "E") {
                    $student_reamrk = "2. Needs more attention* Must focus on developing fundamental skills and knowledge. Needs significant improvement and extra support.";
                  } 




                  if ($student_reamrk != "") {

                  ?>


                    <table cellpadding="0" cellspacing="0" width="100%" class="denifittable" style="padding-top: 20px; padding-bottom: 20px;text-align: center; border:1px solid #858585; margin:20px 0">
                      <tbody>
                        <tr>
                          <td valign="middle" style="font-size:12px;" rowspan="2"><?php echo $student_reamrk; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  <?php } ?>
                </td>

                <td valign="top" width="40%" class="signature text-right">

                  <table border="3" class="denifittable">
                    <tr>
                      <th colspan="3" class="color1" style="font-size:12px;border:1px solid #858585;border-bottom:none;">CCE GRADE POINT LEGEND</th>
                    </tr>
                    <tr class="tab">
                      <th class="color2" style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;">School % Ratings</th>
                      <th class="color2" style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;border-left:none;">Grade</th>
                      <th class="color2" style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;border-left:none;">Grade Points</th>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">91-100</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">A1</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">10</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">81-90</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">A2</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">9</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">71-80</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">B1</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">8</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">61-70</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">B2</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">7</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">51-60</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">C1</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">6</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">41-50</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">C2</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">5</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">35-40</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">D</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">4</td>
                    </tr>
                    <tr class="text">
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">BELOW 35</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">E1</td>
                      <td style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">--</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td valign="top" style="height:20px"></td>
        </tr>

        <tr>
          <td>
            <table cellpadding="0" cellspacing="0" width="100%" class="denifittable" style="padding-bottom: 10px;text-align: center;">
              <tbody>
                <tr>
                  <td valign="middle" class="text-center" style="font-size:12px;border:1px solid #858585;" rowspan="2"><b><?php echo $this->lang->line('attendance_overall'); ?></b></td>
                  <td valign="middle" class="text-center" style="font-size:12px;border:1px solid #858585;padding:5px"><b><?php echo $this->lang->line('total_working_days'); ?></b></td>
                  <td valign="middle" class="text-center" style="font-size:12px;border:1px solid #858585;padding:5px"><b><?php echo $this->lang->line('days_present'); ?></b></td>


                  <td valign="middle" class="text-center" style="font-size:12px;border:1px solid #858585;padding:5px"><b><?php echo $this->lang->line('attendance_percentage'); ?></b></td>
                </tr>
                <tr>
                  <td valign="middle" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px"><?php echo $student_value['total_working_days']; ?></td>
                  <td valign="middle" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px"><?php echo $student_value['total_present_days']; ?></td>

                  <td valign="middle" class="text-center" style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px"><?php echo getPercent($student_value['total_working_days'], $student_value['total_present_days']) . "%"; ?></td>
                </tr>
              </tbody>
            </table>



            <?php
            $months = array(
              1   => 'June',
              2   => 'July',
              3   => 'August',
              4   => 'September',
              5   => 'October',
              6   => 'November',
              7   => 'December',
              8   => 'January',
              9   => 'February',
              10  => 'March',
              11  => 'April'
            );
            ?>

            <!--<table  cellpadding="0" cellspacing="0" width="100%" class="denifittable" style="padding-bottom: 10px;">-->
            <!--<tbody>-->
            <!--<tr>-->
            <!--    <td valign="middle" class="text-center" width="60%" style="background-color:#ccc;" ><b>Months</b></td>-->
            <!--    <td valign="middle" class="text-center" style="background-color:#ccc;"><b><?php echo $months[$student_value['month1']]; ?></b></td>-->
            <!--    <?php if (!empty($student_value['month2'])) { ?>-->
            <!--        <td valign="middle" class="text-center" style="background-color:#ccc;"><b><?php echo $months[$student_value['month2']]; ?></b></td>-->
            <!--    <?php } ?>-->
            <!--    <?php if (!empty($student_value['month3'])) { ?>-->
            <!--        <td valign="middle" class="text-center" style="background-color:#ccc;"><b><?php echo $months[$student_value['month3']]; ?></b></td>-->
            <!--    <?php } ?>-->

            <!--  </tr>-->
            <!--  <tr>  -->
            <!--    <td valign="middle" class="text-center">Present Days / Working Days</td>-->
            <!--    <td valign="middle" class="text-center"><?php echo $student_value['month1_present_days'] . " " . "/" . " " . $student_value['month1_working_days']; ?></td>-->
            <!--    <?php if (!empty($student_value['month2_present_days']) && !empty($student_value['month2_working_days'])) { ?>  -->
            <!--        <td valign="middle" class="text-center"><?php echo $student_value['month2_present_days'] . " " . "/" . " " . $student_value['month2_working_days']; ?></td>-->
            <!--    <?php } ?>  -->
            <!--    <?php if (!empty($student_value['month3_present_days']) && !empty($student_value['month3_working_days'])) { ?>  -->
            <!--        <td valign="middle" class="text-center"><?php echo $student_value['month3_present_days'] . " " . "/" . " " . $student_value['month3_working_days']; ?></td>  -->
            <!--    <?php } ?>-->

            <!--  </tr>-->
            <!--</tbody>-->
            <!--</table>-->

          </td>
        </tr>

        <?php if($student_value['class_id'] == "22" || $student_value['class_id'] == "21" || $student_value['class_id'] == "20"){ ?>

          <tr>
          <td valign="top" style="height:50px"></td>
        </tr>

       <?php } else { ?>

        <tr>
          <td valign="top" style="height:90px"></td>
        </tr>

        <?php } ?>

        <tr>
          <td valign="top" width="100%" align="center">
            <table cellpadding="0" cellspacing="0" width="100%" style="border-bottom:1px solid #999; margin-bottom:10px;">
              <tr>
                <!--   <td valign="top" width="32%" class="signature text-center">-->
                <!--  <img src="<?php echo base_url('uploads/cbseexam/template/right_sign/' . $template['right_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">-->
                <!--  <p class="fw-bold"><?php echo $this->lang->line('signature_of_principal'); ?></p>-->
                <!--</td>-->
                <td valign="top" width="32%" class="signature text-center">
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/white_bg.png') ?>" width="100" height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px">Signature of Parent</p>
                </td>
                <td valign="top" width="32%" class="signature">
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/white_bg.png') ?>" width="100" height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px;"><?php echo $this->lang->line('signature_of_class_teacher'); ?></p>
                </td>
                <td valign="top" width="32%" class="signature text-center">
                  <!--<img src="<?php echo base_url('uploads/cbseexam/template/middle_sign/' . $template['middle_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">-->
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/hm_sign.png') ?>" width="100" height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px"><?php echo $this->lang->line('signature_of_principal'); ?> / HM</p>
                </td>

              </tr>
            </table>
          </td>
        </tr>
        <!--<tr>-->
        <!--        <td valign="top" style="padding-bottom: 5px; padding-top: 5px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">-->
        <!--           <?php echo $this->lang->line('instruction'); ?>-->

        <!--        </td>-->
        <!--  </tr>-->
        <tr>
          <td valign="top" style="height:20px"></td>
        </tr>


        <tr>
          <td valign="top" style="margin-bottom:5px; padding-top: 10px; line-height: normal;">
            <?php echo $template['content_footer']; ?>
          </td>
        </tr>
      </table>
    </div>

  </body>

  </html>
<?php
  if ($student_increment < $count_result) {
    echo "<div style='page-break-after:always'></div>";
  }

  $count++;
}
?>
<?php
function getGrade($grade_array, $Percentage)
{

  if (!empty($grade_array->grades)) {
    foreach ($grade_array->grades as $grade_key => $grade_value) {

      if ($grade_value->minimum_percentage <= $Percentage) {
        return $grade_value->name;
        break;
      } elseif (($grade_value->minimum_percentage >= $Percentage && $grade_value->maximum_percentage <= $Percentage)) {

        return $grade_value->name;
        break;
      }
    }
  }
  return "-";
}

function searchSubjectRank($array, $subject_id)
{

  foreach ($array as $k => $val) {

    if ($k == $subject_id) {
      return $val;
    }
  }
  return null;
}

?>