<div class="content-wrapper">
    <section class="content-header">
        <h1>決済キャンセル</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-warning">
                    <div class="box-header with-border text-center">
                        <h3><i class="fa fa-exclamation-triangle" style="color: #f39c12; font-size: 48px;"></i></h3>
                        <h3 class="box-title">決済がキャンセルされました</h3>
                    </div>
                    <div class="box-body text-center">
                        <p>決済処理がキャンセルされました。</p>
                        <p>もう一度お試しになる場合は、下のボタンをクリックしてください。</p>
                        <hr>
                        <a href="<?php echo base_url('company/payment'); ?>" class="btn btn-primary">
                            <i class="fa fa-credit-card"></i> 料金プランに戻る
                        </a>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-default">
                            <i class="fa fa-home"></i> ダッシュボードに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>