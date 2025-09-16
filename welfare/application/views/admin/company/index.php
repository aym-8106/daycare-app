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

                    <form name="form1" id="form1" method="post">
                        <input name="mode" id="mode" type="hidden">
                        <input name="company_id" id="company_id" type="hidden">
                        <input name="use_flag" id="use_flag" type="hidden">
                    </form>
                  <table class="table table-hover">
                    <tr>
                        <th>№</th>
                        <th>事業所名</th>
                        <th>メール</th>
                        <th>作成日付</th>
                        <th>更新日付</th>
                        <th>有効期間</th>
                        <th>承認</th>
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
                        <td style="word-break: break-all"><?php echo $record['company_email'] ?></td>
                        <td><?php echo date("Y-m-d", strtotime($record['create_date'])) ?></td>
                        <td><?php echo date("Y-m-d", strtotime($record['update_date'])) ?></td>
                        <td><?php echo date("Y-m-d", strtotime($record['payment_date'])) ?>
                            <?php 
                                if(date("Y-m-d") > date("Y-m-d", strtotime($record['payment_date']))) {
                                    echo '<small class="label bg-red">満了</small>';
                                } else {
                                    echo '<small class="label bg-blue">有効</small>';
                                }
                            ?>
                        </td>
                        <td><?php if($record['use_flag']==1) echo '承認済み'; else echo '未承認';?></td>
                        <td class="text-center">
                            <?php if($record['use_flag']==1){?><a class="btn btn-sm btn-default" href="javascript:;" onclick="onAccept(<?php echo $record['company_id']; ?>,0);" title="拒否する">拒否</a> |
                            <?php }else{?><a class="btn btn-sm btn-primary" href="javascript:;" onclick="onAccept(<?php echo $record['company_id']; ?>,1);" title="承認する">承認</a> |<?php }?>
                            <!--a class="btn btn-sm btn-primary" href="<?= admin_url().'history/index/'.$record['company_id']; ?>" title="ログイン履歴"><i class="fa fa-history"></i></a-->
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
<script type="text/javascript">
    function onAccept(id,use_flag){
        $('#company_id').val(id);
        $('#use_flag').val(use_flag);
        $('#mode').val('update');
        $('#form1').submit();
    }
    // jQuery(document).ready(function(){
    //     jQuery('ul.pagination li a').click(function (e) {
    //         e.preventDefault();
    //         var link = jQuery(this).get(0).href;
    //         var value = link.substring(link.lastIndexOf('/') + 1);
    //         jQuery("#searchList").attr("action", baseURL + "userList/" + value);
    //         jQuery("#searchList").submit();
    //     });
    // });
</script>
