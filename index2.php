<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HostelPro · User & Admin Portal</title>
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
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }

    /* ---------- LOGIN CARD ---------- */
    .login-wrapper {
      width: 100%;
      max-width: 420px;
      background: white;
      border-radius: 48px;
      padding: 2.8rem 2rem 2.2rem;
      box-shadow: 0 30px 60px -20px rgba(0, 20, 40, 0.25);
      transition: 0.2s;
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
      letter-spacing: -0.5px;
    }
    .login-wrapper .brand span {
      color: #4c6f96;
      font-weight: 400;
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
      transition: 0.2s;
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
      margin-top: 0.3rem;
    }
    .login-btn:hover {
      background: #134b8f;
      transform: scale(1.01);
    }
    .login-error {
      color: #b13e3e;
      font-size: 0.8rem;
      margin-top: 0.8rem;
      text-align: center;
      background: #fce8e8;
      padding: 0.4rem;
      border-radius: 40px;
      display: none;
    }
    .login-footer {
      margin-top: 1.8rem;
      text-align: center;
      font-size: 0.75rem;
      color: #6b85a3;
    }

    /* ---------- DASHBOARD (hidden by default) ---------- */
    .dashboard {
      display: none;
      width: 100%;
      max-width: 1400px;
      background: white;
      border-radius: 44px;
      padding: 2rem 2rem 2.2rem;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2);
    }
    .dashboard.active {
      display: block;
    }

    /* top bar */
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

    /* stats */
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
      box-shadow: 0 2px 6px rgba(0,0,0,0.01);
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

    /* admin action bar */
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
    .btn-primary:hover { background: #134b8f; }
    .btn-outline { background: transparent; border-color: #cbd5e1; }
    .btn-outline:hover { background: #e6edf8; }
    .btn-success { background: #0f7b3a; color: white; }
    .btn-danger { background: #b13e3e; color: white; }
    .btn-warning { background: #b8860b; color: white; }

    /* tables */
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
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafcff; }
    .badge {
      background: #e6edf8;
      padding: 0.2rem 0.9rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .badge.available { background: #d9f0e1; color: #0b5e2e; }
    .badge.occupied { background: #fce8e8; color: #a13232; }
    .badge.checked-in { background: #d4e3ff; color: #144e8a; }
    .badge.paid { background: #d4edda; color: #0b5e2e; }
    .badge.pending { background: #fff3cd; color: #856404; }
    .action-icons i {
      margin: 0 4px;
      color: #4a6e96;
      cursor: pointer;
      font-size: 0.95rem;
      transition: 0.1s;
    }
    .action-icons i:hover { color: #0b2b4a; transform: scale(1.05); }

    .roommate-chip {
      background: #ecf3fa;
      padding: 0.15rem 0.9rem;
      border-radius: 30px;
      font-size: 0.75rem;
      margin-right: 4px;
      display: inline-block;
    }

    /* user card (for user dashboard) */
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

    /* modal */
    .modal-overlay {
      display: none;
      position: fixed;
      top:0; left:0; width:100%; height:100%;
      background: rgba(0,0,0,0.25);
      backdrop-filter: blur(4px);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .modal-overlay.active { display: flex; }
    .modal-card {
      background: white;
      max-width: 520px;
      width: 92%;
      padding: 2.2rem 2.2rem 1.8rem;
      border-radius: 44px;
      box-shadow: 0 40px 80px -20px rgba(0,0,0,0.4);
      animation: fadeUp 0.2s ease;
    }
    @keyframes fadeUp { 0% { opacity:0; transform:translateY(20px); } 100% { opacity:1; transform:translateY(0); } }
    .modal-card h2 { margin-bottom: 1.5rem; color: #0b2b4a; display: flex; align-items: center; gap: 12px; }
    .modal-card .form-group { margin-bottom: 1.2rem; }
    .modal-card label { font-weight: 600; font-size:0.8rem; color:#1d3755; display:block; margin-bottom:4px; }
    .modal-card input, .modal-card select {
      width:100%; padding:0.7rem 1.2rem; border:1px solid #d0dbea; border-radius:40px;
      background:#fafcff; font-size:0.95rem;
    }
    .modal-card input:focus, .modal-card select:focus { outline:none; border-color:#1a5fb0; box-shadow:0 0 0 4px rgba(26,95,176,0.1); }
    .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 1.8rem; }

    @media (max-width: 700px) {
      .dashboard { padding: 1.2rem; }
      .admin-bar { flex-direction: column; align-items: stretch; }
      .user-room-card { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
    }
  </style>
</head>
<body>

<!-- ========== LOGIN ========== -->
<div class="login-wrapper" id="loginWrapper">
  <div class="brand">
    <i class="fas fa-user-shield"></i>
    <h2>HostelPro</h2>
    <span>secure · integrated</span>
  </div>
  <form class="login-form" id="loginForm">
    <div class="form-group">
      <label><i class="fas fa-user"></i> Username</label>
      <input type="text" id="loginUsername" placeholder="Enter your username" value="admin" />
    </div>
    <div class="form-group">
      <label><i class="fas fa-lock"></i> Password</label>
      <input type="password" id="loginPassword" placeholder="••••••••" value="1234" />
    </div>
    <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> Log in</button>
    <div class="login-error" id="loginError">Invalid credentials</div>
  </form>
  <div class="login-footer">demo: admin/1234 · user/1234</div>
</div>

<!-- ========== DASHBOARD ========== -->
<div class="dashboard" id="dashboard">
  <!-- header -->
  <div class="dash-header">
    <div class="left">
      <h1><i class="fas fa-hotel"></i> HostelHub <span style="font-size:0.9rem; font-weight:400; color:#4c6f96;">· dashboard</span></h1>
    </div>
    <div class="right">
      <div class="user-badge"><i class="fas fa-user-circle"></i> <span id="currentUserDisplay">Admin</span></div>
      <button class="logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </div>

  <!-- ===== ADMIN VIEW ===== -->
  <div id="adminView">
    <!-- stats -->
    <div class="stat-grid">
      <div class="stat-card"><i class="fas fa-door-open"></i><div class="info"><span class="label">Rooms</span><span class="value" id="totalRooms">0</span></div></div>
      <div class="stat-card"><i class="fas fa-user-graduate"></i><div class="info"><span class="label">Occupied</span><span class="value" id="occupiedCount">0</span></div></div>
      <div class="stat-card"><i class="fas fa-user-plus"></i><div class="info"><span class="label">Available</span><span class="value" id="availableCount">0</span></div></div>
      <div class="stat-card"><i class="fas fa-users"></i><div class="info"><span class="label">Guests</span><span class="value" id="guestCount">0</span></div></div>
    </div>

    <!-- admin bar -->
    <div class="admin-bar" id="adminBar">
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

    <!-- rooms table -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin: 1.8rem 0 0.6rem;">
      <h3 style="color:#0b2b4a;"><i class="fas fa-list-ul" style="color:#1a5fb0;"></i> Room Inventory</h3>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Room</th><th>Type</th><th>Status</th><th>Guest</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="roomTableBody"></tbody>
      </table>
    </div>

    <!-- guests -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin: 1.8rem 0 0.6rem;">
      <h3 style="color:#0b2b4a;"><i class="fas fa-users" style="color:#1a5fb0;"></i> All Guests</h3>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Payments</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody id="guestTableBody"></tbody>
      </table>
    </div>
  </div>

  <!-- ===== USER VIEW ===== -->
  <div id="userView" style="display:none;">
    <div class="user-room-card" id="userRoomCard">
      <div class="detail"><span class="label">Room Number</span><span class="value" id="userRoomNumber">—</span></div>
      <div class="detail"><span class="label">Room Type</span><span class="value" id="userRoomType">—</span></div>
      <div class="detail"><span class="label">Status</span><span class="value" id="userRoomStatus">—</span></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin: 1.8rem 0 0.6rem;">
      <h3 style="color:#0b2b4a;"><i class="fas fa-user-friends" style="color:#1a5fb0;"></i> My Roommates</h3>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Check-in Date</th></tr></thead>
        <tbody id="userRoommatesBody"></tbody>
      </table>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin: 1.8rem 0 0.6rem;">
      <h3 style="color:#0b2b4a;"><i class="fas fa-credit-card" style="color:#1a5fb0;"></i> My Payments</h3>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
        <tbody id="userPaymentsBody"></tbody>
      </table>
    </div>
  </div>

  <div style="margin-top:1rem; font-size:0.75rem; color:#7b90ab; text-align:right;">
    <i class="fas fa-database"></i> fully integrated · real-time sync
  </div>
</div>

<!-- ========== REGISTER USER MODAL ========== -->
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

<!-- ========== PAYMENT MODAL ========== -->
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
  (function() {
    "use strict";

    // ---------- MOCK DATABASE ----------
    let users = [
      { id: 1, username: 'admin', password: '1234', role: 'admin', fullName: 'Admin Manager' },
      { id: 2, username: 'user', password: '1234', role: 'user', fullName: 'Regular User' },
      { id: 3, username: 'alice', password: '1234', role: 'user', fullName: 'Alice Johnson' },
      { id: 4, username: 'bob', password: '1234', role: 'user', fullName: 'Bob Smith' },
    ];

    let rooms = [
      { id: 1, number: '101', type: 'Single', capacity: 1, status: 'available', guestId: null },
      { id: 2, number: '102', type: 'Double', capacity: 2, status: 'occupied', guestId: 101 },
      { id: 3, number: '103', type: 'Dorm', capacity: 4, status: 'available', guestId: null },
      { id: 4, number: '104', type: 'Suite', capacity: 3, status: 'maintenance', guestId: null },
      { id: 5, number: '105', type: 'Double', capacity: 2, status: 'occupied', guestId: 102 },
    ];

    let guests = [
      { id: 101, name: 'Alice Johnson', roomId: 2, checkIn: '2026-09-01' },
      { id: 102, name: 'Bob Smith', roomId: 5, checkIn: '2026-09-03' },
    ];

    let payments = [
      { id: 1, guestId: 101, amount: 450.00, date: '2026-09-05', status: 'paid' },
      { id: 2, guestId: 101, amount: 450.00, date: '2026-10-05', status: 'pending' },
      { id: 3, guestId: 102, amount: 600.00, date: '2026-09-10', status: 'paid' },
    ];

    let roomIdCounter = 6;
    let guestIdCounter = 103;
    let userIdCounter = 5;
    let paymentIdCounter = 4;

    // ---------- SESSION ----------
    let currentUser = null;

    // DOM refs
    const loginWrapper = document.getElementById('loginWrapper');
    const dashboard = document.getElementById('dashboard');
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    const currentUserDisplay = document.getElementById('currentUserDisplay');
    const logoutBtn = document.getElementById('logoutBtn');

    const adminView = document.getElementById('adminView');
    const userView = document.getElementById('userView');
    const adminBar = document.getElementById('adminBar');

    const roomTableBody = document.getElementById('roomTableBody');
    const guestTableBody = document.getElementById('guestTableBody');
    const totalRoomsEl = document.getElementById('totalRooms');
    const occupiedCountEl = document.getElementById('occupiedCount');
    const availableCountEl = document.getElementById('availableCount');
    const guestCountEl = document.getElementById('guestCount');

    // user view elements
    const userRoomNumber = document.getElementById('userRoomNumber');
    const userRoomType = document.getElementById('userRoomType');
    const userRoomStatus = document.getElementById('userRoomStatus');
    const userRoommatesBody = document.getElementById('userRoommatesBody');
    const userPaymentsBody = document.getElementById('userPaymentsBody');

    // modals
    const registerModal = document.getElementById('registerModal');
    const registerForm = document.getElementById('registerForm');
    const regCancelBtn = document.getElementById('regCancelBtn');
    const paymentModal = document.getElementById('paymentModal');
    const paymentForm = document.getElementById('paymentForm');
    const paymentGuest = document.getElementById('paymentGuest');
    const paymentCancelBtn = document.getElementById('paymentCancelBtn');

    // ---------- HELPERS ----------
    function getRoomById(id) { return rooms.find(r => r.id === id); }
    function getGuestById(id) { return guests.find(g => g.id === id); }
    function getGuestByRoom(roomId) { return guests.find(g => g.roomId === roomId); }
    function getGuestByUserId(userId) {
      const user = users.find(u => u.id === userId);
      if (!user) return null;
      return guests.find(g => g.name === user.fullName);
    }
    function getRoommates(roomId, excludeId) {
      return guests.filter(g => g.roomId === roomId && g.id !== excludeId);
    }
    function getPaymentsByGuest(guestId) {
      return payments.filter(p => p.guestId === guestId);
    }

    // ---------- RENDER ----------
    function renderAll() {
      if (currentUser.role === 'admin') {
        renderAdminView();
      } else {
        renderUserView();
      }
    }

    function renderAdminView() {
      adminView.style.display = 'block';
      userView.style.display = 'none';
      adminBar.style.display = 'flex';

      let roomHtml = '';
      rooms.forEach(room => {
        const guest = getGuestByRoom(room.id);
        const guestName = guest ? guest.name : '—';
        let statusClass = 'badge';
        if (room.status === 'available') statusClass += ' available';
        else if (room.status === 'occupied') statusClass += ' occupied';

        roomHtml += `
          <tr>
            <td><strong>${room.number}</strong></td>
            <td>${room.type}</td>
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
      roomTableBody.innerHTML = roomHtml;

      let guestHtml = '';
      guests.forEach(g => {
        const room = getRoomById(g.roomId);
        const roomNumber = room ? room.number : '❓';
        const guestPayments = getPaymentsByGuest(g.id);
        const paymentStatus = guestPayments.length > 0 ? guestPayments.map(p => 
          `<span class="badge ${p.status}">$${p.amount} ${p.status}</span>`
        ).join(' ') : '<span style="color:#7a8aa3;">—</span>';

        guestHtml += `
          <tr>
            <td><i class="fas fa-user-circle" style="color:#1a5fb0; margin-right:8px;"></i>${g.name}</td>
            <td>Room ${roomNumber}</td>
            <td>${g.checkIn}</td>
            <td>${paymentStatus}</td>
            <td style="text-align:right;" class="action-icons">
              <i class="fas fa-user-edit" data-action="editGuest" data-id="${g.id}"></i>
              <i class="fas fa-trash" data-action="deleteGuest" data-id="${g.id}"></i>
            </td>
          </tr>
        `;
      });
      guestTableBody.innerHTML = guestHtml;

      // update stats
      const total = rooms.length;
      const occupied = rooms.filter(r => r.status === 'occupied').length;
      const available = rooms.filter(r => r.status === 'available').length;
      totalRoomsEl.textContent = total;
      occupiedCountEl.textContent = occupied;
      availableCountEl.textContent = available;
      guestCountEl.textContent = guests.length;
    }

    function renderUserView() {
      adminView.style.display = 'none';
      userView.style.display = 'block';
      adminBar.style.display = 'none';

      // find user's guest record
      const userGuest = getGuestByUserId(currentUser.id);
      if (!userGuest) {
        userRoomNumber.textContent = 'No room assigned';
        userRoomType.textContent = '—';
        userRoomStatus.textContent = '—';
        userRoommatesBody.innerHTML = '<tr><td colspan="2" style="text-align:center; color:#7a8aa3;">No room assigned yet</td></tr>';
        userPaymentsBody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:#7a8aa3;">No payments</td></tr>';
        return;
      }

      const room = getRoomById(userGuest.roomId);
      if (room) {
        userRoomNumber.textContent = room.number;
        userRoomType.textContent = room.type;
        userRoomStatus.textContent = room.status;
      } else {
        userRoomNumber.textContent = '—';
        userRoomType.textContent = '—';
        userRoomStatus.textContent = '—';
      }

      // roommates
      const roommates = getRoommates(userGuest.roomId, userGuest.id);
      let matesHtml = '';
      if (roommates.length === 0) {
        matesHtml = '<tr><td colspan="2" style="text-align:center; color:#7a8aa3;">No roommates</td></tr>';
      } else {
        roommates.forEach(r => {
          matesHtml += `<tr><td><i class="fas fa-user-circle" style="color:#1a5fb0; margin-right:8px;"></i>${r.name}</td><td>${r.checkIn}</td></tr>`;
        });
      }
      userRoommatesBody.innerHTML = matesHtml;

      // payments
      const userPayments = getPaymentsByGuest(userGuest.id);
      let payHtml = '';
      if (userPayments.length === 0) {
        payHtml = '<tr><td colspan="3" style="text-align:center; color:#7a8aa3;">No payments recorded</td></tr>';
      } else {
        userPayments.forEach(p => {
          payHtml += `<tr><td>$${p.amount.toFixed(2)}</td><td>${p.date}</td><td><span class="badge ${p.status}">${p.status}</span></td></tr>`;
        });
      }
      userPaymentsBody.innerHTML = payHtml;
    }

    // ---------- AUTH ----------
    function login(username, password) {
      const user = users.find(u => u.username === username && u.password === password);
      if (!user) {
        loginError.style.display = 'block';
        return false;
      }
      loginError.style.display = 'none';
      currentUser = user;
      enterDashboard();
      return true;
    }

    function enterDashboard() {
      loginWrapper.style.display = 'none';
      dashboard.classList.add('active');
      currentUserDisplay.textContent = currentUser.fullName + (currentUser.role === 'admin' ? ' (Admin)' : '');
      renderAll();
    }

    function logout() {
      currentUser = null;
      dashboard.classList.remove('active');
      loginWrapper.style.display = 'block';
      loginForm.reset();
      loginError.style.display = 'none';
    }

    // ---------- CRUD (admin only) ----------
    function addRoom(number, type, capacity, status) {
      const newRoom = { id: roomIdCounter++, number, type, capacity: parseInt(capacity,10)||1, status, guestId: null };
      rooms.push(newRoom);
      renderAll();
    }

    function updateRoom(id, number, type, capacity, status) {
      const room = getRoomById(id);
      if (!room) return;
      if (status === 'available' && room.status === 'occupied') {
        const guest = getGuestByRoom(id);
        if (guest) guests = guests.filter(g => g.id !== guest.id);
      }
      room.number = number; room.type = type; room.capacity = parseInt(capacity,10)||1; room.status = status;
      renderAll();
    }

    function deleteRoom(id) {
      const room = getRoomById(id);
      if (!room) return;
      const guest = getGuestByRoom(id);
      if (guest) guests = guests.filter(g => g.id !== guest.id);
      rooms = rooms.filter(r => r.id !== id);
      renderAll();
    }

    function checkInGuest(roomId, guestName, checkInDate) {
      const room = getRoomById(roomId);
      if (!room || room.status !== 'available') return false;
      const newGuest = { id: guestIdCounter++, name: guestName.trim(), roomId, checkIn: checkInDate || new Date().toISOString().split('T')[0] };
      guests.push(newGuest);
      room.status = 'occupied';
      room.guestId = newGuest.id;
      renderAll();
      return true;
    }

    function checkoutGuest(roomId) {
      const room = getRoomById(roomId);
      if (!room) return;
      const guest = getGuestByRoom(roomId);
      if (guest) guests = guests.filter(g => g.id !== guest.id);
      room.status = 'available';
      room.guestId = null;
      renderAll();
    }

    function deleteGuest(guestId) {
      const guest = getGuestById(guestId);
      if (!guest) return;
      const room = getRoomById(guest.roomId);
      if (room && room.status === 'occupied') { room.status = 'available'; room.guestId = null; }
      guests = guests.filter(g => g.id !== guestId);
      renderAll();
    }

    function addPayment(guestId, amount, date, status) {
      const newPayment = { id: paymentIdCounter++, guestId, amount: parseFloat(amount), date, status };
      payments.push(newPayment);
      renderAll();
      return true;
    }

    function registerUser(fullName, username, password, role) {
      if (users.find(u => u.username === username)) {
        alert('Username already exists.');
        return false;
      }
      users.push({ id: userIdCounter++, username, password, role, fullName });
      alert(`User ${username} registered successfully.`);
      return true;
    }

    // ---------- EVENT DELEGATION ----------
    document.addEventListener('click', function(e) {
      const target = e.target.closest('[data-action]');
      if (!target) return;
      const action = target.dataset.action;
      const id = parseInt(target.dataset.id, 10);
      if (!currentUser || currentUser.role !== 'admin') return;

      if (action === 'editRoom') {
        const room = getRoomById(id);
        if (!room) return;
        const newNum = prompt('Room number:', room.number);
        if (newNum === null) return;
        const newType = prompt('Type (Single/Double/Dorm/Suite):', room.type);
        if (newType === null) return;
        const newCap = prompt('Capacity:', room.capacity);
        if (newCap === null) return;
        const newStatus = prompt('Status (available/occupied/maintenance):', room.status);
        if (newStatus === null) return;
        updateRoom(id, newNum.trim(), newType.trim(), parseInt(newCap,10)||1, newStatus.trim());
      } else if (action === 'deleteRoom') {
        if (confirm('Delete room?')) deleteRoom(id);
      } else if (action === 'checkin') {
        const name = prompt('Guest name:');
        if (name && name.trim()) checkInGuest(id, name.trim());
      } else if (action === 'checkout') {
        if (confirm('Checkout guest?')) checkoutGuest(id);
      } else if (action === 'editGuest') {
        const g = getGuestById(id);
        if (!g) return;
        const newName = prompt('Edit guest name:', g.name);
        if (newName && newName.trim()) { g.name = newName.trim(); renderAll(); }
      } else if (action === 'deleteGuest') {
        if (confirm('Remove guest?')) deleteGuest(id);
      }
    });

    // ---------- LOGIN ----------
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const username = document.getElementById('loginUsername').value.trim();
      const password = document.getElementById('loginPassword').value.trim();
      login(username, password);
    });

    // logout
    logoutBtn.addEventListener('click', logout);

    // add room (admin)
    document.getElementById('addRoomBtn').addEventListener('click', function() {
      if (!currentUser || currentUser.role !== 'admin') return;
      const num = prompt('Room number:');
      if (!num) return;
      const type = prompt('Type (Single/Double/Dorm/Suite):', 'Single') || 'Single';
      const cap = prompt('Capacity:', '1') || '1';
      const status = prompt('Status (available/occupied/maintenance):', 'available') || 'available';
      addRoom(num.trim(), type.trim(), parseInt(cap,10)||1, status.trim());
    });

    // check-in quick (admin)
    document.getElementById('checkinBtn').addEventListener('click', function() {
      if (!currentUser || currentUser.role !== 'admin') return;
      const avail = rooms.filter(r => r.status === 'available');
      if (avail.length === 0) { alert('No available rooms.'); return; }
      const list = avail.map(r => `${r.id}:${r.number}`).join(', ');
      const idInput = prompt(`Enter room ID (available: ${list})`);
      if (!idInput) return;
      const roomId = parseInt(idInput, 10);
      const room = getRoomById(roomId);
      if (!room || room.status !== 'available') { alert('Invalid room.'); return; }
      const name = prompt('Guest name:');
      if (name && name.trim()) checkInGuest(roomId, name.trim());
    });

    // register user modal
    document.getElementById('registerUserBtn').addEventListener('click', function() {
      if (!currentUser || currentUser.role !== 'admin') return;
      registerModal.classList.add('active');
    });
    regCancelBtn.addEventListener('click', () => registerModal.classList.remove('active'));
    registerModal.addEventListener('click', function(e) { if (e.target === registerModal) registerModal.classList.remove('active'); });

    registerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const fullName = document.getElementById('regName').value.trim();
      const username = document.getElementById('regUsername').value.trim();
      const password = document.getElementById('regPassword').value.trim();
      const role = document.getElementById('regRole').value;
      if (!fullName || !username || !password) { alert('All fields required.'); return; }
      if (registerUser(fullName, username, password, role)) {
        registerModal.classList.remove('active');
        registerForm.reset();
      }
    });

    // payment modal
    document.getElementById('addPaymentBtn').addEventListener('click', function() {
      if (!currentUser || currentUser.role !== 'admin') return;
      // populate guest dropdown
      paymentGuest.innerHTML = '<option value="">Select guest</option>';
      guests.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g.id;
        opt.textContent = `${g.name} (Room ${getRoomById(g.roomId)?.number || '?'})`;
        paymentGuest.appendChild(opt);
      });
      // set default date
      document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
      paymentModal.classList.add('active');
    });
    paymentCancelBtn.addEventListener('click', () => paymentModal.classList.remove('active'));
    paymentModal.addEventListener('click', function(e) { if (e.target === paymentModal) paymentModal.classList.remove('active'); });

    paymentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const guestId = parseInt(paymentGuest.value, 10);
      const amount = parseFloat(document.getElementById('paymentAmount').value);
      const date = document.getElementById('paymentDate').value;
      const status = document.getElementById('paymentStatus').value;
      if (!guestId || !amount || !date) { alert('All fields required.'); return; }
      if (addPayment(guestId, amount, date, status)) {
        paymentModal.classList.remove('active');
        paymentForm.reset();
      }
    });

    // refresh
    document.getElementById('refreshBtn').addEventListener('click', renderAll);

    // prefill login for demo
    // login('admin', '1234');
  })();
</script>
</body>
</html>