<?php
$TITLE = 'UPLD';
require_once 'private/config.php';

?>
<html>
<head>
    <?php require_once 'private/meta.php'; ?>
</head>
<body>
<?php require_once 'private/sidebar.php'?>
<div class="panel">
    <div class="panel-title">Upload</div>
    <form action="<?=$site_link?>/api/upload.php" method="post" class="panel-inputs" enctype='multipart/form-data' target='_blank'>
        <label class="inputFile">
            <input type="file" name='fileToUpload' required>
            <span class="btn">Upload</span>
            <span class="pth">No file chosen</span>
        </label>
        <button type="submit">Add</button>
    </form>
</div>
<script>
    $('.inputFile').on('change', 'input', function() {
        let file = $(this);
        let path = file.val().toString().split('\\');
        $(this).parent().find('.pth').text(path[path.length - 1]);
    });
</script>
</body>
</html>