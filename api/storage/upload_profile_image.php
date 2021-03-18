<?php
// required headers
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: POST");

require_once('./image_resize.php');

if(isset($_FILES['image'])){
    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png');

    if(in_array($fileActualExt, $allowed)){
        if($fileError === 0){
            $fileNameNew = uniqid('', true).".".$fileActualExt;
            $fileDestination = "../../resources/images/uploads/profile/".$fileNameNew;
            
            // list($width, $height) = getimagesize($fileTmpName);
            // if(resize_image($fileTmpName, $fileActualExt, $width / 2, $height / 2)){
            //     move_uploaded_file($fileTmpName, $fileDestination);
            //     echo json_encode(array("imageName" =>  $fileNameNew));
            // }else{
            //     echo json_encode(array("error" => "There was an error formatting the image. Please try again"));
            // }

            move_uploaded_file($fileTmpName, $fileDestination);
            echo json_encode(array("imageName" =>  $fileNameNew));
         
        }else{
            echo json_encode(array("error" => "There was an error uploading your file. Please try again.login-error"));
        }
    }else{
        echo json_encode(array("error" => "Unable to process the request. Uploaded file is not an image."));
    }
}else{
    echo json_encode(array("error" => "Unable to upload image."));
}
?>