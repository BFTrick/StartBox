<?php
/**
 * StartBox Hooks and Filters
 *
 * A collection of many of StartBox's default hooks, as well as some default filtration.
 *
 * @package StartBox
 * @subpackage Functions
 */

// Located in header.php
function sb_title() { do_action('sb_title'); } // The site title
function sb_before() { do_action('sb_before'); } // the very first thing inside <body>
function sb_before_header() { do_action('sb_before_header'); } // inside div#wrap, before div#header
function sb_header() { do_action('sb_header'); } // inside div#header, before any content
function sb_after_header() { do_action('sb_after_header'); } // inside div#wrap, after div#header
function sb_before_container() { do_action('sb_before_container'); } // inside div#container_wrap, before div#container

// Located in front-page.php
function sb_before_featured() { do_action('sb_before_featured'); } // Located just after sb_before_content
function sb_featured() { do_action('sb_featured'); } // Located just after sb_before_featured
function sb_after_featured() { do_action('sb_after_featured'); } // Located just after sb_featured
function sb_home() { do_action('sb_home'); } // Located just after sb_after_featured

// Located in 404.php, archive.php, attachment.php, author.php, category.php, index.php, page.php and all other page templates, search.php, single.php, tag.php
function sb_before_content() { do_action('sb_before_content'); } // Just before the content
function sb_page_title() { do_action('sb_page_title'); } // The Page Title, appears immediately after sb_before_content
function sb_after_content() { do_action('sb_after_content');} // Just after the content

// Located in loop.php
function sb_before_post() { do_action('sb_before_post'); global $firstpost; if ( !isset( $firstpost ) ) { do_action('sb_before_first_post'); } } // Just before the post
function sb_after_post() { do_action('sb_after_post'); global $firstpost; if ( !isset( $firstpost ) ) { do_action('sb_after_first_post'); $firstpost = 1; } } // Just after the post

// Located in loop.php and single.php
function sb_before_post_content() { do_action('sb_before_post_content'); } // Inside div.post, after .entry-header, before .entry-content
function sb_post_header() { do_action('sb_post_header' ); } // Inside div.entry-meta
function sb_post_footer() { do_action('sb_post_footer' ); } // Inside div.entry-footer
function sb_after_post_content() { do_action('sb_after_post_content'); } // Inside div.post, after .entry-content, before .entry-footer

// Located in 404.php
function sb_404() { do_action('sb_404'); } // Inside div.post, only on 404 page

// Located in sidebar.php
function sb_no_widgets() { do_action('sb_no_widgets'); } // This is hooked into all sb_no_*_widget hooks

// Located in sidebar-tertiary.php
function sb_before_tertiary_widgets() { do_action('sb_before_tertiary_widgets'); } // Inside div#container, after div#content, before div#tertiary
function sb_after_tertiary_widgets() { do_action('sb_after_tertiary_widgets'); } // Inside div#container, after div#tertiary

// Located in sidebar-footer.php
function sb_before_footer_widgets() { do_action('sb_before_footer_widgets'); } // inside div#footer, before div#footer_sidebar
function sb_between_footer_widgets() { do_action('sb_between_footer_widgets'); } // inside div#footer_sidebar, between each div.aside
add_action( 'sb_before_footer_widget_area_2_widgets', 'sb_between_footer_widgets' );
add_action( 'sb_before_footer_widget_area_3_widgets', 'sb_between_footer_widgets' );
add_action( 'sb_before_footer_widget_area_4_widgets', 'sb_between_footer_widgets' );
function sb_after_footer_widgets() { do_action('sb_after_footer_widgets'); } // inside div#footer, after div#footer_sidebar

// Located in footer.php
function sb_after_container() { do_action('sb_after_container'); } // inside div#container_wrap, after div#container
function sb_between_content_and_footer() { do_action('sb_between_content_and_footer'); } // after div#wrap, before div#footer_wrap
function sb_before_footer() { do_action('sb_before_footer'); } // inside div#footer_wrap, before div#footer
function sb_footer() { do_action('sb_footer'); } // inside div#footer after div#footer_sidebar
function sb_after_footer() { do_action('sb_after_footer'); } // inside dive#footer_wrap, after div#footer
function sb_after() { do_action('sb_after'); } // the very last thing before </body>

////////////////////////////////////////////////// Items To Hook into Header //////////////////////////////////////////////////

// Header Wrap
function sb_header_wrap() {
	if ( !did_action( 'sb_header') )
		echo '<div id="header_wrap">'."\n";
	else
		echo '</div><!-- #header_wrap -->'."\n";
}
add_action( 'sb_before_header', 'sb_header_wrap', 999 );
add_action( 'sb_after_header', 'sb_header_wrap', 9 );

