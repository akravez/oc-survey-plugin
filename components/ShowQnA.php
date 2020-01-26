<?php
namespace Akravets\Survey\Components;

use Akravets\Survey\Models\Question;
use Akravets\Survey\Models\Answer;
use Akravets\Survey\Models\Theme;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ShowQnA extends \Cms\Classes\ComponentBase
{
    const UNIQUE_IP = false;
    
    // validator result
    private $valid = true;
    
    // source questions and answers got from DB table
    private $sourceqna;
    
    // questions and answers returned back from user
    private $result;
    
    public function componentDetails()
    {
        return [
            'name' => 'Show Survey',
            'description' => 'Displays a collection of questions and answers.'
        ];
    }
    
    public function defineProperties()
    {
        return [
            'qnaID' => [
                'theme'             => 'ID',
                'description'       => 'ID of survey',
                'default'           => 1,
                'type'              => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/akravets/survey/assets/css/survey.css');
    }
    
    /** Get list of question and variants for answers
     *   for the survey with ID, taken from component property 'qnaID'
     */
    public function qna()
    {
        $id = $this->property('qnaID');
        $active = Theme::where('id', $id)
            ->value('active');
        if (!$active) {
            return [];
        } else {
            return Question::where('theme_id', $id)
                ->where('active', true)
                ->orderBy('sort_order', 'asc')
                ->get()->toArray();
        }
    }
    
    /**
     *  Ajax-handler onSubmit form
     *  Parse answers from request input, insert it into DB table.
     *  Call 'emailing' if necessary.
     *
     *  @return rendered component's partial 'result.htm',
     *      inserted into html element with id="survey-result"
     */
    public function onSubmitHandler()
    {
        $this->page['saved'] = false;
        $this->page['sameip'] = false;
        $this->page['invalid'] = false;
        $id = $this->property('qnaID');
        if (self::UNIQUE_IP &&
            Answer::where('theme_id', $id)->where('ip', $_SERVER['REMOTE_ADDR'])->first()
        ) {
            $this->page['sameip'] = true;
        } else {
            $this->result = $this->parsedResult();
            if (!$this->result && !$this->valid) {
                $this->page['invalid'] = true;
            } else {
                if ($this->saveAnswer()) {
                    // result is successfully stored in the table
                    $this->page['saved'] = true;
                    // should we send email with result
                    $theme = Theme::find($this->property('qnaID'));
                    if ($theme->send_result) {
                        $this->sendResultByEmail($theme->email);
                    }
                }
            }
        }
        return ['#survey-result' => $this->renderPartial('@result')];
    }
    
    /**
     *  Parse answers from request input
     *
     *  @return array of questions and visitor's answers
     */
    private function parsedResult(): array
    {
        $result = [];
        $id = $this->property('qnaID');
        $this->sourceqna = Question::where('theme_id', $id)
            ->where('active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
        foreach (input('q') as $idx => $question) {
            $qna = [];
            $qna['question'] = $question;
            if (!isset(input('a')[$idx])) {
                $qna['answer'] = 'n/a';
            } else {
                switch ($this->sourceqna[$idx]->input_type) {
                    case 'radio':
                        $qna['answer'] = input('a')[$idx];
                        break;
                    case 'checkbox':
                        $qna['answer'] = implode(',', input('a')[$idx]);
                        break;
                    case 'select':
                        $qna['answer'] = input('a')[$idx];
                        break;
                    case 'text':
                        $qna['answer'] = input('a')[$idx];
                        break;
                    default:
                        $qna['answer'] = 'wrong input type';
                        break;
                }
            }
            // check if this q'n'a are correct
            if ($this->validate($idx, $qna)) {
                $result[] = $qna;
            } else {
                // immediate return as questionnaire was changed while user was thinking on answers
                $this->valid = false;
                return [];
            }
        }
        return $result;
    }
    
    /** Save answer
     *
     *  @return bool saving result
     */
    private function saveAnswer(): bool
    {
        $id = $this->property('qnaID');
        $answer = new Answer();
        $answer->theme_id = $id;
        $answer->theme_name = Theme::where('id', $id)->value('title');
        $answer->ip = $_SERVER['REMOTE_ADDR'];
        $answer->answers = $this->result;
        return $answer->save();
    }
    
    /** Send result by email
     *  @param string $list - email addresses list to send mail with results to
     *  @return bool result of email sending
     */
    private function sendResultByEmail($list = null): bool
    {
        if ($list) {
            // send email
            Log::info("'Survey' component is trying to send result to $list");
            $html = $this->prepareMailBody();
            $addresses = explode(',', $list);
            $email = [];
            foreach ($addresses as $address) {
                $address = trim($address);
                $email[$address] = $address;
            }
            $mail = Mail::raw(['html' => $html], function($message) use ($email) {
                $message->to($email);
                $message->subject('User has answered on survey');
            });
            if ($mail) {
                Log::info("'Survey' component has send result to $list successfully");
            } else {
                Log::error("'Survey' component failed to send result to $list");
            }
        } else {
            Log::error("'Survey' component is requested to send result ('send email' is checked), but no email address specified...");
        }
        return true;
    }
    
    /**
     *  Validation of user's answers
     *  It could happens that survey's author has changed Q and/or A 
     *  while user was thinking on answers.
     *  Here we check that Q and A returned from user do correspond the actual ones.
     *  @param int $idx - index of particular question in the questions array
     *  @param array $thatqna - question and user's answer(-s) for this particular question
     *  @return bool validation result
     */
    private function validate($idx, array $thatqna): bool
    {
        if ($this->sourceqna[$idx]->question != $thatqna['question']) {
            return false;
        }
        $validanswers = explode(',', $this->sourceqna[$idx]->answer);
        foreach (explode(',', $thatqna['answer']) as $answer) {
            if ($answer != 'n/a'
                && $this->sourceqna[$idx]->input_type != 'text'
                && !in_array($answer, $validanswers)
            ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     *  Make html code for mail body
     *  @return html code
     */
    private function prepareMailBody()
    {
        $id = $this->property('qnaID');
        $title = Theme::where('id', $id)->value('title');
        $htmlCode = '<html><body>';
        $htmlCode .= "<h2>We've got answers on questionnaire with id=$id</h2>";
        $htmlCode .= "<h3>Title: $title</h3>";
        $htmlCode .= '<table><thead><tr><th>Questions</th><th>Answers</th></tr></thead><tbody>';
        foreach ($this->result as $qna) {
            $htmlCode .= '<tr><td>' . $qna['question'] . '</td><td>' . $qna['answer'] . '</td></tr>';
        }
        $htmlCode .= '</tbody></table></body></html>';
        return $htmlCode;
    }
}
