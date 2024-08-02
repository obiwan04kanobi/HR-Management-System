@extends('layouts.bootstrap')

@section('content')
    <div class="container">
        <h1>Compoff Approvals (Admin Only)</h1>
        <div id="admin-message" class="alert alert-info" style="display: none;">Sorry, you are not an admin user.</div>
        <div id="no-leave-message" class="alert alert-info" style="display: none;">No leave applications currently.</div>
        <form id="leave-approval-form">
            <table id="leave-table" class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Employee Name</th>
                        <th>Compoff Taken Date</th>
                        <th>Compoff Used Date</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Leave data will be dynamically inserted here -->
                </tbody>
            </table>

            <!-- Modal for Leave Approval Confirmation -->
            <div class="modal fade" id="approveLeaveModal" tabindex="-1" role="dialog"
                aria-labelledby="approveLeaveModalLabel" aria-hidden="true">
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
            <div class="modal fade" id="rejectLeaveModal" tabindex="-1" role="dialog"
                aria-labelledby="rejectLeaveModalLabel" aria-hidden="true">
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
        // Function to format date
        const formatDate = (datetime) => {
            const date = new Date(datetime);
            return date.toLocaleDateString('en-GB');
        };

        // Function to handle leave approval
        const approveLeave = (application_ids) => {
            $('#approveLeaveModal').modal('show');

            $('#confirmApprove').off('click').on('click', function() {
                const promises = application_ids.map(id => {
                    return fetch(`http://localhost:8000/api/compoff/approve/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Failed to approve compoff application ${id}.`);
                            }
                            return response.json();
                        });
                });

                Promise.all(promises)
                    .then(responses => {
                        application_ids.forEach(id => {
                            $(`#leave-${id}`).remove();
                        });
                        $('#approveLeaveModal').modal('hide');
                    })
                    .catch(error => {
                        console.error('Error approving compoff applications:', error);
                    });
            });
        };

        // Function to handle leave rejection
        const rejectLeave = (application_ids) => {
            $('#rejectLeaveModal').modal('show');

            $('#confirmReject').off('click').on('click', function() {
                const promises = application_ids.map(id => {
                    return fetch(`http://localhost:8000/api/compoff/reject/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Failed to reject compoff application ${id}.`);
                            }
                            return response.json();
                        });
                });

                Promise.all(promises)
                    .then(responses => {
                        application_ids.forEach(id => {
                            $(`#leave-${id}`).remove();
                        });
                        $('#rejectLeaveModal').modal('hide');
                    })
                    .catch(error => {
                        console.error('Error rejecting compoff applications:', error);
                    });
            });
        };

        // Fetch leave applications on page load
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            const loggedInUserReportTo = {{ Auth::user()->report_to ?? 'null' }};

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
                        $('#footer-buttons').hide();
                        return;
                    }

                    $.ajax({
                        url: 'http://localhost:8000/api/display_compoffs',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            const compoffs = data.Compoffs;
                            const tableBody = $('#leave-table tbody');
                            let hasCompoffs = false;

                            compoffs.forEach(compoff => {
                                if (compoff.employee_id === loggedInUserId ||
                                    loggedInUserReportTo === null && compoff.status === 1) {
                                    hasCompoffs = true;
                                    const row = `
                                        <tr id="leave-${compoff.compoff_id}">
                                            <td><input type="checkbox" class="leaveApprovalCheckbox" name="leaveApproval[]" value="${compoff.compoff_id}"></td>
                                            <td>${compoff.employee.name}</td>
                                            <td>${formatDate(compoff.compoff_taken_date)}</td>
                                            <td>${formatDate(compoff.date)}</td>
                                            <td>${compoff.remarks}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary" onclick="approveLeave([${compoff.compoff_id}])">Approve</button>
                                                <button type="button" class="btn btn-secondary" onclick="rejectLeave([${compoff.compoff_id}])">Reject</button>
                                            </td>
                                        </tr>
                                    `;
                                    tableBody.append(row);
                                }
                            });

                            if (!hasCompoffs) {
                                $('#no-leave-message').show();
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching compoff applications:', error);
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

        // Handle bulk approval and rejection
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
        $('#checkAll').change(function() {
            $('.leaveApprovalCheckbox').prop('checked', $(this).prop('checked'));
        });
    </script>
@endsection
