/**
 * Returns the document that hosts the block editor canvas.
 *
 * With Block API v3 the editor canvas is rendered inside an iframe
 * (name="editor-canvas"), so dynamically generated <style> tags must be
 * appended to the iframe's document — not the top-level admin document.
 * Falls back to the top-level document when the canvas is not iframed.
 *
 * @since 2.12.0
 *
 * @return {Document} The editor canvas document.
 */
const getEditorDocument = () => {
	const canvas = document.querySelector('iframe[name="editor-canvas"]');

	return canvas && canvas.contentDocument ? canvas.contentDocument : document;
};

export default getEditorDocument;
