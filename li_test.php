<html>
	<head>
		<title>List Test</title>
	</head>
<body>

	<ul>
	<?php
		$max = rand(1, 100);
		$num = rand(1, $max); 
		for ($i = 1; $i <= $num; $i++) {
			echo "<li>" . $i . "</li>";
		}
	?>
	</ul>

</body>
</html>
