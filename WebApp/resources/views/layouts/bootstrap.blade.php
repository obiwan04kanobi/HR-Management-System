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
                const isAdmin = employeesData.some(employee => employee.report_to === loggedInUserId);

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
