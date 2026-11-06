<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HostelPro · Complete System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
        }

        body {
            background: #f0f4fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            background: white;
            border-radius: 44px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2);
        }

        /* Login Form */
        .login-wrapper {
            max-width: 420px;
            margin: 0 auto;
        }
        .login-wrapper .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-wrapper .brand i {
            font-size: 2.8rem;
            color: #1a5fb0;
            background: #e9f0fe;
            padding: 0.7rem;
            border-radius: 30px;
        }
        .login-wrapper .brand h2 {
            margin-top: 0.6rem;
            font-weight: 700;
            color: #0b2b4a;
        }
        .login-wrapper .brand span {
            color: #4c6f96;
            font-size: 0.9rem;
        }
        .login-form .form-group {
            margin-bottom: 1.4rem;
        }
        .login-form label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1d3755;
            display: block;
            margin-bottom: 5px;
        }
        .login-form input {
            width: 100%;
            padding: 0.8rem 1.2rem;
            border: 1px solid #d7e0ea;
            border-radius: 40px;
            background: #fafcff;
            font-size: 0.95rem;
        }
        .login-form input:focus {
            outline: none;
            border-color: #1a5fb0;
            box-shadow: 0 0 0 4px rgba(26, 95, 176, 0.12);
        }
        .login-btn {
            width: 100%;
            background: #1a5fb0;
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.15s;
            box-shadow: 0 8px 18px -8px rgba(26, 95, 176, 0.35);
        }
        .login-btn:hover {
            background: #134b8f;
            transform: scale(1.01);
        }
        .login-error {
            color: #b13e3e;
            font-size: 0.8rem;
            text-align: center;
            background: #fce8e8;
            padding: 0.4rem;
            border-radius: 40px;
            display: none;
            margin-top: 0.8rem;
        }
        .login-footer {
            margin-top: 1.8rem;
            text-align: center;
            font-size: 0.75rem;
            color: #6b85a3;
        }

        /* Dashboard */
        .dashboard {
            display: none;
        }
        .dashboard.active {
            display: block;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .dash-header .left h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0b2b4a;
        }
        .dash-header .left i {
            color: #1a5fb0;
            margin-right: 10px;
        }
        .dash-header .right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .user-badge {
            background: #e9f0fe;
            padding: 0.4rem 1.2rem 0.4rem 1rem;
            border-radius: 60px;
            font-weight: 600;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-badge i {
            color: #1a5fb0;
        }
        .logout-btn {
            background: transparent;
            border: 1px solid #d7e0ea;
            padding: 0.4rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            color: #1d3755;
            cursor: pointer;
            transition: 0.1s;
        }
        .logout-btn:hover {
            background: #f0f4fb;
            border-color: #b0c4db;
        }

        .stat-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f8faff;
            padding: 0.8rem 1.8rem 0.8rem 1.5rem;
            border-radius: 60px;
            border: 1px solid #e3ebf5;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-card i {
            font-size: 1.3rem;
            color: #1a5fb0;
            width: 2rem;
        }
        .stat-card .info .label {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #4c6f96;
        }
        .stat-card .info .value {
            font-weight: 700;
            font-size: 1.3rem;
            color: #0b2b4a;
        }

        .admin-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            background: #f2f6fe;
            padding: 0.7rem 1.8rem;
            border-radius: 60px;
            margin-bottom: 2rem;
            gap: 0.8rem;
        }
        .admin-bar .btn-group {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            background: white;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: 0.1s;
            border: 1px solid transparent;
            color: #1d3755;
        }
        .btn-primary {
            background: #1a5fb0;
            color: white;
            box-shadow: 0 4px 12px -6px rgba(26,95,176,0.3);
        }
        .btn-primary:hover {
            background: #134b8f;
        }
        .btn-outline {
            background: transparent;
            border-color: #cbd5e1;
        }
        .btn-outline:hover {
            background: #e6edf8;
        }
        .btn-success {
            background: #0f7b3a;
            color: white;
        }
        .btn-success:hover {
            background: #0b632e;
        }
        .btn-danger {
            background: #b13e3e;
            color: white;
        }
        .btn-danger:hover {
            background: #932f2f;
        }
        .btn-warning {
            background: #b8860b;
            color: white;
        }
        .btn-warning:hover {
            background: #9e740a;
        }

        .table-wrap {
            background: white;
            border-radius: 24px;
            border: 1px solid #e6edf6;
            overflow-x: auto;
            margin-bottom: 2rem;
            box-shadow: 0 6px 16px -10px rgba(0,0,0,0.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 680px;
        }
        th {
            background: #f8fafd;
            text-align: left;
            padding: 0.9rem 1.2rem;
            font-weight: 600;
            color: #1f3b5c;
            border-bottom: 1px solid #dce3ed;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        td {
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid #ebf0f7;
            color: #1f334f;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background: #fafcff;
        }
        .badge {
            background: #e6edf8;
            padding: 0.2rem 0.9rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge.available {
            background: #d9f0e1;
            color: #0b5e2e;
        }
        .badge.occupied {
            background: #fce8e8;
            color: #a13232;
        }
        .badge.paid {
            background: #d4edda;
            color: #0b5e2e;
        }
        .badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .action-icons i {
            margin: 0 4px;
            color: #4a6e96;
            cursor: pointer;
            font-size: 0.95rem;
            transition: 0.1s;
        }
        .action-icons i:hover {
            color: #0b2b4a;
            transform: scale(1.05);
        }

        .user-room-card {
            background: #f8faff;
            border-radius: 28px;
            padding: 1.8rem 2rem;
            border: 1px solid #e3ebf5;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            align-items: center;
        }
        .user-room-card .detail {
            display: flex;
            flex-direction: column;
        }
        .user-room-card .detail .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #4c6f96;
        }
        .user-room-card .detail .value {
            font-weight: 700;
            font-size: 1.3rem;
            color: #0b2b4a;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.25);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: white;
            max-width: 520px;
            width: 92%;
            padding: 2.2rem 2.2rem 1.8rem;
            border-radius: 44px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.4);
            animation: fadeUp 0.2s ease;
        }
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .modal-card h2 {
            margin-bottom: 1.5rem;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-card .form-group {
            margin-bottom: 1.2rem;
        }
        .modal-card label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1d3755;
            display: block;
            margin-bottom: 4px;
        }
        .modal-card input,
        .modal-card select {
            width: 100%;
            padding: 0.7rem 1.2rem;
            border: 1px solid #d0dbea;
            border-radius: 40px;
            background: #fafcff;
            font-size: 0.95rem;
        }
        .modal-card input:focus,
        .modal-card select:focus {
            outline: none;
            border-color: #1a5fb0;
            box-shadow: 0 0 0 4px rgba(26,95,176,0.1);
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 1.8rem;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
            gap: 1rem;
        }
        .loading-overlay.active {
            display: flex;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e9f0fe;
            border-top-color: #1a5fb0;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container { padding: 1.5rem; }
            .admin-bar { flex-direction: column; align-items: stretch; }
            .user-room-card { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
            .dash-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p style="color: #0b2b4a; font-weight: 600;">Loading...</p>
</div>

<div class="container">
    <!-- Login -->
    <div id="loginWrapper" class="login-wrapper">
        <div class="brand">
            <i class="fas fa-hotel"></i>
            <h2>HostelPro</h2>
            <span>Complete Management System</span>
        </div>
        <form class="login-form" id="loginForm">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" id="loginUsername" placeholder="Enter username" value="admin" />
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="loginPassword" placeholder="Enter password" value="12345" />
            </div>
            <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> Log in</button>
            <div class="login-error" id="loginError">Invalid credentials</div>
        </form>
        <div class="login-footer">Default: admin / password</div>
    </div>

    <!-- Dashboard -->
    <div class="dashboard" id="dashboard">
        <div class="dash-header">
            <div class="left">
                <h1><i class="fas fa-hotel"></i> HostelHub</h1>
            </div>
            <div class="right">
                <div class="user-badge"><i class="fas fa-user-circle"></i> <span id="currentUserDisplay">Admin</span></div>
                <button class="logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>

        <!-- Admin View -->
        <div id="adminView">
            <div class="stat-grid" id="statsGrid">
                <div class="stat-card"><i class="fas fa-door-open"></i><div class="info"><span class="label">Rooms</span><span class="value" id="totalRooms">0</span></div></div>
                <div class="stat-card"><i class="fas fa-user-graduate"></i><div class="info"><span class="label">Occupied</span><span class="value" id="occupiedCount">0</span></div></div>
                <div class="stat-card"><i class="fas fa-user-plus"></i><div class="info"><span class="label">Available</span><span class="value" id="availableCount">0</span></div></div>
                <div class="stat-card"><i class="fas fa-users"></i><div class="info"><span class="label">Guests</span><span class="value" id="guestCount">0</span></div></div>
            </div>

            <div class="admin-bar">
                <div class="btn-group">
                    <button class="btn btn-primary" id="addRoomBtn"><i class="fas fa-plus-circle"></i> Add Room</button>
                    <button class="btn btn-success" id="checkinBtn"><i class="fas fa-user-plus"></i> Check-in</button>
                    <button class="btn btn-warning" id="registerUserBtn"><i class="fas fa-user-cog"></i> Register User</button>
                    <button class="btn btn-outline" id="addPaymentBtn"><i class="fas fa-coins"></i> Add Payment</button>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline" id="refreshBtn"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
            </div>

            <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-list-ul" style="color:#1a5fb0;"></i> Room Inventory</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Room</th><th>Type</th><th>Status</th><th>Guest</th><th style="text-align:right;">Actions</th></tr></thead>
                    <tbody id="roomTableBody"></tbody>
                </table>
            </div>

            <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-users" style="color:#1a5fb0;"></i> All Guests</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Payments</th><th style="text-align:right;">Actions</th></tr></thead>
                    <tbody id="guestTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- User View -->
        <div id="userView" style="display:none;">
            <div class="user-room-card">
                <div class="detail"><span class="label">Room Number</span><span class="value" id="userRoomNumber">—</span></div>
                <div class="detail"><span class="label">Room Type</span><span class="value" id="userRoomType">—</span></div>
                <div class="detail"><span class="label">Status</span><span class="value" id="userRoomStatus">—</span></div>
            </div>

            <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-user-friends" style="color:#1a5fb0;"></i> My Roommates</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Check-in Date</th></tr></thead>
                    <tbody id="userRoommatesBody"></tbody>
                </table>
            </div>

            <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-credit-card" style="color:#1a5fb0;"></i> My Payments</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody id="userPaymentsBody"></tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:1rem; font-size:0.75rem; color:#7b90ab; text-align:right;">
            <i class="fas fa-database"></i> MySQL Connected
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal-overlay" id="registerModal">
    <div class="modal-card">
        <h2><i class="fas fa-user-plus" style="color:#1a5fb0;"></i> Register New User</h2>
        <form id="registerForm">
            <div class="form-group"><label>Full Name</label><input type="text" id="regName" placeholder="John Doe" required /></div>
            <div class="form-group"><label>Username</label><input type="text" id="regUsername" placeholder="johndoe" required /></div>
            <div class="form-group"><label>Password</label><input type="password" id="regPassword" placeholder="••••••" required /></div>
            <div class="form-group"><label>Role</label>
                <select id="regRole"><option value="user">User</option><option value="admin">Admin</option></select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="regCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
        <h2><i class="fas fa-coins" style="color:#1a5fb0;"></i> Add Payment</h2>
        <form id="paymentForm">
            <div class="form-group"><label>Guest</label>
                <select id="paymentGuest"><option value="">Select guest</option></select>
            </div>
            <div class="form-group"><label>Amount ($)</label><input type="number" id="paymentAmount" placeholder="0.00" step="0.01" required /></div>
            <div class="form-group"><label>Date</label><input type="date" id="paymentDate" required /></div>
            <div class="form-group"><label>Status</label>
                <select id="paymentStatus"><option value="paid">Paid</option><option value="pending">Pending</option></select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="paymentCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = 'api.php';
    let currentUser = null;
    let authToken = null;

    // DOM refs
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loginWrapper = document.getElementById('loginWrapper');
    const dashboard = document.getElementById('dashboard');
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    const loginUsername = document.getElementById('loginUsername');
    const loginPassword = document.getElementById('loginPassword');
    const currentUserDisplay = document.getElementById('currentUserDisplay');
    const logoutBtn = document.getElementById('logoutBtn');
    const adminView = document.getElementById('adminView');
    const userView = document.getElementById('userView');
    const roomTableBody = document.getElementById('roomTableBody');
    const guestTableBody = document.getElementById('guestTableBody');
    const totalRoomsEl = document.getElementById('totalRooms');
    const occupiedCountEl = document.getElementById('occupiedCount');
    const availableCountEl = document.getElementById('availableCount');
    const guestCountEl = document.getElementById('guestCount');

    // User view refs
    const userRoomNumber = document.getElementById('userRoomNumber');
    const userRoomType = document.getElementById('userRoomType');
    const userRoomStatus = document.getElementById('userRoomStatus');
    const userRoommatesBody = document.getElementById('userRoommatesBody');
    const userPaymentsBody = document.getElementById('userPaymentsBody');

    // Modal refs
    const registerModal = document.getElementById('registerModal');
    const registerForm = document.getElementById('registerForm');
    const regCancelBtn = document.getElementById('regCancelBtn');
    const paymentModal = document.getElementById('paymentModal');
    const paymentForm = document.getElementById('paymentForm');
    const paymentGuest = document.getElementById('paymentGuest');
    const paymentCancelBtn = document.getElementById('paymentCancelBtn');

    // --- API Helpers ---
    async function apiRequest(action, method = 'GET', data = null) {
        const url = `${API_URL}?action=${action}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (authToken) {
            options.headers['Authorization'] = `Bearer ${authToken}`;
        }

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { error: 'Connection failed' };
        }
    }

    // --- Login ---
    async function login(username, password) {
        loadingOverlay.classList.add('active');
        try {
            const result = await apiRequest('login', 'POST', { username, password });
            if (result.success) {
                currentUser = result.user;
                authToken = result.token;
                enterDashboard();
            } else {
                loginError.style.display = 'block';
                loginError.textContent = result.error || 'Invalid credentials';
            }
        } catch (error) {
            loginError.style.display = 'block';
            loginError.textContent = 'Connection error';
        }
        loadingOverlay.classList.remove('active');
    }

    function enterDashboard() {
        loginWrapper.style.display = 'none';
        dashboard.classList.add('active');
        currentUserDisplay.textContent = currentUser.full_name + (currentUser.role === 'admin' ? ' (Admin)' : '');
        
        if (currentUser.role === 'admin') {
            adminView.style.display = 'block';
            userView.style.display = 'none';
            loadAdminData();
        } else {
            adminView.style.display = 'none';
            userView.style.display = 'block';
            loadUserData();
        }
    }

    function logout() {
        currentUser = null;
        authToken = null;
        dashboard.classList.remove('active');
        loginWrapper.style.display = 'block';
        loginForm.reset();
        loginError.style.display = 'none';
    }

    // --- Admin Data ---
    async function loadAdminData() {
        loadingOverlay.classList.add('active');
        try {
            // Load stats
            const stats = await apiRequest('stats');
            if (!stats.error) {
                totalRoomsEl.textContent = stats.totalRooms || 0;
                occupiedCountEl.textContent = stats.occupied || 0;
                availableCountEl.textContent = stats.available || 0;
                guestCountEl.textContent = stats.guests || 0;
            }

            // Load rooms
            const rooms = await apiRequest('rooms');
            if (!rooms.error) {
                let html = '';
                rooms.forEach(room => {
                    const guestName = room.guest_name || '—';
                    let statusClass = 'badge';
                    if (room.status === 'available') statusClass += ' available';
                    else if (room.status === 'occupied') statusClass += ' occupied';

                    html += `
                        <tr>
                            <td><strong>${room.room_number}</strong></td>
                            <td>${room.room_type}</td>
                            <td><span class="${statusClass}">${room.status}</span></td>
                            <td>${guestName}</td>
                            <td style="text-align:right;" class="action-icons">
                                <i class="fas fa-edit" data-action="editRoom" data-id="${room.id}"></i>
                                <i class="fas fa-trash" data-action="deleteRoom" data-id="${room.id}"></i>
                                ${room.status === 'available' ? `<i class="fas fa-user-check" data-action="checkin" data-id="${room.id}" style="color:#1e7b3a;"></i>` : ''}
                                ${room.status === 'occupied' ? `<i class="fas fa-user-minus" data-action="checkout" data-id="${room.id}" style="color:#b13e3e;"></i>` : ''}
                            </td>
                        </tr>
                    `;
                });
                roomTableBody.innerHTML = html;
            }

            // Load guests
            const guests = await apiRequest('guests');
            if (!guests.error) {
                let html = '';
                for (const g of guests) {
                    const payments = await apiRequest(`payments&guest_id=${g.id}`);
                    const paymentStatus = payments && payments.length > 0 ?
                        payments.map(p => `<span class="badge ${p.status}">$${p.amount.toFixed(2)} ${p.status}</span>`).join(' ') :
                        '<span style="color:#7a8aa3;">—</span>';

                    html += `
                        <tr>
                            <td><i class="fas fa-user-circle" style="color:#1a5fb0; margin-right:8px;"></i>${g.name}</td>
                            <td>Room ${g.room_number}</td>
                            <td>${g.check_in}</td>
                            <td>${paymentStatus}</td>
                            <td style="text-align:right;" class="action-icons">
                                <i class="fas fa-user-edit" data-action="editGuest" data-id="${g.id}"></i>
                                <i class="fas fa-trash" data-action="deleteGuest" data-id="${g.id}"></i>
                            </td>
                        </tr>
                    `;
                }
                guestTableBody.innerHTML = html;
            }
        } catch (error) {
            console.error('Error loading admin data:', error);
        }
        loadingOverlay.classList.remove('active');
    }

    // --- User Data ---
    async function loadUserData() {
        loadingOverlay.classList.add('active');
        try {
            const userRoom = await apiRequest(`userroom&user_id=${currentUser.id}`);
            
            if (userRoom && !userRoom.error && userRoom.id) {
                userRoomNumber.textContent = userRoom.room_number || '—';
                userRoomType.textContent = userRoom.room_type || '—';
                userRoomStatus.textContent = userRoom.status || '—';

                // Load roommates
                const roommates = await apiRequest(`guests`);
                if (!roommates.error) {
                    const mates = roommates.filter(g => g.room_id === userRoom.room_id && g.id !== userRoom.id);
                    let html = '';
                    if (mates.length === 0) {
                        html = '<tr><td colspan="2" style="text-align:center; color:#7a8aa3;">No roommates</td></tr>';
                    } else {
                        mates.forEach(m => {
                            html += `<tr><td><i class="fas fa-user-circle" style="color:#1a5fb0; margin-right:8px;"></i>${m.name}</td><td>${m.check_in}</td></tr>`;
                        });
                    }
                    userRoommatesBody.innerHTML = html;
                }

                // Load payments
                const payments = await apiRequest(`payments&guest_id=${userRoom.id}`);
                let html = '';
                if (!payments.error && payments.length > 0) {
                    payments.forEach(p => {
                        html += `<tr><td>$${p.amount.toFixed(2)}</td><td>${p.payment_date}</td><td><span class="badge ${p.status}">${p.status}</span></td></tr>`;
                    });
                } else {
                    html = '<tr><td colspan="3" style="text-align:center; color:#7a8aa3;">No payments</td></tr>';
                }
                userPaymentsBody.innerHTML = html;
            } else {
                userRoomNumber.textContent = 'No room assigned';
                userRoomType.textContent = '—';
                userRoomStatus.textContent = '—';
                userRoommatesBody.innerHTML = '<tr><td colspan="2" style="text-align:center; color:#7a8aa3;">No room assigned</td></tr>';
                userPaymentsBody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:#7a8aa3;">No payments</td></tr>';
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
        loadingOverlay.classList.remove('active');
    }

    // --- Admin Actions ---
    async function addRoom(number, type, capacity, status) {
        const result = await apiRequest('room', 'POST', {
            room_number: number,
            room_type: type,
            capacity: parseInt(capacity),
            status: status
        });
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Failed to add room');
        }
    }

    async function deleteRoom(id) {
        if (!confirm('Delete this room?')) return;
        const result = await apiRequest(`room&id=${id}`, 'DELETE');
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Failed to delete room');
        }
    }

    async function checkInGuest(roomId, name, checkIn) {
        const result = await apiRequest('checkin', 'POST', {
            room_id: roomId,
            name: name,
            check_in: checkIn || new Date().toISOString().split('T')[0]
        });
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Check-in failed');
        }
    }

    async function checkoutGuest(roomId) {
        if (!confirm('Checkout guest?')) return;
        const result = await apiRequest(`checkout&room_id=${roomId}`, 'POST');
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Checkout failed');
        }
    }

    async function deleteGuest(id) {
        if (!confirm('Remove guest?')) return;
        const result = await apiRequest(`guest&id=${id}`, 'DELETE');
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Failed to remove guest');
        }
    }

    async function addPayment(guestId, amount, date, status) {
        const result = await apiRequest('payment', 'POST', {
            guest_id: guestId,
            amount: parseFloat(amount),
            payment_date: date,
            status: status
        });
        if (result.success) {
            loadAdminData();
        } else {
            alert(result.error || 'Failed to add payment');
        }
    }

    async function registerUser(fullName, username, password, role) {
        const result = await apiRequest('register', 'POST', {
            full_name: fullName,
            username: username,
            password: password,
            role: role
        });
        if (result.success) {
            alert('User registered successfully!');
            registerModal.classList.remove('active');
            registerForm.reset();
        } else {
            alert(result.error || 'Registration failed');
        }
    }

    // --- Event Listeners ---
    // Login
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = loginUsername.value.trim();
        const password = loginPassword.value.trim();
        login(username, password);
    });

    // Logout
    logoutBtn.addEventListener('click', logout);

    // Add Room
    document.getElementById('addRoomBtn').addEventListener('click', function() {
        const num = prompt('Room number:');
        if (!num) return;
        const type = prompt('Type (Single/Double/Dorm/Suite):', 'Single') || 'Single';
        const cap = prompt('Capacity:', '1') || '1';
        const status = prompt('Status (available/occupied/maintenance):', 'available') || 'available';
        addRoom(num.trim(), type.trim(), parseInt(cap) || 1, status.trim());
    });

    // Check-in
    document.getElementById('checkinBtn').addEventListener('click', async function() {
        const rooms = await apiRequest('rooms');
        if (rooms.error) return;
        const avail = rooms.filter(r => r.status === 'available');
        if (avail.length === 0) { alert('No available rooms.'); return; }
        const list = avail.map(r => `${r.id}:${r.room_number}`).join(', ');
        const idInput = prompt(`Enter room ID (available: ${list})`);
        if (!idInput) return;
        const roomId = parseInt(idInput);
        const room = avail.find(r => r.id === roomId);
        if (!room) { alert('Invalid room.'); return; }
        const name = prompt('Guest name:');
        if (name && name.trim()) checkInGuest(roomId, name.trim());
    });

    // Register User
    document.getElementById('registerUserBtn').addEventListener('click', function() {
        registerModal.classList.add('active');
    });
    regCancelBtn.addEventListener('click', () => registerModal.classList.remove('active'));
    registerModal.addEventListener('click', function(e) {
        if (e.target === registerModal) registerModal.classList.remove('active');
    });

    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const fullName = document.getElementById('regName').value.trim();
        const username = document.getElementById('regUsername').value.trim();
        const password = document.getElementById('regPassword').value.trim();
        const role = document.getElementById('regRole').value;
        if (!fullName || !username || !password) { alert('All fields required.'); return; }
        registerUser(fullName, username, password, role);
    });

    // Add Payment
    document.getElementById('addPaymentBtn').addEventListener('click', async function() {
        const guests = await apiRequest('guests');
        if (guests.error) return;
        paymentGuest.innerHTML = '<option value="">Select guest</option>';
        guests.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = `${g.name} (Room ${g.room_number})`;
            paymentGuest.appendChild(opt);
        });
        document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
        paymentModal.classList.add('active');
    });
    paymentCancelBtn.addEventListener('click', () => paymentModal.classList.remove('active'));
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal) paymentModal.classList.remove('active');
    });

    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const guestId = parseInt(paymentGuest.value);
        const amount = parseFloat(document.getElementById('paymentAmount').value);
        const date = document.getElementById('paymentDate').value;
        const status = document.getElementById('paymentStatus').value;
        if (!guestId || !amount || !date) { alert('All fields required.'); return; }
        addPayment(guestId, amount, date, status);
        paymentModal.classList.remove('active');
        paymentForm.reset();
    });

    // Refresh
    document.getElementById('refreshBtn').addEventListener('click', function() {
        if (currentUser.role === 'admin') {
            loadAdminData();
        } else {
            loadUserData();
        }
    });

    // Action delegation for table icons
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;
        const action = target.dataset.action;
        const id = parseInt(target.dataset.id);
        if (!currentUser || currentUser.role !== 'admin') return;

        if (action === 'editRoom') {
            // Simplified edit - prompt for each field
            const room = roomTableBody.querySelector(`tr td:first-child`); // We'll just prompt
            const newNum = prompt('Room number:');
            if (newNum === null) return;
            const newType = prompt('Type (Single/Double/Dorm/Suite):', 'Single');
            if (newType === null) return;
            const newCap = prompt('Capacity:', '1');
            if (newCap === null) return;
            const newStatus = prompt('Status (available/occupied/maintenance):', 'available');
            if (newStatus === null) return;
            
            apiRequest(`room&id=${id}`, 'PUT', {
                room_number: newNum.trim(),
                room_type: newType.trim(),
                capacity: parseInt(newCap) || 1,
                status: newStatus.trim()
            }).then(result => {
                if (result.success) loadAdminData();
                else alert(result.error || 'Update failed');
            });
        } else if (action === 'deleteRoom') {
            deleteRoom(id);
        } else if (action === 'checkin') {
            const name = prompt('Guest name:');
            if (name && name.trim()) checkInGuest(id, name.trim());
        } else if (action === 'checkout') {
            checkoutGuest(id);
        } else if (action === 'editGuest') {
            const newName = prompt('Edit guest name:');
            if (newName && newName.trim()) {
                // We'll just show a message since edit guest is complex
                alert('Guest name update would be handled here.');
            }
        } else if (action === 'deleteGuest') {
            deleteGuest(id);
        }
    });

    // Check if already logged in (session would persist, but we'll start fresh)
    console.log('HostelPro ready. Use admin/password to login.');
</script>

</body>
</html>