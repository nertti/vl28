<section class="content">
    <?php $APPLICATION->IncludeFile(
        "/include/main/single_banner.php",
        array(),
        array(
            "MODE" => "text"
        )
    ); ?>
    <div class="container">
        <?php $APPLICATION->IncludeFile(
            "/include/main/under_single_banner.php",
            array(),
            array(
                "MODE" => "text"
            )
        ); ?>
    </div>
</section>