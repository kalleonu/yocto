<HTML>
<HEAD>
 <TITLE> loger</TITLE>
 <SCRIPT type="text/javascript" src="yocto_api.js"></SCRIPT>
 <SCRIPT type="text/javascript" src="yocto_temperature.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="http://192.168.1.35/Planeering/yocto/yocto_genericsensor.js"></SCRIPT>
 <script src="https://code.jquery.com/jquery-2.2.2.js" integrity="sha256-4/zUCqiq0kqxhZIyp4G0Gk+AOtCJsY1TA00k5ClsZYE=" crossorigin="anonymous"></script>
 <script src="http://code.highcharts.com/highcharts.js"></script>
 <script src="http://code.highcharts.com/modules/exporting.js"></script>
 <script src="http://code.highcharts.com/modules/offline-exporting.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>  
<BODY>
<div id="processInfoDiv">
Elapsed Time:<div id="timestamp"></div>
</div>
<div id="currentInfoDiv">
<label id="currentlabel1">pres 1:</label><div id="current1"></div>
<label id="currentlabel2">pres 2:</label><div id="current2"></div>
</div>
<div id="tempInfoDiv">
<label id="templabel1">temp 1:</label><div id="temp1"></div>
<label id="templabel2">temp 2:</label><div id="temp2"></div>
</div>
<div id="controlInfoDiv">
<button id="init" onclick="alusta()">RUN</button>
<button id="append">STOP</button>
<label id="procNr"></label>
</div>
<div id="container"></div>
<script>
var chart; // global
var temp1array=[];
var temp2array=[];
var press1array=[];
var press2array=[];
var timeArray=[];
var temperature1 ;
var temperature2 ;
var sensor1  ;
var sensor2 ;
var temp1;
var temp2 ;
var press1  ;
var press2 ;
var yoctoApp={};
document.getElementById('container').style.width=screen.width-30;

function alusta(){
    yoctoApp.initTime=Date.now();
    $.getJSON(
"getProcNr.php",
{},
function(data2){
    procNr=Number(data2);
    procNr=procNr+1;
    yoctoApp.procNr=procNr;
    document.getElementById("procNr").innerHTML=procNr;
    document.getElementById("init").style.background='red';
        document.getElementById("init").disabled=true;
        document.getElementById("append").disabled=false;
    document.getElementById("append").style.background=''
    refresh();
    
    }
);
}
function refresh(){
        var unixTime=(new Date()).getTime();
        var elapsedTime=(unixTime-yoctoApp.initTime)/1000;
            elapsedTime=Math.round(elapsedTime,0);
        var elapsedTimeMin=Math.round(elapsedTime/60,0);
            yRegisterHub('http://127.0.0.1:4444/');
            temperature1 = yFindTemperature("temperatuuriandur.temperatuur1");
            temperature2 = yFindTemperature("temperatuuriandur.temperatuur2");
            sensor1  = yFindGenericSensor("vool.vool1");
            sensor2  = yFindGenericSensor("vool.vool2");
            temp1 = temperature1.get_currentValue();
            temp2 = temperature2.get_currentValue();
            //algoritm rõhule
          /*   press1  = (sensor1.get_currentValue())*1.25-5;
            press2  = (sensor2.get_currentValue())*1.25-5; */
             press1  = Number(((Math.random()*21)*1.25-5).toFixed(2));
             press2  = Number(((Math.random()*21)*1.25-5).toFixed(2));
if (temperature1.isOnline()){
document.getElementById("temp1").innerHTML = temp1+"\xB0";
document.getElementById("temp2").innerHTML = temp2+"\xB0";
document.getElementById("current1").innerHTML = press1+"bar";
document.getElementById("current2").innerHTML =press2+"bar";
document.getElementById("timestamp").innerHTML = elapsedTime+'sec<br>'+elapsedTimeMin+'min';
}
//kirjutab SQL-i
$.getJSON(
"test2.php",
{"temp1":temp1,
"temp2":temp2,
"current1":press1,
"current2":press2,
"procNr":yoctoApp.procNr
},
function(data){}
);
var timeout=setTimeout('refresh()',60000);

document.getElementById('append').onclick=function(){
temp1array=[];
temp2array=[];
press1array=[];
press2array=[];
timeArray=[];
clearTimeout(timeout);
document.getElementById("init").style.background='';
document.getElementById("init").disabled=false;
document.getElementById("append").disabled=true;
document.getElementById("append").style.background='red';
}

temp1array.push(temp1);
temp2array.push(temp2);
press1array.push(press1);
press2array.push(press2);
timeArray.push(elapsedTime);


//graafik
$(function () { 
    $('#container').highcharts({
        chart: {
            zoomType: 'xy'
        },
        title: {
            text:'Process Graph'
        },
        xAxis: {
             gridLineWidth: 1,
             categories:timeArray
        },
        yAxis: [{

            title: {
                
                text: "Temperature (C\xB0)"
            }
        },
        { 
        title: {
                text: 'Pressure (bar)',
              
            },
            opposite: true

        }
        ],
        series: [{
            name: 'temp1',
            type: 'spline',
 
            data: temp1array
        }, {
            name: 'temp2',
            type: 'spline',

            data: temp2array
        },{
            name: 'press1',
            type: 'spline',
            yAxis:1,
            data: press1array
        },{
            name: 'press2',
            type: 'spline',
            yAxis:1,
            data: press2array
        }],
         exporting: {
             filename: 'rotsess nr:'+yoctoApp.procNr+' date:'+Date(),
            chartOptions: {
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                }
            }
        },
        
    });
});

}//refresh funktsiooni lõpp
</script>
<?php

?>
</BODY>
</HTML> 
