<?php
/*
 * xfnw's weird php upload thing
 *
 * to set it up make a symlink to somewhere in /tmp at i
 * eg: ln -s $(mktemp) i
 *
 * thats it! it should set itself up with all the permissions
 * and stuff
 */


$target_dir = "i";

$real_dir = readlink($target_dir);
if (!file_exists($real_dir)) {
	mkdir($real_dir, 0777, true);
}

$target_dir = $target_dir . '/';

if ($handle = opendir($target_dir)) {

    while (false !== ($file = readdir($handle))) { 
        $filelastmodified = filemtime($target_dir . $file);
        // 6 hours in a day * 3600 seconds per hour
        if((time() - $filelastmodified) > 6*3600)
        {
           unlink($target_dir . $file);
        }

    }

    closedir($handle); 
}


$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 0;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
if ($fileType == '') {
	$fileType = 'txt';
}
$target_file = $target_dir . substr(md5_file($_FILES['file']['tmp_name']), 1, 6) . "." . $fileType;

if (file_exists($_FILES["file"]['tmp_name'])) {
	$uploadOk = 1;
}

// Check if file already exists
if (file_exists($target_file)) {
    //echo "Whoops! someone already uploaded that, " . $target_file;
    $uploadOk = 0;
}
// Check file size
if ($_FILES["file"]["size"] > 600000000) {
    //echo "Sorry, your file is too large. (maximum size allowed 6mb)";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    //echo "Sorry, your file was not uploaded for an unknown reason.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
	    echo "https://" . $_SERVER['HTTP_HOST'] . "/" . $target_file;
	    exit;
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
  <title>xfnw's upload thing</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://xfnw.ttm.sh/assets/xfnw.css">
</head>
<body>
<h1>xfnw's upload thing</h1>
<p>files are regularly automatically deleted, and will probably be gone in 6 hours if not sooner</p>

<pre>
# upload function
upload(){ curl -F"file=@$1" https://xfnw.ttm.sh/u.php }
upload file.png

# paste function
paste(){ curl -F"file=@-" https://xfnw.ttm.sh/u.php }
cat something.txt | paste

# auto upload your screenshots
scrot -e 'curl -F"file=@$f" https://xfnw.ttm.sh/u.php | xclip -selection clipboard'
</pre>

<p>or</p>

<form action="u.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" onchange="form.submit()">
</form>


</body>
</html>

