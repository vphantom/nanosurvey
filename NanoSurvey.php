<?php

/**
 * Simple Survey Framework
 *
 * Multi-page, branching survey framework that doesn't require JavaScript nor
 * cookies client-side, and also doesn't need sessions or an SQL back-end
 * server-side.
 *
 * At each page, hidden form variables persist answers previously given, until
 * the form is completed where results are saved in a plain CSV file.
 *
 * See the included example pages and the NanoSurvey class' documentation
 * below for usage.
 *
 * PHP version 5
 *
 * @category  Library
 * @package   NanoSurvey
 * @author    Stéphane Lavergne <lis@imars.com>
 * @copyright 2017 Stéphane Lavergne
 * @license   https://opensource.org/licenses/MIT  MIT License
 * @link      https://github.com/vphantom/nanosurvey
 */

/**
 * NanoSurvey class
 *
 * @category  Library
 * @package   NanoSurvey
 * @class     NanoSurvey
 * @author    Stéphane Lavergne <lis@imars.com>
 * @copyright 2017 Stéphane Lavergne
 * @license   https://opensource.org/licenses/MIT  MIT License
 * @link      https://github.com/vphantom/nanosurvey
 */
class NanoSurvey
{
    public $page = 0;
    private $_magic = '';
    private $_filename = '';
    private $_answer = 0;
    private $_savePartial = false;
    private $_skip = false;

