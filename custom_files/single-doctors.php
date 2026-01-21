<?php
get_header();



?>
<div class="container">
    <div class="doctor_page">
       <h1> <?php the_title()?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="doctor_page_image">
        <?php the_post_thumbnail( 'full' ); ?>
        </div>
        <?php endif; ?>

        <div class="doctor_meta_data">

            <?php if ( $specs = get_the_term_names( get_the_ID(), 'specialization' ) ) : ?>
                <p>Специализация: <?= $specs; ?></p>
            <?php endif; ?>

            <?php if ( $city = get_the_term_names( get_the_ID(), 'city' ) ) : ?>
                <p>Город: <?= $city; ?></p>
            <?php endif; ?>

            <?php

            display_doctor_info('experience', 'Стаж', ['год', 'года', 'лет']);
            display_doctor_info('price', 'Цена от', ' руб.');
            display_doctor_info('rating', 'Рейтинг');

            ?>

        </div>

        <div class="doctor_post_content">
            <?php the_content(); ?>
        </div>




    </div>
</div>
<?php
get_footer();

?>
