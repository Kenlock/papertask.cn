<?php
	et_get_mobile_header();
?>
<section class="section-wrapper section-profile section-archive-profile">
	<div class="list-link-tabs-page">
    	<div class="container">
            <a href="<?php echo get_post_type_archive_link(PROJECT) ?>" ><?php _e("Projects", ET_DOMAIN); ?></a>
            <a href="<?php echo get_post_type_archive_link(PROFILE) ?>" class="active"><?php _e("Profiles", ET_DOMAIN); ?></a>
        </div>
    </div>
    <!-- <div class="advanced-search-wrapper">
        <div class="search-normal-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-8">
                        <span class="title-advance-search"><?php _e("Search Advanced", ET_DOMAIN); ?></span>
                    </div>
                    <div class="col-xs-4"><a href="#" class="hide-search-advance"><?php _e("Cancel", ET_DOMAIN); ?></a></div>
                </div>
            </div>
        </div>

    </div> -->
    <div class="profiles-wrapper">
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
                        <label><?php _e("Keyword", ET_DOMAIN); ?></label>
                        <div class="skill-control">
                            <input class="form-control keyword search" type="text" id="s" placeholder="<?php _e("Keyword", ET_DOMAIN); ?>" name="s"  autocomplete="off" spellcheck="false" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php _e("Skills Required", ET_DOMAIN); ?></label>
                        <div class="skill-control">
                            <input type="text" class="form-control skill" id="skill" placeholder="<?php _e("Keyword", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" />
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

        <div class="list-profiles-wrapper">
            <?php get_template_part('mobile/list', 'profiles'); ?>
        </div>
        <script type="text/template" id="profile-no-result">
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