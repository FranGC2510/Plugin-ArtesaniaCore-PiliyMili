<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
    <h1>Control Pili & Mili</h1>

    <nav class="nav-tab-wrapper">
        <a href="?page=artesania-control&tab=modules" class="nav-tab <?php echo $active_tab === 'modules' ? 'nav-tab-active' : ''; ?>">Módulos</a>
        <a href="?page=artesania-control&tab=texts" class="nav-tab <?php echo $active_tab === 'texts' ? 'nav-tab-active' : ''; ?>">Textos</a>
        <a href="?page=artesania-control&tab=fiscal" class="nav-tab <?php echo $active_tab === 'fiscal' ? 'nav-tab-active' : ''; ?>">Fiscal</a>
    </nav>

    <div class="artesania-tab-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-top: 0;">
        <form method="post" action="options.php">
            <?php
            echo $tab_content;

            if ( $active_tab === 'modules' && ! current_user_can( 'administrator' ) ) {
                echo '<p class="description" style="margin-top:20px; font-weight: bold ">Solo el Administrador puede gestionar módulos.</p>';
            } else {
                submit_button();
            }
            ?>
        </form>
    </div>
</div>
