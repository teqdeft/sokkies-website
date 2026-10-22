(function () {
  let attempts = 0;
  const maxAttempts = 20;
  const checkInterval = 500;

  const intervalId = setInterval(function () {
    attempts++;

    if (window.taggingHelpers) {
      clearInterval(intervalId);
      runScript();
    } else if (attempts >= maxAttempts) {
      clearInterval(intervalId);
      runScript();
      console.warn(
        "taggingHelpers not available after " + maxAttempts + " attempts"
      );
    }
  }, checkInterval);
})();

function runScript() {
  var taggingEvents = {
    userData: function () {
      let deviceInfo;
      let marketing;
      try {
        deviceInfo = window.taggingHelpers.getDeviceInfo();
        marketing = window.taggingHelpers.getMarketingObject();
      } catch (e) {
        console.warn(e);
      }

      this._sendToDataLayer("trytagging_user_data", {
        device: deviceInfo ?? {},
        page: {
          location: window.location.href,
          title: document.title,
        },
        cart: {
          total: window.cartObj?.ecommerce?.value ?? 0.0,
        },
      });

      // Only set cookie if cookie keeper is not disabled and method is not php_only
      if (
        !window.varGtmTryTagging?.disable_cookie_keeper &&
        window.varGtmTryTagging?.cookie_keeper_method === "both"
      ) {
        const expires = new Date();
        expires.setTime(expires.getTime() + 7 * 24 * 60 * 60 * 1000);
        document.cookie = `trytagging_user_data=${btoa(
          JSON.stringify(marketing ?? {})
        )};expires=${expires.toUTCString()};path=/`;
      }
    },
    purchase: function (data) {
      this._sendToDataLayer("trytagging_purchase", data);
    },
    beginCheckout: function (data) {
      this._sendToDataLayer("trytagging_begin_checkout", data);
    },
    viewItem: function (data) {
      this._sendToDataLayer("trytagging_view_item", data);
    },
    viewItemList: function (data) {
      this._sendToDataLayer("trytagging_view_item_list", data);
    },
    viewCart: function (data) {
      this._sendToDataLayer("trytagging_view_cart", data);
    },
    removeFromCart: function (data) {
      this._sendToDataLayer("trytagging_remove_from_cart", data);
    },
    addToCart: function (data) {
      localStorage.setItem(
        "tagging_add_to_cart_item",
        data.ecommerce.items[0].item_id
      );

      this._sendToDataLayer("trytagging_add_to_cart", data);
    },
    addPaymentInfo: function (data) {
      this._sendToDataLayer("trytagging_add_payment_info", data);
    },
    addShippingInfo: function (data) {
      this._sendToDataLayer("trytagging_add_shipping_info", data);
    },
    generateLead: function (data) {
      this._sendToDataLayer("trytagging_generate_lead", data);
    },
    _sendToDataLayer: function (eventName, data) {
      let marketing;
      try {
        marketing = window.taggingHelpers.getMarketingObject();
      } catch (e) {
        console.warn(e);
      }
      data.event = eventName;
      data.marketing = marketing ?? {};
      data.event_time = new Date().toISOString();

      if (window.dataLayer) {
        if (data.ecommerce) {
          window.dataLayer.push({ ecommerce: null });
        }
        window.dataLayer.push(data);
      } else {
        console.warn("Datalayer not found...");
        console.log(data);
      }
    },
  };

  function formatPrice(price) {
    return parseFloat(parseFloat(price).toFixed(2));
  }

  function handleAddToCart(item, currency) {
    try {
      if (item.item_category) {
        const categories = item.item_category.split(",");
        for (var i = 0; i < 5; i++) {
          if (categories[i] == undefined) {
            break;
          }
          if (i == 0) {
            item.item_category = categories[i];
          } else {
            item["item_category_" + (i + 1)] = categories[i];
          }
        }
      }
      if (item.quantity) {
        item.quantity = parseInt(item.quantity);

        if (item.quantity < 1) {
          item.quantity = 1;
        }
      } else {
        item.quantity = 1;
      }

      if (item.price) {
        item.price = formatPrice(item.price);
      }

      localStorage.setItem("tagging_add_to_cart_item", item.item_id);
      taggingEvents.addToCart({
        ecommerce: {
          value: formatPrice(item.price * item.quantity),
          currency,
          items: [item],
        },
      });
    } catch (e) {
      console.warn(e);
    }
  }

  jQuery(document).ready(function () {
    taggingEvents.userData();

    if (window.viewItem) {
      taggingEvents.viewItem({
        ecommerce: {
          currency: window.viewItem.currency,
          value: window.viewItem.value,
          items: window.viewItem.items,
        },
      });
    }

    // This is triggerd by the add to cart button on the product detail page
    if (
      window.addToCart &&
      window.addToCart.ecommerce &&
      window.localStorage.getItem("tagging_add_to_cart_item") === null
    ) {
      handleAddToCart(
        window.addToCart.ecommerce.items[0],
        window.addToCart.ecommerce.currency
      );
    }

    jQuery(document).on(
      "click",
      "[data-action='click->product#addToCart']",
      function (e) {
        try {
          const $button = jQuery(this);
          if ($button.hasClass("disabled")) {
            return;
          }

          // Check for variation data
          const $form = $button.closest("form");
          const variationId = $form.find(".variation_id").val();
          var viewItemEvent = window.viewItem;
          let buttonData;

          // Check if we have a variation and variation data is available
          if (
            variationId &&
            window.viewItemVariants &&
            window.viewItemVariants[variationId]
          ) {
            // Use the specific variation data
            const variationData = window.viewItemVariants[variationId];
            buttonData = {
              item_id: variationData.item_id,
              item_sku: variationData.item_sku,
              item_name: variationData.item_name,
              item_category: variationData.item_category,
              item_variant: variationData.item_variant || "default",
              quantity: jQuery(this).data("quantity") || 1,
              price: formatPrice(variationData.price),
            };

            if (variationData.discount) {
              buttonData.discount = formatPrice(variationData.discount);
            }
          } else {
            // Fallback to original logic for non-variable products
            buttonData = {
              item_id: viewItemEvent.items[0].item_id,
              item_sku: viewItemEvent.items[0].item_sku,
              item_name: viewItemEvent.items[0].item_name,
              item_category: viewItemEvent.items[0].item_category,
              item_variant: "default",
              quantity: jQuery(this).data("quantity") || 1,
              price: formatPrice(viewItemEvent.items[0].price),
            };

            if (viewItemEvent.items[0].discount) {
              buttonData.discount = formatPrice(
                viewItemEvent.items[0].discount
              );
            }
          }

          const currency =
            window.viewItemList?.currency || window.viewItem?.currency;
          handleAddToCart(buttonData, currency);
        } catch (e) {
          console.warn(e);
        }
      }
    );

    document.addEventListener(
      "click",
      function (e) {
        try {
          const button = e.target.closest(".single_add_to_cart_button");
          if (!button) {
            return;
          }

          if (button.classList.contains("disabled")) {
            return;
          }

          var $elForm = jQuery(button).closest("form.cart");
          const quantity = $elForm.find("[name=quantity]").val();
          const variationId = $elForm.find(".variation_id").val();
          const viewItemEvent = window.viewItem;
          let item;

          if (viewItemEvent) {
            // Check if we have a variation and variation data is available
            if (
              variationId &&
              window.viewItemVariants &&
              window.viewItemVariants[variationId]
            ) {
              // Use the specific variation data
              const variationData = window.viewItemVariants[variationId];
              item = {
                item_id: variationData.item_id,
                item_sku: variationData.item_sku,
                item_name: variationData.item_name,
                affiliation: variationData.affiliation || "",
                discount: formatPrice(variationData.discount ?? 0.0),
                item_brand: variationData.item_brand || "",
                item_category: variationData.item_category || "",
                item_category2: variationData.item_category2 || "",
                item_category3: variationData.item_category3 || "",
                item_category4: variationData.item_category4 || "",
                item_category5: variationData.item_category5 || "",
                item_list_id: variationData.item_list_id || "",
                item_list_name: variationData.item_list_name || "",
                item_variant: variationData.item_variant || "default",
                location_id: variationData.location_id || "",
                price: formatPrice(variationData.price ?? 0.0),
                quantity: quantity,
              };
            } else {
              // Fallback to original logic for non-variable products
              item = {
                item_id: viewItemEvent.items[0].item_id,
                item_sku: viewItemEvent.items[0].item_sku,
                item_name: viewItemEvent.items[0].item_name,
                affiliation: viewItemEvent.items[0].affiliation || "",
                discount: formatPrice(viewItemEvent.items[0].discount ?? 0.0),
                item_brand: viewItemEvent.items[0].item_brand || "",
                item_category: viewItemEvent.items[0].item_category || "",
                item_category2: viewItemEvent.items[0].item_category2 || "",
                item_category3: viewItemEvent.items[0].item_category3 || "",
                item_category4: viewItemEvent.items[0].item_category4 || "",
                item_category5: viewItemEvent.items[0].item_category5 || "",
                item_list_id: viewItemEvent.items[0].item_list_id || "",
                item_list_name: viewItemEvent.items[0].item_list_name || "",
                item_variant: viewItemEvent.items[0].item_variant || "default",
                location_id: viewItemEvent.items[0].location_id || "",
                price: formatPrice(viewItemEvent.items[0].price ?? 0.0),
                quantity: quantity,
              };
            }
          }

          handleAddToCart(item, viewItemEvent.currency);
        } catch (e) {
          console.warn(e);
        }
      },
      true
    );

    // Use capture phase to run before WooCommerce's handler which may call stopImmediatePropagation
    document.addEventListener(
      "click",
      function (e) {
        try {
          const button = e.target.closest(".add_to_cart_button");
          if (!button) {
            return;
          }

          if (
            button.classList.contains("product_type_variable") ||
            button.classList.contains("product_type_grouped") ||
            button.classList.contains("single_add_to_cart_button")
          ) {
            console.log("Skipping add to cart button");
            return;
          }
          console.log("Adding add to cart button");
          const attributes = button.attributes;
          const currency =
            window.viewItemList?.ecommerce?.currency ||
            window.viewItem?.currency;

          var item = {
            item_id: attributes["data-product-id"].value,
            item_sku: attributes["data-product-sku"].value,
            item_name: attributes["data-product-name"].value,
            affiliation: attributes["data-product-affiliation"].value,
            discount: formatPrice(attributes["data-product-discount"].value),
            item_brand: attributes["data-product-brand"].value,
            item_category: attributes["data-product-category"].value,
            item_category2: attributes["data-product-category2"].value,
            item_category3: attributes["data-product-category3"].value,
            item_category4: attributes["data-product-category4"].value,
            item_category5: attributes["data-product-category5"].value,
            item_list_id: attributes["data-product-list-id"].value,
            item_list_name: attributes["data-product-list-name"].value,
            item_variant: attributes["data-product-variant"].value,
            location_id: attributes["data-product-location-id"].value,
            price: formatPrice(attributes["data-product-price"].value),
            quantity: attributes["data-product-quantity"].value,
            price_excluding_vat: formatPrice(
              attributes["data-product-price-excluding-vat"].value
            ),
          };
          handleAddToCart(item, currency);
        } catch (e) {
          console.warn(e);
        }
      },
      true
    );

    if (window.viewItemList) {
      taggingEvents.viewItemList(window.viewItemList);
    }

    if (window.viewCart) {
      taggingEvents.viewCart(window.viewCart);
    }

    if (window.beginCheckout) {
      taggingEvents.beginCheckout(window.beginCheckout);
    }

    if (window.purchase) {
      var transactionId =
        window.purchase &&
        window.purchase.ecommerce &&
        window.purchase.ecommerce.transaction_id;
      if (transactionId) {
        var purchaseKey = "tagging_purchase_" + transactionId;
        if (!window.localStorage.getItem(purchaseKey)) {
          taggingEvents.purchase(window.purchase);
          window.localStorage.setItem(purchaseKey, "true");
        }
      }
    }

    window.localStorage.removeItem("tagging_add_to_cart_item");

    // Remove from cart events
    jQuery(document).on(
      "removed_from_cart",
      function (e, fragments, cart_hash, $thisbutton) {
        var data = $thisbutton.data();

        if (data.product_id) {
          var cart = window.cartObj.ecommerce;
          var item = cart.items.find((item) => {
            return item.item_id == data.product_id;
          });

          if (!item) {
            return;
          }

          taggingEvents.removeFromCart({
            ecommerce: {
              items: [item],
              value: item.price * item.quantity,
              currency: cart.currency,
            },
          });
        }
      }
    );

    function getItemByTableRow(row, rowClass) {
      const allRows = Array.from(
        document.querySelectorAll(
          rowClass ?? ".woocommerce-cart-form__cart-item"
        )
      );
      const rowElement = row[0] || row;
      const rowIndex = allRows.indexOf(rowElement);
      return window.cartObj.ecommerce.items[rowIndex];
    }

    document.addEventListener("click", function (e) {
      try {
        if (e.target.classList.contains("wc-block-cart-item__remove-link")) {
          const $row = jQuery(e.target).closest("tr");
          const item = getItemByTableRow($row, ".wc-block-cart-items__row");

          if (!item) {
            return;
          }

          window.cartObj.ecommerce.items =
            window.cartObj.ecommerce.items.filter(
              (cart_item) => cart_item.item_id !== item.item_id
            );

          taggingEvents.removeFromCart({
            ecommerce: {
              items: [item],
              value: item.price * item.quantity,
              currency: window.cartObj.ecommerce.currency,
            },
          });
        }
      } catch (e) {
        console.warn(e);
      }
    });

    jQuery(document).on(
      "click",
      ".woocommerce-cart-form .product-remove > a",
      function (e) {
        var $el = jQuery(e.currentTarget);
        var data = $el.data();

        if (data.product_id) {
          var cart = window.cartObj.ecommerce;
          var item = cart.items.find((item) => {
            return item.item_id == data.product_id;
          });

          if (!item) {
            item = getItemByTableRow(this.closest("tr"));
            if (!item) {
              return;
            }
          }

          window.cartObj.ecommerce.items =
            window.cartObj.ecommerce.items.filter(
              (cart_item) => cart_item.item_id !== item.item_id
            );

          taggingEvents.removeFromCart({
            ecommerce: {
              items: [item],
              value: item.price * item.quantity,
              currency: cart.currency,
            },
          });
        }
      }
    );

    jQuery(document).on("click", ".remove", function (e) {
      try {
        var $el = jQuery(e.currentTarget);
        var data = $el.data();

        if (data.product_id) {
          var cart = window.cartObj.ecommerce;
          var item = cart.items.find((item) => {
            return item.item_id == data.product_id;
          });

          if (!item) {
            return;
          }

          taggingEvents.removeFromCart({
            ecommerce: {
              items: [item],
              value: parseFloat((item.price * item.quantity).toFixed(2)),
              currency: cart.currency,
            },
          });
        }
      } catch (e) {
        console.warn("Unable to remove item from cart", e);
      }
    });

    if (window.removeFromCart) {
      taggingEvents.removeFromCart({
        ecommerce: {
          items: window.removeFromCart.items,
          value: window.removeFromCart.value,
          currency: window.removeFromCart.currency,
        },
      });
    }

    // Add Contact Form 7 submission handler
    jQuery(document).on("wpcf7submit", function (event) {
      try {
        if (
          event.detail.status !== "mail_sent" &&
          event.detail.status !== "mail_failed"
        ) {
          return;
        }
        const formData = {};
        const inputs = event.detail.inputs;

        inputs.forEach((input) => {
          formData[input.name] = input.value;
        });
        let marketing;

        try {
          marketing = window.taggingHelpers.getMarketingObject();
        } catch (e) {
          console.warn(e);
        }

        taggingEvents.generateLead({
          form_data: formData,
          form_meta: {
            form_id: event.detail.id,
            form_name: event.detail.name,
            form_type: event.detail.type,
          },
          user_data: marketing ?? {},
        });
      } catch (e) {
        console.warn(e);
      }
    });

    jQuery(document).on("click", ".gform_button", function (e) {
      try {
        const $form = jQuery(this).closest("form");
        const formData = {};

        formData.form_id = $form.attr("data-formid");

        $form.find("input, select, textarea").each(function () {
          const $input = jQuery(this);
          const name = $input.attr("name");

          if (name && !name.includes("gform_")) {
            const inputId = $input.attr("id");
            const $label = jQuery(`label[for="${inputId}"]`);
            const labelText = $label.text().trim();

            let value = $input.val();
            if ($input.attr("type") === "password") {
              value = "********";
            }

            if (labelText) {
              const key = labelText.toLowerCase().replace(/[^a-z0-9]/g, "_");
              formData[key] = value;
            } else {
              const key = name.toLowerCase().replace(/[^a-z0-9]/g, "_");
              formData[key] = value;
            }
          }
        });
        try {
          localStorage.setItem("gravityFormData", JSON.stringify(formData));
        } catch (error) {
          console.warn("Failed to save form data to local storage:", error);
        }
      } catch (e) {
        console.warn(e);
      }
    });

    jQuery(document).on("gform_confirmation_loaded", function (e, form_id) {
      sendGravityFormEventFromStorage();
    });
  });

  function sendGravityFormEventFromStorage() {
    try {
      const gravityFormData = localStorage.getItem("gravityFormData");
      let marketing;
      try {
        marketing = window.taggingHelpers.getMarketingObject();
      } catch (e) {
        console.warn(e);
      }

      if (gravityFormData) {
        taggingEvents.generateLead({
          form_data: JSON.parse(gravityFormData),
          form_meta: {
            form_id: gravityFormData.form_id || "",
            form_name: gravityFormData.form_name || "",
            form_type: gravityFormData.form_type || "",
          },
          user_data: marketing ?? {},
        });
        localStorage.removeItem("gravityFormData");
      }
    } catch (e) {
      console.warn(e);
    }
  }

  if (document.readyState === "loading") {
    window.addEventListener("load", () => {
      if (!document.querySelector(".gform_validation_errors")) {
        sendGravityFormEventFromStorage();
      }
    });
  } else {
    if (!document.querySelector(".gform_validation_errors")) {
      sendGravityFormEventFromStorage();
    }
  }

  // Checkout events functions
  const CHECKOUT_STEPS = {
    SHIPPING_METHOD: "shipping_method",
    PAYMENT_METHOD: "payment_method",
  };

  const checkout_form = jQuery(
    "form.checkout, form.wc-block-components-form form.wc-block-checkout__form"
  );
  const fired_checkout_steps = new Set();

  const handleCheckoutPlaceOrder = () => {
    if (!fired_checkout_steps.has(CHECKOUT_STEPS.SHIPPING_METHOD)) {
      handleShippingInfoChanged();
    }

    if (!fired_checkout_steps.has(CHECKOUT_STEPS.PAYMENT_METHOD)) {
      handlePaymentMethodChanged();
    }
  };

  checkout_form.on("checkout_place_order", () => {
    handleCheckoutPlaceOrder();
  });

  function getSelectedInputValue(selector, fallbackSelector) {
    let element =
      document.querySelector(`${selector}:checked`) ||
      document.querySelector(fallbackSelector);

    if (element) {
      return element.value;
    }

    const blocksPaymentInput = document.querySelector(
      '.wc-block-components-radio-control__input[name="radio-control-wc-payment-method-options"]:checked'
    );

    if (blocksPaymentInput) {
      return blocksPaymentInput.value;
    }

    return null;
  }

  function handleShippingInfoChanged() {
    if (
      fired_checkout_steps.has(CHECKOUT_STEPS.SHIPPING_METHOD) ||
      document.readyState !== "complete"
    ) {
      return;
    }

    const shipping_tier =
      getSelectedInputValue(
        "input[name^=shipping_method]",
        "input[name^=shipping_method]"
      ) || "(shipping tier not found)";

    taggingEvents.addShippingInfo({
      ecommerce: {
        ...window.beginCheckout.ecommerce,
        shipping_tier,
      },
    });
    fired_checkout_steps.add(CHECKOUT_STEPS.SHIPPING_METHOD);
  }

  function handlePaymentMethodChanged() {
    if (
      fired_checkout_steps.has(CHECKOUT_STEPS.PAYMENT_METHOD) ||
      document.readyState !== "complete"
    ) {
      return;
    }

    if (!window.varGtmTryTagging?.currency || !window.beginCheckout?.value) {
      console.warn(
        "Missing required currency or value for payment info tracking"
      );
      return;
    }

    const payment_type =
      getSelectedInputValue(
        ".payment_methods input[name=payment_method]",
        "input[name=payment_method]"
      ) ||
      jQuery("input[name=payment_method]:checked").val() ||
      jQuery("input[name=payment_method]:first").val() ||
      "(payment type not found)";

    window.addPaymentInfo = {
      ...window.beginCheckout.user_data,
      ecommerce: {
        ...window.beginCheckout.ecommerce,
        payment_type,
      },
    };

    taggingEvents.addPaymentInfo(window.addPaymentInfo);
    fired_checkout_steps.add(CHECKOUT_STEPS.PAYMENT_METHOD);
  }

  document.addEventListener("click", (e) => {
    if (
      e.target.classList.contains(
        "wc-block-components-checkout-place-order-button"
      )
    ) {
      handlePaymentMethodChanged();
    }
  });

  document.addEventListener("change", (e) => {
    const isClassicPaymentInput =
      e.target.name === "payment_method" ||
      e.target.closest("input[name=payment_method]");
    const isBlocksPaymentInput =
      e.target.name === "radio-control-wc-payment-method-options";

    if (isClassicPaymentInput || isBlocksPaymentInput) {
      handlePaymentMethodChanged();
    }
  });

  // Cookie keeper functions
  function generateUUID() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
      /[xy]/g,
      function (c) {
        var r = (Math.random() * 16) | 0,
          v = c == "x" ? r : (r & 0x3) | 0x8;
        return v.toString(16);
      }
    );
  }

  function setCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  // Only set _taggingmk cookie when method is 'both' (fallback for PHP).
  // When 'js_only', cookie is set via early inline <head> script.
  // When 'php_only', no JS cookie setting at all.
  if (
    !window.varGtmTryTagging?.disable_cookie_keeper &&
    (!window.varGtmTryTagging?.cookie_keeper_method ||
      window.varGtmTryTagging.cookie_keeper_method === "both")
  ) {
    if (document.cookie.indexOf("_taggingmk=") === -1) {
      var uuid = generateUUID();
      setCookie("_taggingmk", uuid, 403);
    }
  }
}
