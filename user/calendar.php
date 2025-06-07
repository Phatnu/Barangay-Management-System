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
    #calendar {
        background-color: #fff;
        border-radius: 8px;
        min-height: 500px;
    }

    #eventsSidebar li {
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-left: 4px solid #28a745;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    #eventsSidebar li:hover {
        background: #e9f5ef;
    }

    #eventsSidebar strong {
        font-size: 0.95rem;
        color: #333;
    }

    #eventsSidebar span {
        color: #555;
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
        <div class="row">
            <!-- Calendar Section -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📅 Calendar</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="calendar" style="padding: 20px;"></div>
                    </div>
                </div>
            </div>

            <!-- Event Sidebar -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow rounded-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">📌 Scheduled Hearings</h6>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <ul id="eventsSidebar" class="list-unstyled mb-0"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Scheduled Hearings -->
<!-- Hearing Details Modal -->
<div class="modal fade" id="hearingModal" tabindex="-1" role="dialog" aria-labelledby="hearingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="hearingModalLabel">Hearing Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Complainant:</strong> <span id="modalComplainant"></span></p>
        <p><strong>Description:</strong> <span id="modalDescription"></span></p>
        <p><strong>Time:</strong> <span id="modalTime"></span></p>
        <p><strong>Date:</strong> <span id="modalDate"></span></p>
      </div>
    </div>
  </div>
</div>

</div>
<!-- FullCalendar Script -->


<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var sidebarEl = document.getElementById('eventsSidebar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'events.php',
        eventColor: '#378006',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        eventClick: function(info) {
            const event = info.event.extendedProps;

            document.getElementById("modalComplainant").innerText = event.complainant || "N/A";
            document.getElementById("modalDescription").innerText = event.description || "N/A";
            document.getElementById("modalTime").innerText = event.time || "N/A";
            document.getElementById("modalDate").innerText = info.event.start.toLocaleDateString();

            $('#hearingModal').modal('show');
        }
    });

    calendar.render();

    fetch('events.php')
        .then(response => response.json())
        .then(events => renderEventSidebar(events));

    function renderEventSidebar(events) {
        sidebarEl.innerHTML = "";
        events.sort((a, b) => new Date(a.start) - new Date(b.start));
        events.forEach(event => {
            const date = new Date(event.start);
            const formattedDate = date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            const item = document.createElement("li");
            item.innerHTML = `
                <strong>🗓 ${formattedDate}</strong><br>
                <span>⏰ ${event.title}</span>
            `;
            sidebarEl.appendChild(item);
        });
    }

    setInterval(() => {
        const now = new Date();
        document.getElementById("currentTime").innerText = now.toLocaleTimeString();
    }, 1000);
});
</script>



<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
<?php ob_end_flush(); ?>
