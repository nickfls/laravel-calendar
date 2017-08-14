<?php

namespace Makzumi\Calendar;

class Calendar
{
    /**
     * Day Labels, US notation
     *
     * @var array
     */
    private $dayLabels = [
        'Sun',
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat'
    ];

    /**
     * Month Labels
     *
     * @var array
     */
    private $monthLabels = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    /**
     * Days of the Months, per month
     *
     * @var array
     */
    private $daysMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    /**
     * Business days array
     *
     * @var array
     */
    private $week_days = array();

    /**
     * Current Day
     *
     * @var int
     */
    private $day;

    /**
     * Current Month
     *
     * @var int
     */
    private $month;

    /**
     * Current Year
     *
     * @var int
     */
    private $year;

    /**
     * Array of the Event Links
     *
     * @var boolean
     */
    private $eventlinks = FALSE;

    /**
     * Array with the Events
     *
     * @var boolean
     */
    private $events = FALSE;

    /**
     * Starting Hour for the Day Calendar
     *
     * @var integer
     */
    private $start_hour = 8;

    /**
     * Ending Hour for the Hourly Calendar
     *
     * @var integer
     */
    private $end_hour = 20;

    /**
     * Add navigation
     * @var boolean
     */
    private $nav = TRUE;

    /**
     * Initial View
     *
     * @var string
     */
    private $view = 'month';

    /**
     * Resulting HTML
     *
     * @var string
     */
    private $html        = "";

    /**
     * Table class
     *
     * @var string
     */
    private $tableClass  = 'table table-calendar';

    /**
     * Table Header Class
     *
     * @var string
     */
    private $headClass   = '';

    /**
     * Previus Arrow
     *
     * @var string
     */
    private $prevIco     = '<';

    /**
     * Next Arrow
     *
     * @var string
     */
    private $nextIco     = '>';

    /**
     * Class of the Previuos
     *
     * @var string
     */
    private $prevClass   = 'cal_prev';

    /**
     * Class of the Next
     *
     * @var string
     */
    private $nextClass   = 'cal_next';

    /**
     * Base Path
     *
     * @var string
     */
    private $basePath    = "/";

    /**
     * Time class
     * @var string
     */
    private $timeClass   = 'ctime';

    /**
     * Template to wrap around calendar Day
     *
     * @var array
     */
    private $dayWrap     = array('<div class="cal_day">', '</div>');

    /**
     * Template to wrap around Day
     *
     * @var array
     */
    private $dateWrap    = array('<div class="date">', '</div>');

    /**
     * Calendar LAbels class
     *
     * @var string
     */
    private $labelsClass = 'cal_labels';

    /*
    Event wrapper
     */
    private $eventWrap   = array('<p>', '</p>');

