<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>スタッフ設定ページ</h1>
    </section>

    <section class="content">
        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>setting/staff" method="POST" id="form1" name="form1" class="form-horizontal">
                    <input type="hidden" name="mode" value="save">
                    <input type="hidden" name="hid_id" value="<?php if(!empty($staff_setting->id)) echo($staff_setting->id); ?>">
                    <table style="width: 100%;"cellpadding="20">
                        <tr>
                            <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;" width="40%">名前</th>
                            <td colspan="3" style="padding: 10px 10px; border-bottom: solid 1px #bbb;"><?php echo ($staff_info['staff_name']); ?>さん</td>
                        </tr>
                        <tr>
                            <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;">職種</th>
                            <td colspan="3" style="padding: 10px 10px; border-bottom: solid 1px #bbb;">
                                <select class="form-control required" id="jobtypeId" name="jobtypeId">
                                    <?php foreach ($staff_data as $key => $value): ?>
                                        <?php if ($value['jobtypeId'] == $staff_info['jobtypeId']): ?>
                                            <option value="<?= $value['jobtypeId'] ?>" selected><?= $value['jobtype'] ?></option>
                                        <?php else: ?>
                                            <option value="<?= $value['jobtypeId'] ?>"><?= $value['jobtype'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;">勤務形態</th>
                            <td colspan="3" style="padding: 10px 10px; border-bottom: solid 1px #bbb;">
                                <select class="form-control required" id="employtypeId" name="employtypeId">
                                    <?php foreach ($employtype_data as $key => $value): ?>
                                        <?php if ($value['employtypeId'] == $staff_info['employtypeId']): ?>
                                            <option value="<?= $value['employtypeId'] ?>" selected><?= $value['employtype'] ?></option>
                                        <?php else: ?>
                                            <option value="<?= $value['employtypeId'] ?>"><?= $value['employtype'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;">出勤日</th>
                            <th colspan="3" style="padding: 10px 10px; border-bottom: solid 1px #bbb;">出勤時間</th>
                        </tr>
                        <?php
                        $days = array('月', '火', '水', '木', '金', '土');
                        $dates = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat');
                        $i = 0;
                        foreach ($days as $day) { ?>
                            <tr>
                                <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;"><?php echo ($day); ?> </th>
                                <td style="padding: 10px 0px 10px 10px; border-bottom: solid 1px #bbb;"><?php $start_key = $dates[$i] . '_start'; ?><input type="text" id="<?php echo($dates[$i]); ?>_start" name="data[<?php echo($dates[$i]); ?>_start]" class="datepicker form-control" value="<?php echo !empty($staff_setting->$start_key) ? $staff_setting->$start_key : ''; ?>"></td>
                                <td style="padding: 10px 0px; border-bottom: solid 1px #bbb;">～</td>
                                <td style="padding: 10px 10px 10px 0px; border-bottom: solid 1px #bbb;"><?php $end_key = $dates[$i] . '_end'; ?><input type="text" id="<?php echo($dates[$i]); ?>_end" name="data[<?php echo($dates[$i]); ?>_end]" class="datepicker form-control" value="<?php echo !empty($staff_setting->$end_key) ? $staff_setting->$end_key : ''; ?>"></td>
                            </tr>
                        <?php $i++; } ?>
                        <tr>
                            <th style="padding: 10px 10px; border-bottom: solid 1px #bbb;">休憩時間(分)</th>
                            <td colspan="3" style="padding: 10px 10px; border-bottom: solid 1px #bbb;"><input type="number" id="relax_time" name="data[relax_time]" class="form-control" value="<?php echo !empty($staff_setting->relax_time) ? $staff_setting->relax_time : ''; ?>"></td>
                        </tr>
                    </table>
                    <div class="box-body">
                        <div class="col-lg-4 col-xs-2"></div>
                        <div class="col-lg-4 col-xs-8 text-center">
                            <!-- small box -->
                            <input type="submit" class="btn btn-primary" value="保存" />
                        </div><!-- ./col -->
                        <div class="col-lg-4 col-xs-2"></div>
                    </div>
                </form>
            </div><!-- ./col -->
        </div>
        <div style="height: 100px;"></div>
    </section>
</div>

<!--<script type="text/javascript" src="--><?php //echo base_url(); 
                                            ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url(); 
                                            ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

<script type="text/javascript">
    $(function() {
        $('.datepicker').datetimepicker({
            locale: 'ja',
            format: 'HH:mm',
            stepping: 15, // 15分単位で選べるように
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down'
            },
            useCurrent: false,
            showClose: true,
            showClear: true,
            showTodayButton: false,
            toolbarPlacement: 'bottom',
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'auto', // 'auto' / 'left' / 'right'
                vertical: 'bottom'   // 'auto' / 'top' / 'bottom'
            }
        });

        // jQuery('.datepicker').datepicker({
        //     language: "ja",
        //     autoclose: true,
        //     format: "yyyy-mm-dd",
        //     zIndexOffset: 1000,
        // });
    });
</script>