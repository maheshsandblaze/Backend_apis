<?php $this->load->view('layout/cbseexam_css.php'); ?>
<?php

$student_allover_exam_rank = [];
$subject_wise_rank = [];
foreach ($result as $student_key => $student_value) {
  $grand_total_term_percentage = 0;
  $grand_total_exam_weight_percentage = 0;

  foreach ($subject_array as $subject_array_key => $subject_array_value) {
    $subject_grand_total = 0;

    $subject_total_weight_percentage = 0;

    foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

      $exam_subject_total = 0;
      $exam_subject_maximum_total = 0;
      foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {

        $subject_marks_array = getSubjectData($student_value, $exam_value['exam_id'], $subject_array_key, $exam_assessment_value['assesment_type_id']);

        if (!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A") {

          $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
          $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
        } else {

          $exam_subject_total += 0;
          $exam_subject_maximum_total += 0;
        }
      }

      $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
      $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));
    }
    if (!array_key_exists($subject_array_key, $subject_wise_rank)) {
      $subject_wise_rank[$subject_array_key] = [];
    }

    $subject_wise_rank[$subject_array_key][] = [
      'student_session_id' => $student_value['student_session_id'],
      'rank_percentage'    => $subject_total_weight_percentage,
      'rank' => 0

    ];

    $grand_total_exam_weight_percentage += $subject_total_weight_percentage;
  }

  $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_exam_weight_percentage);

  $student_allover_exam_rank[$student_value['student_session_id']] = [
    'student_session_id' => $student_value['student_session_id'],
    'firstname' => $student_value['firstname'],
    'rank_percentage' => $overall_percentage,
    'rank' => 0,
  ];
}

// //-=====================start term calculation Rank=============

$rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');
$rank_overall_term_student_name_keys = array_column($student_allover_exam_rank, 'firstname');
array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $rank_overall_term_student_name_keys, SORT_ASC, $student_allover_exam_rank);

$term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {
  $student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
}

//-=====================end term calculation Rank=============

//=====================start subject term calculation Rank=============

foreach ($subject_wise_rank as $subject_term_key => $subject_term_value) {

  $rank_overall_subject = array_column($subject_wise_rank[$subject_term_key], 'rank_percentage');

  array_multisort($rank_overall_subject, SORT_DESC, $subject_wise_rank[$subject_term_key]);

  $subject_rank_allover_list = unique_array($subject_wise_rank[$subject_term_key], "rank_percentage");

  foreach ($subject_wise_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

    $subject_wise_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
  }
}

?>

<?php

$count_result = count($result);
$student_increment = 0;

$count = 0;

