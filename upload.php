<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // File upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["animalPhoto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["animalPhoto"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["animalPhoto"]["size"] > 50000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["animalPhoto"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["animalPhoto"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Get location data
    $address = $_POST['address'] ?? '';
    $fileSize = $_FILES["animalPhoto"]["size"];
    $fileSizeKB = round($fileSize / 1024, 2);

    // Construct the message
    $message = "An injured animal photo has been uploaded.\nLocation: " . $address;
    $message .= "\nImage Name: " . htmlspecialchars(basename($_FILES["animalPhoto"]["name"])); // Include the image name in the message
    $message .= "\nFile Size: " . $fileSizeKB . " KB";

    // Sinch API credentials
    $apiKey = "80d6c4bf0b60403c88094db558151ad6";
    $sender = "447441421498";
    $recipient = "919036702582";

    // Sinch API URL
    $url = "https://sms.api.sinch.com/xms/v1/c3676af2108b4564816e567b9ccb3782/batches";

    // Set SMS recipients and content
    $data = [
        "from" => $sender,
        "to" => [$recipient],
        "body" => $message
    ];

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute cURL and get the response
    $response = curl_exec($ch);

    if ($response === false) {
        echo "cURL Error: " . curl_error($ch);
    } else {
        echo "Response: " . $response;
    }

    // Close cURL
    curl_close($ch);

    // Redirect to the home page
    header("Location: /miniproject/redirectpage.php");
    exit();
}
?>
