/**
 * Vitest / jsdom global setup
 *
 * jsdom is configured as the test environment in vitest.config.js, so
 * `document`, `window`, and other browser globals are already available
 * in every test file.  This setup file handles any additional globals or
 * polyfills that jsdom does not provide out of the box.
 */

// Ensure structuredClone is available (Node 17+ / jsdom may need it)
if (typeof globalThis.structuredClone === 'undefined') {
  globalThis.structuredClone = (obj) => JSON.parse(JSON.stringify(obj));
}

// Stub window.matchMedia — jsdom does not implement it
if (typeof window !== 'undefined' && typeof window.matchMedia === 'undefined') {
  window.matchMedia = (query) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: () => {},
    removeListener: () => {},
    addEventListener: () => {},
    removeEventListener: () => {},
    dispatchEvent: () => false,
  });
}

// Stub window.ResizeObserver — used by some UI helpers
if (typeof window !== 'undefined' && typeof window.ResizeObserver === 'undefined') {
  window.ResizeObserver = class ResizeObserver {
    observe() {}
    unobserve() {}
    disconnect() {}
  };
}
