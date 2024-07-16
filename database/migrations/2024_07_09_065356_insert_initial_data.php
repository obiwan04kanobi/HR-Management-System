<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertInitialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // Insert predefined roles into role_master table
        DB::statement("
            INSERT INTO role_master (role, active, created_by, updated_by, created_at, updated_at)
            VALUES 
                ('Intern', 1, 1, 1, NOW(), NOW()),
                ('Junior Software Engineer', 1, 1, 1, NOW(), NOW()),
                ('Software Engineer', 1, 1, 1, NOW(), NOW()),
                ('Senior Software Engineer', 1, 1, 1, NOW(), NOW()),
                ('Deputy Manager', 1, 1, 1, NOW(), NOW()),
                ('Assistant Manager', 1, 1, 1, NOW(), NOW()),
                ('HR Manager', 1, 1, 1, NOW(), NOW()),
                ('Accountant', 1, 1, 1, NOW(), NOW()),
                ('Marketing Specialist', 1, 1, 1, NOW(), NOW()),
                ('Operations Manager', 1, 1, 1, NOW(), NOW())
        ");

        // Insert example departments into department_master table
        DB::statement("
            INSERT INTO department_master (dept_name, active, created_at, updated_at)
            VALUES 
                ('IT', 1, NOW(), NOW()),
                ('Human Resources', 1, NOW(), NOW()),
                ('Finance', 1, NOW(), NOW()),
                ('Marketing', 1, NOW(), NOW()),
                ('Operations', 1, NOW(), NOW())
        ");

        // Insert example employees into employees table
        DB::statement("
            INSERT INTO employees (name, department, designation, report_to, date_join, status, email)
            VALUES
                ('John Doe', 1, 3, NULL, '2024-06-01', 1, 'john.doe@lava.com'),
                ('Jane Smith', 2, 7, 1, '2023-12-15', 1, 'jane.smith@lava.com'),
                ('Michael Johnson', 3, 8, 1, '2024-01-10', 1, 'michael.johnson@lava.com'),
                ('Emily Brown', 4, 9, 3, '2024-03-20', 1, 'emily.brown@lava.com'),
                ('David Wilson', 5, 10, 3, '2024-02-05', 1, 'david.wilson@lava.com'),
                ('Mayank Pant', 1, 1, 3, '2024-07-08', 1, 'mayank.pant@lava.com')
        ");

        // Insert example leave masters into leave_masters table
        DB::statement("
            INSERT INTO leave_masters (leave_type, days, status)
            VALUES
                ('General', 20, 1),
                ('Emergency', 10, 1),
                ('Maternity Leave', 180, 1),
                ('Half Day', 0, 1),
                ('Short Leave', 0, 1)
        ");

        // Insert example attendace masters into attendance_masters table
        DB::statement("
            INSERT INTO attendance_masters (employee_id, punch_in, punch_out, date)
            VALUES
                -- Employee 1
                (1, '09:30:00', '18:30:00', '2024-06-20'),
                (1, '09:35:00', '18:35:00', '2024-06-19'),
                (1, '09:32:00', '18:31:00', '2024-06-18'),
                (1, '09:31:00', '18:32:00', '2024-06-17'),
                (1, '09:33:00', '18:34:00', '2024-06-16'),
                (1, '09:34:00', '18:33:00', '2024-06-15'),
                (1, '09:36:00', '18:36:00', '2024-06-14'),
                (1, '09:37:00', '18:37:00', '2024-06-13'),
                (1, '09:38:00', '18:38:00', '2024-06-12'),
                (1, '09:39:00', '18:39:00', '2024-06-11'),

                -- Employee 2
                (2, '09:30:00', '18:30:00', '2024-06-20'),
                (2, '09:25:00', '18:31:00', '2024-06-19'),
                (2, '09:28:00', '18:32:00', '2024-06-18'),
                (2, '09:29:00', '18:33:00', '2024-06-17'),
                (2, '09:31:00', '18:34:00', '2024-06-16'),
                (2, '09:33:00', '18:35:00', '2024-06-15'),
                (2, '09:34:00', '18:36:00', '2024-06-14'),
                (2, '09:36:00', '18:37:00', '2024-06-13'),
                (2, '09:37:00', '18:38:00', '2024-06-12'),
                (2, '09:38:00', '18:39:00', '2024-06-11'),

                -- Employee 3
                (3, '09:30:00', '18:30:00', '2024-06-20'),
                (3, '09:35:00', '18:35:00', '2024-06-19'),
                (3, '09:32:00', '18:31:00', '2024-06-18'),
                (3, '09:31:00', '18:32:00', '2024-06-17'),
                (3, '09:33:00', '18:34:00', '2024-06-16'),
                (3, '09:34:00', '18:33:00', '2024-06-15'),
                (3, '09:36:00', '18:36:00', '2024-06-14'),
                (3, '09:37:00', '18:37:00', '2024-06-13'),
                (3, '09:38:00', '18:38:00', '2024-06-12'),
                (3, '09:39:00', '18:39:00', '2024-06-11'),

                -- Employee 4
                (4, '09:30:00', '18:30:00', '2024-06-20'),
                (4, '09:25:00', '18:31:00', '2024-06-19'),
                (4, '09:28:00', '18:32:00', '2024-06-18'),
                (4, '09:29:00', '18:33:00', '2024-06-17'),
                (4, '09:31:00', '18:34:00', '2024-06-16'),
                (4, '09:33:00', '18:35:00', '2024-06-15'),
                (4, '09:34:00', '18:36:00', '2024-06-14'),
                (4, '09:36:00', '18:37:00', '2024-06-13'),
                (4, '09:37:00', '18:38:00', '2024-06-12'),
                (4, '09:38:00', '18:39:00', '2024-06-11'),

                -- Employee 5
                (5, '09:30:00', '18:30:00', '2024-06-20'),
                (5, '09:25:00', '18:31:00', '2024-06-19'),
                (5, '09:28:00', '18:32:00', '2024-06-18'),
                (5, '09:29:00', '18:33:00', '2024-06-17'),
                (5, '09:31:00', '18:34:00', '2024-06-16'),
                (5, '09:33:00', '18:35:00', '2024-06-15'),
                (5, '09:34:00', '18:36:00', '2024-06-14'),
                (5, '09:36:00', '18:37:00', '2024-06-13'),
                (5, '09:37:00', '18:38:00', '2024-06-12'),
                (5, '09:38:00', '18:39:00', '2024-06-11'),

                -- Employee 1
                (1, '09:30:00', '18:30:00', '2024-06-21'),

                -- Employee 2
                (2, '09:31:00', '18:35:00', '2024-06-21'),

                -- Employee 3
                (3, '10:35:00', '19:30:00', '2024-06-21'),

                -- Employee 4
                (4, '09:30:00', '18:30:00', '2024-06-21'),

                -- Employee 5
                (5, '15:00:00', '18:30:00', '2024-06-21'),

                -- Employee 1
                (1, '09:30:00', '18:30:00', '2024-07-02'),

                -- Employee 2
                (2, '09:31:00', '18:35:00', '2024-07-02'),

                -- Employee 3
                (3, '10:35:00', '19:30:00', '2024-07-02'),

                -- Employee 4
                (4, '09:30:00', '18:30:00', '2024-07-02'),

                -- Employee 5
                (5, '15:00:00', '18:30:00', '2024-07-02'),

                -- Employee 1
                (1, '09:35:00', '18:30:00', '2024-07-03'),

                -- Employee 2
                (2, '15:31:00', '18:35:00', '2024-07-03'),

                -- Employee 3
                (3, NULL, NULL, '2024-07-03'),

                -- Employee 4
                (4, '12:30:00', '18:30:00', '2024-07-03'),

                -- Employee 5
                (5, '09:00:00', '18:00:00', '2024-07-03'),


                -- Employee 1
                (1, '11:25:00', '18:30:00', '2024-06-10'),

                -- Employee 2
                (2, '09:25:00', '16:30:00', '2024-06-10'),

                -- Employee 3
                (3, '09:15:00', '18:40:00', '2024-06-10'),

                -- Employee 4
                (4, '09:10:00', '14:00:00', '2024-06-10'),

                -- Employee 5
                (5, '13:45:00', '18:30:00', '2024-06-10'),

                -- Employee 1
                (1, NULL, NULL, '2024-06-07'),

                -- Employee 2
                (2, '14:31:00', '18:35:00', '2024-06-07'),

                -- Employee 3
                (3, '14:10:00', '19:30:00', '2024-06-07'),

                -- Employee 4
                (4, '16:30:00', '18:30:00', '2024-06-07'),

                -- Employee 5
                (5, '08:15:00', '18:30:00', '2024-06-07'),

                -- Employee 6
                (6, '09:10:00', '18:36:00', '2024-07-08')
    ");

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Clear data if rollback is needed, although not recommended for production
    }
}
