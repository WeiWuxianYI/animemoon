<div class="sidebar">
    <a href="<?=$site_link.'/index.php'?>" class="button <?=(($TITLE ?? '') === 'KITSU' ? 'active' : '')?>">
        <i class="fas fa-code-branch b-icon"></i>
        <span>Kitsu API</span>
    </a>
    <a href="<?=$site_link.'/upload.php'?>" class="button  <?=(($TITLE ?? '') === 'UPLD' ? 'active' : '')?>">
        <i class="fas fa-upload b-icon"></i>
        <span>Upload Image</span>
    </a>
    <a href="<?=$site_link.'/add-series.php'?>" class="button <?=(($TITLE ?? '') === 'ADD-SERIES' ? 'active' : '')?>">
        <i class="fas fa-plus b-icon"></i>
        <span>Add Series</span>
    </a>
    <a href="<?=$site_link.'/add-episode.php'?>" class="button <?=(($TITLE ?? '') === 'ADD-EPS' ? 'active' : '')?>">
        <i class="fas fa-plus b-icon"></i>
        <span>Add Episode</span>
    </a>
    <a href="<?=$site_link.'/edit-episode.php'?>" class="button <?=(($TITLE ?? '') === 'EDIT-EPS' ? 'active' : '')?>">
        <i class="fas fa-pencil-alt b-icon"></i>
        <span>Edit Episode</span>
    </a>
    <a href="<?=$site_link.'/delete-episode.php'?>" class="button <?=(($TITLE ?? '') === 'DEL-EPS' ? 'active' : '')?>">
        <i class="far fa-trash-alt b-icon"></i>
        <span>Delete Episode</span>
    </a>
    <a href="?logout=true" class="button">
        <i class="fas fa-sign-out-alt b-icon"></i>
        <span>Logout</span>
    </a>
</div>