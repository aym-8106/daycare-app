<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i> 利用者設定
        <small>作成, 編集, 削除</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>setting/patientadd"><i class="fa fa-plus"></i>追加</a>
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
                    <h3 class="box-title">利用者一覧</h3>
                    <div class="box-tools">
                        <form action="<?php echo base_url() ?>setting/patient" method="POST" id="searchList">
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
                        <th rowspan="3" style="vertical-align: middle;">№</th>
                        <th rowspan="3" style="vertical-align: middle;">利用者名</th>
                        <th rowspan="3" style="vertical-align: middle;">住所</th>
                        <th rowspan="3" style="vertical-align: middle;">登録日</th>
                        <?php
                            for($i=1; $i<8; $i++)
                            { ?>
                                <th colspan="4" style="vertical-align: middle; text-align: center;"><?= $weekdays[$i] ?>曜日</th>
                        <?php
                            }?>
                        <th rowspan="3" class="text-center" style="width: 200px; vertical-align: middle;">アクション</th>
                    </tr>
                    <tr>
                        <?php
                            for($i=0; $i<7; $i++)
                            { ?>
                                <th rowspan="2" style="vertical-align: middle;">サービス</th>
                                <th colspan="2" class="text-center">所要時間</th>
                                <th rowspan="2" style="vertical-align: middle;">頻度</th>
                        <?php
                            }?>
                    </tr>
                    <tr>
                        <?php
                            for($i=0; $i<7; $i++)
                            { ?>
                                <th>開始時間</th>
                                <th>終了時間</th>
                        <?php
                            }?>
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
                            <form role="form" id="addUser" action="<?php echo base_url() ?>setting/patientedit" method="post">
                                <input type="hidden" name="mode" value="edit">
                                <input type="hidden" name="userId" value="<?php echo $record['id'];?>">
                                <button type="submit" class="name-button">
                                    <?php echo $record['patient_name'] ?>
                                </button>
                            </form>
                        </td>
                        <td><?php echo $record['patient_addr'] ?></td>
                        <td><?php echo $record['patient_regdate']; ?></td>
                        <!-- <td><?php //echo $weekdays[$record['patient_date']]; ?></td> -->
                        <td><?php echo $patientcuretype[$record['patient_curetype']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom">
                                <?php echo $record['patient_usefrom'] == '' || $record['patient_usefrom'] == '00:00' ? 'なし' : $record['patient_usefrom']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto">
                                <?php echo $record['patient_useto'] == '' || $record['patient_useto'] == '00:00' ? 'なし' : $record['patient_useto']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype2']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom2">
                                <?php echo $record['patient_usefrom2'] == '' || $record['patient_usefrom2'] == '00:00' ? 'なし' : $record['patient_usefrom2']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto2">
                                <?php echo $record['patient_useto2'] == '' || $record['patient_useto2'] == '00:00' ? 'なし' : $record['patient_useto2']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat2']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype3']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom3">
                                <?php echo $record['patient_usefrom3'] == '' || $record['patient_usefrom3'] == '00:00' ? 'なし' : $record['patient_usefrom3']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto3">
                                <?php echo $record['patient_useto3'] == '' || $record['patient_useto3'] == '00:00' ? 'なし' : $record['patient_useto3']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat3']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype4']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom4">
                                <?php echo $record['patient_usefrom4'] == '' || $record['patient_usefrom4'] == '00:00' ? 'なし' : $record['patient_usefrom4']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto4">
                                <?php echo $record['patient_useto4'] == '' || $record['patient_useto4'] == '00:00' ? 'なし' : $record['patient_useto4']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat4']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype5']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom5">
                                <?php echo $record['patient_usefrom5'] == '' || $record['patient_usefrom5'] == '00:00' ? 'なし' : $record['patient_usefrom5']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto5">
                                <?php echo $record['patient_useto5'] == '' || $record['patient_useto5'] == '00:00' ? 'なし' : $record['patient_useto5']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat5']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype6']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom6">
                                <?php echo $record['patient_usefrom6'] == '' || $record['patient_usefrom6'] == '00:00' ? 'なし' : $record['patient_usefrom6']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto6">
                                <?php echo $record['patient_useto6'] == '' || $record['patient_useto6'] == '00:00' ? 'なし' : $record['patient_useto6']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat6']] ?></td>

                        <td><?php echo $patientcuretype[$record['patient_curetype7']] ?></td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_usefrom7">
                                <?php echo $record['patient_usefrom7'] == '' || $record['patient_usefrom7'] == '00:00' ? 'なし' : $record['patient_usefrom7']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="editable" data-id="<?= $record['id'] ?>" data-field="patient_useto7">
                                <?php echo $record['patient_useto7'] == '' || $record['patient_useto7'] == '00:00' ? 'なし' : $record['patient_useto7']; ?>
                            </span>
                        </td>
                        <td><?php echo $patientrepeat[$record['patient_repeat7']] ?></td>

                        <td class="text-center">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                                <form role="form" id="addUser" action="<?php echo base_url() ?>setting/patientedit" method="post">
                                    <input type="hidden" name="mode" value="edit">
                                    <input type="hidden" name="userId" value="<?php echo $record['id'];?>">
                                    <button type="submit" class="btn btn-sm btn-info ">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </form>
                                <form role="form" id="addUser" action="<?php echo base_url() ?>setting/patientdelete" method="post">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="userId" value="<?php echo $record['id'];?>">
                                    <button type="submit" class="btn btn-sm btn-danger ">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
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
        $('#id').val(id);
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
<script>
$(document).on('click', '.editable', function () {
    var span = $(this);
    var originalValue = span.text().trim();
    var field = span.data('field');
    var id = span.data('id');

    var input = $('<input type="text" class="form-control">').val(originalValue);
    span.replaceWith(input);
    input.focus();

    input.on('blur', function () {
        var newValue = input.val();
        var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/; // Validates 00:00 to 23:59

        if (field.includes('usefrom') || field.includes('useto')) {
            if (!timeRegex.test(newValue)) {
                bootoast.toast({
                    message: '時間の形式が正しくありません。00:00 の形式で入力してください。',
                    type:'error',
                    animationDuration: 300,
                });
                newValue = "なし";
                $(this).text(newValue);
                $(this).replaceWith(span);
                return;
            }
        }

        if (newValue !== originalValue) {
            $.ajax({
                url: '<?= base_url() ?>setting/update_patient_field',
                method: 'POST',
                data: {
                    id: id,
                    field: field,
                    value: newValue
                },
                success: function (response) {
                    if (response === 'success') {
                        var newSpan = $('<span class="editable" data-id="' + id + '" data-field="' + field + '">' + newValue + '</span>');
                        input.replaceWith(newSpan);
                    } else {
                        alert('Update failed.');
                        input.replaceWith(span);
                    }
                },
                error: function () {
                    alert('Error while updating.');
                    input.replaceWith(span);
                }
            });
        } else {
            input.replaceWith(span);
        }
    });
});
</script>