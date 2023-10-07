<div style="background-color:#fff;border:2px solid #ccc;padding:10px;margin-top:20px;">
    <form method="POST" action="#">
        <label>dFlip Config for Newspaper: <select name="dflip_value">
            <?php foreach($dFlipConfigs as $config) { ?>
                <option <?php if($config->ID == $currentDFlipConfigId) echo 'selected'; ?> value="<?php echo $config->ID; ?>"><?php echo $config->post_title; ?></option>
            <?php } ?>
        </select></label>
        <?php if(!empty($mediaFolders)) { ?>
        <br/>
        <br/>
        <label>Media Folder Term assign: <select name="mediafolder">
            <?php foreach($mediaFolders as $folder) { ?>
                <option <?php if($folder->term_id == $currentMediaFolderId) echo 'selected'; ?> value="<?php echo $folder->term_id; ?>"><?php echo $folder->name; ?></option>
            <?php } ?>
        </select></label>
        <?php } ?>
        <br/>
        <br/>
        <input type="submit" name="submit" class="button button-primary" value="Speichern" />
    </form>
</div>