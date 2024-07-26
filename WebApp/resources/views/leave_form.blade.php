@extends('layouts.bootstrap')
@section('content')
    <div class="container mt-5">
        <h1>Leave Application</h1>

        <!-- Leave Application Form -->
        <form id="leaveApplicationForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="name" class="form-label">Employee Name</label>
                    <select class="form-select" id="name" name="employee_id" required>
                        <option value="">Select Name</option>
                        <!-- Populate dynamically from API -->
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="from_date" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="to_date" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="leave_type" class="form-label">Leave Type</label>
                    <select class="form-select" id="leave_type" name="leave_type" required>
                        <option value="">Select Leave Type</option>
                        <!-- Populate dynamically from API -->
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="session" class="form-label">Session</label>
                    <select class="form-select" id="session" name="session" required>
                        <option value="">Select Session</option>
                        <option value="Half Day">Half Day</option>
                        <option value="Full Day">Full Day</option>
                        <option value="Short Leave">Short Leave</option>
                    </select>
                    <small id="sessionMessage" class="text-danger"></small>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="half" class="form-label">Half</label>
                    <select class="form-select" id="half" name="half" required>
                        <option value="">Select Half</option>
                        <option value="1st Half">1st Half</option>
                        <option value="2nd Half">2nd Half</option>
                    </select>
                    <small id="halfMessage" class="text-danger"></small>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3" maxlength="200" required></textarea>
                </div>
            </div>
            <input type="hidden" name="status" value="1"> <!-- Hidden status field -->
            <button type="button" class="btn btn-primary" id="submitBtn">Submit</button>
            <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
        </form>

        <!-- Bootstrap Toast -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Leave application submitted successfully.
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            // const loggedInUserName = "{{ Auth::user()->employee_name }}"; // Fetch logged-in user's name
            // Function to fetch employee names for dropdown
            let half = null;
            let holidays = [];

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
                                .report_to === loggedInUserId || employee.employee_id ===
                                loggedInUserId);

                            // Sort names alphabetically
                            filteredEmployees.sort(function(a, b) {
                                return a.name.localeCompare(b.name);
                            });

                            console.log("logged in userID: " + loggedInUserId);

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

            // Function to fetch leave types for dropdown
            function fetchLeaveTypes() {
                $.ajax({
                    url: 'http://localhost:8000/api/leaves',
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            var leaveTypeDropdown = $('#leave_type');
                            var leaveTypes = response.Leaves;
                            leaveTypes.forEach(function(leave) {
                                leaveTypeDropdown.append(new Option(leave.leave_type, leave
                                    .leave_id));
                            });
                        } else {
                            console.error('Error fetching leave types:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                    }
                });
            }

            // Update session dropdown based on employee attendance
            function updateSessionDropdown() {
                var employeeId = $('#name').val();
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();

                if (!employeeId || !fromDate || !toDate) {
                    $('#session').html(
                        '<option value="">Select Session</option><option value="Half Day">Half Day</option><option value="Full Day">Full Day</option><option value="Short Leave">Short Leave</option>'
                    );
                    $('#sessionMessage').text('');
                    $('#half').html(
                        '<option value="">Select Half</option><option value="1st Half">1st Half</option><option value="2nd Half">2nd Half</option>'
                    );
                    $('#halfMessage').text('');
                    $('#submitBtn').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: 'http://localhost:8000/api/filter_employees',
                    type: 'GET',
                    data: {
                        employee_id: employeeId,
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            var attendance = response.data;
                            var sessionDropdown = $('#session');
                            var halfDropdown = $('#half');
                            sessionDropdown.html('<option value="">Select Session</option>');
                            halfDropdown.html('<option value="">Select Half</option>');

                            var sessionMessage = $('#sessionMessage');
                            var halfMessage = $('#halfMessage');
                            sessionMessage.text('');
                            halfMessage.text('');
                            var fromDate = $('#from_date').val();
                            var toDate = $('#to_date').val();
                            var hasFirstHalf = false;
                            var hasSecondHalf = false;
                            var hasFullDay = false;
                            var pres_shortLeave = false;
                            var shortLeave_pres = false;
                            var present = false;

                            attendance.forEach(function(record) {
                                if (record.leave_type === '1st Half Absent' && record.date ===
                                    fromDate && record.date === toDate) {
                                    hasFirstHalf = true;
                                } else if (record.leave_type === '2nd Half Absent' && record
                                    .date === toDate && record.date === fromDate) {
                                    hasSecondHalf = true;
                                } else if (record.leave_type === 'Absent' && record.date ===
                                    toDate && record.date === fromDate) {
                                    hasFullDay = true;
                                } else if (record.leave_type === 'Present--Short Leave' &&
                                    record
                                    .date === toDate && record.date === fromDate) {
                                    pres_shortLeave = true;
                                } else if (record.leave_type === 'Short Leave--Present' &&
                                    record
                                    .date === toDate && record.date === fromDate) {
                                    shortLeave_pres = true;
                                } else if (record.leave_type === 'Present') {
                                    sessionMessage.text('The employee was present on ' + record
                                        .date);
                                    present = true;
                                }
                            });

                            if (hasFirstHalf) {
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" disabled>Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" selected>Half Day</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" disabled>2nd Half</option>');
                                halfDropdown.append(
                                    '<option value="1st Half" selected>1st Half</option>');

                            } else if (hasSecondHalf) {
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" disabled>Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" selected>Half Day</option>');
                                halfDropdown.append(
                                    '<option value="1st Half" disabled>1st Half</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" selected>2nd Half</option>');


                            } else if (hasFullDay) {
                                sessionDropdown.append(
                                    '<option value="Half Day" disabled>Half Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" disabled>Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Full Day" selected>Full Day</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" disabled>2nd Half</option>');
                                halfDropdown.append(
                                    '<option value="1st Half" disabled>1st Half</option>');

                            } else if (pres_shortLeave) {
                                halfDropdown.append(
                                    '<option value="1st Half" disabled>1st Half</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" selected>2nd Half</option>');
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" disabled>Half Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" selected>Short Leave</option>');

                            } else if (shortLeave_pres) {
                                halfDropdown.append(
                                    '<option value="1st Half" selected>1st Half</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" disabled>2nd Half</option>');
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" disabled>Half Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" selected>Short Leave</option>');

                                // console.log('Short Leave--Present');

                            } else if (present) {
                                halfDropdown.append(
                                    '<option value="1st Half" disabled>1st Half</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half" disabled>2nd Half</option>');
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" disabled>Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" disabled>Half Day</option>');

                            } else if (fromDate != toDate) {
                                sessionDropdown.append(
                                    '<option value="Full Day" selected>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave" disabled>Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day" disabled>Half Day</option>');
                                console.log("fromDate != toDate");
                                $('#half').prop('disabled', true);
                                $('#half').val(half);

                            } else if (fromDate === toDate) {
                                sessionDropdown.append(
                                    '<option value="Full Day" disabled>Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave">Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day">Half Day</option>');
                                halfDropdown.append(
                                    '<option value="1st Half">1st Half</option>');
                                halfDropdown.append(
                                    '<option value="2nd Half">2nd Half</option>');
                                console.log("fromDate === toDate");
                                $('#half').val(half);

                            } else {
                                halfDropdown.append('<option value="1st Half">1st Half</option>');
                                halfDropdown.append('<option value="2nd Half">2nd Half</option>');
                                sessionDropdown.append('<option value="Full Day">Full Day</option>');
                                sessionDropdown.append(
                                    '<option value="Short Leave">Short Leave</option>');
                                sessionDropdown.append(
                                    '<option value="Half Day">Half Day</option>');
                            }

                            if (present) {
                                $('#submitBtn').prop('disabled', true);
                            } else {
                                $('#submitBtn').prop('disabled', false);
                            }
                        } else {
                            console.error('Error fetching attendance:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                    }
                });
            }

            // Fetch employee names and leave types on page load
            fetchEmployeeNames();
            fetchLeaveTypes();

            // Fetch holidays from the API
            function fetchHolidays() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_holidays',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            holidays = response.Holidays.map(holiday => holiday.holiday_date);
                        }
                    },
                    error: function() {
                        alert('Failed to fetch holidays');
                    }
                });
            }

            fetchHolidays();

            // Attach event listeners to update session dropdown when dates change
            $('#from_date').on('change', function() {
                updateSessionDropdown();
                updateDateTo();
            });
            $('#to_date').on('change', function() {
                updateSessionDropdown();
            });

            const picker1 = document.getElementById('from_date');
            picker1.addEventListener('change', function(e) {
                if (this.value) {
                    var day = new Date(this.value).getUTCDay();
                    if ([6, 0].includes(day)) {
                        e.preventDefault();
                        this.value = '';
                        alert('Weekends not allowed');
                    } else if (holidays.includes(this.value)) {
                        e.preventDefault();
                        this.value = '';
                        alert('Holidays not allowed');
                    }
                }
            });

            const picker2 = document.getElementById('to_date');
            picker2.addEventListener('change', function(e) {
                if (this.value) {
                    var day = new Date(this.value).getUTCDay();
                    if ([6, 0].includes(day)) {
                        e.preventDefault();
                        this.value = '';
                        alert('Weekends not allowed');
                    } else if (holidays.includes(this.value)) {
                        e.preventDefault();
                        this.value = '';
                        alert('Holidays not allowed');
                    }
                }
            });

            // Set date range for from_date based on leave type
            $('#leave_type').on('change', function() {
                var leaveType = $(this).val();
                var today = new Date();
                var minDate, maxDate;

                if (leaveType == 3) { // Maternity Leave
                    minDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 180);
                    maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 180);
                } else {
                    minDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 20);
                    maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 20);
                }

                $('#from_date').attr('min', formatDate(minDate));
                $('#from_date').attr('max', formatDate(maxDate));

                updateDateTo();
                updateSessionDropdown();
            });

            // Update date range for to_date based on from_date and leave type
            function updateDateTo() {
                var leaveType = $('#leave_type').val();
                var fromDate = new Date($('#from_date').val());
                var minDate, maxDate;
                var today = new Date();
                // console.log(fromDate);
                // console.log(today);
                if (leaveType == 3) { // Maternity Leave
                    minDate = fromDate;
                    maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 180);
                } else {
                    minDate = fromDate;
                    maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 20);
                }

                $('#to_date').attr('min', formatDate(minDate));
                $('#to_date').attr('max', formatDate(maxDate));

                updateSessionDropdown();
            }

            // Format date as yyyy-mm-dd
            function formatDate(date) {
                var year = date.getFullYear();
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var day = ('0' + date.getDate()).slice(-2);
                return year + '-' + month + '-' + day;
            }

            // Clear form
            $('#clearBtn').on('click', function() {
                $('#leaveApplicationForm')[0].reset();
                // $('#session').html('<option value="">Select Session</option>');
                // $('#half').html('<option value="">Select Half</option>');
                $('#sessionMessage').text('');
                $('#submitBtn').prop('disabled', false);
                $('#half').html('<option value="">Select Half</option>');
            });

            // Update character count for remarks textbox
            function updateRemarksCharCount() {
                var charCount = $('#remarks').val().length;
                $('#charCount').text(charCount);

                if (charCount >= 10) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            }

            // Update remarks character count when user types
            $('#remarks').keyup(updateRemarksCharCount);

            // Initial character count update
            updateRemarksCharCount();

            // Submit leave application
            function submitLeaveApplication() {

                if ($('#remarks').val().length < 10) {
                    alert('Remarks must contain at least 10 characters.');
                    return;
                }

                if ($('#session').val() === null) {
                    alert('Please Select Session');
                    return;
                }

                if ($('#half').val() === null) {
                    alert('Please Select Half');
                    return;
                }

                if ($('#submitBtn').prop('disabled')) {
                    alert(
                        'Cannot submit application because the user was present on the selected date.'
                    );
                    return;
                }

                var formData = $('#leaveApplicationForm').serialize();
                console.log('Form data:', formData); // Debugging

                $.ajax({
                    url: 'http://localhost:8000/api/leave_application',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.status === 201) {
                            $('#successToast').toast('show');
                            $('#leaveApplicationForm')[0].reset();
                            $('#charCount').text('0'); // Reset character count
                            $('#session').html(
                                '<option value="">Select Session</option><option value="Half Day">Half Day</option><option value="Full Day">Full Day</option><option value="Short Leave">Short Leave</option>'
                            );
                            $('#half').html(
                                '<option value="">Select Half</option><option value="1st Half">1st Half</option><option value="2nd Half">2nd Half</option>'
                            );
                            $('#half').prop('disabled', false);
                        } else {
                            console.error('Error submitting leave application:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request error:', status, error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }


            // Handle Submit button click event
            $('#submitBtn').click(function() {
                // var session = $('#session').val();
                // var half = $('#half').val();
                // var concatenatedSession = session + "-" + half;
                // $('#session').val(concatenatedSession);
                // console.log(concatenatedSession);
                console.log('Submit button clicked'); // Debugging
                submitLeaveApplication();
            });
        });
    </script>
@endsection
