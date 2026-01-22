<?php
get_header();

$current_specs = isset($_GET['specialization']) ? explode(',', $_GET['specialization']) : [];
$current_cities = isset($_GET['city']) ? explode(',', $_GET['city']) : [];



$specs_args = [
    'taxonomy'   => 'specialization',
    'hide_empty' => true,
];

$city_args = [
    'taxonomy'   => 'city',
    'hide_empty' => true,
];



$all_cities = get_terms($city_args);
$all_specs =  get_terms($specs_args);
global $wp_query;

?>

<div class="container">

    <div class="row ">
    <aside>
        <form  action="javascript:void(0)" onsubmit="update_doctors_by_filter()">

            <div class="filter-group">
                <h4>Специализация</h4>
                <?php

                foreach ($all_specs as $term) {
                    $is_checked = in_array($term->name, $current_specs);
                    ?>
                    <label>
                        <input type="checkbox" class="filter-check" data-tax="specialization"
                               value="<?= $term->name ?>" <?= $is_checked ? 'checked' : '' ?> />
                        <?= $term->name ?>
                    </label><br>
                <?php } ?>
            </div>

            <!-- Блок Городов -->
            <div class="filter-group">
                <h4>Город</h4>
                <?php

                foreach ($all_cities as $term) {
                    $is_checked = in_array($term->name, $current_cities);
                    ?>
                    <label>
                        <input type="checkbox" class="filter-check" data-tax="city"
                               value="<?= $term->name ?>" <?= $is_checked ? 'checked' : '' ?> />

                        <?= $term->name ?>
                    </label><br>
                <?php } ?>
            </div>

            <input type="submit" value="Подобрать врачей">
        <?php if(isset($_GET['specialization']) or isset($_GET['city'])) {?>
           <br/> <a href="<?= get_post_type_archive_link('doctors'); ?>">Сбросить фильтры</a>
            <?php } ?>


        </form>
    </aside>


        <div class="doctors_wrap">
            <?php if ( have_posts() && $wp_query->found_posts > 1) : ?>
            <div class="sorting-group">
                <label for="doctor-sort">Сортировать:</label>
                <select id="doctor-sort" name="orderby">
                    <option value="date" <?php selected($_GET['orderby'] ?? '', 'date'); ?>>По умолчанию (дате)</option>
                    <option value="rating" <?php selected($_GET['orderby'] ?? '', 'rating'); ?>>По рейтингу (по уменьшению)</option>
                    <option value="price" <?php selected($_GET['orderby'] ?? '', 'price'); ?>>По цене (по увеличению)</option>
                    <option value="experience" <?php selected($_GET['orderby'] ?? '', 'experience'); ?>>По стажу (по увеличению)</option>
                </select>
            </div>

            <?php endif; ?>

        <?php if ( have_posts() ) : ?>
        <div class="row posts_row">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="doctor_card">
                        <a href="<?=get_permalink()?>" class="doctor_link">
                        <?php if ( has_post_thumbnail() ) : ?>
                        <div class="doctor_photo">
                            <?php the_post_thumbnail( 'doctor_image' ); ?>
                        </div>
                        <?php endif; ?>

                        <h3><?php the_title(); ?></h3>
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
                        </a>
                    </article>
                <?php endwhile; ?>
        </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p>По вашим параметрам врачи не найдены.</p>
        <?php endif; ?>


        </div>



    </div>


</div>

<?php
get_footer();

?>
