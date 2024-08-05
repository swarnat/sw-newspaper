<?php
/*
 * Plugin Name:       Newspaper Manager
 * Description:       Handles a PDF newspaper and create thumbnail as separate image
 * Version:           1.1.0
 * Requires PHP:      7.4
 * Author:            Stefan Warnat
 * Author URI:        https://stefanwarnat.de
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sw-newspaper  
 * Domain Path:       /languages
 */

 /** Step 2 (from text above). */
add_action( 'admin_menu', 'sw_newspaper_admin_menu' );

add_shortcode('newspaper_download', 'sw_newspaper_current_download');
add_shortcode('newspaper_archive', 'sw_newspaper_archive');
add_shortcode('newspaper_single', 'sw_newspaper_single');

wp_register_style( 'swnewspaper-style', plugins_url( 'css/stylesheet.css', __FILE__ ) );

function sw_newspaper_single($atts = array()) {
    wp_enqueue_style( 'swnewspaper-style' );

    $currentDFlipConfigId = get_option('swnewspaper_dflipid', 0);

    if(empty($atts["width"])) $atts["width"] = 200;

    $html = '<div class="newspaper-single" style="width:'.intval($atts["width"]).'px;">';
    $html .= do_shortcode('[dflip id="'.$currentDFlipConfigId.'" type="thumb" ][/dflip]');
    $html .= '</div>';

    return $html;
    
}
function sw_newspaper_archive($atts = array()) {
    $newspapers = get_posts( array(
        'post_type' => 'attachment',
        'meta_key'   => '_newspaper',
        'meta_value' => '1',
    ) );


    $columns = $atts['columns'] ?? 4;

    $html = '<div class="newspaper-archive" style="columns-count: '.$columns.'">';
    foreach($newspapers as $newspaper) {
        $thumbId = get_post_meta($newspaper->ID, '_thumbid', true);

        $html .= '<div class="newspaper-archive-single">
<a href="' . $newspaper->guid . '" target="_blank">
    <img src="' . wp_get_attachment_url($thumbId) . '" />
</a>
<div class="newspaper-title">'.$newspaper->post_title.'</div>
</div>';
    }

    $html .= '</div>';

    wp_enqueue_style( 'swnewspaper-style' );

    return $html;
    
}

function sw_newspaper_current_download($atts = array()) {
    $currentAttachmentId = get_option('swnewspaper_currentid');

    $label = $atts['label'] ?? 'Herunterladen';

    return '<a href="'.wp_get_attachment_url($currentAttachmentId).'">' . $label . '</a>';
}

/** Step 1. */
function sw_newspaper_admin_menu() {
	add_options_page( 
        __( 'Newspaper Options', 'sw-newspaper' ), 
        __( 'Newspaper Options', 'sw-newspaper' ), 
        'manage_options', 
        'sw-newspaper-manager', 
        'sw_newspaper_admin_options_page' 
    );

	add_menu_page(
		__( 'Newspaper Options', 'sw-newspaper' ),
		__( 'Newspaper', 'sw-newspaper' ),
		'manage_options',
		'sw-newspaper/upload.php',
		'sw_newspaper_admin_uploader_page',
		'dashicons-media-document',
		6
	);
}

function sw_newspaper_admin_options_page() {
    if(!empty($_POST['dflip_value'])) {
        update_option('swnewspaper_dflipid', (int)$_POST['dflip_value']);
        update_option('swnewspaper_medafolderid', !empty($_POST['mediafolder']) ? (int)$_POST['mediafolder'] : 0);
    }

    $currentDFlipConfigId = get_option('swnewspaper_dflipid', 0);

    $dFlipConfigs = get_posts([ 'post_type' => 'dflip ']);

    $currentMediaFolderId = get_option('swnewspaper_medafolderid', 0);
    $mediaFolders = get_terms( array(
        'taxonomy'   => 'wpmf-category',
        'hide_empty' => false,
    ) );

    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'options.php');
}

