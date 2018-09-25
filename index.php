<?php
    // Get current directory
    if (isset($_GET["currentDir"])) {
        $currentDir = htmlspecialchars($_GET["currentDir"]);
        $isRoot = false; 
    }
    
    else {
        $currentDir = null;
        $isRoot = true; 
    }

    echo "<p>Current directory is " . $currentDir . "</p>"; 

    // Get all files for current directory
    $allFiles = scandir(getcwd() . DIRECTORY_SEPARATOR . $currentDir); 
    
    // Remove current, parent directory pointers
    $allFiles = array_diff($allFiles, array('.', '..', '.git')); 

    // Filter only the directories
    $directories = array_filter($allFiles, function($item) {
        $name = $item; 
        echo $name . "<br>";
        return is_dir($item);
    }); 

    // Get jpg & png images, ignore cases for extension
    $images = preg_grep("/^.*\.(jpe?g|png)$/i", $allFiles);

    // $images = preg_grep('~\.(jpeg|jpg|png)$~', scandir("./"));

    echo "<h1>Folders</h1>";
    print_r($directories); 
    echo "<br>"; 

    echo "<h1>Images</h1>";
    print_r($images); 
    echo "<br>";
?>