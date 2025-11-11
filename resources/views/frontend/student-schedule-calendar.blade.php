<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lich hoc - Sinh vien</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f7fa; }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e1e8ed;
        }
        
        .calendar-header h2 { color: #333; }
        
        .btn-back {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            gap: 1px;
            background: #e1e8ed;
            border: 1px solid #e1e8ed;
        }
        
        .calendar-header-cell {
            background: #f8f9fa;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            color: #333;
        }
        
        .time-cell {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        
        .day-cell {
            background: white;
            min-height: 80px;
            padding: 5px;
            position: relative;
        }
        
        .event {
            background: #667eea;
            color: white;
            padding: 8px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .event-time {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .event-room {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }
        
        .event-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .event-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .event-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .event-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .event-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .event-6 { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
        .event-7 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
        .event-8 { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .legend {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        @media (max-width: 1200px) {
            .calendar-grid {
                grid-template-columns: 60px repeat(7, 1fr);
            }
            .event-title { font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📅 Lịch học của tôi</h1>
        <div>
            <span id="userName"></span>
        </div>
    </div>

    <div class="container">
        <div class="calendar-header">
            <h2>Tuần học</h2>
            <a href="student-dashboard.html" class="btn-back">← Quay lại Dashboard</a>
        </div>

        <div id="calendarContainer">
            <div class="loading">Đang tải lịch học...</div>
        </div>

        <div class="legend" id="legend"></div>
    </div>

    <script>
        const API_URL = window.location.origin + '/api';
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));

        if (!token || !user) {
            window.location.href = 'index.html';
        }

        document.getElementById('userName').textContent = user.full_name;

        const timeSlots = [
            '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
            '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        const dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];

        async function loadSchedule() {
            try {
                const response = await fetch(`${API_URL}/sinh-vien/schedule`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const scheduleData = await response.json();
                
                if (scheduleData.length === 0) {
                    document.getElementById('calendarContainer').innerHTML = '<p style="text-align:center;padding:40px;">Chưa có lịch học</p>';
                    return;
                }

                renderCalendar(scheduleData);
                renderLegend(scheduleData);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('calendarContainer').innerHTML = '<p style="text-align:center;padding:40px;color:red;">Lỗi tải lịch học</p>';
            }
        }

        function renderCalendar(scheduleData) {
            const container = document.getElementById('calendarContainer');
            
            let html = '<div class="calendar-grid">';
            
            html += '<div class="calendar-header-cell">Giờ</div>';
            dayNames.forEach(day => {
                html += `<div class="calendar-header-cell">${day}</div>`;
            });
            
            const scheduleMap = {};
            scheduleData.forEach((item, index) => {
                const dayKey = item.day_of_week;
                if (!scheduleMap[dayKey]) scheduleMap[dayKey] = [];
                scheduleMap[dayKey].push({...item, colorClass: `event-${(index % 8) + 1}`});
            });
            
            timeSlots.forEach(time => {
                html += `<div class="time-cell">${time}</div>`;
                
                for (let day = 2; day <= 8; day++) {
                    const events = scheduleMap[day] || [];
                    const eventsInThisSlot = events.filter(e => {
                        const eventHour = parseInt(e.start_time.split(':')[0]);
                        const slotHour = parseInt(time.split(':')[0]);
                        return eventHour === slotHour;
                    });
                    
                    html += '<div class="day-cell">';
                    eventsInThisSlot.forEach(event => {
                        html += `
                            <div class="event ${event.colorClass}" title="${event.subject_name}">
                                <div class="event-title">${event.subject_code}</div>
                                <div class="event-time">${event.start_time.substring(0,5)} - ${event.end_time.substring(0,5)}</div>
                                <div class="event-room">📍 ${event.room}</div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function renderLegend(scheduleData) {
            const legendDiv = document.getElementById('legend');
            const uniqueSubjects = [...new Map(scheduleData.map(item => 
                [item.subject_code, item]
            )).values()];
            
            let html = '<strong style="margin-right: 15px;">Chú thích:</strong>';
            uniqueSubjects.forEach((subject, index) => {
                const colorClass = `event-${(index % 8) + 1}`;
                html += `
                    <div class="legend-item">
                        <div class="legend-color ${colorClass}"></div>
                        <span>${subject.subject_code} - ${subject.subject_name}</span>
                    </div>
                `;
            });
            
            legendDiv.innerHTML = html;
        }

        loadSchedule();
    </script>
</body>
</html>
