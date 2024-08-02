@extends('layouts.bootstrap')

@section('content')
    <div class="container mt-5">
        <h1>Attendance Page</h1>

        <!-- Filter Form -->
        <form id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="name" class="form-label">Employee Name</label>
                    <select class="form-select" id="name" name="employee_id" disabled>
                        <!-- The logged-in user's name will be automatically selected and the dropdown will be disabled -->
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
        <div id="attendanceTable"></div>
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
            // Function to fetch employee names for dropdown
            function fetchEmployeeNames() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_employees',
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            var nameDropdown = $('#name');
                            var employees = response.Employees_Name;

                            // Sort names alphabetically
                            employees.sort(function(a, b) {
                                return a.name.localeCompare(b.name);
                            });

                            // Get the logged-in user's name and ID (you need to set this data in your view)
                            var loggedInUserName = "{{ Auth::user()->name }}"; // Example, replace with your actual method to get logged-in user's name
                            var loggedInUserId = "{{ Auth::user()->employee_id }}"; // Example, replace with your actual method to get logged-in user's employee ID

                            employees.forEach(function(employee) {
                                if (employee.employee_id == loggedInUserId) {
                                    nameDropdown.append(new Option(employee.name, employee.employee_id));
                                    nameDropdown.val(employee.employee_id); // Automatically select the logged-in user's name
                                    console.log('Logged-in user:', employee.name); // Debugging
                                }
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
                var employeeId = $('#name').val();
                var fromDate = $('#date_from').val();
                var toDate = $('#date_to').val();

                var url = 'http://localhost:8000/api/filter_employees';

                // Add logged-in user's ID as a filter criteria
                var formData = {
                    employee_id: employeeId,
                    from_date: fromDate,
                    to_date: toDate
                };

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        console.log('API Response:', response); // Debugging
                        if (response.status === 200) {
                            displayAttendance(response.data);
                        } else {
                            console.error('Error fetching attendance:', response.error);
                            $('#attendanceTable').html('<p>Error fetching attendance. Please try again later.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                        $('#attendanceTable').html('<p>Error fetching attendance. Please try again later.</p>');
                    }
                });
            }

            // Function to display attendance in a table
            function displayAttendance(data) {
                var tableHtml = '<table class="table table-striped"><thead><tr><th>Employee Name</th><th>Date</th><th>Leave Type</th></tr></thead><tbody>';

                if (data.length === 0) {
                    tableHtml += '<tr><td colspan="3">No records found</td></tr>';
                } else {
                    data.forEach(function(attendance) {
                        tableHtml += '<tr>';
                        tableHtml += '<td>' + attendance.employee_name + '</td>';
                        tableHtml += '<td>' + attendance.date + '</td>';
                        tableHtml += '<td>' + attendance.leave_type + '</td>';
                        tableHtml += '</tr>';
                    });
                }

                tableHtml += '</tbody></table>';
                $('#attendanceTable').html(tableHtml);
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

            // Initial fetch of employee names
            fetchEmployeeNames();
        });
    </script>
@endsection
