<?php

class DECOM_FilesUpload {
	private static $allowedExstensions = array( 'jpg', 'jpeg', 'png', 'gif' );
	private static $allowedMimeTypes = array(
		'image/jpg',
		'image/jpeg',
		'image/png',
		'image/gif'
	);
	private $allowedMaxSize = 5; //Mb
	private $uploadDirectory = false;
	private $allowedWidthHeight = array();
	private $files = array();
	private $errors = array();

	public function __construct( $uploads, $upload_dir = false, $allowedWidthHeight = array() ) {
		//TODO
		$model_options  = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$allowedMaxSize = $model_options->getOption( 'decom_max_size_uploaded_images' );
		if ( $allowedMaxSize !== false ) {
			$this->allowedMaxSize = $allowedMaxSize;
		}

		if ( $upload_dir ) {
			$this->validateUploadDirectory( $upload_dir );
		}

		if ( array_key_exists( 'width', $allowedWidthHeight ) && array_key_exists( 'height', $allowedWidthHeight ) ) {

			if ( is_numeric( $allowedWidthHeight['width'] ) && is_numeric( $allowedWidthHeight['height'] ) ) {
				$this->allowedWidthHeight = $allowedWidthHeight;
			}
		}

		if ( is_array( $uploads ) && count( $uploads ) > 0 ) {
			/*			$files = array();
						foreach ( $uploads as $key => $v ) {
							if ( is_array( $v ) && count( $v ) > 0 ) {
								foreach ( $v as $k => $value ) {
									$value = trim( $value );
									//echo 'NAME : '.$key;
									if ( $key == 'name' && strlen( $value ) == 0 ) {
										$this->files = $files;

										return;
									}
									$files[ $k ][ $key ] = trim( $value );
								}
							} else {
								$v = trim( $v );
								if ( $key == 'name' && strlen( $v ) == 0 ) {
									$this->files = $files;

									return;
								}
								$files[0][ $key ] = $v;
							}
						}*/

			$this->files = $uploads;
		}
	}

	public function validateUploadDirectory( $upload_dir ) {
		$upload_dir = trim( $upload_dir );
		if ( $upload_dir ) {
			$ud_len     = strlen( $upload_dir );
			$last_slash = substr( $upload_dir, $ud_len - 1, 1 );
			if ( $last_slash <> "/" ) {
				$upload_dir = $upload_dir . "/";
			} else {
				$upload_dir = $upload_dir;
			}
			$handle = @opendir( $upload_dir );
			if ( $handle ) {
				$upload_dir = $upload_dir;
				closedir( $handle );
			} else {
				$this->errors[]['code'] = 1;

				return false;
			}
		} else {
			$this->errors[]['code'] = 1;

			return false;
		}

		$this->uploadDirectory = $upload_dir;
	}

	public function validateFiles( $override = true ) {
		$validate = true;
		if ( is_array( $this->files ) && count( $this->files ) > 0 ) {
			foreach ( $this->files as $key => $file ) {
				$this->validateFile( $file, $override );
				if ( isset( $this->errors['files'] ) && count( $this->errors['files'] ) > 0 ) {
					$this->files[ $key ]['invalid'] = $this->errors['files'][ $file['name'] ]['code'];
					$validate                       = false;
				}
			}
		}

		return $validate;
	}

	public function validateFile( $file, $override = true ) {
		$this->validateExtension( $file );
		$this->validateMimeType( $file );
		$this->validateSize( $file );
		if ( count( $this->allowedWidthHeight ) > 0 ) {
			$this->validateWidthHeight( $file );
		}
	}

	public function validateExtension( $file ) {
		$file_name = trim( $file['name'] );
		$temp      = explode( '.', $file_name );
		$extension = strtolower( end( $temp ) );
		if ( ! in_array( $extension, self::$allowedExstensions ) ) {
			$this->errors['files'][ $file['name'] ]['code'] = 10;
		}
	}

	public function validateMimeType( $file ) {
		$file_type = trim( $file['type'] );
		if ( ! in_array( $file_type, self::$allowedMimeTypes ) ) {
			$this->errors['files'][ $file['name'] ]['code'] = 11;
		}
	}

	public function validateSize( $file ) {
		$max_size = $this->getFileMaxSize();
		if ( $file['size'] > $max_size ) {
			$this->errors['files'][ $file['name'] ]['code']  = 12;
			$this->errors['files'][ $file['name'] ]['param'] = round( ( $max_size / 1024 / 1024 ), 1 );
		}
	}

	public function validateWidthHeight( $file ) {
		$image_sizes = getimagesize( $file['tmp_name'] );
		if ( $image_sizes ) {
			list( $w, $h ) = $image_sizes;
			$_w = $this->allowedWidthHeight['width'];
			$_h = $this->allowedWidthHeight['height'];
			if ( $w > $_w ) {
				$this->errors['files'][ $file['name'] ]['code']  = 14;
				$this->errors['files'][ $file['name'] ]['param'] = $_w;
				//$this->errors['files'][$file['name']]['param2'] = $w;
			}
			if ( $h > $_h ) {
				$this->errors['files'][ $file['name'] ]['code']  = 15;
				$this->errors['files'][ $file['name'] ]['param'] = $_h;
				//$this->errors['files'][$file['name']]['param2'] = $h;
			}
		}
	}

	public function getFileMaxSize() {
		return $this->allowedMaxSize * 1024 * 1024;
	}

	public function getErrors() {
		if ( count( $this->errors ) > 0 ) {
			return $this->errors;
		}

		return false;
	}

