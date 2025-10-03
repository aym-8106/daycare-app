<div class="content-wrapper">
    <section class="content-header">
        <h1>決済完了</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-success">
                    <div class="box-header with-border text-center">
                        <h3><i class="fa fa-check-circle" style="color: #00a65a; font-size: 48px;"></i></h3>
                        <h3 class="box-title">決済が完了しました！</h3>
                    </div>
                    <div class="box-body text-center">
                        <p>サブスクリプションの登録が完了しました。</p>
                        <p>ご利用ありがとうございます。</p>
                        <hr>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-primary">
                            <i class="fa fa-home"></i> ダッシュボードに戻る
                        </a>
                        <a href="<?php echo base_url('company/payment-history'); ?>" class="btn btn-info">
                            <i class="fa fa-history"></i> 決済履歴を見る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>