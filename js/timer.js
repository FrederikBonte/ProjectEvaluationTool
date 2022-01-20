function update_time() {
	// Visible timer on screen.
	var timer_element = document.getElementById("timer");
	// Hidden fields with seconds.
	var seconds_element = document.getElementById("seconds");
	// Hidden fields with formatted time.
	var time_element = document.getElementById("tijd");
	// Update the time.
	let seconds = seconds_element.getAttribute("value");
	seconds++;
	// Update all elements.
	seconds_element.setAttribute("value", seconds);	
	var formatted_time = seconds_to_time(seconds);
	timer_element.innerText = formatted_time;
	time_element.setAttribute("value", formatted_time);
	
	// Format the timer.
	if (seconds>60*5) 
	{
		timer_element.style.backgroundColor = "red";
	}
	else if (seconds>60*4) 
	{
		timer_element.style.backgroundColor = "yellow";
	}
	else
	{
		timer_element.style.backgroundColor = "black";
	}
}

function seconds_to_time(seconds)
{
	sec = seconds%60;
	min = Math.floor(seconds/60);
	
	return min + ":" + String(sec).padStart(2, '0');
}

const interval = setInterval(update_time, 995);