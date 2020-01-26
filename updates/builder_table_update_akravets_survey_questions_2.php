<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAkravetsSurveyQuestions2 extends Migration
{
    public function up()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->string('answer', 500)->change();
        });
    }
    
    public function down()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->string('answer', 191)->change();
        });
    }
}
