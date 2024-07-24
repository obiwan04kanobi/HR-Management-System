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


        // Insert example holidays_masters into holidays_masters table where date is in (yyyy-mm-dd) format
        DB::statement("
            INSERT INTO holidays_masters (holiday_name, holiday_date)
            VALUES
                ('New Year''s Day', '2024-01-01'),
                ('Lohri', '2024-01-13'),
                ('Makar Sankranti', '2024-01-14'),
                ('Pongal', '2024-01-15'),
                ('Guru Govind Singh Jayanti', '2024-01-17'),
                ('Hazarat Ali''s Birthday', '2024-01-25'),
                ('Republic Day', '2024-01-26'),
                ('Lunar New Year', '2024-02-10'),
                ('Vasant Panchami', '2024-02-14'),
                ('Valentine''s Day', '2024-02-14'),
                ('Shivaji Jayanti', '2024-02-19'),
                ('Guru Ravidas Jayanti', '2024-02-24'),
                ('Maharishi Dayanand Saraswati Jayanti', '2024-03-06'),
                ('Maha Shivaratri/Shivaratri', '2024-03-08'),
                ('Ramadan Start', '2024-03-12'),
                ('March Equinox', '2024-03-20'),
                ('Holika Dahana', '2024-03-24'),
                ('Holi', '2024-03-25'),
                ('Dolyatra', '2024-03-25'),
                ('Maundy Thursday', '2024-03-28'),
                ('Good Friday', '2024-03-29'),
                ('Easter Day', '2024-03-31'),
                ('Jamat Ul-Vida (Tentative Date)', '2024-04-05'),
                ('Chaitra Sukhladi', '2024-04-09'),
                ('Ugadi', '2024-04-09'),
                ('Gudi Padwa', '2024-04-09'),
                ('Ramzan Id/Eid-ul-Fitar', '2024-04-10'),
                ('Ramzan Id/Eid-ul-Fitar', '2024-04-11'),
                ('Vaisakhi', '2024-04-13'),
                ('Mesadi / Vaisakhadi', '2024-04-14'),
                ('Ambedkar Jayanti', '2024-04-14'),
                ('Rama Navami', '2024-04-17'),
                ('Mahavir Jayanti', '2024-04-21'),
                ('First day of Passover', '2024-04-23'),
                ('International Worker''s Day', '2024-05-01'),
                ('Birthday of Rabindranath', '2024-05-08'),
                ('Mother''s Day', '2024-05-12'),
                ('Buddha Purnima/Vesak', '2024-05-23'),
                ('Father''s Day', '2024-06-16'),
                ('Bakrid/Eid ul-Adha', '2024-06-17'),
                ('June Solstice', '2024-06-21'),
                ('Rath Yatra', '2024-07-07'),
                ('Muharram/Ashura', '2024-07-17'),
                ('Guru Purnima', '2024-07-21'),
                ('Friendship Day', '2024-08-04'),
                ('Independence Day', '2024-08-15'),
                ('Parsi New Year', '2024-08-15'),
                ('Raksha Bandhan (Rakhi)', '2024-08-19'),
                ('Janmashtami', '2024-08-26'),
                ('Janmashtami (Smarta)', '2024-08-26'),
                ('Ganesh Chaturthi/Vinayaka Chaturthi', '2024-09-07'),
                ('Onam', '2024-09-15'),
                ('Milad un-Nabi/Id-e-Milad (Tentative Date)', '2024-09-16'),
                ('September Equinox', '2024-09-22'),
                ('Mahatma Gandhi Jayanti', '2024-10-02'),
                ('First Day of Sharad Navratri', '2024-10-03'),
                ('First Day of Durga Puja Festivities', '2024-10-09'),
                ('Maha Saptami', '2024-10-10'),
                ('Maha Navami', '2024-10-11'),
                ('Maha Ashtami', '2024-10-11'),
                ('Dussehra', '2024-10-12'),
                ('Maharishi Valmiki Jayanti', '2024-10-17'),
                ('Karaka Chaturthi (Karva Chauth)', '2024-10-20'),
                ('Halloween', '2024-10-31'),
                ('Naraka Chaturdasi', '2024-10-31'),
                ('Diwali/Deepavali', '2024-10-31'),
                ('Govardhan Puja', '2024-11-02'),
                ('Bhai Duj', '2024-11-03'),
                ('Chhat Puja (Pratihar Sashthi/Surya Sashthi)', '2024-11-07'),
                ('Guru Nanak Jayanti', '2024-11-15'),
                ('Guru Tegh Bahadur''s Martyrdom Day', '2024-11-24'),
                ('December Solstice', '2024-12-21'),
                ('Christmas Eve', '2024-12-24'),
                ('Christmas', '2024-12-25'),
                ('First Day of Hanukkah', '2024-12-26'),
                ('New Year''s Eve', '2024-12-31')
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
