<?php
include('master/Examination.php');

//require_once('class/class.phpmailer.php');

$exam = new Examination;

$current_datetime = date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));


if($_POST['action'] == 'load_question')
{
  
        $questions = Array();

        $exam->query = "SELECT `qt`.`question_id`,`qt`.`online_exam_id`,`qt`.`question_title`,`qt`.`answer_option`,`ot`.`option_id`,`ot`.`option_number`,`ot`.`option_title` FROM `question_table` As `qt` LEFT JOIN `option_table` As `ot` ON `qt`.`question_id` = `ot`.`question_id` WHERE `qt`.`online_exam_id`=:oei";

        $exam->data = array(
            ":oei" => $_POST["exam_id"]
        );
    

    $result = $exam->query_result();

    //$data = $result[0];
    $i = 0;
    $x = $y = 0;
    for($i=0;$i<=sizeof($result)-4;$i+=4){
        //echo $result[$i]["question_title"];
        array_push($questions,Array($result[$i]["question_id"],$result[$i]["online_exam_id"],$result[$i]["question_title"],$result[$i]["answer_option"]));
        array_push($questions[$x],Array());
        $z = $i;
        for($y=0;$y<=3;$y++){
            array_push($questions[$x][4],$result[$z]["option_title"]);
            $z++;
        }
        $x++;
    }
    //$data = json_encode($result[0]);
    //echo $data;
    echo json_encode($questions);
}


?>