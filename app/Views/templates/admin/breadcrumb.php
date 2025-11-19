<?php if (count($breadcrumb) > 0)  { ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <?php foreach ($breadcrumb as $bitem) { ?>
                <?php if (isset($bitem['url']) && $bitem['url'] !== "") { ?>
                    <li class="breadcrumb-item">
                        <a class="link-underline link-underline-opacity-0" href="<?= base_url($bitem['url']) ?>" class=""><?= $bitem['text'] ?></a>
                    </li>
                <?php } else { ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= $bitem['text'] ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ol>
    </nav>
<?php } ?>