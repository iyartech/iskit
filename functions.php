<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Put your custom code here.

//add page builder to post and product

function pojo5357_add_builder_in_posts() {

    add_post_type_support( 'post', array( 'pojo-page-format' ) );

    add_post_type_support( 'product', array( 'pojo-page-format' ) );

}

//END add page builder to post and product

// rename woo product tab

add_action( 'init', 'pojo5357_add_builder_in_posts', 200 );



add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );

function woo_rename_tabs( $tabs ) {

	$tabs['additional_information']['title'] = __( 'הטבות' );	// Rename the additional information tab

	return $tabs;

}
// END rename woo product tab

//Custum product tab - related product 




//End Custum product tab - related product 

// Change the description tab title to product name  *** Working***
add_filter( 'woocommerce_product_tabs', 'wc_change_product_description_tab_title', 10, 1 );
function wc_change_product_description_tab_title( $tabs ) {
  global $post;
	if ( isset( $tabs['description']['title'] ) )
	/*	$tabs['description']['title'] = $post->post_title;*/
	/*	$tabs['additional_information']['title'] = __( 'הטבות' );	// Rename the additional information tab*/
	return $tabs;
}
 
// Change the description tab heading to product name
add_filter( 'woocommerce_product_description_heading', 'wc_change_product_description_tab_heading', 10, 1 );
function wc_change_product_description_tab_heading( $title ) {
	global $post;
	return $post->post_title;
}
//END Change the description tab title to product name  *** End working ****


// Modify the default WooCommerce orderby dropdown
//
// Options: menu_order, popularity, rating, date, price, price-desc
// In this example I'm removing price & price-desc but you can remove any of the options
function my_woocommerce_catalog_orderby( $orderby ) {
	unset($orderby["price"]);
	unset($orderby["price-desc"]);
	unset($orderby["popularity"]);
	return $orderby;
}
// End Modify the default WooCommerce orderby dropdown

//remove sale-product slug from shop
add_filter( "woocommerce_catalog_orderby", "my_woocommerce_catalog_orderby", 20 );

add_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
function custom_pre_get_posts_query( $q ) {
 
	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() ) {
 
		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => array( 'sale-product' ), // Don't display products in the sale-product category on the shop page
			'operator' => 'NOT IN'
		)));
	
	}
 
	remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
}
//END remove sale-product slug from shop

//Random product
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );

function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

	if ( 'random_list' == $orderby_value ) {
		$args['orderby'] = 'rand';
		$args['order'] = '';
		$args['meta_key'] = '';
	}

	return $args;
}

add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );

function custom_woocommerce_catalog_orderby( $sortby ) {
	$sortby['random_list'] = 'אקראי';
	return $sortby;
}
//END Random product

//block site
/*
function wp_custom_maintenance() { // put the website on maintenance
if (!current_user_can('manage_options')) {
wp_die("האתר בבניה");
}
}
add_action("wp_head","wp_custom_maintenance"); //delete or comment out this line when you want to shut the mainteance off.
*/
//END block site

//change string
function my_text_strings( $translated_text, $text, $domain ) {
switch ( $translated_text ) {
case 'מוצרים קשורים' :
$translated_text = __( 'עסקיות נוספות בתחום', '' );
break;
}
return $translated_text;
}
add_filter( 'gettext', 'my_text_strings', 20, 3 );
//END change string

// Display 250 products per page.
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 300;' ), 20 );
