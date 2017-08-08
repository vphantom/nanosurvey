# nanosurvey

[![license](https://img.shields.io/github/license/vphantom/nanosurvey.svg?style=plastic)]() [![GitHub release](https://img.shields.io/github/release/vphantom/nanosurvey.svg?style=plastic)]()

Barebones PHP survey framework with zero client and server side dependencies.

Multi-page, branching survey framework that doesn't require JavaScript nor cookies client-side, and also doesn't need sessions or an SQL back-end server-side.
 
At each page, hidden form variables persist answers previously given, until the form is completed where results are saved sequentially in a plain CSV file.

See the included example pages and the NanoSurvey class' documentation below for usage.


## Installation

You can just include `NanoSurvey.php` directly in your project or use
Composer:

```sh
$ composer require vphantom/nanosurvey
```


## Usage

Instantiate the `NanoSurvey` class, which will process `$_REQUEST`'s `a`, `p` and `x` CGI variables to situate itself.  Invoke `page()` to display the current page inside a `<form>` block.

```php
<?php

// Manual
require_once 'NanoSurvey.php';

// Composer
require_once __DIR__ . '/vendor/autoload.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey</title>
</head>
<body>

<h1>Survey</h1>

<?php

$survey = new NanoSurvey('/tmp/answers.csv');
echo $survey->page();

?>

</body>
</html>
```

<!-- BEGIN DOC-COMMENT H2 NanoSurvey.php -->
## `class NanoSurvey`

NanoSurvey class 


### `public function __construct($filename, $savePartial = false)`

Initialize a survey 

If $savePartial is set true, each time a participant submits a page, a new CSV line is appended with all answers obtained so far.  In this mode, 2 extra columns are prepended: a unique participant ID and a page number.  This makes it easy to keep only the most complete response from each participant, including those who did not reach the end of the survey. 

If $savePartial is omitted or false, only complete surveys will be saved. 


**Parameters:**

* `$filename` — string — Path and name for the CSV results file
* `$savePartial` — bool|null — Save incomplete rows during progress

**Returns:** `object` — New Survey instance

### `public function previousAnswer($id)`

Get previous answer, if one was given 

Answers count from 1, radios share the same ID, other types increment it each time. 

The safest way to refer to a previous page's answer is to build those pages first and view their HTML source client-side. 


**Parameters:**

* `$id` — int — Serial number of the previous answer

**Returns:** `mixed` — Contents of answer

### `public function progressVars()`

Save current progress in hidden FORM variables 


**Returns:** `string` — HTML with hidden inputs

### `public function newQuestion($type = 'normal')`

Begin a new question 

Initializes internal data.  Every question MUST start with a call to this function. 


**Parameters:**

* `$type` — string — Normal by default, specify 'radio' for a radio group

**Returns:** null

### `public function endQuestion()`

End current question 

Finalizes internal data.  Every question MUST end with a call to this function. 


**Returns:** null

### `public function radioCheckbox($value, $default = false)`

Radio (one of many) checkbox 


**Parameters:**

* `$value` — mixed — Internal value to save if box is checked
* `$default` — bool|null — Set true to make this the default input (Default: false)

**Returns:** `string` — HTML checkbox

### `public function checkbox($value)`

Checkbox (multiple possible) 


**Parameters:**

* `$value` — mixed — Internal value to save if box is checked

**Returns:** `string` — HTML checkbox

### `public function textbox($placeholder = "please specify", $size = 0)`

Single line text input box 


**Parameters:**

* `$placeholder` — string|null — Text to display inside box when it is empty
* `$size` — int|null — How many characters wide the input should be? (Default: unspecified)

**Returns:** `string` — HTML input box

### `public function submitButton($label = "Continue")`

Create submit button for next page 


**Parameters:**

* `$label` — string — HTML content of button (Default: "Continue")

**Returns:** `string` — HTML

### `public function page()`

Display the current/next page 

Pages are expected to be sequential, from "page-0.inc", "page-1.inc", etc. until the final page which invokes endSurvey(). 

Variable $survey is available in these pages, representing $this instance of NanoSurvey. 


**Returns:** `string` — HTML of the page

### `public function skipPage()`

Skip the current page, display next one instead 

If, in a page, you assess that it should be skipped (i.e. based on prior answers), call this method. 


**Returns:** null

### `public function endSurvey()`

Terminate survey, saving to CSV if necessary 


**Returns:** null

<!-- END DOC-COMMENT -->

## MIT License

MIT License

Copyright (c) 2017 Stéphane Lavergne https://github.com/vphantom

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
