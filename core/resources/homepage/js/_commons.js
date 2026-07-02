/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * StatusFetcher - Unified utility for fetching and displaying service status
 */
class StatusFetcher {
  constructor(serviceName, fields = ['checkport', 'versions'], options = {}) {
    this.serviceName = serviceName;
    this.fields = this.normalizeFields(fields);
    this.options = Object.assign({
      interval: 10000,
      proc: serviceName
    }, options);
    this.timer = null;
  }

  normalizeFields(fields) {
    return fields.map(field => {
      if (typeof field === 'string') {
        return { data: field, selector: field };
      }
      return field;
    });
  }

  init() {
    this.fetchStatus();
    if (this.options.interval > 0) {
      this.timer = setInterval(() => this.fetchStatus(), this.options.interval);
    }
  }

  async fetchStatus() {
    const senddata = new URLSearchParams();
    senddata.append('proc', this.options.proc);
    
    let url = AJAX_URL;

    try {
      const response = await fetchWithCsrf(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: senddata
      });

      if (!response.ok) throw new Error('Network response was not ok');
      const data = await response.json();
      
      if (this.options.customUpdater) {
        this.options.customUpdater(data);
      } else {
        this.updateDOM(data);
      }
    } catch (error) {
      // Don't log abort errors (common during page navigation/reload)
      if (error.name === 'AbortError' || (error instanceof TypeError && error.message.includes('NetworkError'))) {
         return;
      }
      console.error(`[${this.serviceName}] Fetch error:`, error);
      this.showErrorFeedback();
    }
  }

  showErrorFeedback() {
    const selector = `.summary-${this.serviceName}`;
    const element = document.querySelector(selector) || document.getElementById(this.serviceName);
    if (element) {
      // 1. Check if the element itself IS the designated container
      const isContentEl = element.classList.contains('status-content');
      
      // 2. Prioritize .status-content for feedback, fallback to .loader, then element
      const contentEl = isContentEl ? element : (element.querySelector('.status-content') || element.querySelector('.loader') || element);
      
      const loaders = contentEl.querySelectorAll('.loader');
      if (loaders.length > 0) {
        loaders.forEach(loader => {
          loader.classList.remove('fa-spin');
          loader.style.filter = 'invert(16%) sepia(89%) saturate(6144%) hue-rotate(357deg) brightness(97%) contrast(113%)';
          const img = loader.querySelector('img');
          if (img) {
            img.style.filter = 'grayscale(100%) brightness(50%) sepia(100%) hue-rotate(-50deg) saturate(600%)';
          }
        });
      } else if (!contentEl.innerText.trim()) {
        // If no loader and content is empty or only whitespace, show error icon
        contentEl.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i></span>';
      }
    }
  }

  updateDOM(data) {
    if (!data) return;
    this.fields.forEach(field => {
      const selector = `.${this.serviceName}-${field.selector}`;
      const elements = document.querySelectorAll(selector);

      elements.forEach(element => {
        if (data[field.data] === undefined || data[field.data] === null) return;
        
        const content = data[field.data];
        
        // 1. Check if the element itself IS the designated container
        const isContentEl = element.classList.contains('status-content');
        
        // 2. Try to find a specifically designated container for dynamic content
        const contentEl = isContentEl ? element : element.querySelector('.status-content');

        // 3. Try to find a loader that should be replaced
        const loader = element.querySelector('.loader');

        if (loader) {
          // Replace the loader specifically to preserve sibling labels/icons
          loader.outerHTML = content;
        } else if (contentEl) {
          // If we have a .status-content container (or the element itself is one), update it safely
          if (contentEl.innerHTML !== content) {
             contentEl.innerHTML = content;
          }
        } else {
          // Standard behavior: replace content of the target container
          // BUT only if it doesn't look like it contains important labels
          if (typeof content === 'string' && content.includes('<')) {
            // Only update if content is different to avoid flickering
            if (element.innerHTML !== content) {
              // If this element contains a link or a label, we MUST NOT overwrite it
              if (element.querySelector('a') || element.querySelector('i') || element.querySelector('img') || element.innerText.trim().length > 15) {
                 console.warn(`[${this.serviceName}] Refusing to overwrite element with HTML content to avoid losing labels`, element);
              } else {
                element.innerHTML = content;
              }
            }
          } else {
            if (element.innerText !== String(content)) {
              element.innerText = content;
            }
          }
        }
      });
    });
  }

  initOnReady() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.init());
    } else {
      this.init();
    }
  }
}

function createStatusFetcher(serviceName, fields, options = {}) {
  const fetcher = new StatusFetcher(serviceName, fields, options);
  fetcher.initOnReady();
  return fetcher;
}
