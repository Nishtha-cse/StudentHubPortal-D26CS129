/* Practical 6 - shared helpers (used by events.js, faqs.js, students.js) */

export function escapeHTML(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

export function debounce(fn, delay) {
  var timer;
  return function () {
    var args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () { fn.apply(null, args); }, delay || 300);
  };
}

export function formatDate(isoDate) {
  return new Date(isoDate).toLocaleDateString("en-IN", {
    day: "2-digit", month: "short", year: "numeric"
  });
}

export function unique(values) {
  return Array.from(new Set(values)).sort(function (a, b) {
    return String(a).localeCompare(String(b), undefined, { numeric: true });
  });
}

/* ---- loading / error / info messages ---- */
export function showLoading(el, message) {
  el.hidden = false;
  el.className = "status status-loading";
  el.setAttribute("role", "status");
  el.innerHTML = '<span class="spinner" aria-hidden="true"></span> ' + escapeHTML(message || "Loading...");
}

export function showError(el, message, onRetry) {
  el.hidden = false;
  el.className = "status status-error";
  el.setAttribute("role", "alert");
  el.innerHTML = "<span>" + escapeHTML(message) + '</span> <button type="button" class="btn-retry">Retry</button>';
  if (onRetry) el.querySelector(".btn-retry").addEventListener("click", onRetry);
}

export function showInfo(el, message) {
  el.hidden = false;
  el.className = "status status-info";
  el.setAttribute("role", "status");
  el.textContent = message;
}

export function hideStatus(el) {
  el.hidden = true;
  el.innerHTML = "";
}