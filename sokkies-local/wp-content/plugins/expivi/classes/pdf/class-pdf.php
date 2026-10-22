<?php
/**
 * PDF
 *
 * @package Expivi/PDF
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi PDF class.
 *
 * @class PDF
 */
class PDF {
	/**
	 * PDF document.
	 *
	 * @var Dompdf
	 */
	private $pdf = null;

	/**
	 * PDF Constructor.
	 */
	public function __construct() {
		// Set document information.
		$options = new \Dompdf\Options();

		// Core settings.
		$options->set( 'tempDir', sys_get_temp_dir() );

		// Default settings.
		$options->set( 'isPhpEnabled', true );
		$options->set( 'isJavascriptEnabled', false );
		$options->set( 'isHtml5ParserEnabled', true );
		$options->set( 'isRemoteEnabled', true ); // Enable load image from url.
		$options->set( 'dpi', 96 );
		$options->set( 'defaultMediaType', 'screen' );
		$options->set( 'defaultPaperSize', 'A4' );
		$options->set( 'defaultPaperOrientation', 'portrait' );

		// Font settings.
		$options->set( 'defaultFont', 'Helvetica' );
		$options->set( 'isFontSubsettingEnabled', true );
		$options->set( 'fontHeightRatio ', 1.1 );

		// Debug options.
		$options->set( 'debugPng', false );
		$options->set( 'debugCss', false );
		$options->set( 'debugLayout', false );
		$options->set( 'debugLayoutLines', false );
		$options->set( 'debugLayoutBlocks', false );
		$options->set( 'debugLayoutInline', false );
		$options->set( 'debugLayoutPaddingBox', false );

		// The PDF rendering backend to use.
		// 'PDFLib', 'CPDF', 'GD', and 'auto'.
		$options->set( 'pdfBackend', 'auto' );

		// Initialise new PDF document.
		$this->pdf = new \Dompdf\Dompdf( $options );

		// Set default format/orientation.
		$this->pdf->setPaper( 'a4', 'portrait' );
	}

	/**
	 * Add HTML to current page of pdf.
	 *
	 * @param string $html Text to display.
	 * @param string $encoding Encoding.
	 */
	public function write_html( $html = '', $encoding = null ) {
		$this->pdf->loadHtml( $html, $encoding );
	}

	/**
	 * Sets page format of the document.
	 *
	 * @param string $format The format used for pages. It can be either: A string indicating the page format:
	 * 4a0,2a0,a0,a1,a2,a3,a4,a5,a6,a7,a8,a9,a10
	 * b0,b1,b2,b3,b4,b5,b6,b7,b8,b9,b10
	 * c0,c1,c2,c3,c4,c5,c6,c7,c8,c9,c10
	 * ra0,ra1,ra2,ra3,ra4
	 * sra0,sra1,sra2,sra3,sra4
	 * letter,half-letter,legal,ledger,tabloid,
	 * executive,folio,commercial #10 envelope, catalog #10 1/2 envelope,
	 * 8.5x11,8.5x14,11x17.
	 * @param string $orientation Page orientation: 'portrait' or 'landscape'.
	 */
	public function set_page( $format = 'a4', $orientation = 'portrait' ) {
		$this->pdf->setPaper( $format, $orientation );
	}

	/**
	 * Send the document to a given destination: string, local file or browser.
	 *
	 * @param string $name The name of the file when saved.
	 */
	public function output( $name = 'example.pdf' ) {
		try {
			// Add extension if missing.
			$ext = xpv_filename_extension( $name );
			if ( 'pdf' !== $ext ) {
				$name .= '.pdf';
			}

			// Render the HTML as PDF.
			$this->pdf->render();

			// Output the generated PDF to Browser.
			$this->pdf->stream(
				$name,
				array(
					'compress'   => 1,
					'Attachment' => 1,
				)
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}
	}
}