    /**
     * Current Date
     *
     * @var String
     */
    private $today;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->day   = date('d');
        $this->month = date('n');
        $this->year  = date('Y');
        $this->today = date('Y-m-d');
    }

    /**
     * Make the Calendar
     *
     * @return Calendar
     */
    public function make()
    {
        return new static();
    }

    /**
     * Generate the Calendar using all the settings
     *
     * @return String
     */
    public function generate()
    {
        # Build teh Header
        $this->buildHeader();

        # Switch the Views
        switch ($this->view){
            case 'day' :
                $this->buildBodyDay();
                break;
            case 'week' :
                $this->buildBodyWeek();
                break;
            default :
                $this->buildBody();
                break;
        }
        # return thr string
        return $this->html;
    }

    /**
     * Display the Navigation
     *
     * @param  boolean $show
     * @return $this
     */
    public function showNav($show)
    {
        $this->nav = $show;

        return $this;
    }

    /**
     * Set the View
     *
     * @param string $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * What class to use in CSS
     *
     * @param string $class
     * @return $this
     */
    public function setTimeClass($class)
    {
        $this->timeClass = $class;

        return $this;
    }

    /**
     * What are the start-end hours (for the Day view )
     * @param int $s
     * @param int $e
     * @return $this
     */
    public function setStartEndHours($s, $e)
    {
        $this->start_hour = $s;
        $this->end_hour = $e;

        return $this;
    }

    /**
     * Decide for when we are displyaing the calendar?
     *
     * @param mixed|string $date
     * @return $this
     */
    public function setDate($date = FALSE)
    {
        $date  = explode('-', $date);

        $day   = @$date[2] ? : date('d');
        $month = @$date[1] ? : date('m');
        $year  = @$date[0] ? : date('Y');

        $this->day   = @$day;
        $this->month = @$month;
        $this->year  = @$year;

        return $this;
    }

    /**
     * WHatever is the Base Path for links
     *
     * @param string $path
     * @return $this
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * Set the Day labels
     *
     * @param array $array
     * @return $this
     */
    public function setDayLabels($array)
    {
        # Error check
        if (count($array) != 7){
            return;
        }
        # set
        $this->dayLabels = $array;

        return $this;
    }

    /**
     * Set the Month Labels
     *
     * @param Array $array
     */
    public function setMonthLabels($array)
    {
        # Error check
        if (count($array) != 12)
        {
            return;
        }

        $this->monthLabels = $array;
        return $this;
    }

    /**
     * Push the Events
     *
     * @param Array $events
     */
    public function setEvents($events)
    {
        if (!is_array($events))
        {
            return;
        }
        $this->events = $events;
        return $this;
    }

    /**
     * Set the Wrapper for the Event
     *
     * @param Array $wrap
     */
    public function setEventsWrap($wrap)
    {
        $this->eventWrap = $wrap;
        return $this;
    }

    /**
     * Set the Day wrapper
     *
     * @param Array $wrap
     */
    public function setDayWrap($wrap)
    {
        $this->dayWrap = $wrap;
        return $this;
    }

    /**
     * Set the Next Icon
     *
     * @param string $html
     */
    public function setNextIcon($html) {
        $this->nextIco = $html;
        return $this;
    }

    /**
     * Set the Previous Icon
     *
     * @param string $html
     */
    public function setPrevIcon($html)
    {
        $this->prevIco = $html;
        return $this;
    }

    /**
     * Set the Date Wrapper
     *
     * @param Array $wrap
     */
    public function setDateWrap($wrap)
    {
        $this->dateWrap = $wrap;
        return $this;
    }

    /**
     * Set the Table class
     *
     * @param string $class
     */
    public function setTableClass($class)
    {
        $this->tableClass = $class;
        return $this;
    }

    /**
     * Set the Header Class
     *
     * @param string $class
     */
    public function setHeadClass($class)
    {
        $this->headClass = $class;
        return $this;
    }

    /**
     * Set Next Button Class
     * @param string $clas
     */
    public function setNextClass($class) {
        $this->nextClass = $class;
        return $this;
    }

    /**
     * Set Previous Button Class
     * @param string $clas
     */
    public function setPrevClass($class)
    {
        $this->prevClass = $class;
        return $this;
    }

    /**
     * Set the Links
     *
     * @param array $urls
     */
    public function setLink($urls)
    {
        if (!is_array($urls))
        {
            return;
        }
        $this->eventlinks = $urls;
        return $this;
    }

    /**
     * Set the class for Labels
     * @param string $class
     */
    public function setLabelsClass($class)
    {
        $this->labelsClass = $class;
        return $this;
    }

    /**
     * Build the Calendar Header
     *
     * @return string
     */
    private function buildHeader()
    {
        $month_name = $this->monthLabels[$this->month - 1] . ' ' . $this->year;
        $vclass = strtolower($this->view);
        $h = "<table class='" . $this->tableClass . " " . $vclass . "'>";
        $h .= "<thead>";
        $h .= "<tr class='" . $this->headClass . "'>";
        $cs = 5;
        if ($this->view == 'week' || $this->view == 'day')
            $h .= "<th>&nbsp;</th>";
        if ($this->view == 'day')
            $cs = 1;

        if ($this->nav) {
            $h .= "<th>";
            $h .= "<a class='" . $this->prevClass . "' href='" . $this->prevLink() . "'>" . $this->prevIco . "</a>";
            $h .= "</th>";
            $h .= "<th colspan='$cs'>";
            $h .= $month_name;
            $h .= "</th>";
            $h .= "<th>";
            $h .= "<a class='" . $this->nextClass . "' href='" . $this->nextLink() . "'>" . $this->nextIco . "</a>";
            $h .= "</th>";
        } else {
            $h .= "<th colspan='7'>";
            $h .= $month_name;
            $h .= "</th>";
        }
        $h .= "</tr>";
        $h .= "</thead>";

        $h .= "<tbody>";
        if ($this->view != 'day' && $this->view != 'week') {
            $h .= "<tr class='" . $this->labelsClass . "'>";

            for ($i = 0; $i <= 6; $i++) {
                $h .= "<td>";
                $h .= $this->dayLabels[$i];
                $h .= "</td>";
            }

            $h .= "</tr>";
        }
        if ($this->view == 'day' || $this->view == 'week')
            $h .= self::getWeekDays();

        $this->html .= $h;
    }

    /**
     * Build the row with weekDays
     * @return String
     */
    private function getWeekDays()
    {
        $time = date('Y-m-d', strtotime($this->year . '-' . $this->month . '-' . $this->day));
        if ($this->view == 'week') {
            $sunday = strtotime('last sunday', strtotime($time . ' +1day'));
            $day = date('j', $sunday);
            $startingDay = date('N', $sunday);
            $cnt = 6;
        }
        if ($this->view == 'day') {
            $day = $this->day;
            $cnt = 0;
        }

        $this->week_days = array();
        $mlen = $this->daysMonth[intval($this->month) - 1];
        if ($this->month == 2 && ((($this->year % 4) == 0) && ((($this->year % 100) != 0) || (($this->year % 400) == 0)))) {
            $mlen = $mlen + 1;
        }
        $h = "<tr class='" . $this->labelsClass . "'>";
        $h .= "<td>&nbsp;</td>";
        for ($j = 0; $j <= $cnt; $j++) {
            $cs = $cnt == 0 ? 3 : 1;
            $h .= "<td colspan='$cs'>";
            if ($this->view == 'day')
                $getDayNumber = date('w', strtotime($time));
            else
                $getDayNumber = $j;
            if ($day <= $mlen) {

            } else {
                $day = 1;
            }
            $h .= $this->dayLabels[$getDayNumber] . ' ';
            $h .= intval($day);
            $this->week_days[] = $day;
            $day++;
            $h .= "</td>";
        }

        $h .= "</tr>";
        return $h;
    }

    /**
     * Complete the build up to the body
     *
     * @return String
     */
    private function buildBody()
    {
        $day = 1;
        $now_date = $this->year . '-' . $this->month . '-01';
        $startingDay = date('N', strtotime('first day of this month', strtotime($now_date)));
        //Add the following line if you want to start the week with monday instead of sunday. Or change the number to suit your needs.
        //$startingDay = $startingDay - 1;
        $monthLength = $this->daysMonth[$this->month - 1];
        if ($this->month == 2 && ((($this->year % 4) == 0) && ((($this->year % 100) != 0) || (($this->year % 400) == 0)))) {
            $monthLength = $monthLength + 1;
        }
                    $h = "<tr>";
        for ($i = $startingDay == 7 ? 1 : 0; $i < 9; $i++) {
            for ($j = 0; $j <= 6; $j++) {
                $currDate = $this->getDayDate($day);
                $class = $this->getTdClass($day);
                $h .= "<td data-datetime='$currDate' $class>";
                $h .= $this->dateWrap[0];
                if ($day <= $monthLength && ($i > 0 || $j >= $startingDay)) {
                    $h .= $this->dayWrap[0];
                    $h .= $this->getEventSearchLink( $day );
                    $h .= $this->dayWrap[1];
                    $h .= $this->buildEvents($currDate);
                    $day++;
                } else {
                    $h .= "&nbsp;";
                }
                $h .= $this->dateWrap[1];
                $h .= "</td>";
            }
            // stop making rows if we've run out of days
            if ($day > $monthLength) {
                break;
            } else {
                $h .= "</tr>";
                $h .= "<tr>";
            }
        }
        $h .= "</tr>";
        $h .= "</tbody>";
        $h .= "</table>";
        $this->html .= $h;
    }

    /**
     * Build the View for a Day
     *
     * @return String
     */
    private function buildBodyDay()
    {

        $events = $this->events;
        $h = "";
        for ($i = $this->start_hour; $i < $this->end_hour; $i++) {
            for ($t = 0; $t < 2; $t++) {
                $h .= "<tr>";
                $min = $t == 0 ? ":00" : ":30";
                $h .= "<td class='$this->timeClass'>" . date('g:ia', strtotime($i . $min)) . "</td>";
                for ($k = 0; $k < 1; $k++) {
                    $wd = $this->week_days[$k];
                    $time_r = $this->year . '-' . $this->month . '-' . $wd . ' ' . $i . ':00:00';
                    $min = $t == 0 ? '' : '+30 minute';
                    $time_1 = strtotime($time_r . $min);
                    $time_2 = strtotime(date('Y-m-d H:i:s', $time_1) . '+30 minute');
                    $dt = date('Y-m-d H:i:s', $time_1);
                    $h .= "<td colspan='3' data-datetime='$dt'>";
                    $h .= $this->dateWrap[0];

                    $hasEvent = FALSE;
                    foreach ($events as $key=>$event) {
                        //EVENT TIME AND DATE
                        $time_e = strtotime($key);
                        if ($time_e >= $time_1 && $time_e < $time_2) {
                            $hasEvent = TRUE;
                            $h .= $this->buildEvents(FALSE, $event);
                        }
                    }
                    $h .= !$hasEvent ? '&nbsp;' : '';
                    $h .= $this->dateWrap[1];
                    $h .= "</td>";
                }
                $h .= "</tr>";
            }
        }
        $h .= "</tbody>";
        $h .= "</table>";

        $this->html .= $h;
    }

    /**
     * Complete Week vuiew
     * @return String
     */
    private function buildBodyWeek()
    {

        $events = $this->events;
        $h = "";
        for ($i = $this->start_hour; $i < $this->end_hour; $i++) {
            for ($t = 0; $t < 2; $t++) {
                $h .= "<tr>";
                $min = $t == 0 ? ":00" : ":30";
                $h .= "<td class='$this->timeClass'>" . date('g:ia', strtotime($i . $min)) . "</td>";

                for ($k = 0; $k < count($this->week_days); $k++) {

                    $wd = $this->week_days[$k];
                    $time_r = $this->year . '-' . $this->month . '-' . $wd . ' ' . $i . ':00:00';
                    //we also need next month string
                    $time_r_next_month = $this->year . '-' . (string)($this->month + 1) . '-' . $wd . ' ' . $i . ':00:00';
                    $min = $t == 0 ? '' : '+30 minute';
                    $time_1 = strtotime($time_r . $min);
                    $time_2 = strtotime(date('Y-m-d H:i:s', $time_1) . '+30 minute');
                    //events need additional checking, if they are in same week but next month they will not show up
                    //so we need somt additional time rules to check
                    $time_3 = strtotime($time_r_next_month . $min);
                    $time_4 = strtotime(date('Y-m-d H:i:s', $time_3) . '+60 minute');
                    $dt = date('Y-m-d H:i:s', $time_1);
                    $h .= "<td data-datetime='$dt'>";
                    $h .= $this->dateWrap[0];

                    $hasEvent = FALSE;
                    foreach ($events as $key=>$event) {
                        //EVENT TIME AND DATE
                        $time_e = strtotime($key);
                        //and the additional check should be done in the below conditional
                        if (($time_e >= $time_1 && $time_e < $time_2) || ($time_e >= $time_3 && $time_e < $time_4)) {
                            $hasEvent = TRUE;
                            $h .= $this->buildEvents(FALSE, $event);
                        }
                    }
                    $h .= !$hasEvent ? '&nbsp;' : '';
                    $h .= $this->dateWrap[1];
                    $h .= "</td>";
                }
                $h .= "</tr>";
            }
        }
        $h .= "</tbody>";
        $h .= "</table>";

        $this->html .= $h;
    }

    /**
     * Build the Events Array
     *
     * @param  int $date
     * @param  boolean $event
     * @return String
     */
    private function buildEvents($date, $event = FALSE)
    {
        if (!$this->events)
            return "";
        $events = $this->events;
        $h = "";
        //IF DAY CALC MINS
        $date = date('Y-m-d', strtotime($date));
        if ($event) {
            return $this->processEvent($event);
        }
        foreach ($events as $key=>$event) {
            $edate = date('Y-m-d', strtotime($key));
            if (is_array($event)) {
                if ($date == $edate) {
                    $h .= $this->processEvent($event);
                }
            } else {
                if ($date == $key) {
                    $h .= $this->eventWrap[0];
                    $h .= $event;
                    $h .= $this->eventWrap[1];
                }
            }
        }
        return $h;
    }

    /**
     * Process Single Event
     *
     * @param  Array $event
     * @return String
     */
    private function processEvent($event) {
        $h = "";
        foreach ($event as $e) {
            $h .= $this->eventWrap[0];
            $h .= $e;
            $h .= $this->eventWrap[1];
        }
        return $h;
    }

    /**
     * Build the Previous Link
     *
     * @return String
     */
    private function prevLink()
    {
        $y = $this->year;
        $d = intval($this->day);
        $d = $d < 10 ? '0' . $d : $d;
        $m = intval($this->month);
        $m = $m < 10 ? '0' . $m : $m;

        $time = $y . '-' . $m . '-' . $d;

        if ($this->view == "week") {
            $time = strtotime('last sunday', strtotime($time . ' +1 day'));
            $time = date('Y-m-d', $time);
            $time = date('Y-m-d', strtotime($time . ' -1 week'));
        } else if ($this->view == "day") {
            $time = date('Y-m-d', strtotime($time . '-1day'));
        } else {
            $time = date('Y-m', strtotime($y . '-' . $m . '-01 -1month'));
        }
        $url = $this->basePath . '?cdate=' . $time;
        return $url . $this->getOldGET();
    }

    /**
     * Byuild the Next Link
     *
     * @return String
     */
    private function nextLink()
    {
        $y = $this->year;
        $d = intval($this->day);
        $d = $d < 10 ? '0' . $d : $d;
        $m = intval($this->month);
        $m = $m < 10 ? '0' . $m : $m;

        $time = $y . '-' . $m . '-' . $d;

        if ($this->view == "week") {
            $time = strtotime('next sunday', strtotime($time . ' -1 day'));
            $time = date('Y-m-d', $time);
            $time = date('Y-m-d', strtotime($time . '+1week'));
        } else if ($this->view == "day") {
            $time = date('Y-m-d', strtotime($time . '+1day'));
        } else {
            $time = date('Y-m', strtotime($y . '-' . $m . '-01 +1month'));
        }

        $url = $this->basePath . '?cdate=' . $time;
        return $url . $this->getOldGET();
    }

    /**
     * Rebuild the Date from the signle digit
     *
     * @param  int $day
     * @return String
     */
    private function getDayDate($day)
    {
        $day  = intval($day);
        $y    = $this->year;
        $m    = intval($this->month);
        $m    = $m < 10 ? '0' . $m : $m;
        $d    = intval($day);
        $d    = $d < 10 ? '0' . $d : $d;
        $date = $y . '-' . $m . '-' . $d;
        return $date;
    }

    /**
     * Load Old GET' variables
     *
     * @return string
     */
    private function getOldGET()
    {
        $get  = $_GET;
        $vars = '';
        foreach ($get as $key=>$value)
        {
            if ($key != 'cdate')
            {
                $vars .= $this->compileOldGET($key, $value);
            }
        }
        return $vars;
    }

    /**
     * Stick old values together
     *
     * @return string
     */
    private function compileOldGET($key, $value, $prepend='', $append='')
    {
        if ( is_array($value) ) {
            # init string
            $string = '';
            # appen
            foreach ($value as $item) {
                $string .= $this->compileOldGET( $key, $item,'[',']');
            }
            # return
            return $string;
        }
        # append return
        return '&' . $key . '=' . $prepend.$value.$append;
    }

    /**
     * Get TD class
     * @param  int $date
     * @param  string $append
     * @return string
     */
    private function getTdClass($date, $append = '' )
    {
        # get classes
        $class = array(
            $this->getTodayClass($date),
            $this->getEventsClass($date),
            $append,
        );

        # split by space
        $class = trim(implode(' ', $class));

        # populate the class
        return "class='{$class}'";
    }

    /**
     * Return string class name if the date matches to today
     *
     * @param  int $date
     * @param  string $className
     * @param  mixed $default
     * @return string|Null
     */
    private function getTodayClass($date, $className = 'today', $default = null)
    {
        return  $this->getDayDate($date) == $this->today ? $className : $default;
    }

    /**
     * Return string class name if the date has an Event
     *
     * @param  int $date
     * @param  string $className
     * @param  mixed $default
     * @return string|Null
     */
    private function getEventsClass($date, $className = 'event', $default = null)
    {
        return  $this->hasEventsOnADate($date) ? $className : $default;
    }

    /**
     * True if the Event Array is not empty
     *
     * @return boolean
     */
    private function hasEventLinks()
    {
        return !empty( $this->eventlinks );
    }

    /**
     * True if the is an Event of the given date (date is given as a # of the day in a current month)
     *
     * @param  int $date
     * @return boolean
     */
    private function getEventsOnADate( $date )
    {
        # Load EventLinks
        $eventlinks = $this->eventlinks;

        # Convert Date to currently displayed month
        $dayDate = $this->getDayDate($date);

        # get all events on a day
        $events =  array_first($eventlinks, function($eventDate, $url) use ($dayDate) {
            return $eventDate == $dayDate;
        }, false);

        return $events ;
    }

    /**
     * True if there are events on a given date, checked by the day number of the current month
     *
     * @param  int $date
     * @return boolean
     */
    private function hasEventsOnADate($date)
    {
        # Get the events
        $events = $this->getEventsOnADate($date);

        # check
        return !empty($events);
    }

    /**
     * Return string for the date or a URL if there is a matching Link
     *
     * @param  int $date
     * @return string
     */
    private function getEventSearchLink($date)
    {
        $events = $this->getEventsOnADate( $date );

        return  empty($events) ? $date : link_to( head($events), $date);
    }
}
