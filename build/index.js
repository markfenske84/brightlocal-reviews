/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/brightlocal.png":
/*!********************************!*\
  !*** ./assets/brightlocal.png ***!
  \********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = __webpack_require__.p + "images/brightlocal.ce7c0e6a.png";

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _assets_brightlocal_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../assets/brightlocal.png */ "./assets/brightlocal.png");











// ADD_BRIGHTLOCAL_ICON_START
// BrightLocal brand icon (PNG)

const BrightlocalIcon = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
  src: _assets_brightlocal_png__WEBPACK_IMPORTED_MODULE_10__,
  alt: "BrightLocal",
  style: {
    width: 24,
    height: 24
  }
});
// ADD_BRIGHTLOCAL_ICON_END

// Source icons mapping
const sourceIcons = {
  google: 'https://www.google.com/favicon.ico',
  facebook: 'https://www.facebook.com/favicon.ico',
  yelp: 'https://www.yelp.com/favicon.ico',
  tripadvisor: 'https://www.tripadvisor.com/favicon.ico',
  brightlocal: 'https://www.brightlocal.com/favicon.ico',
  yahoo: 'https://www.yahoo.com/favicon.ico',
  default: 'https://www.brightlocal.com/favicon.ico'
};
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__.registerBlockType)('brightlocal-reviews/reviews', {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('BrightLocal Reviews', 'brightlocal-reviews'),
  icon: BrightlocalIcon,
  category: 'widgets',
  attributes: {
    displayType: {
      type: 'string',
      default: 'grid'
    },
    showAuthor: {
      type: 'boolean',
      default: true
    },
    showDate: {
      type: 'boolean',
      default: true
    },
    showSource: {
      type: 'boolean',
      default: true
    },
    reviewLabel: {
      type: 'string',
      default: 'all'
    },
    limitItems: {
      type: 'boolean',
      default: false
    },
    itemsPerPage: {
      type: 'number',
      default: 3
    },
    showArrows: {
      type: 'boolean',
      default: true
    }
  },
  edit: function (props) {
    const {
      attributes,
      setAttributes
    } = props;
    const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.useBlockProps)();

    // Local state for editor pagination mimic
    const [editorReviews, setEditorReviews] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_9__.useState)([]);
    const [currentPage, setCurrentPage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_9__.useState)(1);
    const [hasMore, setHasMore] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_9__.useState)(true);

    // Fetch available review labels
    const labels = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_6__.useSelect)(select => {
      return select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_7__.store).getEntityRecords('taxonomy', 'bl_review_label', {
        per_page: -1,
        _fields: ['id', 'name', 'slug']
      });
    }, []);
    const reviews = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_6__.useSelect)(select => {
      const {
        getEntityRecords
      } = select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_7__.store);

      // Prepare base query
      const query = {
        post_type: 'bl-reviews',
        per_page: attributes.limitItems ? attributes.itemsPerPage : 100,
        _embed: true,
        context: 'edit',
        _fields: ['id', 'title', 'content', 'date', 'meta']
      };

      // Convert selected label slug to term ID (REST API expects IDs)
      if (attributes.reviewLabel && attributes.reviewLabel !== 'all') {
        const allLabels = getEntityRecords('taxonomy', 'bl_review_label', {
          per_page: -1,
          _fields: ['id', 'slug']
        });
        if (allLabels) {
          const matched = allLabels.find(l => l.slug === attributes.reviewLabel);
          if (matched) {
            query['bl_review_label'] = matched.id; // Pass ID, not slug
          }
        }
      }
      return getEntityRecords('postType', 'bl-reviews', query);
    }, [attributes.reviewLabel, attributes.limitItems, attributes.itemsPerPage]);
    const isLoading = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_6__.useSelect)(select => {
      return select(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_7__.store).isResolving('postType', 'bl-reviews');
    }, []);
    if (isLoading) {
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        ...blockProps
      }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Loading reviews...', 'brightlocal-reviews'));
    }

    // Sync initial fetched reviews into local state whenever source reviews change
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_9__.useEffect)(() => {
      setEditorReviews(reviews || []);
      setCurrentPage(1);
      setHasMore(true);
    }, [reviews, attributes.limitItems, attributes.itemsPerPage]);

    // Prepare label options for the dropdown
    const labelOptions = [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('All Reviews', 'brightlocal-reviews'),
      value: 'all'
    }];
    if (labels) {
      labels.forEach(label => {
        labelOptions.push({
          label: label.name,
          value: label.slug
        });
      });
    }
    const loadMoreReviews = () => {
      // Build REST query for next page
      const nextPage = currentPage + 1;
      const perPage = attributes.itemsPerPage;
      let path = `/wp/v2/bl-reviews?per_page=${perPage}&page=${nextPage}&context=edit&_embed=true`;

      // Handle label filter
      if (attributes.reviewLabel && attributes.reviewLabel !== 'all') {
        path += `&bl_review_label=${encodeURIComponent(attributes.reviewLabel)}`;
      }
      _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default()({
        path
      }).then(moreReviews => {
        if (moreReviews && moreReviews.length > 0) {
          setEditorReviews([...editorReviews, ...moreReviews]);
          setCurrentPage(nextPage);
          if (moreReviews.length < perPage) {
            setHasMore(false);
          }
        } else {
          setHasMore(false);
        }
      }).catch(() => {
        setHasMore(false);
      });
    };
    const wrapperClass = `bl-reviews-wrapper bl-reviews-${attributes.displayType} ${attributes.displayType === 'carousel' ? 'bl-editor-preview' : ''}`;
    return [(0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Display Settings', 'brightlocal-reviews')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SelectControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Review Group', 'brightlocal-reviews'),
      value: attributes.reviewLabel,
      options: labelOptions,
      onChange: value => setAttributes({
        reviewLabel: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SelectControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Display Type', 'brightlocal-reviews'),
      value: attributes.displayType,
      options: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Grid', 'brightlocal-reviews'),
        value: 'grid'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('List', 'brightlocal-reviews'),
        value: 'list'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Carousel', 'brightlocal-reviews'),
        value: 'carousel'
      }],
      onChange: value => setAttributes({
        displayType: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Show Author', 'brightlocal-reviews'),
      checked: attributes.showAuthor,
      onChange: value => setAttributes({
        showAuthor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Show Date', 'brightlocal-reviews'),
      checked: attributes.showDate,
      onChange: value => setAttributes({
        showDate: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Show Source', 'brightlocal-reviews'),
      checked: attributes.showSource,
      onChange: value => setAttributes({
        showSource: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Limit Reviews', 'brightlocal-reviews'),
      checked: attributes.limitItems,
      onChange: value => setAttributes({
        limitItems: value
      })
    }), attributes.displayType === 'carousel' && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Show Navigation Arrows', 'brightlocal-reviews'),
      checked: attributes.showArrows,
      onChange: value => setAttributes({
        showArrows: value
      })
    }), attributes.limitItems && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Reviews per page', 'brightlocal-reviews'),
      value: attributes.itemsPerPage,
      min: 3,
      max: 30,
      step: 1,
      onChange: value => setAttributes({
        itemsPerPage: value
      })
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ...blockProps
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: wrapperClass
    }, (attributes.limitItems ? editorReviews : reviews) && (attributes.limitItems ? editorReviews : reviews).length > 0 ? (attributes.limitItems ? editorReviews : reviews).map((review, idx) => {
      const rating = review.meta?._bl_rating || 0;
      const source = review.meta?._bl_source || '';
      const reviewTitle = review.meta?._bl_title || '';
      const reviewDate = review.meta?._bl_date || review.date;
      const sourceLower = source.toLowerCase();
      const sourceIcon = sourceIcons[sourceLower] || sourceIcons.default;
      const contentLength = review.content.rendered.replace(/<[^>]*>/g, '').length;
      const needsReadMore = contentLength > 200;
      const reviewItemClass = `bl-review-item ${attributes.displayType === 'carousel' && idx === 0 ? 'active' : ''}`;
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        key: review.id,
        className: reviewItemClass
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "bl-review-header"
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "bl-review-rating"
      }, [...Array(5)].map((_, i) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
        key: i,
        className: `star ${i < rating ? 'filled' : ''}`
      }, "\u2605"))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "bl-review-meta-right"
      }, attributes.showDate && reviewDate && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
        className: "bl-review-date"
      }, new Date(reviewDate).toLocaleDateString()), attributes.showSource && source && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
        className: "bl-review-source"
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
        src: sourceIcon,
        alt: source
      }), source.charAt(0).toUpperCase() + source.slice(1)))), attributes.showAuthor && review.title.rendered && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "bl-review-author"
      }, review.title.rendered), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: `bl-review-content ${needsReadMore ? 'collapsed' : ''}`,
        dangerouslySetInnerHTML: {
          __html: review.content.rendered
        }
      }), needsReadMore && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "bl-review-read-more"
      }, "Read More"));
    }) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('No reviews found.', 'brightlocal-reviews'))), attributes.displayType !== 'carousel' && attributes.limitItems && hasMore && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      type: "button",
      className: "bl-reviews-load-more-editor",
      onClick: loadMoreReviews
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Load More', 'brightlocal-reviews')))];
  },
  save: function () {
    return null; // Dynamic block, render handled by PHP
  }
});

