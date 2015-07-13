function ToggleNew() {
$("#popup-notice").show();
on = document.getElementById("subNewPopup");
off1 = document.getElementById("subOkPopup");
off2 = document.getElementById("subNotPopup");
switch (on.style.display) {
	case "none":
	on.style.display="block";
	off1.style.display="none";
	off2.style.display="none";
	break;
	case "block":
	on.style.display="none";
	break;
}}


function ToggleNot(usrid, url) {
	$("#popup-notice").show();

	on = document.getElementById("subNotPopup");
	off1 = document.getElementById("subNewPopup");
	off2 = document.getElementById("subOkPopup");
	switch (on.style.display) {
		case "none":
		on.style.display="block";
		off1.style.display="none";
		off2.style.display="none";
		break;
		case "block":
		on.style.display="none";
		break;
}}


function ToggleOk() {
	$("#popup-notice").show();
on = document.getElementById("subOkPopup");
off1 = document.getElementById("subNewPopup");
off2 = document.getElementById("subNotPopup");
switch (on.style.display) {
	case "none":
	on.style.display="block";
	off1.style.display="none";
	off2.style.display="none";
	break;
	case "block":
	on.style.display="none";
	break;
}}