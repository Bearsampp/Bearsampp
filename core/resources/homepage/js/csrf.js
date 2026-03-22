/*
 * Copyright (c) 2021-2025 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * CSRF Token Helper
 *
 * Provides utilities for including CSRF tokens in AJAX requests.
 * This helps protect against Cross-Site Request Forgery attacks.
 */

/**
 * Gets the CSRF token from the meta tag
 *
 * @returns {string|null} The CSRF token or null if not found
 */
function getCsrfToken() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    return csrfMeta ? csrfMeta.getAttribute('content') : null;
}

/**
 * Adds CSRF token to URLSearchParams
 *
 * @param {URLSearchParams} params - The URLSearchParams object to add the token to
 * @returns {URLSearchParams} The params object with token added
 */
function addCsrfToken(params) {
    const token = getCsrfToken();
    if (token) {
        params.append('csrf_token', token);
    }
    return params;
}

/**
 * Adds CSRF token to FormData
 *
 * @param {FormData} formData - The FormData object to add the token to
 * @returns {FormData} The formData object with token added
 */
function addCsrfTokenToFormData(formData) {
    const token = getCsrfToken();
    if (token) {
        formData.append('csrf_token', token);
    }
    return formData;
}

/**
 * Creates fetch options with CSRF token in headers
 *
 * @param {Object} options - Base fetch options
 * @returns {Object} Options with CSRF token header added
 */
function addCsrfHeader(options = {}) {
    const token = getCsrfToken();
    if (token) {
        options.headers = options.headers || {};
        options.headers['X-CSRF-Token'] = token;
    }
    return options;
}

/**
 * Wrapper for fetch that automatically includes CSRF token
 *
 * @param {string} url - The URL to fetch
 * @param {Object} options - Fetch options
 * @returns {Promise} The fetch promise
 */
async function fetchWithCsrf(url, options = {}) {
    // If body is URLSearchParams, add token to it
    if (options.body instanceof URLSearchParams) {
        addCsrfToken(options.body);
    }
    // If body is FormData, add token to it
    else if (options.body instanceof FormData) {
        addCsrfTokenToFormData(options.body);
    }
    // Otherwise add as header
    else {
        addCsrfHeader(options);
    }

    return fetch(url, options);
}

// Make functions available globally
window.getCsrfToken = getCsrfToken;
window.addCsrfToken = addCsrfToken;
window.addCsrfTokenToFormData = addCsrfTokenToFormData;
window.addCsrfHeader = addCsrfHeader;
window.fetchWithCsrf = fetchWithCsrf;
