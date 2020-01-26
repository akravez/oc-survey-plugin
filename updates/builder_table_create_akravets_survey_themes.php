<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAkravetsSurveyThemes extends Migration
{
    public function up()
    {
        Schema::create('akravets_survey_themes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->boolean('active')->default(0);
            $table->boolean('send_result')->default(0);
            $table->string('email')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('akravets_survey_themes');
    }
}
