<div class="profile-bar">
    <a class="<?=(!isset($pageM) ? "active" : "")?>" href="<?=$site_link."/profile/".(isset($userToView) ? "$userToView/" : "")?>">Playlists</a>
    <a class="<?=(isset($pageM) && $pageM == "favorites" ? "active" : "")?>" href="<?=$site_link."/profile/".(isset($userToView) ? "$userToView/" : "")."page/favorites/"?>">Favorites</a>
    <a class="<?=(isset($pageM) && $pageM == "watched" ? "active" : "")?>" href="<?=$site_link."/profile/".(isset($userToView) ? "$userToView/" : "")."page/watched/"?>">Watched</a>
    <a class="<?=(isset($pageM) && $pageM == "watch-later" ? "active" : "")?>" href="<?=$site_link."/profile/".(isset($userToView) ? "$userToView/" : "")."page/watch-later/"?>">Watch Later</a>
    <a class="<?=(isset($pageM) && $pageM == "likes" ? "active" : "")?>" href="<?=$site_link."/profile/".(isset($userToView) ? "$userToView/" : "")."page/likes/"?>">Likes/Dislikes</a>
    <?php if (!isset($userToView)) { ?>
    <a class="<?=(isset($pageM) && $pageM == "settings" ? "active" : "")?>" href="<?=$site_link."/profile/page/settings/"?>">Settings</a>
    <a href="<?=$site_link?>/profile/index.php?logout=true">Logout</a>
    <?php } ?>
</div>