<?php get_header(); $q = get_queried_object(); $subs = get_terms(['taxonomy' => 'product_cat', 'parent' => $q->term_id, 'hide_empty' => true]); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<div class="lay">
  <aside class="lay__s"><h4>Danh mục</h4><ul class="cats cats--s"><?php foreach (icd_top_cats() as $t) : $kids = icd_cat_kids($t->term_id); ?><li><a class="<?php echo $t->term_id === $q->term_id || $t->term_id === $q->parent ? 'on' : ''; ?>" href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a><?php if ($kids) : ?><ul class="cats__kids"><?php foreach ($kids as $c) : ?><li><a class="<?php echo $c->term_id === $q->term_id ? 'on' : ''; ?>" href="<?php echo esc_url(get_term_link($c)); ?>"><span><?php echo esc_html($c->name); ?></span><em><?php echo (int) $c->count; ?></em></a></li><?php endforeach; ?></ul><?php endif; ?></li><?php endforeach; ?></ul></aside>
  <div class="lay__m">
    <header class="ph"><h1><?php echo esc_html($q->name); ?></h1><p class="ph__c"><?php echo (int) $wp_query->found_posts; ?> sản phẩm</p>
      <?php if ($subs) : ?><div class="chips"><?php foreach ($subs as $s) echo '<a href="' . esc_url(get_term_link($s)) . '">' . esc_html($s->name) . '</a>'; ?></div><?php endif; ?></header>
    <?php icd_sortbar(); if (have_posts()) : ?><div class="grid grid--4 grid--box"><?php while (have_posts()) { the_post(); icd_card(); } ?></div>
      <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '‹', 'next_text' => '›']); ?>
    <?php else : ?><p>Chưa có sản phẩm trong danh mục này.</p><?php endif; ?>
    <?php if ($q->description && !is_paged()) : ?><div class="prose prose--box prose--desc"><?php echo wp_kses_post(wpautop(icd_pretty_content($q->description))); ?></div><?php endif; ?>
  </div>
</div>
<?php icd_callback("cb--page"); icd_cta(); icd_related_posts(); ?></div>
<?php get_footer();
