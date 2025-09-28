<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 事業所管理
            <small>作成, 編集, 削除</small>
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
                    <form role="form" method="post" id="editUser" role="form">
                        <input type="hidden" name="Password" value=""/>
                        <div class="box-header">
                            <h3 class="box-title">事業所編集</h3>
                            <input type="submit" class="btn btn-primary pull-right" value="保存"/>
                        </div><!-- /.box-header -->
                        <input type="hidden" value="save" name='mode' id='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">事業所名：</label>
                                        <input type="text" class="form-control required" id="company_name" placeholder=""
                                               name="company_name" value="<?php echo $company['company_name']; ?>"
                                               maxlength="128" required>
                                        <input type="hidden" value="<?php echo $company['company_id']; ?>"
                                               name="company_id" id="company_id"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_number">事業所番号：</label>
                                        <input type="text" class="form-control required" id="company_number" placeholder="10桁の数字"
                                               name="company_number" value="<?php echo isset($company['company_number']) ? $company['company_number'] : ''; ?>"
                                               maxlength="10" pattern="[0-9]{10}" required>
                                        <small class="text-muted">10桁の数字で入力してください</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">郵便番号：</label>
                                        <input type="text" class="form-control required" id="postal_code" placeholder="000-0000"
                                               name="postal_code" value="<?php echo isset($company['postal_code']) ? $company['postal_code'] : ''; ?>"
                                               maxlength="8" pattern="[0-9]{3}-[0-9]{4}" required>
                                        <small class="text-muted">ハイフン付きで入力してください（例：163-8001）</small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="company_address">住所：</label>
                                        <textarea class="form-control required" id="company_address" placeholder="事業所の住所を入力してください"
                                                  name="company_address" rows="3" required><?php echo isset($company['company_address']) ? $company['company_address'] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>


                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-info" href="<?php echo admin_url() . 'company/staff/' . $company['company_id']; ?>">職員配属管理</a>
                            <a class="btn btn-default" href="<?php echo admin_url() . 'company/' ?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/common.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        // 郵便番号入力時の住所自動補完機能
        $('#postal_code').on('blur', function() {
            var postal_code = $(this).val();
            if (postal_code.match(/^\d{3}-\d{4}$/)) {
                // zipcloudAPIを使用して住所を取得
                $.ajax({
                    url: 'https://zipcloud.ibsnet.co.jp/api/search',
                    type: 'GET',
                    dataType: 'jsonp',
                    data: {
                        zipcode: postal_code
                    },
                    success: function(response) {
                        if (response.results && response.results.length > 0) {
                            var result = response.results[0];
                            var address = result.address1 + result.address2 + result.address3;
                            $('#company_address').val(address);
                        }
                    },
                    error: function() {
                        console.log('住所の取得に失敗しました');
                    }
                });
            }
        });
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