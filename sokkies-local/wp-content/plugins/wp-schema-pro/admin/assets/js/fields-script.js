/* eslint-env jquery */
(function ($) {
	$(window).on('load', function () {
		$(
			'.aiosrs-pro-custom-field.aiosrs-pro-custom-field-repeater .aiosrs-pro-repeater-table-wrap'
		).hide();
		$(
			'.aiosrs-pro-custom-field.aiosrs-pro-custom-field-repeater .bsf-repeater-add-new-btn'
		).hide();
		$(
			'.aiosrs-pro-custom-field.aiosrs-pro-custom-field-repeater-target .aiosrs-pro-repeater-table-wrap'
		).hide();
		$(
			'.aiosrs-pro-custom-field.aiosrs-pro-custom-field-repeater-target .bsf-repeater-add-new-btn'
		).hide();

	});

	$(document).ready(function () {
		const { __ } = wp.i18n;
		$('#wpsp-reset-dialog-confirmation').dialog({
			dialogClass: 'wp-dialog',
			autoOpen: false,
			modal: true,
		});

		// Added support to repeater validation.

		$(
			'.wpsp-custom-field-connect, .wpsp-field-close, .bsf-aiosrs-schema-meta-field, image-field-wrap, .aiosrs-pro-custom-field, .wpsp-custom-field-connect'
		).on('click focus change', function () {
			$('.wpsp-local-fields').each(function (index, repeater) {
				let fieldValue = $(repeater)
					.find('.wpsp-default-hidden-value')
					.val();
				const requiredPath = $(repeater)
					.parents('.bsf-aiosrs-schema-row-content')
					.prev();
				fieldValue = fieldValue.trim();
				if (fieldValue) {
					if ($('body').hasClass('block-editor-page')) {
						if (
							!$(repeater).find('.wpsp-required-error-field')
								.length
						) {
							let metaField;
							switch (fieldValue) {
								case 'post_title':
									metaField = $(
										'.editor-post-title__input'
									).val();
									break;
								case 'post_content':
									metaField =
										$(
											'p.block-editor-rich-text__editable'
										).text().length > 1
											? $(
												'p.block-editor-rich-text__editable'
											).text()
											: '';
									break;
								case 'post_excerpt':
									metaField = $(
										'.components-textarea-control__input'
									).val();
									break;
								case 'featured_img':
									if (
										'Set featured image' ===
										$(
											'.editor-post-featured-image__toggle'
										).text()
									) {
										metaField = '';
									} else {
										metaField = $(
											'.components-responsive-wrapper__content'
										).attr('src');
									}
									break;
								default:
									requiredPath.removeClass(
										'wpsp-required-error-field'
									);
							}
							if (undefined !== metaField) {
								if ('' !== metaField) {
									requiredPath.removeClass(
										'wpsp-required-error-field'
									);
								} else if (
									requiredPath.find('.required').length
								) {
									requiredPath.addClass(
										'wpsp-required-error-field'
									);
								}
							}
						}
					} else {
						requiredPath.removeClass('wpsp-required-error-field');
					}
				} else if (requiredPath.find('.required').length) {
					requiredPath.addClass('wpsp-required-error-field');
				}
			});
		});
		$(document).on('click', '.wpsp-show-repeater-field', function () {
			const parent = $(this).parents(
				'.aiosrs-pro-custom-field-repeater'
			);
			parent.find('.aiosrs-pro-repeater-table-wrap').show();
			parent.find('.bsf-repeater-add-new-btn').show();
			parent.find('.wpsp-show-repeater-field').addClass('bsf-hidden');
			parent
				.find('.wpsp-hide-repeater-field')
				.removeClass('bsf-hidden');
		});
		$(document).on('click', '.wpsp-hide-repeater-field', function () {
			const parent = $(this).parents(
				'.aiosrs-pro-custom-field-repeater'
			);
			parent.find('.aiosrs-pro-repeater-table-wrap').hide();
			parent.find('.bsf-repeater-add-new-btn').hide();
			parent.find('.wpsp-hide-repeater-field').addClass('bsf-hidden');
			parent
				.find('.wpsp-show-repeater-field')
				.removeClass('bsf-hidden');
		});
		$(document).on('click', '.wpsp-show-repeater-target-field', function () {
			const parent = $(this).parents(
				'.aiosrs-pro-custom-field-repeater-target'
			);
			parent.find('.aiosrs-pro-repeater-table-wrap').show();
			parent.find('.bsf-repeater-add-new-btn').show();
			parent
				.find('.wpsp-show-repeater-target-field')
				.addClass('bsf-hidden');
			parent
				.find('.wpsp-hide-repeater-target-field')
				.removeClass('bsf-hidden');
		});
		$(document).on('click', '.wpsp-hide-repeater-target-field', function () {
			const parent = $(this).parents(
				'.aiosrs-pro-custom-field-repeater-target'
			);
			parent.find('.aiosrs-pro-repeater-table-wrap').hide();
			parent.find('.bsf-repeater-add-new-btn').hide();
			parent
				.find('.wpsp-hide-repeater-target-field')
				.addClass('bsf-hidden');
			parent
				.find('.wpsp-show-repeater-target-field')
				.removeClass('bsf-hidden');
		});
		$('input[type="checkbox"].wpsp-enable-schema-toggle__input').on(
			'click',
			function () {
				const parent = $(this).parents(
					'.wpsp-enable-schema-markup'
				);
				const thisVal = $(this).val();
				const togglebtn = parent.find('.wpsp-enable-schema-toggle');
				const togglehid = parent.find(
					'.wpsp-enable-schema-toggle__input-hidden'
				);
				const isChecked = togglebtn.hasClass('is-checked');

				if (!isChecked && '1' === thisVal) {
					togglehid.attr('value', '1');
					togglebtn.addClass('is-checked');
				} else {
					togglehid.attr('value', 'disabled');
					togglebtn.removeClass('is-checked');
				}
			}
		);
		$(document).on('change click', function () {
			$('.wpsp-local-fields')
				.find('select, textarea, input')
				.on('change keyup', function (event) {
					if (
						event.isTrigger &&
						!$(this).hasClass('wpsp-specific-field') &&
						!$(this).hasClass('wpsp-date-field')
					) {
						return false;
					}

					const parent = $(this).parents('.wpsp-local-fields');
					parent
						.find('.wpsp-default-hidden-value')
						.val($(this).val());
					parent
						.find('.wpsp-default-hidden-fieldtype')
						.val(
							$(this)
								.parents('.wpsp-parent-field')
								.attr('data-type')
						);

					if (
						$(this).is('select') &&
						$(this).parent().hasClass('wpsp-connect-field')
					) {
						const selectedOption = $(this).val();

						if (
							'create-field' === selectedOption ||
							'specific-field' === selectedOption
						) {
							if ('create-field' === selectedOption) {
								displayCustomField(parent);
								parent
									.find('.wpsp-default-hidden-fieldtype')
									.val('custom-field');
							}
							if ('specific-field' === selectedOption) {
								displaySpecificField(parent);
								parent
									.find('.wpsp-default-hidden-fieldtype')
									.val('specific-field');
							}
							parent
								.find('.wpsp-default-hidden-value')
								.val('');
						}
					}
				});

			$('select.bsf-aiosrs-schema-meta-field').change(function () {
				const parent = $(this).parents('.wpsp-local-fields');
				const label = parent.find('select option:selected').html();

				const selectedOption = $(this).val();

				if (
					'none' === selectedOption ||
					'create-field' === selectedOption ||
					'specific-field' === selectedOption
				) {
					parent
						.find('.bsf-aiosrs-schema-heading-help')
						.attr(
							'title',
							'Please connect any field to apply in the Schema Markup!'
						);
				} else {
					parent
						.find('.bsf-aiosrs-schema-heading-help')
						.attr(
							'title',
							'The ' +
							label +
							' value in this field will be added to the schema markup of this particular post/page.'
						);
				}
			});
			function displaySpecificField(parent) {
				parent.find('.wpsp-connect-field,.wpsp-custom-field').hide();
				parent
					.find('.wpsp-specific-field')
					.removeClass('bsf-hidden')
					.show()
					.find('select, textarea, input')
					.val('');
			}

			function displayCustomField(parent) {
				parent
					.find('.wpsp-connect-field,.wpsp-specific-field')
					.hide();
				parent
					.find('.wpsp-custom-field')
					.removeClass('bsf-hidden')
					.show()
					.find('select, textarea, input')
					.val('');
			}

			$(document).on('click', '.wpsp-field-close', function () {
				const parent = $(this).parents('.wpsp-local-fields');
				const select = parent
					.find('.wpsp-connect-field')
					.removeClass('bsf-hidden')
					.show()
					.find('select')
					.removeAttr('disabled');
				const selectVal = select.val();
				if ('specific-field' === selectVal) {
					parent.find('.wpsp-default-hidden-value').val('');
					parent
						.find('.wpsp-default-hidden-fieldtype')
						.val('specific-field');
					displaySpecificField(parent);
					return;
				}
				parent.find('.wpsp-default-hidden-value').val('');
				parent
					.find('.wpsp-default-hidden-fieldtype')
					.val('custom-field');
				displayCustomField(parent);
			});

			$(document).on(
				'click',
				'.wpsp-specific-field-connect, .wpsp-custom-field-connect',
				function () {
					const parent = $(this).parents('.wpsp-local-fields');
					const select = parent
						.find('.wpsp-connect-field')
						.removeClass('bsf-hidden')
						.show()
						.find('select')
						.removeAttr('disabled');

					let selectVal = select.val();

					if (
						'create-field' === selectVal ||
						'specific-field' === selectVal
					) {
						selectVal = 'none';
					}

					parent
						.find('.wpsp-default-hidden-value')
						.val(selectVal);
					parent
						.find('.wpsp-default-hidden-fieldtype')
						.val('global-field');
					parent
						.find('.wpsp-custom-field, .wpsp-specific-field')
						.hide();
				}
			);
		});
		$(document).on('change input', '.bsf-rating-field', function () {
			const starWrap = $(this).next('.aiosrs-star-rating-wrap');
			const maxRating = parseFloat($(this).attr('data-max-rating')) || 5;
			let value = parseFloat($(this).val());

			if (isNaN(value) || value < 0) {
				value = 0;
			} else if (value > maxRating) {
				value = maxRating;
			}

			// Normalize the entered value to a 5-star display, matching the
			// server-side get_star_rating_markup() logic so a 1-10 rating maps
			// onto five stars (e.g. 8/10 shows four filled stars).
			const rating = maxRating > 0 ? (value / maxRating) * 5 : value;
			starWrap.find('span').each(function (index) {
				const position = index + 1;
				$(this).removeClass(
					'dashicons-star-filled dashicons-star-half dashicons-star-empty'
				);
				if (position <= rating) {
					$(this).addClass('dashicons-star-filled');
				} else if (position - rating < 1) {
					$(this).addClass('dashicons-star-half');
				} else {
					$(this).addClass('dashicons-star-empty');
				}
			});
		});
		$(document).on(
			'click',
			'.aiosrs-star-rating-wrap:not(.disabled) > .aiosrs-star-rating',
			function (e) {
				e.preventDefault();
				const index = $(this).data('index');
				const starWrap = $(this).parent();
				const parent = $(this).parents('.wpsp-local-fields');
				const ratingInput = starWrap.prev('.bsf-rating-field');
				const maxRating =
					parseFloat(ratingInput.attr('data-max-rating')) || 5;
				// Map the clicked star (1-5) back to the configured scale so a
				// 1-10 rating can reach its full range (e.g. the 5th star = 10).
				const value = (index / 5) * maxRating;

				ratingInput.val(value);
				parent.find('.wpsp-default-hidden-value').val(value);
				starWrap.find('.aiosrs-star-rating').each(function (i) {
					$(this).removeClass(
						'dashicons-star-filled dashicons-star-half dashicons-star-empty'
					);
					if (i < index) {
						$(this).addClass('dashicons-star-filled');
					} else {
						$(this).addClass('dashicons-star-empty');
					}
				});
			}
		);
		// Keep the rating input's max in sync when its "Rating Scale" dropdown
		// changes, so a value up to the selected scale can be entered without a
		// save/reload. Matched by name (the scale dropdown's class differs
		// between the meta box and the schema config editor, and per item type),
		// so this covers every rating/scale pair. The rating field shares the
		// scale field's name minus the "-scale" token (rating-scale-custom ->
		// rating-custom, rating-scale-custom-text -> rating-custom-text).
		$(document).on(
			'change',
			'select[name*="rating-scale-custom"]',
			function () {
				const scaleName = $(this).attr('name') || '';
				const ratingName = scaleName.replace('-scale-custom', '-custom');
				if (ratingName === scaleName) {
					return;
				}
				const maxRating = $(this).val() === '1-10' ? 10 : 5;
				$('input.bsf-rating-field')
					.filter(function () {
						return this.name === ratingName;
					})
					.attr('max', maxRating)
					.attr('data-max-rating', maxRating)
					.each(function () {
						// Clamp a now out-of-range value (e.g. 8 after switching
						// 1-10 -> 1-5) so the input is not left :invalid, which can
						// block saving in the classic editor. Keep the resolved
						// hidden value in step so the clamped value persists.
						const current = parseFloat( this.value );
						if ( ! isNaN( current ) && current > maxRating ) {
							this.value = maxRating;
							const wrap = this.closest( '.wpsp-local-fields' );
							if ( wrap ) {
								const hidden = wrap.querySelector(
									'.wpsp-default-hidden-value'
								);
								if ( hidden ) {
									hidden.value = maxRating;
								}
							}
						}
						// Re-render the star display against the new scale.
						this.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					});
			}
		);
		$(document).on(
			'change',
			'#aiosrs-pro-custom-fields .aiosrs-pro-custom-field-checkbox input[type="checkbox"]',
			function (e) {
				e.preventDefault();

				const siblings = $(this)
					.closest('tr.row')
					.siblings('tr.row');
				if ($(this).prop('checked')) {
					siblings.show();
				} else {
					siblings.hide();
				}
			}
		);
		$(
			'#aiosrs-pro-custom-fields .aiosrs-pro-custom-field-checkbox input[type="checkbox"]'
		).trigger('change');
		$(document.body).on(
			'change',
			'#aiosrs-pro-custom-fields .wpsp-enable-schema-markup input[type="checkbox"].wpsp-enable-schema-toggle__input',
			function (e) {
				e.preventDefault();

				const parent = $(this).parents(
					'.wpsp-enable-schema-markup'
				);

				if ($(this).prop('checked')) {
					parent
						.find('.wpsp-enable-schema-toggle')
						.addClass('is-checked');
					parent
						.find('.wpsp-enable-schema-toggle__input-hidden')
						.attr('value', '1');
				} else {
					parent
						.find('.wpsp-enable-schema-toggle')
						.removeClass('is-checked');
					parent
						.find('.wpsp-enable-schema-toggle__input-hidden')
						.attr('value', 'disabled');
				}
			}
		);
		$(
			'#aiosrs-pro-custom-fields .wpsp-enable-schema-markup input[type="checkbox"].wpsp-enable-schema-toggle__input'
		).trigger('change');
		$(document).on('click', '.aiosrs-reset-rating', function (e) {
			e.preventDefault();
			const thisObj = $(this);
			const parent = thisObj.closest('.aiosrs-pro-custom-field-rating');

			const ajaxData = {
				action: 'aiosrs_reset_post_rating',
				post_id: thisObj.data('post-id'),
				schema_id: thisObj.data('schema-id'),
				nonce: thisObj.data('nonce'),
			};

			$('#wpsp-reset-dialog-confirmation').dialog({
				resizable: false,
				title: __('Confirmation Required!', 'wp-schema-pro'),
				height: 'auto',
				width: 400,
				modal: true,
				open() {
					$(this)
						.closest('.ui-dialog')
						.find('.ui-dialog-titlebar-close')
						.hide();
					const markup =
						'<p><span class="dashicons dashicons-trash"></span> Do you really want to reset current post rating?</p>';
					$(this).html(markup);
				},
				buttons: {
					Yes() {
						thisObj.addClass('reset-disabled');
						parent.find('.spinner').addClass('is-active');
						jQuery
							.ajax({
								url: ajaxurl,
								type: 'post',
								dataType: 'json',
								data: ajaxData,
							})
							.success(function (response) {
								if (
									'undefined' !== typeof response.success &&
									response.success === true
								) {
									const avgRating = response['rating-avg'],
										reviewCount =
											response['review-count'];
									parent
										.find('.aiosrs-rating')
										.text(avgRating);
									parent
										.find('.aiosrs-rating-count')
										.text(reviewCount);
									parent
										.find(
											'.aiosrs-star-rating-wrap > .aiosrs-star-rating'
										)
										.removeClass(
											'dashicons-star-filled dashicons-star-half dashicons-star-empty'
										)
										.addClass('dashicons-star-empty');
								} else {
									thisObj.removeClass('reset-disabled');
								}
								parent
									.find('.spinner')
									.removeClass('is-active');
							});
						$(this).dialog('close');
					},
					Cancel() {
						$(this).dialog('close');
					},
				},
			});
			$('#wpsp-reset-dialog-confirmation').dialog('open');
		});
		$(document).on('change', '.multi-select-wrap select', function () {
			const multiselectWrap = $(this).closest('.multi-select-wrap'),
				selectWrap = multiselectWrap.find('select'),
				inputField = multiselectWrap.find('input[type="hidden"]'),
				value = selectWrap.val();

			if (
				'undefined' !== typeof value &&
				null !== value &&
				value.length > 0
			) {
				inputField.val(value.join(','));
			} else {
				inputField.val('');
			}
		});

		// Verticle Tabs
		$(document).on(
			'click',
			'.aiosrs-pro-meta-fields-tab',
			function (e) {
				e.preventDefault();

				const id = $(this).data('tab-id');
				$(this)
					.siblings('.aiosrs-pro-meta-fields-tab')
					.removeClass('active');
				$(this).addClass('active');

				$('#aiosrs-pro-custom-fields')
					.find('.aiosrs-pro-meta-fields-wrap')
					.removeClass('open');
				$('#aiosrs-pro-custom-fields')
					.find('.' + id)
					.addClass('open');
			}
		);

		// Toggle Js for Enable Schema Markup.

		$(document.body).on(
			'change',
			'#aiosrs-pro-custom-fields .wpsp-enable-schema-markup .wpsp-enable-schema-toggle',
			function () {
				const parent = $(this).parents(
					'.aiosrs-pro-meta-fields-tab'
				);
				const parents = $(this).parents('.inside');
				const id = parent.data('tab-id');
				const hasClass = parents
					.find('.aiosrs-pro-meta-fields-wrapper')
					.find('.' + id)
					.hasClass('is-enable-schema-markup');
				const isChecked = parent
					.find('.wpsp-enable-schema-toggle')
					.hasClass('is-checked');

				if (!hasClass && !isChecked) {
					parents
						.find('.aiosrs-pro-meta-fields-wrapper')
						.find('.' + id)
						.addClass('is-enable-schema-markup');
				}
			}
		);

		$(
			'#aiosrs-pro-custom-fields .wpsp-enable-schema-markup .wpsp-enable-schema-toggle'
		).trigger('change');

		$('.wpsp-enable-schema-toggle').on('click', function () {
			const parent = $(this).parents('.aiosrs-pro-meta-fields-tab');
			const parents = $(this).parents('.inside');
			const id = parent.data('tab-id');

			parents
				.find('.aiosrs-pro-meta-fields-wrapper')
				.find('.' + id)
				.toggleClass('is-enable-schema-markup');
		});

		// Call Tooltip
		$('.bsf-aiosrs-schema-heading-help').tooltip({
			content() {
				return $(this).prop('title');
			},
			tooltipClass: 'bsf-aiosrs-schema-ui-tooltip',
			position: {
				my: 'center top',
				at: 'center bottom+10',
			},
			hide: {
				duration: 200,
			},
			show: {
				duration: 200,
			},
		});

		let fileFrame;
		window.inputWrapper = '';

		$(document.body).on(
			'click',
			'.image-field-wrap .aiosrs-image-select',
			function (e) {
				e.preventDefault();

				window.inputWrapper = $(this).closest(
					'.bsf-aiosrs-schema-custom-text-wrap, .aiosrs-pro-custom-field-image'
				);

				// Create the media frame.
				fileFrame = wp.media({
					button: {
						text: 'Select Image',
						close: false,
					},
					states: [
						new wp.media.controller.Library({
							title: __('Select Custom Image', 'wp-schema-pro'),
							library: wp.media.query({ type: 'image' }),
							multiple: false,
						}),
					],
				});

				// When an image is selected, run a callback.
				fileFrame.on('select', function () {
					const attachment = fileFrame
						.state()
						.get('selection')
						.first()
						.toJSON();

					const image = window.inputWrapper.find(
						'.image-field-wrap img'
					);
					if (image.length === 0) {
						window.inputWrapper
							.find('.image-field-wrap')
							.append(
								'<a href="#" class="aiosrs-image-select img"><img src="' +
								attachment.url +
								'" /></a>'
							);
					} else {
						image.attr('src', attachment.url);
					}
					window.inputWrapper
						.find('.image-field-wrap')
						.addClass('bsf-custom-image-selected');
					window.inputWrapper
						.find('.single-image-field')
						.val(attachment.id);

					const parent = window.inputWrapper.parents(
						'.wpsp-local-fields'
					);
					parent
						.find('.wpsp-default-hidden-value')
						.val(attachment.id);
					parent
						.find('.wpsp-default-hidden-fieldtype')
						.val(
							window.inputWrapper
								.parents('.wpsp-parent-field')
								.attr('data-type')
						);

					fileFrame.close();
				});

				fileFrame.open();
			}
		);

		$(document).on('click', '.aiosrs-image-remove', function (e) {
			e.preventDefault();
			const parent = $(this).closest(
				'.bsf-aiosrs-schema-custom-text-wrap, .aiosrs-pro-custom-field-image'
			);
			parent
				.find('.image-field-wrap')
				.removeClass('bsf-custom-image-selected');
			parent.find('.single-image-field').val('');
			parent.find('.image-field-wrap img').removeAttr('src');
		});

		window.inputWrapper = '';
	});
})(jQuery);
