var request = null;
try {
 request = new XMLHttpRequest();
} catch (trymicrosoft) {
 try {
  request = new ActiveXObject("Msxm12.XMLHTTP");
 } catch (othermicrosoft) {
  try {
   request = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (failed) {
   request = null;
  }
 }
}
//if (request == null) {
 // Couldn't create request object.
//}
function updateTimezone() {
 if (request.readyState == 4) {
  var timezone_time = request.responseText;
  if (timezone_time != "") {
   document.getElementById("timezone_display").innerHTML = timezone_time;
  } else {
   document.getElementById("timezone_display").innerHTML = "";
  }
 }
}
function fetchTimezone() {
 //createRequest();
 var timezone = document.getElementById("timezone").value;
 if (timezone != "") {
  var url = "./includes/scripts/timezone.php?timezone=" + escape(timezone) + "&dummyurl=" + new Date().getTime();
  request.open("GET", url, true);
  request.onreadystatechange = updateTimezone;
  request.send(null);
 } else {
  document.getElementById("timezone_display").innerHTML = "";
 }
}