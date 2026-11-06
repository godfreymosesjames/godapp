<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>🏠 Hostel Management · Full Integrated</title>
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
        }

        body {
            background: #f4f7fc;
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
            min-height: 100vh;
        }

        .app-container {
            max-width: 1400px;
            width: 100%;
            background: white;
            border-radius: 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem 2rem 2.5rem;
            transition: all 0.2s;
        }

        /* header */
        .header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            gap: 1rem;
        }

        .logo h1 {
            font-size: 1.9rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #0b2b4a;
        }
        .logo i {
            color: #2a7de1;
            margin-right: 10px;
        }
        .logo span {
            background: #eef4ff;
            padding: 0.2rem 1rem;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #1f5f9e;
            margin-left: 12px;
        }

        .stats-grid {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #f8faff;
            padding: 0.5rem 1.3rem 0.5rem 1.2rem;
            border-radius: 60px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e9edf4;
            box-shadow: 0 2px 6px rgba(0,20,40,0.02);
        }
        .stat-card i {
            font-size: 1.2rem;
            color: #2a7de1;
            width: 1.8rem;
            text-align: center;
        }
        .stat-card .info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .stat-card .label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #4a5d7a;
        }
        .stat-card .value {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0b2b4a;
        }

        /* main action bar */
        .action-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem 0.5rem;
            background: #f2f6fe;
            padding: 0.75rem 1.8rem;
            border-radius: 50px;
        }
        .action-bar .left {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .action-bar .right {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            background: white;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1e3755;
            cursor: pointer;
            transition: 0.15s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid transparent;
        }
        .btn i {
            font-size: 0.9rem;
        }
        .btn-primary {
            background: #1a5fb0;
            color: white;
            box-shadow: 0 6px 12px -6px rgba(26,95,176,0.3);
        }
        .btn-primary:hover {
            background: #134b8f;
            transform: scale(1.02);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #e6edf8;
            border-color: #a0b6d4;
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

        /* tables & cards */
        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #0b2b4a;
            margin: 2rem 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: #2a7de1;
        }

        .table-wrapper {
            background: white;
            border-radius: 24px;
            border: 1px solid #e6edf6;
            overflow-x: auto;
            box-shadow: 0 8px 20px -12px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 700px;
        }

        th {
            background: #f8fafd;
            text-align: left;
            padding: 1rem 1.2rem;
            font-weight: 600;
            color: #1f3b5c;
            border-bottom: 1px solid #dce3ed;
            letter-spacing: 0.3px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        td {
            padding: 0.9rem 1.2rem;
            border-bottom: 1px solid #ebf0f7;
            color: #1f334f;
            vertical-align: middle;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background: #fafcff;
        }

        .badge {
            background: #e6edf8;
            padding: 0.25rem 0.8rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #1f4870;
        }
        .badge.available { background: #d9f0e1; color: #0b5e2e; }
        .badge.occupied { background: #fce8e8; color: #a13232; }
        .badge.maintenance { background: #fff0d9; color: #8a6700; }
        .badge.checked-in { background: #d4e3ff; color: #144e8a; }
        .badge.checked-out { background: #eaeef5; color: #4b5e77; }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 10px;
            display: inline-block;
        }
        .status-dot.green { background: #1e9b5a; }
        .status-dot.red { background: #c04a4a; }
        .status-dot.orange { background: #d48c2c; }

        .action-icons i {
            margin: 0 6px;
            color: #4a6e96;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.1s;
        }
        .action-icons i:hover {
            color: #0b2b4a;
            transform: scale(1.1);
        }
        .action-icons .fa-trash { color: #b13e3e; }
        .action-icons .fa-edit { color: #1f6bb0; }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(3px);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal {
            background: white;
            max-width: 600px;
            width: 90%;
            padding: 2rem 2.2rem;
            border-radius: 40px;
            box-shadow: 0 40px 70px -20px rgba(0,0,0,0.5);
            animation: fadeUp 0.25s ease;
        }
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .modal h2 {
            margin-bottom: 1.5rem;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal .form-group {
            margin-bottom: 1.2rem;
        }
        .modal label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1d3755;
            display: block;
            margin-bottom: 4px;
        }
        .modal input, .modal select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #d0dbea;
            border-radius: 30px;
            font-size: 0.95rem;
            background: #fafcff;
            transition: 0.2s;
        }
        .modal input:focus, .modal select:focus {
            outline: none;
            border-color: #2a7de1;
            box-shadow: 0 0 0 4px rgba(42,125,225,0.15);
        }
        .modal .actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        .modal .btn {
            padding: 0.6rem 1.8rem;
        }

        .footer-note {
            text-align: right;
            font-size: 0.75rem;
            color: #7b90ab;
            margin-top: 1rem;
            border-top: 1px solid #e6edf6;
            padding-top: 1.5rem;
        }

        @media (max-width: 700px) {
            .app-container { padding: 1.5rem 1rem; }
            .header { flex-direction: column; align-items: start; }
            .stats-grid { width: 100%; justify-content: space-between; }
            .action-bar { flex-direction: column; align-items: stretch; border-radius: 28px; }
            .action-bar .left, .action-bar .right { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="app-container" id="app">
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-bed"></i> HostelHub <span>v2.0 · integrated</span></h1>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><i class="fas fa-door-open"></i><div class="info"><span class="label">Rooms</span><span class="value" id="totalRooms">0</span></div></div>
            <div class="stat-card"><i class="fas fa-user-graduate"></i><div class="info"><span class="label">Occupied</span><span class="value" id="occupiedCount">0</span></div></div>
            <div class="stat-card"><i class="fas fa-user-plus"></i><div class="info"><span class="label">Available</span><span class="value" id="availableCount">0</span></div></div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="left">
            <button class="btn btn-primary" id="addRoomBtn"><i class="fas fa-plus-circle"></i> Add Room</button>
            <button class="btn btn-outline" id="addGuestBtn"><i class="fas fa-user-plus"></i> Check-in Guest</button>
        </div>
        <div class="right">
            <button class="btn btn-outline" id="refreshBtn"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="section-title"><i class="fas fa-list-ul"></i> Room Inventory</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Guest</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="roomTableBody">
                <!-- dynamic rows -->
            </tbody>
        </table>
    </div>

    <!-- Guests Section -->
    <div class="section-title"><i class="fas fa-users"></i> Current Guests</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="guestTableBody">
                <!-- dynamic -->
            </tbody>
        </table>
    </div>
    <div class="footer-note">
        <i class="fas fa-database"></i> fully integrated · data syncs automatically
    </div>
</div>

<!-- ========== MODAL (add / edit) ========== -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h2 id="modalTitle"><i class="fas fa-hotel"></i> Add Room</h2>
        <form id="modalForm">
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" id="roomNumber" placeholder="e.g. 204" required />
            </div>
            <div class="form-group">
                <label>Type</label>
                <select id="roomType">
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Dorm">Dorm (4)</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            <div class="form-group">
                <label>Capacity</label>
                <input type="number" id="roomCapacity" placeholder="1" min="1" value="1" />
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="roomStatus">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-outline" id="modalCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="modalSaveBtn"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        "use strict";

        // ---------- DATA ----------
        let rooms = [
            { id: 1, number: '101', type: 'Single', capacity: 1, status: 'available', guest: null },
            { id: 2, number: '102', type: 'Double', capacity: 2, status: 'occupied', guest: 'Alice Johnson' },
            { id: 3, number: '103', type: 'Dorm', capacity: 4, status: 'available', guest: null },
            { id: 4, number: '104', type: 'Suite', capacity: 3, status: 'maintenance', guest: null },
            { id: 5, number: '105', type: 'Double', capacity: 2, status: 'occupied', guest: 'Bob Smith' },
        ];

        let guests = [
            { id: 101, name: 'Alice Johnson', roomId: 2, checkIn: '2026-09-01', status: 'checked-in' },
            { id: 102, name: 'Bob Smith', roomId: 5, checkIn: '2026-09-03', status: 'checked-in' },
        ];

        let roomIdCounter = 6;
        let guestIdCounter = 103;

        // DOM refs
        const roomTableBody = document.getElementById('roomTableBody');
        const guestTableBody = document.getElementById('guestTableBody');
        const totalRoomsEl = document.getElementById('totalRooms');
        const occupiedCountEl = document.getElementById('occupiedCount');
        const availableCountEl = document.getElementById('availableCount');

        // Modal elements
        const modalOverlay = document.getElementById('modalOverlay');
        const modalTitle = document.getElementById('modalTitle');
        const modalForm = document.getElementById('modalForm');
        const roomNumber = document.getElementById('roomNumber');
        const roomType = document.getElementById('roomType');
        const roomCapacity = document.getElementById('roomCapacity');
        const roomStatus = document.getElementById('roomStatus');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const modalSaveBtn = document.getElementById('modalSaveBtn');

        let editingRoomId = null; // null = add mode

        // ---------- helpers ----------
        function getRoomById(id) { return rooms.find(r => r.id === id); }
        function getGuestById(id) { return guests.find(g => g.id === id); }
        function getGuestByRoom(roomId) { return guests.find(g => g.roomId === roomId); }

        // ---------- render ----------
        function renderAll() {
            renderRooms();
            renderGuests();
            updateStats();
        }

        function renderRooms() {
            if (!roomTableBody) return;
            let html = '';
            rooms.forEach(room => {
                const guest = getGuestByRoom(room.id);
                const guestName = guest ? guest.name : '—';
                let statusLabel = room.status;
                let badgeClass = 'badge';
                if (room.status === 'available') badgeClass += ' available';
                else if (room.status === 'occupied') badgeClass += ' occupied';
                else if (room.status === 'maintenance') badgeClass += ' maintenance';

                html += `
                    <tr>
                        <td><strong>${room.number}</strong></td>
                        <td>${room.type}</td>
                        <td>${room.capacity}</td>
                        <td><span class="${badgeClass}">${room.status}</span></td>
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

        function renderGuests() {
            if (!guestTableBody) return;
            let html = '';
            if (guests.length === 0) {
                html = `<tr><td colspan="5" style="text-align:center; color:#7a8aa3;">No guests checked in</td></tr>`;
            } else {
                guests.forEach(g => {
                    const room = getRoomById(g.roomId);
                    const roomNumber = room ? room.number : '❓';
                    const statusBadge = g.status === 'checked-in' ? 'badge checked-in' : 'badge checked-out';
                    html += `
                        <tr>
                            <td><i class="fas fa-user-circle" style="color:#2a7de1; margin-right:8px;"></i>${g.name}</td>
                            <td>Room ${roomNumber}</td>
                            <td>${g.checkIn}</td>
                            <td><span class="${statusBadge}">${g.status}</span></td>
                            <td style="text-align:right;" class="action-icons">
                                <i class="fas fa-user-edit" data-action="editGuest" data-id="${g.id}"></i>
                                <i class="fas fa-trash" data-action="deleteGuest" data-id="${g.id}"></i>
                            </td>
                        </tr>
                    `;
                });
            }
            guestTableBody.innerHTML = html;
        }

        function updateStats() {
            const total = rooms.length;
            const occupied = rooms.filter(r => r.status === 'occupied').length;
            const available = rooms.filter(r => r.status === 'available').length;
            if (totalRoomsEl) totalRoomsEl.textContent = total;
            if (occupiedCountEl) occupiedCountEl.textContent = occupied;
            if (availableCountEl) availableCountEl.textContent = available;
        }

        // ---------- MODAL ----------
        function openModal(mode, roomId = null) {
            editingRoomId = roomId;
            if (mode === 'add') {
                modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add New Room';
                roomNumber.value = '';
                roomType.value = 'Single';
                roomCapacity.value = '1';
                roomStatus.value = 'available';
                modalSaveBtn.textContent = 'Add Room';
            } else if (mode === 'edit' && roomId !== null) {
                const room = getRoomById(roomId);
                if (!room) return;
                modalTitle.innerHTML = `<i class="fas fa-edit"></i> Edit Room ${room.number}`;
                roomNumber.value = room.number;
                roomType.value = room.type;
                roomCapacity.value = room.capacity;
                roomStatus.value = room.status;
                modalSaveBtn.textContent = 'Update Room';
            }
            modalOverlay.classList.add('active');
        }

        function closeModal() {
            modalOverlay.classList.remove('active');
            editingRoomId = null;
            modalForm.reset();
        }

        // ---------- CRUD operations ----------
        function addRoom(number, type, capacity, status) {
            const newRoom = {
                id: roomIdCounter++,
                number: number.trim(),
                type: type,
                capacity: parseInt(capacity, 10) || 1,
                status: status,
                guest: null,
            };
            rooms.push(newRoom);
            renderAll();
        }

        function updateRoom(id, number, type, capacity, status) {
            const room = getRoomById(id);
            if (!room) return;
            // if status changes from occupied to available, remove guest if any
            if (status === 'available' && room.status === 'occupied') {
                const guest = getGuestByRoom(id);
                if (guest) {
                    guests = guests.filter(g => g.id !== guest.id);
                }
            }
            room.number = number.trim();
            room.type = type;
            room.capacity = parseInt(capacity, 10) || 1;
            room.status = status;
            // if status set to occupied but no guest, we keep as occupied (guest will be assigned via checkin)
            renderAll();
        }

        function deleteRoom(id) {
            const room = getRoomById(id);
            if (!room) return;
            // remove associated guest
            const guest = getGuestByRoom(id);
            if (guest) {
                guests = guests.filter(g => g.id !== guest.id);
            }
            rooms = rooms.filter(r => r.id !== id);
            renderAll();
        }

        function checkInGuest(roomId, guestName, checkInDate) {
            const room = getRoomById(roomId);
            if (!room) return false;
            if (room.status !== 'available') return false;
            // create guest
            const newGuest = {
                id: guestIdCounter++,
                name: guestName.trim(),
                roomId: roomId,
                checkIn: checkInDate || new Date().toISOString().split('T')[0],
                status: 'checked-in',
            };
            guests.push(newGuest);
            room.status = 'occupied';
            renderAll();
            return true;
        }

        function checkoutGuest(roomId) {
            const room = getRoomById(roomId);
            if (!room) return;
            const guest = getGuestByRoom(roomId);
            if (guest) {
                guest.status = 'checked-out';
                // remove guest from guests list (or keep history? we remove for simplicity)
                guests = guests.filter(g => g.id !== guest.id);
            }
            room.status = 'available';
            renderAll();
        }

        function deleteGuest(guestId) {
            const guest = getGuestById(guestId);
            if (!guest) return;
            const room = getRoomById(guest.roomId);
            if (room && room.status === 'occupied') {
                room.status = 'available';
            }
            guests = guests.filter(g => g.id !== guestId);
            renderAll();
        }

        // ---------- EVENT HANDLERS (delegation) ----------
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;
            const action = target.dataset.action;
            const id = parseInt(target.dataset.id, 10);

            if (action === 'editRoom') {
                openModal('edit', id);
            }
            else if (action === 'deleteRoom') {
                if (confirm('Delete this room and remove associated guest?')) {
                    deleteRoom(id);
                }
            }
            else if (action === 'checkin') {
                const guestName = prompt('Enter guest name:');
                if (guestName && guestName.trim() !== '') {
                    const success = checkInGuest(id, guestName.trim());
                    if (!success) alert('Room not available.');
                }
            }
            else if (action === 'checkout') {
                if (confirm('Checkout guest from this room?')) {
                    checkoutGuest(id);
                }
            }
            else if (action === 'deleteGuest') {
                if (confirm('Remove guest and free room?')) {
                    deleteGuest(id);
                }
            }
            else if (action === 'editGuest') {
                const guest = getGuestById(id);
                if (!guest) return;
                const newName = prompt('Edit guest name:', guest.name);
                if (newName && newName.trim() !== '') {
                    guest.name = newName.trim();
                    renderAll();
                }
            }
        });

        // add room button
        document.getElementById('addRoomBtn').addEventListener('click', function() {
            openModal('add');
        });

        // add guest (check-in) quick
        document.getElementById('addGuestBtn').addEventListener('click', function() {
            const availRooms = rooms.filter(r => r.status === 'available');
            if (availRooms.length === 0) {
                alert('No available rooms. Please add a room or free one.');
                return;
            }
            const roomOptions = availRooms.map(r => `${r.id}:${r.number}`).join(', ');
            const roomIdInput = prompt(`Enter room ID to check-in (available rooms: ${roomOptions})`);
            if (!roomIdInput) return;
            const roomId = parseInt(roomIdInput, 10);
            const room = getRoomById(roomId);
            if (!room || room.status !== 'available') {
                alert('Room not available or invalid ID.');
                return;
            }
            const guestName = prompt('Guest name:');
            if (!guestName || guestName.trim() === '') return;
            checkInGuest(roomId, guestName.trim());
        });

        // refresh
        document.getElementById('refreshBtn').addEventListener('click', function() {
            renderAll();
        });

        // modal events
        modalCancelBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) closeModal();
        });

        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const number = roomNumber.value.trim();
            const type = roomType.value;
            const capacity = roomCapacity.value;
            const status = roomStatus.value;
            if (!number) {
                alert('Room number is required.');
                return;
            }
            if (editingRoomId !== null) {
                // edit mode
                updateRoom(editingRoomId, number, type, capacity, status);
            } else {
                addRoom(number, type, capacity, status);
            }
            closeModal();
        });

        // initial render
        renderAll();

        // expose some for console debugging
        window.__hostel = { rooms, guests, renderAll };
    })();
</script>

</body>
</html>