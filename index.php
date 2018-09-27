<?php
	function longest_common_substring($words)
	{
		$words = array_map('strtolower', array_map('trim', $words));
		$sort_by_strlen = create_function('$a, $b', 'if (strlen($a) == strlen($b)) { return strcmp($a, $b); } return (strlen($a) < strlen($b)) ? -1 : 1;');
		usort($words, $sort_by_strlen);

		// We have to assume that each string has something in common with the first
		// string (post sort), we just need to figure out what the longest common
		// string is. If any string DOES NOT have something in common with the first
		// string, return false.
		$longest_common_substring = array();
		$shortest_string = str_split(array_shift($words));

		while (sizeof($shortest_string)) {
			array_unshift($longest_common_substring, '');
			foreach ($shortest_string as $ci => $char) {
				foreach ($words as $wi => $word) {
					if (!strstr($word, $longest_common_substring[0] . $char)) {
						// No match
						break 2;
					} // if
				} // foreach
			// we found the current char in each word, so add it to the first longest_common_substring element,
			// then start checking again using the next char as well
				$longest_common_substring[0].= $char;
			} // foreach
			// We've finished looping through the entire shortest_string.
			// Remove the first char and start all over. Do this until there are no more
			// chars to search on.
			array_shift($shortest_string);
		}

		// If we made it here then we've run through everything
		usort($longest_common_substring, $sort_by_strlen);
		return array_pop($longest_common_substring);
	}

	// Get current directory
	if (isset($_GET["currentDir"])) {
		$currentDir = htmlspecialchars($_GET["currentDir"]);
		$isRoot = false; 
	}
	
	else {
		$currentDir = null;
		$isRoot = true; 
	}

	// Get root directory path
	$rootDir = getcwd(); 

	// Get all files in root directory
	$allRootFiles = scandir($rootDir); 

	// Remove current, parent directory pointers
	$allRootFiles = array_diff($allRootFiles, array('.', '..', '.git')); 

	// Filter only the directories
	$directories = array_filter($allRootFiles, function($item) {
		return is_dir($item);
	}); 

	// Get current directory path
	$currentDirPath = getcwd() . DIRECTORY_SEPARATOR . $currentDir;

	// Get all files for current directory
	$allFiles = scandir($currentDirPath); 

	// Get jpg & png images, ignore cases for extension
	$allImageFiles = preg_grep("/^.*\.(jpe?g|png)$/i", $allFiles);

	// Prefix
	$slideNamePrefix = longest_common_substring($allImageFiles); 
?>
<!doctype html>
<html>
	<head>
		<title>Slide Viewer</title>

		<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap-grid.min.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

		<style>
		body {
			font-family: 'Roboto', sans-serif;
			position: relative; 
		}

		#sidebar {
			width: 250px; 
			position: fixed;
			top: 0; 
			left: 0;
		}

		#content {
			padding-left: 250px; 
			box-sizing: border-box; 
		}	
			#root-display {
				color: white; 
				background: black; 
				padding: 200px 60px; 
				text-align: center; 
			}

			#content img {
				max-width: 100%; 
				border: 1px solid #ddd; 
				margin-bottom: 50px; 
			}

		</style>
	</head>

	<body>
		<div id="sidebar">
			<nav>
				<ul>
					<?php foreach ($directories as $dir) { ?>
						<li><a href="?currentDir=<?php echo $dir; ?>"><?php echo $dir; ?></a></li>
					<?php } ?>
				</ul>
			</nav>
		</div>

		<div id="content">
			<?php 
				echo "<p>Current directory is " . $currentDir . "</p>"; 
				echo "<p>Slide prefix is " . $slideNamePrefix . "</p>"; 

				echo "<h1>Folders</h1>";
				print_r($directories); 
				echo "<br>"; 

				echo "<h1>Images</h1>";
				print_r($allImageFiles); 
				echo "<br>";

			?>

			<?php if ($isRoot) { ?>
				<div id="root-display">
					<p>Please select a slide from the left menu</p>
				</div>
			<?php } else { ?>
				<?php 
					foreach ($allImageFiles as $image) { 
						$imagePath = $currentDir . DIRECTORY_SEPARATOR . $image; 
				?>
					

					<img src="<?php echo $imagePath; ?>" />
				<?php } ?>
			<?php } ?>
		</div>
		
	</body>
	
</html>
