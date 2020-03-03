<?php
$currentDirectory = getcwd();
$uploadDirectory = "/uploads/";
$errors = [];
$mimeTypes = ['jpeg','jpg','png','jfif'];
$maximumImageSize = 10;
$maximumNumberOfImages = 10;

// var_dump($_POST);
// var_dump($_FILES);

$data = json_decode($_POST['imageFilenameList']); // imageFilenameList was stringified.
if ($data == null) {
  echo json_last_error(); // There was no imageFilenameList content.
  exit;
}
// echo var_dump($data[0]); // Each object (one index) in the imageFileNameList array.
// echo $data[0]->imageName; // Filename of one uploaded image.

$numberOfUploadedImages = count($data); // How many images are being uploaded.

if ($numberOfUploadedImages == 0) {
  $errors[] = `Error : No images were included.`;
}

if ($numberOfUploadedImages >= $maximumNumberOfImages) {
  $errors[] = `Error : Maximum {$maximumNumberOfImages} images allowed per chat message.`;
}

$imageNameList = [];
$imageBlobIds = [];

foreach ($data as $imageUploaded) {
  $imageName = $imageUploaded->imageName; // Each imageName in the $data array.
  $blobName = $imageUploaded->blobName; // Each blobName in the $data array.
  if ($imageName) {
    array_push($imageNameList, $imageName);
  }
  if ($blobName) {
    $blobNameArray = explode('/', $blobName);
    $blobIdName = $blobNameArray[sizeof($blobNameArray) - 1];
    array_push($imageBlobIds, $blobIdName);
  }
}
// Remove duplicates.
$imageNameList = array_unique($imageNameList);
$imageBlobIds = array_unique($imageBlobIds);

// print_r($imageNameList);
print_r($imageBlobIds);

$numberOfUploadedFiles = $_FILES['chatImageInput']['name'];
// echo count($numberOfUploadedFiles);

foreach ($numberOfUploadedFiles as $index => $file) {
  // Finds index of item in array to make sure it is a not a file that the
  // user removed from the preview.
  $key = array_search($file, $imageNameList); 
  // $key !== false makes sure that key 0 is not regarded as false.
  if($key !== false) { 
    // The filename was found in imageNameList, so proceed:
    $fileTmpName = $_FILES['chatImageInput']['tmp_name'][$index]; 
    $fileName = $_FILES['chatImageInput']['name'][$index]; 
    $fileSize = $_FILES['chatImageInput']['size'][$index]; 
    $fileNameArray = explode(".", $fileName);
    $fileExtension = strtolower(array_pop($fileNameArray));

    $uploadPath = $currentDirectory . $uploadDirectory . $fileName;
    
    if (!in_array($fileExtension, $mimeTypes)) {
      $errors[] = `Error : Only JPEG, PNG, JFIF, or GIF files allowed.`;
      echo `Error : Only JPEG, PNG, JFIF, or GIF files allowed. ${fileExtension}`;
    }
    if ($fileSize > $maximumImageSize*1024*1024) {
      $errors[] = `Error : Exceeded size {$maximumImageSize}MB.`;
      echo `Error : Exceeded size {$maximumImageSize}MB. ${fileSize}`;
    }

    if (empty($errors)) {
      $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
      if ($didUpload) {
        // echo "The file " . basename($fileName) . " has been uploaded";
        echo $imageBlobIds;
      } else {
        echo "An error occurred. Please contact the administrator.";
      }
    } else {
      echo "There were problems uploading the image(s):\n\n";
      foreach($errors as $error) {
      echo $error . "\n";
      }
    }

  }
}