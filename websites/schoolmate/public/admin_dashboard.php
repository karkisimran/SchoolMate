<?php
// Include database connection
include('database.php');
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Rest of your admin dashboard code


// Get counts for dashboard stats
$user_count_query = "SELECT COUNT(*) as count FROM users";
$user_count_result = mysqli_query($conn, $user_count_query);
$user_count = mysqli_fetch_assoc($user_count_result)['count'];

$admin_count_query = "SELECT COUNT(*) as count FROM admin";
$admin_count_result = mysqli_query($conn, $admin_count_query);
$admin_count = mysqli_fetch_assoc($admin_count_result)['count'];

$school_count_query = "SELECT COUNT(*) as count FROM registered_schools";
$school_count_result = mysqli_query($conn, $school_count_query);
$school_count = mysqli_fetch_assoc($school_count_result)['count'];

// Get recent logins (assuming you have a login_logs table)
$recent_logins_query = "SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 5";
$recent_logins_result = mysqli_query($conn, $recent_logins_query);

// Get administrators
$admins_query = "SELECT * FROM admin";
$admins_result = mysqli_query($conn, $admins_query);

// Get registered schools
$schools_query = "SELECT * FROM registered_schools ORDER BY created_at DESC LIMIT 5";
$schools_result = mysqli_query($conn, $schools_query);

// Get user registrations
$registrations_query = "SELECT * FROM registrations ORDER BY created_at DESC LIMIT 5";
$registrations_result = mysqli_query($conn, $registrations_query);

