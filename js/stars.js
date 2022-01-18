function update_star(field, value) {
	var element = document.getElementsByName(field)[0];
	element.value = value;
	for (i=1;i<=5;i++) {
		var sterretje = document.getElementsByName("sterretje_"+i)[0];
		if (i<=value) {
			sterretje.style.color = "yellow";
		}
		else {
			sterretje.style.color = "#224";
		}
	}
}

function update_star_size(value) {
	for (i=1;i<=5;i++) {
		var sterretje = document.getElementsByName("sterretje_"+i)[0];
		if (i<=value) {
			sterretje.style.fontSize = "110%";
		}
		else {
			sterretje.style.fontSize = "100%";
		}
	}
}