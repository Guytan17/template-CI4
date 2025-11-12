<nav id="sidebar" class="navbar navbar-expand-lg bg-body-secondary p-3">
    <div class="container px-3">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <?php foreach($menus as $menu) : ?>
                    <?php if (isset($menu['title']) && isset($menu['url'])) :
                        //Lien dropdown
                        if(isset($menu['subs'])) : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= $menu['class'] ?? '';?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if(isset($menu['icon'])) : ?>
                                        <i class="<?= $menu['icon']; ?>"></i>
                                    <?php endif; ?>
                                    <?= $menu['title']; ?>
                                </a>
                                <ul class="dropdown-menu ps-4 py-0">
                                    <?php
                                    foreach($menu['subs'] as $submenu) :
                                        if (isset($submenu['title']) && isset($submenu['url'])) :
                                            ?>
                                            <li>
                                                <a href="<?= base_url($submenu['url']); ?>" class="nav-link">
                                                    <?php if(isset($submenu['icon'])) : ?>
                                                        <i class="<?= $submenu['icon']; ?>"></i>
                                                    <?php endif; ?>
                                                    <?= $submenu['title']; ?>
                                                </a>
                                            </li>
                                        <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            </li>
                        <?php
                        else : //Lien simple
                            ?>
                            <li class="nav-item">
                                <a href="<?= base_url($menu['url']); ?>" class="nav-link <?= $menu['class'] ?? '';?>">
                                    <?php if(isset($menu['icon'])) : ?>
                                        <i class="<?= $menu['icon']; ?>"></i>
                                    <?php endif; ?>
                                    <?= $menu['title']; ?>
                                </a>
                            </li>
                        <?php
                        endif;
                        ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="ms-auto" id="themeToggle">
                <i class="fas fa-lightbulb" style="cursor:pointer"></i>
            </div>
        </div>
    </div>

</nav>