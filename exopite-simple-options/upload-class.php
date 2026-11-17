<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 * Chunks
 * - https://docs.fineuploader.com/features/chunking.html
 */
/**
 *
 * Options Class
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Upload' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Upload.
	 */
	class Exopite_Simple_Options_Framework_Upload {

		/**
		 * Add hooks.
		 */
		public static function add_hooks() {

			add_action(
				'wp_ajax_exopite-sof-file_uploader',
				array(
					'Exopite_Simple_Options_Framework_Upload',
					'file_uploader_callback',
				)
			);
			add_action(
				'wp_ajax_exopite-sof-file-batch-delete',
				array(
					'Exopite_Simple_Options_Framework_Upload',
					'file_batch_delete_callback',
				)
			);
		}

		// DEGUB.
		public static function write_log( $type, $log_line ) {

			$hash        = '';
			$fn          = plugin_dir_path( __FILE__ ) . '/' . $type . '-' . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );
		}

		/**
		 * Handle Response
		 *
		 * Code based on:
		 *
		 * @link https://wordpress.org/plugins/wp-multi-file-uploader/
		 * @link https://wordpress.org/plugins/wp-dropzone/
		 */
		/**
		 * Nonce action for the upload/delete AJAX requests.
		 */
		const NONCE_ACTION = 'exopite_sof_upload';

		/**
		 * Verify the AJAX nonce, stop the request if it is invalid.
		 */
		protected static function verify_nonce() {

			$nonce = ( isset( $_POST['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
				status_header( 403 );
				die( json_encode( array( 'error' => 'Invalid nonce' ) ) );
			}
		}

		/**
		 * File uploader callback.
		 */
		public static function file_uploader_callback() {

			// file_put_contents( dirname(__FILE__) . '\test.log', var_export( $_POST, true ) . PHP_EOL . PHP_EOL, FILE_APPEND );.

			self::verify_nonce();

			$method = ( isset( $_POST['_method'] ) ) ? strtoupper( sanitize_key( $_POST['_method'] ) ) : '';

			if ( 'DELETE' === $method && isset( $_POST['qquuid'] ) ) {

				/**
				 * Delete file on AJAX request with qquuid
				 *
				 * @link https://docs.fineuploader.com/features/delete.html
				 * @link https://docs.fineuploader.com/branch/master/api/options.html#deleteFile
				 */
				$result = self::file_delete();

				$protocol = wp_get_server_protocol();

				header( $protocol . $result );

			} else {

				$result = self::file_uploader();

				header( 'Content-Type: text/plain' );
				echo json_encode( $result );

			}

			die();
		}

		/**
		 * File batch delete callback.
		 */
		public static function file_batch_delete_callback() {

			$deleted = array();

			self::verify_nonce();

			if ( isset( $_POST['media-ids'] ) && is_array( $_POST['media-ids'] ) ) {

				// Sanitize attachment ids, these should be absint.
				$attachment_id_array = array_map( 'absint', $_POST['media-ids'] );

				foreach ( $attachment_id_array as $attachmentid ) {

					// Only attachments, and only if the current user is allowed to delete them.
					if ( ! $attachmentid || get_post_type( $attachmentid ) !== 'attachment' || ! current_user_can( 'delete_post', $attachmentid ) ) {
						continue;
					}

					$deleted_item = wp_delete_attachment( $attachmentid, true );

					if ( $deleted_item instanceof WP_Post ) {
						$deleted[] = $deleted_item->ID;
					}
				}
			}

			die( json_encode( $deleted ) );
		}

		/**
		 * File delete.
		 */
		public static function file_delete() {

			/**
			 * Query attachments
			 *
			 * @link https://gist.github.com/BronsonQuick/1971067
			 */
			$args = array(
				'post_type'   => 'attachment',
				'numberposts' => 1,
				'post_status' => null,
				'meta_query'  => array(
					array(
						'key'     => 'qquuid',
						'value'   => sanitize_text_field( $_POST['qquuid'] ),
						'compare' => '=',
					),
				),

			);

			$attachments = get_posts( $args );

			if ( $attachments ) {

				foreach ( $attachments as $attachment ) {

					if ( ! current_user_can( 'delete_post', $attachment->ID ) ) {
						return ' 403 Forbidden';
					}

					if ( wp_delete_post( $attachment->ID, true ) ) {

						return ' 200 OK';

					}
				}
			}

			// Restore original post data.
			wp_reset_postdata();
			wp_reset_query();

			return ' 404 Not Found';
		}

		/**
		 * File uploader.
		 */
		public static function file_uploader() {

			// The user must be allowed to upload files.
			if ( ! current_user_can( 'upload_files' ) ) {
				return array( 'error' => 'You are not allowed to upload files' );
			}

			// If the file should be attached to a post, the user must be allowed to edit that post.
			$post_id = ( isset( $_POST['postId'] ) ) ? absint( $_POST['postId'] ) : 0;
			if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
				return array( 'error' => 'You are not allowed to edit this post' );
			}

			if ( ! isset( $_FILES['qqfile']['name'] ) ) {
				return array( 'error' => 'No file uploaded' );
			}

			// Make sure all files are allowed.
			if ( ! self::check_file_type( $_FILES['qqfile']['name'] ) ) {

				return array( 'error' => 'Unsupported file type' );

			}

			// Including file library if not exist.
			if ( ! function_exists( 'wp_handle_upload' ) ) {

				require_once ABSPATH . 'wp-admin/includes/file.php';

			}

			if ( isset( $_POST['qqfilename'] ) ) {
				$_FILES['qqfile']['name'] = sanitize_file_name( $_POST['qqfilename'] );
			}

			// Uploading file to server.
			$uploaded_file = $_FILES['qqfile'];

			$upload_overrides = array( 'test_form' => false );
			$movefile         = wp_handle_upload( $uploaded_file, $upload_overrides );

			if ( $movefile && empty( $movefile['error'] ) ) {

				$wp_upload_dir = wp_upload_dir();
				$filename      = str_replace( $wp_upload_dir['url'] . '/', '', $movefile['url'] );

				$attachment = self::add_attachment( $movefile['url'], $movefile['file'] );

				// Add qquuid for possibility to delete with AJAX fine uploader.
				if ( isset( $_POST['qquuid'] ) ) {
					update_post_meta( $attachment, 'qquuid', sanitize_text_field( $_POST['qquuid'] ) );
				}

				// Generate ALT attribute based on file name.
				$alt = substr( $_FILES['qqfile']['name'], 0, strrpos( $_FILES['qqfile']['name'], '.' ) );
				$alt = sanitize_title( $alt );
				$alt = str_replace( '-', ' ', $alt );
				$alt = ucwords( strtolower( $alt ) );
				update_post_meta( $attachment, '_wp_attachment_image_alt', $alt );

				return array(
					'success'      => $movefile,
					'attachmentId' => $attachment,
				);

			} else {

				return array( 'error' => ( ! empty( $movefile['error'] ) ) ? $movefile['error'] : 'Can not move file' );

			}

			return array( 'error' => 'General error' );
		}

		/**
		 * Handle Attachment
		 */
		public static function add_attachment( $url, $filepath ) {

			$updated_url = trim( substr( $url, strpos( $url, 'uploads/' ) + 8 ) );

			$wp_upload_dir = wp_upload_dir();
			$filename      = str_replace( $wp_upload_dir['url'] . '/', '', $url );

			$wp_filetype = wp_check_filetype( basename( $filename ), null );

			$attachment = array(
				'guid'           => $url,
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			// Attach file to post if attach is true and it is uploaded in a post (metabox).
			$post_id   = ( isset( $_POST['postId'] ) ) ? intval( $_POST['postId'] ) : 0;
			$attach_id = wp_insert_attachment( $attachment, $updated_url, $post_id );

			// Determines if attachment is an image.
			if ( wp_attachment_is_image( $attach_id ) ) {

				if ( ! function_exists( 'wp_generate_attachment_metadata' ) || ! function_exists( 'wp_update_attachment_metadata' ) ) {

					require_once ABSPATH . 'wp-admin/includes/image.php';

				}

				$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );

				wp_update_attachment_metadata( $attach_id, $attach_data );

			}

			return $attach_id;
		}

		/**
		 * Create Image Sizes
		 */
		function create_image_sizes( $filepath ) {
			$sizes = array();

			foreach ( get_intermediate_image_sizes() as $size ) {

				$sizes[ $size ]           = array(
					'width'  => '',
					'height' => '',
					'crop'   => true,
				);
				$sizes[ $size ]['width']  = get_option( "{$size}_size_w" ); // For default sizes set in options.
				$sizes[ $size ]['height'] = get_option( "{$size}_size_h" ); // For default sizes set in options.
				$sizes[ $size ]['crop']   = get_option( "{$size}_crop" ); // For default sizes set in options.

			}

			$sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );

			$metadata = array();

			foreach ( $sizes as $size => $size_data ) {

				$resized = image_make_intermediate_size(
					$filepath,
					$size_data['width'],
					$size_data['height'],
					$size_data['crop']
				);

				if ( $resized ) {

					$metadata[ $size ] = $resized;

				}
			}

			return $metadata;
		}

		/**
		 * Get WordPress Default Allowed Mime Types
		 *
		 * @since 1.1.0
		 */
		public static function allowed_mime_types() {
			// Work through the WordPress supported mime types.
			$wp_allowed_mime_types = get_allowed_mime_types();

			$allowed_mime_types = array();

			foreach ( $wp_allowed_mime_types as $key => $value ) {

				$mime_types = explode( '|', $key );

				// Build mime types array out of WordPress allowed mime types.
				foreach ( $mime_types as $mime_type ) {

					$allowed_mime_types[] = $mime_type;

				}
			}

			return $allowed_mime_types;
		}

		/**
		 * Check File Type
		 */
		public static function check_file_type( $file_name ) {

			// Get file info.
			$filetype = wp_check_filetype( $file_name );

			// Get from options.
			$allowed_exts = self::allowed_mime_types();

			// Make sure the file type is allowed.
			$ext = ( isset( $filetype['ext'] ) ) ? strtolower( $filetype['ext'] ) : '';

			if ( in_array( $ext, $allowed_exts ) ) {

				return true;

			}

			return false;
		}

		/*
		 * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
		 *
		 * @link https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size/25370978#25370978
		 */
		public static function file_upload_max_size() {
			static $max_size = - 1;

			if ( $max_size < 0 ) {

				// Start with post_max_size.
				$post_max_size = self::parse_size( ini_get( 'post_max_size' ) );

				if ( $post_max_size > 0 ) {

					$max_size = $post_max_size;

				}

				// If upload_max_size is less, then reduce. Except if upload_max_size is
				// zero, which indicates no limit.
				$upload_max = self::parse_size( ini_get( 'upload_max_filesize' ) );
				if ( $upload_max > 0 && $upload_max < $max_size ) {

					$max_size = $upload_max;

				}
			}

			return $max_size;
		}

		/**
		 * Parse size.
		 */
		public static function parse_size( $size ) {

			$unit = preg_replace( '/[^bkmgtpezy]/i', '', $size ); // Remove the non-unit characters from the size.
			$size = preg_replace( '/[^0-9\.]/', '', $size ); // Remove the non-numeric characters from the size.

			if ( $unit ) {

				// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
				return round( $size * pow( 1024, stripos( 'bkmgtpezy', $unit[0] ) ) );

			} else {

				return round( $size );

			}
		}
	}

}
