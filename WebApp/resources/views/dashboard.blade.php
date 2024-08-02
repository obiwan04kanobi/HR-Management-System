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
                    <ul id="threadMessages" class="list-group">
                        <!-- Thread messages will be dynamically added here -->
                    </ul>
                    <button id="replyButton" class="btn btn-primary mt-3">Reply</button>
                    <div id="replySection" class="mt-3" style="display: none;">
                        <textarea id="replyMessage" class="form-control mb-3" rows="3"></textarea>
                        <button id="sendReply" class="btn btn-success">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            let employeesData = [];
            let loggedinAdmin; // 0 means false, 1 means true
            let employee_boss;
            let globalparentID;

            // Function to fetch and display employee data
            async function fetchEmployeeData() {
                try {
                    const response = await $.ajax({
                        url: 'http://localhost:8000/api/display_employees',
                        method: 'GET',
                        dataType: 'json'
                    });

                    if (response.status === 200) {
                        const employee = response.Employees_Name.find(emp => emp.email ===
                            '{{ Auth::user()->email }}');
                        if (employee) {
                            $('#employee_id').text(employee.employee_id);
                            $('#employee_name').text(employee.name);
                            $('#employee_email').text(employee.email);
                            $('#date_join').text(employee.date_join);
                            employee_boss = employee.report_to;
                            console.log(employee_boss);
                        }
                    } else {
                        console.error('Failed to fetch employee data:', response);
                    }
                } catch (error) {
                    console.error('Error fetching employee data:', error);
                }
            }

            // Initial fetch of employee data
            fetchEmployeeData();

            //////////////////////////////////////// 1. Fetch and Display Messages ////////////////////////////////////////

            // Check if the logged-in user is an admin
            $.ajax({
                url: 'http://localhost:8000/api/display_employees',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    employeesData = data.Employees_Name;
                    const isAdmin = employeesData.some(employee => employee.report_to ===
                        loggedInUserId);

                    loggedinAdmin = isAdmin ? 1 : 0;
                    console.log(isAdmin ? "admin logged in" : "normal user logged in");

                },
                error: function(error) {
                    console.error('Error fetching employees:', error);
                }
            });

            //////////////////////////////////////// 2. Fetch and Display Messages ////////////////////////////////////////

            async function fetchMessages() {
                try {
                    let response;
                    if (loggedinAdmin === 1) {
                        response = await $.ajax({
                            url: 'http://localhost:8000/api/display_admin_messages',
                            method: 'GET',
                            dataType: 'json'
                        });
                    } else if (loggedinAdmin === 0) {
                        response = await $.ajax({
                            url: 'http://localhost:8000/api/display_emp_messages',
                            method: 'GET',
                            dataType: 'json'
                        });
                    }

                    if (response && response.status === 200) {
                        const messagesList = $('#messagesList');
                        messagesList.empty(); // Clear existing messages

                        let hasUnreadMessages = false;

                        response.messages.forEach(message => {
                            if (message.message && message.message_to === loggedInUserId && (message
                                    .message_status === 1 || message.message_status === 2)) {
                                const listItem = $('<li>')
                                    .addClass('list-group-item')
                                    .html(
                                        `<strong>${message.attendance_date || 'No Date'}</strong>: ${message.message.substring(0, 50)}... <a href="#" class="message-details" data-message="${message.message}" data-id="${message.employee_message_id}" data-from="${message.message_from}" data-to="${message.message_to}" data-attendance="${message.attendance_id}" data-parent="${message.parent_id}">Read More</a>`
                                    );
                                messagesList.append(listItem);

                                if (message.message_status === 1) {
                                    hasUnreadMessages = true;
                                }
                                else if (message.message_status === 1 || message.message_status === 2) {
                                    globalparentID = message.parent_message
                                    console.log(`ParentID: ${message.parent_message}`);
                                }


                            }
                        });

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
                        console.error('Failed to fetch messages:', response);
                    }
                } catch (error) {
                    console.error('Error fetching messages:', error);
                }
            }


            // Initial fetch of messages
            fetchMessages();

            // Poll for new messages every 10 seconds
            setInterval(fetchMessages, 1000); // 10 seconds

            $(document).on('click', '.message-details', async function(e) {
                e.preventDefault();
                const message = $(this).data('message');
                const messageId = $(this).data('id');
                const messageFrom = $(this).data('from');
                const messageTo = $(this).data('to');
                const attendanceId = $(this).data('attendance');
                const parentId = $(this).data('parent'); // Ensure this is correctly set

                $('#messageDetails').text(message);
                $('#messageModal').modal('show');
                currentMessageID = messageId;
                currentMessageFrom = messageFrom;
                currentAttendanceID = attendanceId;

                await fetchThreadMessages(messageId);

                // Show reply section
                $('#replyButton').off('click').click(function() {
                    $('#replySection').toggle();
                });

                // Send reply
                $('#sendReply').off('click').click(async function() {
                    const replyMessage = $('#replyMessage').val();
                    await sendReply(replyMessage, globalparentID, messageFrom,
                        currentAttendanceID);
                });

                // Update message status to read
                try {
                    if (loggedinAdmin === 1) {
                        console.log(`${currentMessageID}+${messageFrom}+${currentAttendanceID}`);
                        await $.ajax({
                            url: 'http://localhost:8000/api/update_message_status',
                            method: 'POST',
                            data: {
                                message_id: globalparentID,
                                message_status: 2, // Mark as read
                                _token: '{{ csrf_token() }}' // CSRF token
                            }
                        });
                    } else if (loggedinAdmin === 0) {
                        console.log(`${currentMessageID}+${messageFrom}+${currentAttendanceID}`);
                        await $.ajax({
                            url: 'http://localhost:8000/api/emp_update_message_status',
                            method: 'POST',
                            data: {
                                employee_message_id: currentMessageID,
                                message_status: 2, // Mark as read
                                _token: '{{ csrf_token() }}' // CSRF token
                            }
                        });
                    }

                    fetchMessages(); // Fetch updated messages after marking as read
                } catch (error) {
                    console.error('Error updating message status:', error);
                }
            });


            // Function to fetch thread messages
            // Function to fetch thread messages
            async function fetchThreadMessages(parentId) {
                try {
                    let response;
                    if (loggedinAdmin === 1) {
                        response = await $.ajax({
                            url: `http://localhost:8000/api/display_thread_messages/${parentId}`,
                            method: 'GET',
                            dataType: 'json'
                        });
                    } else if (loggedinAdmin === 0) {
                        response = await $.ajax({
                            url: `http://localhost:8000/api/emp_display_thread_messages/${parentId}`,
                            method: 'GET',
                            dataType: 'json'
                        });
                    }

                    if (response && response.status === 200) {
                        const threadMessages = $('#threadMessages');
                        threadMessages.empty();

                        let currentDate = '';
                        response.thread_messages.forEach(message => {
                            // Group by attendance date
                            if (currentDate !== message.attendance_date) {
                                threadMessages.append(
                                    `<h4>Attendance Date: ${message.attendance_date}</h4>`);
                                currentDate = message.attendance_date;
                            }

                            // Display message details
                            const listItem = $('<li>')
                                .addClass('list-group-item')
                                .html(
                                    `<strong>From:</strong> ${message.message_from_name} <br>
                        <strong>To:</strong> ${message.message_to_name} <br>
                        <strong>Message:</strong> ${message.message}`
                                );
                            threadMessages.append(listItem);
                        });
                    } else {
                        console.error('Failed to fetch thread messages:', response);
                    }
                } catch (error) {
                    console.error('Error fetching thread messages:', error);
                }
            }



            async function sendReply(message, parentId, message_from, attendanceId) {
                try {
                    let response;
                    const url = loggedinAdmin === 1 ?
                        'http://localhost:8000/api/send_reply' :
                        'http://localhost:8000/api/emp_send_reply';

                    console.log(`Sending request to: ${url}`); // Debug URL
                    console.log(`parentID: ${globalparentID}`);
                    response = await $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            parent_id: loggedinAdmin === 1 ? globalparentID : currentMessageID,
                            message: message,
                            message_from: loggedInUserId,
                            message_to: loggedinAdmin === 1 ? message_from : employee_boss,
                            attendance_id: attendanceId,
                            _token: '{{ csrf_token() }}' // CSRF token
                        },
                        success: function(data) {
                            console.log('AJAX success:', data); // Debug success response
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error); // Debug error response
                        }
                    });

                    console.log('API Response:', response); // Debug API response

                    if (response && response.status === 200) {
                        $('#replyMessage').val('');
                        fetchMessages(); // Refresh messages
                    } else {
                        console.error('Failed to send reply:', response);
                    }
                } catch (error) {
                    console.error('Error sending reply:', error);
                }
            }


            // Function to clear messages
            $('#clearMessages').click(async function() {
                try {
                    if (loggedinAdmin === 1) {
                        await $.ajax({
                            url: 'http://localhost:8000/api/clear_messages',
                            method: 'POST',
                            data: {
                                message_to: loggedInUserId,
                                _token: '{{ csrf_token() }}' // CSRF token
                            }
                        });
                    } else if (loggedinAdmin === 0) {
                        await $.ajax({
                            url: 'http://localhost:8000/api/emp_clear_messages',
                            method: 'POST',
                            data: {
                                message_to: loggedInUserId,
                                _token: '{{ csrf_token() }}' // CSRF token
                            }
                        });
                    }

                    fetchMessages(); // Fetch updated messages after clearing
                } catch (error) {
                    console.error('Error clearing messages:', error);
                }
            });
        });
    </script>
@endsection
