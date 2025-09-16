<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>カレンダー</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <!-- small box -->
                <a href="<?php echo base_url() ?>schedule/edit" class="btn btn-primary btn-flat">編集</a>
            </div><!-- ./col -->
        </div>

        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>schedule/index" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group text-center">
                        <div class="col-lg-12 col-sm-12" style="display: flex;justify-content: center;">
                            <input type="text" id="cond_date" name="cond_date" class="datepicker form-control text-center" readonly value="<?php echo ($cond_date); ?>" style="width: 150px;">
                        </div>
                    </div>
                    <div class="cal-container" id="calendar-container"></div>
                </form>
            </div><!-- ./col -->
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ja.global.min.js"></script>

<script>
    $( function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
        $('#cond_date').on('change', function () {
            $('#form1').submit();
        });
    });
    
  document.addEventListener('DOMContentLoaded', async function () {
    const container = document.getElementById('calendar-container');
    const toolbar = document.getElementById('calendar-toolbar');
    const response = await fetch(baseURL + 'schedule/get_members');
    const members = await response.json();
    const cond_date = document.getElementById('cond_date').value;

    const calendars = [];

    // Create calendars for each member
    members.forEach((member) => {
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.flex = '1';
      wrapper.style.minWidth = '180px';
      wrapper.style.marginRight = '10px';

      const header = document.createElement('div');
      header.textContent = member.staff_name;
      header.style.textAlign = 'center';
      header.style.fontWeight = 'bold';
      header.style.padding = '8px';
      header.style.backgroundColor = '#f0f0f0';
      header.style.borderBottom = '1px solid #ccc';

      const calEl = document.createElement('div');
      calEl.id = `calendar-member-${member.staff_id}`;

      wrapper.appendChild(header);
      wrapper.appendChild(calEl);
      container.appendChild(wrapper);

      const calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'timeGridDay',
        editable: false,
        selectable: false,
        height: 'auto',
        headerToolbar: false,
        allDaySlot: false, // ❌ Hides all-day row
        slotMinTime: "09:00:00", // ⏱ Start time
        slotMaxTime: "18:00:00", // ⏱ End time
        slotDuration: "00:30:00", // ⏱ 30-minute step
        events: baseURL + 'schedule/get_events?member_id=' + member.staff_id + '&cond_date=' + cond_date,
        select: function (info) {
          const title = prompt(`[${member.staff_name}]の予定を入力してください:`);
          if (title) {
            fetch(baseURL + 'schedule/add_event', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                title: title,
                start: info.startStr,
                end: info.endStr,
                member_id: member.staff_id
              }),
            }).then(() => calendar.refetchEvents());
          }
        },
        eventClick: function (info) {
          if (confirm("この予定を削除しますか？")) {
            fetch(baseURL + 'schedule/delete_event', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                id: info.event.id
              }),
            }).then(() => calendar.refetchEvents());
          }
        }
      });

      calendar.render();
      calendars.push(calendar); // Store the calendar instance for later reference
    });
  });
</script>

<style>
  .cal-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem;
  }
</style>
