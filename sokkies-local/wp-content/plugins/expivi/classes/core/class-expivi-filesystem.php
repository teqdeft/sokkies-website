<?php
/**
 * Expivi Filesystem.
 *
 * @package Expivi/Filesystem
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi Filesystem class.
 *
 * @class Expivi_Filesystem
 */
class Expivi_Filesystem {

	const SUBDIR = 'expivi';

	/**
	 * Function to read contents of a file.
	 *
	 * @param string      $filename Name of a file.
	 * @param string|bool $basepath Base path to file. If false, this will point to expivi directory.
	 *
	 * @return string|boolean Returns content of file or FALSE on failure.
	 */
	public function read( $filename, $basepath = false ) {
		// Stop if filename is empty.
		if ( empty( $filename ) ) {
			return false;
		}

		// Retrieve upload directory.
		$fullpath = $this->resolve_path( $filename, $basepath );

		if ( is_wp_error( $fullpath ) ) {
			return false;
		}

		// Retrieve internal filesystem.
		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}

		// Check if file exists.
		if ( ! $fs->exists( $fullpath ) ) {
			return false;
		}

		// Retrieve contents.
		$content = $fs->get_contents( $fullpath );

		// Return contents as string or FALSE on failure.
		return $content;
	}

	/**
	 * Function to write files.
	 * File will be written to upload path with
	 *
	 * @param string      $filename Name of file + extension (path will be stripped).
	 * @param string      $content Content that needs to be written to the file.
	 * @param boolean     $overwrite Whether the content should overwrite or append.
	 * @param string|bool $basepath Base path to file. If false, this will point to expivi directory.
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function write( string $filename, string $content, bool $overwrite = true, $basepath = false ): bool {
		// Stop if filename is empty.
		if ( empty( $filename ) ) {
			return false;
		}

		// Don't do anything if given content is empty and overwrite is false (=append content).
		if ( empty( $content ) && false === $overwrite ) {
			return true;
		}

		// Retrieve basedir values.
		$fullpath = $this->resolve_path( $filename, $basepath );
		if ( false === $fullpath ) {
			return false;
		}

		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}

		// Use chmod setting from wp filesystem.
		$mode = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : false;

		// Write contents to file.
		$file_content = '';
		if ( false === $overwrite && $fs->exists( $fullpath ) ) {
			$file_content = $fs->get_contents( $fullpath );
		}
		$file_content .= $content;

		// Returns true on success, or false on failure.
		return $fs->put_contents( $fullpath, $file_content, $mode );
	}

	/**
	 * Function to copy a file.
	 *
	 * @param string $filename Name of the file to copy.
	 * @param string $source_path The source of the file.
	 * @param string $destination_path The destination of where the file needs to go.
	 * @param bool   $overwrite Optional. Whether to overwrite the destination file if it exists.
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function copy( string $filename, string $source_path, string $destination_path, bool $overwrite = false ): bool {
		$source          = $this->resolve_path( $filename, $source_path );
		$destination     = $this->resolve_path( $filename, $destination_path );
		$destination_dir = dirname( $destination );

		// Check if the paths are resolved correctly.
		if ( false === $source || false === $destination ) {
			return false;
		}

		// Check if source file is present.
		if ( ! $this->is_file( $source ) ) {
			return false;
		}

		// Check if file already exists before copy, to align expected behavior.
		// Note: Default copy behavior will return false if file already exists while overwrite is false.
		if ( false === $overwrite && $this->is_file( $destination ) ) {
			return true;
		}

		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}

		// Create dirs to destination.
		if ( ! $this->is_dir( $destination_dir ) ) {
			$this->mkdir( $destination_dir );
		}

		// Copy file from source to destination using default permissions.
		return $fs->copy( $source, $destination, $overwrite );
	}

	/**
	 * Function to delete a file or directory.
	 *
	 * @param string      $filename File or directory which needs to be deleted.
	 * @param string|bool $basepath Base path to file. If false, this will point to expivi directory.
	 *
	 * @return bool Returns validation whether operation did succeed.
	 */
	public function delete( $filename, $basepath = false ) {
		if ( empty( $filename ) ) {
			return false;
		}

		// Retrieve upload path.
		$fullpath = $this->resolve_path( $filename, $basepath );
		if ( false === $fullpath ) {
			return false;
		}

		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}

		// Check existence and delete file.
		if ( $fs->exists( $fullpath ) ) {
			return $fs->delete( $fullpath, true, false );
		}

		return false;
	}

	/**
	 * Function to check whether a file exists.
	 *
	 * @param string $fullpath Full path to the file.
	 *
	 * @return bool
	 */
	public function exists( string $fullpath ): bool {
		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}
		return $fs->exists( $fullpath );
	}

	/**
	 * Function to create directory (recursively).
	 *
	 * @param string $fullpath Full path to the directory.
	 *
	 * @return bool
	 */
	public function mkdir( string $fullpath ): bool {
		// wp_mkdir_p introduced in WP @version: 2.0.1.
		return wp_mkdir_p( $fullpath );
	}

	/**
	 * Function to check whether given resource is a directory.
	 *
	 * @param string $fullpath Full path to the directory.
	 *
	 * @return bool
	 */
	public function is_dir( string $fullpath ): bool {
		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}
		return $fs->is_dir( $fullpath );
	}

	/**
	 * Function to check whether given recource is a file.
	 *
	 * @param string $fullpath Full path to the file.
	 *
	 * @return bool
	 */
	public function is_file( string $fullpath ): bool {
		$fs = $this->get_internal_fs();
		if ( is_wp_error( $fs ) ) {
			return false;
		}
		return $fs->is_file( $fullpath );
	}

	/**
	 * Function to combine given arguments to a single path.
	 *
	 * @return string Combined path.
	 */
	public function combine(): string {
		$args  = func_get_args();
		$paths = array();

		foreach ( $args as $arg ) {
			if ( strlen( $arg ) > 0 ) {
				$paths[] = $arg;
			}
		}

		return preg_replace( '#/+#', '/', join( '/', $paths ) );
	}

	/**
	 * Function to retrieve all files in given directory.
	 * Note: folders and hidden files will not be included in results.
	 *
	 * @param string $fullpath Full path to the directory.
	 * @param array  $levels Levels of folders to follow.
	 *
	 * @return array List of filenames ([sanitized file name => file name + extension]).
	 */
	public function get_files_in_dir( string $fullpath, $levels = 1 ): array {
		$results = array();

		if ( empty( $fullpath ) || ! $this->is_dir( $fullpath ) ) {
			return $results;
		}

		// list_files introduced in WP @version: 2.6.0.
		$files = list_files( $fullpath, $levels );

		foreach ( $files as $file ) {
			if ( ! $this->is_file( $file ) ) {
				continue;
			}

			$filename       = basename( $file );
			$sanitized_name = sanitize_title( $filename );

			// Save result as [sanitized file name => file name + extension].
			$results[ $sanitized_name ] = $filename;
		}

		return $results;
	}

	/**
	 * Connect with internal WP Filesystem.
	 *
	 * @return WP_Filesystem|WP_Error
	 */
	private function get_internal_fs() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			if ( ! WP_Filesystem() ) {
				return new WP_Error( 500, 'Unable to instantiate WP_Filesystem.' );
			}
		}
		return $wp_filesystem;
	}

	/**
	 * Resolve path with given parameters.
	 *
	 * @param string      $filename Optional filename to append in path.
	 * @param string|bool $basepath Base path to file. If false, this will point to expivi directory.
	 *
	 * @return string|bool Returns path or FALSE on failure.
	 */
	private function resolve_path( string $filename, $basepath = false ) {
		$fullpath = '';
		if ( false === $basepath ) {
			$upload_basedir = xpv_upload_dir( false );
			if ( false === $upload_basedir ) {
				return false;
			}
			$fullpath = $this->combine( $upload_basedir, self::SUBDIR, $filename );
		} else {
			$fullpath = $this->combine( $basepath, $filename );
		}
		return $fullpath;
	}

	/**
	 * Resolve url with given parameters.
	 *
	 * @param string      $filename Optional filename to append in url.
	 * @param string|bool $baseurl Base url to file. If fasle, this will point to expivi directory.
	 *
	 * @return string|boolean Returns url or FALSE on failure.
	 */
	private function resolve_url( string $filename = '', $baseurl = false ) {
		$fullurl = '';
		if ( false === $baseurl ) {
			$upload_baseurl = xpv_upload_url( false );
			if ( false === $upload_baseurl ) {
				return false;
			}
			$fullurl = $this->combine( $upload_baseurl, self::SUBDIR, $filename );
		} else {
			$fullurl = $this->combine( $baseurl, $filename );
		}

		return $fullurl;
	}
}
