<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
          <i class="fa fa-users"></i> 指示書編集
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

                    <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/instruction_save" method="post" role="form">
                        <input type="hidden" name="mode" value="update">
                        <input type="hidden" name="instruction_id" value="<?php echo $instructionId ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">                                
                                    <div class="form-group">
                                        <label for="patient_name">利用者名：</label>
                                        <select class="form-control required" id="post_patient" name="patientId">
                                            <option value="">選択してください</option>
                                            <?php foreach ($patient as $key => $value): ?>
                                                <?php if ($value['id'] == $instruction['patient_id']): ?>
                                                    <option value="<?= $value['id'] ?>" selected><?= $value['patient_name'] ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $value['id'] ?>"><?= $value['patient_name'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- 指示元 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">指示元：</label>
                                        <select class="form-control required" id="company" name="company">
                                            <option value="">選択してください</option>
                                            <?php foreach ($company as $key => $value): ?>
                                                <?php if ($value['company_id'] == $instruction['company_id']): ?>
                                                    <option value="<?= $value['company_id'] ?>" selected><?= $value['company_name'] ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $value['company_id'] ?>"><?= $value['company_name'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- 主治医 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_name">主治医：</label>
                                        <select class="form-control required" id="staff" name="staff">
                                            <option value="">選択してください</option>
                                            <?php foreach ($staff_list as $key => $value): ?>
                                                <?php if ($value['staff_id'] == $instruction['staff_id']): ?>
                                                    <option value="<?= $value['staff_id'] ?>" selected><?= $value['staff_name'] ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $value['staff_id'] ?>"><?= $value['staff_name'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_usefrom">指示期間開始：</label>
                                        <input type="text" id="patient_usefrom" name="patient_usefrom" readonly class="datepicker form-control required" value="<?php echo $instruction['instruction_start']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_useto">指示期間終了：</label>
                                        <input type="text" id="patient_useto" name="patient_useto" readonly class="datepicker form-control required" value="<?php echo $instruction['instruction_end']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存" />
                            <a class="btn btn-default" href="<?php echo base_url().'instruction/index/'?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </section>
    
</div>

<script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!-- Select2 CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $( function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
    });
</script>

<script>
    $('#company').on('change', function () {
        const companyId = $(this).val();
        if (companyId) {
            $.ajax({
                url: '<?= base_url() ?>instruction/get_staff_data',
                method: 'POST',
                data: { company_id: companyId },
                dataType: 'json',
                success: function(response) {
                    $('#staff').empty();
                    $('#staff').append('<option value="">選択してください</option>');
                    $.each(response, function(index, staff) {
                        $('#staff').append('<option value="' + staff.staff_id + '">' + staff.staff_name + '</option>');
                    });
                },
                error: function () {
                    alert('情報の取得に失敗しました');
                }
            });
        }
    });
</script>

<script>
$(document).ready(function() {
    $('#company, #staff').select2({
        tags: true, // 新しい値の入力を許可
        placeholder: '選択または入力してください',
        allowClear: true,
        width: '100%', // Bootstrapとの整合性
        language: {
            noResults: function() {
                return "選択可能な情報がありません。";
            }
        }
    });
});
</script>
