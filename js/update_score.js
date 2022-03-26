function update_score()
{
	var total = 0;
	var weights = document.querySelectorAll("input[name^='weight[']");
	var criteria = document.querySelectorAll("input[name^='criterium[']");
//	console.log("There are "+criteria.length+" criteria.");
//	console.log(criteria);
	for (let i=0;i<criteria.length;i++)
	{
		let = value = parseInt(criteria[i].value);
		if (value>0)
		{
			let weight = find_weight(criteria[i].name, weights);
			total += value * weight;
		}
	}
	var criteria = document.querySelectorAll("select[name^='criterium[']");
//	console.log("There are "+criteria.length+" criteria.");
	console.log(criteria);
	for (let i=0;i<criteria.length;i++)
	{
		let = value = parseInt(criteria[i].value);
		if (value>0)
		{
			let weight = find_weight(criteria[i].name, weights);
			total += value * weight;
		}
	}
	console.log("Total points for now : "+total);
	var totalElement = document.getElementById("actual_points");
	totalElement.innerHTML = total;
	var maxElement = document.getElementById("max_points");
	var max = parseFloat(maxElement.innerHTML);
	var scoreElement = document.getElementById("score");
	scoreElement.innerHTML = (total*10/max).toFixed(1);
}

function find_weight(name, weights)
{
	let index = name.substr(9);
	for (let i=0;i<weights.length;i++)
	{
		if (weights[i].name.endsWith(index))
		{
			return parseFloat(weights[i].value);
		}
	}
	return 0;
}