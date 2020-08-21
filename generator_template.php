<?php
require_once('config.php');
$total_hours = 0;

$html = '<page style="font-family: Helvetica, sans-serif;">
<style>
th { text-align: left; }
td { text-align: middle; }
</style>

<table width="100%" cellspacing="0" style="font-size: 14pt;">
    <tr>
        <td>
            <b>Ausbildungsnachweis Nr.: </b> <u>'.$report_number.'</u> 
        </td>
        <td style="text-align: right;">
            Woche vom <u>'.$date_from->format("d.m.").'</u> bis <u>'.$date_to->format("d.m.Y").'</u>
        </td>
    </tr>
</table>
<h6></h6>';

$html .= '<table width="100%" cellspacing="0" cellpadding="4">
        <tr style="border: 2px solid #000000;">
            <th width="30" style="border-left: 2px solid #000000; border-top: 2px solid #000000;"></th>
            <th width="560" style="font-size: 15px; font-weight: bold; border-top: 2px solid #000000;">Ausgeführte Arbeiten, Unterweisungen, Berufsschulunterricht usw.</th>
            <th width="48" style="border-left: 2px solid #000000; border-right: 2px solid #000000; border-top: 2px solid #000000; font-weight: normal; font-size: 10px;">Gesamt-<br>stunden</th>
        </tr>';


        for ($weekday=0; $weekday < 5; $weekday++) {
            $bool = true;
            $style_border_top = "border-top: 2px solid #000000;";

            $row_count = 0;
            $select_rows = $trDB->query("SELECT text, hours FROM `report_weekday`
                LEFT JOIN report_weekday_row ON report_weekday.report_weekday_id = report_weekday_row.report_weekday_id
                WHERE report_id='".$report_id."'"
                ." AND weekday_id=".$weekday
                ." LIMIT 5");

            $row_text_array = array();
            $row_hours_array = array();
            while ($select_rows_result = $select_rows->fetch_assoc()) {
                $row_text_array[] = $select_rows_result["text"];
                $row_hours_array[] = $select_rows_result["hours"];
                $row_count++;
            }

            for ($i=0; $i < 5; $i++) { 
                $html .= '<tr>';
                    
                if($bool) {
                    $html .= '<th rowspan="5" width="30" style="vertical-align: top; border-left: 2px solid #000000; border-top: 2px solid #000000; font-size: 15px; font-weight: bold;"><div style="line-height: 90px;">'.(getWeekdayTextShort($weekday)).'</div></th>';
                    $bool = false;
                }

                if ($i < $row_count) {
                    $text = $row_text_array[$i];
                    $hours = $row_hours_array[$i];
                    $total_hours += $hours;

                    if ($hours % 1 === 0.5) {
                        $hours = number_format($hours, 1, ",", ".");
                    } else {
                        $hours = number_format($hours, 0, ",", ".");
                    }
                } else {
                    $text = "";
                    $hours = "";
                }
                
                $html .= '<td width="560" style="border-left: 2px solid #000000; border-top: 1px solid #000000; font-size: 14px; padding: 4px;'.$style_border_top.'">'.$text.'</td>';
                $html .= '<td width="48" style="border-top: 1px solid #000000;; border-left: 2px solid #000000; border-right: 2px solid #000000; font-size: 14px; text-align:center; padding: 2px;'.$style_border_top.'">'.$hours.'</td>';
                $html .= '</tr>';
                $style_border_top = "";
            }
            
        }


$html .= ' <tr>
        <td colspan="2" style="border-top: 2px solid #000000; font-size: 15px; text-align: right;">Wochenstunden</td>
        <td width="48" style="border: 2px solid #000000; font-size: 14px; text-align: center;">'.$total_hours.'</td>
    </tr>';

$html .= '</table>';



$html .= '
    <h4 style="margin-right: 0; margin-bottom: 0;">Für die Richtigkeit</h4>
    <table width="100%" cellspacing="0" style="border: 2px solid #000000; font-size: 12px;">
        <tr valign="middle">
            <td style="border-bottom: 1px solid #000;" width="33.33%" height="42">
            </td>
            
            <td style="border-bottom: 1px solid #000; border-left: 2px solid #000000; border-right: 2px solid #000000;" width="33.33%">
            </td>
            
            <td style="border-bottom: 1px solid #000;" width="33.33%">
            </td>
        </tr>

        <tr valign="top">
            <td>
                <table width="100%" cellspacing="0">
                    <tr valign="top">
                        <td style="width: 60px;">Datum</td>
                        <td>Unterschrift des<br>Auszubildenden</td>
                    </tr>
                </table>
            </td>
            
            <td style="border-left: 2px solid #000000; border-right: 2px solid #000000;">
                <table width="100%" cellspacing="0">
                    <tr valign="top">
                        <td style="width: 60px;">Datum</td>
                        <td>Gesetzliche Vertretung des Auszubildenden</td>
                    </tr>
                </table>
            </td>
            
            <td>
                <table width="100%" cellspacing="0">
                    <tr valign="top">
                        <td style="width: 60px;">Datum</td>
                        <td>Unterschrift des Ausbildenden bzw. Ausbilders</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';

$html .= '</page>';


if (TESTMODE && basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) echo $html;
?>
