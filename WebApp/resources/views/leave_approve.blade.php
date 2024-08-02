@extends('layouts.bootstrap')

@section('content')
    <div class="container">
        <h1>Leave Approvals (Admin Only)</h1>
        <div id="admin-message" class="alert alert-info" style="display: none;">Sorry, you are not an admin user.</div>
        <div id="no-leave-message" class="alert alert-info" style="display: none;">No leave applications currently.</div>
        <form id="leave-approval-form">
            <table id="leave-table" class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Session</th>
                        <th>Half</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Remarks</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Leave data will be dynamically inserted here -->
                </tbody>
            </table>

            <!-- Modal for Leave Approval Confirmation -->
            <div class="modal fade" id="approveLeaveModal" tabindex="-1" role="dialog" aria-labelledby="approveLeaveModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveLeaveModalLabel">Confirm Leave Approval</h5>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to approve selected leave applications?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmApprove">Approve</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Leave Rejection Confirmation -->
            <div class="modal fade" id="rejectLeaveModal" tabindex="-1" role="dialog" aria-labelledby="rejectLeaveModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectLeaveModalLabel">Confirm Leave Rejection</h5>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to reject selected leave applications?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div id="footer-buttons" class="fixed-bottom bg-light p-3 d-flex justify-content-end" style="display: none;">
            <button type="button" class="btn btn-primary me-2" id="approveBottom">Approve</button>
            <button type="button" class="btn btn-danger" id="rejectBottom">Reject</button>
        </div>
    </div>

    <script>
        // Function to format date and time properly
        const formatDate = (datetime) => {
            const date = new Date(datetime);
            return date.toLocaleString('en-US', {
                timeZone: 'Asia/Kolkata'
            });
        };

        // Function to handle leave approval
        const approveLeave = (application_ids) => {
            $('#approveLeaveModal').modal('show'); // Show the approval confirmation modal

            // Handle approval confirmation
            $('#confirmApprove').on('click', function() {
                const promises = application_ids.map(id => {
                    return fetch(`http://localhost:8000/api/leave/approve/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            application_id: id
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Failed to approve leave application ${id}.`);
                        }
                        return response.json();
                    });
                });

                Promise.all(promises)
                    .then(responses => {
                        // Handle all successful responses
                        console.log('Leave applications approved successfully:', responses);

                        // Remove the approved leave applications from the table
                        application_ids.forEach(id => {
                            $(`#leave-${id}`).remove();
                        });

                        $('#approveLeaveModal').modal('hide'); // Hide modal after processing
                    })
                    .catch(error => {
                        console.error('Error approving leave applications:', error);
                    });
            });
        };

        // Function to handle leave rejection
        const rejectLeave = (application_ids) => {
            $('#rejectLeaveModal').modal('show'); // Show the rejection confirmation modal

            // Handle rejection confirmation
            $('#confirmReject').on('click', function() {
                const promises = application_ids.map(id => {
                    return fetch(`http://localhost:8000/api/leave/reject/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            application_id: id
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Failed to reject leave application ${id}.`);
                        }
                        return response.json();
                    });
                });

                Promise.all(promises)
                    .then(responses => {
                        // Handle all successful responses
                        console.log('Leave applications rejected successfully:', responses);

                        // Remove the rejected leave applications from the table
                        application_ids.forEach(id => {
                            $(`#leave-${id}`).remove();
                        });

                        $('#rejectLeaveModal').modal('hide'); // Hide modal after processing
                    })
                    .catch(error => {
                        console.error('Error rejecting leave applications:', error);
                    });
            });
        };

        // Fetch leave applications via AJAX on page load
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            const loggedInUserReportTo = {{ Auth::user()->report_to ?? 'null' }};

            // Check if the logged-in user is an admin or their report_to is null
            $.ajax({
                url: 'http://localhost:8000/api/display_employees',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const employees = data.Employees_Name;
                    const isAdmin = employees.some(employee => employee.report_to === loggedInUserId) ||
                        loggedInUserReportTo === null;

                    if (!isAdmin) {
                        $('#admin-message').show();
                        $('#leave-table').hide();
                        $('#footer-buttons').hide(); // Hide footer buttons if not admin
                        return;
                    }

                    // Fetch leave applications if the user is an admin or their report_to is null
                    $.ajax({
                        url: 'http://localhost:8000/api/display_leaves',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            const leaves = data.Leaves;
                            const tableBody = $('#leave-table tbody');

                            let hasLeaves = false;

                            leaves.forEach(leave => {
                                // Check if the leave application is from an employee who reports to the logged-in user or if the user is self reporting
                                if (leave.report_to === loggedInUserId || (
                                        loggedInUserReportTo === null && leave
                                        .employee_id === loggedInUserId)) {
                                    hasLeaves = true;
                                    const row = `
                                        <tr id="leave-${leave.application_id}">
                                            <td><input type="checkbox" class="leaveApprovalCheckbox" name="leaveApproval[]" value="${leave.application_id}"></td>
                                            <td>${leave.employee_name}</td>
                                            <td>${leave.leave_type}</td>
                                            <td>${leave.session}</td>
                                            <td>${leave.half}</td>
                                            <td>${leave.from_date}</td>
                                            <td>${leave.to_date}</td>
                                            <td>${leave.remarks}</td>
                                            <td>${formatDate(leave.created_at)}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary"
                                                    onclick="approveLeave([${leave.application_id}])">Approve</button>
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="rejectLeave([${leave.application_id}])">Reject</button>
                                            </td>
                                        </tr>
                                    `;
                                    tableBody.append(row);
                                }
                            });

                            if (!hasLeaves) {
                                $('#no-leave-message').show();
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching leave applications:', error);
                        }
                    });
                },
                error: function(error) {
                    console.error('Error fetching employees:', error);
                }
            });
        });

        // Show footer buttons when scrolling
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#footer-buttons').fadeIn();
            } else {
                $('#footer-buttons').fadeOut();
            }
        });

        // Handle form submission for approving/rejecting multiple leave applications
        $('#approveBottom').click(function() {
            const selectedApplications = $('input[name="leaveApproval[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            approveLeave(selectedApplications);
        });

        $('#rejectBottom').click(function() {
            const selectedApplications = $('input[name="leaveApproval[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            rejectLeave(selectedApplications);
        });

        // Select All functionality
        $('#selectAll').click(function() {
            $('.leaveApprovalCheckbox').prop('checked', $(this).prop('checked'));
        });

        $('#checkAll').change(function() {
            $('.leaveApprovalCheckbox').prop('checked', $(this).prop('checked'));
        });
    </script>
@endsection
