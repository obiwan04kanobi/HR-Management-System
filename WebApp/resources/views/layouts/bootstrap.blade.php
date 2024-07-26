<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'My Lava') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap JavaScript Bundle (including Popper) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js" integrity="sha256-sw0iNNXmOJbQhYFuC9OF2kOlD5KQKe1y5lfBn4C9Sjg=" crossorigin="anonymous"></script>
    </script>
</head>

<body>
    <header>
        <nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                        <li class="nav-item">
                            <a class="nav-link" href="/emp_attendance">Attendance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/leave">Leave Application</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/reset-password">Reset Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/compoff">Compoff Application</a>
                        </li>
                    </ul>
                    <form action="/logout" method="post" class="d-flex" role="search">
                        @csrf
                        <button class="btn btn-outline-success" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <div id="bootstrap">
        <main>
            @yield('content')
        </main>
    </div>
    <!-- Alert Container -->
    <div id="messageAlert" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <!-- Alert will be dynamically updated here -->
    </div>
</body>

</html>
<script>
    $(document).ready(function() {
        const loggedInUserId = {{ Auth::user()->employee_id }};
        let employeesData = [];

        // Check if the logged-in user is an admin
        $.ajax({
            url: 'http://localhost:8000/api/display_employees',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                employeesData = data.Employees_Name;
                const isAdmin = employeesData.some(employee => employee.report_to ===
                    loggedInUserId);

                // Modify navbar links based on admin status
                if (isAdmin) {
                    // Show all links
                    $('#navLinks').append(`
                        <li class="nav-item">
                            <a class="nav-link" href="/adm_attendance">Admin Attendance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/leave_approve">Approve Leave</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/compoff_approve">Approve Compoff</a>
                        </li>
                    `);
                } else {
                    // Remove Admin Attendance & Approve Leave links
                    $('#navLinks li.nav-item:contains("Admin Attendance")').remove();
                    $('#navLinks li.nav-item:contains("Approve Leave")').remove();
                }
            },
            error: function(error) {
                console.error('Error fetching employees:', error);
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        const loggedInUserId = {{ Auth::user()->employee_id }};
        let lastCheckedMessages = [];
        let employeesData = [];


        // Check if the logged-in user is an admin
        $.ajax({
            url: 'http://localhost:8000/api/display_employees',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                employeesData = data.Employees_Name;
                const isAdmin = employeesData.some(employee => employee.report_to ===
                    loggedInUserId);

                // Modify navbar links based on admin status
                if (isAdmin) {
                    // Function to fetch and display messages
                    function fetchMessages() {
                        $.ajax({
                            url: 'http://localhost:8000/api/display_admin_messages',
                            method: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 200) {
                                    const hasNewMessages = response.messages.some(
                                        message =>
                                        message.message_status === 1 && message
                                        .message_to ===
                                        loggedInUserId
                                    );

                                    if (hasNewMessages) {
                                        showNewMessageAlert();
                                    }
                                } else {
                                    console.error('Failed to fetch messages');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching messages:', error);
                            }
                        });
                    }

                    // Function to show the new message alert
                    function showNewMessageAlert() {
                        const alertContainer = $('#messageAlert');
                        const alertMessage = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    You have a new message.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
                        alertContainer.html(alertMessage);
                    }

                    // Poll for new messages every 1 seconds
                    setInterval(function() {
                        fetchMessages();
                    }, 1000); // 1 seconds

                } else {
                    // Function to fetch and display messages
                    function fetchMessages() {
                        $.ajax({
                            url: 'http://localhost:8000/api/display_emp_messages',
                            method: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 200) {
                                    const hasNewMessages = response.messages.some(
                                        message =>
                                        message.message_status === 1 && message
                                        .message_to ===
                                        loggedInUserId
                                    );

                                    if (hasNewMessages) {
                                        showNewMessageAlert();
                                    }
                                } else {
                                    console.error('Failed to fetch messages');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching messages:', error);
                            }
                        });
                    }

                    // Function to show the new message alert
                    function showNewMessageAlert() {
                        const alertContainer = $('#messageAlert');
                        const alertMessage = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    You have a new message.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
                        alertContainer.html(alertMessage);
                    }

                    // Poll for new messages every 1 seconds
                    setInterval(function() {
                        fetchMessages();
                    }, 1000); // 1 seconds
                }
            },
            error: function(error) {
                console.error('Error fetching employees:', error);
            }
        });

    });
</script>
