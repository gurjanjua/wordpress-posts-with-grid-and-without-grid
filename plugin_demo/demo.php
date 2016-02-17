<?php ob_start(); error_reporting(0); ?>
<?php

/**
* Plugin Name: Demo
* Description: use [demo] as a shortcode
* Version: 1.0.0
* Author: Demo
*/
register_activation_hook( __FILE__, 'create_db' );
function create_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'add_relationship';

	$sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
		post_id int NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}
add_action('widgets_init', function(){
	register_widget('My_Widget');
});

add_action('wp_head','addBreadcrumb');
function addBreadcrumb() {
	if(is_single()){
		?>
		<style>
			.breadcrumb,.breadcrumbSaved{padding:10px 15px;margin:0 0 10px 0;border-radius:3px;}
			.breadcrumb{background: #e4e4e4;}
			.breadcrumbSaved{background: #f6f6f6;overflow: auto;max-height: 78px;}
			.padRemove{padding-top: 1px !important;}
		</style>
		<script>
			var pageTitle = "<?php echo get_the_title(); ?>";
			var checkcrumb = sessionStorage.getItem('breadcrumb');
			var checkSaved = sessionStorage.getItem('breadcrumbSaved');
			if(checkSaved!=null){
				var breadcrumbSaved = checkSaved;
			} else {
				var breadcrumbSaved = '';
			}
			var findstr = checkcrumb.search(pageTitle);
			if(findstr>-1){
				var breadcrumb = checkcrumb;
			} else {
				var breadcrumb = checkcrumb + ' > ' + pageTitle;
				sessionStorage.setItem('breadcrumb',breadcrumb);
			}
			jQuery(document).ready(function(){
				if(breadcrumbSaved!=''){
					jQuery('.post.type-post').addClass('padRemove');
					jQuery('.post.type-post').prepend("<div class='breadcrumbArea' style='margin:12px;'><p class='breadcrumb'>"+breadcrumb+"</p><p class='breadcrumbSaved'>"+breadcrumbSaved+"</p></div>");
				} else {
					jQuery('.post.type-post').addClass('padRemove');
					jQuery('.post.type-post').prepend("<div class='breadcrumbArea' style='margin:12px;'><p class='breadcrumb'>"+breadcrumb+"</p></div>");
				}
			});
			jQuery(document).ready(function(){
				jQuery('#lang_choice_polylang-6').change(function(){
					sessionStorage.removeItem('breadcrumb');
					sessionStorage.removeItem('breadcrumbSaved');
				});
			});
		</script>
		<?php 
	}
}

class My_Widget extends WP_Widget {

	function __construct() {
		parent::__construct('My_Widget', __('My Widget', 'text_domain'),array( 'description' => __( 'My first widget!', 'text_domain' ), ));
	}

	public function widget() {

		$HostName = 'http://'.$_SERVER['HTTP_HOST'].'/wordpressplugin/';

		$args = array(
			'orderby' => 'name',
			'parent' => 0,
			'taxonomy' => 'category',
			'hide_empty' => 0 ,
			);

		$categoriesSeen = get_categories( $args );
		foreach ($categoriesSeen as $value) {

			$term_id = $value->term_id;
			$name = $value->name;
			$name = strtolower($name);
			$args = array(
				'orderby' => 'name',
				'parent' => $term_id,
				'taxonomy' => 'category',
				'hide_empty' => 0 ,
				);

			$categoriesUnseen = get_categories( $args );
			foreach ($categoriesUnseen as $category) {
				?>
				<div style="margin-left:30px; margin-right:30px;">
					<?php if($name=="seen" || $name=="גלוי") { ?>
					<?php if($category->count>0) { ?>
					<div><a href="<?php echo $HostName; ?>?term_id=<?php echo $category->term_id; ?>&type=seen&showposts=true&levelStatus=parent"><?php echo $category->name; ?></a></div>
					<?php } else {  ?>
					<div><a href="<?php echo $HostName; ?>?term_id=<?php echo $category->term_id; ?>&type=seen&levelStatus=parent"><?php echo $category->name; ?></a></div>
					<?php } ?>
					<?php } else { ?>
					<div><a href="<?php echo $HostName; ?>?term_id=<?php echo $category->term_id; ?>&type=unseen&&levelStatus=parent"><?php echo $category->name; ?></a></div>
					<?php }	?>
				</div>
				<?php
			}
		} 

		$args = array(
			'orderby' => 'name',
			'parent' => 0,
			'taxonomy' => 'category',
			'hide_empty' => 0 ,
			);
		$categories = get_categories( $args );
		foreach ( $categories as $category )
		{
			$term_id=$category->term_id;
			$args = array(
				'orderby' => 'name',
				'parent' => $term_id,
				'taxonomy' => 'category',
				'hide_empty' => 0 ,
				);
			$category = get_categories( $args );
			foreach ( $category as $cat )
			{
				$name=$cat->name;
				if($name=="Body Systems" || $name=="מערכות גוף")
				{
					$term_id=$cat->term_id;
					$args = array(
						'orderby' => 'name',
						'parent' => $term_id,
						'taxonomy' => 'category',
						'hide_empty' => 0 ,
						);
					$subcat=get_categories($args);
					foreach ( $subcat as $category )
					{
						$name=$category->name;
						?>
						<div class="removeOnMain" style="margin-top:20px; margin-left:20px; margin-right:30px; margin-bottom:15px;">
							<span style="font-weight:bold;"><?php echo get_the_category_by_ID( $term_id );?></span>
							<div><a href="<?php echo $HostName; ?>?term_id=<?php echo $category->term_id; ?>&type=seen"><?php echo $name ?></a></div>
						</div>
						<?php }
					}
				}
			}
		}
	}

	function categoryData($categoryID,$length='excerpt'){
		$categoryHTML .= "<h3>".get_the_category_by_ID($categoryID)."</h3>";
		$categoryHTML .= "<div class='contentBlock'>";
		if (z_taxonomy_image_url($categoryID)!='') {
			$categoryHTML .= "<a href='?term_id=".$categoryID."&showpage=show'>"; 
			if($length=='excerpt'){
				$categoryHTML .= "<img class='compactImage' src='".z_taxonomy_image_url($categoryID)."' alt='Category Image'>"; 
			} else {
				$categoryHTML .= "<img class='blockImage' src='".z_taxonomy_image_url($categoryID)."' alt='Category Image'>"; 
			}
			$categoryHTML .= "</a>"; 

		}
		$categoryObject = get_category($categoryID);
		if($length=='excerpt' && $categoryObject->description!=''){
			$categoryHTML .= substr($categoryObject->description,0,60)."...  <a href='?term_id=".$categoryID."&showpage=show'>Read More</a>";
		} else {
			$categoryHTML .= $categoryObject->description;
		}
		$categoryHTML .= "</div>";
		return $categoryHTML;

	}

	function shortcode() {
		?>
		<style>
			.blue{background: blue;}
			.red{background: red;}
			.green{background: green;}
			.orange{background: orange;}
			.box{ padding: 98px; float:left;list-style: none;border:2px solid #000;}
			.gridArea ul li{font-weight:bold;}
			.gridArea ul{clear:both;}
			.mainArea .removeOnMain{display: none !important;}
			.contentBlock{display:inline-block;margin-bottom:40px;}
			.contentBlock img.compactImage{float:left;width:200px;height:auto;margin:0 20px 0 0;}
			.contentBlock img.blockImage{float:left;width:100%;height:auto;margin:0 20px 0 0;}
			.breadcrumb,.breadcrumbSaved{padding:10px 15px;margin:0 0 10px 0;border-radius:3px;}
			.breadcrumb{background: #e4e4e4;display:none;}
			.breadcrumbSaved{background: #f6f6f6;overflow: auto;max-height: 120px;display: none;}
			.cat-color{background:floralwhite;display: block; padding: 12px 12px 1px 12px;}
			.post-color{background:mistyrose;display: block; padding: 12px 12px 15px 12px;}
			.setup ul li{list-style-position: inside; padding: 5px;}
			@media only screen and (max-width : 1300px) { .box { padding : 75px; } }
			@media only screen and (max-width : 1000px) { .box { padding : 69px; } }
			@media only screen and (max-width : 954px)  { .box { padding : 90px; } }
			@media only screen and (max-width : 814px)  { .box { padding : 80px; } }
			@media only screen and (max-width : 727px)  { .box { padding : 72px; } }
			@media only screen and (max-width : 658px)  { .box { padding : 65px; } }
			@media only screen and (max-width : 516px)  { .box { padding : 58px; } .fourSquare { width: 280px !important;} }
			@media only screen and (max-width : 465px)  { .box { padding : 50px; } }
			@media only screen and (max-width : 410px)  { .box { padding : 44px; } }
			@media only screen and (max-width : 367px)  { .box { padding : 36px; } .fourSquare { width: 240px !important;} }
		</style>

		<script>
			jQuery(document).ready(function(){

				jQuery('#lang_choice_polylang-6').on('change',function(){
					sessionStorage.removeItem('breadcrumb');
					sessionStorage.removeItem('breadcrumbSaved');
				});

				var levelStatus = '<?php echo $_REQUEST["levelStatus"]; ?>';
				var seenStatus = '<?php echo $_REQUEST["type"]; ?>';
				jQuery('.breadcrumbArea').show();
				var checkcrumb = sessionStorage.getItem('breadcrumb');
				var checkSaved = sessionStorage.getItem('breadcrumbSaved');
				if(checkSaved!=null){ jQuery('.breadcrumbSaved').html(checkSaved).show(); }
				if(checkcrumb!=null){
					var checkLen = checkcrumb.length;
					if(checkLen>0){
						var currentTitle = jQuery('.gridArea > h3').text();
						if(levelStatus=='parent'){
							if(checkSaved!=null){
								var unlimitedCrumb = checkcrumb+'<br>'+checkSaved;
							} else {
								var unlimitedCrumb = checkcrumb;
							}
							sessionStorage.setItem('breadcrumbSaved',unlimitedCrumb);
							jQuery('.breadcrumbSaved').html(unlimitedCrumb).show();
							sessionStorage.setItem('breadcrumb',currentTitle);
							jQuery('.breadcrumb').text(currentTitle).show();
						} else {
							if(seenStatus=='seen'){
								var breadcrumb = checkcrumb+' > '+currentTitle;
							} else {
								var breadcrumb = checkcrumb;
							}
							sessionStorage.setItem('breadcrumb',breadcrumb);
							jQuery('.breadcrumb').text(breadcrumb).show();
						}		
					} else {
						var breadcrumb = jQuery('.gridArea > h3').text();
						sessionStorage.setItem('breadcrumb',breadcrumb);
						jQuery('.breadcrumb').text(breadcrumb).show();
					}
				} else {
					var breadcrumb = jQuery('.gridArea > h3').text();
					sessionStorage.setItem('breadcrumb',breadcrumb);
					jQuery('.breadcrumb').text(breadcrumb);
				}
			});
</script>

<div class="breadcrumbArea" style="display: none;">
	<p class="breadcrumb"></p>		
	<p class="breadcrumbSaved"></p>		
</div>

<?php

if(empty($_REQUEST)){
	echo "<div class='mainArea'>";
	$getWidget = new My_Widget();
	$getWidget->widget();
	echo "</div>";
}

if(isset($_REQUEST['term_id'])){

	if($_REQUEST['type']=='seen' && !isset($_REQUEST['showposts'])){

		$args = array(
			'orderby'                => 'name',
			'order'                  => 'ASC',
			'hide_empty'             => false,
			'include'                => array(),
			'exclude'                => array(),
			'exclude_tree'           => array(),
			'number'                 => '',
			'offset'                 => '',
			'fields'                 => 'all',
			'name'                   => '',
			'slug'                   => '',
			'hierarchical'           => true,
			'search'                 => '',
			'name__like'             => '',
			'description__like'      => '',
			'pad_counts'             => false,
			'get'                    => '',
			'child_of'               => 0,
			'parent'                 => $_REQUEST['term_id'],
			'childless'              => false,
			'cache_domain'           => 'core',
			'update_term_meta_cache' => true,
			'meta_query'             => ''
			);

		$terms = get_terms('category', $args);
		?>

		<div class="gridArea">
			<?php echo categoryData($_REQUEST['term_id']); ?>
			<ul>
				<?php foreach ($terms as $content) { ?>
				<?php if($content->count>0){ ?>
				<li><a href="<?php echo $HostName; ?>?term_id=<?php echo $content->term_id; ?>&type=seen&showposts=true"><?php echo $content->name; ?></a></li>
				<?php } else { ?>
				<li><a href="<?php echo $HostName; ?>?term_id=<?php echo $content->term_id; ?>&type=seen"><?php echo $content->name; ?></a></li>
				<?php } } ?>
			</ul>
		</div>

		<?php

	} elseif($_REQUEST['type']=='seen' && isset($_REQUEST['showposts'])) {
		
		$args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => $_REQUEST['term_id'],
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false 

			); 

		$categories = get_categories( $args );
		if(!empty($categories)){
			?>
			<div class="gridArea">
				<?php echo categoryData($_REQUEST['term_id']); ?>
				<div class="cat-color setup">
					<ul>
						<?php foreach ($categories as $content) { ?>
						<?php if($content->count>0){ ?>
						<li><a href="<?php echo $HostName; ?>?term_id=<?php echo $content->term_id; ?>&type=seen&showposts=true"><?php echo $content->name; ?></a></li>
						<?php } else { ?>
						<li><a href="<?php echo $HostName; ?>?term_id=<?php echo $content->term_id; ?>&type=seen"><?php echo $content->name; ?></a></li>
						<?php } } ?>
					</ul>
				</div>
			</div>
			<div class="gridArea post-color setup">
				<?php 
				global $wpdb;
				global $post;
				$results = $wpdb->get_results ( "SELECT * FROM wp_term_relationships WHERE term_taxonomy_id='".$_REQUEST['term_id']."'");  
				$counter = 0;
				foreach ( $results as $print ){
					$post_id = $print->object_id;
					$postsArray[$counter]['id']=$post_id;
					$postsArray[$counter]['sort_order']= get_post_meta ($post_id,'customSortOrder',true);
					$counter++;
				}
				//print_r(array_sort($postsArray, 'sort_order', SORT_DESC)); // Sort by oldest first
				$sortArray = array_sort($postsArray, 'sort_order', SORT_ASC);
				$post_id = array_values($sortArray);
				$counter = count($post_id);
				for($i=0;$i<$counter;$i++){
					$result =$wpdb->get_results("SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ID='".$post_id[$i]['id']."'");
					foreach($result as $post):
						setup_postdata($post);?>
					<ul style="margin-top:5px;">	
						<li style="margin-bottom:-30px;"><a href="<?php the_permalink()?>"><?php the_title();?></a></li>
					</ul>
					<?php 
					endforeach;
				}
				?>
			</div>
			<?php 
		} else {
			$args = array(
				'posts_per_page'   => 9999,
				'offset'           => 0,
				'category'         => $_REQUEST['term_id'],
				'category_name'    => '',
				'orderby'          => 'meta_value',
				'order'            => 'ASC',
				'include'          => '',
				'exclude'          => '',
				'meta_value'       => '',
				'meta_key'         => 'customSortOrder',
				'post_type'        => 'post',
				'post_mime_type'   => '',
				'post_parent'      => '',
				'author'	   => '',
				'post_status'      => 'publish',
				'suppress_filters' => true
				);

			/*'meta_key'         => 'customSortOrder',*/
			$postsArray = get_posts( $args );

			?>

			<div class="gridArea" >
				<?php echo categoryData($_REQUEST['term_id']); ?>
				<ul>
					<?php foreach ($postsArray as $content) { ?>
					<a href="<?php echo get_the_permalink($content->ID); ?>"><li><?php echo $content->post_title; ?></li></a>
					<?php } ?>
				</ul>
			</div>

			<?php
		}
	} elseif($_REQUEST['type']=='unseen') {

		$args = array(
			'posts_per_page'   => 9999,
			'offset'           => 0,
			'category'         => $_REQUEST['term_id'],
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true
			);

		$postsArray = get_posts( $args );
		
		if(!empty($postsArray)){

			foreach ($postsArray as $posts) {
				$postsIDS[] = $posts->ID;
			}

			$countLength = count($postsIDS);
			if($countLength>=9){
				$divideBy = 9;
			} elseif($countLength==4){
				$divideBy = 2;
			} elseif($countLength>4){
				$divideBy = 4;
			} elseif($countLength<4){
				$divideBy = 2;
			}

			$divideScheme = getDivideRule($countLength,$postsIDS,$divideBy);

			if($divideBy==9){ ?>
			<div class="gridArea">
				<?php echo categoryData($_REQUEST['term_id']); ?>
				<ul>
					<?php foreach ($divideScheme as $scheme) { ?>
					<?php $productString = implode(',',$scheme['ID']); ?>
					<?php if($scheme['division']==1) { ?>
					<a href="<?php echo get_the_permalink($scheme['ID'][0]); ?>"><li class="box blue"></li></a>
					<?php } else { ?>
					<a href="<?php echo $HostName; ?>?productID=<?php echo $productString; ?>&term_id=<?php echo $_REQUEST['term_id']; ?>"><li class="box blue"></li></a>
					<?php } } ?>
				</ul>
			</div>
			<?php } elseif($divideBy==4) {	?>
			<div class="gridArea fourSquare" style="width:400px">
				<?php echo categoryData($_REQUEST['term_id']); ?>
				<ul>
					<?php foreach ($divideScheme as $scheme) { ?>
					<?php $productString = implode(',',$scheme['ID']); ?>
					<?php if($scheme['division']==1) { ?>
					<a href="<?php echo get_the_permalink($scheme['ID'][0]); ?>"><li class="box green"></li></a>
					<?php } else { ?>	
					<a href="<?php echo $HostName; ?>?productID=<?php echo $productString; ?>&term_id=<?php echo $_REQUEST['term_id']; ?>"><li class="box green"></li></a>
					<?php } } ?>
				</ul>
			</div>
			<?php } elseif($divideBy==2) {	?>
			<div class="gridArea fourSquare" style="width:400px">
				<?php echo categoryData($_REQUEST['term_id']); ?>
				<ul>
					<?php foreach ($divideScheme as $scheme) { ?>
					<?php $productString = implode(',',$scheme['ID']); ?>
					<?php if($scheme['division']==1) { ?>
					<a href="<?php echo get_the_permalink($scheme['ID'][0]); ?>"><li class="box orange"></li></a>
					<?php } else { ?>
					<a href="<?php echo $HostName; ?>?productID=<?php echo $productString; ?>&term_id=<?php echo $_REQUEST['term_id']; ?>"><li class="box orange"></li></a>
					<?php } } ?>
				</ul>
			</div>
			<?php
		}
	}
} elseif($_REQUEST['showpage']=='show' && isset($_REQUEST['term_id'])) {
	echo categoryData($_REQUEST['term_id'],'fulldescription');
}	


}

if(isset($_REQUEST['productID'])){
	$requestList = explode(',',$_REQUEST['productID']);
	$countValues = count($requestList);

	if($countValues==4){
		$divideScheme = getDivideRule($countValues,$requestList,2);
		?>
		<div class="gridArea fourSquare" style="width:400px;">
			<?php echo categoryData($_REQUEST['term_id']); ?>
			<ul>
				<?php foreach ($divideScheme as $scheme) { ?>
				<?php $productString = implode(',',$scheme['ID']); ?>
				<a href="<?php echo $HostName; ?>?productID=<?php echo $productString; ?>&term_id=<?php echo $_REQUEST['term_id']; ?>"><li class="box orange"></li></a>
				<?php } ?>
			</ul>
		</div>
		<?php
	} elseif($countValues<4 || $countValues>4) {
		if($countValues>4){
			$divideScheme = getDivideRule($countValues,$requestList,4);
		} else {
			$divideScheme = getDivideRule($countValues,$requestList,2);
		}
		?>

		<div class="gridArea fourSquare" style="width:400px;">
			<?php echo categoryData($_REQUEST['term_id']); ?>
			<ul>

				<?php foreach ($divideScheme as $scheme) { ?>
				<?php $length = count($scheme['ID']); ?>
				<?php $productString = implode(',',$scheme['ID']); ?>
				<?php if($length==1){ ?>
				<a href="<?php echo get_the_permalink($scheme['ID'][0]); ?>"><li class="box green"></li></a>
				<?php } else { ?>
				<a href="<?php echo $HostName; ?>?productID=<?php echo $productString; ?>&term_id=<?php echo $_REQUEST['term_id']; ?>"><li class="box green"></li></a>
				<?php } } ?>
			</ul>
		</div>
		<?php
	}
}
}
add_shortcode('demo','shortcode');

function getDivideRule($count,$posts,$rule){

	$divideRule = explode('.',$count/$rule);
	$divideRule = $divideRule[0];
	$counter = 1;
	$start = 0;
	while($counter<=$rule){
		if($counter<$rule){
			$divideScheme[$counter]['ID'] = array_slice($posts,$start,$divideRule);
			$start = $start + $divideRule;
			$divideScheme[$counter]['division'] = $divideRule;
		} else {
			$divideScheme[$counter]['ID'] = array_slice($posts,$start,$count);
			$divideScheme[$counter]['division'] = $count;
		}
		$count = $count - $divideRule;
		$counter++;
	}

	return $divideScheme;
}


function customSortOrder() {
	add_meta_box('sortOrder','Sort Order Plugin','customSortOrderContent','post','side','high');
}
add_action('add_meta_boxes','customSortOrder');

function customSortOrderContent( $post ) {
	$orderMeta = get_post_meta( $post->ID );
	?>
	<p>
		<label for="customSortOrder" class="label">Sort Order : </label>
		<?php if(isset($orderMeta['customSortOrder'] )) { ?>
		<input type="text" name="customSortOrder" id="customSortOrder" value="<?php echo $orderMeta['customSortOrder'][0] ?>" />
		<?php } else { ?>
		<input type="text" name="customSortOrder" id="customSortOrder" value="999" />
		<?php } ?>
	</p>
	<?php
}

function sortOrderSave( $post_id ) {
	if( isset( $_POST[ 'customSortOrder' ] ) ) {
		update_post_meta( $post_id, 'customSortOrder', sanitize_text_field( $_POST[ 'customSortOrder' ] ) );
	}
}
add_action( 'save_post', 'sortOrderSave' );

remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );

add_filter('edit_category_form_fields', 'cat_description');
function cat_description($tag)
{
	?>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="description"><?php _ex('Description', 'Taxonomy Description'); ?></label></th>
		<td>
			<?php
			$settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
			wp_editor(wp_kses_post($tag->description , ENT_QUOTES, 'UTF-8'), 'cat_description', $settings);
			?>
			<br />
			<span class="description"><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></span>
		</td>
	</tr>

	<?php
}

add_action('admin_head', 'remove_default_category_description');
function remove_default_category_description()
{
	global $current_screen;
	if ( $current_screen->id == 'edit-category' )
	{
		?>
		<script type="text/javascript">
			jQuery(function($) {
				$('textarea#description').closest('tr.form-field').remove();
			});
		</script>
		<?php
	}
}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

?>
