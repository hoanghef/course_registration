<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sinh vien</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-left h1 { font-size: 24px; }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: white;
            color: #667eea;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .stat-card p {
            opacity: 0.9;
            font-size: 16px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e1e8ed;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
            font-weight: 600;
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: white;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .course-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .course-code {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .course-title {
            font-size: 18px;
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .course-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .schedule-list { margin-top: 8px; display:flex; flex-direction:column; gap:6px; }
        .schedule-item { font-size:13px; color:#444; background:#f8f9fb; padding:6px 8px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; }
        .schedule-day { font-weight:700; color:#667eea; margin-right:8px; }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-register:hover { background: #5568d3; }
        .btn-register:disabled { background: #ccc; cursor: not-allowed; }

        .btn-cancel {
            width: 100%;
            padding: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-cancel:hover { background: #c82333; }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            gap: 1px;
            background: #e1e8ed;
            border: 1px solid #e1e8ed;
            overflow-x: auto;
        }

        .calendar-header-cell {
            background: #f8f9fa;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .time-cell {
            background: #f8f9fa;
            padding: 10px 5px;
            text-align: center;
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .day-cell {
            background: white;
            min-height: 60px;
            padding: 5px;
            position: relative;
        }

        .event {
            background: #667eea;
            color: white;
            padding: 6px;
            border-radius: 4px;
            font-size: 11px;
            margin-bottom: 3px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .event:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .event-title {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .event-time {
            font-size: 10px;
            opacity: 0.9;
        }

        .event-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .event-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .event-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .event-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .event-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 1200px) {
            .calendar-grid {
                grid-template-columns: 60px repeat(7, minmax(100px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <h1>🎓 Dashboard Sinh vien</h1>
        </div>
        <div class="navbar-right">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">S</div>
                <div>
                    <div id="userName" style="font-weight: 600;"></div>
                    <div id="userEmail" style="font-size: 12px; opacity: 0.9;"></div>
                </div>
            </div>
            <button class="btn-logout" onclick="logout()">Dang xuat</button>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalRegistered">0</h3>
                <p>Mon da dang ky</p>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('available')">Dang ky mon hoc</button>
            <button class="tab" onclick="showTab('registered')">Mon da dang ky</button>
            <button class="tab" onclick="showTab('schedule')">Lich hoc</button>
        </div>

        <div id="available" class="tab-content active">
            <div class="card">
                <h2>📚 Danh sach mon hoc co the dang ky</h2>
                <div id="availableCourses" class="course-grid">
                    <div class="loading"><div class="spinner"></div></div>
                </div>
            </div>
        </div>

        <div id="registered" class="tab-content">
            <div class="card">
                <h2>📝 Mon hoc da dang ky</h2>
                <div id="registeredCourses" class="course-grid"></div>
            </div>
        </div>

        <div id="schedule" class="tab-content">
            <div class="card">
                <h2>📅 Lich hoc tuan nay</h2>
                <div id="calendarContainer">
                    <div class="loading"><div class="spinner"></div></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = window.location.origin + '/api';
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));

        if (!token || !user) window.location.href = 'index.html';

        document.getElementById('userName').textContent = user.full_name;
        document.getElementById('userEmail').textContent = user.email;
        document.getElementById('userAvatar').textContent = user.full_name.charAt(0).toUpperCase();

        function logout() {
            localStorage.clear();
            window.location.href = 'index.html';
        }

        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');

            if (tabName === 'registered') loadRegisteredCourses();
            if (tabName === 'schedule') loadScheduleCalendar();
        }

        async function loadAvailableCourses() {
            try {
                const response = await fetch(`${API_URL}/sinh-vien/courses/available`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();
                const coursesDiv = document.getElementById('availableCourses');

                if (data.data && data.data.length > 0) {
                    coursesDiv.innerHTML = data.data.map(course => `
                        <div class="course-card">
                            <div class="course-header">
                                <span class="course-code">${course.course_code}</span>
                                <span style="color: #666; font-size: 12px;">${course.current_students}/${course.max_students}</span>
                            </div>
                            <div class="course-title">${course.subject.subject_name}</div>
                            <div class="course-info"><strong>Tin chi:</strong> ${course.subject.credits}</div>
                            <div class="course-info"><strong>Giang vien:</strong> ${course.teacher?.user?.full_name || 'Chua PC'}</div>
                            <div class="course-info"><strong>Phong:</strong> ${course.room}</div>
                            ${formatSchedulesHTML(course.schedules, course.room)}
                            <button class="btn-register" onclick="registerCourse(${course.id})" ${course.current_students >= course.max_students ? 'disabled' : ''}>
                                ${course.current_students >= course.max_students ? 'Da day' : 'Dang ky'}
                            </button>
                        </div>
                    `).join('');
                } else {
                    coursesDiv.innerHTML = '<p style="text-align:center;color:#666;">Khong co mon hoc</p>';
                }
            } catch (error) {
                console.error(error);
            }
        }

        async function registerCourse(courseId) {
            if (!confirm('Dang ky mon hoc nay?')) return;

            try {
                const response = await fetch(`${API_URL}/sinh-vien/registrations`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ course_id: courseId })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Dang ky thanh cong!');
                    loadAvailableCourses();
                    loadStats();
                } else {
                    alert(data.error || 'That bai');
                }
            } catch (error) {
                alert('Loi ket noi');
            }
        }

        async function loadRegisteredCourses() {
            try {
                const response = await fetch(`${API_URL}/sinh-vien/registrations`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();
                const coursesDiv = document.getElementById('registeredCourses');

                const activeCourses = data.filter(reg => reg.status !== 'cancelled');

                if (activeCourses.length > 0) {
                    coursesDiv.innerHTML = activeCourses.map(reg => `
                        <div class="course-card">
                            <div class="course-header">
                                <span class="course-code">${reg.course.course_code}</span>
                                <span class="status-badge">Thanh cong</span>
                            </div>
                            <div class="course-title">${reg.course.subject.subject_name}</div>
                            <div class="course-info"><strong>Tin chi:</strong> ${reg.course.subject.credits}</div>
                            <div class="course-info"><strong>Phong:</strong> ${reg.course.room}</div>
                            ${formatSchedulesHTML(reg.course.schedules, reg.course.room)}
                            <div class="course-info"><strong>Ngay DK:</strong> ${new Date(reg.registration_date).toLocaleDateString('vi-VN')}</div>
                            <button class="btn-cancel" onclick="cancelRegistration(${reg.id})">Huy dang ky</button>
                        </div>
                    `).join('');
                } else {
                    coursesDiv.innerHTML = '<p style="text-align:center;color:#666;">Chua dang ky mon nao</p>';
                }

                loadStats();
            } catch (error) {
                console.error(error);
            }
        }

        async function cancelRegistration(regId) {
            if (!confirm('Huy dang ky mon nay?')) return;

            try {
                const response = await fetch(`${API_URL}/sinh-vien/registrations/${regId}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (response.ok) {
                    alert('Da huy thanh cong!');
                    loadRegisteredCourses();
                    loadAvailableCourses();
                } else {
                    alert('Huy that bai');
                }
            } catch (error) {
                alert('Loi ket noi');
            }
        }

        async function loadScheduleCalendar() {
            try {
                const response = await fetch(`${API_URL}/sinh-vien/schedule`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const scheduleData = await response.json();
                
                if (scheduleData.length === 0) {
                    document.getElementById('calendarContainer').innerHTML = '<p style="text-align:center;padding:40px;">Chua co lich hoc</p>';
                    return;
                }

                renderCalendar(scheduleData);
            } catch (error) {
                console.error(error);
            }
        }

        function renderCalendar(scheduleData) {
            const timeSlots = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
            const dayNames = ['Thu 2', 'Thu 3', 'Thu 4', 'Thu 5', 'Thu 6', 'Thu 7', 'CN'];
            
            const container = document.getElementById('calendarContainer');
            
            let html = '<div class="calendar-grid">';
            html += '<div class="calendar-header-cell">Gio</div>';
            dayNames.forEach(day => {
                html += `<div class="calendar-header-cell">${day}</div>`;
            });
            
            const scheduleMap = {};
            scheduleData.forEach((item, index) => {
                const dayKey = item.day_of_week;
                if (!scheduleMap[dayKey]) scheduleMap[dayKey] = [];
                scheduleMap[dayKey].push({...item, colorClass: `event-${(index % 5) + 1}`});
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
                            <div class="event ${event.colorClass}">
                                <div class="event-title">${event.subject_code}</div>
                                <div class="event-time">${event.start_time.substring(0,5)}-${event.end_time.substring(0,5)}</div>
                                <div class="event-time">📍 ${event.room}</div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function formatSchedulesHTML(schedules, fallbackRoom) {
            if (!schedules || schedules.length === 0) return '';

            const dayNames = {2:'Thứ 2',3:'Thứ 3',4:'Thứ 4',5:'Thứ 5',6:'Thứ 6',7:'Thứ 7',8:'CN'};

            const items = schedules.slice(0,3).map(s => {
                const day = dayNames[s.day_of_week] || ('Thứ ' + s.day_of_week);
                const time = `${s.start_time.substring(0,5)} - ${s.end_time.substring(0,5)}`;
                const room = s.room || fallbackRoom || '-';
                return `<div class="schedule-item"><div><span class="schedule-day">${day}</span> ${time}</div><div style="font-size:12px;color:#666">📍 ${room}</div></div>`;
            }).join('');

            if (schedules.length > 3) {
                return `<div class="schedule-list">${items}<div class="schedule-item" style="justify-content:center;color:#666">+ ${schedules.length - 3} buổi khác</div></div>`;
            }

            return `<div class="schedule-list">${items}</div>`;
        }

        async function loadStats() {
            try {
                const response = await fetch(`${API_URL}/sinh-vien/registrations`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();
                const activeCount = data.filter(r => r.status !== 'cancelled').length;
                
                document.getElementById('totalRegistered').textContent = activeCount;
            } catch (error) {
                console.error(error);
            }
        }

        loadAvailableCourses();
        loadStats();
    </script>
</body>
</html>
