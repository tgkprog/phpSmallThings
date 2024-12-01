<?php
/* script looks if there is a GET param LevelID if its there it redirects to lemmings app with a 302, else showsa form to accept a level and submit form to itself to do this. To open a custom level in lemmings game, tested in lemmings */
// Get the parameter value
$param = isset($_GET['LevelID']) ? $_GET['LevelID'] : '';

// Validate that the parameter is in the expected format
if (!empty($param)) {
    // Set the MIME type and the redirection header
    header("Content-Type: application/vnd.custom+link");
	$c = "sadpuppylemmingsdplnk://?LinkType=LevelCode&LevelID=$param";
	//echo "$c";
    header("Location: $c", true, 302);
    exit;
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>

	<link rel="shortcut icon" href="/favicon.ico">
	<title>Sel2In Lemmings level go to helper page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1>Sel2In &nbsp;&nbsp;&nbsp;&nbsp;<img src="/imgs/ThemeProg.jpg" alt="Sel2In Software Services" name="Sel2In_Logo" width="233" height="130" id="Sel2In_Logo" /></h1>
Can use this page to enter a lemmings level that you made to run, enter the code in this box and press enter or the submit button

 
 
<h1>Enter LevelID</h1>
    <form method="GET" action="">
        <label for="levelID">LevelID:</label>
        <input type="text" id="levelID" name="LevelID" required value=3jv6mn7 >
		
        <button type="submit">Submit</button>
    </form>
	
	<br> to share this form with someone else with your level prefilled share it like the URL below just replace the level code
	
	<a href=https://sel2in.com/e?LevelID=3jv6mn7>https://sel2in.com/e?LevelID=3jv6mn7</a>
	
	<?php
	if (!empty($param) &&  !empty($_GET)){
		// Handle invalid or missing parameter
		echo "Invalid or missing URL parameter. Needs a param LevelID like <a href=https://sel2in.com/e?LevelID=3jv6mn7>https://sel2in.com/e?LevelID=3jv6mn7</a>, got value of LevelID :" . $param . ".";
		
}
	?>
</body>
</html>
