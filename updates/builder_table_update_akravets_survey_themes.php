<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAkravetsSurveyThemes extends Migration
{
    public function up()
    {
        Schema::table('akravets_survey_themes', function($table)
        {
            $table->text('email')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('akravets_survey_themes', function($table)
        {
            $table->string('email', 191)->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