	public function uploadFiles( $commentId = true, $single = false ) {
		if ( $commentId && is_array( $this->files ) && count( $this->files ) > 0 ) {
			$attach_ids = array();

			foreach ( $this->files as $file ) {
				if ( array_key_exists( 'invalid', $file ) ) {
					$this->errors['invalids'][] = $file['invalid'];
				} else {
					if ( $this->uploadDirectory ) {
						$upload_file = $this->uploadFileWithValidation( $file );
						if ( is_array( $upload_file ) ) {
							if ( $single ) {
								return $upload_file;
							}
						} else {
							$this->errors[] = $upload_file;
						}
					} else {
						$attach_ids[] = $this->WPMediaHandleUpload( $file, $commentId );
					}
				}
			}

			return $attach_ids;
		}

		$this->errors[] = 'Empty files.';
	}

	public function uploadFileWithValidation( $file ) {

		$file_path = $this->uploadDirectory . $file['name'];
//		if ( @move_uploaded_file( $file['tmp_name'], $file_path ) ) {

		if ( @rename( $file['tmp_name'], $file_path ) ) {
			$stat  = stat( dirname( $file_path ) );
			$perms = $stat['mode'] & 0000666;
			@chmod( $file_path, $perms );

			return $file_path;
		} else {
			return $this->getErrorByCode( 16, $this->uploadDirectory );
		}
	}

	public function WPMediaHandleUpload( $file, $commentId ) {
		if ( ! file_exists( $file['tmp_name'] ) ) {
			return false;
		}

		$uploads               = wp_upload_dir();
		$this->uploadDirectory = $uploads['path'] . '/';

		$file_name = 'decom_' . $file['filename'] . '_' . uniqid();
		$file_name = $file_name . '.' . $file['extension'];

		$file['name'] = $file_name;

		$file['file'] = $this->uploadFileWithValidation( $file );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		if ( $image_meta = @wp_read_image_metadata( $file['file'] ) ) {
			if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$title = $image_meta['title'];
			}
		}

		// Construct the attachment array
		$attachment = array(
			'post_mime_type' => $file['type'],
			'guid'           => $uploads['url'] . '/' . $file_name,
			'post_parent'    => 0,
			'post_title'     => $file_name,
		);

		// This should never be set as it would then overwrite an existing attachment.
		if ( isset( $attachment['ID'] ) ) {
			unset( $attachment['ID'] );
		}

		// Save the data
		$id = wp_insert_attachment( $attachment, $file['file'], 0 );

		if ( ! is_wp_error( $id ) ) {
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file['file'] ) );
		}

		$attach_ids = get_comment_meta( $commentId, 'decom_attached_pictures', true );
		if ( $attach_ids ) {
			$attach_ids = unserialize( $attach_ids );
		} else {
			$attach_ids = array();
		}
		$attach_ids[] = $id;
		$attach_ids   = serialize( $attach_ids );
		update_comment_meta( $commentId, 'decom_attached_pictures', $attach_ids );

		return $id;
	}

	public function is_ani( $filename ) {
		return (bool) preg_match( '#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents( $filename ) );
	}

	public static function getErrorByCode( $code, $param = '', $param2 = '' ) {
		if ( ! is_numeric( $code ) ) {
			return __( 'Unknown upload error.', DECOM_LANG_DOMAIN );
		}

		$errors = array(
			1  => __( 'Upload directory is invalid. Please check.', DECOM_LANG_DOMAIN ),
			2  => __( 'Uploaded file exceeds the upload_max_filesize directive in php.ini.', DECOM_LANG_DOMAIN ),
			3  => __( 'Uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', DECOM_LANG_DOMAIN ),
			4  => __( 'Uploaded file was only partially uploaded.', DECOM_LANG_DOMAIN ),
			5  => __( 'File was not uploaded.', DECOM_LANG_DOMAIN ),
			6  => __( 'Missing a temporary folder.', DECOM_LANG_DOMAIN ),
			7  => __( 'Failed to write file to disk.', DECOM_LANG_DOMAIN ),
			8  => __( 'File upload stopped by extension.', DECOM_LANG_DOMAIN ),
			9  => __( 'Unknown upload error.', DECOM_LANG_DOMAIN ),
			10 => __( 'Extension of uploaded file is invalid. Please upload an image.', DECOM_LANG_DOMAIN ),
			//implode(',',self::$allowedExstensions),
			11 => __( 'MIME type of uploaded file invalid. Upload an image, please.', DECOM_LANG_DOMAIN ),
			12 => sprintf( __( 'File size invalid. Maximum file size %g MB.', DECOM_LANG_DOMAIN ), $param ),
			13 => __( 'File already exist. Try again, please.', DECOM_LANG_DOMAIN ),
			14 => sprintf( __( 'Image width is invalid. Maximum image width is %g px.', DECOM_LANG_DOMAIN ), $param ),
			15 => sprintf( __( 'Invalid image height. Maximum image height %g px.', DECOM_LANG_DOMAIN ), $param ),
			16 => sprintf( __( 'Uploaded file could not be moved to %s.', DECOM_LANG_DOMAIN ), $param ),
			17 => sprintf( __( 'Uploaded file %s cannot be updated', DECOM_LANG_DOMAIN ), $param ),
		);

		if ( ! array_key_exists( $code, $errors ) ) {
			return __( 'Unknown upload error.', DECOM_LANG_DOMAIN );
		}

		return $errors[ $code ];
	}
}