function sw_newspaper_admin_uploader_page() {
    if(!empty($_FILES['uploadfile'])) {
        //if(is_uploaded_file($_FILES['uploadfile']))
        $title = $_POST['title'];
        $newspaperDate = date('Y-m-d', strtotime($_POST['date']));

        $currentDFlipConfigId = get_option('swnewspaper_dflipid', 0);

        $fileData = $_FILES['uploadfile'];
        $file = array(
            'name'     => $fileData['name'],
            'type'     => mime_content_type( $fileData['tmp_name'] ),
            'tmp_name' => $fileData['tmp_name'],
            'size'     => filesize( $fileData['tmp_name'] ),
        );
        $sideload = wp_handle_sideload(
            $file,
            array(
                'test_form'   => false // no needs to check 'action' parameter
            )
        );        

        if( ! empty( $sideload[ 'error' ] ) ) {
            // you may return error message if you want
            return false;
        }
    
        // it is time to add our uploaded image into WordPress media library
        $pdfAttachmentId = wp_insert_attachment(
            array(
                'guid'           => $sideload[ 'url' ],
                'post_mime_type' => $sideload[ 'type' ],
                'post_title'     => $title,
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $sideload[ 'file' ]
        );
    
        if( is_wp_error( $pdfAttachmentId ) || ! $pdfAttachmentId ) {
            return false;
        }        

        update_post_meta($pdfAttachmentId, '_newspaper', 1);

        update_option('swnewspaper_currentid', $pdfAttachmentId);

        $filepath = get_attached_file($pdfAttachmentId);
        $thumbFilename = str_replace('.pdf','.png', basename($filepath));
        
        $imageFileTmp = tempnam(sys_get_temp_dir(), 'PDF') . '.png';

        $imagick = new Imagick();
        $imagick->readImage($filepath . '[0]');
        $imagick->writeImages($imageFileTmp, false);
        unset($imagick);

        $file = array(
            'name'     => $thumbFilename,
            'type'     => mime_content_type( $imageFileTmp ),
            'tmp_name' => $imageFileTmp,
            'size'     => filesize( $imageFileTmp ),
        );
        $sideload = wp_handle_sideload(
            $file,
            array(
                'test_form'   => false // no needs to check 'action' parameter
            )
        );        

        if( ! empty( $sideload[ 'error' ] ) ) {
            // you may return error message if you want
            return false;
        }
    
        // it is time to add our uploaded image into WordPress media library
        $thumbAttachmentId = wp_insert_attachment(
            array(
                'guid'           => $sideload[ 'url' ],
                'post_mime_type' => $sideload[ 'type' ],
                'post_title'     => $title . '.png',
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $sideload[ 'file' ]
        );
    
        if( is_wp_error( $thumbAttachmentId ) || ! $thumbAttachmentId ) {
            return false;
        }        

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        wp_update_attachment_metadata(
            $thumbAttachmentId,
            wp_generate_attachment_metadata( $thumbAttachmentId, $sideload[ 'file' ] )
        );

        update_post_meta($pdfAttachmentId, '_thumbid', $thumbAttachmentId);
        update_post_meta($pdfAttachmentId, 'date', $newspaperDate);

        $dFlipData = get_post_meta($currentDFlipConfigId, '_dflip_data', true);
        
        $dFlipData['pdf_source'] = wp_get_attachment_url($pdfAttachmentId);
        $dFlipData['pdf_thumb'] = wp_get_attachment_url($thumbAttachmentId);

        update_post_meta($currentDFlipConfigId, '_dflip_data', $dFlipData);

        $currentMediaFolderId = get_option('swnewspaper_medafolderid', 0);
        if(!empty($currentMediaFolderId)) {
            wp_set_post_terms($pdfAttachmentId, [$currentMediaFolderId], 'wpmf-category');
            wp_set_post_terms($thumbAttachmentId, [$currentMediaFolderId], 'wpmf-category');
        }
    }
    
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'uploader.php');
}