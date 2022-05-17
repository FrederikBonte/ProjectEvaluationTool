function load_klas(klas_code) 
{
	xhttp = new XMLHttpRequest();
	xhttp.onload = processStudents;
	// Create your own get request.
	xhttp.open("GET", "export_students.php?klas=" + klas_code + "&json");
	xhttp.send();
	
	xhttp = new XMLHttpRequest();
	xhttp.onload = processEvaluations;
	// Create your own get request.
	xhttp.open("GET", "common/list_evaluations.php?klas=" + klas_code);
	xhttp.send();
}

function processStudents()
{
	var mytable = document.getElementById("students");
	// Interpret the json result.
	var students = JSON.parse(this.responseText).leerlingen;
	console.log("Received data for "+students.length+" student(s).");

	// Retrieve all table rows.
	options = $("#students").children();		

	// Loop through all the elements.
	for (i = students.length-1; i >= 0; i--) 
	{
		console.log("Table has "+options.length+" rows.");
		// Add student after the last row.
		addStudent(options, students[i]);
	}
	
//	myinput.style.fontFamily = "Comic Sans MS";
//	$("#students tr").remove(); 
	
}

function addStudent(options, student)
{
	console.log("About to add "+student.voornaam+".");
	// Create a new table row for each students.
	//  * name
	//  * previous evaluation
	//  * form button for new evaluation
	// Create the new HTML elements NEATLY!!! (No innerhtml dump.)
	var name;
	if (typeof student.tussenvoegsel === 'undefined')
	{
		name = student.voornaam + " " + student.achternaam;
	}
	else
	{
		name = student.voornaam + " " + student.tussenvoegsel + " " + student.achternaam;
	}	
		
	new_option = $("<option value=\""+ student.nummer +"\">"+name+"</option>");
	options.after(new_option);	
}

function processEvaluations()
{
	// Retrieve the required element...
	var div_recent = document.getElementById("recent_students");
	// Replace the content of the div with the table...
	div_recent.innerHTML = this.responseText;
}