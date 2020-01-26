<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAkravetsSurveyQuestions extends Migration
{
    public function up()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->renameColumn('title_id', 'theme_id');
        });
    }
    
    public function down()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->renameColumn('theme_id', 'title_id');
        });
    }
}
