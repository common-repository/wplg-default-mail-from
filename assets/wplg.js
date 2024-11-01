function wdmf_radio_yes_click()
{
	document.getElementById("sendername_inp").style.backgroundColor = "white";
	document.getElementById("domainname_inp").style.backgroundColor = "white";
	document.getElementById("sendername_inp").readOnly = false;
	document.getElementById("domainname_inp").readOnly = false;
	document.getElementById("sendername_inp").value = document.getElementById("sh").value;
	document.getElementById("domainname_inp").value = document.getElementById("dh").value;
}
function wdmf_radio_no_click()
{
	document.getElementById("sendername_inp").style.backgroundColor = "#f1f1f1";
	document.getElementById("domainname_inp").style.backgroundColor = "#f1f1f1";
	document.getElementById("sendername_inp").readOnly = true;
	document.getElementById("domainname_inp").readOnly = true;
	document.getElementById("sendername_inp").value = "";
	document.getElementById("domainname_inp").value = "";
}
