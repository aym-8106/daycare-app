<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>日報管理</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>report/index" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group text-center">
                        <div class="col-lg-12 col-sm-12" style="display: flex;justify-content: center;">
                            <input type="text" id="cond_date" name="cond_date" class="datepicker form-control text-center" readonly value="<?php echo ($cond_date); ?>" style="width: 150px;">
                        </div>
                    </div>
                    <div class="row">
                      <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">日報一覧</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                          <table class="table table-hover">
                            <tr>
                                <th style="vertical-align: middle;">№</th>
                                <th style="vertical-align: middle;">利用者名</th>
                                <th style="vertical-align: middle;">区分</th>
                                <th style="vertical-align: middle;">時間</th>
                            </tr>
                            <?php
                            if(!empty($daily))
                            {
                                foreach($daily as $key=> $record)
                                {
                            ?>
                            <tr>
                                <td><?php echo $key+1 ?></td>
                                <td><?php echo $record['patient_name'].'さん' ?></td>
                                <td><?php echo $patientcuretype[$record['patient_curetype']] ?></td>
                                <td><?php echo $record['patient_usefrom'].'~'.$record['patient_useto'] ?></td>
                            </tr>
                            <?php
                                }
                            } else {?>
                                <tr>
                                    <td class="text-center" colspan="4">現実的な資料はありません。</td>
                                </tr>
                            <?php
                            }
                            ?>
                          </table>
                        </div><!-- /.box -->
                      </div>
                    </div>
                    <div class="row">
                      <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">月報一覧</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                          <table class="table table-hover">
                            <tr>
                                <th style="vertical-align: middle;">№</th>
                                <th style="vertical-align: middle;">月</th>
                                <th style="vertical-align: middle;">利用者名</th>
                                <th style="vertical-align: middle;">区分</th>
                                <th style="vertical-align: middle;">時間</th>
                            </tr>
                            <?php
                            if (!empty($monthly)) {
                                $no = 1;
                                $num1 = 0;
                                $minute1 = 0;
                                $curetype1 = "";
                                $num2 = 0;
                                $minute2 = 0;
                                $curetype2 = "";
                                $prev_date = ''; // to store the previous post_date

                                foreach ($monthly as $key => $record) {
                                    // Count patient_curetype
                                    $usefrom = new DateTime($record['patient_usefrom']);
                                    $useto = new DateTime($record['patient_useto']);
                                    if ($record['patient_curetype'] == 1) {
                                        $num1++;
                                        $minute1 += ($useto->getTimestamp() - $usefrom->getTimestamp()) / 60;
                                        $curetype1 = $patientcuretype[$record['patient_curetype']];
                                    } elseif ($record['patient_curetype'] == 2) {
                                        $num2++;
                                        $minute2 += ($useto->getTimestamp() - $usefrom->getTimestamp()) / 60;
                                        $curetype2 = $patientcuretype[$record['patient_curetype']];
                                    }

                                    // Check if this is the first record or the post_date is different from the previous one
                                    if ($key == 0 || $record['post_date'] != $prev_date) {
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $record['post_date']; ?></td>
                                            <td><?php echo $record['patient_name'] . 'さん'; ?></td>
                                            <td><?php echo $patientcuretype[$record['patient_curetype']]; ?></td>
                                            <td><?php echo $record['patient_usefrom'] . '〜' . $record['patient_useto']; ?></td>
                                        </tr>
                                        <?php
                                    } else {
                                        // post_date is the same as previous
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td><?php echo $record['post_date']; ?></td>
                                            <td><?php echo $record['patient_name'] . 'さん'; ?></td>
                                            <td><?php echo $patientcuretype[$record['patient_curetype']]; ?></td>
                                            <td><?php echo $record['patient_usefrom'] . '〜' . $record['patient_useto']; ?></td>
                                        </tr>
                                        <?php
                                    }

                                    $prev_date = $record['post_date'];
                                } ?>
                                <tr>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                </tr>
                                <tr>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                </tr>
                                <tr>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                    <td>　</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>集計</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>総訪問件数</td>
                                    <td><?php echo $num1+$num2 ?>回</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <?php
                                if($curetype1 != "") {?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo $curetype1 ?></td>
                                        <td><?php echo $minute1 ?>分</td>
                                        <td></td>
                                        <td></td>
                                    </tr><?php
                                }
                                ?>
                                <?php
                                if($curetype2 != "") {?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo $curetype2 ?></td>
                                        <td><?php echo $minute2 ?>分</td>
                                        <td></td>
                                        <td></td>
                                    </tr><?php
                                }
                            } else { ?>
                                <tr>
                                    <td class="text-center" colspan="5">現実的な資料はありません。</td>
                                </tr>
                            <?php
                            }
                            ?>
                          </table>
                        </div><!-- /.box -->
                      </div><!-- /.box -->
                    </div>
                </form>
            </div><!-- ./col -->
        </div>
    </section>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.js.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>


<script type="text/javascript">
    $( function () {
      $('.datepicker').datepicker({
        locale: 'ja',
        format: 'yyyy-mm-dd',
        autoclose: true,
      });
    });
</script>

<script>    
    $('#cond_date').on('change', function () {
        document.getElementById('form1').submit();
    });
</script>
