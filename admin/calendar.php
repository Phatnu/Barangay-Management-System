<?php
ob_start();
session_start();
include('includes/header.php');
include('includes/navbar.php');
include_once("../connections/connection.php");

$con = connection();

if (!isset($_SESSION['STAFFID'])) {
    header('location:../index.php');
    exit();
}

$user_id = $_SESSION['STAFFID'];
$sql = "SELECT * FROM STAFF WHERE STAFFID = '$user_id'";
$query = $con->query($sql);
$rows = $query->fetch_assoc();
?>

<!-- FullCalendar Styles & Scripts -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<style>
   :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%,rgb(37, 9, 149) 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%,rgb(5, 155, 63) 100%);
            --card-shadow: 0 10px 40px rgba(0,0,0,0.1);
            --hover-shadow: 0 20px 60px rgba(0,0,0,0.15);
            --border-radius: 5px;
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .calendar-container {
            padding: 2rem;
            padding-top:0rem;
            max-width: 100%;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .gradient-header {
            background: var(--primary-gradient);
            padding: 1.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .gradient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .gradient-header:hover::before {
            left: 100%;
        }

        .sidebar-header {
            background: var(--success-gradient);
            padding: 1.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .sidebar-header:hover::before {
            left: 100%;
        }

        .header-title {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .calendar-body {
            padding: 0;
            background: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        #calendar {
            padding: 2rem;
            background: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        /* FullCalendar Custom Styling */
        .fc-toolbar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 1rem !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .fc-button-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease !important;
        }

        .fc-button-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4) !important;
        }

        .fc-event {
            background: var(--success-gradient) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 2px 6px !important;
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        .fc-event:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.5) !important;
        }

        .fc-daygrid-day {
            transition: background-color 0.3s ease;
        }

        .fc-daygrid-day:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
        }

        .fc-day-today {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
        }

        /* Sidebar Styling */
        .sidebar-body {
            padding: 0;
            background: white;
            max-height: 600px;
            overflow-y: auto;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .sidebar-scroll {
            padding: 1.5rem;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: var(--success-gradient);
            border-radius: 10px;
        }

        .event-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid rgba(17, 153, 142, 0.1);
            border-radius: 15px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .event-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--success-gradient);
            transition: width 0.3s ease;
        }

        .event-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.15);
            border-color: rgba(17, 153, 142, 0.3);
        }

        .event-item:hover::before {
            width: 8px;
        }

        .event-date {
            color: #667eea;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-title {
            color: #2d3748;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }


        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .fc .fc-button-group {
    gap: 1rem!important;
}

        @media (max-width: 768px) {
            .calendar-container {
                padding: 1rem;
            }
            
  
            
            .fc-toolbar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }
        }

        /* Animation for page load */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-card {
            animation: slideInUp 0.6s ease-out;
        }

        .glass-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .glass-card:nth-child(3) {
            animation-delay: 0.4s;
        }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow mx-1 mt-4" style="display: flex;gap:5px">
                <i class="fas fa-clock text-secondary me-2"></i>
                <p id="currentTime" style="color: gray;font-size:12px"></p>
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $rows['FIRST_NAME'] ?></label>
                    </span>
                     <img class="img-profile rounded-circle" src="../admin/images/<?=$rows['PROFILE']?>">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="profile.php">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                    </a>
                    <a class="dropdown-item" href="../admin/change_password.php">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Account Settings
                    </a>
                   <a class="dropdown-item" href="Systemlogs.php">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- End of Topbar -->

    <!-- Main Content -->
    <div class="container-fluid py-4">
<!-- Floating Time Display -->

    <div class="calendar-container">
        <!-- Stats Row -->
        <div class="row stats-row">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number" id="totalEvents">0</div>
                    <div class="stat-label">Total Hearings</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number" id="todayEvents">0</div>
                    <div class="stat-label">Today's Hearings</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number" id="upcomingEvents">0</div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number" id="monthEvents">0</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Calendar Section -->
            <div class="col-lg-8 mb-4">
                <div class="glass-card">
                    <div class="gradient-header">
                        <h5 class="header-title">
                            <i class="fas fa-calendar-alt"></i>
                            Calendar Overview
                        </h5>
                    </div>
                    <div class="calendar-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Event Sidebar -->
            <div class="col-lg-4 mb-4">
                <div class="glass-card">
                    <div class="sidebar-header">
                        <h6 class="header-title">
                            <i class="fas fa-gavel"></i>
                            Scheduled Hearings
                        </h6>
                    </div>
                    <div class="sidebar-body">
                        <div class="sidebar-scroll">
                            <div id="eventsSidebar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hearing Details Modal -->
    <div class="modal fade" id="hearingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: none; box-shadow: var(--card-shadow);">
                <div class="modal-header" style="background: var(--primary-gradient); color: white; border: none;">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i>
                        Hearing Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Complainant</label>
                            <div class="p-3 bg-light rounded-3" id="modalComplainant">N/A</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Date</label>
                            <div class="p-3 bg-light rounded-3" id="modalDate">N/A</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Time</label>
                            <div class="p-3 bg-light rounded-3" id="modalTime">N/A</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Incident Type</label>
                            <div class="p-3 bg-light rounded-3" id="modalIncident">N/A</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted">Description</label>
                            <div class="p-3 bg-light rounded-3" id="modalDescription" style="min-height: 80px;">N/A</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- Modal for Scheduled Hearings -->



