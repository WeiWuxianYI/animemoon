<form class="profile-input" style="padding-top: 0" enctype='multipart/form-data' method="post" action="">
    <img style="width: 100px;height:100px;display: block;margin-bottom:20px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;" src="<?=$showIcon?>">
    <label class="bt">
        <input name="profile-image" id="npii" type="file" style="height: 0;overflow: hidden;padding: 0">
        <span id="npii-s">Choose Image</span>
    </label>
    <script>
        $('#npii').on('change', function () {
            $('#npii-s').text(this.files[0].name);
        });
    </script>
    <button name="new-profile-image" type="submit">Save profile image</button>
</form>
<form class="profile-input" <?=($editMode ? 'method="post" action=""' : '')?>>
    <input name="accountUser" <?=($editMode ? 'required' : 'disabled')?> type="text" value="<?=$user?>" placeholder="Username">
    <input name="accountEmail" <?=($editMode ? 'required' : 'disabled')?> type="email" value="<?=$email?>" placeholder="Email Address">
        <button <?=($editMode ? 'type="submit" name="saveUserEmail"' : 'onclick="window.location.href=\''.$site_link.'/profile/page/settings/edit/\'" type="button"')?>><?=($editMode ? 'Save' : 'Edit')?></button>
        <?php if ($editMode) { ?>
        <button type="button" onclick="window.location.href='<?=$site_link.'/profile/page/settings/'?>'">Cancel</button><?php } ?>
</form>