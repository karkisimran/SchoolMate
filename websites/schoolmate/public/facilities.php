<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facilities & Events - SchoolMate</title>
    <link rel="stylesheet" href="facilities.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="school-name">XYZ School</div>
        <nav class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile Management</a>
            <a href="academic.php">Academic Details</a>
            <a href="facilities.php" class="active">Facilities & Services</a>
            <a href="events.php">Events & Announcements</a>
            <a href="messages.php">Messages & Inquiries</a>
            <a href="settings.php">Account Settings</a>
        </nav>
    </div>

    <!-- Main -->
    <div class="main">
        <!-- Header -->
        <div class="header">
            <div class="contact-info">
                +977 9802585485 &nbsp;&nbsp; | &nbsp;&nbsp; schoolmate1@gmail.com
            </div>
            <div class="logo">SchoolMate</div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Welcome, XYZ School</h2>

            <!-- Facilities Section -->
            <section class="facilities">
                <h3>Facilities & Services</h3>
                <div class="facility-box">
                    <p><b>Available facilities :</b></p>
                    <ul>
                        <li><input type="checkbox" checked> Playground</li>
                        <li><input type="checkbox" checked> Library</li>
                        <li><input type="checkbox" checked> Transportation</li>
                        <li><input type="checkbox" checked> Sports</li>
                    </ul>
                    <span class="add">+</span>
                </div>
            </section>

            <!-- Events Section -->
            <section class="events">
                <h3>Events & Announcements</h3>
                <div class="event-box">
                    <div class="event-header">
                        <span>Upcoming Events</span>
                        <input type="text" placeholder="Search For Events">
                        <button>Search</button>
                    </div>
                    <div class="event-grid">
                        <div class="event-item"><img src="image/events.png" alt="Upcoming Event"></div>
                        <div class="event-item"><img src="image/events.png" alt="Upcoming Event"></div>
                        <div class="event-item"><img src="image/events.png" alt="Upcoming Event"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</body>
</html>