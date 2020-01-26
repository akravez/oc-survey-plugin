<?php namespace Akravets\Survey\Models;

use Model;

/**
 * Model
 */
class Question extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    
    const ANSWERS_DELIMITER = ',';

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'akravets_survey_questions';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'theme_id' => 'required',
        'pos' => 'required|numeric',
        'question' => 'required',
        'input_type' => 'required|in:checkbox,radio,select,text'
    ];

    /**
     * Define inverse of the relation
     */
    public $belongsTo = [
        'theme' => 'Akravets\Survey\Models\Theme'
    ];
    
    /**
     * Get options list for question'n'answer form
     */
    public function getThemeIdOptions()
    {
        $titles = Theme::pluck('title', 'id');
        return $titles;
    }
    
    /**
     * Set the answer field.
     *
     * @param  string  $value
     * @return string
     */
    public function setAnswerAttribute($value)
    {
        $pattern = '/( )*' . self::ANSWERS_DELIMITER . '( )*/';
        $this->attributes['answer'] = preg_replace($pattern, ',', $value);
    }
}
