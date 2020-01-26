<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAkravetsSurveyQuestions extends Migration
{
    public function up()
    {
        Schema::create('akravets_survey_questions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('title_id');
            $table->integer('pos');
            $table->string('question');
            $table->string('answer');
            $table->string('input_type')->default('radio');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('akravets_survey_questions');
    }
}
