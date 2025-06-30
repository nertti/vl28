<?php /** @var $block array */ ?>
<?php if (!empty($block['title']) && !empty($block['url'])) { ?>
    <a class="default__link default__link_small gray" <?php if (!empty($block['target'])){ ?>target="<?= $block['target'] ?>" <?php } ?> href="<?= $block['url'] ?>"><?= $block['title'] ?></a>
<?php } ?>
