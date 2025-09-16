<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-user-circle"></i> プロフィール
        <small>情報変更 </small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->

            <div class="col-md-12">

                <div class="box">
                <?php
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

                <?php
                $noMatch = $this->session->flashdata('nomatch');
                if($noMatch)
                {
                    ?>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('nomatch'); ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
                <form action="<?php echo company_url() ?>profile" method="post" id="editProfile" role="form">
                    <input type="hidden" name="mode" value="save">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">事業所名：</label>
                                    <?php echo set_value('company_name', $company['company_name']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">メールアドレス：</label>
                                    <?php echo set_value('company_email', $company['company_email']); ?>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="old_password">有効日：</label>
                                    <?php echo set_value('payment_date', date("Y-m-d", strtotime($company['payment_date']))); ?>
                                    <?php 
                                        if(date("Y-m-d") > date("Y-m-d", strtotime($company['payment_date']))) {
                                            echo '<small class="label bg-red">満了</small>';
                                        } else {
                                            echo '<small class="label bg-blue">有効</small>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
                        <stripe-pricing-table pricing-table-id="<?= $this->config->item('stripe')['pricing_table_id1']; ?>"
                        publishable-key="<?= $this->config->item('stripe')['secret_key']; ?>">
                        </stripe-pricing-table>

                        <stripe-pricing-table pricing-table-id="<?= $this->config->item('stripe')['pricing_table_id12']; ?>"
                        publishable-key="<?= $this->config->item('stripe')['secret_key']; ?>">
                        </stripe-pricing-table>
                        <!--a href="<?= $this->config->item('stripe')['paymentlink']; ?>" class="btn btn-primary">購入</a-->
                    </div>
                </form>
                </div>

            </div>
        </div>    
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>