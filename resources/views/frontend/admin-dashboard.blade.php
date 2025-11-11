<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quản lý tài khoản</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fa;color:#333}
        .navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:14px 28px;display:flex;justify-content:space-between;align-items:center}
        .navbar h1{font-size:20px}
        .user-info{display:flex;align-items:center;gap:12px}
        .btn-logout{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);color:white;padding:8px 14px;border-radius:8px;cursor:pointer}
        .container{max-width:1200px;margin:28px auto;padding:0 18px}
        .card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:20px}
        .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:12px}
        .input{padding:8px 12px;border:1px solid #e1e8ed;border-radius:8px}
        .btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer;font-weight:600}
        .btn-primary{background:#667eea;color:white}
        .btn-danger{background:#dc3545;color:white}
        table{width:100%;border-collapse:collapse}
        th,td{padding:12px;text-align:left;border-bottom:1px solid #eef2f6}
        th{background:#fafbfd;font-weight:700}
        .badge-active{background:#d4edda;color:#155724;padding:6px 10px;border-radius:999px;font-weight:700}
        .badge-inactive{background:#f8d7da;color:#721c24;padding:6px 10px;border-radius:999px;font-weight:700}
        .actions button{margin-right:6px}
        .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1000;align-items:center;justify-content:center}
        .modal.show{display:flex}
        .modal-content{background:#fff;padding:20px;border-radius:10px;width:90%;max-width:720px;box-shadow:0 10px 40px rgba(0,0,0,0.3)}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:10px}
        .full{grid-column:1/-1}
        @media(max-width:700px){.form-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🛠️ Admin - Quản lý tài khoản</h1>
        <div class="user-info">
            <div id="userName"></div>
            <button class="btn-logout" onclick="logout()">Đăng xuất</button>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="toolbar">
                <input id="searchInput" class="input" placeholder="Tìm kiếm tên, email, username...">
                <select id="filterRole" class="input">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin">Admin</option>
                    <option value="phong_dao_tao">Phòng Đào Tạo</option>
                    <option value="giang_vien">Giảng viên</option>
                    <option value="sinh_vien">Sinh viên</option>
                </select>
                <button class="btn btn-primary" id="btnSearch">Tìm</button>
                <div style="flex:1"></div>
                <button class="btn btn-primary" onclick="openCreateModal()">+ Tạo tài khoản</button>
            </div>

            <div id="usersTableWrapper">
                <div class="loading" id="loadingUsers" style="text-align:center;padding:24px">
                    Đang tải...
                </div>
                <table id="usersTable" style="display:none">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Họ tên</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="usersTbody"></tbody>
                </table>
            </div>

            <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center">
                <div id="paginationInfo"></div>
                <div>
                    <button class="btn" id="prevPage">Trang trước</button>
                    <button class="btn" id="nextPage">Trang sau</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Tạo người dùng</h3>
            <div style="margin-top:12px">
                <div class="form-row">
                    <input id="f_username" class="input" placeholder="username">
                    <input id="f_email" class="input" placeholder="email">
                </div>
                <div class="form-row">
                    <input id="f_full_name" class="input" placeholder="Họ tên">
                    <select id="f_role" class="input">
                        <option value="admin">Admin</option>
                        <option value="phong_dao_tao">Phòng Đào Tạo</option>
                        <option value="giang_vien">Giảng viên</option>
                        <option value="sinh_vien">Sinh viên</option>
                    </select>
                </div>
                <div class="form-row">
                    <input id="f_password" class="input" placeholder="Mật khẩu (để trống nếu không đổi)">
                    <input id="f_password_confirm" class="input" placeholder="Xác nhận mật khẩu">
                </div>

                <!-- Student fields -->
                <div id="studentFields" style="display:none">
                    <div class="form-row">
                        <input id="f_student_code" class="input" placeholder="Mã sinh viên">
                        <input id="f_major" class="input" placeholder="Ngành">
                    </div>
                    <div class="form-row">
                        <input id="f_academic_year" class="input" placeholder="Khóa/Năm học">
                        <input id="f_class_name" class="input" placeholder="Lớp">
                    </div>
                </div>

                <!-- Teacher fields -->
                <div id="teacherFields" style="display:none">
                    <div class="form-row">
                        <input id="f_teacher_code" class="input" placeholder="Mã giảng viên">
                        <input id="f_department" class="input" placeholder="Bộ môn/Phòng">
                    </div>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
                    <button class="btn" onclick="closeModal()">Hủy</button>
                    <button class="btn btn-primary" id="saveUserBtn">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = window.location.origin + '/api';
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));

        if (!token || !user || user.role !== 'admin') {
            window.location.href = 'index.html';
        }

        document.getElementById('userName').textContent = user.full_name;

        function logout(){ localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href='index.html'}

        // Pagination & state
        let currentPage = 1; let lastPage = 1; let perPage = 10;

        document.getElementById('btnSearch').addEventListener('click', ()=>{ currentPage=1; loadUsers(); });
        document.getElementById('prevPage').addEventListener('click', ()=>{ if(currentPage>1){currentPage--; loadUsers()} });
        document.getElementById('nextPage').addEventListener('click', ()=>{ if(currentPage<lastPage){currentPage++; loadUsers()} });

        async function loadUsers(){
            const q = encodeURIComponent(document.getElementById('searchInput').value || '');
            const role = document.getElementById('filterRole').value || '';
            document.getElementById('loadingUsers').style.display='block';
            document.getElementById('usersTable').style.display='none';
            try{
                const url = `${API_URL}/admin/users?page=${currentPage}&per_page=${perPage}` + (q?`&q=${q}`:'') + (role?`&role=${role}`:'');
                const res = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });

                if (!res.ok) {
                    let errMsg = `HTTP ${res.status}`;
                    try {
                        const errBody = await res.json();
                        errMsg = errBody.error || JSON.stringify(errBody);
                    } catch (e) {
                        try { errMsg = await res.text(); } catch (e2) { }
                    }
                    document.getElementById('loadingUsers').textContent = 'Lỗi tải dữ liệu: ' + errMsg;
                    return;
                }

                const data = await res.json();

                const users = data.data || data;
                lastPage = data.last_page || 1;
                document.getElementById('usersTbody').innerHTML = users.map(u=>`
                    <tr>
                        <td>${u.id}</td>
                        <td>${u.username}</td>
                        <td>${u.email}</td>
                        <td>${u.full_name || '-'}</td>
                        <td>${roleLabel(u.role)}</td>
                        <td>${u.is_active?'<span class="badge-active">Kích hoạt</span>':'<span class="badge-inactive">Khóa</span>'}</td>
                        <td class="actions">
                            <button class="btn" onclick='openEditModal(${encodeURIComponent(JSON.stringify(u))})'>Sửa</button>
                            <button class="btn" onclick="toggleActive(${u.id})">Bật/Tắt</button>
                            <button class="btn btn-danger" onclick="deleteUser(${u.id})">Xóa</button>
                        </td>
                    </tr>
                `).join('');

                document.getElementById('loadingUsers').style.display='none';
                document.getElementById('usersTable').style.display='table';
                document.getElementById('paginationInfo').textContent = `Trang ${currentPage} / ${lastPage}`;
            }catch(err){
                console.error(err);
                document.getElementById('loadingUsers').textContent='Lỗi tải dữ liệu';
            }
        }

        function roleLabel(r){
            switch(r){
                case 'admin': return 'Admin';
                case 'phong_dao_tao': return 'Phòng Đào Tạo';
                case 'giang_vien': return 'Giảng viên';
                case 'sinh_vien': return 'Sinh viên';
                default: return r;
            }
        }

        // Modal logic
        let editingUserId = null;
        function openCreateModal(){ editingUserId = null; document.getElementById('modalTitle').textContent='Tạo người dùng'; resetForm(); showFieldsByRole(); document.getElementById('userModal').classList.add('show'); }

        function openEditModal(uJson){
            const u = typeof uJson === 'string' ? JSON.parse(decodeURIComponent(uJson)) : uJson;
            editingUserId = u.id;
            document.getElementById('modalTitle').textContent='Chỉnh sửa người dùng';
            document.getElementById('f_username').value = u.username || '';
            document.getElementById('f_email').value = u.email || '';
            document.getElementById('f_full_name').value = u.full_name || '';
            document.getElementById('f_role').value = u.role || 'sinh_vien';
            document.getElementById('f_password').value = '';
            document.getElementById('f_password_confirm').value = '';
            if(u.student){ document.getElementById('f_student_code').value = u.student.student_code || ''; document.getElementById('f_major').value = u.student.major || ''; document.getElementById('f_academic_year').value = u.student.academic_year || ''; document.getElementById('f_class_name').value = u.student.class_name || ''; }
            if(u.teacher){ document.getElementById('f_teacher_code').value = u.teacher.teacher_code || ''; document.getElementById('f_department').value = u.teacher.department || ''; }
            showFieldsByRole();
            document.getElementById('userModal').classList.add('show');
        }

        function closeModal(){ document.getElementById('userModal').classList.remove('show'); }

        function resetForm(){
            document.getElementById('f_username').value = '';
            document.getElementById('f_email').value = '';
            document.getElementById('f_full_name').value = '';
            document.getElementById('f_role').value = 'admin';
            document.getElementById('f_password').value = '';
            document.getElementById('f_password_confirm').value = '';
            document.getElementById('f_student_code').value = '';
            document.getElementById('f_major').value = '';
            document.getElementById('f_academic_year').value = '';
            document.getElementById('f_class_name').value = '';
            document.getElementById('f_teacher_code').value = '';
            document.getElementById('f_department').value = '';
        }

        document.getElementById('f_role').addEventListener('change', showFieldsByRole);
        function showFieldsByRole(){
            const r = document.getElementById('f_role').value;
            document.getElementById('studentFields').style.display = r === 'sinh_vien' ? 'block' : 'none';
            document.getElementById('teacherFields').style.display = r === 'giang_vien' ? 'block' : 'none';
        }

        // Save user
        document.getElementById('saveUserBtn').addEventListener('click', async ()=>{
            const payload = {
                username: document.getElementById('f_username').value,
                email: document.getElementById('f_email').value,
                full_name: document.getElementById('f_full_name').value,
                role: document.getElementById('f_role').value,
                password: document.getElementById('f_password').value,
                password_confirmation: document.getElementById('f_password_confirm').value,
            };

            if(payload.role === 'sinh_vien'){
                payload.student_code = document.getElementById('f_student_code').value;
                payload.major = document.getElementById('f_major').value;
                payload.academic_year = document.getElementById('f_academic_year').value;
                payload.class_name = document.getElementById('f_class_name').value;
            }
            if(payload.role === 'giang_vien'){
                payload.teacher_code = document.getElementById('f_teacher_code').value;
                payload.department = document.getElementById('f_department').value;
            }

            try{
                let res, data;
                if(editingUserId){
                    const body = {};
                    for(const k in payload) if(payload[k]) body[k]=payload[k];
                    res = await fetch(`${API_URL}/admin/users/${editingUserId}`, { method: 'PUT', headers: {'Authorization':`Bearer ${token}`,'Content-Type':'application/json'}, body: JSON.stringify(body) });
                    data = await res.json();
                } else {
                    res = await fetch(`${API_URL}/admin/users`, { method: 'POST', headers: {'Authorization':`Bearer ${token}`,'Content-Type':'application/json'}, body: JSON.stringify(payload) });
                    data = await res.json();
                }

                if(res.ok){
                    alert('Lưu thành công'); closeModal(); loadUsers();
                } else {
                    alert(data.error || JSON.stringify(data));
                }
            }catch(err){console.error(err); alert('Lỗi lưu dữ liệu');}
        });

        async function deleteUser(id){ if(!confirm('Xóa người dùng?')) return; try{ const res = await fetch(`${API_URL}/admin/users/${id}`, { method:'DELETE', headers:{'Authorization':`Bearer ${token}`} }); if(res.ok){ alert('Đã xóa'); loadUsers(); } else { const d = await res.json(); alert(d.error || 'Xóa thất bại'); } } catch(e){alert('Lỗi');} }

        async function toggleActive(id){ try{ const res = await fetch(`${API_URL}/admin/users/${id}/toggle-active`, { method:'PUT', headers:{'Authorization':`Bearer ${token}`} }); const d = await res.json(); if(res.ok){ alert(d.message || 'Đã cập nhật'); loadUsers(); } else { alert(d.error || 'Thất bại'); } } catch(e){ alert('Lỗi'); } }

        // init
        loadUsers();
    </script>
</body>
</html>
