

/* Time Sale */
if (typeof(BackColor)=="undefined")
	BackColor = "white";
if (typeof(ForeColor)=="undefined")
	ForeColor= "black";
if (typeof(DisplayFormat)=="undefined")
	DisplayFormat = "<div class='day box-time-date'><span class='time-num time-day'>%%D%%</span>Days</div><div class='hour box-time-date'><span class='time-num'>%%H%%</span>Hrs</div><div class='min box-time-date'><span class='time-num'>%%M%%</span>Mins</div><div class='sec box-time-date'><span class='time-num'>%%S%%</span>Secs</div>";
if (typeof(CountActive)=="undefined")
	CountActive = true;
if (typeof(FinishMessage)=="undefined")
	FinishMessage = "";
if (typeof(CountStepper)!="number")
	CountStepper = -1;
if (typeof(LeadingZero)=="undefined")
	LeadingZero = true;
CountStepper = Math.ceil(CountStepper);
if (CountStepper == 0)
	CountActive = false;
var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
function calcage(secs, num1, num2) {
	s = ((Math.floor(secs/num1)%num2)).toString();
	if (LeadingZero && s.length < 2)
		s = "0" + s;
	return s;
}
//function for slider
function CountBack_slider(secs,iid,j_timer) {
	if (secs < 0) {
		document.getElementById(iid).innerHTML = FinishMessage;
		document.getElementById('caption'+j_timer).style.display = "none";
		document.getElementById('heading'+j_timer).style.display = "none";
		return;
	}
	DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs,86400,100000));
	DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
	DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
	DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
	
	var elems = document.getElementsByTagName('*'), i;
	for (i in elems) {
		if((' ' + elems[i].className + ' ').indexOf(' ' + iid + ' ')
				> -1) {
			elems[i].innerHTML = DisplayStr;
		}
	}
	
	$('.'+iid).innerHTML = DisplayStr;
	  if (CountActive)
		setTimeout(function(){CountBack_slider((secs+CountStepper),iid,j_timer)}, SetTimeOutPeriod);
}

//function default
function CountBack(secs,iid,j) {
	if (secs < 0) {
		document.getElementById(iid).innerHTML = FinishMessage;
		document.getElementById('caption'+j).style.display = "none";
		document.getElementById('heading'+j).style.display = "none";
		return;
	}
	DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs,86400,100000));
	DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
	DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
	DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
	document.getElementById(iid).innerHTML = DisplayStr;
	 if (CountActive)
		setTimeout(function(){CountBack((secs+CountStepper),iid,j)}, SetTimeOutPeriod);
}