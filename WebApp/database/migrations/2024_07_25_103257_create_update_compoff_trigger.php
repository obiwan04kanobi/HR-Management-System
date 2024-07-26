<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUpdateCompoffTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the trigger if it already exists (optional)
        DB::unprepared('DROP TRIGGER IF EXISTS update_compoff');

        // Create the trigger to update compoff_master after inserting into attendance_masters
        DB::unprepared('
            CREATE TRIGGER update_compoff AFTER INSERT ON attendance_masters
            FOR EACH ROW
            BEGIN
                DECLARE holiday_count INT;

                -- Check if the attendance date matches a holiday date
                SELECT COUNT(*) INTO holiday_count
                FROM holidays_masters
                WHERE holiday_date = NEW.date;

                -- If there is a matching holiday, update/add the record in compoff_master
                IF holiday_count > 0 THEN
                    INSERT INTO compoff_master (employee_id, holiday_id, date, status)
                    SELECT NEW.employee_id, h.holiday_id, NEW.date, -1
                    FROM holidays_masters h
                    WHERE h.holiday_date = NEW.date
                    ON DUPLICATE KEY UPDATE
                        status = -1,
                        updated_at = NOW();
                END IF;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_compoff');
    }
}