</div>
<!-- FullCalendar Script -->


 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var sidebarEl = document.getElementById('eventsSidebar');
            let eventsData = [];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: 'events.php', // Replace with your actual events.php path
                eventColor: '#11998e',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventClick: function(info) {
                    const event = info.event.extendedProps;
                    
                    document.getElementById("modalComplainant").innerText = event.complainant || "N/A";
                    document.getElementById("modalDescription").innerText = event.description || "N/A";
                    document.getElementById("modalIncident").innerText = event.incident || "N/A";
                    document.getElementById("modalTime").innerText = event.time || "N/A";
                    document.getElementById("modalDate").innerText = info.event.start.toLocaleDateString();
                    
                    new bootstrap.Modal(document.getElementById('hearingModal')).show();
                },
                eventDidMount: function(info) {
                    // Add hover effect to events
                    info.el.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                        this.style.zIndex = '10';
                    });
                    
                    info.el.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                        this.style.zIndex = '1';
                    });
                }
            });

            calendar.render();

            // Fetch events and render sidebar
            fetch('events.php') // Replace with your actual events.php path
                .then(response => response.json())
                .then(events => {
                    eventsData = events;
                    renderEventSidebar(events);
                    updateStats(events);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    // For demo purposes, let's create some sample data
                    const sampleEvents = [
                        {
                            title: "Hearing: Noise Complaint (2:00 PM)",
                            start: "2025-06-10",
                            complainant: "John Doe",
                            description: "Loud music during night hours",
                            time: "2:00 PM"
                        },
                        {
                            title: "Hearing: Property Dispute (10:00 AM)",
                            start: "2025-06-12",
                            complainant: "Jane Smith",
                            description: "Boundary line disagreement",
                            time: "10:00 AM"
                        },
                        {
                            title: "Hearing: Minor Assault (3:30 PM)",
                            start: "2025-06-15",
                            complainant: "Bob Johnson",
                            description: "Physical altercation between neighbors",
                            time: "3:30 PM"
                        }
                    ];
                    eventsData = sampleEvents;
                    renderEventSidebar(sampleEvents);
                    updateStats(sampleEvents);
                });

            function renderEventSidebar(events) {
                sidebarEl.innerHTML = "";
                
                if (events.length === 0) {
                    sidebarEl.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-2x mb-3"></i>
                            <p>No scheduled hearings</p>
                        </div>
                    `;
                    return;
                }

                events.sort((a, b) => new Date(a.start) - new Date(b.start));
                
                events.forEach((event, index) => {
                    const date = new Date(event.start);
                    const formattedDate = date.toLocaleDateString(undefined, {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    const item = document.createElement("div");
                    item.className = "event-item";
                    item.style.animationDelay = `${index * 0.1}s`;
                    
                    // Check if event is today
                    const today = new Date();
                    const isToday = date.toDateString() === today.toDateString();
                    
                    // Check if event is overdue
                    const isOverdue = date < today && !isToday;
                    
                    item.innerHTML = `
                        <div class="event-date ${isToday ? 'text-success' : isOverdue ? 'text-danger' : ''}">
                            <i class="fas fa-calendar-day"></i>
                            ${formattedDate}
                            ${isToday ? '<span class="badge bg-success ms-2">Today</span>' : ''}
                            ${isOverdue ? '<span class="badge bg-danger ms-2">Overdue</span>' : ''}
                        </div>
                        <div class="event-title">
                            <i class="fas fa-clock"></i>
                            ${event.title}
                        </div>
                    `;
                    
                    // Add click handler to show modal
                    item.addEventListener('click', function() {
                        document.getElementById("modalComplainant").innerText = event.complainant || "N/A";
                        document.getElementById("modalDescription").innerText = event.description || "N/A";
                        document.getElementById("modalIncident").innerText = event.incident || "N/A";
                        document.getElementById("modalTime").innerText = event.time || "N/A";
                        document.getElementById("modalDate").innerText = formattedDate;
                        
                        new bootstrap.Modal(document.getElementById('hearingModal')).show();
                    });
                    
                    item.style.cursor = 'pointer';
                    sidebarEl.appendChild(item);
                });
            }

            function updateStats(events) {
                const today = new Date();
                const startOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay());
                const endOfWeek = new Date(startOfWeek.getTime() + 6 * 24 * 60 * 60 * 1000);
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                const totalEvents = events.length;
                const todayEvents = events.filter(event => {
                    const eventDate = new Date(event.start);
                    return eventDate.toDateString() === today.toDateString();
                }).length;

                const upcomingEvents = events.filter(event => {
                    const eventDate = new Date(event.start);
                    return eventDate >= startOfWeek && eventDate <= endOfWeek;
                }).length;

                const monthEvents = events.filter(event => {
                    const eventDate = new Date(event.start);
                    return eventDate >= startOfMonth && eventDate <= endOfMonth;
                }).length;

                // Animate counters
                animateCounter('totalEvents', totalEvents);
                animateCounter('todayEvents', todayEvents);
                animateCounter('upcomingEvents', upcomingEvents);
                animateCounter('monthEvents', monthEvents);
            }

            function animateCounter(elementId, targetValue) {
                const element = document.getElementById(elementId);
                let currentValue = 0;
                const increment = targetValue / 20;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= targetValue) {
                        currentValue = targetValue;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(currentValue);
                }, 50);
            }
        });
    </script>

<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
<?php ob_end_flush(); ?>
