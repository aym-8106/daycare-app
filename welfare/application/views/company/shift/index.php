<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-calendar"></i> シフト設定
        <small>作成, 編集, 削除</small>
      </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-1 text-center">
            </div>
            <div class="col-xs-10 text-center" style="padding: 0px;">
                <form action="<?= base_url('company/shift/index') ?>" method="POST" id="dateForm">
                    <div class="form-group text-center" style="padding: 0px; display: flex; flex-direction: row;align-items: center;justify-content: center;">
                            <select id="cond_year" name="cond_year" class="form-control text-center" style="width: 100px;">
                                <?php
                                $currentYear = date('Y');
                                $selectedYear = isset($cond_date) ? date('Y', strtotime($cond_date)) : $currentYear;
                                
                                for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                    $selected = ($year == $selectedYear) ? 'selected' : '';
                                    echo "<option value=\"{$year}\" {$selected}>{$year}</option>";
                                }
                                ?>
                            </select>
                            
                            <select id="cond_month" name="cond_month" class="form-control text-center" style="width: 90px;">
                                <?php
                                $selectedMonth = isset($cond_date) ? date('m', strtotime($cond_date)) : date('m');
                                
                                for ($month = 1; $month <= 12; $month++) {
                                    $monthValue = str_pad($month, 2, '0', STR_PAD_LEFT);
                                    $selected = ($monthValue == $selectedMonth) ? 'selected' : '';
                                    echo "<option value=\"{$monthValue}\" {$selected}>{$monthValue}</option>";
                                }
                                ?>
                            </select>
                            
                            <!-- Hidden input to store the combined date value -->
                            <input type="hidden" id="cond_date" name="cond_date" value="<?php echo $cond_date ?>">
                    </div>
                    <div style="display:flex;justify-content: center; gap: 10px 20px;flex-wrap: wrap;">  
                        <?php $option_cnt = 0; ?>
                        <?php if(!empty($loggedin_user['shift_option1'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option1'] ?></p><?php } ?>
                        <?php if(!empty($loggedin_user['shift_option2'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option2'] ?></p><?php } ?>
                        <?php if(!empty($loggedin_user['shift_option3'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option3'] ?></p><?php } ?>
                        <?php if(!empty($loggedin_user['shift_option4'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option4'] ?></p><?php } ?>
                        <?php if(!empty($loggedin_user['shift_option5'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option5'] ?></p><?php } ?>
                        <?php if(!empty($loggedin_user['shift_option6'])) { $option_cnt++ ?><p><?php echo $loggedin_user['shift_option6'] ?></p><?php } ?>
                    </div>  
                </form>
            </div>
            <div class="col-xs-1" style="padding: 0px;"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-body table-responsive no-padding">
                    <form name="form1" id="form1" method="post">
                        <input name="mode" id="mode" type="hidden">
                        <input name="id" id="id" type="hidden">
                        <input name="use_flag" id="use_flag" type="hidden">
                    </form>
                    <table class="table table-hover shift-table">  
                        <thead>  
                            <tr>  
                                <?php
                                $year = isset($cond_date) ? date('Y', strtotime($cond_date)) : date('Y');
                                $month = isset($cond_date) ? date('m', strtotime($cond_date)) : date('m');
                                $monthName = date('n月', strtotime("$year-$month-01"));
                                
                                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                
                                echo "<th>{$monthName}</th>";
                                
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    echo "<th>{$day}</th>";
                                }
                                
                                echo "<th>合計</th><th>週平均</th>";
                                ?>
                            </tr>  
                            <tr class="day-of-week">  
                                <?php
                                echo "<th></th>";
                                
                                $dayNames = ['日', '月', '火', '水', '木', '金', '土'];
                                
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    $date = "$year-$month-$day";
                                    $dayOfWeek = date('w', strtotime($date));
                                    $dayName = $dayNames[$dayOfWeek];
                                    
                                    $class = ($dayOfWeek == 0 || $dayOfWeek == 6) ? 'class="weekend"' : '';
                                    
                                    echo "<th {$class}>{$dayName}</th>";
                                }
                                
                                echo "<th></th><th></th>";
                                ?>
                            </tr>  
                        </thead>  
                        <tbody>  
                            <?php
                            // Group data by staff_id
                            $staffData = [];
                            foreach ($data_list as $row) {
                                $staffData[$row['staff_id']][] = $row;
                            }
                            
                            // Process each staff member
                            foreach ($staffData as $staffId => $staffShifts) {
                                // Get staff name from the first record
                                $staffName = $staffShifts[0]['staff_name'];
                                
                                // Create an array to hold shift data for each day
                                $shiftsByDay = [];
                                $callFlagsByDay = [];
                                
                                // Organize data by day
                                foreach ($staffShifts as $shift) {
                                    $day = (int)date('d', strtotime($shift['shift_date']));
                                    $shiftsByDay[$day] = $shift['shift_option'];
                                    $callFlagsByDay[$day] = $shift['call_flag'];
                                }
                                
                                // Calculate total on-call days
                                $totalOnCall = array_sum($callFlagsByDay);
                                
                                // First row - shift numbers
                                echo "<tr>";
                                echo "<td class='staff-name''>{$staffName}</td>";
                                
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    $shiftOption = isset($shiftsByDay[$day]) ? $shiftsByDay[$day] : '';
                                    echo "<td id='sel_{$staffId}' data-shiftdate='{$day}'>{$shiftOption}</td>";
                                }
                                
                                // Empty cells for total and average in shift row
                                echo "<td></td><td></td>";
                                echo "</tr>";
                                
                                // Second row - on-call markers
                                echo "<tr>";
                                echo "<td class='staff-name'></td>";
                                
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    $callFlag = isset($callFlagsByDay[$day]) ? $callFlagsByDay[$day] : 0;
                                    if ($callFlag == 1) {
                                        echo "<td id='call_{$staffId}' data-shiftdate='{$day}'><span class='oncall'>○</span></td>";
                                    } else {
                                        echo "<td id='call_{$staffId}' data-shiftdate='{$day}'></td>";
                                    }
                                }
                                
                                // Total and average for on-call row
                                echo "<td>{$totalOnCall}</td>";
                                echo "<td></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
    document.getElementById('cond_year').addEventListener('change', function() {
        updateCondDate();
        document.getElementById('dateForm').submit();
    });
    
    document.getElementById('cond_month').addEventListener('change', function() {
        updateCondDate();
        document.getElementById('dateForm').submit();
    });

    function updateCondDate() {
        var year = document.getElementById('cond_year').value;
        var month = document.getElementById('cond_month').value;
        document.getElementById('cond_date').value = year + '-' + month + '-01';
    }

    window.onload = function() {
        updateCondDate();
        setupTableInteractions();
        updateAllTotals(); 
    };

    document.getElementById('cond_year').addEventListener('change', function() {
        updateCondDate();
        document.getElementById('dateForm').submit();
    });
    
    document.getElementById('cond_month').addEventListener('change', function() {
        updateCondDate();
        document.getElementById('dateForm').submit();
    });

    function updateCondDate() {
        var year = document.getElementById('cond_year').value;
        var month = document.getElementById('cond_month').value;
        document.getElementById('cond_date').value = year + '-' + month + '-01';
    }

    // Initialize the hidden input with the current selections
    window.onload = function() {
        updateCondDate();
        setupTableInteractions();
        updateAllTotals(); // Calculate initial totals
    };

    // New function to handle table cell interactions
    function setupTableInteractions() {
        const shiftTable = document.querySelector('.shift-table');
        if (!shiftTable) return;

        shiftTable.addEventListener('click', function(event) {
            const cell = event.target.closest('td');
            if (!cell) return; // Only handle clicks on td elements
            
            const row = cell.parentElement;
            const rowIndex = Array.from(row.parentElement.children).indexOf(row);
            const isNameCell = cell.classList.contains('staff-name');
            const isTotalCell = cell.cellIndex === row.cells.length - 2; // Second to last cell is total
            const isAverageCell = cell.cellIndex === row.cells.length - 1; // Last cell is average
            
            // Don't handle clicks on special cells
            if (isNameCell || isTotalCell || isAverageCell) return;
            
            // Handle cells in odd rows (staff shift numbers)
            if (rowIndex % 2 === 0) {
                handleShiftNumberCell(cell);
            } 
            // Handle cells in even rows (on-call markers)
            else {
                handleOnCallCell(cell, row);
            }
        });
    }

    function handleShiftNumberCell(cell) {
        // Create select element if it doesn't exist
        if (!cell.querySelector('select')) {
            const currentValue = cell.textContent.trim();
            
            // Create select element
            const select = document.createElement('select');
            select.className = 'form-control shift-select';
            
            // Add options 1-6
            
            var option_cnt = <?php echo json_encode($option_cnt); ?>;
            for (let i = 1; i <= option_cnt; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                if (currentValue === String(i)) {
                    option.selected = true;
                }
                select.appendChild(option);
            }
            
            // Add empty option
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '';
            if (currentValue === '') {
                emptyOption.selected = true;
            }
            select.appendChild(emptyOption);
            
            // Clear cell and add select
            cell.textContent = '';
            cell.appendChild(select);
            
            // Focus the select
            select.focus();
            
            // Handle change event
            select.addEventListener('change', function() {
                const value = this.value;
                cell.textContent = value; // Replace select with just the value
                
                saveShiftChange(cell, value);
            });
            
            // Handle blur event
            select.addEventListener('blur', function() {
                const value = this.value;
                cell.textContent = value; // Replace select with just the value
            });
        }
    }
    function saveShiftChange(cell, value) {
        const staffId = cell.id.split('_')[1];
        const shiftDate = cell.dataset.shiftdate;
        const year = document.getElementById('cond_year').value;
        const month = document.getElementById('cond_month').value;
        
        $.ajax({
            url: '<?= base_url() ?>company/shift/saveShift',
            method: 'POST',
            data: {
                staff_id: staffId,
                shift_date: `${year}-${month}-${shiftDate}`,
                shift_option: value
            },
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
            }
        });
    }
    function handleOnCallCell(cell, row) {
        // Toggle the on-call marker
        const span = cell.querySelector('.oncall');
        var onCallFlag = 0;
        if (span) {
            // Remove the marker if it exists
            cell.textContent = '';
        } else {
            // Add the marker if it doesn't exist
            const onCallSpan = document.createElement('span');
            onCallSpan.className = 'oncall';
            onCallSpan.textContent = '○';
            cell.textContent = '';
            cell.appendChild(onCallSpan);
            onCallFlag = 1;
        }
        
        // Update the total for this row
        updateTotalForRow(row);
        
    
        // Get year and month values from the form
        const year = document.getElementById('cond_year').value;
        const month = document.getElementById('cond_month').value;
        
        $.ajax({
            url: '<?= base_url() ?>company/shift/saveoncall',
            method: 'POST',
            data: {
                staff_id: cell.id.split('_')[1],
                shift_date: `${year}-${month}-${cell.dataset.shiftdate}`,
                call_flag: onCallFlag
            },
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
            }
        });
    }

    // Function to update the total for a specific row
    function updateTotalForRow(row) {
        const totalCell = row.cells[row.cells.length - 2]; // Second to last cell is total
        
        // Count the number of cells with the "oncall" class
        let count = 0;
        for (let i = 1; i < row.cells.length - 2; i++) { // Skip name cell and total/average cells
            if (row.cells[i].querySelector('.oncall')) {
                count++;
            }
        }
        
        // Update the total cell
        totalCell.textContent = count > 0 ? count : '';
    }

    // Function to update totals for all rows
    function updateAllTotals() {
        const rows = document.querySelectorAll('.shift-table tbody tr');
        
        rows.forEach(row => {
            // Only process even-indexed rows (on-call rows)
            const rowIndex = Array.from(row.parentElement.children).indexOf(row);
            if (rowIndex % 2 === 1) { // Even rows in 0-based index are odd in 1-based
                updateTotalForRow(row);
            }
        });
    }
</script>

