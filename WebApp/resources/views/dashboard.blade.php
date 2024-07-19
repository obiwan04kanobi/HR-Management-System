@extends('layouts.bootstrap')

@section('content')
    <div class="container">

        <!-- Success Alert -->
        @if (session('login_success'))
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                You have successfully logged in.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- You Have Unread Messages Alert -->
        <div id="successAlertContainer" class="mt-4">
            <!-- Alert will be dynamically added here -->
        </div>

        <div class="row mt-4">
            <!-- Employee Details Card -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Details</h5>
                        <p class="card-text"><strong>Employee ID:</strong> <span id="employee_id"></span></p>
                        <p class="card-text"><strong>Name:</strong> <span id="employee_name"></span></p>
                        <p class="card-text"><strong>Email:</strong> <span id="employee_email"></span></p>
                        <p class="card-text"><strong>Date Joined:</strong> <span id="date_join"></span></p>
                    </div>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Messages</h5>
                            <button id="clearMessages" class="btn btn-danger btn-sm">Clear</button>
                        </div>
                        <div class="messages-scroll" style="max-height: 145px; overflow-y: auto;">
                            <ul id="messagesList" class="list-group">
                                <!-- Messages will be dynamically added here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="messageDetails"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            let lastCheckedMessages = [];
            let currentMessageID = null;

            // Function to fetch and display employee data
            function fetchEmployeeData() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_employees',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 200) {
                            const employee = response.Employees_Name.find(emp => emp.email ===
                                '{{ Auth::user()->email }}');
                            if (employee) {
                                $('#employee_id').text(employee.employee_id);
                                $('#employee_name').text(employee.name);
                                $('#employee_email').text(employee.email);
                                $('#date_join').text(employee.date_join);
                            }
                        } else {
                            console.error('Failed to fetch employee data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching employee data:', error);
                    }
                });
            }

            // Initial fetch of employee data
            fetchEmployeeData();

            // Function to fetch and display messages
            function fetchMessages() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_messages',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 200) {
                            const messagesList = $('#messagesList');
                            messagesList.empty(); // Clear existing messages

                            let hasUnreadMessages = false;

                            response.messages.forEach(message => {
                                if (message.message && message.message_to === loggedInUserId && message.message_status === 1 || message.message_status === 2) {
                                    const listItem = $('<li>')
                                        .addClass('list-group-item')
                                        .html(
                                            `<strong>${message.attendance_date || 'No Date'}</strong>: ${message.message.substring(0, 50)}... <a href="#" class="message-details" data-message="${message.message}" data-id="${message.attendance_id}">Read More</a>`
                                        );
                                    messagesList.append(listItem);
                                    currentMessageID = message.message_id;
                                    console.log("Selected Message",message.message_id);

                                    if (message.message_status === 1) {
                                        hasUnreadMessages = true;
                                    }
                                }
                            });

                            if (!hasUnreadMessages) {
                                messagesList.html('<li class="list-group-item">No messages</li>');
                            }

                            // Show success alert if there are unread messages
                            const successAlertContainer = $('#successAlertContainer');
                            successAlertContainer.empty();

                            if (hasUnreadMessages) {
                                const successAlert = $('<div>')
                                    .addClass('alert alert-success alert-dismissible fade show mt-4')
                                    .attr('role', 'alert')
                                    .html('You have unread messages.')
                                    .append(
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                                    );
                                successAlertContainer.append(successAlert);
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

            // Initial fetch of messages
            fetchMessages();

            // Poll for new messages every 1 seconds
            setInterval(function() {
                fetchMessages();
            }, 1000); // 1 second

            // Function to handle message details modal
            $(document).on('click', '.message-details', function(e) {
                e.preventDefault();
                const message = $(this).data('message');
                const attendanceId = $(this).data('id');
                $('#messageDetails').text(message);
                $('#messageModal').modal('show');
                // Update message status to read
                console.log("message details opened");
                $.ajax({
                    url: 'http://localhost:8000/api/update_message_status',
                    method: 'POST',
                    data: {
                        message_id: currentMessageID,
                        message_status: 2, // Mark as read
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            fetchMessages(); // Fetch updated messages after marking as read
                        } else {
                            console.error('Failed to update message status');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating message status:', error);
                    }
                });
            });

            // Function to clear messages
            $('#clearMessages').click(function() {
                $.ajax({
                    url: 'http://localhost:8000/api/clear_messages',
                    method: 'POST',
                    data: {
                        message_to: loggedInUserId,
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            fetchMessages(); // Fetch updated messages after clearing
                        } else {
                            console.error('Failed to clear messages');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error clearing messages:', error);
                    }
                });
            });
        });
    </script>
@endsection
