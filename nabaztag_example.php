<html>
<head>
	<title>Nabaztag PHP Class Examples</title>
</head>

<body>

<h1>Nabaztag PHP Class Examples</h1>

<?php

require('Nabaztag.php');

/**
 * Set serial and API token variables to match your Nabaztag.
 *
 * Your serial number can be found on the bottom of your Nabaztag rabbit.
 * You can create an API token by creating an account at http://www.nabaztag.com/
 */

$serial = ""; // TODO add your serial number here.
$token = ""; // TODO add your API token here.

// Get values from request.

$phrase = $_REQUEST["phrase"];
$leftEar = (int) $_REQUEST["left_ear"];
$rightEar = (int) $_REQUEST["right_ear"];
$submitEars = $_REQUEST["submit_ears"];
$wakeStatus = $_REQUEST["wake_status"];
$streamUrl = $_REQUEST["stream_url"];

// Get an instance of the Nabaztag class.

$nabaztag = new Nabaztag($serial, $token);

// Text to speech.

if (!is_null($phrase))
{
	$nabaztag->speak($phrase);
}

// Move rabbit ears.

if (!is_null($submitEars))
{
	$nabaztag->moveEars($leftEar, $rightEar);
}

// Set wake status.

if (!is_null($wakeStatus))
{
	$nabaztag->setWakeStatus($wakeStatus);
}

// Stream media URL.

if (!is_null($streamUrl))
{
	$nabaztag->streamUrl($streamUrl);
}

// Debug information.

echo "<p><b>Parameters sent to the API:</b></p>";

print_r($nabaztag->getApiParams());

echo "<p><b>URLs sent to the streaming API:</b></p>";

print_r($nabaztag->getStreamingApiUrls());

echo "<p><b>The API responded:</b></p>";

print_r($nabaztag->getLastApiResponse());

// Display form.

?>

<h2>Text to Speech Example</h2>

<form name="nabaztag_tts" action="<?=$_SERVER['PHP_SELF']?>" method="post">

<p>
<input type="text" name="phrase" size="50" value="<?=$phrase?>" /><br />
<input type="submit" name="submit_tts" value="Speak" />
</p>

</form>

<h2>Ears Example</h2>

<form name="nabaztag_ears" action="<?=$_SERVER['PHP_SELF']?>" method="post">

<p><i>Enter a positon between 0 and 16. 0 points the ear straight up.</i></p>
<p>
Left Ear: <input type="text" name="left_ear" size="3" value="<?=$leftEar?>" />
Right Ear: <input type="text" name="right_ear" size="3" value="<?=$rightEar?>" />
<input type="submit" name="submit_ears" value="Move Ears" />
</p>

</form>

<h2>Wake Status Example</h2>

<form name="nabaztag_status" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<select name="wake_status">
	<option value="1">Wake</option>
	<option value="0">Sleep</option>
</select>
<input type="submit" name="submit_status_wake" value="Set Wake Status" />
</p>

</form>

<h2>Streaming Media Example</h2>

<form name="nabaztag_stream" action="<?=$_SERVER['PHP_SELF']?>" method="post">

<p>Enter the URL of an MP3 file or online radio station.</p>
<p>Working radio stream for testing: http://82.197.165.137/</p>

<p>
<input type="text" name="stream_url" size="50" value="<?=$streamUrl?>" />
<input type="submit" name="submit_stream" value="Stream Media" />
</p>

</form>

</body>
</html>
