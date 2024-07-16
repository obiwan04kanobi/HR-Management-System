<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLeaveApplicationTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the trigger if it exists
        DB::unprepared('DROP TRIGGER IF EXISTS create_leave_application_trigger');

        // Create the trigger
        DB::unprepared('
            CREATE TRIGGER create_leave_application_trigger AFTER UPDATE ON leave_application_masters
            FOR EACH ROW
            BEGIN
                DECLARE days_difference INT;
                DECLARE leave_deduction FLOAT;

                -- Check if the status has changed from 1 to 0
                IF OLD.status = 1 AND NEW.status = 0 THEN
                    -- Calculate the number of days between from_date and to_date
                    SET days_difference = DATEDIFF(NEW.to_date, NEW.from_date) + 1;  -- +1 to include both from_date and to_date

                    -- Determine the leave deduction based on the leave type and session
                    IF NEW.session = "Full day" THEN
                        SET leave_deduction = days_difference * 1;
                    ELSEIF NEW.session = "1st half" OR NEW.session = "2nd half" THEN
                        SET leave_deduction = days_difference * 0.5;
                    ELSEIF NEW.session = "Short leave" THEN
                        SET leave_deduction = days_difference * 0.25;
                    ELSE
                        SET leave_deduction = 0;  -- Default to 0 if session type is not recognized
                    END IF;

                    -- Attempt to update the existing balance
                    UPDATE leave_balances
                    SET balance = balance - leave_deduction
                    WHERE employee_id = NEW.employee_id;

                    -- Check if the employee_id does not exist in leave_balances
                    IF (SELECT COUNT(*) FROM leave_balances WHERE employee_id = NEW.employee_id) = 0 THEN
                        INSERT INTO leave_balances (employee_id, balance)
                        VALUES (NEW.employee_id, 30 - leave_deduction);
                    END IF;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the trigger if it exists
        DB::unprepared('DROP TRIGGER IF EXISTS create_leave_application_trigger');
    }
}
