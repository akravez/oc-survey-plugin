<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAkravetsSurveyQuestions4 extends Migration
{
    public function up()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->integer('sort_order')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('akravets_survey_questions', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