// The default site title
function sb_default_title( $title, $sep, $seplocation) {
	$site_name = get_bloginfo('name');
	$sep = ' | ';

	if ( is_home() || is_front_page() ) { $title = get_bloginfo('description'); }
	elseif ( is_singular() || is_page() ) { $title = single_post_title( '', false ); }
	elseif ( is_search() ) { $title = sprintf( __('Search Results for: %s', 'startbox'), esc_html(stripslashes(get_search_query())) ); }
    elseif ( is_category() ) { $title = sprintf( __('Category Archives: %s', 'startbox'), single_cat_title( '', false )); }
    elseif ( is_tag() ) { $title = sprintf( __('Tag Archives: %s', 'startbox'), sb_tag_query() ); }
	elseif ( is_404() ) { $title = __( 'Not Found', 'startbox' ); }
	else { $title = get_bloginfo('description'); }
	
	// Appends current page number (if on page 2 or greater)
    if ( get_query_var('paged') ) { $title .= $sep . sprintf( __( 'Page %s', 'startbox' ), get_query_var('paged') ); }

	if ( is_home() || is_front_page() ) {
		$title = array(
			'site_name' => $site_name,
			'separator' => $sep,
			'title' => $title
		);
	} else {
		$title = array(
			'title' => $title,
			'separator' => $sep,
			'site_name' => $site_name
		);
	}

    // Filters should return an array
    $title = apply_filters('sb_doctitle', $title);

	if( is_array( $title ) )
		$title = implode('', $title);

	return $title;
}
add_filter( 'wp_title', 'sb_default_title', 9, 3 );
add_action( 'sb_title', 'wp_title' );

// Filter the RSS title to return nothing, otherwise RSS shows dupilicate title
add_filter( 'wp_title_rss', create_function( '$a', 'return "";' ) );

// The default stylesheet
function sb_default_stylesheet() {
	if (!is_admin())
	wp_enqueue_style( 'style', get_stylesheet_uri(), null, null, 'screen' );
}
add_action( 'wp_enqueue_scripts', 'sb_default_stylesheet', 15 );

// Insert Top anchor
function sb_topofpage() {
	echo '<a name="top"></a>'."\n";
}
add_action('sb_before', 'sb_topofpage', 1);

// Insert skip-to-content link
function sb_skip_to_content() {
	echo '<a href="#content" title="Skip to content" class="skip-to-content">' . __( 'Skip to content', 'startbox' ) . '</a>'."\n";
}
add_action('sb_before','sb_skip_to_content');

// Insert Yoast Breadcrumbs if Active
function sb_breadcrumb_output() {
	if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb('<div id="yoastbreadcrumb">','</div>'); }
}
add_action( 'sb_before_content', 'sb_breadcrumb_output', 15 );


////////////////////////////////////////////////// Items To Hook into home page //////////////////////////////////////////////////

function sb_home_featured_sidebar() {
	sb_do_sidebar( 'featured_aside', 'home_featured', 'featured-aside' );
}
add_action('sb_featured','sb_home_featured_sidebar');

function sb_home_content() {
	while ( have_posts() ) : the_post();
		if (is_page()) { ?>
			<h2 class="page-title"><?php the_title(); ?></h2>
				<div class="entry-content">
					<?php the_content() ?>
					<?php edit_post_link(__('Edit', 'startbox'),'<span class="edit-link">','</span>') ?>
				</div><!-- .entry-content -->
		<?php }
		else { get_template_part( 'loop', 'home' ); }
	endwhile;
}
add_action('sb_home','sb_home_content');

////////////////////////////////////////////////// Items To Hook into content areas //////////////////////////////////////////////////

// Filter the page title (credit: Ian Stewart)
function sb_default_page_title() {
	global $post;
	$container = apply_filters( 'sb_page_title_container', 'h1' );
	
	$content = '<' . $container . ' class="page-title">';
	if (is_attachment()) {
		$content .= '<a href="';
		$content .= get_permalink($post->post_parent);
		$content .= '" rev="attachment"><span class="meta-nav">&laquo; </span>';
		$content .= get_the_title($post->post_parent);
		$content .= '</a>';
	} elseif ( is_singular() ) {
		$content .= get_the_title();
	} elseif (is_author()) {
		$content .= '<span>';
		$content .= __('Author Archives: ', 'startbox');
		$content .= '</span> ';
		$content .= get_the_author();
	} elseif (is_category()) {
		$content .= '<span>';
		$content .= __('Category Archives:', 'startbox');
		$content .= '</span> ';
		$content .= single_cat_title('', FALSE);
	} elseif (is_search()) {
		$content .= '<span>';
		$content .= __('Search Results for:', 'startbox');
		$content .= '</span> <span id="search-terms">';
		$content .= esc_html(stripslashes($_GET['s']), true);
		$content .= '</span>';
	} elseif (is_tag()) {
		$content .= '<span>' . __('Tag Archives:', 'startbox') . '</span> ' . sb_tag_query();
	} elseif (is_tax()) {
		$term = get_term_by('slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$content .= '<span>' . __('Archives:', 'startbox') . '</span> ' . $term->name;
	} elseif (is_day()) {
		$content .= sprintf(__('<span>Daily Archives:</span> %s', 'startbox'), get_the_time(get_option('date_format')));
	} elseif (is_month()) {
		$content .= sprintf(__('<span>Monthly Archives:</span> %s', 'startbox'), get_the_time('F Y'));
	} elseif (is_year()) {
		$content .= sprintf(__('<span>Yearly Archives:</span> %s', 'startbox'), get_the_time('Y'));
	} elseif (is_404()) {
		$content .= __('404 - File Not Found', 'startbox');
	} elseif (is_post_type_archive()) {
		$content .= '<span>' . __('Content Archives:', 'startbox') . '</span> ' . post_type_archive_title( '', false );
	} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
		$content .= __('Blog Archives', 'startbox');
	}
	$content .= '</' . $container . '>';
	$content .= "\n";
	
	echo apply_filters('sb_default_page_title', $content, $container, $post );
}
add_action( 'sb_page_title', 'sb_default_page_title' );

