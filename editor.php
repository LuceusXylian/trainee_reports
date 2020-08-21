<?php
require_once('config.php');


// POST //
if (isset($_POST["action"])) {
    if($_POST["action"] === "save") {
        if(isset($_POST["report_id"]) && isset($_POST["report_number"]) && isset($_POST["date_from"]) && isset($_POST["date_to"])) {
            if (is_numeric($_POST["report_id"])) {
                $trDB->query("DELETE FROM report WHERE report_id=".$_POST["report_id"]);
            }
            $sql = "INSERT INTO report
                SET report_number = ".$_POST["report_number"]
                .",date_from = '".$_POST["date_from"]."'"
                .",date_to = '".$_POST["date_to"]."'";
            $trDB->query($sql);
            $report_id = $trDB->insert_id;
            
            $report_weekday_count = count($_POST["text"]);
            for ($weekday=0; $weekday < $report_weekday_count; $weekday++) { 
                $sql = "INSERT INTO report_weekday
                    SET report_id = ".$report_id
                    .",weekday_id = ".$weekday;
                $trDB->query($sql);
                $report_weekday_id = $trDB->insert_id;
                
                $report_weekday_row_count = count($_POST["text"][$weekday]);
                for ($row=0; $row < $report_weekday_row_count; $row++) { 
                    if ( isset($_POST["text"][$weekday][$row]) && isset($_POST["hours"][$weekday][$row])
                        && $_POST["text"][$weekday][$row] != "" && is_numeric($_POST["hours"][$weekday][$row])
                    ) {
                        $sql = "INSERT INTO report_weekday_row
                            SET report_weekday_id = ".$report_weekday_id
                            .",text = '".$_POST["text"][$weekday][$row]."'"
                            .",hours = ".$_POST["hours"][$weekday][$row];
                        $trDB->query($sql);
                    }
                }
            }
        }
    } elseif($_POST["action"] === "generate") {
		if(isset($_POST["r_from"]) && isset($_POST["r_to"]) && isset($_POST["topic"])
			&& is_numeric($_POST["r_from"]) && is_numeric($_POST["r_to"])
		) {
			generateTraineeReports(intval($_POST["r_from"]), intval($_POST["r_to"]), $_POST["topic"]);
		}
	}
}


// GET //
$report_number = isset($_GET["r"]) && is_numeric($_GET["r"])? intval($_GET["r"]) : REPORT_NUMBER_STARTWITH;
$max_reports = intval($trDB->query("SELECT IFNULL((SELECT MAX(report_number)+1 FROM report), ".REPORT_NUMBER_STARTWITH.") AS max_num")->fetch_array()[0]);
$select_report = $trDB->query("SELECT report_id, date_from, date_to FROM report WHERE report_number=".$report_number);

