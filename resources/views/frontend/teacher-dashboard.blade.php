<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Giảng viên</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
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
            color: #00f2fe;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
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
            color: #4facfe;
            border-bottom-color: #4facfe;
            font-weight: 600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

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
            cursor: pointer;
        }

        .course-card:hover {
            border-color: #4facfe;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.2);
            transform: translateY(-2px);
        }

        .course-code {
            background: #4facfe;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .course-title {
            font-size: 18px;
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .course-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .btn-view {
            width: 100%;
            padding: 10px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-view:hover {
            background: #3b8cd3;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }

        .schedule-table th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e1e8ed;
        }

        .modal-header h3 {
            color: #333;
            font-size: 24px;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #666;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .btn-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .student-table th,
        .student-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }

        .student-table th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .student-table tr:hover {
            background: #f8f9fa;
        }

        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4facfe;
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
    </style>
</head>
<body>
    <div class="navbar">
        <h1>👨‍🏫 Dashboard Giảng viên</h1>
        <div class="user-info">
            <span id="userName"></span>
            <button class="btn-logout" onclick="logout()">Đăng xuất</button>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <button class="tab active" onclick="showTab('courses')">Lớp đang dạy</button>
            <button class="tab" onclick="showTab('schedule')">Lịch giảng dạy</button>
        </div>

        <div id="courses" class="tab-content active">
            <div class="card">
                <h2>📚 Danh sách lớp đang dạy</h2>
                <div id="coursesList" class="course-grid">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p style="margin-top: 10px; color: #666;">Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="schedule" class="tab-content">
            <div class="card">
                <h2>📅 Lịch giảng dạy</h2>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Thứ</th>
                            <th>Giờ học</th>
                            <th>Môn học</th>
                            <th>Lớp</th>
                            <th>Phòng</th>
                            <th>Sĩ số</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleBody">
                        <tr>
                            <td colspan="6">
                                <div class="loading">
                                    <div class="spinner"></div>
                                    <p style="margin-top: 10px; color: #666;">Đang tải...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Students List -->
    <div id="studentsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Danh sách sinh viên</h3>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            
            <div id="courseInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;"></div>
            
            <table class="student-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <div class="loading">
                                <div class="spinner"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const API_URL = window.location.origin + '/api';
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));

        // Check authentication
        if (!token || !user || user.role !== 'giang_vien') {
            window.location.href = 'index.html';
        }

        // Display user info
        document.getElementById('userName').textContent = user.full_name;

        // Logout
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = 'index.html';
        }

        // Tab switching
        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');

            if (tabName === 'schedule') loadSchedule();
        }

        // Load courses
        async function loadCourses() {
            try {
                const response = await fetch(`${API_URL}/giang-vien/courses`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await response.json();
                const coursesDiv = document.getElementById('coursesList');

                if (data.length > 0) {
                    coursesDiv.innerHTML = data.map(course => `
                        <div class="course-card">
                            <span class="course-code">${course.course_code}</span>
                            <div class="course-title">${course.subject.subject_name}</div>
                            <div class="course-info">
                                <strong>Học kỳ:</strong> ${course.semester} - ${course.academic_year}
                            </div>
                            <div class="course-info">
                                <strong>Phòng:</strong> ${course.room}
                            </div>
                            <div class="course-info">
                                <strong>Sĩ số:</strong> ${course.current_students}/${course.max_students} sinh viên
                            </div>
                            <div class="course-info">
                                <strong>Trạng thái:</strong> 
                                <span style="color: ${course.status === 'open' ? '#28a745' : '#6c757d'};">
                                    ${getStatusText(course.status)}
                                </span>
                            </div>
                            <button class="btn-view" onclick="viewStudents(${course.id})">
                                👥 Xem danh sách sinh viên
                            </button>
                        </div>
                    `).join('');
                } else {
                    coursesDiv.innerHTML = `
                        <div style="text-align: center; padding: 60px; color: #666; grid-column: 1/-1;">
                            <p style="font-size: 18px; margin-bottom: 10px;">📚 Chưa có lớp nào</p>
                            <p>Hiện tại bạn chưa được phân công giảng dạy lớp nào</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Load schedule
        async function loadSchedule() {
            try {
                const response = await fetch(`${API_URL}/giang-vien/schedule`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await response.json();
                const tbody = document.getElementById('scheduleBody');

                if (data.length > 0) {
                    tbody.innerHTML = data.map(item => `
                        <tr>
                            <td><strong>${item.day_name}</strong></td>
                            <td>${item.start_time.substring(0,5)} - ${item.end_time.substring(0,5)}</td>
                            <td>${item.subject_name}</td>
                            <td><span style="background: #4facfe; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px;">${item.course_code}</span></td>
                            <td>${item.room}</td>
                            <td>${item.current_students}/${item.max_students}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                📅 Chưa có lịch giảng dạy
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // View students
        async function viewStudents(courseId) {
            document.getElementById('studentsModal').style.display = 'block';
            
            try {
                const response = await fetch(`${API_URL}/giang-vien/courses/${courseId}/students`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await response.json();
                
                // Update modal title and course info
                document.getElementById('modalTitle').textContent = 
                    `Danh sách sinh viên - ${data.course.course_code}`;
                
                document.getElementById('courseInfo').innerHTML = `
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <strong>Môn học:</strong><br>
                            ${data.course.subject.subject_name}
                        </div>
                        <div>
                            <strong>Số tín chỉ:</strong><br>
                            ${data.course.subject.credits} tín chỉ
                        </div>
                        <div>
                            <strong>Phòng:</strong><br>
                            ${data.course.room}
                        </div>
                        <div>
                            <strong>Sĩ số:</strong><br>
                            ${data.students.length}/${data.course.max_students} sinh viên
                        </div>
                    </div>
                `;

                const tbody = document.getElementById('studentsTableBody');

                if (data.students.length > 0) {
                    tbody.innerHTML = data.students.map((student, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${student.student_code}</strong></td>
                            <td>${student.full_name}</td>
                            <td>${student.class_name || '-'}</td>
                            <td>${student.email}</td>
                            <td>${student.phone || '-'}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                Chưa có sinh viên nào đăng ký lớp này
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi khi tải danh sách sinh viên');
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('studentsModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('studentsModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Get status text
        function getStatusText(status) {
            const statusMap = {
                'pending': 'Chưa mở',
                'open': 'Đang mở',
                'closed': 'Đã đóng',
                'completed': 'Hoàn thành'
            };
            return statusMap[status] || status;
        }

        // Initialize
        loadCourses();
    </script>
</body>
</html>
