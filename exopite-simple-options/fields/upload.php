<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Upload
 *
 */
/*
 * Info about JavaScript uploaders
 *
 * https://codex.wordpress.org/Function_Reference/wp_handle_upload
 * https://codex.wordpress.org/Function_Reference/media_handle_upload
 * http://www.kvcodes.com/2013/12/create-front-end-multiple-file-upload-wordpress/
 * https://wordpress.stackexchange.com/questions/173197/upload-multiple-files-with-media-handle-upload
 * https://www.ibenic.com/wordpress-file-upload-with-ajax/
 * https://www.theaveragedev.com/wordpress-files-ajax/
 *
 * Dropzone
 * https://www.startutorial.com/articles/view/how-to-build-a-file-upload-form-using-dropzonejs-and-php
 * http://www.dropzonejs.com/
 * https://github.com/enyo/dropzone/wiki/FAQ
 * https://wordpress.org/plugins/wp-dropzone/
 *
 * FineUploader
 * wp-multi-file-uploader
 * https://docs.fineuploader.com/integrating/jquery.html
 * https://github.com/FineUploader/fine-uploader
 *
 * PlUpload
 * http://www.plupload.com/examples/events
 *
 * JQuery Drag and Drop Files
 * https://danielmg.org/demo/java-script/bootstrap-drag-and-drop-uploader
 *
 * jQuery File Upload
 * https://blueimp.github.io/jQuery-File-Upload/jquery-ui.html
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_upload' ) ) {

	class Exopite_Simple_Options_Framework_Field_upload extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '' ) {

			parent::__construct( $field, $value, $unique, $where );

			$defaults = array(
				'attach'                   => false,
				'filecount'                => 1,
				'delete-enabled'           => true,
				'delete-force-confirm'     => true,
				'retry-enable-auto'        => true,
				'retry-max-auto-attempts'  => 1,
				'retry-auto-attempt-delay' => 2,
				'auto-upload'              => false,
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();

			$this->field['options'] = wp_parse_args( $options, $defaults );

		}

		public function output() {

			echo $this->element_before();

			?>
            <!-- Fine Uploader Thumbnails template w/ customization
            ====================================================================== -->
            <script type="text/template" id="qq-template-manual-trigger">
                <div class="qq-uploader-selector qq-uploader"
                     qq-drop-area-text="<?php esc_html_e( 'Drop files here', 'exopite-sof' ); ?>">
                    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                             class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                    </div>
                    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                        <span class="qq-upload-drop-area-text-selector"></span>
                    </div>
                    <div class="buttons">
                        <div class="qq-upload-button-selector exopite-sof-btn">
                            <div><?php esc_html_e( 'Select files', 'exopite-sof' ); ?></div>
                        </div>
                        <div class="exopite-sof-btn trigger-upload">
                            <i class="icon-upload icon-white"></i> <?php esc_html_e( 'Upload', 'exopite-sof' ); ?>
                        </div>
                    </div>
                    <span class="qq-drop-processing-selector qq-drop-processing">
                        <span><?php esc_html_e( 'Processing dropped files...', 'exopite-sof' ); ?></span>
                        <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                    </span>
                    <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite"
                        aria-relevant="additions removals">
                        <li>
                            <div class="qq-progress-bar-container-selector">
                                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                     class="qq-progress-bar-selector qq-progress-bar"></div>
                            </div>
                            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                            <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"
                                  aria-label="Edit filename"></span>
                            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                            <span class="qq-upload-size-selector qq-upload-size"></span>
                            <button type="button"
                                    class="qq-btn qq-upload-cancel-selector qq-upload-cancel"><?php esc_html_e( 'Cancel', 'exopite-sof' ); ?></button>
                            <button type="button"
                                    class="qq-btn qq-upload-retry-selector qq-upload-retry"><?php esc_html_e( 'Retry', 'exopite-sof' ); ?></button>
                            <button type="button"
                                    class="qq-btn qq-upload-delete-selector qq-upload-delete"><?php esc_html_e( 'Delete', 'exopite-sof' ); ?></button>
                            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                        </li>
                    </ul>

                    <dialog class="qq-alert-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button"
                                    class="qq-cancel-button-selector"><?php esc_html_e( 'Close', 'exopite-sof' ); ?></button>
                        </div>
                    </dialog>

                    <dialog class="qq-confirm-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button"
                                    class="qq-cancel-button-selector"><?php esc_html_e( 'No', 'exopite-sof' ); ?></button>
                            <button type="button"
                                    class="qq-ok-button-selector"><?php esc_html_e( 'Yes', 'exopite-sof' ); ?></button>
                        </div>
                    </dialog>

                    <dialog class="qq-prompt-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <input type="text">
                        <div class="qq-dialog-buttons">
                            <button type="button"
                                    class="qq-cancel-button-selector"><?php esc_html_e( 'Cancel', 'exopite-sof' ); ?></button>
                            <button type="button"
                                    class="qq-ok-button-selector"><?php esc_html_e( 'Ok', 'exopite-sof' ); ?></button>
                        </div>
                    </dialog>
                </div>
            </script>
			<?php

			$maxsize = Exopite_Simple_Options_Framework_Upload::file_upload_max_size();
			if ( isset( $this->field['options']['maxsize'] ) && Exopite_Simple_Options_Framework_Upload::file_upload_max_size() >= $this->field['options']['maxsize'] ) {
				$maxsize = $this->field['options']['maxsize'];
			}

			$allowed_mime_types = ( gettype( Exopite_Simple_Options_Framework_Upload::allowed_mime_types() ) == 'array' ) ? implode( ',', Exopite_Simple_Options_Framework_Upload::allowed_mime_types() ) : Exopite_Simple_Options_Framework_Upload::allowed_mime_types();

			if ( isset( $this->field['options']['allowed'] ) && is_array( $this->field['options']['allowed'] ) ) {
				$allowed_mime_types_array = explode( ',', $allowed_mime_types );
				$allowed_mime_types_array = array_intersect( $allowed_mime_types_array, $this->field['options']['allowed'] );
				$allowed_mime_types       = implode( ',', $allowed_mime_types_array );
			}


			?>
            <div class="qq-template" <?php
			echo 'data-filecount="' . $this->field['options']['filecount'] . '" ';
			echo 'data-mimetypes="' . $allowed_mime_types . '" ';
			echo 'data-maxsize="' . $maxsize . '" ';
			echo ( $this->field['options']['attach'] && $this->where == 'metabox' ) ? 'data-postid="' . get_the_ID() . '" ' : '';
			echo 'data-ajaxurl="' . site_url( 'wp-admin/admin-ajax.php' ) . '" ';
			echo 'data-delete-enabled="' . $this->field['options']['delete-enabled'] . '" ';
			echo 'data-delete-force-confirm="' . $this->field['options']['delete-force-confirm'] . '" ';
			echo 'data-retry-enable-auto="' . $this->field['options']['retry-enable-auto'] . '" ';
			echo 'data-retry-max-auto-attempts="' . $this->field['options']['retry-max-auto-attempts'] . '" ';
			echo 'data-retry-auto-attempt-delay="' . $this->field['options']['retry-auto-attempt-delay'] . '" ';
			echo 'data-auto-upload="' . $this->field['options']['auto-upload'] . '" ';
			?>>
            </div>
            <div class="qq-template-info">
				<?php

				echo __( 'Max amount of files: ', 'exopite-sof' ) . $this->field['options']['filecount'] . '<br>';
				echo __( 'Max file upload size: ', 'exopite-sof' ) . number_format( (float) ( Exopite_Simple_Options_Framework_Upload::file_upload_max_size() / 1048576 ), 2, '.', '' ) . 'Mb';


				?>
            </div>
			<?php

			echo $this->element_after();

		}

		public static function enqueue( $args ) {

			if ( ! wp_script_is( 'fine-uploader' ) ) {

				/*
				 * https://fineuploader.com/
				 */
				// If local
				// wp_enqueue_script( 'fine-uploader', $plugin_sof_url . 'assets/fine-uploader.js', array(), '5.15.5', true );

				// Style
