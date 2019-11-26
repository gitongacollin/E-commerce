<?php
 
$current_month_counties = $getUserCounties[0]['county'];
$last_month_counties = $getUserCounties[1]['county'];
 

 
?>

<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function() {
 
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "Usage Share of Desktop Browsers"
	},
	subtitles: [{
		text: "November 2017"
	}],
	data: [{
		type: "pie",
		yValueFormatString: "#,##0.00\"%\"",
		indexLabel: "{label} ({y})",
		dataPoints: [
								{y:<?php echo $getUserCounties[0]['count']; ?>, label: $current_month_counties },

								{y:<?php echo $getUserCounties[1]['count']; ?>, label: $last_month_counties  }
					]
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>      