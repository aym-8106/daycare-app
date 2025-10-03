<div class="content-wrapper">
    <section class="content-header">
        <h1>料金プラン<small>サブスクリプション管理</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">料金プラン一覧</h3>
                    </div>
                    <div class="box-body">
                        <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
                        <stripe-pricing-table
                            pricing-table-id="<?php echo htmlspecialchars($stripe_pricing_table_id); ?>"
                            publishable-key="<?php echo htmlspecialchars($stripe_publishable_key); ?>">
                        </stripe-pricing-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>