<?php



include('master/Examination.php');

$exam = new Examination;

$exam->user_session_private();

$exam->Change_exam_status($_SESSION['user_id']);
$exam_id = $exam->Get_exam_id($_GET["code"]);
//print_r($_POST);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  	<title>Online Examination System in PHP</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  	<script src="https://cdn.jsdelivr.net/gh/guillaumepotier/Parsley.js@2.9.1/dist/parsley.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
  	<link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="style/TimeCircles.css" />
    <script src="style/TimeCircles.js"></script>

    <style>

.progressBar{
  box-sizing:border-box;
  width:100%;
  border:1px solid #ccc;
  height:25px;
}

.pbar{
  float:lefr;
  overflow:hidden;
  height:100%;
  background:red;
  width:1%;
  opacity:0.7;
}
    </style>

</head>
<body>


<div class="container-fluid text-center">
<div class="row">
<?php




$exam->query = "
SELECT online_exam_status, online_exam_datetime, online_exam_duration FROM online_exam_table 
WHERE online_exam_id = '$exam_id'
";
  
date_default_timezone_set("Asia/Kolkata");
$result = $exam->query_result();

$examTime = strtotime($result[0]["online_exam_datetime"]);

$examDuration = $result[0]["online_exam_duration"];

$timeDuration = $examTime + (($examDuration * 60)*1000);

$current_datetime = strtotime(date("Y-m-d H:i:s")); 

////echo $examTime."    ".$examDuration."    ".$timeDuration. "  ". $current_datetime;
$remainingMills =  ($timeDuration - $current_datetime); 
?>
<div class="progressBar">
  <div class="pbar" id="PBar"></div>
</div>
</div>
<div class="row d-flex justify-content-center" style="">

<div class="card text-center mt-4" style="width:70%;">
<h6 class="card-header">Online Examination</h6>
<div class="card-body">

<div class="row" id="user_details_area"></div>

<div class="row">
<div class="col-md-12"><h4 id="question">Question</h4></div>
</div>

<div class="row">

<div class="col-md-6 p-2" style="border:1px solid #ccc;border-radius:5px;">
<div class="form-check">
  <input class="form-check-input" type="radio" name="answerOpt" id="exampleRadios1" value="1">
  <label class="form-check-label" for="exampleRadios1" id="opt1">
    Opt 1
  </label>
</div>
</div>

<div class="col-md-6 p-2" style="border:1px solid #ccc;border-radius:5px;">
<div class="form-check">
  <input class="form-check-input" type="radio" name="answerOpt" id="exampleRadios1" value="2">
  <label class="form-check-label" for="exampleRadios1" id="opt2">
    Opt 1
  </label>
</div>
</div>

<div class="col-md-6 p-2" style="border:1px solid #ccc;border-radius:5px;">
<div class="form-check">
  <input class="form-check-input" type="radio" name="answerOpt" id="exampleRadios1" value="3">
  <label class="form-check-label" for="exampleRadios1" id="opt3">
    Opt 1
  </label>
</div>
</div>

<div class="col-md-6 p-2" style="border:1px solid #ccc;border-radius:5px;">
<div class="form-check">
  <input class="form-check-input" type="radio" name="answerOpt" id="exampleRadios1" value="4">
  <label class="form-check-label" for="exampleRadios1" id="opt4">
    Opt 1
  </label>
</div>
</div>


</div>

<div class="row mt-4">
<div class="col-md-2">
<button class="btn btn-primary" type="button" id="pre"> Previous</button>
</div>
<div class="col-md-8">
<button class="btn btn-warning" type="button"> Reset Answer</button>
<button class="btn btn-primary" type="button" id="submitExam"> Submit</button>

</div>
<div class="col-md-2">
<button class="btn btn-primary" type="button" id="nxt"> Next</button>
</div>
</div>

</div>
</div>

</div>
</div>



<script>
var exam_id = <?php echo $exam_id; ?>;
var questionPosition = 0;
var questionData;
var answered = [];
var answer_book = [];


