<?php
    et_get_mobile_header();
?>
<section class="section-wrapper section-project section-archive-project">
    <div class="list-link-tabs-page">
        <div class="container">
            <a href="<?php echo get_post_type_archive_link(PROJECT) ?>" class="active"><?php _e("Projects", ET_DOMAIN); ?></a>
            <a href="<?php echo get_post_type_archive_link(PROFILE) ?>"><?php _e("Profiles", ET_DOMAIN); ?></a>
        </div>
    </div>
    <div class="project-wrapper">
        <div class="search-normal-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="search-wrap">
                            <span class="icon-form-search icon-search"></span>
                            <input type="text" name="s" value="" placeholder="<?php _e("Type keyword", ET_DOMAIN); ?>" class="search-normal-input keyword search">
                        </div>
                    </div>
                    <div class="col-xs-4"><a href="#" class="show-search-advance"><?php _e("Advanced", ET_DOMAIN); ?></a></div>
                    <div class="col-xs-4"><a href="#" class="hide-search-advance" style="display:none;"><?php _e("Cancel", ET_DOMAIN); ?></a></div>
                </div>
            </div>
            <div class="container" id="advance-search" style="display:none; margin-top: 5px;">
                <form class="form-search-wrapper">
                    <div class="form-group">
                        <label><?php _e("Source Language", ET_DOMAIN); ?></label>
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
                    </div>
                    <div class="form-group">
                        <label><?php _e("Target Language", ET_DOMAIN); ?></label>
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
                    </div>
                    <div class="form-group">
                        <label><?php _e("Category", ET_DOMAIN); ?></label>
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
                    </div>
                    <div class="form-group">
                        <label><?php _e("Keyword", ET_DOMAIN); ?></label>
                        <div class="skill-control">
                            <input class="form-control keyword search" type="text" id="s" placeholder="<?php _e("Keyword", ET_DOMAIN); ?>" name="s"  autocomplete="off" spellcheck="false" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php _e("Fields", ET_DOMAIN); ?></label>
                        <div class="skill-control">
                            <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Type and enter", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
                            <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                            <ul class="skills-list" id="skills_list"></ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" value="<?php _e("HIDE ADVANCED SEARCH", ET_DOMAIN); ?>" class="hide-advance-search btn-sumary btn-search-advance">
                    </div>
                </form>
            </div>
        </div>
        
        <div class="list-project-wrapper">
            <?php
                query_posts(array('post_type' => PROJECT, 'post_status' => 'publish')) ;
                get_template_part('mobile/list', 'projects'); 
                wp_reset_query();
            ?>
        </div>
        <script type="text/template" id="project-no-result">
            <div class="col-md-12 no-result">
                <p class="alert alert-info">
                    <i class="fa fa-info-circle"></i>&nbsp;<?php _e("Sorry, no results were found.", ET_DOMAIN); ?>
                </p>
            </div>  
        </script>
    </div>
</section>
<?php
    et_get_mobile_footer();
?>