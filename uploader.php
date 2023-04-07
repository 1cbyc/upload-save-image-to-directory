<?php
if ($_FILES["image"]["error"] == 0) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file has been uploaded.";
    } else {
        echo "There was an error uploading the file.";
    }
} else {
    echo "There was an error uploading the file.";
}
?>
