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
    <?php if ($count % 2 == 0 || $count % 3 == 0): ?>
        <div class="default__gallery">
            <?php foreach ($images as $image): ?>
                <img class="default__img default__img_s<?php if ($count % 2 == 0): ?>50<?php else: ?>33<?php endif; ?>"
                     alt="<?= $image['DESCRIPTION'] ?>" src="<?= $image['SRC'] ?>">
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="swiper product">
            <div class="swiper-wrapper">
                <!-- Слайды -->
                <?php foreach ($images as $image): ?>
                    <img class="swiper-slide default__img"
                         alt="<?= $image['DESCRIPTION'] ?>" src="<?= $image['SRC'] ?>">
                <?php endforeach; ?>
            </div>

            <!-- Навигация -->
            <div class="swiper-button-prev">
                <svg class="arrow">
                    <use xlink:href="/local/templates/public/assets/img/arrows.svg#arrow-left"></use>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg class="arrow">
                    <use xlink:href="/local/templates/public/assets/img/arrows.svg#arrow-right"></use>
                </svg>
            </div>

            <!-- Пагинация -->
            <div class="swiper-pagination"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>