// Get users
$users_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: #f5f7fa;
      color: #333;
    }
    
    .topbar {
      background: linear-gradient(135deg, #f3f5f7ff, #fcfdfdff);
      color: black;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .contact-info {
      display: flex;
      gap: 20px;
      font-size: 14px;
    }
    
    .contact-info i {
      margin-right: 5px;
    }
    
    .logo {
      display: flex;
      align-items: center;
      font-weight: bold;
      font-size: 20px;
    }
    
    .logo img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      object-fit: cover;
    }
    
    .container {
      display: flex;
      min-height: calc(100vh - 70px);
    }
    
    .sidebar {
      width: 250px;
      background: linear-gradient(135deg, #2c3e50, #2c3e50);
      color: white;
      padding: 20px 0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .sidebar ul {
      list-style: none;
    }
    
    .sidebar ul li {
      margin-bottom: 10px;
    }
    
    .sidebar ul li a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }
    
    .sidebar ul li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      border-left-color: #3498db;
    }
    
    .content {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
    }
    
    .content h1 {
      color: #2c3e50;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid #e0e6ed;
    }
    
    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: transform 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
      font-size: 24px;
      color: white;
    }
    
    .users-icon {
      background: linear-gradient(135deg, #3498db, #2980b9);
    }
    
    .admins-icon {
      background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    
    .schools-icon {
      background: linear-gradient(135deg, #2ecc71, #27ae60);
    }
    
    .logins-icon {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    
    .stat-card h3 {
      font-size: 14px;
      color: #7f8c8d;
      margin-bottom: 10px;
    }
    
    .stat-card .number {
      font-size: 32px;
      font-weight: bold;
      color: #2c3e50;
    }
    
    section {
      background: white;
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    
    section h2 {
      color: #2c3e50;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e0e6ed;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .view-all {
      font-size: 14px;
      color: #3498db;
      text-decoration: none;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    table th, table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #e0e6ed;
    }
    
    table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #2c3e50;
    }
    
    table tr:hover {
      background-color: #f8f9fa;
    }
    
    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }
    
    .pending {
      background-color: #ffeaa7;
      color: #d35400;
    }
    
    .approved {
      background-color: #d1f7c4;
      color: #27ae60;
    }
    
    .active {
      background-color: #d6eaf8;
      color: #3498db;
    }
    
    .actions a {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
      margin-right: 5px;
      cursor: pointer;
      text-decoration: none;
    }
    
    .edit {
      background-color: #3498db;
      color: white;
    }
    
    .delete {
      background-color: #e74c3c;
      color: white;
    }
    
    .promote {
      background-color: #9b59b6;
      color: white;
    }
    
    .ban {
      background-color: #f39c12;
      color: white;
    }
    
    .approve {
      background-color: #2ecc71;
      color: white;
    }
    
    .no-data {
      text-align: center;
      padding: 20px;
      color: #7f8c8d;
      font-style: italic;
    }
    
    @media (max-width: 1024px) {
      .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        padding: 10px 0;
      }
      
      .sidebar ul {
        display: flex;
        overflow-x: auto;
        padding: 0 10px;
      }
      
      .sidebar ul li {
        margin-right: 10px;
        margin-bottom: 0;
      }
      
      .sidebar ul li a {
        border-left: none;
        border-bottom: 4px solid transparent;
        padding: 10px 15px;
        white-space: nowrap;
      }
      
      .sidebar ul li a:hover {
        border-left-color: transparent;
        border-bottom-color: #3498db;
      }
      
      .dashboard-stats {
        grid-template-columns: 1fr;
      }
      
      table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div class="contact-info">
      <span><i class='bx bx-phone'></i> +977 9802585485</span>
      <span><i class='bx bx-envelope'></i> schoolmate1@gmail.com</span>
    </div>
    <div class="logo">
      <img src="images/logo.jpg" alt="SchoolMate Logo"> 
      <span>SchoolMate</span>
    </div>
  </div>

  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Admin Profile</h2>
      <ul>
        <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#"><i class="fas fa-school"></i> Schools</a></li>
        <li><a href="#"><i class="fas fa-clipboard-list"></i> Registrations</a></li>
        <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#"><i class="fas fa-user-shield"></i> Admins</a></li>
        <li><a href="#"><i class="fas fa-question-circle"></i> Inquiries</a></li>
        <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="content">
      <h1>Welcome, Admin</h1>
      
      <!-- Dashboard Stats -->
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon users-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3>TOTAL USERS</h3>
          <div class="number"><?php echo $user_count; ?></div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon admins-icon">
            <i class="fas fa-user-shield"></i>
          </div>
          <h3>ADMINISTRATORS</h3>
          <div class="number"><?php echo $admin_count; ?></div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon schools-icon">
            <i class="fas fa-school"></i>
          </div>
          <h3>REGISTERED SCHOOLS</h3>
          <div class="number"><?php echo $school_count; ?></div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon logins-icon">
            <i class="fas fa-sign-in-alt"></i>
          </div>
          <h3>TODAY'S LOGINS</h3>
          <div class="number">
            <?php 
            // Query for today's logins
            $today = date('Y-m-d');
            $today_logins_query = "SELECT COUNT(*) as count FROM login_logs WHERE DATE(login_time) = '$today'";
            $today_logins_result = mysqli_query($conn, $today_logins_query);
            $today_logins = mysqli_fetch_assoc($today_logins_result)['count'];
            echo $today_logins;
            ?>
          </div>
        </div>
      </div>

      <!-- Recent Logins -->
      <section class="recent-logins">
        <h2>
          Recent User Logins
          <a href="login_logs.php" class="view-all">View All</a>
        </h2>
        <table>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Login Time</th>
              <th>IP Address</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($recent_logins_result) > 0): ?>
              <?php while($login = mysqli_fetch_assoc($recent_logins_result)): ?>
                <tr>
                  <td><?php echo $login['user_id']; ?></td>
                  <td><?php echo $login['username']; ?></td>
                  <td><?php echo $login['login_time']; ?></td>
                  <td><?php echo $login['ip_address']; ?></td>
                  <td><span class="status active">Active</span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="no-data">No login records found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Administrators -->
      <section class="administrators">
        <h2>
          Administrators
          <a href="admins.php" class="view-all">View All</a>
        </h2>
        <table>
          <thead>
            <tr>
              <th>Admin ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Last Login</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($admins_result) > 0): ?>
              <?php while($admin = mysqli_fetch_assoc($admins_result)): ?>
                <tr>
                  <td><?php echo $admin['id']; ?></td>
                  <td><?php echo $admin['name']; ?></td>
                  <td><?php echo $admin['email']; ?></td>
                  <td><?php echo $admin['role']; ?></td>
                  <td><?php echo $admin['last_login']; ?></td>
                  <td><span class="status active">Active</span></td>
                  <td class="actions">
                    <a class="edit" href="edit_admin.php?id=<?php echo $admin['id']; ?>">Edit</a>
                    <a class="delete" href="delete_admin.php?id=<?php echo $admin['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="no-data">No administrators found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Registered Schools -->
      <section class="schools">
        <h2>
          Registered Schools
          <a href="schools.php" class="view-all">View All</a>
        </h2>
        <table>
          <thead>
            <tr>
              <th>School ID</th>
              <th>School Name</th>
              <th>Email</th>
              <th>Address</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Registered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($schools_result) > 0): ?>
              <?php while($school = mysqli_fetch_assoc($schools_result)): ?>
                <tr>
                  <td><?php echo $school['id']; ?></td>
                  <td><?php echo $school['school_name']; ?></td>
                  <td><?php echo $school['email']; ?></td>
                  <td><?php echo substr($school['address'], 0, 20) . '...'; ?></td>
                  <td><?php echo $school['contact_number']; ?></td>
                  <td><span class="status <?php echo $school['status'] === 'approved' ? 'approved' : 'pending'; ?>"><?php echo ucfirst($school['status']); ?></span></td>
                  <td><?php echo $school['created_at']; ?></td>
                  <td class="actions">
                    <a class="edit" href="edit_school.php?id=<?php echo $school['id']; ?>">Edit</a>
                    <a class="delete" href="delete_school.php?id=<?php echo $school['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="no-data">No schools registered yet</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Registrations -->
      <section class="registrations">
        <h2>
          Recent Registrations
          <a href="registrations.php" class="view-all">View All</a>
        </h2>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>School</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($registrations_result) > 0): ?>
              <?php while($registration = mysqli_fetch_assoc($registrations_result)): ?>
                <tr>
                  <td><?php echo $registration['name']; ?></td>
                  <td><?php echo $registration['email']; ?></td>
                  <td><?php echo $registration['school_name']; ?></td>
                  <td><span class="status <?php echo $registration['status'] === 'approved' ? 'approved' : 'pending'; ?>"><?php echo ucfirst($registration['status']); ?></span></td>
                  <td class="actions">
                    <a class="approve" href="approve_registration.php?id=<?php echo $registration['id']; ?>">Approve</a>
                    <a class="delete" href="reject_registration.php?id=<?php echo $registration['id']; ?>">Reject</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="no-data">No registration requests</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Users -->
      <section class="users">
        <h2>
          Users
          <a href="users.php" class="view-all">View All</a>
        </h2>
        <table>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Registered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($users_result) > 0): ?>
              <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                <tr>
                  <td><?php echo $user['id']; ?></td>
                  <td><?php echo $user['name']; ?></td>
                  <td><?php echo $user['email']; ?></td>
                  <td><?php echo ucfirst($user['role']); ?></td>
                  <td><span class="status <?php echo $user['status'] === 'active' ? 'active' : 'pending'; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                  <td><?php echo $user['created_at']; ?></td>
                  <td class="actions">
                    <a class="edit" href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                    <a class="delete" href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="no-data">No users found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    // Simple confirmation for delete actions
    document.querySelectorAll('.delete').forEach(link => {
      link.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
          e.preventDefault();
        }
      });
    });
  </script>
</body>
</html>