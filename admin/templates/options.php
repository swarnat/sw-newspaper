<div style="background-color:#fff;border:2px solid #ccc;padding:10px;margin-top:20px;">
    <form method="POST" action="#">
        <label>dFlip Config for Newspaper: <select name="dflip_value">
            <?php foreach($dFlipConfigs as $config) { ?>
                <option <?php if($config->ID == $currentDFlipConfigId) echo 'selected'; ?> value="<?php echo $config->ID; ?>"><?php echo $config->post_title; ?></option>
            <?php } ?>
        </select></label>
        <br/>
        <br/>
        <input type="submit" name="submit" class="button button-primary" value="Speichern" />
    </form>
</div>