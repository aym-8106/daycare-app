<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 職員配属管理
            <small><?php echo $company['company_name']; ?></small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
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
            </div>
        </div>

        <!-- 事業所情報表示 -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">事業所情報</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>事業所名:</strong> <?php echo $company['company_name']; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>事業所番号:</strong> <?php echo isset($company['company_number']) ? $company['company_number'] : '未設定'; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>住所:</strong> <?php echo isset($company['company_address']) ? $company['company_address'] : '未設定'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 配属職員一覧 -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">配属職員一覧</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($staff_list)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ユーザーID</th>
                                            <th>職員名</th>
                                            <th>メールアドレス</th>
                                            <th>役割</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staff_list as $staff) { ?>
                                            <tr>
                                                <td><?php echo $staff['staff_id']; ?></td>
                                                <td><?php echo $staff['staff_name']; ?></td>
                                                <td><?php echo $staff['email']; ?></td>
                                                <td>
                                                    <?php
                                                    echo ($staff['staff_role'] == 1) ? '<span class="label label-primary">管理者</span>' : '<span class="label label-default">スタッフ</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" onclick="transferStaff(<?php echo $staff['staff_id']; ?>, '<?php echo $staff['staff_name']; ?>')">
                                                        <i class="fa fa-exchange"></i> 配属変更
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="removeStaff(<?php echo $staff['staff_id']; ?>, '<?php echo $staff['staff_name']; ?>')">
                                                        <i class="fa fa-trash"></i> 削除
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> この事業所に配属されている職員はいません。
                            </div>
                        <?php } ?>
                    </div>
                    <div class="box-footer">
                        <a class="btn btn-default" href="<?php echo admin_url() . 'company/edit/' . $company['company_id']; ?>">事業所編集に戻る</a>
                        <a class="btn btn-default" href="<?php echo admin_url() . 'company/'; ?>">事業所一覧に戻る</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- 配属変更モーダル -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="transferForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">職員配属変更</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mode" value="transfer_staff">
                    <input type="hidden" name="staff_id" id="transfer_staff_id">

                    <div class="form-group">
                        <label>職員名:</label>
                        <span id="transfer_staff_name" class="form-control-static"></span>
                    </div>

                    <div class="form-group">
                        <label for="new_company_id">転属先事業所:</label>
                        <select class="form-control" name="new_company_id" id="new_company_id" required>
                            <option value="">選択してください</option>
                            <?php foreach ($company_list as $comp) { ?>
                                <?php if ($comp['company_id'] != $company['company_id'] && $comp['del_flag'] == 0) { ?>
                                    <option value="<?php echo $comp['company_id']; ?>"><?php echo $comp['company_name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i> 配属変更を行うと、職員の所属事業所が変更されます。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-warning">配属変更実行</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="removeForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">職員削除確認</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mode" value="remove_staff">
                    <input type="hidden" name="staff_id" id="remove_staff_id">

                    <div class="alert alert-danger">
                        <i class="fa fa-warning"></i>
                        <strong><span id="remove_staff_name"></span></strong> を削除しますか？
                        <br><br>
                        この操作は取り消せません。削除された職員のデータは復元できません。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-danger">削除実行</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function transferStaff(staffId, staffName) {
    $('#transfer_staff_id').val(staffId);
    $('#transfer_staff_name').text(staffName);
    $('#transferModal').modal('show');
}

function removeStaff(staffId, staffName) {
    $('#remove_staff_id').val(staffId);
    $('#remove_staff_name').text(staffName);
    $('#removeModal').modal('show');
}
</script>