<?php
/* Script to redirect to Lemmings app if LevelID exists, otherwise show a form. */
// Get the parameter value
$param = isset($_GET['LevelID']) ? $_GET['LevelID'] : '';

// Validate that the parameter is in the expected format
if (!empty($param)) {
	$param = preg_replace('/\s+|^#/', '', $param);
	if (!empty($param)) {
		// Set the MIME type and the redirection header
		header("Content-Type: application/vnd.custom+link");
		$c = "sadpuppylemmingsdplnk://?LinkType=LevelCode&LevelID=$param";
		header("Location: $c", true, 302);
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="shortcut icon" href="/favicon.ico">
    <title>Sel2In Lemmings Level Helper</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        #done {
            width: 200px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px auto;
            transition: all 0.5s ease;
        }
    </style>	
    <script>
	
	function onSubmit(e){
		done2("Sent to server, will open if device/ laptop has app installed and enabled", "green")
	}
        // Function to copy the generated link to clipboard
        function createLink(ww) {
            // Get the LevelID from the text box
            var tmpId = document.getElementById('levelID').value.trim();
			tmpId = tmpId.replace(/ /g, "").replace(/^#/, "");
			const levelID = tmpId;
            if (!levelID) {
                alert('Please enter a LevelID before copying the link.');
                return;
            }
            // Generate the link
            const link = `https://sel2in.com/e?LevelID=${levelID}`;
			const ii = document.getElementById("lnk3");
			ii.innerHTML = "<a href=" + link + ">" + link + "</a><br>"
			
			if(ww == 2){
				// Copy the link to clipboard
				navigator.clipboard.writeText(link).then(() => {
					done2("Okay copied", "green")
					//alert('Link copied to clipboard: ' + link);
				}).catch(err => {
					done2("Oops " + err, 'red');
					console.error('Failed to copy link: ', err);
					alert('Failed to copy the link. Please try again.');
				});
			}else{
				done2("Ok link put above, can tap, click or copy it", "lightgreen")
			}
        }
		
		function done2(msg, clr){
			const doneDiv = document.getElementById('done3');

        // Apply initial styles and content
        doneDiv.style.border = '2px solid ' + clr;
        doneDiv.style.borderRadius = '10px';
        doneDiv.style.color = clr;
        doneDiv.innerHTML = msg;

        // Change styles and content after 3 seconds
        setTimeout(() => {
            doneDiv.style.border = '2px solid gray';
            doneDiv.style.color = 'gray';
            doneDiv.innerHTML = '-';
        }, 4500);			
			
		}
    </script>
</head>
<body>
<h1>Sel2In &nbsp;&nbsp;&nbsp;&nbsp;<img src="/imgs/ThemeProg.jpg" alt="Sel2In Software Services" name="Sel2In_Logo" width="233" height="130" id="Sel2In_Logo" /></h1>
<p>Use this page to enter a Lemmings level you made to run. Enter the code in this box and press "Submit" or copy the link.</p>

<h1>Enter LevelID</h1>

<form method="GET" action="" onsubmit="onSubmit(event)" style="border: 2px; margin : 10px ; font-size: 125%;>
    <label for="levelID">LevelID:</label>
    <input type="text" id="levelID" name="LevelID" required value="3jv6mn7">
    <button type="submit">Submit</button>
</form>

<br>
<p>To share this form with someone else prefilled with your level, share the URL below and replace the level code:</p>
<a href="https://sel2in.com/e?LevelID=3jv6mn7" id="exampleLink">https://sel2in.com/e?LevelID=3jv6mn7</a>
<br><br>
<!-- Button to copy link -->
<button onclick="createLink(1)">Put Link below</button> &nbsp;
<button onclick="createLink(2)">Copy Link to Clipboard & put it below</button>

Link will appear here: <div id=lnk3 style="border: 2px solid; margin : 10px ; font-size: 175%;"></div>
<div id="done3"></div>

<?php
if (!empty($param) && !empty($_GET)) {
    // Display an error if the parameter is invalid
    echo "Invalid or missing URL parameter. Needs a param LevelID like <a href='https://sel2in.com/e?LevelID=3jv6mn7'>https://sel2in.com/e?LevelID=3jv6mn7</a>, got value of LevelID: " . htmlspecialchars($param) . ".";
}
?>

<br>
Code of this page <a href="https://github.com/tgkprog/phpSmallThings/">github tgkprog phpSmallThings</a> 
<br><br>
<a href="/">Home page</a> 
</body>
</html>