if($select_report_result = $select_report->fetch_array()) {
	$report_id = $select_report_result["report_id"];
	$report_date_from = new DateTime($select_report_result["date_from"]);
	$report_date_to = new DateTime($select_report_result["date_to"]);
} else {
	$report_id = "new";
	if($report_number > $max_reports || $report_number < REPORT_NUMBER_STARTWITH) $report_number = REPORT_NUMBER_STARTWITH;
	$diff = $report_number - REPORT_NUMBER_STARTWITH;
	$report_date_from = new DateTime(START_DATE);
	if($diff > 0) $report_date_from->add(new DateInterval('P'.($diff*7).'D'));
	$report_date_to = new DateTime(START_DATE);
	if($diff > 0) $report_date_to->add(new DateInterval('P'.($diff*7).'D'));
	$report_date_to->add(new DateInterval('P5D'));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo PROGRAMM_NAME." Editor"; ?></title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <style>
        body {
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            padding: 0;
            margin: 0;
            background-color: #343a40;
            color: white;
        }
        .table-dark {
            width: 100%;
            margin-bottom: 1rem;
            color: #fff;
            background-color: #343a40;
            border-collapse: collapse;
            box-sizing: border-box;
        }
        .table-dark th, .table-dark td {
            padding: .75rem;
            vertical-align: top;
            border-top: 1px solid #454d55;
        }
        .table-dark .data {
            word-break: break-all;
        }
		.d-ib { display: inline-block; }
		.v-t { vertical-align: top; }
    </style>
</head>

<body>
<div class="p-2" style="background: gray;">
<form action="" method="get" class="mb-2">
	<div>
	<?php
		for ($r_num=REPORT_NUMBER_STARTWITH; $r_num <= $max_reports; $r_num++) { 
			echo '<button type="submit" class="btn m-1" name="r" value="'.$r_num.'">'.$r_num.'</button>';
		}
	?>
	</div>
</form>

<form action="" method="get" class="mb-2">
	<div>
		<label class="d-ib pt-1" style="width: 100px;">Bericht: </label>
		<input type="number" class="form-control d-ib v-t" style="width: 100px;" name="r" min="<?php echo REPORT_NUMBER_STARTWITH; ?>" max="<?php echo $max_reports; ?>" step="1">
		<button type="submit" class="btn d-ib v-t" style="width: 100px;">Senden</button>
	</div>
</form>
</div>


<form action="" method="post">
<?php
function editor_row($weekday, $row, $text, $hours) {
	echo '<div class="row p-2">';
		echo '<div class="col-9 p-0">';
			echo '<input type="text" class="form-control" name="text['.$weekday.']['.$row.']" value="'.$text.'">';
		echo '</div>';
		echo '<div class="col-3 p-0">';
			echo '<input type="number" class="form-control" name="hours['.$weekday.']['.$row.']" value="'.$hours.'" min="0" max="8" step="0.5">';
		echo '</div>';
	echo '</div>';
}

echo '<table class="table table-dark">';
echo '<thead>';
echo '<tr> <th>Bericht #'.$report_number.'</th>';
$tmp = new DateTime(START_DATE);
for ($weekday=0; $weekday < 5; $weekday++) { 
    echo '<th>'.getWeekdayText($weekday) .' <small>'.$tmp->format("d.m.Y").'</small>' .'</th>';
    $tmp->add(new DateInterval('P1D'));
}
echo '</tr>';
echo '</thead>';

echo '<tbody>';
echo '<tr>';
echo '<td colspan="10" style="padding:0;">';
if($report_number > REPORT_NUMBER_STARTWITH) {
	echo '<a href="?r='.($report_number -1).'" style="color: white;">';
	echo '<div style="padding: .75rem; background:#ccc; text-align:center; cursor: pointer; user-select: none;">Zurück</div>';
	echo '</a>';
} else {
	echo '<div style="padding: .75rem; background: gray; text-align: center; user-select: none;">Zurück</div>';
}
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
echo '<input type="hidden" name="report_id" value="'.$report_id.'">';
echo '<input type="hidden" name="report_number" value="'.$report_number.'">';
echo '<input type="hidden" name="date_from" value="'.$report_date_from->format("Y-m-d").'">';
echo '<input type="hidden" name="date_to" value="'.$report_date_to->format("Y-m-d").'">';
echo $report_date_from->format("d.m.Y") .' - ' .$report_date_to->format("d.m.Y")  .'</td>';
for ($weekday=0; $weekday < 5; $weekday++) { 
    echo '<td>';
	$row_count = 0;
	$select_rows = $trDB->query("SELECT text, hours FROM `report_weekday`
		LEFT JOIN report_weekday_row ON report_weekday.report_weekday_id = report_weekday_row.report_weekday_id
		WHERE report_id='".$report_id."'"
		." AND weekday_id=".$weekday
		." LIMIT 5");

	while ($select_rows_result = $select_rows->fetch_assoc()) {
		editor_row($weekday, $row_count, $select_rows_result["text"], $select_rows_result["hours"]);
		$row_count++;
	}

    for ($row=$row_count; $row < 5; $row++) { 
		editor_row($weekday, $row, "", "");
    }

    echo '</td>';
}
echo '</tr>';


echo '<tr>';
echo '<td colspan="10" style="padding:0;">';
if($report_number < $max_reports) {
	echo '<a href="?r='.($report_number +1).'" style="color: white;">';
	echo '<div style="padding: .75rem; background:#ccc; text-align:center; cursor: pointer; user-select: none;">Vorwärts</div>';
	echo '</a>';
} else {
	echo '<div style="padding: .75rem; background: gray; text-align: center; user-select: none;">Vorwärts</div>';
}
echo '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
?>

<button type="submit" class="btn btn-primary col-12 mt-5 mb-5" name="action" value="save">Speichern</button>
</form>


<form action="" method="post" class="p-2" style="display: block; background: gray;">
Erstelle von 
<input type="number" class="form-control d-ib v-t" style="width: 60px;" name="r_from" min="<?php echo $max_reports; ?>" value="<?php echo $max_reports; ?>" step="1" required autocomplete="off">
zu
<input type="number" class="form-control d-ib v-t" style="width: 60px;" name="r_to" min="<?php echo $max_reports; ?>" value="<?php echo $max_reports; ?>" step="1" required autocomplete="off">
mit dem Thema
<input type="text" class="form-control d-ib v-t" style="width: 200px;" name="topic" required>
<button type="submit" class="btn btn-primary" style="width: 100px;" name="action" value="generate">Generieren</button>
</form>
</body>
</html>
<?php $trDB->close(); ?>