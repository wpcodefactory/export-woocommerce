/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/modules/my-plugins.js"
/*!**************************************!*\
  !*** ./src/js/modules/my-plugins.js ***!
  \**************************************/
() {

/**
 * My Plugins page: tabs, search, install and activate.
 * No dependencies. Data comes from wp_localize_script (wpfcsMyPlugins).
 */
(function () {
  'use strict';

  var root = document.querySelector('.wpfcs-my-plugins');
  if (!root || !window.wpfcsMyPlugins) {
    return;
  }
  var cfg = window.wpfcsMyPlugins;
  var t = cfg.i18n;

  // ------------------------------------------------------------------ Tabs and search.
  var tabs = Array.prototype.slice.call(root.querySelectorAll('.wpfcs-mp-tabs [role="tab"]'));
  var rows = Array.prototype.slice.call(root.querySelectorAll('.wpfcs-mp-row'));
  var list = root.querySelector('#wpfcs-mp-catalog-list');
  var search = root.querySelector('.wpfcs-mp-search input');
  var empty = root.querySelector('.wpfcs-mp-empty');
  var current = tabs.length ? tabs[0].getAttribute('data-job') : '';
  function inTab(row, job) {
    return 'top' === job ? '1' === row.getAttribute('data-top') : job === row.getAttribute('data-job');
  }

  // Words of a text, lower case: letters and digits in any language, split on everything else.
  var splitter;
  try {
    splitter = new RegExp('[^\\p{L}\\p{N}]+', 'u');
  } catch (e) {
    splitter = /[\s.,;:!?'"()\/&+\-]+/;
  }
  function words(text) {
    return String(text || '').toLowerCase().split(splitter).filter(Boolean);
  }
  var rowWords = rows.map(function (row) {
    return words(row.getAttribute('data-search'));
  });

  // Every search term must match the start of a word ("vat" finds "VAT", not "private").
  function matches(index, terms) {
    return terms.every(function (term) {
      return rowWords[index].some(function (word) {
        return 0 === word.indexOf(term);
      });
    });
  }
  function applyFilter() {
    var q = search ? words(search.value) : [];
    var visible = [];
    rows.forEach(function (row, index) {
      var show = q.length ? matches(index, q) : inTab(row, current);
      row.hidden = !show;
      row.classList.remove('is-last');
      if (show) {
        visible.push(row);
      }
    });
    if (visible.length) {
      visible[visible.length - 1].classList.add('is-last');
    }
    if (empty) {
      empty.hidden = visible.length > 0;
    }
    // While searching, results come from every tab, so no tab is selected.
    tabs.forEach(function (tab) {
      var on = !q.length && tab.getAttribute('data-job') === current;
      tab.classList.toggle('on', on);
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.tabIndex = tab.getAttribute('data-job') === current ? 0 : -1;
    });
    if (list) {
      var active = root.querySelector('#wpfcs-mp-tab-' + current);
      if (active) {
        list.setAttribute('aria-labelledby', active.id);
      }
    }
  }
  tabs.forEach(function (tab, index) {
    tab.addEventListener('click', function () {
      current = tab.getAttribute('data-job');
      if (search) {
        search.value = '';
      }
      applyFilter();
    });
    tab.addEventListener('keydown', function (e) {
      var next = null;
      if ('ArrowRight' === e.key) {
        next = tabs[(index + 1) % tabs.length];
      } else if ('ArrowLeft' === e.key) {
        next = tabs[(index - 1 + tabs.length) % tabs.length];
      } else if ('Home' === e.key) {
        next = tabs[0];
      } else if ('End' === e.key) {
        next = tabs[tabs.length - 1];
      }
      if (next) {
        e.preventDefault();
        next.focus();
        next.click();
      }
    });
  });
  if (search) {
    search.addEventListener('input', applyFilter);
  }
  if (tabs.length) {
    applyFilter();
  }

  // ------------------------------------------------------------------ Install and activate.
  function post(action, id) {
    var body = new URLSearchParams();
    body.append('action', action);
    body.append('nonce', cfg.nonce);
    body.append('id', id);
    return fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: body
    }).then(function (res) {
      return res.json().catch(function () {
        return {
          success: false,
          data: {}
        };
      });
    });
  }
  function showError(button, data) {
    var box = button.closest('[data-id]');
    var el = box ? box.querySelector('.wpfcs-mp-error') : null;
    if (!el) {
      return;
    }
    el.textContent = data && data.message ? data.message + ' ' : t.error + ' ';
    if (data && data.wporg_url) {
      var a = document.createElement('a');
      a.href = data.wporg_url;
      a.target = '_blank';
      a.rel = 'noopener noreferrer';
      a.textContent = t.wporg;
      var sr = document.createElement('span');
      sr.className = 'screen-reader-text';
      sr.textContent = ' ' + t.newTab;
      a.appendChild(sr);
      el.appendChild(a);
    }
    el.hidden = false;
  }
  function clearError(button) {
    var box = button.closest('[data-id]');
    var el = box ? box.querySelector('.wpfcs-mp-error') : null;
    if (el) {
      el.hidden = true;
      el.textContent = '';
    }
  }
  function setBusy(button, label) {
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.textContent = label;
  }
  function setIdle(button, label) {
    button.disabled = false;
    button.removeAttribute('aria-busy');
    button.textContent = label;
  }

  // In the installed list, the chip follows the version that is now active.
  function updateChip(button, data) {
    var row = button.closest('.wpfcs-mp-owned-row');
    var chip = row ? row.querySelector('.wpfcs-mp-chip') : null;
    if (!chip || !data || !data.edition) {
      return;
    }
    var pro = 'pro' === data.edition;
    chip.classList.remove('wpfcs-ui-chip-ok', 'wpfcs-ui-chip-neutral');
    chip.classList.add(pro ? 'wpfcs-ui-chip-ok' : 'wpfcs-ui-chip-neutral');
    chip.textContent = pro ? t.pro : t.free;
  }
  function activated(button, data) {
    updateChip(button, data);
    var done = document.createElement('span');
    done.className = 'wpfcs-mp-done';
    done.setAttribute('role', 'status');
    done.textContent = t.activated;
    var wrap = document.createElement('span');
    wrap.className = 'wpfcs-mp-actions';
    wrap.appendChild(done);
    // Settings only when the plugin's own Settings link is known; otherwise it shows after a reload.
    var link = null;
    if (data && data.settings_url) {
      link = document.createElement('a');
      link.className = 'wpfcs-ui-a';
      link.href = data.settings_url;
      link.textContent = t.settings;
      wrap.appendChild(link);
    }
    button.replaceWith(wrap);
    if (link) {
      link.focus();
    } else {
      done.setAttribute('tabindex', '-1');
      done.focus();
    }
  }
  function activate(button) {
    var id = button.getAttribute('data-id');
    clearError(button);
    setBusy(button, t.activating);
    post('wpfcs_my_plugins_activate', id).then(function (res) {
      if (res && res.success) {
        activated(button, res.data);
      } else {
        setIdle(button, t.activate);
        showError(button, res && res.data);
      }
    }).catch(function () {
      setIdle(button, t.activate);
      showError(button, null);
    });
  }
  function install(button) {
    var id = button.getAttribute('data-id');
    var label = button.textContent;
    clearError(button);
    setBusy(button, t.installing);
    post('wpfcs_my_plugins_install', id).then(function (res) {
      if (res && res.success) {
        button.classList.remove('wpfcs-mp-install');
        button.classList.add('wpfcs-mp-activate');
        setIdle(button, t.activate);
        button.focus();
      } else {
        setIdle(button, label);
        showError(button, res && res.data);
      }
    }).catch(function () {
      setIdle(button, label);
      showError(button, null);
    });
  }
  root.addEventListener('click', function (e) {
    var button = e.target.closest('button.wpfcs-mp-install, button.wpfcs-mp-activate');
    if (!button || button.disabled) {
      return;
    }
    if (button.classList.contains('wpfcs-mp-install')) {
      install(button);
    } else {
      activate(button);
    }
  });
})();

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			const e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		Object.defineProperties(exports, { [Symbol.toStringTag]: { value: 'Module' }, __esModule: { value: true } });
/******/ 	};
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
let __webpack_exports__ = {};
/*!*************************!*\
  !*** ./src/js/admin.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_my_plugins__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/my-plugins */ "./src/js/modules/my-plugins.js");
/**
 * WPFactory Cross-Selling - Admin.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 */


})();

// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*****************************!*\
  !*** ./src/scss/admin.scss ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin

})();

/******/ })()
;
//# sourceMappingURL=admin.js.map