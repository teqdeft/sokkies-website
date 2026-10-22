// Production steps of ECMA-262, Edition 6, 22.1.2.1
if (!Array.from) {
    Array.from = (function () {
        var symbolIterator;
        try {
            symbolIterator = Symbol.iterator
                ? Symbol.iterator
                : 'Symbol(Symbol.iterator)';
        } catch (e) {
            symbolIterator = 'Symbol(Symbol.iterator)';
        }

        var toStr = Object.prototype.toString;
        var isCallable = function (fn) {
            return (
                typeof fn === 'function' ||
                toStr.call(fn) === '[object Function]'
            );
        };
        var toInteger = function (value) {
            var number = Number(value);
            if (isNaN(number)) return 0;
            if (number === 0 || !isFinite(number)) return number;
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
        };
        var maxSafeInteger = Math.pow(2, 53) - 1;
        var toLength = function (value) {
            var len = toInteger(value);
            return Math.min(Math.max(len, 0), maxSafeInteger);
        };

        var setGetItemHandler = function setGetItemHandler(isIterator, items) {
            var iterator = isIterator && items[symbolIterator]();
            return function getItem(k) {
                return isIterator ? iterator.next() : items[k];
            };
        };

        var getArray = function getArray(
            T,
            A,
            len,
            getItem,
            isIterator,
            mapFn
        ) {
            // 16. Let k be 0.
            var k = 0;

            // 17. Repeat, while k < len… or while iterator is done (also steps a - h)
            while (k < len || isIterator) {
                var item = getItem(k);
                var kValue = isIterator ? item.value : item;

                if (isIterator && item.done) {
                    return A;
                } else {
                    if (mapFn) {
                        A[k] =
                            typeof T === 'undefined'
                                ? mapFn(kValue, k)
                                : mapFn.call(T, kValue, k);
                    } else {
                        A[k] = kValue;
                    }
                }
                k += 1;
            }

            if (isIterator) {
                throw new TypeError(
                    'Array.from: provided arrayLike or iterator has length more then 2 ** 52 - 1'
                );
            } else {
                A.length = len;
            }

            return A;
        };

        // The length property of the from method is 1.
        return function from(arrayLikeOrIterator /*, mapFn, thisArg */) {
            // 1. Let C be the this value.
            var C = this;

            // 2. Let items be ToObject(arrayLikeOrIterator).
            var items = Object(arrayLikeOrIterator);
            var isIterator = isCallable(items[symbolIterator]);

            // 3. ReturnIfAbrupt(items).
            if (arrayLikeOrIterator == null && !isIterator) {
                throw new TypeError(
                    'Array.from requires an array-like object or iterator - not null or undefined'
                );
            }

            // 4. If mapfn is undefined, then let mapping be false.
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
            var T;
            if (typeof mapFn !== 'undefined') {
                // 5. else
                // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                if (!isCallable(mapFn)) {
                    throw new TypeError(
                        'Array.from: when provided, the second argument must be a function'
                    );
                }

                // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (arguments.length > 2) {
                    T = arguments[2];
                }
            }

            // 10. Let lenValue be Get(items, "length").
            // 11. Let len be ToLength(lenValue).
            var len = toLength(items.length);

            // 13. If IsConstructor(C) is true, then
            // 13. a. Let A be the result of calling the [[Construct]] internal method
            // of C with an argument list containing the single item len.
            // 14. a. Else, Let A be ArrayCreate(len).
            var A = isCallable(C) ? Object(new C(len)) : new Array(len);

            return getArray(
                T,
                A,
                len,
                setGetItemHandler(isIterator, items),
                isIterator,
                mapFn
            );
        };
    })();
}

if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
        var el = this;

        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

// REQUIRED FOR:
// draco_decoder.js (loaded async from DRACOLoader.js)
if (!Math.clz32) Math.clz32 = (function(log, LN2){
    return function(x) {
      // Let n be ToUint32(x).
      // Let p be the number of leading zero bits in
      // the 32-bit binary representation of n.
      // Return p.
      var asUint = x >>> 0;
      if (asUint === 0) {
        return 32;
      }
      return 31 - (log(asUint) / LN2 | 0) |0; // the "| 0" acts like math.floor
    };
})(Math.log, Math.LN2);

// REQUIRED FOR:
// draco_decoder.js (loaded async from DRACOLoader.js)
Math.fround = Math.fround || (function (array) {
    return function(x) {
      return array[0] = x, array[0];
    };
})(new Float32Array(1));

// REQUIRED FOR:
// draco_decoder.js (loaded async from DRACOLoader.js)
if (!Math.imul) Math.imul = function(opA, opB) {
    opB |= 0; // ensure that opB is an integer. opA will automatically be coerced.
    // floating points give us 53 bits of precision to work with plus 1 sign bit
    // automatically handled for our convienence:
    // 1. 0x003fffff /*opA & 0x000fffff*/ * 0x7fffffff /*opB*/ = 0x1fffff7fc00001
    //    0x1fffff7fc00001 < Number.MAX_SAFE_INTEGER /*0x1fffffffffffff*/
    var result = (opA & 0x003fffff) * opB;
    // 2. We can remove an integer coersion from the statement above because:
    //    0x1fffff7fc00001 + 0xffc00000 = 0x1fffffff800001
    //    0x1fffffff800001 < Number.MAX_SAFE_INTEGER /*0x1fffffffffffff*/
    if (opA & 0xffc00000 /*!== 0*/) result += (opA & 0xffc00000) * opB |0;
    return result |0;
};

window.MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

if (!Object.entries) {
    Object.entries = function( obj ){
        var ownProps = Object.keys( obj ),
            i = ownProps.length,
            resArray = new Array(i); // preallocate the Array
        while (i--)
            resArray[i] = [ownProps[i], obj[ownProps[i]]];

        return resArray;
    };
}

if (!Object.fromEntries) {
    Object.fromEntries = function(iterable){
        var result = {};
        var array = Array.from(iterable);
        array.forEach(function(value){
            result[value[0]] = value[1];
        });
        return result;
    }
}

// https://developer.mozilla.org/en-US/docs/Web/API/window/requestAnimationFrame
// https://developer.mozilla.org/en-US/docs/Web/API/Window/cancelAnimationFrame