foreach ($result as $student_key => $student_value) {


  // echo "<pre>";
  // print_r($student_value);exit;
  $student_increment++;
  $grand_total_marks = 0;
  $grand_total_exam_weight_percentage = 0;
  $grand_total_gain_marks = 0;
  $terms_weight_array = [];
  // $total_present_day=0;
  // $total_total_working_day=0;

  $month1 = 0;
  $month2 = 0;
  $month3 = 0;
  $total_present_day1 = 0;
  $total_total_working_day1 = 0;
  $total_present_day2 = 0;
  $total_total_working_day2 = 0;
  $total_present_day3 = 0;
  $total_total_working_day3 = 0;


  foreach ($student_value['exams'] as $each_exam_key => $each_exam_value) {

    //   $total_present_day+=$each_exam_value['total_present_days'];
    //   $total_total_working_day+=$each_exam_value['total_working_days'] ;

    // $month1 = $each_exam_value['month1'];  
    // $total_present_day1 = $each_exam_value['month1_present_days'];

    // $total_total_working_day2 = $each_exam_value['month2_working_days'];

    // $month3 = $each_exam_value['month3'];  
    // $total_present_day3 = $each_exam_value['month3_present_days'];
    // $total_total_working_day3 = $each_exam_value['month3_working_days'];

    // $total_present_days = $each_exam_value->total_present_days;
    // $total_working_days = $each_exam_value->total_working_days;

  }



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

        <img width="100%" max-width="100%"
          src="<?php echo base_url("/uploads/cbseexam/template/header_image/" . $template['header_image']) ?>" />

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
                <td valign="top"
                  style="padding-bottom: 0px; padding-top: 5px; width: 100%; font-weight: bold; text-align: center; font-size:20px;">
                  <?php // echo $this->lang->line('report_card'); 
                  ?>
                  <?php echo $template['name'] ?>
                </td>
              </tr>
              <tr>
                <td valign="top"
                  style="padding-bottom: 20px; padding-top: 2px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">
                  Academic Year : <?php echo $current_setting['session']; ?>

                </td>
              </tr>

            </table>
          </td>
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
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('admission_no'); ?>.</td>
                              <td valign="top">: <?php echo $student_value['admission_no']; ?>
                              </td>
                            <?php
                            }
                            ?>
                            <?php

                            if ($template['is_roll_no']) {
                            ?>
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('roll_no'); ?></td>
                              <td valign="top" style="margin-left:0px">:
                                <?php echo $student_value['roll_no']; ?></td>
                            <?php
                            }
                            ?>

                          </tr>
                          <tr>
                            <?php

                            if ($template['is_name']) {
                            ?>
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px;">
                                <?php echo $this->lang->line('students_name'); ?></td>
                              <td valign="top">:
                                <?php echo   $this->customlib->getFullName($student_value['firstname'], $student_value['middlename'], $student_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                              </td>

                            <?php
                            }
                            ?>
                            <?php
                            if ($template['is_dob']) {
                            ?>
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px;">
                                <?php echo $this->lang->line('date_of_birth'); ?></td>
                              <td valign="top">:
                                <?php echo $this->customlib->dateformat($student_value['dob']); ?>
                              </td>

                            <?php
                            }
                            ?>
                          </tr>
                          <tr>
                            <?php

                            if ($template['is_father_name']) {
                            ?>
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('fathers_name'); ?></td>
                              <td valign="top">: <?php echo $student_value['father_name']; ?>
                              </td>

                            <?php
                            }
                            ?>
                            <?php

                            if ($template['is_mother_name']) {
                            ?>
                              <td valign="top" style="font-weight: bold; padding-bottom: 2px;">
                                <?php echo $this->lang->line('mothers_name'); ?></td>
                              <td valign="top">: <?php echo $student_value['mother_name']; ?></td>
                            <?php
                            }
                            ?>

                          </tr>
                          <tr>

                            <?php

                            if ($template['is_class'] && $template['is_section']) {
                            ?>
                              <td valign="top"
                                style="font-size:12px;font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">:
                                <?php echo $cs = $student_value['class'] . " (" . $student_value['section'] . ")"; ?>
                              </td>
                            <?php
                            } else if ($template['is_class']) {
                            ?>
                              <td valign="top"
                                style="font-size:12px;font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">:
                                <?php echo $student_value['class']; ?></td>
                            <?php
                            } else if ($template['is_section']) {
                            ?>
                              <td valign="top"
                                style="font-size:12px;font-weight: bold; padding-bottom: 2px">
                                <?php echo $this->lang->line('class_section'); ?></td>
                              <td valign="top" style="font-size:12px;">:
                                <?php echo $student_value['section']; ?></td>
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
                          <img src="<?php echo $student_image; ?>" width="125" height="130"
                            style="border:1px solid #000">
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
                <!-- <tr>
              <td valign="middle" style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;"><?php echo $this->lang->line('scholastic_areas'); ?></td>

                  <?php
                  foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {

                    $term_colspan = count($assess_value['exam_total_assessments']);

                    $terms_weight_array[] = ($template['is_weightage'] == "no") ? ($assess_value['exam_name']) : $assess_value['exam_name'] . " (" . $assess_value['weightage'] . ")";
                  ?>
                    <?php
                    // $is_fa1 = (strpos($assess_value['exam_name'], 'FORMATIVE ASSESSMENT-1') !== false);
                    // $is_fa2 = (strpos($assess_value['exam_name'], 'FORMATIVE ASSESSMENT-2') !== false);
                    // $is_sa1 = (strpos($assess_value['exam_name'], 'SUMMATIVE ASSESSMENT-1') !== false);

                    $is_fa1 = ($assess_value['cbse_term_name'] === 'FA 1');
                    $is_fa2 = ($assess_value['cbse_term_name'] === 'FA 2');
                    $is_sa1 = ($assess_value['cbse_term_name'] === 'SA 1');
                    ?>
                    <?php if ($is_fa1 || $is_fa2) { ?>
                  <td valign="middle" style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;">FA's Average 
                    </td>
                  <?php } else if ($is_sa1) { ?>
                      <td valign="middle" colspan="2<?php $term_colspan + 2; ?>" style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;"><?php echo $assess_value['exam_name']; ?> 
                  </td>
                  <?php
                    }
                  }
                  ?>
              <td valign="top" colspan="3" style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;">   
                    <?php
                    //term merge array              
                    echo implode(" + ", $terms_weight_array);
                    ?>
                  </td>
          </tr> -->
                <tr>
                  <td valign="middle"
                    style="font-size:12px;background-color:#ccc;width:190px;border:1px solid #858585;border-right:none;padding:5px;">
                    <?php echo $this->lang->line('subject'); ?>s</td>
                  <?php
                  foreach ($exam_term_exam_assessment as $exam_name => $exam_value) {

                    foreach ($exam_value['exam_total_assessments'] as $exam_assement_key => $exam_assement_value) {
                  ?> <?php
                      $exam_assement_value['assesment_type_name'] . "";
                      "<br/>";
                      $tot = $exam_assement_value['assesment_type_maximum_marks'];

                      ?>
                  <?php
                    }
                  }

                  ?>
                  <?php
                  // $is_fa1 = (strpos($exam_value['exam_name'], 'FORMATIVE ASSESSMENT-1') !== false);
                  // $is_fa2 = (strpos($exam_value['exam_name'], 'FORMATIVE ASSESSMENT-2') !== false);
                  // $is_sa1 = (strpos($exam_value['exam_name'], 'SUMMATIVE ASSESSMENT-1') !== false);

                  $is_fa1 = ($exam_value['cbse_term_name'] === 'FA 1');
                  $is_fa2 = ($exam_value['cbse_term_name'] === 'FA 2');
                  $is_sa1 = ($exam_value['cbse_term_name'] === 'SA 1');
                  ?>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center">
                    FA's <?php echo $this->lang->line('total'); ?>(20)</td>


                  <td valign="middle" class="text-center"
                    style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center">
                    SA <?php echo $this->lang->line('total'); ?>(80)</td>
                  <!--<td  valign="middle" class="text-center" style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center"><?php echo $this->lang->line('grade'); ?></td>-->

                  <td valign="middle" class="text-center"
                    style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center">
                    <?php echo $this->lang->line('grand_total'); ?>(100)</td>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;border-right:none;padding:5px;text-align:center">
                    <?php echo $this->lang->line('grade'); ?></td>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;background-color:#ccc;width:80px;border:1px solid #858585;padding:5px;text-align:center">
                    Grade Points</td>

                </tr>
                <?php

                foreach ($subject_array as $subject_array_key => $subject_array_value) {

                  $subject_grand_total = 0;

                  $subject_total_weight_percentage = 0;
                  $fa1_marks = null;
                  $fa2_marks = null;
                  $fa1_maximum = 0;
                  $fa2_maximum = 0;
                  $sa_marks  = null;
                  $sa_maxmarks = 0;
                ?>
                  <tr>
                    <td valign="top"
                      style="font-size:12px;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                      <?php echo $subject_array_value; ?></td>
                    <?php

                    //print_r($exam_term_exam_assessment);exit;
                    foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

                      $exam_subject_total = 0;
                      $exam_subject_maximum_total = 0;

                      // $is_fa1 = ($exam_value['exam_id'] == 85);
                      // $is_fa2 = ($exam_value['exam_id'] == 98);

                      // $is_fa1 = (strpos($exam_value['exam_name'], 'FORMATIVE ASSESSMENT-1') !== false);
                      // $is_fa2 = (strpos($exam_value['exam_name'], 'FORMATIVE ASSESSMENT-2') !== false);
                      // $is_sa1 = (strpos($exam_value['exam_name'], 'SUMMATIVE ASSESSMENT-1') !== false);

                      $is_fa1 = ($exam_value['cbse_term_name'] === 'FA 1');
                      $is_fa2 = ($exam_value['cbse_term_name'] === 'FA 2');
                      $is_sa1 = ($exam_value['cbse_term_name'] === 'SA 1');

                      foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {
                    ?>

                        <?php

                        $subject_marks_array = getSubjectData($student_value, $exam_value['exam_id'], $subject_array_key, $exam_assessment_value['assesment_type_id']);

                        if (!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A") {
                          ($subject_marks_array['is_absent']) ? $this->lang->line('abs') : $subject_marks_array['marks'];

                          $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                          $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];

                          if ($is_fa1) {
                            $fa1_marks += $subject_marks_array['marks'];
                            $fa1_maximum += $subject_marks_array['maximum_marks'];
                          } elseif ($is_fa2) {
                            $fa2_marks += $subject_marks_array['marks'];
                            $fa2_maximum += $subject_marks_array['maximum_marks'];
                          } elseif ($is_sa1) {
                            $sa_marks += $subject_marks_array['marks'];
                            $sa_maxmarks += $subject_marks_array['maximum_marks'];
                          }
                        } else {
                          echo "-";
                          $exam_subject_total += 0;
                          $exam_subject_maximum_total += 0;
                          $fa1_marks += 0;
                          $fa1_maximum += 0;
                          $fa2_marks += 0;
                          $fa2_maximum += 0;
                        }
                        ?>


                    <?php
                      }
                    }
                    // $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                    $subject_percentage = getPercent($sa_maxmarks, $sa_marks);

                    $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));

                    //   if ($is_fa1) {
                    //     echo "<td>FA1 Marks: " . $exam_subject_total . "</td>";
                    // } elseif ($is_fa2) {
                    //     echo "<td>FA2 Marks: " . $exam_subject_total . "</td>";
                    // }

                    ?>
                    <td valign="top"
                      style="text-align:center; font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                      <?php
                      // Calculate and display the average of FA1 and FA2 only if both are available


                      if (!is_null($fa1_marks) && !is_null($fa2_marks)) {
                        $fas_average = ($fa1_marks + $fa2_marks) / 2;

                        $fa_average = round($fas_average);


                        $primary_subjects = ['Physical Science', 'Biology'];
                        if (!in_array($subject_array_value, $primary_subjects)) {
                          echo ($fa_average == 0) ? "N/A" : $fa_average;
                        } else {
                          $fa_average = $fa_average;
                          echo ($fa_average == 0) ? "N/A" : $fa_average;
                        }
                      }



                      if ($sa_marks != 0) {
                        $ind_subject_total = $fa_average + $sa_marks;
                      } else {

                        $ind_subject_total = $fa_average;
                      }

                      // $ind_subject_total = $fa_average + $exam_subject_total;


                      ?>
                    </td>

                    <?php
                    $primary_subjects = ['Physical Science', 'Biology'];
                    $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'Computer'];

                    if (in_array($subject_array_value, $excluded_subjects)) { ?>

                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo ($sa_marks == 0) ? "N/A" : $sa_marks; ?>
                      </td>


                    <?php } else if (!in_array($subject_array_value, $primary_subjects)) {
                    ?>
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo ($sa_marks == 0) ? "N/A" : $sa_marks; ?>
                      </td>

                    <?php } else { ?>
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo ($sa_marks == 0) ? "N/A" : $sa_marks; ?>
                      </td>
                    <?php } ?>





                    <?php
                    $primary_subjects = ['Physical Science', 'Biology', 'Computer'];
                    if (!in_array($subject_array_value, $primary_subjects)) {
                    ?>
                      <!--<td valign="top" style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;"> <?php echo getGrade($exam_grades, $subject_percentage); ?></td>-->
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo two_digit_float($ind_subject_total); ?></td>
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo  $grade = getGrade($exam_grades, $ind_subject_total); ?></td>
                    <?php } else { ?>
                      <!--<td valign="top" style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;"> <?php echo getGrade($exam_grades, $subject_percentage * 2); ?></td>-->
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo two_digit_float($ind_subject_total); ?></td>
                      <td valign="top"
                        style="text-align:center;font-size:12px;font-weight:300;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                        <?php echo  $grade = getGrade($exam_grades, $ind_subject_total * 2); ?></td>
                    <?php } ?>






                    <td valign="top"
                      style="text-align:center; font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;padding:5px;">
                      <?php
                      searchSubjectRank($student_value['subject_rank'], $subject_array_key);

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
                      } elseif ($grade == "E") {
                        $gpa_point = 2;
                      } else {
                        $gpa_point = 0;
                      }
                      echo $gpa_point;

                      $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'Computer'];

                      if (!in_array($subject_array_value, $excluded_subjects)) {

                        $gpa_points[] = $gpa_point;
                      }





                      ?>

                    </td>
                  </tr>
                <?php

                  $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'Computer'];

                  if (!in_array($subject_array_value, $excluded_subjects)) {

                    $grand_total_exam_weight_percentage += $ind_subject_total;
                  }
                }
                ?>

              </thead>
            </table>

          </td>
        </tr>
        <tr>
          <td valign="top" style="height:0px"></td>
        </tr>
        <tr>
          <td>
            <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
              <tbody>
                <tr>
                  <?php
                  $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'Computer'];

                  $included_subjects = array_filter($subject_array, function ($subject) use ($excluded_subjects) {
                    return !in_array($subject, $excluded_subjects);
                  });

                  $excluded_classes = [20, 21, 22];

                  if (!in_array($student_value['class_id'], $excluded_classes)) {



                    $overall_percentage = getPercent((count($included_subjects) * 100), $grand_total_exam_weight_percentage);
                  } else {
                    $overall_percentage = getPercent((count($included_subjects) * 100 - 100), $grand_total_exam_weight_percentage);
                  }

                  $total_gpa_points = array_sum($gpa_points);
                  $total_subjects = count($gpa_points);
                  $overall_gpa = $total_subjects ? $total_gpa_points / $total_subjects : 0;
                  ?>
                  <td
                    style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                    Overall Result</td>
                  <!--<td style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;"><?php echo $this->lang->line('overall_marks'); ?> : <?php echo two_digit_float($grand_total_exam_weight_percentage, 2) . "/" . count($subject_array) * 100; ?></td>-->
                  <td
                    style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                    <?php echo $this->lang->line('overall_marks'); ?> :
                    <?php
                    $excluded_subjects = ['ICT', 'Moral Values', 'G.K', 'Drawing', 'Rhymes', 'Computer'];

                    $included_subjects = array_filter($subject_array, function ($subject) use ($excluded_subjects) {
                      return !in_array($subject, $excluded_subjects);
                    });

                    $total_max_marks = count($included_subjects) * 100;

                    $excluded_classes = [20, 21, 22];

                    if (!in_array($student_value['class_id'], $excluded_classes)) {

                      echo two_digit_float($grand_total_exam_weight_percentage, 2) . " / " . $total_max_marks;
                    } else {
                      echo two_digit_float($grand_total_exam_weight_percentage, 2) . " / " . ($total_max_marks - 100);
                    }
                    ?>
                  </td>
                  <td
                    style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                    <?php echo $this->lang->line('percentage'); ?> :
                    <?php echo two_digit_float($overall_percentage, 2); ?></td>
                  <td
                    style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-right:none;border-top:none;padding:5px;">
                    <?php echo $this->lang->line('grade'); ?> :
                    <?php echo $grade = getGrade($exam_grades, $overall_percentage); ?></td>
                  <td
                    style="font-size:12px;font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;padding:5px;">
                    GPA
                    <?php echo  number_format($overall_gpa, 1); ?>
                    Out of 10</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td valign="top" style="height:20px"></td>
        </tr>
        <!--<tr>           -->

        <?php
        // $total_colspan=4;
        // foreach ($exam_term_exam_assessment as $assess_key => $assess_value) { 

        //  $term_colspan=count($assess_value['exam_total_assessments']);
        // $total_colspan+=$term_colspan+2;

        // }
        ?>
        <!--<td valign="top" colspan="<?php echo $total_colspan ?>">   -->
        <?php //echo $this->lang->line('grading_scale'); 
        ?> : <?php

              //     echo implode(', ', array_map(
              //     function($k)  {
              //         return $k->name." (".$k->maximum_percentage . "% - " . $k->minimum_percentage."%)";
              //     },
              //     ($exam_grades)

              //     )
              // );

              ?>
        <!--   </td>          -->
        <!--</tr>-->
        <tr>
          <td valign="top" style="height:40px"></td>
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
                  <img src="<?php echo $bimage; ?>" id="myChartImage" />
                </td>
              </tr>
            </table>

            <!--<table>
              <tr width="100%">
                <td>
                  <img src="<?php echo $bimage[$count]; ?>" id="myChartImage" />
          </td>
        </tr>
            </table>-->

          </td>
        </tr>

        <!--<tr><td valign="top" style="height:15px"></td></tr>-->

        <tr>
          <td valign="top" style="height:20px"></td>
        </tr>
        <tr>
          <td valign="top" width="100%" align="center">
            <table cellpadding="0" cellspacing="0" width="100%" style=" margin-bottom:10px;">
              <tr>
                <td valign="top" width="60%" style="text-align:left;font-size:12px;">

                  <!--<b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b> <?php echo $student_value['remark']; ?>-->
                  <b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b>

                  <?php


                  $finalGrade = getGrade($exam_grades, $overall_percentage);

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


                    <table cellpadding="0" cellspacing="0" width="100%" class="denifittable" style="padding-top: 20px; padding-bottom: 20px;text-align: center; border:1px solid #0463fd; margin:20px 0;">
                      <tbody>
                        <tr>
                          <td valign="middle" style="font-size:12px; " rowspan="2"><?php echo $student_reamrk; ?></td>
                        </tr>
                      </tbody>
                    </table>


                  <?php } ?>

                </td>

                <td valign="top" width="40%" class="signature text-right">

                  <table border="3" class="denifittable">
                    <tr>
                      <th colspan="3" class="color1"
                        style="font-size:12px;border:1px solid #858585;border-bottom:none;">CCE
                        GRADE POINT LEGEND</th>
                    </tr>
                    <tr class="tab">
                      <th class="color2"
                        style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;">
                        School % Ratings</th>
                      <th class="color2"
                        style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;border-left:none;">
                        Grade</th>
                      <th class="color2"
                        style="font-size:12px;background-color:#ccc;border:1px solid #858585;border-top:none;border-left:none;">
                        Grade Points</th>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        91-100</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        A1</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        10</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        81-90</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        A2</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        9</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        71-80</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        B1</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        8</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        61-70</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        B2</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        7</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        51-60</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        C1</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        6</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        41-50</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        C2</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        5</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        35-40</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        D</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        4</td>
                    </tr>
                    <tr class="text">
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;">
                        BELOW 35</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        E1</td>
                      <td
                        style="font-size:12px;font-weight:300;border:1px solid #858585;border-top:none;border-left:none;">
                        --</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td>
            <table cellpadding="0" cellspacing="0" width="100%" class="denifittable"
              style="padding-bottom: 10px;text-align: center;">
              <tbody>
                <tr>
                  <td valign="middle" class="text-center" style="font-size:12px;border:1px solid #858585;"
                    rowspan="2"><b><?php echo $this->lang->line('attendance_overall'); ?></b></td>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;border:1px solid #858585;padding:5px">
                    <b><?php echo $this->lang->line('total_working_days'); ?></b>
                  </td>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;border:1px solid #858585;padding:5px">
                    <b><?php echo $this->lang->line('days_present'); ?></b>
                  </td>


                  <td valign="middle" class="text-center"
                    style="font-size:12px;border:1px solid #858585;padding:5px">
                    <b><?php echo $this->lang->line('attendance_percentage'); ?></b>
                  </td>
                </tr>
                <tr>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px">
                    <?php echo $student_value['total_working_days']; ?></td>
                  <td valign="middle" class="text-center"
                    style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px">
                    <?php echo $student_value['total_present_days']; ?></td>

                  <td valign="middle" class="text-center"
                    style="font-size:12px;font-weight:300;border:1px solid #858585;padding:5px">
                    <?php echo getPercent($student_value['total_working_days'], $student_value['total_present_days']) . "%"; ?></td>
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
            <!--    <td valign="middle" class="text-center" width="60%" ><b>Months</b></td>-->
            <!--    <td valign="middle" class="text-center"><b><?php echo $months[$month1]; ?></b></td>-->
            <!--    <?php if (!empty($month2)) { ?>-->
            <!--        <td valign="middle" class="text-center"><b><?php echo $months[$month2]; ?></b></td>-->
            <!--    <?php } ?>-->
            <!--    <?php if (!empty($month3)) { ?>-->
            <!--        <td valign="middle" class="text-center"><b><?php echo $months[$month3]; ?></b></td>-->
            <!--    <?php } ?>-->

            <!--  </tr>-->
            <!--  <tr>  -->
            <!--    <td valign="middle" class="text-center">Present Days / Working Days</td>-->
            <!--    <td valign="middle" class="text-center"><?php echo $total_present_day1 . " " . "/" . " " . $total_total_working_day1; ?></td>-->
            <!--    <?php if (!empty($total_present_day2) && !empty($total_total_working_day2)) { ?>  -->
            <!--        <td valign="middle" class="text-center"><?php echo $total_present_day2 . " " . "/" . " " . $total_total_working_day2; ?></td>-->
            <!--    <?php } ?>  -->
            <!--    <?php if (!empty($total_present_day3) && !empty($total_total_working_day3)) { ?>  -->
            <!--        <td valign="middle" class="text-center"><?php echo $total_present_day3 . " " . "/" . " " . $total_total_working_day3; ?></td>  -->
            <!--    <?php } ?>-->

            <!--  </tr>-->
            <!--</tbody>-->
            <!--</table>-->

          </td>
        </tr>


        <tr>
          <td valign="top" style="height:80px"></td>
        </tr>
        <tr>
          <td valign="top" width="100%" align="center">
            <table cellpadding="0" cellspacing="0" width="100%"
              style="border-bottom:1px solid #999; margin-bottom:10px;">
              <tr>
                <!--   <td valign="top" width="32%" class="signature text-center">-->
                <!--  <img src="<?php echo base_url('uploads/cbseexam/template/right_sign/' . $template['right_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">-->
                <!--  <p class="fw-bold"><?php echo $this->lang->line('signature_of_principal'); ?></p>-->
                <!--</td>-->
                <td valign="top" width="32%" class="signature text-center">
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/white_bg.png') ?>" width="100"
                                    height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px">Signature of Parent</p>
                </td>
                <td valign="top" width="32%" class="signature">
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/white_bg.png') ?>" width="100"
                                    height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px;">
                    <?php echo $this->lang->line('signature_of_class_teacher'); ?></p>
                </td>
                <td valign="top" width="32%" class="signature text-center">
                  <!--<img src="<?php echo base_url('uploads/cbseexam/template/middle_sign/' . $template['middle_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">-->
                  <!-- <img src="<?php echo base_url('uploads/cbseexam/template/hm_sign.png') ?>" width="100"
                                    height="50" style="padding-bottom: 5px;"> -->
                  <p class="fw-bold" style="font-size:14px">
                    <?php echo $this->lang->line('signature_of_principal'); ?> / HM</p>
                </td>

              </tr>
            </table>
          </td>
        </tr>
        <!--<tr>-->
        <!--      <td valign="top" style="padding-bottom: 5px; padding-top: 5px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">-->
        <!--         <?php echo $this->lang->line('instruction'); ?>            -->
        <!--      </td>-->
        <!--</tr>-->
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
}
?>
<?php

function getGrade($grade_array, $Percentage)
{

  if (!empty($grade_array)) {
    foreach ($grade_array as $grade_key => $grade_value) {
      if ($grade_value->minimum_percentage <= $Percentage) {
        return $grade_value->name;
        break;
      } elseif ($grade_value->maximum_percentage <= $Percentage && $grade_value->minimum_percentage >= $Percentage) {

        return $grade_value->name;
        break;
      }
    }
  }
  return "-";
}


function getStudentObservation($student_observations, $student_session_id, $cbse_term_id, $parameter_id)
{
  if (!empty($student_observations)) {
    if (array_key_exists($student_session_id, $student_observations)) {

      if (array_key_exists($cbse_term_id, $student_observations[$student_session_id]['terms'])) {

        if (array_key_exists($parameter_id, $student_observations[$student_session_id]['terms'][$cbse_term_id]['paramters'])) {

          return $student_observations[$student_session_id]['terms'][$cbse_term_id]['paramters'][$parameter_id];
        }
      }
    }
  }
  return [];
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