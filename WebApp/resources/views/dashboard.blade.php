@extends('layouts.bootstrap')

@section('content')
<div class="container">
    <!-- Success Alert -->
    @if(session('login_success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        You have successfully logged in.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Employee Details</h5>
            <p class="card-text"><strong>Employee ID:</strong> <span id="employee_id"></span></p>
            <p class="card-text"><strong>Name:</strong> <span id="employee_name"></span></p>
            <p class="card-text"><strong>Email:</strong> <span id="employee_email"></span></p>
            <p class="card-text"><strong>Date Joined:</strong> <span id="date_join"></span></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch employee data
        fetch('http://localhost:8000/api/display_employees')
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    const employee = data.Employees_Name.find(emp => emp.email === '{{ Auth::user()->email }}');
                    if (employee) {
                        document.getElementById('employee_id').textContent = employee.employee_id;
                        document.getElementById('employee_name').textContent = employee.name;
                        document.getElementById('employee_email').textContent = employee.email;
                        document.getElementById('date_join').textContent = employee.date_join;
                    }
                } else {
                    console.error('Failed to fetch employee data');
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
@endsection
