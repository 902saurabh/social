<?php
include("include/header.php")
?>


<!DOCTYPE html>
<html>
<head>
	<link href="assets/css/upload.css" rel="stylesheet" type="text/css" />
	<!-- add styles -->
<!-- add scripts -->


	<title></title>
</head>
<body>
	<div class="bbody">
    <!-- upload form -->
    <form id="upload_form" enctype="multipart/form-data" method="post" action="upload.php" onsubmit="return checkForm()">
        <!-- hidden crop params -->
        <input type="hidden" id="x1" name="x1" />
        <input type="hidden" id="y1" name="y1" />
        <input type="hidden" id="x2" name="x2" />
        <input type="hidden" id="y2" name="y2" />
        <h2>Step1: Please select image file</h2>
        <div><input type="file" name="image_file" id="image_file" onchange="fileSelectHandler()" /></div>
        <div class="error"></div>
        <div class="step2">
            <h2>Step2: Please select a crop region</h2>
            <img id="preview" />
            <div class="info">
                <label>File size</label> <input type="text" id="filesize" name="filesize" />
                <label>Type</label> <input type="text" id="filetype" name="filetype" />
                <label>Image dimension</label> <input type="text" id="filedim" name="filedim" />
                <label>W</label> <input type="text" id="w" name="w" />
                <label>H</label> <input type="text" id="h" name="h" />
            </div>
            <input type="submit" name="sub_img" value="Upload" />
        </div>
    </form>
</div>

<?php 
function uploadImageFile() { // Note: GD library is required for this function
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $iWidth = $iHeight = 200; // desired image result dimensions
        $iJpgQuality = 90;
        if ($_FILES) {
            // if no errors and size less than 250kb
            if (! $_FILES['image_file']['error'] && $_FILES['image_file']['size'] < 250 * 1024) {
                if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
                    // new unique filename
                    $uploaddir ='assets/images/profile_pics/';
                   // $file_temp_name = '_original.'.md5(time()).'n'.$type;
                    $sTempFileName =$uploaddir.md5(time().rand());
                    // move uploaded file into cache folder
                    move_uploaded_file($_FILES['image_file']['tmp_name'], $sTempFileName);
                    // change file permission to 644
                    @chmod($sTempFileName, 0644);
                    if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                        $aSize = getimagesize($sTempFileName); // try to obtain image info
                        if (!$aSize) {
                            @unlink($sTempFileName);
                            return;
                        }
                        // check for image type
                        switch($aSize[2]) {
                            case IMAGETYPE_JPEG:
                                $sExt = '.jpg';
                                // create a new image from file
                                $vImg = @imagecreatefromjpeg($sTempFileName);
                                break;
                            case IMAGETYPE_PNG:
                                $sExt = '.png';
                                // create a new image from file
                                $vImg = @imagecreatefrompng($sTempFileName);
                                break;
                            default:
                                @unlink($sTempFileName);
                                return;
                        }
                        // create a new true color image
                        $vDstImg = @imagecreatetruecolor( $iWidth, $iHeight );
                        // copy and resize part of an image with resampling
                        imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1'], (int)$_POST['y1'], $iWidth, $iHeight, (int)$_POST['w'], (int)$_POST['h']);
                        // define a result image filename
                        $sResultFileName = $sTempFileName . $sExt;
                        // output image to file
                        imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
                        @unlink($sTempFileName);
                        return $sResultFileName;
                    }
                }
            }
        }
    }
}
$sImage = uploadImageFile();
	if(isset($_POST['sub_img'])){
	$query=mysqli_query($con,"UPDATE users set profile_pic='$sImage' where username='$userLoggedIn'");
	header("Location:index.php");
	}
 ?>

</body>
</html>

