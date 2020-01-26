<?php namespace Akravets\Survey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAkravetsSurveyAnswers extends Migration
{
    public function up()
    {
        Schema::create('akravets_survey_answers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('ip', 15);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('theme_id');
            $table->string('theme_name');
            $table->text('answers');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('akravets_survey_answers');
    }
}
