@extends('layouts.bootstrap')

@section('content')
    <div class="container mt-5">
        <h1>Admin Attendance Page</h1>

        <!-- Alert for non-admin users -->
        <div id="admin-message" class="alert alert-info" style="display: none;">Sorry, you are not an admin user.</div>

        <!-- Filter Form -->
        <form id="filterForm" style="display: none;">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="name" class="form-label">Employee Name</label>
                    <select class="form-select" id="name" name="employee_id">
                        <option value="">Select Name</option>
                        <!-- Populate dynamically from API -->
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="from_date">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="to_date">
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="searchBtn">Search</button>
            <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
        </form>

        <hr>

        <!-- Table to display filtered data -->
        <div id="attendanceTable" style="display: none;"></div>
    </div>

    <!-- Modal for sending message -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Send Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="messageForm">
                        <div class="mb-3">
                            <label for="messageText" class="form-label">Message</label>
                            <textarea class="form-control" id="messageText" rows="3" required></textarea>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Attendance data updated successfully.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            let employeesData = [];
            let currentAttendanceId = null;
            let message_status = 1;

            // Function to fetch employee names for dropdown
            function fetchEmployeeNames() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_employees',
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            var nameDropdown = $('#name');
                            employeesData = response.Employees_Name;

                            // Filter employees who report to the logged-in user and exclude the logged-in user
                            var filteredEmployees = employeesData.filter(employee => employee
                                .report_to === loggedInUserId && employee.employee_id !==
                                loggedInUserId);

                            // Sort names alphabetically
                            filteredEmployees.sort(function(a, b) {
                                return a.name.localeCompare(b.name);
                            });

                            filteredEmployees.forEach(function(employee) {
                                nameDropdown.append(new Option(employee.name, employee
                                    .employee_id));
                            });
                        } else {
                            console.error('Error fetching employee names:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                    }
                });
            }

            // Function to fetch attendance based on filter criteria
            function fetchAttendance() {
                var formData = $('#filterForm').serialize();

                $.ajax({
                    url: 'http://localhost:8000/api/filter_employees',
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        if (response.status === 200) {
                            // Filter attendance data to include only employees reporting to the logged-in user and exclude the logged-in user
                            var filteredData = response.data.filter(attendance => attendance
                                .report_to === loggedInUserId && attendance.employee_id !==
                                loggedInUserId);
                            displayAttendance(filteredData);

                            // Set date range for date_from and date_to inputs
                            if (filteredData.length > 0) {
                                // Find the earliest and latest dates in the attendance data
                                var dates = filteredData.map(item => new Date(item.date));
                                var minDate = new Date(Math.min.apply(null, dates));
                                var maxDate = new Date(Math.max.apply(null, dates));

                                // Format dates for input fields (YYYY-MM-DD)
                                var minDateString = minDate.toISOString().slice(0, 10);
                                var maxDateString = maxDate.toISOString().slice(0, 10);

                                // Set the date_from and date_to inputs
                                $('#date_from').attr('min', minDateString);
                                $('#date_from').attr('max', maxDateString);
                                $('#date_to').attr('min', minDateString);
                                $('#date_to').attr('max', maxDateString);
                            }
                        } else {
                            console.error('Error fetching attendance:', response.error);
                            $('#attendanceTable').html(
                                '<p>Error fetching attendance. Please try again later.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                        $('#attendanceTable').html(
                            '<p>Error fetching attendance. Please try again later.</p>');
                    }
                });
            }

            // Function to display attendance in a table
            function displayAttendance(data) {
                var tableHtml =
                    '<table class="table table-striped"><thead><tr><th>Employee Name</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody>';

                if (data.length === 0) {
                    tableHtml += '<tr><td colspan="3">No records found</td></tr>';
                } else {
                    data.forEach(function(attendance) {
                        tableHtml += '<tr>';
                        tableHtml += '<td>' + attendance.employee_name + '</td>';
                        tableHtml += '<td>' + attendance.date + '</td>';
                        tableHtml += '<td>' + attendance.leave_type + '</td>';
                        tableHtml +=
                            '<td><button class="btn btn-primary btn-sm message-btn" data-attendance-id="' +
                            attendance.attendance_id + '">Message</button></td>';
                        tableHtml += '</tr>';
                    });
                }

                tableHtml += '</tbody></table>';
                $('#attendanceTable').html(tableHtml);

                $('.message-btn').on('click', function() {
                    currentAttendanceId = $(this).data('attendance-id'); // Corrected to 'attendance-id'
                    console.log("Logged in UserID:", loggedInUserId);
                    console.log('Message button clicked for attendance ID:', currentAttendanceId);
                    $('#messageModal').modal('show');
                });
            }

            // Handle Search button click event
            $('#searchBtn').click(function() {
                fetchAttendance();
            });

            // Handle Clear button click event
            $('#clearBtn').click(function() {
                $('#filterForm')[0].reset();
                $('#attendanceTable').empty();
            });

            $('#messageForm').on('submit', function(e) {
                e.preventDefault();
                var message = $('#messageText').val();

                console.log({
                    attendance_id: currentAttendanceId,
                    message_from: loggedInUserId,
                    message_status: message_status,
                    message: message,
                    _token: "{{ csrf_token() }}"
                });

                fetch(`http://localhost:8000/api/send_message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            attendance_id: currentAttendanceId,
                            message_from: loggedInUserId,
                            message_status: message_status,
                            message: message,
                            _token: "{{ csrf_token() }}"
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to send message.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        $('#messageModal').modal('hide');
                        $('#messageText').val('');
                        fetchAttendance();
                        showToast('Message sent successfully', 'success');
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    });
            });


            // Function to show a toast message
            function showToast(message, type) {
                var toast = new bootstrap.Toast($('#successToast'));
                $('#successToast .toast-body').text(message);
                toast.show();
            }


            // Check if the logged-in user is an admin
            $.ajax({
                url: 'http://localhost:8000/api/display_employees',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    employeesData = data.Employees_Name;
                    const hasReports = employeesData.some(employee => employee.report_to ===
                        loggedInUserId);

                    if (hasReports) {
                        $('#filterForm').show();
                        $('#attendanceTable').show();
                        // Initial fetch of employee names
                        fetchEmployeeNames();
                    } else {
                        $('#admin-message').show();
                    }
                },
                error: function(error) {
                    console.error('Error fetching employees:', error);
                }
            });
        });
    </script>
@endsection
