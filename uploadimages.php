<?php
/**
 * Backend function that uploads image attachments to chat messages.
 * PHP 5+
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, SabrinaMarkon.com
 * @license LICENSE.md
 **/

function uploadImageFiles() {

  $currentDirectory = getcwd();
  $uploadDirectory = "/uploads/";
  $errors = [];
  $mimeTypes = ['jpeg', 'jpg', 'png', 'jfif'];
  $maximumImageSize = 10;
  $maximumNumberOfImages = 10;

  $data = json_decode($_POST['imageFilenameList']); // imageFilenameList was stringified.
  if ($data == null) {
    $errors[] = `ERROR: ` . json_last_error(); // There was no imageFilenameList content.
  }

  $numberOfUploadedImages = count($data); // How many images are being uploaded.
  if ($numberOfUploadedImages === 0) {
    $errors[] = `ERROR: No images were included.`;
  }
  if ($numberOfUploadedImages >= $maximumNumberOfImages) {
    $errors[] = `ERROR : Maximum {$maximumNumberOfImages} images allowed per chat message.`;
  }

  // New multi-dimensional array that will have no duplicates or falsy values.
  $imageList = [];
  foreach ($data as $imageUploaded) {
    $imageName = $imageUploaded->imageName; // Each imageName in the $data array.
    $blobName = $imageUploaded->blobName; // Each blobName in the $data array.
    if (!in_array($imageName, $imageList) && !empty($imageName) && !empty($blobName)) {
      $blobNameArray = explode('/', $blobName);
      $blobId = $blobNameArray[sizeof($blobNameArray) - 1];
      // New array with duplicates or falsy values removed.
      array_push($imageList, [$imageName, $blobId]);
    }
  }

  $numberOfUploadedFiles = $_FILES['chatImageInput']['name'];
  // List of blob filenames we will be sending back to the chatroom to replace the loading divs for each.
  $returnBlobIds = [];
  foreach ($numberOfUploadedFiles as $index => $file) {

    foreach ($imageList as $key => $key) {
      if (array_search($file, $imageList[$key]) === 0) {
        // 0 is each subarray item's first element, which is the original filename added by the user
        // to the imageList array. If a match is found in a subarray[0], it means the user did not
        // cancel this image, so it should be uploaded to the server.
        $fileTmpName = $_FILES['chatImageInput']['tmp_name'][$index];
        $fileName = $_FILES['chatImageInput']['name'][$index];
        $fileSize = $_FILES['chatImageInput']['size'][$index];
        $fileNameArray = explode(".", $fileName);
        $fileExtension = strtolower(array_pop($fileNameArray));

        // Use the blob ID as a new filename for the image to prevent problems with duplicate filenames.
        $blobId = $imageList[$key][1];
        $fileNameBlob = $blobId . '.' . $fileExtension;
        $uploadPath = $currentDirectory . $uploadDirectory . $fileNameBlob;
        array_push($returnBlobIds, $fileNameBlob);

        if (!in_array($fileExtension, $mimeTypes)) {
          $errors[] = `ERROR: Only JPEG, PNG, or GIF files allowed.`;
        }
        if ($fileSize > $maximumImageSize * 1024 * 1024) {
          $errors[] = `ERROR: Exceeded size {$maximumImageSize}MB.`;
        }

        if (empty($errors)) {
          $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
          if (!$didUpload) {
            $errors[] = `ERROR: An error occurred. Please contact the administrator.`;
          }
        }
      }
    }
  }
  if (!empty($errors)) {
    echo json_encode($errors);
    exit;
  }
  echo json_encode($returnBlobIds);
}
uploadImageFiles();