<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 利用者情報編集
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="" style="padding: 0 20px;">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <form role="form" method="post" action="<?php echo base_url() ?>setting/patientadd" id="editUser" role="form">
                        <input type="hidden" name="patient_id" value="<?php echo $patient['id'];?>">
                        <input type="hidden" value="update" name='mode' id='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_name">利用者名：</label>
                                        <input type="text" class="form-control" id="patient_name" placeholder=""
                                               name="patient_name" value="<?php echo $patient['patient_name']; ?>"
                                               maxlength="128">
                                        <input type="hidden" value="<?php echo $patient['id']; ?>"
                                               name="patient_id" id="patient_id"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_addr">住所：</label>
                                        <input type="text" class="form-control" id="patient_addr" placeholder=""
                                               name="patient_addr" value="<?php echo $patient['patient_addr']; ?>"
                                               maxlength="128">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_regdate">登録日：</label>
                                        <input type="text" id="patient_regdate" name="patient_regdate" class="datepicker form-control required" value="<?php echo $patient['patient_regdate']; ?>">
                                    </div>
                                </div>

                                <div class ="box-body" style = "height: 300px; overflow: auto;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date">曜日：</label>
                                            <select class="form-control required" name="patient_date" id="patient_date">
                                                <option value="1">月</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype">サービス：</label>
                                            <select class="form-control required" name="patient_curetype" id="patient_curetype">
                                                <option value="0" <?= ($patient['patient_curetype'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom">開始時間：</label>
                                            <input type="text" id="patient_usefrom" name="patient_usefrom" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto">終了時間：</label>
                                            <input type="text" id="patient_useto" name="patient_useto" class="timepicker form-control required" value="<?php echo $patient['patient_useto']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat">頻度：</label>
                                            <select class="form-control required" name="patient_repeat" id="patient_repeat">
                                                <option value="0" <?= ($patient['patient_repeat'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date2">曜日：</label>
                                            <select class="form-control required" name="patient_date2" id="patient_date2">
                                                <option value="2">火</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype2">サービス：</label>
                                            <select class="form-control required" name="patient_curetype2" id="patient_curetype2">
                                                <option value="0" <?= ($patient['patient_curetype2'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype2'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype2'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom2">開始時間：</label>
                                            <input type="text" id="patient_usefrom2" name="patient_usefrom2" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom2']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto2">終了時間：</label>
                                            <input type="text" id="patient_useto2" name="patient_useto2" class="timepicker form-control required" value="<?php echo $patient['patient_useto2']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat2">頻度：</label>
                                            <select class="form-control required" name="patient_repeat2" id="patient_repeat2">
                                                <option value="0" <?= ($patient['patient_repeat2'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat2'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat2'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat2'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat2'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date3">曜日：</label>
                                            <select class="form-control required" name="patient_date3" id="patient_date3">
                                                <option value="3">水</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype3">サービス：</label>
                                            <select class="form-control required" name="patient_curetype3" id="patient_curetype3">
                                                <option value="0" <?= ($patient['patient_curetype3'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype3'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype3'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom3">開始時間：</label>
                                            <input type="text" id="patient_usefrom3" name="patient_usefrom3" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom3']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto3">終了時間：</label>
                                            <input type="text" id="patient_useto3" name="patient_useto3" class="timepicker form-control required" value="<?php echo $patient['patient_useto3']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat3">頻度：</label>
                                            <select class="form-control required" name="patient_repeat3" id="patient_repeat3">
                                                <option value="0" <?= ($patient['patient_repeat3'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat3'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat3'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat3'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat3'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date4">曜日：</label>
                                            <select class="form-control required" name="patient_date4" id="patient_date4">
                                                <option value="4">木</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype4">サービス：</label>
                                            <select class="form-control required" name="patient_curetype4" id="patient_curetype4">
                                                <option value="0" <?= ($patient['patient_curetype4'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype4'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype4'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom4">開始時間：</label>
                                            <input type="text" id="patient_usefrom4" name="patient_usefrom4" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom4']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto4">終了時間：</label>
                                            <input type="text" id="patient_useto4" name="patient_useto4" class="timepicker form-control required" value="<?php echo $patient['patient_useto4']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat4">頻度：</label>
                                            <select class="form-control required" name="patient_repeat4" id="patient_repeat4">
                                                <option value="0" <?= ($patient['patient_repeat4'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat4'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat4'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat4'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat4'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date5">曜日：</label>
                                            <select class="form-control required" name="patient_date5" id="patient_date5">
                                                <option value="5">金</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype5">サービス：</label>
                                            <select class="form-control required" name="patient_curetype5" id="patient_curetype5">
                                                <option value="0" <?= ($patient['patient_curetype5'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype5'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype5'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom5">開始時間：</label>
                                            <input type="text" id="patient_usefrom5" name="patient_usefrom5" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom5']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto5">終了時間：</label>
                                            <input type="text" id="patient_useto5" name="patient_useto5" class="timepicker form-control required" value="<?php echo $patient['patient_useto5']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat5">頻度：</label>
                                            <select class="form-control required" name="patient_repeat5" id="patient_repeat5">
                                                <option value="0" <?= ($patient['patient_repeat5'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat5'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat5'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat5'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat5'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date6">曜日：</label>
                                            <select class="form-control required" name="patient_date6" id="patient_date6">
                                                <option value="6">土</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype6">サービス：</label>
                                            <select class="form-control required" name="patient_curetype6" id="patient_curetype6">
                                                <option value="0" <?= ($patient['patient_curetype6'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype6'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype6'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom6">開始時間：</label>
                                            <input type="text" id="patient_usefrom6" name="patient_usefrom6" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom6']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto6">終了時間：</label>
                                            <input type="text" id="patient_useto6" name="patient_useto6" class="timepicker form-control required" value="<?php echo $patient['patient_useto6']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat6">頻度：</label>
                                            <select class="form-control required" name="patient_repeat6" id="patient_repeat6">
                                                <option value="0" <?= ($patient['patient_repeat6'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat6'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat6'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat6'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat6'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_date7">曜日：</label>
                                            <select class="form-control required" name="patient_date7" id="patient_date7">
                                                <option value="7">日</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_curetype7">サービス：</label>
                                            <select class="form-control required" name="patient_curetype7" id="patient_curetype7">
                                                <option value="0" <?= ($patient['patient_curetype7'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_curetype7'] == 1) ? 'selected' : '' ?>>看護</option>
                                                <option value="2" <?= ($patient['patient_curetype7'] == 2) ? 'selected' : '' ?>>リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom">開始時間：</label>
                                            <input type="text" id="patient_usefrom" name="patient_usefrom" class="timepicker form-control required" value="<?php echo $patient['patient_usefrom']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto">終了時間：</label>
                                            <input type="text" id="patient_useto" name="patient_useto" class="timepicker form-control required" value="<?php echo $patient['patient_useto']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat">頻度：</label>
                                            <select class="form-control required" name="patient_repeat" id="patient_repeat">
                                                <option value="0" <?= ($patient['patient_repeat'] == 0) ? 'selected' : '' ?>>選択してください</option>
                                                <option value="1" <?= ($patient['patient_repeat'] == 1) ? 'selected' : '' ?>>毎日</option>
                                                <option value="2" <?= ($patient['patient_repeat'] == 2) ? 'selected' : '' ?>>毎週</option>
                                                <option value="3" <?= ($patient['patient_repeat'] == 3) ? 'selected' : '' ?>>隔週</option>
                                                <option value="4" <?= ($patient['patient_repeat'] == 4) ? 'selected' : '' ?>>毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-default" href="<?php echo base_url().'setting/patient/'?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/common.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript">
  $(function () {
    $('.timepicker').datetimepicker({
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
  });

  $('.datepicker').datetimepicker({
        locale: 'ja',
        format: 'YYYY-MM-DD',
    });
  var timer = 0;

  function onFaqSync(id) {
    const base_url = "<?php echo base_url(); ?>";
    if (confirm("メイン→サブにすべての記事を同期してもよろしいですか？")) {
      $("#loading").show();
      $.ajax({
        url: base_url + 'admin/company/faq_sync/' + id,
        type: 'post',
        dataType: 'json',
      }).done(function (res) {
        $("#loading").hide();
        console.log("elapse: ",res.time)
        if (res.ok == true) {
          alert(res.cnt + '件の記事を同期しました。');
        } else {
          alert('エラーが発生しました。');
        }
      }).fail(function () {
        $("#loading").hide();
        alert("メイン→サブに記事のコピー中にエラーが発生しました。");
      });

    }
  }
</script>