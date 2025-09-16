<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>スケジュールページ</h1>
    </section>
    
    <section class="content">
        <!-- <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <a href="<?php echo base_url() ?>schedule/edit" class="btn btn-primary btn-flat">編集</a>
            </div>
        </div> -->

        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>schedule/index" method="POST" id="form1" name="form1" class="form-horizontal calendar-form">
                    <div class="form-group text-center">
                        <div class="col-lg-12 col-sm-12" style="display: flex;justify-content: center;">
                            <input type="text" id="cond_date" name="cond_date" class="datepicker form-control text-center" readonly value="<?php echo ($cond_date); ?>" style="width: 150px;">
                        </div>
                    </div>
                    <div class="cal-container" id="calendar-container"></div> 
                </form>
                <div class="empty-box"></div>
                <div id="external-events">
                    <p><strong>設定可能な使用者</strong></p>
                    <div class="fc-event-box">
                        <?php foreach($patient AS $key => $value) {?>
                        <div class="fc-event" data-id="<?php echo $value['id']; ?>" data-pid="<?php echo $value['id']; ?>" data-title="<?php echo $value['patient_name']; ?>"><?php echo $value['patient_name']; ?>(<?php echo $value['patient_usefrom']; ?>~<?php echo $value['patient_useto']; ?>)</div>
                        <?php } ?>
                    </div>
                </div>
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
            $('#form1').submit(); // ← これだけでOK
        });
    });
    
    const calendars = [];

    document.addEventListener('DOMContentLoaded', function () {
        (async function () {
            
            new FullCalendar.Draggable(document.getElementById('external-events'), {
                itemSelector: '.fc-event',
                eventData: function (eventEl) {
                    return {
                        title: eventEl.getAttribute('data-title'),
                        duration: '00:10',
                        extendedProps: {
                            pId: eventEl.getAttribute('data-pid')
                        }
                    };
                },
                longPressDelay: 300,
            });

            const container = document.getElementById('calendar-container');

            // 既存カレンダーDOMをクリア
            container.innerHTML = '';
            calendars.length = 0;
            
            let clickTimer = null;

            const cond_date = document.getElementById('cond_date').value;
            const response = await fetch(baseURL + 'schedule/get_members');
            const members = await response.json();
            const scheduleData = <?php echo json_encode($schedule_data ?? []); ?>;
            
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

                const memberEvents = scheduleData
                .filter(item => item.staff_id === member.staff_id)
                .map(item => ({
                    title: item.patient_name,
                    start: `${item.schedule_date}T${item.schedule_start_time}`,
                    end: `${item.schedule_date}T${item.schedule_end_time}`,
                    id: item.id,
                    pId: item.patient_id
                }));
                const calendar = new FullCalendar.Calendar(calEl, {
                    initialView: 'timeGridDay',
                    initialDate: cond_date,
                    editable: true,
                    droppable: true,
                    height: 'auto',
                    headerToolbar: false,
                    allDaySlot: false,
                    slotMinTime: "09:00:00",
                    slotMaxTime: "18:00:00",
                    slotDuration: "00:10:00",
                    defaultTimedEventDuration: '00:10:00',
                    forceEventDuration: true,
                    events: memberEvents,
                    
                    eventReceive: function (info) {
                        const eventId = info.event.id;
                        const patientId = info.event.extendedProps.pId;
                        const patientName = info.event.title;
                        let eventStart;
                        let eventEnd;
                        const text = info.draggedEl.innerHTML;

                        // より広くマッチさせる正規表現（全角対応）
                        const match = text.match(/[\（(](\d{1,2}:\d{2})\s*[~～－ー−]\s*(\d{1,2}:\d{2})[\）)]/);
                        if (match) {
                            eventStart = match[1];
                            eventEnd = match[2];
                        } else {
                            eventStart = info.event.start.toTimeString().substring(0, 5);
                            eventEnd = info.event.end.toTimeString().substring(0, 5);
                        }
                        const eventEl = info.draggedEl;
                        
                        const postData = {
                            date: cond_date,
                            schedule_id: eventId,
                            staff_id: member.staff_id,
                            staff_name: member.staff_name,
                            patient_id: patientId,
                            patient_name: patientName,
                            start_time: eventStart,
                            end_time: eventEnd,
                        };
                        fetch(baseURL + 'schedule/add_event', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(postData)
                        })
                        .then(async response => {
                            const text = await response.text();
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error("Invalid JSON response:", text);
                                throw new Error("Server did not return valid JSON");
                            }
                        })
                        .then(data => {
                            if (data.id != 0) {
                                info.event.setProp('id', data.id);
                                eventEl.remove();
                                location.reload();
                            } else {
                                // alert("イベントIDの取得に失敗しました。");
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            // alert("エラーが発生しました。");
                        });
                    },
                    eventDrop: function (info) {
                        const eventId = info.event.id;
                        const eventStart = info.event.start.toTimeString().substring(0, 5);
                        const eventEnd = info.event.end.toTimeString().substring(0, 5);

                        const calendarId = info.el.closest('.fc').id;
                        const staffId = calendarId.replace('calendar-member-', '');

                        const postData = {
                            id: eventId,
                            staff_id: staffId,
                            start_time: eventStart,
                            end_time: eventEnd
                        };

                        fetch(baseURL + 'schedule/update_event_time', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(postData),
                        })
                        .then(async response => {
                            const text = await response.text();
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error("Invalid JSON response:", text);
                                throw new Error("Server did not return valid JSON");
                            }
                        })
                        .then(data => {
                            console.log("Received JSON:", data);
                            if (data.id) {
                                info.event.setProp('id', data.id);
                            } else {
                                alert("イベントIDの取得に失敗しました。");
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert("エラーが発生しました。");
                        });
                    },
                    eventResize: function (info) {
                        const eventId = info.event.id;
                        const eventStart = info.event.start.toTimeString().substring(0, 5);
                        const eventEnd = info.event.end.toTimeString().substring(0, 5);
                        const calendarId = info.el.closest('.fc').id; // e.g., calendar-member-3
                        const staffId = calendarId.replace('calendar-member-', '');

                        const postData = {
                            id: eventId,
                            staff_id: staffId,
                            start_time: eventStart,
                            end_time: eventEnd
                        };

                        fetch(baseURL + 'schedule/update_event_time', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(postData),
                        })
                        .then(async response => {
                            const text = await response.text();
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error("Invalid JSON response:", text);
                                throw new Error("Server did not return valid JSON");
                            }
                        })
                        .then(data => {
                            console.log("Received JSON:", data);
                            if (data.id) {
                                info.event.setProp('id', data.id);
                            } else {
                                alert("イベントIDの取得に失敗しました。");
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert("エラーが発生しました。");
                        });
                    },
                    eventClick: function(info) {
                        // Cancel previous click timer if it's a double-click
                        if (clickTimer !== null) {
                            clearTimeout(clickTimer);
                            clickTimer = null;

                            // 🔁 Double click: Delete the event
                            if (confirm("この予定を削除しますか？")) {
                                fetch(baseURL + 'schedule/delete_event', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        id: info.event.id
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        info.event.remove(); // remove from calendar
                                        window.location.reload();
                                    } else {
                                        alert("削除に失敗しました。");
                                    }
                                })
                                .catch(error => {
                                    console.error("削除エラー:", error);
                                    alert("通信エラーが発生しました。");
                                });
                            }

                        } else {
                            // 🖱 Single click: Show tooltip
                            clickTimer = setTimeout(() => {
                                clickTimer = null;

                                const timeEl = info.el.querySelector('.fc-event-time');
                                const titleEl = info.el.querySelector('.fc-event-title');

                                const time = timeEl ? timeEl.textContent : '';
                                const title = titleEl ? titleEl.textContent : '';

                                const tooltipText = `🕒 ${time}\n📌 ${title}`;

                                // Use native alert for simplicity or customize as needed
                                alert(tooltipText);

                            }, 250); // delay to distinguish single vs double click
                        }
                    }
                });

                calendar.render();
                calendars.push(calendar);
            });
        })();
    });

    window.addEventListener('load', function () {
        const form = document.querySelector('.calendar-form');
        const externalEvents = document.querySelector('#external-events');

        if (form && externalEvents) {
            externalEvents.style.width = form.offsetWidth + 'px';
        }
    });

    window.addEventListener('resize', function () {
        const form = document.querySelector('.calendar-form');
        const externalEvents = document.querySelector('#external-events');

        if (form && externalEvents) {
            externalEvents.style.width = form.offsetWidth + 'px';
        }
    });

    function adjustCalendarHeight() {
        const viewportHeight = window.innerHeight;

        const mainHeader = document.querySelector('.main-header');
        const mainFooter = document.querySelector('.main-footer');
        const externalEvents = document.querySelector('#external-events');
        const contentHeader = document.querySelector('.content-header');
        const formGroup = document.querySelector('.form-group.text-center');
        const calContainer = document.querySelector('.cal-container');

        let totalSubtractHeight = 0;

        if (mainHeader) totalSubtractHeight += mainHeader.offsetHeight;
        if (mainFooter) totalSubtractHeight += mainFooter.offsetHeight;
        if (externalEvents) totalSubtractHeight += externalEvents.offsetHeight;
        if (contentHeader) totalSubtractHeight += contentHeader.offsetHeight;
        if (formGroup) totalSubtractHeight += formGroup.offsetHeight;

        if (calContainer) {
            calContainer.style.height = (viewportHeight - totalSubtractHeight - 60) + 'px';
            calContainer.style.overflow = 'auto'; // Optional: scroll if overflow
        }
    }

    window.addEventListener('load', adjustCalendarHeight);
    window.addEventListener('resize', adjustCalendarHeight);
</script>

<style>
  .cal-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0rem;
  }
  #external-events {
    padding: 10px;
    width: 100%; /* Adjust for smaller screens */
    background: #ffffff;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    position: fixed;
    bottom: 50px;
    z-index: 2;
  }
  .fc-event {
      margin: 3px 0;
      padding: 0px; /* Increase padding for easier touch interaction */
      background: #f7ae26;
      color: white;
      cursor: pointer;
      touch-action: none; /* Prevent default touch behavior */
  }

  .fc .fc-timegrid-slot {
    border-bottom: 0px;
    height: 3em !important;
    background: #ffffff;
  }

  .content-wrapper {
    /* margin-bottom: 0px !important; */
  }
</style>
