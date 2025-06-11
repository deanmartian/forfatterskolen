<?php

use Illuminate\Database\Migrations\Migration;

class CreateSpGetEditorWhereHidden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sp = "CREATE PROCEDURE `getIDWhereHidden`(
            IN `editor_expected_finish` DATE
        )
        LANGUAGE SQL
        NOT DETERMINISTIC
        CONTAINS SQL
        SQL SECURITY DEFINER
        COMMENT ''
        BEGIN
        
            SELECT DISTINCT(editor_id) FROM hidden_editors 
            WHERE DATE_FORMAT(editor_expected_finish, '%Y-%m-%d') >= hide_date_from  
            AND DATE_FORMAT(editor_expected_finish, '%Y-%m-%d') <= hide_date_to;
        
        END";
        DB::unprepared($sp);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getIDWhereHidden;');
    }
}
