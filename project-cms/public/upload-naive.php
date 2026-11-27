<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It keeps the uploader's file name, keeps the file in the web root, and
// never looks at what the file is.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $name);
    echo "Uploaded: <a href=\"/uploads/$name\">$name</a>\n";
    exit;
}
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <button type="submit">Upload</button>
</form>