/***/ }),

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		var scriptUrl;
/******/ 		if (__webpack_require__.g.importScripts) scriptUrl = __webpack_require__.g.location + "";
/******/ 		var document = __webpack_require__.g.document;
/******/ 		if (!scriptUrl && document) {
/******/ 			if (document.currentScript && document.currentScript.tagName.toUpperCase() === 'SCRIPT')
/******/ 				scriptUrl = document.currentScript.src;
/******/ 			if (!scriptUrl) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				if(scripts.length) {
/******/ 					var i = scripts.length - 1;
/******/ 					while (i > -1 && (!scriptUrl || !/^http(s?):/.test(scriptUrl))) scriptUrl = scripts[i--].src;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 		// When supporting browsers where an automatic publicPath is not supported you must specify an output.publicPath manually via configuration
/******/ 		// or pass an empty string ("") and set the __webpack_public_path__ variable from your code to use your own logic.
/******/ 		if (!scriptUrl) throw new Error("Automatic publicPath is not supported in this browser");
/******/ 		scriptUrl = scriptUrl.replace(/^blob:/, "").replace(/#.*$/, "").replace(/\?.*$/, "").replace(/\/[^\/]+$/, "/");
/******/ 		__webpack_require__.p = scriptUrl;
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkbrightlocal_reviews"] = globalThis["webpackChunkbrightlocal_reviews"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], () => (__webpack_require__("./src/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map