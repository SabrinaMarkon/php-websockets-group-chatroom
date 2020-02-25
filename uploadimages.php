<?php
$currentDirectory = getcwd();
$uploadDirectory = "/uploads/";
// print_r($_POST);
// ($_FILES);
// print_r($_FILES['chatImageInput']);
$imageCount = 0;
foreach($_FILES['chatImageInput']['name'] as $name) {
  $imageCount++;
  echo $name;
}
echo $imageCount;
exit;

$errors = [];
$mimeTypes = ['jpeg','jpg','png'];
$maximumImageSize = 10;
$maximumNumberOfImages = 10;

$fileName = $_FILES['file']['name'];
$fileSize = $_FILES['file']['size'];
$fileTmpName  = $_FILES['file']['tmp_name'];
$fileType = $_FILES['file']['type'];
$fileExtension = strtolower(end(explode('.',$fileName)));

$uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 

if (isset($_POST['msg'])) {
    if (! in_array($fileExtension,$mimeTypes)) {
      $errors[] = "Error : Only JPEG, PNG, or GIF files allowed.";
    }
    if ($fileSize > $maximumImageSize*1024*1024) {
      $errors[] = `Error : Exceeded size {$maximumImageSize}MB.`;
    }
    if ($imageCount >= $maximumNumberOfImages) {
      $errors[] = `Error : Maximum {$maximumNumberOfImages} images per chat message.`;
    }

    if (empty($errors)) {
      $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
      if ($didUpload) {
        echo "The file " . basename($fileName) . " has been uploaded";
      } else {
        echo "An error occurred. Please contact the administrator.";
      }
    } else {
        echo "There were problems with the uploaded image(s):\n\n";
        foreach($errors as $error) {
        echo $error . "\n";
      }
    }
  }
