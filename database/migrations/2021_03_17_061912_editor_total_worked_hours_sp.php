<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $editor_total_worked_personal_assignment = "CREATE PROCEDURE `editor_total_worked_personal_assignment`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
            #Personal Assignment - Item
            IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
                SELECT f.feedback_user_id AS editor_id, COUNT(*) AS `total`,
                DATE_FORMAT(f.`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks_no_group f
                WHERE DATE_FORMAT(f.`created_at`,'%Y%m') >= var_month_from AND
                DATE_FORMAT(f.`created_at`,'%Y%m') <= var_month_to
                GROUP BY feedback_user_id, DATE_FORMAT(f.`created_at`,'%Y%m');
            ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
                SELECT f.feedback_user_id AS editor_id, COUNT(*) AS `total`,
                DATE_FORMAT(f.`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks_no_group f
                WHERE f.feedback_user_id = var_editor_id
                GROUP BY feedback_user_id, DATE_FORMAT(f.`created_at`,'%Y%m');
            ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
                SELECT f.feedback_user_id AS editor_id, COUNT(*) AS `total`,
                DATE_FORMAT(f.`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks_no_group f
                GROUP BY feedback_user_id, DATE_FORMAT(f.`created_at`,'%Y%m');
            ELSE
                SELECT f.feedback_user_id AS editor_id, COUNT(*) AS `total`,
                DATE_FORMAT(f.`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks_no_group f
                WHERE f.feedback_user_id = var_editor_id AND 
                DATE_FORMAT(f.`created_at`,'%Y%m') >= var_month_from AND
                DATE_FORMAT(f.`created_at`,'%Y%m') <= var_month_to
                GROUP BY feedback_user_id, DATE_FORMAT(f.`created_at`,'%Y%m');
            END IF;
                        
        END";

        $editor_total_worked_shop_manuscript = "CREATE PROCEDURE `editor_total_worked_shop_manuscript`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
                
            IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
                SELECT B.feedback_user_id AS editor_id, sum(hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
                FROM shop_manuscript_taken_feedbacks A
                JOIN shop_manuscripts_taken B ON A.shop_manuscript_taken_id = B.id
                WHERE A.hours_worked > 0 AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
                GROUP BY B.feedback_user_id, DATE_FORMAT(A.`created_at`,'%Y%m');
            ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
                SELECT B.feedback_user_id AS editor_id, sum(hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
                FROM shop_manuscript_taken_feedbacks A
                JOIN shop_manuscripts_taken B ON A.shop_manuscript_taken_id = B.id
                WHERE A.hours_worked > 0 AND B.feedback_user_id = var_editor_id
                GROUP BY B.feedback_user_id, DATE_FORMAT(A.`created_at`,'%Y%m');
            ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
                SELECT B.feedback_user_id AS editor_id, sum(hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
                FROM shop_manuscript_taken_feedbacks A
                JOIN shop_manuscripts_taken B ON A.shop_manuscript_taken_id = B.id
                WHERE A.hours_worked > 0
                GROUP BY B.feedback_user_id, DATE_FORMAT(A.`created_at`,'%Y%m');
            ELSE
                SELECT B.feedback_user_id AS editor_id, sum(hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
                FROM shop_manuscript_taken_feedbacks A
                JOIN shop_manuscripts_taken B ON A.shop_manuscript_taken_id = B.id
                WHERE A.hours_worked > 0 AND B.feedback_user_id = var_editor_id AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
                GROUP BY B.feedback_user_id, DATE_FORMAT(A.`created_at`,'%Y%m');
            END IF;
        
        END";

        $editor_total_worked_group_assignment = "CREATE PROCEDURE `editor_total_worked_group_assignment`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
                
            IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
               SELECT f.user_id as editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks f
                WHERE DATE_FORMAT(`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(`created_at`,'%Y%m') <= var_month_to
                GROUP BY f.user_id, DATE_FORMAT(`created_at`,'%Y%m');
            ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
               SELECT f.user_id as editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks f
                WHERE f.user_id = var_editor_id
                GROUP BY f.user_id, DATE_FORMAT(`created_at`,'%Y%m');
            ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
               SELECT f.user_id as editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks f
                GROUP BY f.user_id, DATE_FORMAT(`created_at`,'%Y%m');
            ELSE
               SELECT f.user_id as editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
                FROM assignment_feedbacks f
                WHERE f.user_id = var_editor_id AND DATE_FORMAT(`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(`created_at`,'%Y%m') <= var_month_to
                GROUP BY f.user_id, DATE_FORMAT(`created_at`,'%Y%m');
            END IF;
        
        END";

        $editor_total_worked_coaching = "CREATE PROCEDURE `editor_total_worked_coaching`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
                
           IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
               SELECT editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
               FROM coaching_timer_manuscripts
               WHERE DATE_FORMAT(`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(`created_at`,'%Y%m') <= var_month_to
               GROUP BY editor_id, DATE_FORMAT(`created_at`,'%Y%m');
           ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
               SELECT editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
               FROM coaching_timer_manuscripts
               WHERE editor_id = var_editor_id
               GROUP BY editor_id, DATE_FORMAT(`created_at`,'%Y%m');
           ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
               SELECT editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
               FROM coaching_timer_manuscripts
               GROUP BY editor_id, DATE_FORMAT(`created_at`,'%Y%m');
           ELSE
               SELECT editor_id, COUNT(id) AS total, DATE_FORMAT(`created_at`,'%Y%m') AS `year_month`
               FROM coaching_timer_manuscripts
               WHERE editor_id = var_editor_id AND DATE_FORMAT(`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(`created_at`,'%Y%m') <= var_month_to
               GROUP BY editor_id, DATE_FORMAT(`created_at`,'%Y%m');
           END IF;
        
        END";

        $editor_total_worked_correction = "CREATE PROCEDURE `editor_total_worked_correction`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
                
           IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
               SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN correction_manuscripts B ON A.service_id = B.id
               WHERE A.service_type = 2 AND A.hours_worked > 0
               AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
               SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN correction_manuscripts B ON A.service_id = B.id
               WHERE A.service_type = 2 AND A.hours_worked > 0
               AND B.editor_id = var_editor_id
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
              SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN correction_manuscripts B ON A.service_id = B.id
               WHERE A.service_type = 2 AND A.hours_worked > 0
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSE
              SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN correction_manuscripts B ON A.service_id = B.id
               WHERE A.service_type = 2 AND A.hours_worked > 0
               AND B.editor_id = var_editor_id AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           END IF;
        
        END";

        $editor_total_worked_copy_editing = "CREATE PROCEDURE `editor_total_worked_copy_editing`(
            IN `var_editor_id` INT,
            IN `var_month_from` INT,
            IN `var_month_to` INT
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
                
           IF (isnull(var_editor_id) AND ! ISNULL(var_month_from) AND ! ISNULL (var_month_to)) THEN
               SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN copy_editing_manuscripts B ON A.service_id = B.id
               WHERE service_type = 1 AND A.hours_worked > 0 
               AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSEIF (! isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
              SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN copy_editing_manuscripts B ON A.service_id = B.id
               WHERE service_type = 1 AND A.hours_worked > 0 
               AND B.editor_id = var_editor_id
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSEIF (isnull(var_editor_id) AND ISNULL(var_month_from) AND ISNULL (var_month_to)) THEN
              SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN copy_editing_manuscripts B ON A.service_id = B.id
               WHERE service_type = 1 AND A.hours_worked > 0
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           ELSE
              SELECT B.editor_id, SUM(A.hours_worked) as total, DATE_FORMAT(A.`created_at`,'%Y%m') AS `year_month`
               FROM other_service_feedbacks A
               JOIN copy_editing_manuscripts B ON A.service_id = B.id
               WHERE service_type = 1 AND A.hours_worked > 0
               AND B.editor_id = var_editor_id AND DATE_FORMAT(A.`created_at`,'%Y%m') >= var_month_from AND DATE_FORMAT(A.`created_at`,'%Y%m') <= var_month_to
               GROUP BY B.editor_id, DATE_FORMAT(A.`created_at`,'%Y%m');
           END IF;
        
        END";

        DB::unprepared($editor_total_worked_personal_assignment);
        DB::unprepared($editor_total_worked_shop_manuscript);
        DB::unprepared($editor_total_worked_group_assignment);
        DB::unprepared($editor_total_worked_coaching);
        DB::unprepared($editor_total_worked_correction);
        DB::unprepared($editor_total_worked_copy_editing);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_personal_assignment;');
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_shop_manuscript;');
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_group_assignment;');
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_coaching;');
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_correction;');
        DB::unprepared('DROP PROCEDURE IF EXISTS editor_total_worked_copy_editing;');
    }
};
