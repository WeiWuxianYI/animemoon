<?php
$TITLE = 'UPLD';
require_once '../private/config.php';
$target_dir = $ROOT."/img/anime/newicons/";
$imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
$fileName = bin2hex(openssl_random_pseudo_bytes(8)).'_'.time().'.'.$imageFileType;
$target_file = $target_dir . $fileName;
$uploadOk = 1;
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

$isSuccess = false;
$fPath = '';
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    $wasMoved = @move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
    if ($wasMoved) {
        $fPath = $site_link_images . '/img/anime/newicons/'.$fileName;
        $isSuccess = true;
    } else {
        $isSuccess = false;
    }
}
?>
<html>
<head>
    <?php require_once '../private/meta.php'; ?>
</head>
<body>
<?php require_once '../private/sidebar.php'?>
<div class="panel">
    <div class="panel-title">Upload</div>
    <div class="panel-inputs">
        <?php if ($isSuccess) { ?>
            <a href="<?=$fPath?>" class="line">The file `<?=$fileName?>` has been uploaded. (Click here to see the image.)</a>
            <img src="<?=$fPath?>">
        <?php } else { ?>
            <div class="line">Sorry, there was an error uploading your file.</div>
        <?php } ?>
    </div>
</div>
</body>
</html>