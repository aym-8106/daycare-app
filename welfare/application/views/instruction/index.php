<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> 指示書設定
        <small>作成, 編集, 削除</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-1 text-center">
            </div>
            <form action="<?= base_url('instruction/index') ?>" method="POST" id="dateForm">
                <div class="col-xs-8 text-center" style="padding: 0px;">
                    <div class="form-group text-center" style="padding: 0px; display: flex; flex-direction: row;align-items: center;justify-content: center;">
                        <input type="text" id="start_date" name="start_date" class="datepicker form-control text-center" readonly value="<?php echo $start_date ?>" style="width: 110px;">～ 
                        <input type="text" id="end_date" name="end_date" class="datepicker form-control text-center" readonly value="<?php echo $end_date ?>" style="width: 110px;">
                    </div>
                </div>
            </form>
            <div class="form-group" style="padding: 0px;">
                <a class="btn btn-primary" href="<?php echo base_url(); ?>instruction/add"><i class="fa fa-plus"></i>追加</a>
            </div>
            <div class="col-xs-1" style="padding: 0px;"></div>
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
                    <h3 class="box-title">指示書一覧</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>instruction" method="POST" id="searchList">
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
                        <input name="id" id="id" type="hidden">
                        <input name="use_flag" id="use_flag" type="hidden">
                    </form>
                    <table class="table table-hover">
                        <tr>
                            <th class="text-center">№</th>
                            <th class="text-center">利用者名</th>
                            <th class="text-center">指示期間開始</th>
                            <th class="text-center">指示期間終了</th>
                            <th class="text-center">指示元</th>
                            <th class="text-center">主治医</th>
                            <th class="text-center" style="width: 200px;">アクション</th>
                        </tr>
                        <?php
                        if(!empty($list))
                        {
                            foreach($list as $key=> $record)
                            {
                            if($today >= $record['instruction_start'] && $today <= $record['instruction_end']) { ?>
                            <tr class = "In_progress_tr">
                                <td><?php echo $key+$start_page ?></td>
                                <td>
                                    <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                        <input type="hidden" name="mode" value="edit">
                                        <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                        <button type="submit" class="name-button">
                                            <?php echo $record['patient_name'] ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo $record['instruction_start'] ?></td>
                                <td><?php echo $record['instruction_end'] ?></td>
                                <td><?php echo $record['company_name'] ?></td>
                                <td><?php echo $record['staff_name'] ?></td>
                                <td class="text-center">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                            <input type="hidden" name="mode" value="edit">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-info ">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/add" method="post">
                                            <input type="hidden" name="mode" value="copy">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/delete" method="post">
                                            <input type="hidden" name="mode" value="delete">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-danger ">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/print" method="post">
                                            <input type="hidden" name="mode" value="print">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-warning ">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            } else if($today > $record['instruction_end']){ ?>
                            <tr class = "After_progress_tr">
                                <td><?php echo $key+$start_page ?></td>
                                <td>
                                    <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                        <input type="hidden" name="mode" value="edit">
                                        <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                        <button type="submit" class="name-button">
                                            <?php echo $record['patient_name'] ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo $record['instruction_start'] ?></td>
                                <td><?php echo $record['instruction_end'] ?></td>
                                <td><?php echo $record['company_name'] ?></td>
                                <td><?php echo $record['staff_name'] ?></td>
                                <td class="text-center">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                            <input type="hidden" name="mode" value="edit">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-info ">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/add" method="post">
                                            <input type="hidden" name="mode" value="copy">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/delete" method="post">
                                            <input type="hidden" name="mode" value="delete">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-danger ">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/print" method="post">
                                            <input type="hidden" name="mode" value="print">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-warning ">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            } else { ?>
                            <tr class = "Before_progress_tr">
                                <td><?php echo $key+$start_page ?></td>
                                <td>
                                    <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                        <input type="hidden" name="mode" value="edit">
                                        <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                        <button type="submit" class="name-button">
                                            <?php echo $record['patient_name'] ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo $record['instruction_start'] ?></td>
                                <td><?php echo $record['instruction_end'] ?></td>
                                <td><?php echo $record['company_name'] ?></td>
                                <td><?php echo $record['staff_name'] ?></td>
                                <td class="text-center">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/edit" method="post">
                                            <input type="hidden" name="mode" value="edit">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-info ">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/add" method="post">
                                            <input type="hidden" name="mode" value="copy">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </form>
                                        <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/delete" method="post">
                                            <input type="hidden" name="mode" value="delete">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-danger ">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        <!-- <form role="form" id="addUser" action="<?php echo base_url() ?>instruction/print" method="post">
                                            <input type="hidden" name="mode" value="print">
                                            <input type="hidden" name="instructionId" value="<?php echo $record['instruction_id'];?>">
                                            <button type="submit" class="btn btn-sm btn-warning ">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </form> -->
                                    </div>
                                </td>
                            </tr>
                            <?php
                        } 
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    $( function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
        $('#cond_date').on('change', function () {
            $('#form1').submit();
        });
    });
    function onAccept(id,use_flag){
        $('#id').val(id);
        $('#use_flag').val(use_flag);
        $('#mode').val('update');
        $('#form1').submit();
    }

    $(document).ready(function () {
        $('#start_date, #end_date').on('change', function () {
            $('#dateForm').submit();
        });
    });
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

