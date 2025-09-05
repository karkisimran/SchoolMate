<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h2 {
            margin-top: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar li {
            margin: 10px 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .header {
            background-color: #ecf0f1;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .section {
            margin: 20px 0;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-form, .inquiry-form {
            display: flex;
            flex-direction: column;
        }
        .profile-form input, .profile-form textarea, .inquiry-form input, .inquiry-form textarea, .inquiry-form select {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .profile-form button, .inquiry-form button {
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .profile-form button:hover, .inquiry-form button:hover {
            background-color: #2980b9;
        }
        #profile-display {
            display: none;
        }
        #inquiries-list {
            margin-top: 20px;
        }
        .inquiry-item {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Dashboard</h2>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#dashboard" onclick="showSection('dashboard')">Dashboard</a></li>
            <li><a href="#profile" onclick="showSection('profile')">Profile</a></li>
            <li><a href="#inquiries" onclick="showSection('inquiries')">Inquiries</a></li>
            <li><a href="#settings" onclick="showSection('settings')">Account Settings</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>Welcome, User</h1>
        </div>
        <div id="dashboard" class="section">
            <h2>Dashboard Overview</h2>
            <p>Quick stats: You have sent 0 inquiries.</p>
            <div id="profile-summary"></div>
        </div>
        <div id="profile" class="section" style="display: none;">
            <h2>Profile Management</h2>
            <form id="profile-form" class="profile-form">
                <input type="text" id="name" placeholder="Full Name" required>
                <input type="email" id="email" placeholder="Email" required>
                <input type="tel" id="phone" placeholder="Phone Number">
                <textarea id="bio" placeholder="Bio/About Yourself"></textarea>
                <button type="submit">Save Profile</button>
            </form>
            <div id="profile-display"></div>
        </div>
        <div id="inquiries" class="section" style="display: none;">
            <h2>School Inquiries</h2>
            <form id="inquiry-form" class="inquiry-form">
                <select id="school" required>
                    <option value="">Select School</option>
                    <option value="XYZ School">XYZ School</option>
                    <option value="ABC Academy">ABC Academy</option>
                    <option value="DEF High">DEF High</option>
                </select>
                <input type="text" id="subject" placeholder="Subject" required>
                <textarea id="message" placeholder="Your Inquiry Message" required></textarea>
                <button type="submit">Send Inquiry</button>
            </form>
            <div id="inquiries-list"></div>
        </div>
        <div id="settings" class="section" style="display: none;">
            <h2>Account Settings</h2>
            <p>Manage your account preferences here.</p>
            <!-- Placeholder for settings options -->
        </div>
    </div>
    <script>
        let profileData = {};
        let inquiries = [];

        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';
        }

        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            profileData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                bio: document.getElementById('bio').value
            };
            // Send to backend
            try {
                const response = await fetch('/api/profile', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(profileData)
                });
                if (response.ok) {
                    displayProfile();
                    updateDashboardSummary();
                }
            } catch (error) {
                console.error('Error saving profile:', error);
            }
        });

        function displayProfile() {
            const display = document.getElementById('profile-display');
            display.innerHTML = `
                <h3>Your Profile</h3>
                <p>Name: ${profileData.name}</p>
                <p>Email: ${profileData.email}</p>
                <p>Phone: ${profileData.phone}</p>
                <p>Bio: ${profileData.bio}</p>
            `;
            display.style.display = 'block';
        }

        function updateDashboardSummary() {
            document.getElementById('profile-summary').innerHTML = `
                <h3>Profile Summary</h3>
                <p>Name: ${profileData.name || 'Not set'}</p>
                <p>Email: ${profileData.email || 'Not set'}</p>
            `;
        }

        document.getElementById('inquiry-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const inquiry = {
                school: document.getElementById('school').value,
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value,
                date: new Date().toLocaleString()
            };
            // Send to backend
            try {
                const response = await fetch('/api/inquiries', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(inquiry)
                });
                if (response.ok) {
                    inquiries.push(inquiry);
                    displayInquiries();
                    document.getElementById('inquiry-form').reset();
                }
            } catch (error) {
                console.error('Error sending inquiry:', error);
            }
        });

        function displayInquiries() {
            const list = document.getElementById('inquiries-list');
            list.innerHTML = '<h3>Sent Inquiries</h3>';
            inquiries.forEach(inq => {
                list.innerHTML += `
                    <div class="inquiry-item">
                        <p><strong>School:</strong> ${inq.school}</p>
                        <p><strong>Subject:</strong> ${inq.subject}</p>
                        <p><strong>Message:</strong> ${inq.message}</p>
                        <p><strong>Date:</strong> ${inq.date}</p>
                    </div>
                `;
            });
        }

        // Load profile on start
        async function loadProfile() {
            try {
                const response = await fetch('/api/profile');
                if (response.ok) {
                    profileData = await response.json();
                    if (Object.keys(profileData).length > 0) {
                        displayProfile();
                        updateDashboardSummary();
                    }
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        // Load inquiries on start
        async function loadInquiries() {
            try {
                const response = await fetch('/api/inquiries');
                if (response.ok) {
                    inquiries = await response.json();
                    displayInquiries();
                }
            } catch (error) {
                console.error('Error loading inquiries:', error);
            }
        }

        loadProfile();
        loadInquiries();
        showSection('dashboard');
    </script>
</body>
</html>