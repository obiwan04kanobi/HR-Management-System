@extends('layouts.bootstrap')

@section('content')
    <div class="container">
        <h1 style="text-align: center">Compensatory Leave Application</h1>
        <div id="no-compoff-message" class="alert alert-info" style="display: none;">No compensatory leave applications
            currently.</div>
        <form id="compoff-application-form">
            <table id="compoff-table" class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Holiday</th>
                        <th>Date</th>
                        <th>Compensatory off Date</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Compoff data will be dynamically inserted here -->
                </tbody>
            </table>

            <!-- Modal for Leave Application Confirmation -->
            <div class="modal fade" id="applyCompoffModal" tabindex="-1" role="dialog"
                aria-labelledby="applyCompoffModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="applyCompoffModalLabel">Confirm Compoff Application</h5>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to apply for selected compensatory leaves?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmApply">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div id="footer-buttons" class="fixed-bottom bg-light p-3 d-flex justify-content-end" style="display: none;">
            <button type="button" class="btn btn-primary" id="applyBottom" disabled>Apply</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const loggedInUserId = {{ Auth::user()->employee_id }};
            let holidays = [];
            let leaveDates = []; // To store leave dates and types

            // Function to format date properly
            const formatDate = (datetime) => {
                const date = new Date(datetime);
                return date.toISOString().slice(0, 10); // Format date as YYYY-MM-DD
            };

            // Function to handle compoff application
            const applyCompoff = (compoff_ids) => {
                $('#applyCompoffModal').modal('show'); // Show the application confirmation modal

                // Handle application confirmation
                $('#confirmApply').off('click').on('click',
            function() { // Ensure event handler is not attached multiple times
                    $.ajax({
                        url: 'http://localhost:8000/api/update_compoff',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            compoff_ids: compoff_ids,
                            _token: '{{ csrf_token() }}' // Add CSRF token
                        },
                        success: function(response) {
                            $('#applyCompoffModal').modal(
                            'hide'); // Hide the modal on success
                            location
                        .reload(); // Reload the page after successful submission
                        },
                        error: function(error) {
                            console.error('Error applying compoff:', error);
                        }
                    });
                });
            };

            async function fetchCompoffs() {
                $.ajax({
                    url: 'http://localhost:8000/api/display_compoffs',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        const compoffs = data.Compoffs;
                        const tableBody = $('#compoff-table tbody');

                        let hasCompoffs = false;

                        compoffs.forEach(compoff => {
                            if (compoff.employee_id === loggedInUserId && compoff.status ===
                                -1) {
                                hasCompoffs = true;
                                const row = `
                                    <tr id="compoff-${compoff.compoff_id}">
                                        <td><input type="checkbox" class="compoffCheckbox" name="compoffApply[]" value="${compoff.compoff_id}"></td>
                                        <td>${compoff.holiday_name}</td>
                                        <td>${formatDate(compoff.date)}</td>
                                        <td><input type="date" class="form-control compoff-date-picker" name="compoff_taken_date_${compoff.compoff_id}"></td>
                                        <td><input type="text" class="form-control compoff-remarks" name="remarks_${compoff.compoff_id}" placeholder="Enter Remarks"></td>
                                        <td>
                                            <button type="button" class="btn btn-primary apply-btn" onclick="updateCompoff(${compoff.compoff_id})" disabled>Apply</button>
                                        </td>
                                    </tr>
                                `;
                                tableBody.append(row);
                            }
                        });

                        if (!hasCompoffs) {
                            $('#no-compoff-message').show();
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching compoff applications:', error);
                    }
                });
            }

            fetchCompoffs();

            function fetchHolidaysAndLeaveDates() {
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

                $.ajax({
                    url: 'http://localhost:8000/api/filter_employees',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            leaveDates = response.data.map(entry => ({
                                date: entry.date,
                                leave_type: entry.leave_type
                            }));
                        }
                    },
                    error: function() {
                        alert('Failed to fetch leave dates');
                    }
                });
            }

            fetchHolidaysAndLeaveDates();

            $(document).on('change', '.compoff-date-picker', function(e) {
                const selectedDate = new Date(this.value);
                const day = selectedDate.getUTCDay();
                const today = new Date();
                const minDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 60);
                const maxDate = today;

                if (day === 6 || day === 0) { // Check for weekends
                    e.preventDefault();
                    this.value = '';
                    alert('Weekends not allowed');
                } else if (holidays.includes(this.value)) { // Check for holidays
                    e.preventDefault();
                    this.value = '';
                    alert('Holidays not allowed');
                } else if (selectedDate < minDate || selectedDate > maxDate) { // Check date range
                    e.preventDefault();
                    this.value = '';
                    alert('Only dates within past 60 days from today are allowed');
                } else {
                    const isAllowedDate = leaveDates.some(entry => entry.date === this.value && entry
                        .leave_type === 'Absent');
                    if (!isAllowedDate) {
                        e.preventDefault();
                        this.value = '';
                        alert('Date is not allowed due to existing leave type');
                    }
                }
            });

            $(document).on('input', '.compoff-remarks', function() {
                const row = $(this).closest('tr');
                const applyButton = row.find('.apply-btn');
                const remarksLength = $(this).val().length;

                // Enable the button if remarks have at least 10 characters
                applyButton.prop('disabled', remarksLength < 10);
            });

            $(document).on('input', '.compoff-remarks', function() {
                const applyButton = $('#applyBottom');
                let allRemarksValid = true;

                $('.compoff-remarks').each(function() {
                    if ($(this).val().length < 10 && $(
                            `input[name="compoffApply[]"][value="${$(this).closest('tr').attr('id').split('-')[1]}"]`
                        ).is(':checked')) {
                        allRemarksValid = false;
                    }
                });

                applyButton.prop('disabled', !allRemarksValid);
            });

            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#footer-buttons').fadeIn();
                } else {
                    $('#footer-buttons').fadeOut();
                }
            });

            $('#applyBottom').click(function() {
                const selectedApplications = $('input[name="compoffApply[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedApplications.length > 0) {
                    applyCompoff(selectedApplications);
                } else {
                    alert('Please select at least one compensatory leave to apply.');
                }
            });

            $('#checkAll').change(function() {
                $('.compoffCheckbox').prop('checked', $(this).prop('checked'));
            });

            function toggleApplyButton() {
                const applyButton = $('#applyBottom');
                const anyChecked = $('.compoffCheckbox:checked').length > 0;
                applyButton.prop('disabled', !anyChecked);
            }

            $(document).on('change', '.compoffCheckbox', function() {
                toggleApplyButton();
            });

            window.updateCompoff = function(compoffId) {
                const row = $(`#compoff-${compoffId}`);
                const takenDate = row.find(`input[name="compoff_taken_date_${compoffId}"]`).val();
                const remarks = row.find(`input[name="remarks_${compoffId}"]`).val();

                $.ajax({
                    url: 'http://localhost:8000/api/update_compoff',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        compoff_id: compoffId,
                        compoff_taken_date: takenDate,
                        remarks: remarks,
                        _token: '{{ csrf_token() }}' // Add CSRF token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            location.reload(); // Reload the page on success
                        } else {
                            console.error(response.message || 'Failed to update compoff');
                        }
                    },
                    error: function(error) {
                        console.error('Error updating compoff:', error);
                    }
                });
            };
        });
    </script>
@endsection
