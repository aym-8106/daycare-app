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
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo admin_url(); ?>company/add"><i class="fa fa-plus"></i>追加</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
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
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                    <h3 class="box-title">事業所一覧</h3>
                    <div class="box-tools">
                        <form action="<?php echo admin_url() ?>company" method="POST" id="searchList">
                            <div class="input-group">
                              <input type="text" name="searchText" value="<?php echo $search; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="検索"/>
                              <div class="input-group-btn">
                                <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">

                    <div class="" style="padding-left: 20px">
                        <?php echo $list_cnt;?>件中（<?php echo $start_page;?>件～<?php echo $end_page;?>件）
                    </div>

                  <table class="table table-hover">
                    <tr>
                        <th>№</th>
                        <th>事業所名</th>
                        <th>事業所番号</th>
                        <th>郵便番号</th>
                        <th>住所</th>
                        <th class="text-center" style="width: 200px">アクション</th>
                    </tr>
                    <?php
                    if(!empty($list))
                    {
                        foreach($list as $key=> $record)
                        {
                    ?>
                    <tr>
                        <td><?php echo $key+$start_page ?></td>
                        <td>
                            <a href="<?php echo admin_url().'company/edit/'.$record['company_id']; ?>" title="">
                                <?php echo $record['company_name'] ?></a>
                        </td>
                        <td><?php echo isset($record['company_number']) ? $record['company_number'] : '<span class="text-muted">未設定</span>'; ?></td>
                        <td><?php echo isset($record['postal_code']) ? $record['postal_code'] : '<span class="text-muted">未設定</span>'; ?></td>
                        <td style="max-width: 200px; word-break: break-all">
                            <?php echo isset($record['company_address']) ? mb_substr($record['company_address'], 0, 30) . (mb_strlen($record['company_address']) > 30 ? '...' : '') : '<span class="text-muted">未設定</span>'; ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-warning" href="<?php echo admin_url().'company/staff/'.$record['company_id']; ?>" title="職員管理"><i class="fa fa-users"></i></a>
                            <a class="btn btn-sm btn-info" href="<?php echo admin_url().'company/edit/'.$record['company_id']; ?>" title="編集"><i class="fa fa-pencil"></i></a>
                            <a class="btn btn-sm btn-danger deleteUser" href="#" data-userid="<?php echo $record['company_id']; ?>" title="削除"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                  </table>
                  
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php echo $this->pagination->create_links(); ?>
                </div>
              </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
