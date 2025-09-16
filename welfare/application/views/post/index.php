<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>申し送りページ</h1>
    </section>
    
    <section class="content">
        <div class="row mb-3">
            <div class="col-xs-12 text-right">
                <form role="form" id="addUser" action="<?php echo base_url() ?>post/postadd" method="post">
                    <input type="hidden" name="mode" value="insert">
                    <!-- <div class="form-group text-center top-form1"> -->
                        <!-- <div class="col-lg-10 col-sm-10 top_status"></div> -->
                        <!-- <div class="col-lg-2 col-sm-2 top_status"> -->
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i>追加
                            </button>
                        <!-- </div> -->
                    <!-- </div> -->
                </form>
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
                    <h3 class="box-title">申し送り一覧</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>post/index" method="POST" id="searchList">
                            <!-- <select name="search_id" id="search_id"> -->
                                <!-- <option value="">選択してください</option> -->
                                <!-- <?php //foreach ($patient as $key => $value): ?> -->
                                    <!-- <option value="<?php //echo $value['patient_id'] ?>"><?php //echo $value['patient_name'] ?></option> -->
                                <!-- <?php //endforeach; ?> -->
                            <!-- </select> -->
                            <!-- <input type="text" id="search_date" name="search_date" class="datepicker_search form-control" readonly value="<?php //echo date('Y-m-d'); ?>"> -->
                            <div class="input-group">
                              <input type="text" name="searchText" value="<?php echo $search; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="検索"/>
                              <div class="input-group-btn">
                                <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">

                    <div class="" style="padding-left: 20px">
                        <?php echo $list_cnt;?>件中（<?php echo $start_page;?>件～<?php echo $end_page;?>件）
                    </div>

                    <form name="form1" id="form1" method="post">
                        <input name="mode" id="mode" type="hidden">
                        <input name="id" id="id" type="hidden">
                        <input name="use_flag" id="use_flag" type="hidden">
                    </form>
                    <table class="table table-hover">
                        <tr>
                            <th style="vertical-align: middle;">№</th>
                            <th style="vertical-align: middle;">利用者名</th>
                            <th style="vertical-align: middle;">日付</th>
                            <th style="vertical-align: middle;">開始時間</th>
                            <th style="vertical-align: middle;">終了時間</th>
                            <th style="vertical-align: middle;">スタッフ</th>
                            <th style="vertical-align: middle;">申し送り内容</th>
                            <th class="text-center" style="width: 200px; vertical-align: middle;">アクション</th>
                        </tr>
                        <?php
                        if(!empty($list))
                        {
                            foreach($list as $key=> $record)
                            {
                        ?>
                        <tr>
                            <td><?php echo $key+1 ?></td>
                            <td><?php echo $record['patient_name'] ?></td>
                            <td><?php echo $record['schedule_date'] ?></td>
                            <td><?php echo $record['schedule_start_time'] ?></td>
                            <td><?php echo $record['schedule_end_time'] ?></td>
                            <td><?php echo $record['staff_name'] ?></td>
                            <td style="width: 200px !important; word-break: break-word; overflow-wrap: break-word; white-space: normal;">
                                <?php echo nl2br(htmlspecialchars($record['post_content'], ENT_QUOTES, 'UTF-8')); ?>
                            </td>
                            <td class="text-center">
                                <div style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                                    <form role="form" id="addUser" action="<?php echo base_url() ?>post/postedit" method="post">
                                        <input type="hidden" name="mode" value="edit">
                                        <input type="hidden" name="userId" value="<?php echo $record['id'];?>">
                                        <input type="hidden" name="patientId" value="<?php echo $record['patient_id'];?>">
                                        <button type="submit" class="btn btn-sm btn-info ">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </form>
                                    <form role="form" id="addUser" action="<?php echo base_url() ?>post/postdelete" method="post">
                                        <input type="hidden" name="mode" value="delete">
                                        <input type="hidden" name="userId" value="<?php echo $record['id'];?>">
                                        <input type="hidden" name="post_id" value="<?php echo $record['post_id'];?>">
                                        <button type="submit" class="btn btn-sm btn-danger ">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {?>
                            <tr>
                                <td class="text-center" colspan="6">現実的な資料はありません。</td>
                            </tr>
                        <?php
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<!-- 
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.js.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/> -->

<script type="text/javascript">
    $( function () {
      $('.datepicker').datepicker({
        locale: 'ja',
        format: 'yyyy-mm-dd',
        autoclose: true,
      });

      $('.datepicker_search').datepicker({
        locale: 'ja',
        format: 'yyyy-mm-dd',
        autoclose: true,
      });
      
    //   document.getElementById('post_staff').addEventListener('change', function () {
    //     document.getElementById('form1').submit();
    //   });
    });
</script>
