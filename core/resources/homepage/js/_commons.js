/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

const AJAX_URL = "1fd5bfc5c72323f1d019208088a6de21/ajax.php"

/**
 * StatusFetcher - Unified utility for fetching and displaying service status
 *
 * This class eliminates code duplication across service-specific JavaScript files
 * by providing a common interface for AJAX status fetching and DOM updates.
 */
class StatusFetcher {
  /**
   * Create a StatusFetcher instance
   *
   * @param {string} serviceName - The service name (e.g., 'mysql', 'apache', 'php')
   * @param {Array<string|Object>} fields - Array of field names or field mapping objects
   *   - String format: 'checkport' (uses same name for data key and selector)
   *   - Object format: { data: 'versions', selector: 'version-list' }
   * @param {Object} options - Optional configuration
   * @param {Function} options.errorHandler - Custom error handler for specific services
   * @param {Function} options.responseValidator - Custom response validator
   * @param {Function} options.customUpdater - Custom DOM updater function
   */
  constructor(serviceName, fields = ['checkport', 'versions'], options = {}) {
    this.serviceName = serviceName;
    this.fields = this.normalizeFields(fields);
    this.options = options;
  }

  /**
   * Normalize field definitions to consistent format
   *
   * @param {Array<string|Object>} fields - Field definitions
   * @returns {Array<Object>} Normalized field objects
   */
  normalizeFields(fields) {
    return fields.map(field => {
      if (typeof field === 'string') {
        return { data: field, selector: field };
      }
      return field;
    });
  }

  /**
   * Fetch status from the server
   *
   * @returns {Promise<void>}
   */
  async fetchStatus() {
    const senddata = new URLSearchParams();
    senddata.append('proc', this.serviceName);

    try {
      const response = await fetch(AJAX_URL, {
        method: 'POST',
        body: senddata
      });

      if (!response.ok) {
        console.log(`Error receiving from ajax.php for ${this.serviceName}`);
        return;
      }

      const responseText = await response.text();

      // Custom response validation (e.g., for MySQL mysqli_sql_exception)
      if (this.options.responseValidator) {
        const validationResult = this.options.responseValidator(responseText);
        if (!validationResult.valid) {
          console.log(validationResult.message || `Validation failed for ${this.serviceName}`);
          return;
        }
      }

      const data = JSON.parse(responseText);

      // Use custom updater if provided, otherwise use default
      if (this.options.customUpdater) {
        this.options.customUpdater(data);
      } else {
        this.updateDOM(data);
      }
    } catch (error) {
      console.error(`Failed to parse response for ${this.serviceName}:`, error);
      if (this.options.errorHandler) {
        this.options.errorHandler(error);
      }
    }
  }

  /**
   * Update DOM elements with fetched data
   *
   * @param {Object} data - The parsed JSON data from the server
   */
  updateDOM(data) {
    this.fields.forEach(field => {
      const selector = `.${this.serviceName}-${field.selector}`;
      const element = document.querySelector(selector);

      if (element) {
        const loader = element.querySelector('.loader');
        if (loader) {
          loader.remove();
        }

        if (data[field.data] !== undefined) {
          element.insertAdjacentHTML('beforeend', data[field.data]);
        }
      } else {
        console.warn(`Element not found: ${selector}`);
      }
    });
  }

  /**
   * Initialize status fetching when DOM is ready
   *
   * @param {string} elementId - The element ID to check for (defaults to serviceName)
   * @returns {void}
   */
  initOnReady(elementId = null) {
    const checkId = elementId || this.serviceName;

    document.addEventListener("DOMContentLoaded", () => {
      if (document.getElementById(checkId)) {
        this.fetchStatus();
      }
    });
  }
}

/**
 * Helper function to create and initialize a StatusFetcher
 *
 * @param {string} serviceName - The service name
 * @param {Array<string|Object>} fields - Array of field names or mapping objects
 * @param {Object} options - Optional configuration
 * @returns {StatusFetcher} The created StatusFetcher instance
 */
function createStatusFetcher(serviceName, fields, options = {}) {
  const fetcher = new StatusFetcher(serviceName, fields, options);
  fetcher.initOnReady();
  return fetcher;
}
