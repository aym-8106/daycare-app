<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>出退勤一覧ページ</h1>
    </section>
    
    <section class="content">
        <!-- <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <a href="<?php //echo base_url() ?>attendance/edit" class="btn btn-primary btn-flat">編集</a>
            </div>
        </div> -->
        
        <?php
            $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
            $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

            $prev_year = $year;
            $prev_month = $month - 1;
            if ($prev_month < 1) {
                $prev_month = 12;
                $prev_year--;
            }

            $next_year = $year;
            $next_month = $month + 1;
            if ($next_month > 12) {
                $next_month = 1;
                $next_year++;
            }
        ?>

        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>attendance/list" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group text-center">
                        <?php
                            if($this->user['staff_role'] == 1) {
                                ?>
                                <div class="col-lg-12 col-sm-12">
                                    <select class="form-control required" name="staff_id" id="company_staff_id">
                                        <?php foreach ($company_staff as $staff): ?>
                                            <?php echo $url = "?year={$year}&month={$month}&staff_id={$staff['staff_id']}"; ?>
                                            <option value="<?php echo $url ?>" <?php echo ($staff['staff_id'] == $selected_staff_id) ? 'selected' : '' ?>>
                                                <?php echo $staff['staff_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-sm-12">
                                    <a href="?year=<?php echo $prev_year ?>&month=<?php echo $prev_month ?>&staff_id=<?php echo isset($_GET['staff_id']) ? $_GET['staff_id'] : $company_staff[0]['staff_id'] ?>" class="btn btn-default btn-sm month-btn prev-month">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                    <?php echo ($year.'年 '.$month.'月'); ?>
                                    <a href="?year=<?php echo $next_year ?>&month=<?php echo $next_month ?>&staff_id=<?php echo isset($_GET['staff_id']) ? $_GET['staff_id'] : $company_staff[0]['staff_id'] ?>" class="btn btn-default btn-sm month-btn next-month">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </div><?php 
                            } else if($this->user['staff_role'] == 2) {?>
                                <div class="col-lg-12 col-sm-12">
                                    <label class="">
                                        <a href="?year=<?php echo $prev_year; ?>&month=<?php echo $prev_month; ?>" class="btn btn-default btn-sm month-btn prev-month">
                                            <i class="fa fa-chevron-left"></i>
                                        </a>

                                        <?php echo($user['staff_name']); ?>さん 
                                        <?php echo ($year.'年 '.$month.'月'); ?>

                                        <a href="?year=<?php echo $next_year; ?>&month=<?php echo $next_month; ?>" class="btn btn-default btn-sm month-btn next-month">
                                            <i class="fa fa-chevron-right"></i>
                                        </a>
                                    </label>
                                </div><?php
                            }?>
                    </div>
                    <div class="form-group">
                        <div class="box-body table-responsive no-padding"  style="overflow: auto;">
                            <table class="table table-hover table-bordered table-striped sticky-table">
                                <thead>
                                    <tr>
                                        <th class="sticky-col">日付</th>
                                        <th>出勤時間</th>
                                        <th>退勤時間</th>
                                        <th>休憩</th>
                                        <th>残業開始</th>
                                        <th>残業終了</th>
                                        <th>残業時間</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($month_attendance_data)) {
                                    for($i = 0; $i < $days; $i++) {
                                        $current_day = $i + 1;
                                        $attendance_found = false;
                                        
                                        foreach ($month_attendance_data as $attendance) {
                                            $attendance_day = $attendance['work_date'];
                                            
                                            if ($current_day == $attendance_day) {
                                                $attendance_found = true; ?>
                                                <tr>
                                                    <td class="sticky-col"><?php echo $current_day; ?>日</td>
                                                    <td><?php echo $attendance['work_time']; ?></td>
                                                    <td><?php echo $attendance['leave_time']; ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($attendance['total_break_time'] == 0) {
                                                            if(date('d') != $current_day) {
                                                                echo $attendance['relax_time'] . '分';
                                                            }
                                                        } elseif ($attendance['total_break_time'] < 60) {
                                                            echo '1分未満';
                                                        } else {
                                                            echo floor($attendance['total_break_time'] / 60) . '分';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $attendance['overtime_start_time']; ?></td>
                                                    <td><?php echo $attendance['overtime_end_time']; ?></td>
                                                    <td><?php echo $attendance['overtime_duration']; ?></td>
                                                </tr><?php
                                                break;
                                            }
                                        }
                                        
                                        if (!$attendance_found) { ?>
                                            <tr>
                                                <td class="sticky-col"><?php echo $current_day; ?>日</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } else {
                                    for($i = 0; $i < $days; $i++) { ?>
                                        <tr>
                                            <td class="sticky-col"><?php echo $i + 1; ?>日</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr><?php
                                    }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div><!-- ./col -->
        </div>
    </section>
</div>

<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript">
    $( function () {
        $('.datepicker').datetimepicker({
            locale: 'ja',
            format: 'HH:mm',
        });

        // jQuery('.datepicker').datepicker({
        //     language: "ja",
        //     autoclose: true,
        //     format: "yyyy-mm-dd",
        //     zIndexOffset: 1000,
        // });
    });

    document.getElementById('company_staff_id').addEventListener('change', function () {
        window.location.href = this.value;
    });
</script>