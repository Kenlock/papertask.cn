<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get(PROFILE);
$currency    = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
?>
<div class="header-sub-wrapper header-sub-wrapper-tan">
    <div class="container box-shadow-style-theme search-form-top">
        <div class="row">
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Source Language', ET_DOMAIN); ?></h2>
                    <p>
                        <?php
                            ae_tax_dropdown( 'sourcelanguage' ,
                                  array(  'attr' => 'data-chosen-width="90%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                          'class' => 'cat-filter chosen-select',
                                          'hide_empty' => false,
                                          'hierarchical' => true ,
                                          'id' => 'sourcelanguage' ,
                                          'show_option_all' => __("Source Language", ET_DOMAIN),
                                          'value' => 'slug'
                                      )
                            );
                        ?>
                    </p>
                </div>
            </div>
			
			<div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Target Language', ET_DOMAIN); ?></h2>
                    <p>
                        <?php
                            ae_tax_dropdown( 'targetlanguage' ,
                                  array(  'attr' => 'data-chosen-width="90%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                          'class' => 'cat-filter chosen-select',
                                          'hide_empty' => false,
                                          'hierarchical' => true ,
                                          'id' => 'targetlanguage' ,
                                          'show_option_all' => __("Target Language", ET_DOMAIN),
                                          'value' => 'slug'
                                      )
                            );
                        ?>
                    </p>
                </div>
            </div>

            <div class="col-md-15 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Category', ET_DOMAIN); ?></h2>
                    <p>
                        <?php
                            ae_tax_dropdown( 'project_category' ,
                                  array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                          'class' => 'cat-filter chosen-select',
                                          'hide_empty' => false,
                                          'hierarchical' => true ,
                                          'id' => 'project_category' ,
                                          'show_option_all' => __("All categories", ET_DOMAIN),
                                          'value' => 'slug'
                                      )
                            );
                        ?>
                    </p>
                </div>
            </div>

            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top"><?php _e('Fields', ET_DOMAIN) ?></h2>
                        <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Type and enter", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
                        <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                        <ul class="skills-list" id="skills_list"></ul>
                    </div>
                </div>
            </div>

            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top"><?php _e('Keyword', ET_DOMAIN) ?></h2>
                        <input class="form-control keyword search" type="text" id="s" placeholder="<?php _e("Keyword", ET_DOMAIN); ?>" name="s"  autocomplete="off" spellcheck="false" >
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="number-project-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="number-project">

                    <?php
                        $found_posts = '<span class="found_post">'.$wp_query->found_posts.'</span>';
                        $plural = sprintf(__('%s Projects for you',ET_DOMAIN), $found_posts);
                        $singular = sprintf(__('%s Project for you',ET_DOMAIN),$found_posts);
                    ?>
                        <span class="plural <?php if( $wp_query->found_posts <= 1 ) { echo 'hide'; } ?>" >
                            <?php echo $plural; ?>
                        </span>
                        <span class="singular <?php if( $wp_query->found_posts > 1 ) { echo 'hide'; } ?>">
                            <?php echo $singular; ?>
                        </span>
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>
