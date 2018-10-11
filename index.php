<?php
	function longest_common_substring($words)
	{
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
		$currentDir = htmlspecialchars_decode(urldecode($_GET["currentDir"]));
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
	$numImageFiles = count($allImageFiles); 

	// Prefix
	$slideNamePrefix = longest_common_substring($allImageFiles); 

	natsort($allImageFiles);
?>
<!doctype html>
<html>
	<head>
		<?php 
			$docTitle = $isRoot ? 'Slide Viewer' : $currentDir; 
		?>
		<title><?php echo $docTitle; ?></title>

		<meta charset="utf-8">

		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">

		<style>
		body {
			font-family: 'Roboto', sans-serif;
			position: relative; 
			margin: 0; 
			padding: 0; 
			font-size: 14px; 
			line-height: 24px; 
		}

		h1 {
			font-size: 14px; 
			line-height: 24px; 
			font-weight: normal; 
			margin-top: 20px; 
		}

		ul, li, h1, div {
			margin: 0; 
			padding: 0; 
		}

		ul {
			list-style-type: none; 
		}

			ul li a {
				display: block; 
			}

		a {
			color: black; 
			text-decoration: none; 
		}

			a:hover {
				color: #ff5544; 
			}

			li.current a {
				font-weight: bold; 
			}

				li.current a:hover {
					color: black; 
				}

		#sidebar {
			width: 250px; 
			position: fixed;
			top: 0; 
			left: 0; 
			box-sizing: border-box; 
			padding: 20px 10px 20px 25px; 
			overflow: hidden; 
		}

			#sidebar li {
				display: block; 
			}

		#content {
			padding-left: 250px; 
			box-sizing: border-box; 
			padding-right: 40px; 
		}

			#content ul#list-main {
				font-size: 24px; 
				line-height: 34px; 
				font-weight: 700; 
			}

				#content ul#list-main li {
					display: block; 
				}

				#content ul#list-main li a {
					display: block; 
					color: #ccc; 
					background: #f5f5f5; 
					border: 1px solid #e5e5e5; 
					border-top: none; 
					padding: 40px 50px;
				}

					#content ul#list-main li a:hover {
						color: black; 
						border-color: #ddd;
						background: #eee; 
					}

			#content h1 {
				margin-top: 20px; 
				margin-bottom: 15px; 
			}

			#content h1 .separator {
				color: #ff5544; 
			}

			#content h1 .count {
				color: #999; 
			}

			#content img {
				max-width: 100%; 
				border: 1px solid #ddd; 
				margin-bottom: 80px; 
			}

		</style>
	</head>

	<body>
		<div id="sidebar">
			<nav>
				<ul>
					<li><a id="link-home" href="./">Home</a></li>

					<?php foreach ($directories as $dir) { ?>
						<li<?php if ($dir == $currentDir) echo ' class="current"'; ?>><a href="?currentDir=<?php echo urlencode(htmlspecialchars($dir)); ?>"><?php echo $dir; ?></a></li>
					<?php } ?>
				</ul>
			</nav>
		</div>

		<div id="content">
			<?php if ($isRoot) { ?>
				<ul id="list-main">
					<?php foreach ($directories as $dir) { ?>
						
						<li><a href="?currentDir=<?php echo urlencode(htmlspecialchars($dir)); ?>"><?php echo $dir; ?></a></li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<?php
					$slideString = ($numImageFiles > 1) ? 'slides' : 'slide';

					echo "<h1>$currentDir <span class=\"separator\">/</span> <span class=\"count\">$numImageFiles $slideString</span></h1>"; 

					foreach ($allImageFiles as $image) { 
						$imagePath = $currentDir . DIRECTORY_SEPARATOR . $image; 
				?>
					

					<img src="<?php echo $imagePath; ?>" />
				<?php } ?>
			<?php } ?>
		</div>
		
	</body>
	
</html>
