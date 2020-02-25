<?php
$currentDirectory = getcwd();
$uploadDirectory = "/uploads/";
$errors = [];
$mimeTypes = ['jpeg','jpg','png'];
$maximumImageSize = 10;
$maximumNumberOfImages = 10;

// var_dump($_POST);
var_dump($_FILES);

$data = json_decode($_POST['imageFilenameList']); // imageFilenameList was stringified.
if ($data == null) {
  echo json_last_error(); // There was no imageFilenameList content.
  exit;
}
// echo var_dump($data[0]); // Each object (one index) in the imageFileNameList array.
// echo $data[0]->imageName; // Filename of one uploaded image.
foreach ($data as $imageUploaded) {
  $imageName = $imageUploaded->imageName; // Each imageName in the $data array.
  echo $imageName;




}
$numberOfUploadedImages = count($data); // How many images are being uploaded.
exit;





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
    if ($numberOfUploadedImages >= $maximumNumberOfImages) {
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