function sb_archive_meta() {
	global $post;
	if ( is_category() || is_tag() && !( category_description() == '' || tag_description() == '' ) ) {
		$content = '<div class="archive-meta">';
		if ( !( category_description() == '' ) ) { $content .= apply_filters('sb_archive_meta', category_description()); }
		elseif ( !( tag_description() == '' ) ) { $content .= apply_filters('sb_archive_meta', tag_description()); }
		$content .= '</div>';
		echo $content;
	}
}
add_action( 'sb_page_title', 'sb_archive_meta' );

// Default 404 Page
function sb_404_content() { 
	echo '<p>' . sprintf( __('Sorry, but we were unable to find what you were looking for. Try searching or browsing our content below. If you still can\'t find it, %sContact Us%s and we\'ll try to&nbsp;help!', 'startbox' ), '<a href="' . apply_filters( 'sb_404_contact', home_url() . '/contact/' ) . '">', '</a>' ) . '</p>';
	get_template_part( 'searchform' );
	echo '<br/>';
	sb_sitemap();
}
add_action( 'sb_404', 'sb_404_content' );

////////////////////////////////////////////////// Items To Hook into Sidebars //////////////////////////////////////////////////

function sb_do_primary_widgets() {
	if ( is_sidebar_active('primary_widget_area') || has_action('sb_no_primary_widgets') ) { ?>
	<div id="primary" class="aside primary-aside">
		<ul class="xoxo">
			
			<?php if ( !dynamic_sidebar('primary-aside') ) { sb_no_primary_widgets(); } ?>
			
		</ul>
	</div><!-- #primary .aside -->
<?php }
}
add_action( 'sb_primary_widgets', 'sb_do_primary_widgets' );

function sb_do_secondary_widgets() {
	if ( is_sidebar_active('secondary_widget_area') || has_action('sb_no_secondary_widgets') ) { ?>
		<div id="secondary" class="aside secondary-aside">
			<ul class="xoxo">

				<?php if ( !dynamic_sidebar('secondary-aside') ) { sb_no_secondary_widgets(); } ?>

			</ul>
		</div><!-- #secondary .aside -->
	<?php }
}
add_action( 'sb_secondary_widgets', 'sb_do_secondary_widgets' );

// Hook sb_no_widgets to all default widget areas, but only if it's active
function sb_no_widgets_active() {
	if ( has_action( 'sb_no_widgets' ) ) {
		add_action( 'sb_no_primary_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_secondary_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_featured_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_footer_aside_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_footer_aside_2_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_footer_aside_3_widgets', 'sb_no_widgets' );
		add_action( 'sb_no_footer_aside_4_widgets', 'sb_no_widgets' );
	}
}
add_action( 'init', 'sb_no_widgets_active' );

////////////////////////////////////////////////// Items To Hook into Footer //////////////////////////////////////////////////

// Auto-hide the address bar in mobile Safari (iPhone)
function sb_iphone() { echo '<script type="text/javascript">window.scrollTo(0, 1);</script>'; }
add_action('sb_after','sb_iphone');

// Add left/right hooks
function sb_footer_left_right() {
	if ( has_action( 'sb_footer_left' ) ) { echo '<div id="footer_left" class="left">'; do_action( 'sb_footer_left' ); echo '</div><!-- #footer_left -->'; }
	if ( has_action( 'sb_footer_right' ) ) { echo '<div id="footer_right" class="right">'; do_action( 'sb_footer_right' ); echo '</div><!-- #footer_right -->'; }
}
add_action( 'sb_footer', 'sb_footer_left_right', 15 );

?>