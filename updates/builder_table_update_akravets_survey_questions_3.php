<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAkravetsSurveyQuestions3 extends Migration
{
    public function up()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->boolean('active')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->dropColumn('active');
        });
    }
}
