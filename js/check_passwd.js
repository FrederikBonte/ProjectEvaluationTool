function check_passwd() 
{
	var passwd1 = document.getElementById("passwd1");
	var btnSubmit = document.getElementById("chpasswd");
	// Interpret the json result.
	if (passwd1.value.length<8)
	{
		passwd1.style.backgroundColor = "red";
		btnSubmit.disabled = true;
	}
	else
	{
		passwd1.style.backgroundColor = "lightgreen";
		var passwd2 = document.getElementById("passwd2");
		if (passwd1.value==passwd2.value)
		{
			passwd2.style.backgroundColor = "lightgreen";
			btnSubmit.disabled = false;
		}
		else
		{
			passwd2.style.backgroundColor = "red";
			btnSubmit.disabled = true;
		}
	}
}

check_passwd();