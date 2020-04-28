<form id='anony-share-email-form' method="post" autocomplete="on">
    <div id="anony-share-container">
        <div id="anony-close-share">
            <span><i class="fa fa-close"></i></span>
        </div>
        
        <h3><?= $titel ?></h3>
        <p><?= $subtitle ?></p>
        <input type="email" name="anony_share_email" placeholder="Email"/>
        <input type="hidden" name="anony_share_title" value="<?= $page_title ?>"/>
        <input type="hidden" name="anony_share_description" value="<?= $description ?>"/>
        <input type="hidden" name="anony_share_url" value="<?= $permalink ?>"/>
        <input type="hidden" name="anony_share_img" value="<?= $og_featured_image ?>"/>
        <a class="anony-email-share" href="#" id="button"><?= $submit_txt ?></a>
        <div id="anony-share-msg"></div>
    </div>
    
</form>