(function() {
    window.requestAnimationFrame =
        window.requestAnimationFrame ||
        window.msRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.oRequestAnimationFrame;
    window.cancelAnimationFrame =
        window.cancelAnimationFrame ||
        window.msCancelAnimationFrame ||
        window.mozCancelAnimationFrame ||
        window.webkitCancelAnimationFrame ||
        window.oCancelAnimationFrame;

    if (!window.requestAnimationFrame) {
        var lastTime = 0;

        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(
                function() {
                    callback(currTime + timeToCall);
                },
                timeToCall
            );
            lastTime = currTime + timeToCall;
            return id;
        };
    }

    if (!window.cancelAnimationFrame) {
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
    }
}());

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (factory((global.WHATWGFetch = {})));
}(this, (function (exports) { 'use strict';

  /* eslint-disable no-prototype-builtins */
  var g =
    (typeof globalThis !== 'undefined' && globalThis) ||
    (typeof self !== 'undefined' && self) ||
    // eslint-disable-next-line no-undef
    (typeof global !== 'undefined' && global) ||
    {};

  var support = {
    searchParams: 'URLSearchParams' in g,
    iterable: 'Symbol' in g && 'iterator' in Symbol,
    blob:
      'FileReader' in g &&
      'Blob' in g &&
      (function() {
        try {
          new Blob();
          return true
        } catch (e) {
          return false
        }
      })(),
    formData: 'FormData' in g,
    arrayBuffer: 'ArrayBuffer' in g
  };

  function isDataView(obj) {
    return obj && DataView.prototype.isPrototypeOf(obj)
  }

  if (support.arrayBuffer) {
    var viewClasses = [
      '[object Int8Array]',
      '[object Uint8Array]',
      '[object Uint8ClampedArray]',
      '[object Int16Array]',
      '[object Uint16Array]',
      '[object Int32Array]',
      '[object Uint32Array]',
      '[object Float32Array]',
      '[object Float64Array]'
    ];

    var isArrayBufferView =
      ArrayBuffer.isView ||
      function(obj) {
        return obj && viewClasses.indexOf(Object.prototype.toString.call(obj)) > -1
      };
  }

  function normalizeName(name) {
    if (typeof name !== 'string') {
      name = String(name);
    }
    if (/[^a-z0-9\-#$%&'*+.^_`|~!]/i.test(name) || name === '') {
      throw new TypeError('Invalid character in header field name: "' + name + '"')
    }
    return name.toLowerCase()
  }

  function normalizeValue(value) {
    if (typeof value !== 'string') {
      value = String(value);
    }
    return value
  }

  // Build a destructive iterator for the value list
  function iteratorFor(items) {
    var iterator = {
      next: function() {
        var value = items.shift();
        return {done: value === undefined, value: value}
      }
    };

    if (support.iterable) {
      iterator[Symbol.iterator] = function() {
        return iterator
      };
    }

    return iterator
  }

  function Headers(headers) {
    this.map = {};

    if (headers instanceof Headers) {
      headers.forEach(function(value, name) {
        this.append(name, value);
      }, this);
    } else if (Array.isArray(headers)) {
      headers.forEach(function(header) {
        if (header.length != 2) {
          throw new TypeError('Headers constructor: expected name/value pair to be length 2, found' + header.length)
        }
        this.append(header[0], header[1]);
      }, this);
    } else if (headers) {
      Object.getOwnPropertyNames(headers).forEach(function(name) {
        this.append(name, headers[name]);
      }, this);
    }
  }

  Headers.prototype.append = function(name, value) {
    name = normalizeName(name);
    value = normalizeValue(value);
    var oldValue = this.map[name];
    this.map[name] = oldValue ? oldValue + ', ' + value : value;
  };

  Headers.prototype['delete'] = function(name) {
    delete this.map[normalizeName(name)];
  };

  Headers.prototype.get = function(name) {
    name = normalizeName(name);
    return this.has(name) ? this.map[name] : null
  };

  Headers.prototype.has = function(name) {
    return this.map.hasOwnProperty(normalizeName(name))
  };

  Headers.prototype.set = function(name, value) {
    this.map[normalizeName(name)] = normalizeValue(value);
  };

  Headers.prototype.forEach = function(callback, thisArg) {
    for (var name in this.map) {
      if (this.map.hasOwnProperty(name)) {
        callback.call(thisArg, this.map[name], name, this);
      }
    }
  };

  Headers.prototype.keys = function() {
    var items = [];
    this.forEach(function(value, name) {
      items.push(name);
    });
    return iteratorFor(items)
  };

  Headers.prototype.values = function() {
    var items = [];
    this.forEach(function(value) {
      items.push(value);
    });
    return iteratorFor(items)
  };

  Headers.prototype.entries = function() {
    var items = [];
    this.forEach(function(value, name) {
      items.push([name, value]);
    });
    return iteratorFor(items)
  };

  if (support.iterable) {
    Headers.prototype[Symbol.iterator] = Headers.prototype.entries;
  }

  function consumed(body) {
    if (body._noBody) return
    if (body.bodyUsed) {
      return Promise.reject(new TypeError('Already read'))
    }
    body.bodyUsed = true;
  }

  function fileReaderReady(reader) {
    return new Promise(function(resolve, reject) {
      reader.onload = function() {
        resolve(reader.result);
      };
      reader.onerror = function() {
        reject(reader.error);
      };
    })
  }

  function readBlobAsArrayBuffer(blob) {
    var reader = new FileReader();
    var promise = fileReaderReady(reader);
    reader.readAsArrayBuffer(blob);
    return promise
  }

  function readBlobAsText(blob) {
    var reader = new FileReader();
    var promise = fileReaderReady(reader);
    var match = /charset=([A-Za-z0-9_-]+)/.exec(blob.type);
    var encoding = match ? match[1] : 'utf-8';
    reader.readAsText(blob, encoding);
    return promise
  }

  function readArrayBufferAsText(buf) {
    var view = new Uint8Array(buf);
    var chars = new Array(view.length);

    for (var i = 0; i < view.length; i++) {
      chars[i] = String.fromCharCode(view[i]);
    }
    return chars.join('')
  }

  function bufferClone(buf) {
    if (buf.slice) {
      return buf.slice(0)
    } else {
      var view = new Uint8Array(buf.byteLength);
      view.set(new Uint8Array(buf));
      return view.buffer
    }
  }

  function Body() {
    this.bodyUsed = false;

    this._initBody = function(body) {
      /*
        fetch-mock wraps the Response object in an ES6 Proxy to
        provide useful test harness features such as flush. However, on
        ES5 browsers without fetch or Proxy support pollyfills must be used;
        the proxy-pollyfill is unable to proxy an attribute unless it exists
        on the object before the Proxy is created. This change ensures
        Response.bodyUsed exists on the instance, while maintaining the
        semantic of setting Request.bodyUsed in the constructor before
        _initBody is called.
      */
      // eslint-disable-next-line no-self-assign
      this.bodyUsed = this.bodyUsed;
      this._bodyInit = body;
      if (!body) {
        this._noBody = true;
        this._bodyText = '';
      } else if (typeof body === 'string') {
        this._bodyText = body;
      } else if (support.blob && Blob.prototype.isPrototypeOf(body)) {
        this._bodyBlob = body;
      } else if (support.formData && FormData.prototype.isPrototypeOf(body)) {
        this._bodyFormData = body;
      } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
        this._bodyText = body.toString();
      } else if (support.arrayBuffer && support.blob && isDataView(body)) {
        this._bodyArrayBuffer = bufferClone(body.buffer);
        // IE 10-11 can't handle a DataView body.
        this._bodyInit = new Blob([this._bodyArrayBuffer]);
      } else if (support.arrayBuffer && (ArrayBuffer.prototype.isPrototypeOf(body) || isArrayBufferView(body))) {
        this._bodyArrayBuffer = bufferClone(body);
      } else {
        this._bodyText = body = Object.prototype.toString.call(body);
      }

      if (!this.headers.get('content-type')) {
        if (typeof body === 'string') {
          this.headers.set('content-type', 'text/plain;charset=UTF-8');
        } else if (this._bodyBlob && this._bodyBlob.type) {
          this.headers.set('content-type', this._bodyBlob.type);
        } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
          this.headers.set('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
        }
      }
    };

    if (support.blob) {
      this.blob = function() {
        var rejected = consumed(this);
        if (rejected) {
          return rejected
        }

        if (this._bodyBlob) {
          return Promise.resolve(this._bodyBlob)
        } else if (this._bodyArrayBuffer) {
          return Promise.resolve(new Blob([this._bodyArrayBuffer]))
        } else if (this._bodyFormData) {
          throw new Error('could not read FormData body as blob')
        } else {
          return Promise.resolve(new Blob([this._bodyText]))
        }
      };
    }

    this.arrayBuffer = function() {
      if (this._bodyArrayBuffer) {
        var isConsumed = consumed(this);
        if (isConsumed) {
          return isConsumed
        } else if (ArrayBuffer.isView(this._bodyArrayBuffer)) {
          return Promise.resolve(
            this._bodyArrayBuffer.buffer.slice(
              this._bodyArrayBuffer.byteOffset,
              this._bodyArrayBuffer.byteOffset + this._bodyArrayBuffer.byteLength
            )
          )
        } else {
          return Promise.resolve(this._bodyArrayBuffer)
        }
      } else if (support.blob) {
        return this.blob().then(readBlobAsArrayBuffer)
      } else {
        throw new Error('could not read as ArrayBuffer')
      }
    };

    this.text = function() {
      var rejected = consumed(this);
      if (rejected) {
        return rejected
      }

      if (this._bodyBlob) {
        return readBlobAsText(this._bodyBlob)
      } else if (this._bodyArrayBuffer) {
        return Promise.resolve(readArrayBufferAsText(this._bodyArrayBuffer))
      } else if (this._bodyFormData) {
        throw new Error('could not read FormData body as text')
      } else {
        return Promise.resolve(this._bodyText)
      }
    };

    if (support.formData) {
      this.formData = function() {
        return this.text().then(decode)
      };
    }

    this.json = function() {
      return this.text().then(JSON.parse)
    };

    return this
  }

  // HTTP methods whose capitalization should be normalized
  var methods = ['CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'];

  function normalizeMethod(method) {
    var upcased = method.toUpperCase();
    return methods.indexOf(upcased) > -1 ? upcased : method
  }

  function Request(input, options) {
    if (!(this instanceof Request)) {
      throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.')
    }

    options = options || {};
    var body = options.body;

    if (input instanceof Request) {
      if (input.bodyUsed) {
        throw new TypeError('Already read')
      }
      this.url = input.url;
      this.credentials = input.credentials;
      if (!options.headers) {
        this.headers = new Headers(input.headers);
      }
      this.method = input.method;
      this.mode = input.mode;
      this.signal = input.signal;
      if (!body && input._bodyInit != null) {
        body = input._bodyInit;
        input.bodyUsed = true;
      }
    } else {
      this.url = String(input);
    }

    this.credentials = options.credentials || this.credentials || 'same-origin';
    if (options.headers || !this.headers) {
      this.headers = new Headers(options.headers);
    }
    this.method = normalizeMethod(options.method || this.method || 'GET');
    this.mode = options.mode || this.mode || null;
    this.signal = options.signal || this.signal || (function () {
      if ('AbortController' in g) {
        var ctrl = new AbortController();
        return ctrl.signal;
      }
    }());
    this.referrer = null;

    if ((this.method === 'GET' || this.method === 'HEAD') && body) {
      throw new TypeError('Body not allowed for GET or HEAD requests')
    }
    this._initBody(body);

    if (this.method === 'GET' || this.method === 'HEAD') {
      if (options.cache === 'no-store' || options.cache === 'no-cache') {
        // Search for a '_' parameter in the query string
        var reParamSearch = /([?&])_=[^&]*/;
        if (reParamSearch.test(this.url)) {
          // If it already exists then set the value with the current time
          this.url = this.url.replace(reParamSearch, '$1_=' + new Date().getTime());
        } else {
          // Otherwise add a new '_' parameter to the end with the current time
          var reQueryString = /\?/;
          this.url += (reQueryString.test(this.url) ? '&' : '?') + '_=' + new Date().getTime();
        }
      }
    }
  }

  Request.prototype.clone = function() {
    return new Request(this, {body: this._bodyInit})
  };

  function decode(body) {
    var form = new FormData();
    body
      .trim()
      .split('&')
      .forEach(function(bytes) {
        if (bytes) {
          var split = bytes.split('=');
          var name = split.shift().replace(/\+/g, ' ');
          var value = split.join('=').replace(/\+/g, ' ');
          form.append(decodeURIComponent(name), decodeURIComponent(value));
        }
      });
    return form
  }

  function parseHeaders(rawHeaders) {
    var headers = new Headers();
    // Replace instances of \r\n and \n followed by at least one space or horizontal tab with a space
    // https://tools.ietf.org/html/rfc7230#section-3.2
    var preProcessedHeaders = rawHeaders.replace(/\r?\n[\t ]+/g, ' ');
    // Avoiding split via regex to work around a common IE11 bug with the core-js 3.6.0 regex polyfill
    // https://github.com/github/fetch/issues/748
    // https://github.com/zloirock/core-js/issues/751
    preProcessedHeaders
      .split('\r')
      .map(function(header) {
        return header.indexOf('\n') === 0 ? header.substr(1, header.length) : header
      })
      .forEach(function(line) {
        var parts = line.split(':');
        var key = parts.shift().trim();
        if (key) {
          var value = parts.join(':').trim();
          try {
            headers.append(key, value);
          } catch (error) {
            console.warn('Response ' + error.message);
          }
        }
      });
    return headers
  }

  Body.call(Request.prototype);

  function Response(bodyInit, options) {
    if (!(this instanceof Response)) {
      throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.')
    }
    if (!options) {
      options = {};
    }

    this.type = 'default';
    this.status = options.status === undefined ? 200 : options.status;
    if (this.status < 200 || this.status > 599) {
      throw new RangeError("Failed to construct 'Response': The status provided (0) is outside the range [200, 599].")
    }
    this.ok = this.status >= 200 && this.status < 300;
    this.statusText = options.statusText === undefined ? '' : '' + options.statusText;
    this.headers = new Headers(options.headers);
    this.url = options.url || '';
    this._initBody(bodyInit);
  }

  Body.call(Response.prototype);

  Response.prototype.clone = function() {
    return new Response(this._bodyInit, {
      status: this.status,
      statusText: this.statusText,
      headers: new Headers(this.headers),
      url: this.url
    })
  };

  Response.error = function() {
    var response = new Response(null, {status: 200, statusText: ''});
    response.ok = false;
    response.status = 0;
    response.type = 'error';
    return response
  };

  var redirectStatuses = [301, 302, 303, 307, 308];

  Response.redirect = function(url, status) {
    if (redirectStatuses.indexOf(status) === -1) {
      throw new RangeError('Invalid status code')
    }

    return new Response(null, {status: status, headers: {location: url}})
  };

  exports.DOMException = g.DOMException;
  try {
    new exports.DOMException();
  } catch (err) {
    exports.DOMException = function(message, name) {
      this.message = message;
      this.name = name;
      var error = Error(message);
      this.stack = error.stack;
    };
    exports.DOMException.prototype = Object.create(Error.prototype);
    exports.DOMException.prototype.constructor = exports.DOMException;
  }

  function fetch(input, init) {
    return new Promise(function(resolve, reject) {
      var request = new Request(input, init);

      if (request.signal && request.signal.aborted) {
        return reject(new exports.DOMException('Aborted', 'AbortError'))
      }

      var xhr = new XMLHttpRequest();

      function abortXhr() {
        xhr.abort();
      }

      xhr.onload = function() {
        var options = {
          statusText: xhr.statusText,
          headers: parseHeaders(xhr.getAllResponseHeaders() || '')
        };
        // This check if specifically for when a user fetches a file locally from the file system
        // Only if the status is out of a normal range
        if (request.url.indexOf('file://') === 0 && (xhr.status < 200 || xhr.status > 599)) {
          options.status = 200;
        } else {
          options.status = xhr.status;
        }
        options.url = 'responseURL' in xhr ? xhr.responseURL : options.headers.get('X-Request-URL');
        var body = 'response' in xhr ? xhr.response : xhr.responseText;
        setTimeout(function() {
          resolve(new Response(body, options));
        }, 0);
      };

      xhr.onerror = function() {
        setTimeout(function() {
          reject(new TypeError('Network request failed'));
        }, 0);
      };

      xhr.ontimeout = function() {
        setTimeout(function() {
          reject(new TypeError('Network request timed out'));
        }, 0);
      };

      xhr.onabort = function() {
        setTimeout(function() {
          reject(new exports.DOMException('Aborted', 'AbortError'));
        }, 0);
      };

      function fixUrl(url) {
        try {
          return url === '' && g.location.href ? g.location.href : url
        } catch (e) {
          return url
        }
      }

      xhr.open(request.method, fixUrl(request.url), true);

      if (request.credentials === 'include') {
        xhr.withCredentials = true;
      } else if (request.credentials === 'omit') {
        xhr.withCredentials = false;
      }

      if ('responseType' in xhr) {
        if (support.blob) {
          xhr.responseType = 'blob';
        } else if (
          support.arrayBuffer
        ) {
          xhr.responseType = 'arraybuffer';
        }
      }

      if (init && typeof init.headers === 'object' && !(init.headers instanceof Headers || (g.Headers && init.headers instanceof g.Headers))) {
        var names = [];
        Object.getOwnPropertyNames(init.headers).forEach(function(name) {
          names.push(normalizeName(name));
          xhr.setRequestHeader(name, normalizeValue(init.headers[name]));
        });
        request.headers.forEach(function(value, name) {
          if (names.indexOf(name) === -1) {
            xhr.setRequestHeader(name, value);
          }
        });
      } else {
        request.headers.forEach(function(value, name) {
          xhr.setRequestHeader(name, value);
        });
      }

      if (request.signal) {
        request.signal.addEventListener('abort', abortXhr);

        xhr.onreadystatechange = function() {
          // DONE (success or failure)
          if (xhr.readyState === 4) {
            request.signal.removeEventListener('abort', abortXhr);
          }
        };
      }

      xhr.send(typeof request._bodyInit === 'undefined' ? null : request._bodyInit);
    })
  }

  fetch.polyfill = true;

  if (!g.fetch) {
    g.fetch = fetch;
    g.Headers = Headers;
    g.Request = Request;
    g.Response = Response;
  }

  exports.Headers = Headers;
  exports.Request = Request;
  exports.Response = Response;
  exports.fetch = fetch;

  Object.defineProperty(exports, '__esModule', { value: true });

})));

/*! Hammer.JS - v2.0.7 - 2016-04-22
 * http://hammerjs.github.io/
 *
 * Copyright (c) 2016 Jorik Tangelder;
 * Licensed under the MIT license */
