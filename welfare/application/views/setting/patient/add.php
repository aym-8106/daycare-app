<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
          <i class="fa fa-users"></i> 利用者追加
      </h1>
    </section>
    
    <section class="content">

        <div class="row">
            <div class="col-md-8">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if($error)
                {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if($success)
                {
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
        </div>

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->

                <div class="box box-primary">
                    <div class="box-header">
                        <!-- <h3 class="box-title">事業所追加</h3> -->
                    </div><!-- /.box-header -->
                    <!-- form start -->

                    <form role="form" id="addUser" action="<?php echo base_url() ?>setting/patientadd" method="post" role="form">
                        <input type="hidden" name="mode" value="save">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="patient_name">利用者名：</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('patient_name'); ?>" id="patient_name" name="patient_name" maxlength="128">
                                    </div>
                                </div>
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="patient_addr">住所：</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('patient_addr'); ?>" id="patient_addr" name="patient_addr" maxlength="128">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_regdate">登録日：</label>
                                        <input type="text" id="patient_regdate" name="patient_regdate" class="datepicker form-control required" value="<?php echo $patient_regdate; ?>">
                                    </div>
                                </div>
                                <div class="box-body" style = "height: 400px; overflow: auto; margin: 20px 5px; background-color: #ffe6b9;">
                                    <!-- mon -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom">開始時間：</label>
                                            <input type="text" id="patient_usefrom" name="patient_usefrom" class="timepicker form-control required" value="<?php echo $patient_usefrom; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto">終了時間：</label>
                                            <input type="text" id="patient_useto" name="patient_useto" class="timepicker form-control required" value="<?php echo $patient_useto; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat">頻度：</label>
                                            <select class="form-control required" name="patient_repeat" id="patient_repeat">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- tue -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom2">開始時間：</label>
                                            <input type="text" id="patient_usefrom2" name="patient_usefrom2" class="timepicker form-control required" value="<?php echo $patient_usefrom2; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto2">終了時間：</label>
                                            <input type="text" id="patient_useto2" name="patient_useto2" class="timepicker form-control required" value="<?php echo $patient_useto2; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat2">頻度：</label>
                                            <select class="form-control required" name="patient_repeat2" id="patient_repeat2">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- wed -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom3">開始時間：</label>
                                            <input type="text" id="patient_usefrom3" name="patient_usefrom3" class="timepicker form-control required" value="<?php echo $patient_usefrom3; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto3">終了時間：</label>
                                            <input type="text" id="patient_useto3" name="patient_useto3" class="timepicker form-control required" value="<?php echo $patient_useto3; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat3">頻度：</label>
                                            <select class="form-control required" name="patient_repeat3" id="patient_repeat3">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- thu -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom4">開始時間：</label>
                                            <input type="text" id="patient_usefrom4" name="patient_usefrom4" class="timepicker form-control required" value="<?php echo $patient_usefrom4; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto4">終了時間：</label>
                                            <input type="text" id="patient_useto4" name="patient_useto4" class="timepicker form-control required" value="<?php echo $patient_useto4; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat4">頻度：</label>
                                            <select class="form-control required" name="patient_repeat4" id="patient_repeat4">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- fri -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom5">開始時間：</label>
                                            <input type="text" id="patient_usefrom5" name="patient_usefrom5" class="timepicker form-control required" value="<?php echo $patient_usefrom5; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto5">終了時間：</label>
                                            <input type="text" id="patient_useto5" name="patient_useto5" class="timepicker form-control required" value="<?php echo $patient_useto5; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat5">頻度：</label>
                                            <select class="form-control required" name="patient_repeat5" id="patient_repeat5">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- sat -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom6">開始時間：</label>
                                            <input type="text" id="patient_usefrom6" name="patient_usefrom6" class="timepicker form-control required" value="<?php echo $patient_usefrom6; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto6">終了時間：</label>
                                            <input type="text" id="patient_useto6" name="patient_useto6" class="timepicker form-control required" value="<?php echo $patient_useto6; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat6">頻度：</label>
                                            <select class="form-control required" name="patient_repeat6" id="patient_repeat6">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- sun -->
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
                                                <option value="1">看護</option>
                                                <option value="2">リハビリ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_usefrom7">開始時間：</label>
                                            <input type="text" id="patient_usefrom7" name="patient_usefrom7" class="timepicker form-control required" value="<?php echo $patient_usefrom7; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_useto7">終了時間：</label>
                                            <input type="text" id="patient_useto7" name="patient_useto7" class="timepicker form-control required" value="<?php echo $patient_useto7; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                
                                        <div class="form-group">
                                            <label for="patient_repeat7">頻度：</label>
                                            <select class="form-control required" name="patient_repeat7" id="patient_repeat7">
                                                <option value="1">毎日</option>
                                                <option value="2">毎週</option>
                                                <option value="3">隔週</option>
                                                <option value="4">毎月</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存" />
                            <a class="btn btn-default" href="<?php echo base_url().'setting/patient/'?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </section>
    
</div>

<script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script>
    $( function () {
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
        $('.datepicker').datetimepicker({
            locale: 'ja',
            format: 'YYYY-MM-DD',
        });
    });
</script>