//				wp_enqueue_style( 'fine-uploader', '//cdnjs.cloudflare.com/ajax/libs/file-uploader/5.15.5/all.fine-uploader/fine-uploader-new.min.css', array(), '5.15.5', 'all' );

				// Developer version
				// wp_enqueue_script( 'fine-uploader', '//cdnjs.cloudflare.com/ajax/libs/file-uploader/5.15.5/jquery.fine-uploader/jquery.fine-uploader.js', array(), '5.15.5', true );

				// Minified version
//				wp_enqueue_script( 'fine-uploader', '//cdnjs.cloudflare.com/ajax/libs/file-uploader/5.15.5/jquery.fine-uploader/jquery.fine-uploader.min.js', array(), '5.15.5', true );

				$script_file = 'loader-fine-uploader.min.js';
				$script_name = 'exopite-sof-fine-uploader-loader';

				wp_enqueue_script( $script_name, $args['plugin_sof_url'] . 'assets/' . $script_file, array( 'fine-uploader' ), filemtime( join( DIRECTORY_SEPARATOR, array(
					$args['plugin_sof_path'] . 'assets',
					$script_file
				) ) ), true );

				$resources = array(
					array(
						'name'       => 'fine-uploader',
						'fn'         => 'fine-uploader-new.min.css',
						'type'       => 'style',
						'dependency' => array(),
						'version'    => '5.15.5',
						'attr'       => 'all',
					),
					array(
						'name'       => 'fine-uploader',
						'fn'         => 'jquery.fine-uploader.min.js',
						'type'       => 'script',
						'dependency' => array(),
						'version'    => '5.15.5',
						'attr'       => true,
					),
					array(
						'name'       => 'exopite-sof-fine-uploader-loader',
						'fn'         => 'loader-fine-uploader.min.js',
						'type'       => 'script',
						'dependency' => array( 'fine-uploader' ),
						'version'    => '',
						'attr'       => true,
					),
				);

				parent::do_enqueue( $resources, $args );

			}

		}

	}

}