!function(a,b,c,d){"use strict";function e(a,b,c){return setTimeout(j(a,c),b)}function f(a,b,c){return Array.isArray(a)?(g(a,c[b],c),!0):!1}function g(a,b,c){var e;if(a)if(a.forEach)a.forEach(b,c);else if(a.length!==d)for(e=0;e<a.length;)b.call(c,a[e],e,a),e++;else for(e in a)a.hasOwnProperty(e)&&b.call(c,a[e],e,a)}function h(b,c,d){var e="DEPRECATED METHOD: "+c+"\n"+d+" AT \n";return function(){var c=new Error("get-stack-trace"),d=c&&c.stack?c.stack.replace(/^[^\(]+?[\n$]/gm,"").replace(/^\s+at\s+/gm,"").replace(/^Object.<anonymous>\s*\(/gm,"{anonymous}()@"):"Unknown Stack Trace",f=a.console&&(a.console.warn||a.console.log);return f&&f.call(a.console,e,d),b.apply(this,arguments)}}function i(a,b,c){var d,e=b.prototype;d=a.prototype=Object.create(e),d.constructor=a,d._super=e,c&&la(d,c)}function j(a,b){return function(){return a.apply(b,arguments)}}function k(a,b){return typeof a==oa?a.apply(b?b[0]||d:d,b):a}function l(a,b){return a===d?b:a}function m(a,b,c){g(q(b),function(b){a.addEventListener(b,c,!1)})}function n(a,b,c){g(q(b),function(b){a.removeEventListener(b,c,!1)})}function o(a,b){for(;a;){if(a==b)return!0;a=a.parentNode}return!1}function p(a,b){return a.indexOf(b)>-1}function q(a){return a.trim().split(/\s+/g)}function r(a,b,c){if(a.indexOf&&!c)return a.indexOf(b);for(var d=0;d<a.length;){if(c&&a[d][c]==b||!c&&a[d]===b)return d;d++}return-1}function s(a){return Array.prototype.slice.call(a,0)}function t(a,b,c){for(var d=[],e=[],f=0;f<a.length;){var g=b?a[f][b]:a[f];r(e,g)<0&&d.push(a[f]),e[f]=g,f++}return c&&(d=b?d.sort(function(a,c){return a[b]>c[b]}):d.sort()),d}function u(a,b){for(var c,e,f=b[0].toUpperCase()+b.slice(1),g=0;g<ma.length;){if(c=ma[g],e=c?c+f:b,e in a)return e;g++}return d}function v(){return ua++}function w(b){var c=b.ownerDocument||b;return c.defaultView||c.parentWindow||a}function x(a,b){var c=this;this.manager=a,this.callback=b,this.element=a.element,this.target=a.options.inputTarget,this.domHandler=function(b){k(a.options.enable,[a])&&c.handler(b)},this.init()}function y(a){var b,c=a.options.inputClass;return new(b=c?c:xa?M:ya?P:wa?R:L)(a,z)}function z(a,b,c){var d=c.pointers.length,e=c.changedPointers.length,f=b&Ea&&d-e===0,g=b&(Ga|Ha)&&d-e===0;c.isFirst=!!f,c.isFinal=!!g,f&&(a.session={}),c.eventType=b,A(a,c),a.emit("hammer.input",c),a.recognize(c),a.session.prevInput=c}function A(a,b){var c=a.session,d=b.pointers,e=d.length;c.firstInput||(c.firstInput=D(b)),e>1&&!c.firstMultiple?c.firstMultiple=D(b):1===e&&(c.firstMultiple=!1);var f=c.firstInput,g=c.firstMultiple,h=g?g.center:f.center,i=b.center=E(d);b.timeStamp=ra(),b.deltaTime=b.timeStamp-f.timeStamp,b.angle=I(h,i),b.distance=H(h,i),B(c,b),b.offsetDirection=G(b.deltaX,b.deltaY);var j=F(b.deltaTime,b.deltaX,b.deltaY);b.overallVelocityX=j.x,b.overallVelocityY=j.y,b.overallVelocity=qa(j.x)>qa(j.y)?j.x:j.y,b.scale=g?K(g.pointers,d):1,b.rotation=g?J(g.pointers,d):0,b.maxPointers=c.prevInput?b.pointers.length>c.prevInput.maxPointers?b.pointers.length:c.prevInput.maxPointers:b.pointers.length,C(c,b);var k=a.element;o(b.srcEvent.target,k)&&(k=b.srcEvent.target),b.target=k}function B(a,b){var c=b.center,d=a.offsetDelta||{},e=a.prevDelta||{},f=a.prevInput||{};b.eventType!==Ea&&f.eventType!==Ga||(e=a.prevDelta={x:f.deltaX||0,y:f.deltaY||0},d=a.offsetDelta={x:c.x,y:c.y}),b.deltaX=e.x+(c.x-d.x),b.deltaY=e.y+(c.y-d.y)}function C(a,b){var c,e,f,g,h=a.lastInterval||b,i=b.timeStamp-h.timeStamp;if(b.eventType!=Ha&&(i>Da||h.velocity===d)){var j=b.deltaX-h.deltaX,k=b.deltaY-h.deltaY,l=F(i,j,k);e=l.x,f=l.y,c=qa(l.x)>qa(l.y)?l.x:l.y,g=G(j,k),a.lastInterval=b}else c=h.velocity,e=h.velocityX,f=h.velocityY,g=h.direction;b.velocity=c,b.velocityX=e,b.velocityY=f,b.direction=g}function D(a){for(var b=[],c=0;c<a.pointers.length;)b[c]={clientX:pa(a.pointers[c].clientX),clientY:pa(a.pointers[c].clientY)},c++;return{timeStamp:ra(),pointers:b,center:E(b),deltaX:a.deltaX,deltaY:a.deltaY}}function E(a){var b=a.length;if(1===b)return{x:pa(a[0].clientX),y:pa(a[0].clientY)};for(var c=0,d=0,e=0;b>e;)c+=a[e].clientX,d+=a[e].clientY,e++;return{x:pa(c/b),y:pa(d/b)}}function F(a,b,c){return{x:b/a||0,y:c/a||0}}function G(a,b){return a===b?Ia:qa(a)>=qa(b)?0>a?Ja:Ka:0>b?La:Ma}function H(a,b,c){c||(c=Qa);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return Math.sqrt(d*d+e*e)}function I(a,b,c){c||(c=Qa);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return 180*Math.atan2(e,d)/Math.PI}function J(a,b){return I(b[1],b[0],Ra)+I(a[1],a[0],Ra)}function K(a,b){return H(b[0],b[1],Ra)/H(a[0],a[1],Ra)}function L(){this.evEl=Ta,this.evWin=Ua,this.pressed=!1,x.apply(this,arguments)}function M(){this.evEl=Xa,this.evWin=Ya,x.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function N(){this.evTarget=$a,this.evWin=_a,this.started=!1,x.apply(this,arguments)}function O(a,b){var c=s(a.touches),d=s(a.changedTouches);return b&(Ga|Ha)&&(c=t(c.concat(d),"identifier",!0)),[c,d]}function P(){this.evTarget=bb,this.targetIds={},x.apply(this,arguments)}function Q(a,b){var c=s(a.touches),d=this.targetIds;if(b&(Ea|Fa)&&1===c.length)return d[c[0].identifier]=!0,[c,c];var e,f,g=s(a.changedTouches),h=[],i=this.target;if(f=c.filter(function(a){return o(a.target,i)}),b===Ea)for(e=0;e<f.length;)d[f[e].identifier]=!0,e++;for(e=0;e<g.length;)d[g[e].identifier]&&h.push(g[e]),b&(Ga|Ha)&&delete d[g[e].identifier],e++;return h.length?[t(f.concat(h),"identifier",!0),h]:void 0}function R(){x.apply(this,arguments);var a=j(this.handler,this);this.touch=new P(this.manager,a),this.mouse=new L(this.manager,a),this.primaryTouch=null,this.lastTouches=[]}function S(a,b){a&Ea?(this.primaryTouch=b.changedPointers[0].identifier,T.call(this,b)):a&(Ga|Ha)&&T.call(this,b)}function T(a){var b=a.changedPointers[0];if(b.identifier===this.primaryTouch){var c={x:b.clientX,y:b.clientY};this.lastTouches.push(c);var d=this.lastTouches,e=function(){var a=d.indexOf(c);a>-1&&d.splice(a,1)};setTimeout(e,cb)}}function U(a){for(var b=a.srcEvent.clientX,c=a.srcEvent.clientY,d=0;d<this.lastTouches.length;d++){var e=this.lastTouches[d],f=Math.abs(b-e.x),g=Math.abs(c-e.y);if(db>=f&&db>=g)return!0}return!1}function V(a,b){this.manager=a,this.set(b)}function W(a){if(p(a,jb))return jb;var b=p(a,kb),c=p(a,lb);return b&&c?jb:b||c?b?kb:lb:p(a,ib)?ib:hb}function X(){if(!fb)return!1;var b={},c=a.CSS&&a.CSS.supports;return["auto","manipulation","pan-y","pan-x","pan-x pan-y","none"].forEach(function(d){b[d]=c?a.CSS.supports("touch-action",d):!0}),b}function Y(a){this.options=la({},this.defaults,a||{}),this.id=v(),this.manager=null,this.options.enable=l(this.options.enable,!0),this.state=nb,this.simultaneous={},this.requireFail=[]}function Z(a){return a&sb?"cancel":a&qb?"end":a&pb?"move":a&ob?"start":""}function $(a){return a==Ma?"down":a==La?"up":a==Ja?"left":a==Ka?"right":""}function _(a,b){var c=b.manager;return c?c.get(a):a}function aa(){Y.apply(this,arguments)}function ba(){aa.apply(this,arguments),this.pX=null,this.pY=null}function ca(){aa.apply(this,arguments)}function da(){Y.apply(this,arguments),this._timer=null,this._input=null}function ea(){aa.apply(this,arguments)}function fa(){aa.apply(this,arguments)}function ga(){Y.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function ha(a,b){return b=b||{},b.recognizers=l(b.recognizers,ha.defaults.preset),new ia(a,b)}function ia(a,b){this.options=la({},ha.defaults,b||{}),this.options.inputTarget=this.options.inputTarget||a,this.handlers={},this.session={},this.recognizers=[],this.oldCssProps={},this.element=a,this.input=y(this),this.touchAction=new V(this,this.options.touchAction),ja(this,!0),g(this.options.recognizers,function(a){var b=this.add(new a[0](a[1]));a[2]&&b.recognizeWith(a[2]),a[3]&&b.requireFailure(a[3])},this)}function ja(a,b){var c=a.element;if(c.style){var d;g(a.options.cssProps,function(e,f){d=u(c.style,f),b?(a.oldCssProps[d]=c.style[d],c.style[d]=e):c.style[d]=a.oldCssProps[d]||""}),b||(a.oldCssProps={})}}function ka(a,c){var d=b.createEvent("Event");d.initEvent(a,!0,!0),d.gesture=c,c.target.dispatchEvent(d)}var la,ma=["","webkit","Moz","MS","ms","o"],na=b.createElement("div"),oa="function",pa=Math.round,qa=Math.abs,ra=Date.now;la="function"!=typeof Object.assign?function(a){if(a===d||null===a)throw new TypeError("Cannot convert undefined or null to object");for(var b=Object(a),c=1;c<arguments.length;c++){var e=arguments[c];if(e!==d&&null!==e)for(var f in e)e.hasOwnProperty(f)&&(b[f]=e[f])}return b}:Object.assign;var sa=h(function(a,b,c){for(var e=Object.keys(b),f=0;f<e.length;)(!c||c&&a[e[f]]===d)&&(a[e[f]]=b[e[f]]),f++;return a},"extend","Use `assign`."),ta=h(function(a,b){return sa(a,b,!0)},"merge","Use `assign`."),ua=1,va=/mobile|tablet|ip(ad|hone|od)|android/i,wa="ontouchstart"in a,xa=u(a,"PointerEvent")!==d,ya=wa&&va.test(navigator.userAgent),za="touch",Aa="pen",Ba="mouse",Ca="kinect",Da=25,Ea=1,Fa=2,Ga=4,Ha=8,Ia=1,Ja=2,Ka=4,La=8,Ma=16,Na=Ja|Ka,Oa=La|Ma,Pa=Na|Oa,Qa=["x","y"],Ra=["clientX","clientY"];x.prototype={handler:function(){},init:function(){this.evEl&&m(this.element,this.evEl,this.domHandler),this.evTarget&&m(this.target,this.evTarget,this.domHandler),this.evWin&&m(w(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&n(this.element,this.evEl,this.domHandler),this.evTarget&&n(this.target,this.evTarget,this.domHandler),this.evWin&&n(w(this.element),this.evWin,this.domHandler)}};var Sa={mousedown:Ea,mousemove:Fa,mouseup:Ga},Ta="mousedown",Ua="mousemove mouseup";i(L,x,{handler:function(a){var b=Sa[a.type];b&Ea&&0===a.button&&(this.pressed=!0),b&Fa&&1!==a.which&&(b=Ga),this.pressed&&(b&Ga&&(this.pressed=!1),this.callback(this.manager,b,{pointers:[a],changedPointers:[a],pointerType:Ba,srcEvent:a}))}});var Va={pointerdown:Ea,pointermove:Fa,pointerup:Ga,pointercancel:Ha,pointerout:Ha},Wa={2:za,3:Aa,4:Ba,5:Ca},Xa="pointerdown",Ya="pointermove pointerup pointercancel";a.MSPointerEvent&&!a.PointerEvent&&(Xa="MSPointerDown",Ya="MSPointerMove MSPointerUp MSPointerCancel"),i(M,x,{handler:function(a){var b=this.store,c=!1,d=a.type.toLowerCase().replace("ms",""),e=Va[d],f=Wa[a.pointerType]||a.pointerType,g=f==za,h=r(b,a.pointerId,"pointerId");e&Ea&&(0===a.button||g)?0>h&&(b.push(a),h=b.length-1):e&(Ga|Ha)&&(c=!0),0>h||(b[h]=a,this.callback(this.manager,e,{pointers:b,changedPointers:[a],pointerType:f,srcEvent:a}),c&&b.splice(h,1))}});var Za={touchstart:Ea,touchmove:Fa,touchend:Ga,touchcancel:Ha},$a="touchstart",_a="touchstart touchmove touchend touchcancel";i(N,x,{handler:function(a){var b=Za[a.type];if(b===Ea&&(this.started=!0),this.started){var c=O.call(this,a,b);b&(Ga|Ha)&&c[0].length-c[1].length===0&&(this.started=!1),this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:za,srcEvent:a})}}});var ab={touchstart:Ea,touchmove:Fa,touchend:Ga,touchcancel:Ha},bb="touchstart touchmove touchend touchcancel";i(P,x,{handler:function(a){var b=ab[a.type],c=Q.call(this,a,b);c&&this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:za,srcEvent:a})}});var cb=2500,db=25;i(R,x,{handler:function(a,b,c){var d=c.pointerType==za,e=c.pointerType==Ba;if(!(e&&c.sourceCapabilities&&c.sourceCapabilities.firesTouchEvents)){if(d)S.call(this,b,c);else if(e&&U.call(this,c))return;this.callback(a,b,c)}},destroy:function(){this.touch.destroy(),this.mouse.destroy()}});var eb=u(na.style,"touchAction"),fb=eb!==d,gb="compute",hb="auto",ib="manipulation",jb="none",kb="pan-x",lb="pan-y",mb=X();V.prototype={set:function(a){a==gb&&(a=this.compute()),fb&&this.manager.element.style&&mb[a]&&(this.manager.element.style[eb]=a),this.actions=a.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var a=[];return g(this.manager.recognizers,function(b){k(b.options.enable,[b])&&(a=a.concat(b.getTouchAction()))}),W(a.join(" "))},preventDefaults:function(a){var b=a.srcEvent,c=a.offsetDirection;if(this.manager.session.prevented)return void b.preventDefault();var d=this.actions,e=p(d,jb)&&!mb[jb],f=p(d,lb)&&!mb[lb],g=p(d,kb)&&!mb[kb];if(e){var h=1===a.pointers.length,i=a.distance<2,j=a.deltaTime<250;if(h&&i&&j)return}return g&&f?void 0:e||f&&c&Na||g&&c&Oa?this.preventSrc(b):void 0},preventSrc:function(a){this.manager.session.prevented=!0,a.preventDefault()}};var nb=1,ob=2,pb=4,qb=8,rb=qb,sb=16,tb=32;Y.prototype={defaults:{},set:function(a){return la(this.options,a),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(a){if(f(a,"recognizeWith",this))return this;var b=this.simultaneous;return a=_(a,this),b[a.id]||(b[a.id]=a,a.recognizeWith(this)),this},dropRecognizeWith:function(a){return f(a,"dropRecognizeWith",this)?this:(a=_(a,this),delete this.simultaneous[a.id],this)},requireFailure:function(a){if(f(a,"requireFailure",this))return this;var b=this.requireFail;return a=_(a,this),-1===r(b,a)&&(b.push(a),a.requireFailure(this)),this},dropRequireFailure:function(a){if(f(a,"dropRequireFailure",this))return this;a=_(a,this);var b=r(this.requireFail,a);return b>-1&&this.requireFail.splice(b,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(a){return!!this.simultaneous[a.id]},emit:function(a){function b(b){c.manager.emit(b,a)}var c=this,d=this.state;qb>d&&b(c.options.event+Z(d)),b(c.options.event),a.additionalEvent&&b(a.additionalEvent),d>=qb&&b(c.options.event+Z(d))},tryEmit:function(a){return this.canEmit()?this.emit(a):void(this.state=tb)},canEmit:function(){for(var a=0;a<this.requireFail.length;){if(!(this.requireFail[a].state&(tb|nb)))return!1;a++}return!0},recognize:function(a){var b=la({},a);return k(this.options.enable,[this,b])?(this.state&(rb|sb|tb)&&(this.state=nb),this.state=this.process(b),void(this.state&(ob|pb|qb|sb)&&this.tryEmit(b))):(this.reset(),void(this.state=tb))},process:function(a){},getTouchAction:function(){},reset:function(){}},i(aa,Y,{defaults:{pointers:1},attrTest:function(a){var b=this.options.pointers;return 0===b||a.pointers.length===b},process:function(a){var b=this.state,c=a.eventType,d=b&(ob|pb),e=this.attrTest(a);return d&&(c&Ha||!e)?b|sb:d||e?c&Ga?b|qb:b&ob?b|pb:ob:tb}}),i(ba,aa,{defaults:{event:"pan",threshold:10,pointers:1,direction:Pa},getTouchAction:function(){var a=this.options.direction,b=[];return a&Na&&b.push(lb),a&Oa&&b.push(kb),b},directionTest:function(a){var b=this.options,c=!0,d=a.distance,e=a.direction,f=a.deltaX,g=a.deltaY;return e&b.direction||(b.direction&Na?(e=0===f?Ia:0>f?Ja:Ka,c=f!=this.pX,d=Math.abs(a.deltaX)):(e=0===g?Ia:0>g?La:Ma,c=g!=this.pY,d=Math.abs(a.deltaY))),a.direction=e,c&&d>b.threshold&&e&b.direction},attrTest:function(a){return aa.prototype.attrTest.call(this,a)&&(this.state&ob||!(this.state&ob)&&this.directionTest(a))},emit:function(a){this.pX=a.deltaX,this.pY=a.deltaY;var b=$(a.direction);b&&(a.additionalEvent=this.options.event+b),this._super.emit.call(this,a)}}),i(ca,aa,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[jb]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.scale-1)>this.options.threshold||this.state&ob)},emit:function(a){if(1!==a.scale){var b=a.scale<1?"in":"out";a.additionalEvent=this.options.event+b}this._super.emit.call(this,a)}}),i(da,Y,{defaults:{event:"press",pointers:1,time:251,threshold:9},getTouchAction:function(){return[hb]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime>b.time;if(this._input=a,!d||!c||a.eventType&(Ga|Ha)&&!f)this.reset();else if(a.eventType&Ea)this.reset(),this._timer=e(function(){this.state=rb,this.tryEmit()},b.time,this);else if(a.eventType&Ga)return rb;return tb},reset:function(){clearTimeout(this._timer)},emit:function(a){this.state===rb&&(a&&a.eventType&Ga?this.manager.emit(this.options.event+"up",a):(this._input.timeStamp=ra(),this.manager.emit(this.options.event,this._input)))}}),i(ea,aa,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[jb]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.rotation)>this.options.threshold||this.state&ob)}}),i(fa,aa,{defaults:{event:"swipe",threshold:10,velocity:.3,direction:Na|Oa,pointers:1},getTouchAction:function(){return ba.prototype.getTouchAction.call(this)},attrTest:function(a){var b,c=this.options.direction;return c&(Na|Oa)?b=a.overallVelocity:c&Na?b=a.overallVelocityX:c&Oa&&(b=a.overallVelocityY),this._super.attrTest.call(this,a)&&c&a.offsetDirection&&a.distance>this.options.threshold&&a.maxPointers==this.options.pointers&&qa(b)>this.options.velocity&&a.eventType&Ga},emit:function(a){var b=$(a.offsetDirection);b&&this.manager.emit(this.options.event+b,a),this.manager.emit(this.options.event,a)}}),i(ga,Y,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:9,posThreshold:10},getTouchAction:function(){return[ib]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime<b.time;if(this.reset(),a.eventType&Ea&&0===this.count)return this.failTimeout();if(d&&f&&c){if(a.eventType!=Ga)return this.failTimeout();var g=this.pTime?a.timeStamp-this.pTime<b.interval:!0,h=!this.pCenter||H(this.pCenter,a.center)<b.posThreshold;this.pTime=a.timeStamp,this.pCenter=a.center,h&&g?this.count+=1:this.count=1,this._input=a;var i=this.count%b.taps;if(0===i)return this.hasRequireFailures()?(this._timer=e(function(){this.state=rb,this.tryEmit()},b.interval,this),ob):rb}return tb},failTimeout:function(){return this._timer=e(function(){this.state=tb},this.options.interval,this),tb},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==rb&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),ha.VERSION="2.0.7",ha.defaults={domEvents:!1,touchAction:gb,enable:!0,inputTarget:null,inputClass:null,preset:[[ea,{enable:!1}],[ca,{enable:!1},["rotate"]],[fa,{direction:Na}],[ba,{direction:Na},["swipe"]],[ga],[ga,{event:"doubletap",taps:2},["tap"]],[da]],cssProps:{userSelect:"none",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}};var ub=1,vb=2;ia.prototype={set:function(a){return la(this.options,a),a.touchAction&&this.touchAction.update(),a.inputTarget&&(this.input.destroy(),this.input.target=a.inputTarget,this.input.init()),this},stop:function(a){this.session.stopped=a?vb:ub},recognize:function(a){var b=this.session;if(!b.stopped){this.touchAction.preventDefaults(a);var c,d=this.recognizers,e=b.curRecognizer;(!e||e&&e.state&rb)&&(e=b.curRecognizer=null);for(var f=0;f<d.length;)c=d[f],b.stopped===vb||e&&c!=e&&!c.canRecognizeWith(e)?c.reset():c.recognize(a),!e&&c.state&(ob|pb|qb)&&(e=b.curRecognizer=c),f++}},get:function(a){if(a instanceof Y)return a;for(var b=this.recognizers,c=0;c<b.length;c++)if(b[c].options.event==a)return b[c];return null},add:function(a){if(f(a,"add",this))return this;var b=this.get(a.options.event);return b&&this.remove(b),this.recognizers.push(a),a.manager=this,this.touchAction.update(),a},remove:function(a){if(f(a,"remove",this))return this;if(a=this.get(a)){var b=this.recognizers,c=r(b,a);-1!==c&&(b.splice(c,1),this.touchAction.update())}return this},on:function(a,b){if(a!==d&&b!==d){var c=this.handlers;return g(q(a),function(a){c[a]=c[a]||[],c[a].push(b)}),this}},off:function(a,b){if(a!==d){var c=this.handlers;return g(q(a),function(a){b?c[a]&&c[a].splice(r(c[a],b),1):delete c[a]}),this}},emit:function(a,b){this.options.domEvents&&ka(a,b);var c=this.handlers[a]&&this.handlers[a].slice();if(c&&c.length){b.type=a,b.preventDefault=function(){b.srcEvent.preventDefault()};for(var d=0;d<c.length;)c[d](b),d++}},destroy:function(){this.element&&ja(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},la(ha,{INPUT_START:Ea,INPUT_MOVE:Fa,INPUT_END:Ga,INPUT_CANCEL:Ha,STATE_POSSIBLE:nb,STATE_BEGAN:ob,STATE_CHANGED:pb,STATE_ENDED:qb,STATE_RECOGNIZED:rb,STATE_CANCELLED:sb,STATE_FAILED:tb,DIRECTION_NONE:Ia,DIRECTION_LEFT:Ja,DIRECTION_RIGHT:Ka,DIRECTION_UP:La,DIRECTION_DOWN:Ma,DIRECTION_HORIZONTAL:Na,DIRECTION_VERTICAL:Oa,DIRECTION_ALL:Pa,Manager:ia,Input:x,TouchAction:V,TouchInput:P,MouseInput:L,PointerEventInput:M,TouchMouseInput:R,SingleTouchInput:N,Recognizer:Y,AttrRecognizer:aa,Tap:ga,Pan:ba,Swipe:fa,Pinch:ca,Rotate:ea,Press:da,on:m,off:n,each:g,merge:ta,extend:sa,assign:la,inherit:i,bindFn:j,prefixed:u});var wb="undefined"!=typeof a?a:"undefined"!=typeof self?self:{};wb.Hammer=ha,"function"==typeof define&&define.amd?define(function(){return ha}):"undefined"!=typeof module&&module.exports?module.exports=ha:a[c]=ha}(window,document,"Hammer");
//# sourceMappingURL=hammer.min.js.map
!function(){"use strict";var t={34:function(t,n,r){var e=r(4901);t.exports=function(t){return"object"==typeof t?null!==t:e(t)}},235:function(t,n,r){var e=r(9213).forEach,o=r(4598)("forEach");t.exports=o?[].forEach:function(t){return e(this,t,arguments.length>1?arguments[1]:void 0)}},283:function(t,n,r){var e=r(9504),o=r(9039),i=r(4901),u=r(9297),c=r(3724),a=r(350).CONFIGURABLE,f=r(3706),s=r(1181),l=s.enforce,p=s.get,v=String,y=Object.defineProperty,b=e("".slice),h=e("".replace),g=e([].join),m=c&&!o(function(){return 8!==y(function(){},"length",{value:8}).length}),d=String(String).split("String"),x=t.exports=function(t,n,r){"Symbol("===b(v(n),0,7)&&(n="["+h(v(n),/^Symbol\(([^)]*)\).*$/,"$1")+"]"),r&&r.getter&&(n="get "+n),r&&r.setter&&(n="set "+n),(!u(t,"name")||a&&t.name!==n)&&(c?y(t,"name",{value:n,configurable:!0}):t.name=n),m&&r&&u(r,"arity")&&t.length!==r.arity&&y(t,"length",{value:r.arity});try{r&&u(r,"constructor")&&r.constructor?c&&y(t,"prototype",{writable:!1}):t.prototype&&(t.prototype=void 0)}catch(t){}var e=l(t);return u(e,"source")||(e.source=g(d,"string"==typeof n?n:"")),t};Function.prototype.toString=x(function(){return i(this)&&p(this).source||f(this)},"toString")},350:function(t,n,r){var e=r(3724),o=r(9297),i=Function.prototype,u=e&&Object.getOwnPropertyDescriptor,c=o(i,"name"),a=c&&"something"===function(){}.name,f=c&&(!e||e&&u(i,"name").configurable);t.exports={EXISTS:c,PROPER:a,CONFIGURABLE:f}},421:function(t){t.exports={}},616:function(t,n,r){var e=r(9039);t.exports=!e(function(){var t=function(){}.bind();return"function"!=typeof t||t.hasOwnProperty("prototype")})},741:function(t){var n=Math.ceil,r=Math.floor;t.exports=Math.trunc||function(t){var e=+t;return(e>0?r:n)(e)}},757:function(t,n,r){var e=r(7751),o=r(4901),i=r(1625),u=r(7040),c=Object;t.exports=u?function(t){return"symbol"==typeof t}:function(t){var n=e("Symbol");return o(n)&&i(n.prototype,c(t))}},1181:function(t,n,r){var e,o,i,u=r(8622),c=r(4576),a=r(34),f=r(6699),s=r(9297),l=r(7629),p=r(6119),v=r(421),y="Object already initialized",b=c.TypeError,h=c.WeakMap;if(u||l.state){var g=l.state||(l.state=new h);g.get=g.get,g.has=g.has,g.set=g.set,e=function(t,n){if(g.has(t))throw new b(y);return n.facade=t,g.set(t,n),n},o=function(t){return g.get(t)||{}},i=function(t){return g.has(t)}}else{var m=p("state");v[m]=!0,e=function(t,n){if(s(t,m))throw new b(y);return n.facade=t,f(t,m,n),n},o=function(t){return s(t,m)?t[m]:{}},i=function(t){return s(t,m)}}t.exports={set:e,get:o,has:i,enforce:function(t){return i(t)?o(t):e(t,{})},getterFor:function(t){return function(n){var r;if(!a(n)||(r=o(n)).type!==t)throw new b("Incompatible receiver, "+t+" required");return r}}}},1291:function(t,n,r){var e=r(741);t.exports=function(t){var n=+t;return n!=n||0===n?0:e(n)}},1469:function(t,n,r){var e=r(7433);t.exports=function(t,n){return new(e(t))(0===n?0:n)}},1625:function(t,n,r){var e=r(9504);t.exports=e({}.isPrototypeOf)},2140:function(t,n,r){var e={};e[r(8227)("toStringTag")]="z",t.exports="[object z]"===String(e)},2195:function(t,n,r){var e=r(9504),o=e({}.toString),i=e("".slice);t.exports=function(t){return i(o(t),8,-1)}},2777:function(t,n,r){var e=r(9565),o=r(34),i=r(757),u=r(5966),c=r(4270),a=r(8227),f=TypeError,s=a("toPrimitive");t.exports=function(t,n){if(!o(t)||i(t))return t;var r,a=u(t,s);if(a){if(void 0===n&&(n="default"),r=e(a,t,n),!o(r)||i(r))return r;throw new f("Can't convert object to primitive value")}return void 0===n&&(n="number"),c(t,n)}},2839:function(t,n,r){var e=r(4576).navigator,o=e&&e.userAgent;t.exports=o?String(o):""},3179:function(t,n,r){var e=r(2140),o=r(6955);t.exports=e?{}.toString:function(){return"[object "+o(this)+"]"}},3392:function(t,n,r){var e=r(9504),o=0,i=Math.random(),u=e(1.1.toString);t.exports=function(t){return"Symbol("+(void 0===t?"":t)+")_"+u(++o+i,36)}},3500:function(t,n,r){var e=r(4576),o=r(7400),i=r(9296),u=r(235),c=r(6699),a=function(t){if(t&&t.forEach!==u)try{c(t,"forEach",u)}catch(n){t.forEach=u}};for(var f in o)o[f]&&a(e[f]&&e[f].prototype);a(i)},3517:function(t,n,r){var e=r(9504),o=r(9039),i=r(4901),u=r(6955),c=r(7751),a=r(3706),f=function(){},s=c("Reflect","construct"),l=/^\s*(?:class|function)\b/,p=e(l.exec),v=!l.test(f),y=function(t){if(!i(t))return!1;try{return s(f,[],t),!0}catch(t){return!1}},b=function(t){if(!i(t))return!1;switch(u(t)){case"AsyncFunction":case"GeneratorFunction":case"AsyncGeneratorFunction":return!1}try{return v||!!p(l,a(t))}catch(t){return!0}};b.sham=!0,t.exports=!s||o(function(){var t;return y(y.call)||!y(Object)||!y(function(){t=!0})||t})?b:y},3706:function(t,n,r){var e=r(9504),o=r(4901),i=r(7629),u=e(Function.toString);o(i.inspectSource)||(i.inspectSource=function(t){return u(t)}),t.exports=i.inspectSource},3724:function(t,n,r){var e=r(9039);t.exports=!e(function(){return 7!==Object.defineProperty({},1,{get:function(){return 7}})[1]})},4055:function(t,n,r){var e=r(4576),o=r(34),i=e.document,u=o(i)&&o(i.createElement);t.exports=function(t){return u?i.createElement(t):{}}},4117:function(t){t.exports=function(t){return null==t}},4270:function(t,n,r){var e=r(9565),o=r(4901),i=r(34),u=TypeError;t.exports=function(t,n){var r,c;if("string"===n&&o(r=t.toString)&&!i(c=e(r,t)))return c;if(o(r=t.valueOf)&&!i(c=e(r,t)))return c;if("string"!==n&&o(r=t.toString)&&!i(c=e(r,t)))return c;throw new u("Can't convert object to primitive value")}},4376:function(t,n,r){var e=r(2195);t.exports=Array.isArray||function(t){return"Array"===e(t)}},4495:function(t,n,r){var e=r(9519),o=r(9039),i=r(4576).String;t.exports=!!Object.getOwnPropertySymbols&&!o(function(){var t=Symbol("symbol detection");return!i(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&e&&e<41})},4576:function(t,n,r){var e=function(t){return t&&t.Math===Math&&t};t.exports=e("object"==typeof globalThis&&globalThis)||e("object"==typeof window&&window)||e("object"==typeof self&&self)||e("object"==typeof r.g&&r.g)||e("object"==typeof this&&this)||function(){return this}()||Function("return this")()},4598:function(t,n,r){var e=r(9039);t.exports=function(t,n){var r=[][t];return!!r&&e(function(){r.call(null,n||function(){return 1},1)})}},4901:function(t){var n="object"==typeof document&&document.all;t.exports=void 0===n&&void 0!==n?function(t){return"function"==typeof t||t===n}:function(t){return"function"==typeof t}},4913:function(t,n,r){var e=r(3724),o=r(5917),i=r(8686),u=r(8551),c=r(6969),a=TypeError,f=Object.defineProperty,s=Object.getOwnPropertyDescriptor,l="enumerable",p="configurable",v="writable";n.f=e?i?function(t,n,r){if(u(t),n=c(n),u(r),"function"==typeof t&&"prototype"===n&&"value"in r&&v in r&&!r[v]){var e=s(t,n);e&&e[v]&&(t[n]=r.value,r={configurable:p in r?r[p]:e[p],enumerable:l in r?r[l]:e[l],writable:!1})}return f(t,n,r)}:f:function(t,n,r){if(u(t),n=c(n),u(r),o)try{return f(t,n,r)}catch(t){}if("get"in r||"set"in r)throw new a("Accessors not supported");return"value"in r&&(t[n]=r.value),t}},5745:function(t,n,r){var e=r(7629);t.exports=function(t,n){return e[t]||(e[t]=n||{})}},5917:function(t,n,r){var e=r(3724),o=r(9039),i=r(4055);t.exports=!e&&!o(function(){return 7!==Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a})},5966:function(t,n,r){var e=r(9306),o=r(4117);t.exports=function(t,n){var r=t[n];return o(r)?void 0:e(r)}},6080:function(t,n,r){var e=r(7476),o=r(9306),i=r(616),u=e(e.bind);t.exports=function(t,n){return o(t),void 0===n?t:i?u(t,n):function(){return t.apply(n,arguments)}}},6099:function(t,n,r){var e=r(2140),o=r(6840),i=r(3179);e||o(Object.prototype,"toString",i,{unsafe:!0})},6119:function(t,n,r){var e=r(5745),o=r(3392),i=e("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},6198:function(t,n,r){var e=r(8014);t.exports=function(t){return e(t.length)}},6395:function(t){t.exports=!1},6699:function(t,n,r){var e=r(3724),o=r(4913),i=r(6980);t.exports=e?function(t,n,r){return o.f(t,n,i(1,r))}:function(t,n,r){return t[n]=r,t}},6823:function(t){var n=String;t.exports=function(t){try{return n(t)}catch(t){return"Object"}}},6840:function(t,n,r){var e=r(4901),o=r(4913),i=r(283),u=r(9433);t.exports=function(t,n,r,c){c||(c={});var a=c.enumerable,f=void 0!==c.name?c.name:n;if(e(r)&&i(r,f,c),c.global)a?t[n]=r:u(n,r);else{try{c.unsafe?t[n]&&(a=!0):delete t[n]}catch(t){}a?t[n]=r:o.f(t,n,{value:r,enumerable:!1,configurable:!c.nonConfigurable,writable:!c.nonWritable})}return t}},6955:function(t,n,r){var e=r(2140),o=r(4901),i=r(2195),u=r(8227)("toStringTag"),c=Object,a="Arguments"===i(function(){return arguments}());t.exports=e?i:function(t){var n,r,e;return void 0===t?"Undefined":null===t?"Null":"string"==typeof(r=function(t,n){try{return t[n]}catch(t){}}(n=c(t),u))?r:a?i(n):"Object"===(e=i(n))&&o(n.callee)?"Arguments":e}},6969:function(t,n,r){var e=r(2777),o=r(757);t.exports=function(t){var n=e(t,"string");return o(n)?n:n+""}},6980:function(t){t.exports=function(t,n){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:n}}},7040:function(t,n,r){var e=r(4495);t.exports=e&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},7055:function(t,n,r){var e=r(9504),o=r(9039),i=r(2195),u=Object,c=e("".split);t.exports=o(function(){return!u("z").propertyIsEnumerable(0)})?function(t){return"String"===i(t)?c(t,""):u(t)}:u},7400:function(t){t.exports={CSSRuleList:0,CSSStyleDeclaration:0,CSSValueList:0,ClientRectList:0,DOMRectList:0,DOMStringList:0,DOMTokenList:1,DataTransferItemList:0,FileList:0,HTMLAllCollection:0,HTMLCollection:0,HTMLFormElement:0,HTMLSelectElement:0,MediaList:0,MimeTypeArray:0,NamedNodeMap:0,NodeList:1,PaintRequestList:0,Plugin:0,PluginArray:0,SVGLengthList:0,SVGNumberList:0,SVGPathSegList:0,SVGPointList:0,SVGStringList:0,SVGTransformList:0,SourceBufferList:0,StyleSheetList:0,TextTrackCueList:0,TextTrackList:0,TouchList:0}},7433:function(t,n,r){var e=r(4376),o=r(3517),i=r(34),u=r(8227)("species"),c=Array;t.exports=function(t){var n;return e(t)&&(n=t.constructor,(o(n)&&(n===c||e(n.prototype))||i(n)&&null===(n=n[u]))&&(n=void 0)),void 0===n?c:n}},7476:function(t,n,r){var e=r(2195),o=r(9504);t.exports=function(t){if("Function"===e(t))return o(t)}},7629:function(t,n,r){var e=r(6395),o=r(4576),i=r(9433),u="__core-js_shared__",c=t.exports=o[u]||i(u,{});(c.versions||(c.versions=[])).push({version:"3.47.0",mode:e?"pure":"global",copyright:"© 2014-2025 Denis Pushkarev (zloirock.ru), 2025 CoreJS Company (core-js.io)",license:"https://github.com/zloirock/core-js/blob/v3.47.0/LICENSE",source:"https://github.com/zloirock/core-js"})},7750:function(t,n,r){var e=r(4117),o=TypeError;t.exports=function(t){if(e(t))throw new o("Can't call method on "+t);return t}},7751:function(t,n,r){var e=r(4576),o=r(4901);t.exports=function(t,n){return arguments.length<2?(r=e[t],o(r)?r:void 0):e[t]&&e[t][n];var r}},8014:function(t,n,r){var e=r(1291),o=Math.min;t.exports=function(t){var n=e(t);return n>0?o(n,9007199254740991):0}},8227:function(t,n,r){var e=r(4576),o=r(5745),i=r(9297),u=r(3392),c=r(4495),a=r(7040),f=e.Symbol,s=o("wks"),l=a?f.for||f:f&&f.withoutSetter||u;t.exports=function(t){return i(s,t)||(s[t]=c&&i(f,t)?f[t]:l("Symbol."+t)),s[t]}},8551:function(t,n,r){var e=r(34),o=String,i=TypeError;t.exports=function(t){if(e(t))return t;throw new i(o(t)+" is not an object")}},8622:function(t,n,r){var e=r(4576),o=r(4901),i=e.WeakMap;t.exports=o(i)&&/native code/.test(String(i))},8686:function(t,n,r){var e=r(3724),o=r(9039);t.exports=e&&o(function(){return 42!==Object.defineProperty(function(){},"prototype",{value:42,writable:!1}).prototype})},8981:function(t,n,r){var e=r(7750),o=Object;t.exports=function(t){return o(e(t))}},9039:function(t){t.exports=function(t){try{return!!t()}catch(t){return!0}}},9213:function(t,n,r){var e=r(6080),o=r(9504),i=r(7055),u=r(8981),c=r(6198),a=r(1469),f=o([].push),s=function(t){var n=1===t,r=2===t,o=3===t,s=4===t,l=6===t,p=7===t,v=5===t||l;return function(y,b,h,g){for(var m,d,x=u(y),S=i(x),w=c(S),E=e(b,h),j=0,A=g||a,O=n?A(y,w):r||p?A(y,0):void 0;w>j;j++)if((v||j in S)&&(d=E(m=S[j],j,x),t))if(n)O[j]=d;else if(d)switch(t){case 3:return!0;case 5:return m;case 6:return j;case 2:f(O,m)}else switch(t){case 4:return!1;case 7:f(O,m)}return l?-1:o||s?s:O}};t.exports={forEach:s(0),map:s(1),filter:s(2),some:s(3),every:s(4),find:s(5),findIndex:s(6),filterReject:s(7)}},9296:function(t,n,r){var e=r(4055)("span").classList,o=e&&e.constructor&&e.constructor.prototype;t.exports=o===Object.prototype?void 0:o},9297:function(t,n,r){var e=r(9504),o=r(8981),i=e({}.hasOwnProperty);t.exports=Object.hasOwn||function(t,n){return i(o(t),n)}},9306:function(t,n,r){var e=r(4901),o=r(6823),i=TypeError;t.exports=function(t){if(e(t))return t;throw new i(o(t)+" is not a function")}},9433:function(t,n,r){var e=r(4576),o=Object.defineProperty;t.exports=function(t,n){try{o(e,t,{value:n,configurable:!0,writable:!0})}catch(r){e[t]=n}return n}},9504:function(t,n,r){var e=r(616),o=Function.prototype,i=o.call,u=e&&o.bind.bind(i,i);t.exports=e?u:function(t){return function(){return i.apply(t,arguments)}}},9519:function(t,n,r){var e,o,i=r(4576),u=r(2839),c=i.process,a=i.Deno,f=c&&c.versions||a&&a.version,s=f&&f.v8;s&&(o=(e=s.split("."))[0]>0&&e[0]<4?1:+(e[0]+e[1])),!o&&u&&(!(e=u.match(/Edge\/(\d+)/))||e[1]>=74)&&(e=u.match(/Chrome\/(\d+)/))&&(o=+e[1]),t.exports=o},9565:function(t,n,r){var e=r(616),o=Function.prototype.call;t.exports=e?o.bind(o):function(){return o.apply(o,arguments)}}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var i=n[e]={exports:{}};return t[e].call(i.exports,i,i.exports,r),i.exports}function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},e(t)}function o(t){var n=function(t){if("object"!=e(t)||!t)return t;var n=t[Symbol.toPrimitive];if(void 0!==n){var r=n.call(t,"string");if("object"!=e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(t)}(t);return"symbol"==e(n)?n:n+""}function i(t,n){for(var r=0;r<n.length;r++){var e=n[r];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(t,o(e.key),e)}}function u(t,n,r){return(n=o(n))in t?Object.defineProperty(t,n,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[n]=r,t}r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),r(6099),r(3500);var c=function(){return t=function t(){!function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}(this,t),u(this,"DRAGGABLE_DRAGSTART",t.DRAGGABLE_DRAGSTART),u(this,"DRAGGABLE_DRAGMOVE",t.DRAGGABLE_DRAGMOVE),u(this,"DRAGGABLE_DRAGEND",t.DRAGGABLE_DRAGEND),u(this,"mPlugins",void 0),u(this,"mEvents",void 0),this.mPlugins={},this.mEvents={}},n=[{key:"registerPlugin",value:function(t,n){return void 0===this.mPlugins[t]&&(this.mPlugins[t]=n,this[t]=function(){var n;return(n=this.mPlugins)[t].apply(n,arguments)},!0)}},{key:"unregisterPlugin",value:function(t){return void 0!==this.mPlugins[t]&&(delete this.mPlugins[t],delete this[t],!0)}},{key:"subscribe",value:function(t,n){return void 0===this.mEvents[t]&&(this.mEvents[t]=[]),this.mEvents[t].push(n)}},{key:"unsubscribe",value:function(t,n){void 0!==this.mEvents[t]&&(this.mEvents[t][n]=void 0)}},{key:"emit",value:function(t,n){void 0!==this.mEvents[t]&&this.mEvents[t].forEach(function(t){void 0!==t&&setTimeout(function(){t(n)},0)})}}],n&&i(t.prototype,n),Object.defineProperty(t,"prototype",{writable:!1}),t;var t,n}();u(c,"DRAGGABLE_DRAGSTART","draggable:dragstart"),u(c,"DRAGGABLE_DRAGMOVE","draggable:dragmove"),u(c,"DRAGGABLE_DRAGEND","draggable:dragend"),window.expivi=window.expivi||{interact:null};var a=null;window.expivi.interact=function(){return null===a&&(a=new c),a}}();
!function(){"use strict";var t={34:function(t,n,r){var e=r(4901);t.exports=function(t){return"object"==typeof t?null!==t:e(t)}},235:function(t,n,r){var e=r(9213).forEach,o=r(4598)("forEach");t.exports=o?[].forEach:function(t){return e(this,t,arguments.length>1?arguments[1]:void 0)}},283:function(t,n,r){var e=r(9504),o=r(9039),i=r(4901),u=r(9297),c=r(3724),a=r(350).CONFIGURABLE,f=r(3706),s=r(1181),p=s.enforce,l=s.get,v=String,h=Object.defineProperty,y=e("".slice),m=e("".replace),d=e([].join),b=c&&!o(function(){return 8!==h(function(){},"length",{value:8}).length}),g=String(String).split("String"),w=t.exports=function(t,n,r){"Symbol("===y(v(n),0,7)&&(n="["+m(v(n),/^Symbol\(([^)]*)\).*$/,"$1")+"]"),r&&r.getter&&(n="get "+n),r&&r.setter&&(n="set "+n),(!u(t,"name")||a&&t.name!==n)&&(c?h(t,"name",{value:n,configurable:!0}):t.name=n),b&&r&&u(r,"arity")&&t.length!==r.arity&&h(t,"length",{value:r.arity});try{r&&u(r,"constructor")&&r.constructor?c&&h(t,"prototype",{writable:!1}):t.prototype&&(t.prototype=void 0)}catch(t){}var e=p(t);return u(e,"source")||(e.source=d(g,"string"==typeof n?n:"")),t};Function.prototype.toString=w(function(){return i(this)&&l(this).source||f(this)},"toString")},298:function(t,n,r){var e=r(2195),o=r(5397),i=r(8480).f,u=r(7680),c="object"==typeof window&&window&&Object.getOwnPropertyNames?Object.getOwnPropertyNames(window):[];t.exports.f=function(t){return c&&"Window"===e(t)?function(t){try{return i(t)}catch(t){return u(c)}}(t):i(o(t))}},350:function(t,n,r){var e=r(3724),o=r(9297),i=Function.prototype,u=e&&Object.getOwnPropertyDescriptor,c=o(i,"name"),a=c&&"something"===function(){}.name,f=c&&(!e||e&&u(i,"name").configurable);t.exports={EXISTS:c,PROPER:a,CONFIGURABLE:f}},397:function(t,n,r){var e=r(7751);t.exports=e("document","documentElement")},421:function(t){t.exports={}},511:function(t,n,r){var e=r(9167),o=r(9297),i=r(1951),u=r(4913).f;t.exports=function(t){var n=e.Symbol||(e.Symbol={});o(n,t)||u(n,t,{value:i.f(t)})}},597:function(t,n,r){var e=r(9039),o=r(8227),i=r(9519),u=o("species");t.exports=function(t){return i>=51||!e(function(){var n=[];return(n.constructor={})[u]=function(){return{foo:1}},1!==n[t](Boolean).foo})}},616:function(t,n,r){var e=r(9039);t.exports=!e(function(){var t=function(){}.bind();return"function"!=typeof t||t.hasOwnProperty("prototype")})},655:function(t,n,r){var e=r(6955),o=String;t.exports=function(t){if("Symbol"===e(t))throw new TypeError("Cannot convert a Symbol value to a string");return o(t)}},687:function(t,n,r){var e=r(4913).f,o=r(9297),i=r(8227)("toStringTag");t.exports=function(t,n,r){t&&!r&&(t=t.prototype),t&&!o(t,i)&&e(t,i,{configurable:!0,value:n})}},741:function(t){var n=Math.ceil,r=Math.floor;t.exports=Math.trunc||function(t){var e=+t;return(e>0?r:n)(e)}},757:function(t,n,r){var e=r(7751),o=r(4901),i=r(1625),u=r(7040),c=Object;t.exports=u?function(t){return"symbol"==typeof t}:function(t){var n=e("Symbol");return o(n)&&i(n.prototype,c(t))}},1072:function(t,n,r){var e=r(1828),o=r(8727);t.exports=Object.keys||function(t){return e(t,o)}},1181:function(t,n,r){var e,o,i,u=r(8622),c=r(4576),a=r(34),f=r(6699),s=r(9297),p=r(7629),l=r(6119),v=r(421),h="Object already initialized",y=c.TypeError,m=c.WeakMap;if(u||p.state){var d=p.state||(p.state=new m);d.get=d.get,d.has=d.has,d.set=d.set,e=function(t,n){if(d.has(t))throw new y(h);return n.facade=t,d.set(t,n),n},o=function(t){return d.get(t)||{}},i=function(t){return d.has(t)}}else{var b=l("state");v[b]=!0,e=function(t,n){if(s(t,b))throw new y(h);return n.facade=t,f(t,b,n),n},o=function(t){return s(t,b)?t[b]:{}},i=function(t){return s(t,b)}}t.exports={set:e,get:o,has:i,enforce:function(t){return i(t)?o(t):e(t,{})},getterFor:function(t){return function(n){var r;if(!a(n)||(r=o(n)).type!==t)throw new y("Incompatible receiver, "+t+" required");return r}}}},1278:function(t,n,r){var e=r(6518),o=r(3724),i=r(5031),u=r(5397),c=r(7347),a=r(4659);e({target:"Object",stat:!0,sham:!o},{getOwnPropertyDescriptors:function(t){for(var n,r,e=u(t),o=c.f,f=i(e),s={},p=0;f.length>p;)void 0!==(r=o(e,n=f[p++]))&&a(s,n,r);return s}})},1291:function(t,n,r){var e=r(741);t.exports=function(t){var n=+t;return n!=n||0===n?0:e(n)}},1296:function(t,n,r){var e=r(4495);t.exports=e&&!!Symbol.for&&!!Symbol.keyFor},1469:function(t,n,r){var e=r(7433);t.exports=function(t,n){return new(e(t))(0===n?0:n)}},1510:function(t,n,r){var e=r(6518),o=r(7751),i=r(9297),u=r(655),c=r(5745),a=r(1296),f=c("string-to-symbol-registry"),s=c("symbol-to-string-registry");e({target:"Symbol",stat:!0,forced:!a},{for:function(t){var n=u(t);if(i(f,n))return f[n];var r=o("Symbol")(n);return f[n]=r,s[r]=n,r}})},1625:function(t,n,r){var e=r(9504);t.exports=e({}.isPrototypeOf)},1828:function(t,n,r){var e=r(9504),o=r(9297),i=r(5397),u=r(9617).indexOf,c=r(421),a=e([].push);t.exports=function(t,n){var r,e=i(t),f=0,s=[];for(r in e)!o(c,r)&&o(e,r)&&a(s,r);for(;n.length>f;)o(e,r=n[f++])&&(~u(s,r)||a(s,r));return s}},1951:function(t,n,r){var e=r(8227);n.f=e},2008:function(t,n,r){var e=r(6518),o=r(9213).filter;e({target:"Array",proto:!0,forced:!r(597)("filter")},{filter:function(t){return o(this,t,arguments.length>1?arguments[1]:void 0)}})},2062:function(t,n,r){var e=r(6518),o=r(9213).map;e({target:"Array",proto:!0,forced:!r(597)("map")},{map:function(t){return o(this,t,arguments.length>1?arguments[1]:void 0)}})},2106:function(t,n,r){var e=r(283),o=r(4913);t.exports=function(t,n,r){return r.get&&e(r.get,n,{getter:!0}),r.set&&e(r.set,n,{setter:!0}),o.f(t,n,r)}},2140:function(t,n,r){var e={};e[r(8227)("toStringTag")]="z",t.exports="[object z]"===String(e)},2195:function(t,n,r){var e=r(9504),o=e({}.toString),i=e("".slice);t.exports=function(t){return i(o(t),8,-1)}},2360:function(t,n,r){var e,o=r(8551),i=r(6801),u=r(8727),c=r(421),a=r(397),f=r(4055),s=r(6119),p="prototype",l="script",v=s("IE_PROTO"),h=function(){},y=function(t){return"<"+l+">"+t+"</"+l+">"},m=function(t){t.write(y("")),t.close();var n=t.parentWindow.Object;return t=null,n},d=function(){try{e=new ActiveXObject("htmlfile")}catch(t){}var t,n,r;d="undefined"!=typeof document?document.domain&&e?m(e):(n=f("iframe"),r="java"+l+":",n.style.display="none",a.appendChild(n),n.src=String(r),(t=n.contentWindow.document).open(),t.write(y("document.F=Object")),t.close(),t.F):m(e);for(var o=u.length;o--;)delete d[p][u[o]];return d()};c[v]=!0,t.exports=Object.create||function(t,n){var r;return null!==t?(h[p]=o(t),r=new h,h[p]=null,r[v]=t):r=d(),void 0===n?r:i.f(r,n)}},2675:function(t,n,r){r(6761),r(1510),r(7812),r(3110),r(9773)},2777:function(t,n,r){var e=r(9565),o=r(34),i=r(757),u=r(5966),c=r(4270),a=r(8227),f=TypeError,s=a("toPrimitive");t.exports=function(t,n){if(!o(t)||i(t))return t;var r,a=u(t,s);if(a){if(void 0===n&&(n="default"),r=e(a,t,n),!o(r)||i(r))return r;throw new f("Can't convert object to primitive value")}return void 0===n&&(n="number"),c(t,n)}},2796:function(t,n,r){var e=r(9039),o=r(4901),i=/#|\.prototype\./,u=function(t,n){var r=a[c(t)];return r===s||r!==f&&(o(n)?e(n):!!n)},c=u.normalize=function(t){return String(t).replace(i,".").toLowerCase()},a=u.data={},f=u.NATIVE="N",s=u.POLYFILL="P";t.exports=u},2839:function(t,n,r){var e=r(4576).navigator,o=e&&e.userAgent;t.exports=o?String(o):""},3110:function(t,n,r){var e=r(6518),o=r(7751),i=r(8745),u=r(9565),c=r(9504),a=r(9039),f=r(4376),s=r(4901),p=r(5810),l=r(757),v=r(2195),h=r(655),y=r(7680),m=r(8235),d=r(3392),b=r(4495),g=r(7819),w=String,O=o("JSON","stringify"),S=c(/./.exec),x=c("".charAt),j=c("".charCodeAt),E=c("".replace),P=c("".slice),D=c([].push),A=c(1.1.toString),L=/[\uD800-\uDFFF]/g,T=/^[\uD800-\uDBFF]$/,R=/^[\uDC00-\uDFFF]$/,k=d(),I=k.length,F=!b||a(function(){var t=o("Symbol")("stringify detection");return"[null]"!==O([t])||"{}"!==O({a:t})||"{}"!==O(Object(t))}),M=a(function(){return'"\\udf06\\ud834"'!==O("\udf06\ud834")||'"\\udead"'!==O("\udead")}),N=F?function(t,n){var r=y(arguments),e=C(n);if(s(e)||void 0!==t&&!l(t))return r[1]=function(t,n){if(s(e)&&(n=u(e,this,w(t),n)),!l(n))return n},i(O,null,r)}:O,G=function(t,n,r){var e=x(r,n-1),o=x(r,n+1);return S(T,t)&&!S(R,o)||S(R,t)&&!S(T,e)?"\\u"+A(j(t,0),16):t},C=function(t){if(s(t))return t;if(f(t)){for(var n=t.length,r=[],e=0;e<n;e++){var o=t[e];"string"==typeof o?D(r,o):"number"!=typeof o&&"Number"!==v(o)&&"String"!==v(o)||D(r,h(o))}var i=r.length,u=!0;return function(t,n){if(u)return u=!1,n;if(f(this))return n;for(var e=0;e<i;e++)if(r[e]===t)return n}}};O&&e({target:"JSON",stat:!0,arity:3,forced:F||M||!g},{stringify:function(t,n,r){var e=C(n),o=[],i=N(t,function(t,n){var r=s(e)?u(e,this,w(t),n):n;return!g&&p(r)?k+(D(o,r.rawJSON)-1):r},r);if("string"!=typeof i)return i;if(M&&(i=E(i,L,G)),g)return i;for(var c="",a=i.length,f=0;f<a;f++){var l=x(i,f);if('"'===l){var v=m(i,++f).end-1,h=P(i,f,v);c+=P(h,0,I)===k?o[P(h,I)]:'"'+h+'"',f=v}else c+=l}return c}})},3179:function(t,n,r){var e=r(2140),o=r(6955);t.exports=e?{}.toString:function(){return"[object "+o(this)+"]"}},3392:function(t,n,r){var e=r(9504),o=0,i=Math.random(),u=e(1.1.toString);t.exports=function(t){return"Symbol("+(void 0===t?"":t)+")_"+u(++o+i,36)}},3500:function(t,n,r){var e=r(4576),o=r(7400),i=r(9296),u=r(235),c=r(6699),a=function(t){if(t&&t.forEach!==u)try{c(t,"forEach",u)}catch(n){t.forEach=u}};for(var f in o)o[f]&&a(e[f]&&e[f].prototype);a(i)},3517:function(t,n,r){var e=r(9504),o=r(9039),i=r(4901),u=r(6955),c=r(7751),a=r(3706),f=function(){},s=c("Reflect","construct"),p=/^\s*(?:class|function)\b/,l=e(p.exec),v=!p.test(f),h=function(t){if(!i(t))return!1;try{return s(f,[],t),!0}catch(t){return!1}},y=function(t){if(!i(t))return!1;switch(u(t)){case"AsyncFunction":case"GeneratorFunction":case"AsyncGeneratorFunction":return!1}try{return v||!!l(p,a(t))}catch(t){return!0}};y.sham=!0,t.exports=!s||o(function(){var t;return h(h.call)||!h(Object)||!h(function(){t=!0})||t})?y:h},3706:function(t,n,r){var e=r(9504),o=r(4901),i=r(7629),u=e(Function.toString);o(i.inspectSource)||(i.inspectSource=function(t){return u(t)}),t.exports=i.inspectSource},3717:function(t,n){n.f=Object.getOwnPropertySymbols},3724:function(t,n,r){var e=r(9039);t.exports=!e(function(){return 7!==Object.defineProperty({},1,{get:function(){return 7}})[1]})},3802:function(t,n,r){var e=r(9504),o=r(7750),i=r(655),u=r(7452),c=e("".replace),a=RegExp("^["+u+"]+"),f=RegExp("(^|[^"+u+"])["+u+"]+$"),s=function(t){return function(n){var r=i(o(n));return 1&t&&(r=c(r,a,"")),2&t&&(r=c(r,f,"$1")),r}};t.exports={start:s(1),end:s(2),trim:s(3)}},3851:function(t,n,r){var e=r(6518),o=r(9039),i=r(5397),u=r(7347).f,c=r(3724);e({target:"Object",stat:!0,forced:!c||o(function(){u(1)}),sham:!c},{getOwnPropertyDescriptor:function(t,n){return u(i(t),n)}})},3904:function(t,n,r){var e=r(4576),o=r(9039),i=r(9504),u=r(655),c=r(3802).trim,a=r(7452),f=i("".charAt),s=e.parseFloat,p=e.Symbol,l=p&&p.iterator,v=1/s(a+"-0")!=-1/0||l&&!o(function(){s(Object(l))});t.exports=v?function(t){var n=c(u(t)),r=s(n);return 0===r&&"-"===f(n,0)?-0:r}:s},4055:function(t,n,r){var e=r(4576),o=r(34),i=e.document,u=o(i)&&o(i.createElement);t.exports=function(t){return u?i.createElement(t):{}}},4117:function(t){t.exports=function(t){return null==t}},4270:function(t,n,r){var e=r(9565),o=r(4901),i=r(34),u=TypeError;t.exports=function(t,n){var r,c;if("string"===n&&o(r=t.toString)&&!i(c=e(r,t)))return c;if(o(r=t.valueOf)&&!i(c=e(r,t)))return c;if("string"!==n&&o(r=t.toString)&&!i(c=e(r,t)))return c;throw new u("Can't convert object to primitive value")}},4376:function(t,n,r){var e=r(2195);t.exports=Array.isArray||function(t){return"Array"===e(t)}},4495:function(t,n,r){var e=r(9519),o=r(9039),i=r(4576).String;t.exports=!!Object.getOwnPropertySymbols&&!o(function(){var t=Symbol("symbol detection");return!i(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&e&&e<41})},4527:function(t,n,r){var e=r(3724),o=r(4376),i=TypeError,u=Object.getOwnPropertyDescriptor,c=e&&!function(){if(void 0!==this)return!0;try{Object.defineProperty([],"length",{writable:!1}).length=1}catch(t){return t instanceof TypeError}}();t.exports=c?function(t,n){if(o(t)&&!u(t,"length").writable)throw new i("Cannot set read only .length");return t.length=n}:function(t,n){return t.length=n}},4554:function(t,n,r){var e=r(6518),o=r(8981),i=r(5610),u=r(1291),c=r(6198),a=r(4527),f=r(6837),s=r(1469),p=r(4659),l=r(4606),v=r(597)("splice"),h=Math.max,y=Math.min;e({target:"Array",proto:!0,forced:!v},{splice:function(t,n){var r,e,v,m,d,b,g=o(this),w=c(g),O=i(t,w),S=arguments.length;for(0===S?r=e=0:1===S?(r=0,e=w-O):(r=S-2,e=y(h(u(n),0),w-O)),f(w+r-e),v=s(g,e),m=0;m<e;m++)(d=O+m)in g&&p(v,m,g[d]);if(v.length=e,r<e){for(m=O;m<w-e;m++)b=m+r,(d=m+e)in g?g[b]=g[d]:l(g,b);for(m=w;m>w-e+r;m--)l(g,m-1)}else if(r>e)for(m=w-e;m>O;m--)b=m+r-1,(d=m+e-1)in g?g[b]=g[d]:l(g,b);for(m=0;m<r;m++)g[m+O]=arguments[m+2];return a(g,w-e+r),v}})},4576:function(t,n,r){var e=function(t){return t&&t.Math===Math&&t};t.exports=e("object"==typeof globalThis&&globalThis)||e("object"==typeof window&&window)||e("object"==typeof self&&self)||e("object"==typeof r.g&&r.g)||e("object"==typeof this&&this)||function(){return this}()||Function("return this")()},4598:function(t,n,r){var e=r(9039);t.exports=function(t,n){var r=[][t];return!!r&&e(function(){r.call(null,n||function(){return 1},1)})}},4606:function(t,n,r){var e=r(6823),o=TypeError;t.exports=function(t,n){if(!delete t[n])throw new o("Cannot delete property "+e(n)+" of "+e(t))}},4659:function(t,n,r){var e=r(3724),o=r(4913),i=r(6980);t.exports=function(t,n,r){e?o.f(t,n,i(0,r)):t[n]=r}},4901:function(t){var n="object"==typeof document&&document.all;t.exports=void 0===n&&void 0!==n?function(t){return"function"==typeof t||t===n}:function(t){return"function"==typeof t}},4913:function(t,n,r){var e=r(3724),o=r(5917),i=r(8686),u=r(8551),c=r(6969),a=TypeError,f=Object.defineProperty,s=Object.getOwnPropertyDescriptor,p="enumerable",l="configurable",v="writable";n.f=e?i?function(t,n,r){if(u(t),n=c(n),u(r),"function"==typeof t&&"prototype"===n&&"value"in r&&v in r&&!r[v]){var e=s(t,n);e&&e[v]&&(t[n]=r.value,r={configurable:l in r?r[l]:e[l],enumerable:p in r?r[p]:e[p],writable:!1})}return f(t,n,r)}:f:function(t,n,r){if(u(t),n=c(n),u(r),o)try{return f(t,n,r)}catch(t){}if("get"in r||"set"in r)throw new a("Accessors not supported");return"value"in r&&(t[n]=r.value),t}},5031:function(t,n,r){var e=r(7751),o=r(9504),i=r(8480),u=r(3717),c=r(8551),a=o([].concat);t.exports=e("Reflect","ownKeys")||function(t){var n=i.f(c(t)),r=u.f;return r?a(n,r(t)):n}},5397:function(t,n,r){var e=r(7055),o=r(7750);t.exports=function(t){return e(o(t))}},5610:function(t,n,r){var e=r(1291),o=Math.max,i=Math.min;t.exports=function(t,n){var r=e(t);return r<0?o(r+n,0):i(r,n)}},5745:function(t,n,r){var e=r(7629);t.exports=function(t,n){return e[t]||(e[t]=n||{})}},5810:function(t,n,r){var e=r(34),o=r(1181).get;t.exports=function(t){if(!e(t))return!1;var n=o(t);return!!n&&"RawJSON"===n.type}},5917:function(t,n,r){var e=r(3724),o=r(9039),i=r(4055);t.exports=!e&&!o(function(){return 7!==Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a})},5966:function(t,n,r){var e=r(9306),o=r(4117);t.exports=function(t,n){var r=t[n];return o(r)?void 0:e(r)}},6080:function(t,n,r){var e=r(7476),o=r(9306),i=r(616),u=e(e.bind);t.exports=function(t,n){return o(t),void 0===n?t:i?u(t,n):function(){return t.apply(n,arguments)}}},6099:function(t,n,r){var e=r(2140),o=r(6840),i=r(3179);e||o(Object.prototype,"toString",i,{unsafe:!0})},6119:function(t,n,r){var e=r(5745),o=r(3392),i=e("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},6198:function(t,n,r){var e=r(8014);t.exports=function(t){return e(t.length)}},6395:function(t){t.exports=!1},6469:function(t,n,r){var e=r(8227),o=r(2360),i=r(4913).f,u=e("unscopables"),c=Array.prototype;void 0===c[u]&&i(c,u,{configurable:!0,value:o(null)}),t.exports=function(t){c[u][t]=!0}},6518:function(t,n,r){var e=r(4576),o=r(7347).f,i=r(6699),u=r(6840),c=r(9433),a=r(7740),f=r(2796);t.exports=function(t,n){var r,s,p,l,v,h=t.target,y=t.global,m=t.stat;if(r=y?e:m?e[h]||c(h,{}):e[h]&&e[h].prototype)for(s in n){if(l=n[s],p=t.dontCallGetSet?(v=o(r,s))&&v.value:r[s],!f(y?s:h+(m?".":"#")+s,t.forced)&&void 0!==p){if(typeof l==typeof p)continue;a(l,p)}(t.sham||p&&p.sham)&&i(l,"sham",!0),u(r,s,l,t)}}},6699:function(t,n,r){var e=r(3724),o=r(4913),i=r(6980);t.exports=e?function(t,n,r){return o.f(t,n,i(1,r))}:function(t,n,r){return t[n]=r,t}},6761:function(t,n,r){var e=r(6518),o=r(4576),i=r(9565),u=r(9504),c=r(6395),a=r(3724),f=r(4495),s=r(9039),p=r(9297),l=r(1625),v=r(8551),h=r(5397),y=r(6969),m=r(655),d=r(6980),b=r(2360),g=r(1072),w=r(8480),O=r(298),S=r(3717),x=r(7347),j=r(4913),E=r(6801),P=r(8773),D=r(6840),A=r(2106),L=r(5745),T=r(6119),R=r(421),k=r(3392),I=r(8227),F=r(1951),M=r(511),N=r(8242),G=r(687),C=r(1181),z=r(9213).forEach,B=T("hidden"),_="Symbol",J="prototype",V=C.set,W=C.getterFor(_),$=Object[J],U=o.Symbol,q=U&&U[J],X=o.RangeError,H=o.TypeError,Y=o.QObject,Q=x.f,K=j.f,Z=O.f,tt=P.f,nt=u([].push),rt=L("symbols"),et=L("op-symbols"),ot=L("wks"),it=!Y||!Y[J]||!Y[J].findChild,ut=function(t,n,r){var e=Q($,n);e&&delete $[n],K(t,n,r),e&&t!==$&&K($,n,e)},ct=a&&s(function(){return 7!==b(K({},"a",{get:function(){return K(this,"a",{value:7}).a}})).a})?ut:K,at=function(t,n){var r=rt[t]=b(q);return V(r,{type:_,tag:t,description:n}),a||(r.description=n),r},ft=function(t,n,r){t===$&&ft(et,n,r),v(t);var e=y(n);return v(r),p(rt,e)?(r.enumerable?(p(t,B)&&t[B][e]&&(t[B][e]=!1),r=b(r,{enumerable:d(0,!1)})):(p(t,B)||K(t,B,d(1,b(null))),t[B][e]=!0),ct(t,e,r)):K(t,e,r)},st=function(t,n){v(t);var r=h(n),e=g(r).concat(ht(r));return z(e,function(n){a&&!i(pt,r,n)||ft(t,n,r[n])}),t},pt=function(t){var n=y(t),r=i(tt,this,n);return!(this===$&&p(rt,n)&&!p(et,n))&&(!(r||!p(this,n)||!p(rt,n)||p(this,B)&&this[B][n])||r)},lt=function(t,n){var r=h(t),e=y(n);if(r!==$||!p(rt,e)||p(et,e)){var o=Q(r,e);return!o||!p(rt,e)||p(r,B)&&r[B][e]||(o.enumerable=!0),o}},vt=function(t){var n=Z(h(t)),r=[];return z(n,function(t){p(rt,t)||p(R,t)||nt(r,t)}),r},ht=function(t){var n=t===$,r=Z(n?et:h(t)),e=[];return z(r,function(t){!p(rt,t)||n&&!p($,t)||nt(e,rt[t])}),e};f||(U=function(){if(l(q,this))throw new H("Symbol is not a constructor");var t=arguments.length&&void 0!==arguments[0]?m(arguments[0]):void 0,n=k(t),r=function(t){var e=void 0===this?o:this;e===$&&i(r,et,t),p(e,B)&&p(e[B],n)&&(e[B][n]=!1);var u=d(1,t);try{ct(e,n,u)}catch(t){if(!(t instanceof X))throw t;ut(e,n,u)}};return a&&it&&ct($,n,{configurable:!0,set:r}),at(n,t)},D(q=U[J],"toString",function(){return W(this).tag}),D(U,"withoutSetter",function(t){return at(k(t),t)}),P.f=pt,j.f=ft,E.f=st,x.f=lt,w.f=O.f=vt,S.f=ht,F.f=function(t){return at(I(t),t)},a&&(A(q,"description",{configurable:!0,get:function(){return W(this).description}}),c||D($,"propertyIsEnumerable",pt,{unsafe:!0}))),e({global:!0,constructor:!0,wrap:!0,forced:!f,sham:!f},{Symbol:U}),z(g(ot),function(t){M(t)}),e({target:_,stat:!0,forced:!f},{useSetter:function(){it=!0},useSimple:function(){it=!1}}),e({target:"Object",stat:!0,forced:!f,sham:!a},{create:function(t,n){return void 0===n?b(t):st(b(t),n)},defineProperty:ft,defineProperties:st,getOwnPropertyDescriptor:lt}),e({target:"Object",stat:!0,forced:!f},{getOwnPropertyNames:vt}),N(),G(U,_),R[B]=!0},6801:function(t,n,r){var e=r(3724),o=r(8686),i=r(4913),u=r(8551),c=r(5397),a=r(1072);n.f=e&&!o?Object.defineProperties:function(t,n){u(t);for(var r,e=c(n),o=a(n),f=o.length,s=0;f>s;)i.f(t,r=o[s++],e[r]);return t}},6823:function(t){var n=String;t.exports=function(t){try{return n(t)}catch(t){return"Object"}}},6837:function(t){var n=TypeError;t.exports=function(t){if(t>9007199254740991)throw n("Maximum allowed index exceeded");return t}},6840:function(t,n,r){var e=r(4901),o=r(4913),i=r(283),u=r(9433);t.exports=function(t,n,r,c){c||(c={});var a=c.enumerable,f=void 0!==c.name?c.name:n;if(e(r)&&i(r,f,c),c.global)a?t[n]=r:u(n,r);else{try{c.unsafe?t[n]&&(a=!0):delete t[n]}catch(t){}a?t[n]=r:o.f(t,n,{value:r,enumerable:!1,configurable:!c.nonConfigurable,writable:!c.nonWritable})}return t}},6955:function(t,n,r){var e=r(2140),o=r(4901),i=r(2195),u=r(8227)("toStringTag"),c=Object,a="Arguments"===i(function(){return arguments}());t.exports=e?i:function(t){var n,r,e;return void 0===t?"Undefined":null===t?"Null":"string"==typeof(r=function(t,n){try{return t[n]}catch(t){}}(n=c(t),u))?r:a?i(n):"Object"===(e=i(n))&&o(n.callee)?"Arguments":e}},6969:function(t,n,r){var e=r(2777),o=r(757);t.exports=function(t){var n=e(t,"string");return o(n)?n:n+""}},6980:function(t){t.exports=function(t,n){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:n}}},7040:function(t,n,r){var e=r(4495);t.exports=e&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},7055:function(t,n,r){var e=r(9504),o=r(9039),i=r(2195),u=Object,c=e("".split);t.exports=o(function(){return!u("z").propertyIsEnumerable(0)})?function(t){return"String"===i(t)?c(t,""):u(t)}:u},7347:function(t,n,r){var e=r(3724),o=r(9565),i=r(8773),u=r(6980),c=r(5397),a=r(6969),f=r(9297),s=r(5917),p=Object.getOwnPropertyDescriptor;n.f=e?p:function(t,n){if(t=c(t),n=a(n),s)try{return p(t,n)}catch(t){}if(f(t,n))return u(!o(i.f,t,n),t[n])}},7400:function(t){t.exports={CSSRuleList:0,CSSStyleDeclaration:0,CSSValueList:0,ClientRectList:0,DOMRectList:0,DOMStringList:0,DOMTokenList:1,DataTransferItemList:0,FileList:0,HTMLAllCollection:0,HTMLCollection:0,HTMLFormElement:0,HTMLSelectElement:0,MediaList:0,MimeTypeArray:0,NamedNodeMap:0,NodeList:1,PaintRequestList:0,Plugin:0,PluginArray:0,SVGLengthList:0,SVGNumberList:0,SVGPathSegList:0,SVGPointList:0,SVGStringList:0,SVGTransformList:0,SourceBufferList:0,StyleSheetList:0,TextTrackCueList:0,TextTrackList:0,TouchList:0}},7433:function(t,n,r){var e=r(4376),o=r(3517),i=r(34),u=r(8227)("species"),c=Array;t.exports=function(t){var n;return e(t)&&(n=t.constructor,(o(n)&&(n===c||e(n.prototype))||i(n)&&null===(n=n[u]))&&(n=void 0)),void 0===n?c:n}},7452:function(t){t.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},7476:function(t,n,r){var e=r(2195),o=r(9504);t.exports=function(t){if("Function"===e(t))return o(t)}},7629:function(t,n,r){var e=r(6395),o=r(4576),i=r(9433),u="__core-js_shared__",c=t.exports=o[u]||i(u,{});(c.versions||(c.versions=[])).push({version:"3.47.0",mode:e?"pure":"global",copyright:"© 2014-2025 Denis Pushkarev (zloirock.ru), 2025 CoreJS Company (core-js.io)",license:"https://github.com/zloirock/core-js/blob/v3.47.0/LICENSE",source:"https://github.com/zloirock/core-js"})},7680:function(t,n,r){var e=r(9504);t.exports=e([].slice)},7740:function(t,n,r){var e=r(9297),o=r(5031),i=r(7347),u=r(4913);t.exports=function(t,n,r){for(var c=o(n),a=u.f,f=i.f,s=0;s<c.length;s++){var p=c[s];e(t,p)||r&&e(r,p)||a(t,p,f(n,p))}}},7750:function(t,n,r){var e=r(4117),o=TypeError;t.exports=function(t){if(e(t))throw new o("Can't call method on "+t);return t}},7751:function(t,n,r){var e=r(4576),o=r(4901);t.exports=function(t,n){return arguments.length<2?(r=e[t],o(r)?r:void 0):e[t]&&e[t][n];var r}},7812:function(t,n,r){var e=r(6518),o=r(9297),i=r(757),u=r(6823),c=r(5745),a=r(1296),f=c("symbol-to-string-registry");e({target:"Symbol",stat:!0,forced:!a},{keyFor:function(t){if(!i(t))throw new TypeError(u(t)+" is not a symbol");if(o(f,t))return f[t]}})},7819:function(t,n,r){var e=r(9039);t.exports=!e(function(){var t="9007199254740993",n=JSON.rawJSON(t);return!JSON.isRawJSON(n)||JSON.stringify(n)!==t})},8014:function(t,n,r){var e=r(1291),o=Math.min;t.exports=function(t){var n=e(t);return n>0?o(n,9007199254740991):0}},8227:function(t,n,r){var e=r(4576),o=r(5745),i=r(9297),u=r(3392),c=r(4495),a=r(7040),f=e.Symbol,s=o("wks"),p=a?f.for||f:f&&f.withoutSetter||u;t.exports=function(t){return i(s,t)||(s[t]=c&&i(f,t)?f[t]:p("Symbol."+t)),s[t]}},8235:function(t,n,r){var e=r(9504),o=r(9297),i=SyntaxError,u=parseInt,c=String.fromCharCode,a=e("".charAt),f=e("".slice),s=e(/./.exec),p={'\\"':'"',"\\\\":"\\","\\/":"/","\\b":"\b","\\f":"\f","\\n":"\n","\\r":"\r","\\t":"\t"},l=/^[\da-f]{4}$/i,v=/^[\u0000-\u001F]$/;t.exports=function(t,n){for(var r=!0,e="";n<t.length;){var h=a(t,n);if("\\"===h){var y=f(t,n,n+2);if(o(p,y))e+=p[y],n+=2;else{if("\\u"!==y)throw new i('Unknown escape sequence: "'+y+'"');var m=f(t,n+=2,n+4);if(!s(l,m))throw new i("Bad Unicode escape at: "+n);e+=c(u(m,16)),n+=4}}else{if('"'===h){r=!1,n++;break}if(s(v,h))throw new i("Bad control character in string literal at: "+n);e+=h,n++}}if(r)throw new i("Unterminated string at: "+n);return{value:e,end:n}}},8242:function(t,n,r){var e=r(9565),o=r(7751),i=r(8227),u=r(6840);t.exports=function(){var t=o("Symbol"),n=t&&t.prototype,r=n&&n.valueOf,c=i("toPrimitive");n&&!n[c]&&u(n,c,function(t){return e(r,this)},{arity:1})}},8459:function(t,n,r){var e=r(6518),o=r(3904);e({global:!0,forced:parseFloat!==o},{parseFloat:o})},8480:function(t,n,r){var e=r(1828),o=r(8727).concat("length","prototype");n.f=Object.getOwnPropertyNames||function(t){return e(t,o)}},8551:function(t,n,r){var e=r(34),o=String,i=TypeError;t.exports=function(t){if(e(t))return t;throw new i(o(t)+" is not an object")}},8622:function(t,n,r){var e=r(4576),o=r(4901),i=e.WeakMap;t.exports=o(i)&&/native code/.test(String(i))},8686:function(t,n,r){var e=r(3724),o=r(9039);t.exports=e&&o(function(){return 42!==Object.defineProperty(function(){},"prototype",{value:42,writable:!1}).prototype})},8727:function(t){t.exports=["constructor","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","toLocaleString","toString","valueOf"]},8745:function(t,n,r){var e=r(616),o=Function.prototype,i=o.apply,u=o.call;t.exports="object"==typeof Reflect&&Reflect.apply||(e?u.bind(i):function(){return u.apply(i,arguments)})},8773:function(t,n){var r={}.propertyIsEnumerable,e=Object.getOwnPropertyDescriptor,o=e&&!r.call({1:2},1);n.f=o?function(t){var n=e(this,t);return!!n&&n.enumerable}:r},8980:function(t,n,r){var e=r(6518),o=r(9213).findIndex,i=r(6469),u="findIndex",c=!0;u in[]&&Array(1)[u](function(){c=!1}),e({target:"Array",proto:!0,forced:c},{findIndex:function(t){return o(this,t,arguments.length>1?arguments[1]:void 0)}}),i(u)},8981:function(t,n,r){var e=r(7750),o=Object;t.exports=function(t){return o(e(t))}},9039:function(t){t.exports=function(t){try{return!!t()}catch(t){return!0}}},9167:function(t,n,r){var e=r(4576);t.exports=e},9213:function(t,n,r){var e=r(6080),o=r(9504),i=r(7055),u=r(8981),c=r(6198),a=r(1469),f=o([].push),s=function(t){var n=1===t,r=2===t,o=3===t,s=4===t,p=6===t,l=7===t,v=5===t||p;return function(h,y,m,d){for(var b,g,w=u(h),O=i(w),S=c(O),x=e(y,m),j=0,E=d||a,P=n?E(h,S):r||l?E(h,0):void 0;S>j;j++)if((v||j in O)&&(g=x(b=O[j],j,w),t))if(n)P[j]=g;else if(g)switch(t){case 3:return!0;case 5:return b;case 6:return j;case 2:f(P,b)}else switch(t){case 4:return!1;case 7:f(P,b)}return p?-1:o||s?s:P}};t.exports={forEach:s(0),map:s(1),filter:s(2),some:s(3),every:s(4),find:s(5),findIndex:s(6),filterReject:s(7)}},9296:function(t,n,r){var e=r(4055)("span").classList,o=e&&e.constructor&&e.constructor.prototype;t.exports=o===Object.prototype?void 0:o},9297:function(t,n,r){var e=r(9504),o=r(8981),i=e({}.hasOwnProperty);t.exports=Object.hasOwn||function(t,n){return i(o(t),n)}},9306:function(t,n,r){var e=r(4901),o=r(6823),i=TypeError;t.exports=function(t){if(e(t))return t;throw new i(o(t)+" is not a function")}},9432:function(t,n,r){var e=r(6518),o=r(8981),i=r(1072);e({target:"Object",stat:!0,forced:r(9039)(function(){i(1)})},{keys:function(t){return i(o(t))}})},9433:function(t,n,r){var e=r(4576),o=Object.defineProperty;t.exports=function(t,n){try{o(e,t,{value:n,configurable:!0,writable:!0})}catch(r){e[t]=n}return n}},9504:function(t,n,r){var e=r(616),o=Function.prototype,i=o.call,u=e&&o.bind.bind(i,i);t.exports=e?u:function(t){return function(){return i.apply(t,arguments)}}},9519:function(t,n,r){var e,o,i=r(4576),u=r(2839),c=i.process,a=i.Deno,f=c&&c.versions||a&&a.version,s=f&&f.v8;s&&(o=(e=s.split("."))[0]>0&&e[0]<4?1:+(e[0]+e[1])),!o&&u&&(!(e=u.match(/Edge\/(\d+)/))||e[1]>=74)&&(e=u.match(/Chrome\/(\d+)/))&&(o=+e[1]),t.exports=o},9565:function(t,n,r){var e=r(616),o=Function.prototype.call;t.exports=e?o.bind(o):function(){return o.apply(o,arguments)}},9617:function(t,n,r){var e=r(5397),o=r(5610),i=r(6198),u=function(t){return function(n,r,u){var c=e(n),a=i(c);if(0===a)return!t&&-1;var f,s=o(u,a);if(t&&r!=r){for(;a>s;)if((f=c[s++])!=f)return!0}else for(;a>s;s++)if((t||s in c)&&c[s]===r)return t||s||0;return!t&&-1}};t.exports={includes:u(!0),indexOf:u(!1)}},9773:function(t,n,r){var e=r(6518),o=r(4495),i=r(9039),u=r(3717),c=r(8981);e({target:"Object",stat:!0,forced:!o||i(function(){u.f(1)})},{getOwnPropertySymbols:function(t){var n=u.f;return n?n(c(t)):[]}})}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var i=n[e]={exports:{}};return t[e].call(i.exports,i,i.exports,r),i.exports}function e(t,n){(null==n||n>t.length)&&(n=t.length);for(var r=0,e=Array(n);r<n;r++)e[r]=t[r];return e}function o(t){return function(t){if(Array.isArray(t))return e(t)}(t)||function(t){if("undefined"!=typeof Symbol&&null!=t[Symbol.iterator]||null!=t["@@iterator"])return Array.from(t)}(t)||function(t,n){if(t){if("string"==typeof t)return e(t,n);var r={}.toString.call(t).slice(8,-1);return"Object"===r&&t.constructor&&(r=t.constructor.name),"Map"===r||"Set"===r?Array.from(t):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?e(t,n):void 0}}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function i(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}function u(t){return u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},u(t)}function c(t){var n=function(t){if("object"!=u(t)||!t)return t;var n=t[Symbol.toPrimitive];if(void 0!==n){var r=n.call(t,"string");if("object"!=u(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(t)}(t);return"symbol"==u(n)?n:n+""}function a(t,n){for(var r=0;r<n.length;r++){var e=n[r];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(t,c(e.key),e)}}function f(t,n,r){return n&&a(t.prototype,n),r&&a(t,r),Object.defineProperty(t,"prototype",{writable:!1}),t}function s(t,n,r){return(n=c(n))in t?Object.defineProperty(t,n,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[n]=r,t}function p(t,n){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var e=Object.getOwnPropertySymbols(t);n&&(e=e.filter(function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable})),r.push.apply(r,e)}return r}r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),r(2675),r(2008),r(3851),r(1278),r(9432),r(8980),r(2062),r(4554),r(6099),r(3500),r(8459);var l=function(){return f(function t(n){i(this,t),s(this,"mOptions",void 0),s(this,"mOriginalItem",void 0),s(this,"mStartPosition",void 0),s(this,"mDimensions",void 0),this.mOptions=function(t){for(var n=1;n<arguments.length;n++){var r=null!=arguments[n]?arguments[n]:{};n%2?p(Object(r),!0).forEach(function(n){s(t,n,r[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):p(Object(r)).forEach(function(n){Object.defineProperty(t,n,Object.getOwnPropertyDescriptor(r,n))})}return t}({},n),this.mOriginalItem=this.mOptions.element,this.mStartPosition={x:0,y:0},this.mDimensions={width:0,height:0},this.update()},[{key:"destroy",value:function(){}},{key:"update",value:function(){var t=this.mOriginalItem.getBoundingClientRect(),n=window.getComputedStyle(this.mOriginalItem),r=parseFloat(n.marginTop)||0,e=parseFloat(n.marginLeft)||0;this.mStartPosition.x=t.left+window.pageXOffset-e,this.mStartPosition.y=t.top+window.pageYOffset-r,this.mDimensions.width=t.width,this.mDimensions.height=t.height}},{key:"onDropped",value:function(t,n){return!!this.isInRect(t,n)}},{key:"isInRect",value:function(t,n){return t>this.mStartPosition.x&&n>this.mStartPosition.y&&t<this.mStartPosition.x+this.mDimensions.width&&n<this.mStartPosition.y+this.mDimensions.height}},{key:"width",get:function(){return this.mDimensions.width}},{key:"height",get:function(){return this.mDimensions.height}},{key:"element",get:function(){return this.mOptions.element}}])}();function v(t,n){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var e=Object.getOwnPropertySymbols(t);n&&(e=e.filter(function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable})),r.push.apply(r,e)}return r}var h=function(){return f(function t(n){i(this,t),s(this,"mOptions",void 0),s(this,"mItems",[]),s(this,"mSubscriptions",[]),s(this,"mDomObserver",void 0),this.mOptions=function(t){for(var n=1;n<arguments.length;n++){var r=null!=arguments[n]?arguments[n]:{};n%2?v(Object(r),!0).forEach(function(n){s(t,n,r[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):v(Object(r)).forEach(function(n){Object.defineProperty(t,n,Object.getOwnPropertyDescriptor(r,n))})}return t}({ondropped:function(){}},n);var r=this.parseElements(this.mOptions.droppable);this.mItems=r.map(function(t){return new l({element:t})}),this.registerListeners()},[{key:"destroy",value:function(){this.unregisterListeners(),this.mItems.splice(0).forEach(function(t){return t.destroy()})}},{key:"registerListeners",value:function(){var t=this,n=window.MutationObserver||window.WebKitMutationObserver;this.mDomObserver=new n(this.onDomChanged.bind(this)),this.mDomObserver.observe(document.body,{attributes:!1,childList:!0,subtree:!0}),document.addEventListener("resize",this.onWindowResize.bind(this));var r=window.expivi.interact();if(this.mOptions.ondragstart){var e=r.subscribe(r.DRAGGABLE_DRAGSTART,function(n){t.mOptions.ondragstart(n)});this.mSubscriptions.push({event:r.DRAGGABLE_DRAGSTART,id:e})}if(this.mOptions.ondragmove){var o=r.subscribe(r.DRAGGABLE_DRAGMOVE,function(n){t.mOptions.ondragmove(n)});this.mSubscriptions.push({event:r.DRAGGABLE_DRAGMOVE,id:o})}if(this.mOptions.ondragend){var i=r.subscribe(r.DRAGGABLE_DRAGEND,function(n){t.mOptions.ondragend(n)});this.mSubscriptions.push({event:r.DRAGGABLE_DRAGEND,id:i})}var u=r.subscribe(r.DRAGGABLE_DRAGEND,function(n){var r=n.position.x,e=n.position.y;t.mItems.forEach(function(o){o.onDropped(r,e)&&t.mOptions.ondropped({type:"droppped",originalSource:n.originalSource,event:n.event,position:{x:r,y:e},clientX:r-window.pageXOffset,clientY:e-window.pageYOffset,width:o.width,height:o.height})})});this.mSubscriptions.push({event:r.DRAGGABLE_DRAGEND,id:u})}},{key:"unregisterListeners",value:function(){this.mDomObserver&&this.mDomObserver.disconnect(),document.removeEventListener("resize",this.onWindowResize.bind(this));var t=window.expivi.interact();this.mSubscriptions.splice(0).forEach(function(n){return t.unsubscribe(n.event,n.id)})}},{key:"onWindowResize",value:function(t){this.mItems.forEach(function(t){t.update()})}},{key:"onDomChanged",value:function(t){var n=this,r=[],e=[];t.forEach(function(t){t.addedNodes.length&&r.push.apply(r,o(t.addedNodes)),t.removedNodes.length&&e.push.apply(e,o(t.removedNodes))}),e.forEach(function(t){if(t){var r=n.mItems.findIndex(function(n){return n.element==t});r>=0&&n.mItems.splice(r,1)}}),r.forEach(function(t){if(t){var r=!1;"string"==typeof n.mOptions.droppable?n.matchesQuery(t,n.mOptions.droppable)&&(r=!0):n.matchesNode(t,n.mOptions.droppable)&&(r=!0),r&&n.mItems.push(new l({element:t}))}}),this.mItems.forEach(function(t){t.update()})}},{key:"parseElements",value:function(t){if(null==t||null==t)return[];if(t&&t.nodeName)return[t];if("string"==typeof t){var n=document.querySelectorAll(t),r=[];return n.forEach(function(t){r.push(t)}),r}return Array.isArray(t)?t:[]}},{key:"matchesQuery",value:function(t,n){return t.mozMatchesSelector?t.mozMatchesSelector(n):t.msMatchesSelector?t.msMatchesSelector(n):t.oMatchesSelector?t.oMatchesSelector(n):!!t.webkitMatchesSelector&&t.webkitMatchesSelector(n)}},{key:"matchesNode",value:function(t,n){return!(!t||!n)&&t.isEqualNode(n)}}])}();if(void 0===window.expivi.interact)throw"Implement expivi-interact before dropzone.";window.expivi.interact().registerPlugin("dropzone",function(t){return new h(t)})}();