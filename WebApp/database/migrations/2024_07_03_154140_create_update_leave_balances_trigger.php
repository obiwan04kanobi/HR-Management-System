<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUpdateLeaveBalancesTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the trigger if it already exists (optional)
        DB::unprepared('DROP TRIGGER IF EXISTS update_leave_balances');

        // Create the trigger to update leave_balances after inserting into attendance_masters
        DB::unprepared('
            CREATE TRIGGER update_leave_balances AFTER INSERT ON attendance_masters
            FOR EACH ROW
            BEGIN
                DECLARE present_date DATE;
                SET present_date = CURDATE();  -- Get the current date
                
                INSERT INTO leave_balances (employee_id, balance)
                VALUES (NEW.employee_id,
                        30 - IFNULL(
                            (
                                SELECT 
                                    SUM(CASE 
                                        WHEN punch_in <= \'09:30:00\' THEN 0
                                        WHEN punch_in <= \'10:30:00\' THEN 0.25
                                        WHEN punch_in <= \'13:00:00\' THEN 0.5
                                        WHEN punch_in > \'14:00:00\' THEN 1
                                        ELSE 
                                            CASE 
                                                WHEN punch_in <= \'09:30:00\' AND punch_out >= \'18:30:00\' THEN 0
                                                WHEN punch_in > \'09:30:00\' AND punch_in <= \'11:30:00\' AND punch_out >= \'18:30:00\' THEN 0.5
                                                WHEN punch_in <= \'09:31:00\' AND punch_out >= \'16:30:00\' AND punch_out < \'18:30:00\' THEN 0.25
                                                WHEN TIME_TO_SEC(TIMEDIFF(punch_out, punch_in)) / 3600 >= 7 THEN 0.5
                                                WHEN TIME_TO_SEC(TIMEDIFF(punch_out, punch_in)) / 3600 >= 9 THEN 0
                                                WHEN punch_in <= \'09:31:00\' AND punch_out >= \'14:00:00\' AND punch_out <= \'18:30:00\' THEN 1
                                                WHEN punch_in > \'09:30:00\' AND punch_in <= \'14:00:00\' AND punch_out >= \'18:30:00\' THEN 1
                                                ELSE 1
                                            END
                                    END)
                                FROM attendance_masters
                                WHERE employee_id = NEW.employee_id
                                AND date >= DATE_SUB(present_date, INTERVAL 30 DAY)  -- Last 30 days from current date
                                AND date <= present_date  -- Current date as the end date
                            ),
                            0 -- Default deduction for absence
                        )
                )
                ON DUPLICATE KEY UPDATE
                    balance = VALUES(balance);  -- Update the balance to the new calculated value
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the trigger when rolling back migration
        DB::unprepared('DROP TRIGGER IF EXISTS update_leave_balances');
    }
}
