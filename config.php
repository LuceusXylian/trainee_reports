<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

define("PROGRAMM_NAME", 'Trainee Reports');
define("TESTMODE", false);
define("GENERATOR_PATH", dirname(__FILE__));
define("FILE_AUTHOR", 'FILE_AUTHOR');
define("START_DATE", '2020-08-10');
define("REPORT_NUMBER_STARTWITH", 1);


require_once('secrets.php');
$trDB = new mysqli($server,$user,$password,$database);
if($trDB->connect_errno){
	echo "The connection to '$database' database failed!<br/>"
		."Error " .$trDB->connect_errno .". " .$trDB->connect_error;
	exit();
}
$trDB->set_charset("utf8");


function dump($var, $name) {
    echo '<div>'.$name.': ';
    var_dump($var);
    echo '</div>';
}

function getWeekdayText($id) {
    switch ($id) {
        case 0: return "Montag";
        case 1: return "Dienstag";
        case 2: return "Mittwoch";
        case 3: return "Donnerstag";
        case 4: return "Freitag";
        case 5: return "Samstag";
        case 6: return "Sonntag";
        
        default: return "";
    }
}

function getWeekdayTextShort($id) {
    switch ($id) {
        case 0: return "Mo";
        case 1: return "Di";
        case 2: return "Mi";
        case 3: return "Do";
        case 4: return "Fr";
        case 5: return "Sa";
        case 6: return "So";
        
        default: return "";
    }
}

$topics_array = array(
    array("Fehlerbehebung", "E-Mails beantworten")
    ,array("Optimierungen", "Design verbessert")
    ,array("Benutzeroberfläche verbessert")
);

function generateTraineeReports($from, $to, $topic) {
    global $trDB;
    global $topics_array;
    $hasnoTopic = trim($topic) === "";

    for ($r=$from; $r <= $to; $r++) {
        $diff = $r - REPORT_NUMBER_STARTWITH;
        $report_date_from = new DateTime(START_DATE);
        if($diff > 0) $report_date_from->add(new DateInterval('P'.($diff*7).'D'));
        $report_date_to = new DateTime(START_DATE);
        if($diff > 0) $report_date_to->add(new DateInterval('P'.($diff*7).'D'));
        $report_date_to->add(new DateInterval('P5D'));

        $sql = "INSERT INTO report
            SET report_number = ".$r
            .",date_from = '".$report_date_from->format("Y-m-d")."'"
            .",date_to = '".$report_date_to->format("Y-m-d")."'";
        $trDB->query($sql);
        $report_id = $trDB->insert_id;
        
        for ($weekday=0; $weekday < 5; $weekday++) { 
            $sql = "INSERT INTO report_weekday
                SET report_id = ".$report_id
                .",weekday_id = ".$weekday;
            $trDB->query($sql);
            $report_weekday_id = $trDB->insert_id;
            
            $report_weekday_row_count = random_int(2,3);
            $hours_count = 0;
            for ($row=0; $row < $report_weekday_row_count; $row++) { 
                if ($row === 0) {
                    $s_row = 0;
                    $hours = 1;
                } elseif ($row === 1 && $report_weekday_row_count >= 3) {
                    $s_row = 1;
                    $hours = random_int(1,3);
                } else {
                    $s_row = 2;
                    $hours = 8 - $hours_count;
                }
                
                if ($hasnoTopic) {
                    $text = $topics_array[$s_row][random_int(0, count($topics_array[$s_row]))];
                } else {
                    $text = $topic .": " .$topics_array[$s_row][random_int(0, count($topics_array[$s_row]) -1)];
                }
                

                $sql = "INSERT INTO report_weekday_row
                    SET report_weekday_id = ".$report_weekday_id
                    .",text = '".$text."'"
                    .",hours = ".$hours;
                $trDB->query($sql);

                $hours_count += $hours;
            }
        }
    }
}
?>
