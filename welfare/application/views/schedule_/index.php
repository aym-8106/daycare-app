<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>スケジュールページ</h1>
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
                    <div id="external-events">
                      <p><strong>設定可能な使用者</strong></p>
                      <?php foreach($patient AS $key => $value) {?>
                        <div class="fc-event" data-id="<?php echo $value['id']; ?>" data-title="<?php echo $value['patient_name']; ?>"><?php echo $value['patient_name']; ?>(<?php echo $value['patient_usefrom']; ?>~<?php echo $value['patient_useto']; ?>)</div>
                      <?php } ?>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ja.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/interaction.global.min.js"></script>
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

    document.addEventListener('DOMContentLoaded', function () {
        // Wrap the async code in an async function
        (async function () {
            // Make external events draggable
            new FullCalendar.Draggable(document.getElementById('external-events'), {
                itemSelector: '.fc-event',
                eventData: function (eventEl) {
                    return {
                        title: eventEl.getAttribute('data-title'),
                    };
                },
                longPressDelay: 300, // Adjust for touch devices (long press to drag)
            });
            const container = document.getElementById('calendar-container');
            const cond_date = document.getElementById('cond_date').value;

            const calendars = [];

            // Fetch members and create calendars
            const response = await fetch(baseURL + 'schedule/get_members');
            const members = await response.json();
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
                    editable: true,
                    droppable: true,
                    height: 'auto',
                    headerToolbar: false,
                    allDaySlot: false,
                    slotMinTime: "09:00:00",
                    slotMaxTime: "18:00:00",
                    slotDuration: "00:30:00",
                    events: baseURL + 'schedule/get_events?member_id=' + member.staff_id + '&cond_date=' + cond_date,
                    eventReceive: function (info) {
                        // Handle external event drop
                        fetch(baseURL + 'schedule/add_event', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                title: info.event.title,
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : null,
                                member_id: member.staff_id,
                            }),
                        }).then(() => calendar.refetchEvents());
                    },
                    eventDrop: function (info) {
                        // Handle event move within the calendar
                        fetch(baseURL + 'schedule/update_event', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: info.event.id,
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : null,
                            }),
                        }).then(response => {
                            if (!response.ok) {
                                alert("イベントの更新に失敗しました。");
                                info.revert(); // Revert the event to its original position if the update fails
                            }
                        });
                    },
                });

                calendar.render();
                calendars.push(calendar);
            });
        })(); // Immediately invoke the async function
    });
</script>

<style>
  .cal-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem;
  }
  #external-events {
    padding: 10px;
    width: 100%; /* Adjust for smaller screens */
    background: #f4f4f4;
    border: 1px solid #ddd;
    margin-bottom: 10px;
  }
  .fc-event {
      margin: 5px 0;
      padding: 1px; /* Increase padding for easier touch interaction */
      background: #dd9000;
      color: white;
      cursor: pointer;
      touch-action: none; /* Prevent default touch behavior */
  }
</style>