    /**
     * Initialize a survey
     *
     * If $savePartial is set true, each time a participant submits a page, a
     * new CSV line is appended with all answers obtained so far.  In this
     * mode, 2 extra columns are prepended: a unique participant ID and a page
     * number.  This makes it easy to keep only the most complete response
     * from each participant, including those who did not reach the end of the
     * survey.
     *
     * If $savePartial is omitted or false, only complete surveys will be
     * saved.
     *
     * @param string    $filename    Path and name for the CSV results file
     * @param bool|null $savePartial Save incomplete rows during progress
     *
     * @return object New Survey instance
     */
    public function __construct($filename, $savePartial = false)
    {
        $this->_filename = $filename;
        $this->_savePartial = $savePartial;

        if (isset($_REQUEST['p']) && is_numeric($_REQUEST['p'])) {
            $this->page = $_REQUEST['p'];
        };

        if (isset($_REQUEST['m']) && is_numeric($_REQUEST['m'])) {
            $this->_answer = $_REQUEST['m'];
        };

        if ($savePartial) {
            if (isset($_REQUEST['x']) && $_REQUEST['x'] != '') {
                $this->_magic = $_REQUEST['x'];
            } else {
                $this->_magic = md5(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
            };
        };
    }

    /**
     * Encode a variable for safe HTML display
     *
     * @param string $in Raw string
     *
     * @return string Sanitized string
     *
     * @access protected
     */
    protected static function escape($in)
    {
        return filter_var($in, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /**
     * Get previous answer, if one was given
     *
     * Answers count from 1, radios share the same ID, other types increment
     * it each time.
     *
     * The safest way to refer to a previous page's answer is to build those
     * pages first and view their HTML source client-side.
     *
     * @param int $id Serial number of the previous answer
     *
     * @return mixed Contents of answer
     */
    public function previousAnswer($id)
    {
        return (isset($_REQUEST['a'][$id]) ? $_REQUEST['a'][$id] : null);
    }

    /**
     * Save current progress in hidden FORM variables
     *
     * @return string HTML with hidden inputs
     */
    public function progressVars()
    {
        $out = '';
        foreach ($_REQUEST['a'] as $key => $val) {
            if ($_REQUEST['a'][$key] != '') {
                $out .= "<input type=\"hidden\" name=\"a[{$key}]\" value=\"".self::escape($_REQUEST['a'][$key])."\">\n";
            };
        };
        return $out;
    }

    /**
     * Build current answer CGI variable name
     *
     * @return string CGI variable name
     *
     * @access private
     */
    private function _answerId()
    {
        return "a[{$this->_answer}]";
    }

    /**
     * Begin a new question
     *
     * Initializes internal data.  Every question MUST start with a call to this
     * function.
     *
     * @param string $type Normal by default, specify 'radio' for a radio group
     *
     * @return null
     */
    public function newQuestion($type = 'normal')
    {
        if ($type == 'radio') {
            $this->_answer++;
        };
    }

    /**
     * End current question
     *
     * Finalizes internal data.  Every question MUST end with a call to this
     * function.
     *
     * @return null
     */
    public function endQuestion()
    {
    }

    /**
     * Radio (one of many) checkbox
     *
     * @param mixed     $value   Internal value to save if box is checked
     * @param bool|null $default Set true to make this the default input (Default: false)
     *
     * @return string HTML checkbox
     */
    public function radioCheckbox($value, $default = false)
    {
        return
            "<input type=\"radio\" name=\""
            . $this->_answerId()
            . "\" value=\""
            . self::escape($value)
            . "\" required"
            . ($default ? " checked" : '')
            . ">"
        ;
    }

    /**
     * Checkbox (multiple possible)
     *
     * @param mixed $value Internal value to save if box is checked
     *
     * @return string HTML checkbox
     */
    public function checkbox($value)
    {
        $this->_answer++;
        $out = "<input type=\"checkbox\" name=\"".$this->_answerId()."\" value=\"".self::escape($value)."\">";
        return $out;
    }

    /**
     * Single line text input box
     *
     * @param string $placeholder Text to display inside box when it is empty
     *
     * @return string HTML input box
     */
    public function textbox($placeholder = "please specify")
    {
        $this->_answer++;
        $out = "<input type=\"text\" name=\"".$this->_answerId()."\" placeholder=\"".self::escape($placeholder)."\">";
        return $out;
    }

    /**
     * Create submit button for next page
     *
     * @param string $label HTML content of button (Default: "Continue")
     *
     * @return string HTML
     */
    public function submitButton($label = "Continue")
    {
        $newPage = $this->page + 1;
        $lastAnswer = max(0, $this->_answer);
        return "<input type=\"hidden\" name=\"m\" value=\"{$lastAnswer}\">\n"
            . "<input type=\"hidden\" name=\"p\" value=\"{$newPage}\">\n"
            . ($this->_savePartial ? "<input type=\"hidden\" name=\"x\" value=\"{$this->_magic}\">\n" : '')
            . "<button type=\"submit\">{$label}</button>";
    }

    /**
     * Display the current/next page
     *
     * Pages are expected to be sequential, from "page-0.inc", "page-1.inc",
     * etc. until the final page which invokes endSurvey().
     *
     * Variable $survey is available in these pages, representing $this
     * instance of NanoSurvey.
     *
     * @return string HTML of the page
     */
    public function page()
    {
        $out = '';
        $out .= "<form method=\"post\">\n";
        $out .= $this->progressVars();

        if ($this->_savePartial) {
            $this->_saveAnswers();
        };

        do {
            $this->_skip = false;
            ob_start();
            $survey = $this;
            include_once 'page-' . $this->page . '.inc';
            $body = ob_get_contents();
            ob_end_clean();
        } while ($this->_skip === true);

        $out .= $body;
        $out .= "</form>\n";

        return $out;
    }

    /**
     * Skip the current page, display next one instead
     *
     * If, in a page, you assess that it should be skipped (i.e. based on
     * prior answers), call this method.
     *
     * @return null
     */
    public function skipPage()
    {
        $this->_skip = true;
        $this->page++;
    }

    /**
     * Save answers to CSV file, terminate survey
     *
     * @return null
     *
     * @access private
     */
    private function _saveAnswers()
    {
        $answers = array();

        if ($this->_savePartial) {
            $answers[] = $this->_magic;
            $answers[] = $this->page;
        };
        for ($i = 1; $i <= $this->_answer; $i++) {
            $answers[] = (isset($_REQUEST['a'][$i]) ? $_REQUEST['a'][$i] : '');
        };

        if (($fp = fopen($this->_filename, 'a')) !== false) {
            fputcsv($fp, $answers);
            fclose($fp);
        } else {
            echo "<p><b>Error:</b> Saving failed!</p>\n";
        };
    }

    /**
     * Terminate survey, saving to CSV if necessary
     *
     * @return null
     */
    public function endSurvey()
    {
        if (!$this->_savePartial) {
            $this->_saveAnswers();
        };
    }
}