$(document).ready(function(){

$("#submitExam").on("click",()=>{
  window.location = "http://localhost/exam/view_exam.php";
})



var dt = $.post("loadquestions.php",{"action":"load_question","exam_id":exam_id});

dt.done(function(data){
    questionData = JSON.parse(data);
    console.log(JSON.parse(data));
    displayQuestion(JSON.parse(data));
    questionData.forEach((itm,idx)=>{
      answer_book.push(0);
    });
    console.log(answer_book);
});

$("#pre").on("click",function(){
console.log("previous");
navigateQuestion(-1);
});

$("#nxt").on("click",function(){
console.log("next");
navigateQuestion(1);
console.log(questionPosition +  "QP NXT");
});


$("input[name=answerOpt]").on("click",function(){
console.log("selected", $(this).prop("checked"),$(this).prop("value"));
console.log($(this));
answer_book[questionPosition] = $(this).prop("value");
console.log(answer_book);

question_id = $("#question").attr("question_id");
answer_option = $(this).prop("value");

$.ajax({
			url:"user_ajax_action.php",
			method:"POST",
			data:{question_id:question_id, answer_option:answer_option, exam_id:exam_id, page:'view_exam', action:'answer'},
			success:function(data)
			{

			}
		})


});

//startTimer(1000);
load_user_details();

});

function displayQuestion(data,questionPosition = 0){
$("#question").text(data[questionPosition][2]);
$("#question").attr("question_id",data[questionPosition][0]);
$("#opt1").text(data[questionPosition][4][0]);
$("#opt2").text(data[questionPosition][4][1]);
$("#opt3").text(data[questionPosition][4][2]);
$("#opt4").text(data[questionPosition][4][3]);
setOptionState();
console.log(answer_book, "   ",questionPosition);
//$(tmp[answer_book[questionPosition]-1]).prop("checked",true);
}

function setOptionState(){
  var tmp = $("input[type=radio]");
  $(tmp[answer_book[questionPosition]-1]).prop("checked",true);
}

function addToAnswerBook(index,value){
  answer_book[index] = value;
  var options = $("input[type=radio]");
  console.log(options);
}

function navigateQuestion(loc){
    if(loc == -1){
    if(questionPosition == 0) {
        console.log(questionPosition +  "QPP");
            displayQuestion(questionData,questionData.length-1);
            questionPosition = questionData.length - 1;
        }
        else {
            questionPosition--;
            displayQuestion(questionData,questionPosition);
            
        }
        
        resetOptions();
        setOptionState();
        
    }
    if(loc == 1){
        
    if(questionPosition == questionData.length - 1) {
            
            displayQuestion(questionData,0);
            questionPosition = 0;
        }
        else {
            questionPosition++;
            displayQuestion(questionData,questionPosition);
            
        }
        console.log(questionPosition +  "QP");
        console.log(answer_book, " before reset");
        resetOptions();
        setOptionState();
        console.log(answer_book, "after reset");
    }
}

function resetOptions(){
  $("input[type=radio]").prop("checked",false);
}

var dura = <?php

echo $remainingMills;

?>;

var counter = 0;
var elePbar = document.getElementById("PBar");
var examTimer;
startTimer(Math.round(dura/1000));
//alert(dura);
function startTimer(duration){
  examTimer = setInterval(()=>{
    if(counter>duration){
      clearInterval(examTimer);
      alert("exan over");
      window.location = "http://localhost/exam/view_exam.php";
    }
    counter++;
    console.log(counter);
    var pbarval = Math.round((counter/duration)*100)
    elePbar.style.width = pbarval + "%";
  },1000);
}

function stopTimer(){
  window.clearInterval(examTimer);
}

function load_user_details()
	{
		$.ajax({
			url:"user_ajax_action.php",
			method:"POST",
			data:{page:'view_exam', action:'user_detail'},
			success:function(data)
			{
				$('#user_details_area').html(data);
        console.log(data);
			}
		})
	}

</script>



</body>
</html>