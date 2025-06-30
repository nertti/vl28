<?php /**
 * @var $block array
 * @var $this  SprintEditorBlocksComponent
 */ ?><?php
$images = Sprint\Editor\Blocks\Gallery::getImages(
    $block, [
    'width' => 600,
    'height' => 600,
    'exact' => 0,
], [
        'width' => 1024,
        'height' => 768,
        'exact' => 0,
    ]
);
$count = count($images);
?>

<?php if (!empty($images)): ?>
    <div class="default__gallery">
        <?php foreach ($images as $image): ?>
            <img class="default__img default__img_s<?php if ($count % 2 == 0): ?>50<?php else: ?>33<?php endif; ?>"
                 alt="<?= $image['DESCRIPTION'] ?>" src="<?= $image['SRC'] ?>">
        <?php endforeach; ?>
    </div>
<?php endif; ?>
