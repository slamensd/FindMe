<?php include 'language.php'; ?>
<div id="scoreHolder">
	<div class="scoreInnerContent ignorePadding">
        <div class="title fontLink" data-fontsize="80"><?php echo $lang['submit-title']; ?></div>
        <div class="label fontLink" data-fontsize="30"><?php echo $lang['submit-name']; ?></div>
        <input id="uName" name="uName" type="text" class="fontLink" data-fontsize="25">
        <div class="forEmail">
            <div class="label fontLink" data-fontsize="30"><?php echo $lang['submit-email']; ?></div>
            <input id="uEmail" name="uEmail" type="text" class="fontLink" data-fontsize="25">
        </div>
        <div class="forMobile">
            <div class="label fontLink" data-fontsize="30"><?php echo $lang['submit-mobile']; ?></div>
            <input id="uMobile" name="uMobile" type="text" class="fontLink" data-fontsize="25">
        </div>
        <div class="action">
            <div id="buttonSubmit" class="fontLink button" data-fontsize="35"><?php echo $lang['button-submit']; ?></div>
            <div id="buttonCancel" class="fontLink button" data-fontsize="35"><?php echo $lang['button-cancel']; ?></div>
        </div>
    </div>
</div>