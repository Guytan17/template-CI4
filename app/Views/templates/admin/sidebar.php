<nav id="sidebar" class="navbar navbar-expand-lg bg-body-secondary flex-row align-items-start p-3">
    <div id="top-sidebar" class="d-flex justify-content-between align-items-center">
        <img src="<?= base_url('assets/img/logo.png'); ?>" alt="Logo" class="img-fluid" style="width: 30px; height: auto">
        <a class="navbar-brand" href="#">Navbar</a>
        <div id="toggle-sidebar" data-collapse="false" data-bs-toggle="tooltip" data-bs-placement="right" title="Toggle Sidebar">
            <i class="fas fa-arrow-left" id="sidebarCollapse"></i>
        </div>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse flex-column w-100" id="navbarNavDropdown">
        <ul class="navbar-nav flex-column w-100 align-self-start flex-grow-1">
            <?php foreach($menus as $menu) : ?>
                <?php if (isset($menu['title']) && isset($menu['url'])) :
                    //Lien dropdown
                    if(isset($menu['subs'])) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $menu['class'] ?? '';?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if(isset($menu['icon'])) : ?>
                                    <i class="<?= $menu['icon']; ?>"></i>
                                <?php endif; ?>
                                <span class="link-text"><?= $menu['title']; ?></span>
                            </a>
                            <ul class="dropdown-menu ps-2 py-0">
                                <?php
                                foreach($menu['subs'] as $submenu) :
                                    if (isset($submenu['title']) && isset($submenu['url'])) :
                                    ?>
                                        <li>
                                            <a href="<?= base_url($submenu['url']); ?>" class="nav-link">
                                                <?php if(isset($submenu['icon'])) : ?>
                                                    <i class="<?= $submenu['icon']; ?>"></i>
                                                <?php endif; ?>
                                                <span class="link-text"><?= $submenu['title']; ?></span>
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
                                <span class="link-text"><?= $menu['title']; ?></span>
                            </a>
                        </li>
                    <?php
                    endif;
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>
        <div class="navbar-nav sidebar-footer d-flex w-100 justify-content-between">
            <small class="text-secondary">v1.0.0</small>

            <div id="themeToggle" class="ms-2" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Tooltip on left">
                <i class="fas fa-lightbulb" style="cursor:pointer"></i>
            </div>
        </div>
    </div>
</nav>
