/* Practical 6 - reusable search + filter + sort + pagination engine
 *
 * createListView({
 *   items, listEl, paginationEl, countEl, searchEl, searchFields,
 *   filters: [{ el: <select>, field: "category", label: (v) => text }],  // options are built from the data
 *   sortEl, sorts: { key: compareFn }, resetEl, pageSize, renderItem, emptyMessage
 * })
 */
import { debounce, unique, escapeHTML } from "./utils.js";

export function createListView(config) {
  const items = config.items;
  const listEl = config.listEl;
  const paginationEl = config.paginationEl;
  const countEl = config.countEl;
  const searchEl = config.searchEl;
  const searchFields = config.searchFields || [];
  const filters = config.filters || [];
  const sortEl = config.sortEl;
  const sorts = config.sorts || {};
  const resetEl = config.resetEl;
  const pageSize = config.pageSize || 6;
  const renderItem = config.renderItem;
  const emptyMessage = config.emptyMessage || "No matching records found.";

  const state = { query: "", filters: {}, sort: sortEl ? sortEl.value : "", page: 1 };

  // Fill each filter dropdown with the unique values found in the data
  filters.forEach(function (f) {
    unique(items.map(function (item) { return item[f.field]; })).forEach(function (value) {
      const text = f.label ? f.label(value) : value;
      f.el.insertAdjacentHTML("beforeend",
        '<option value="' + escapeHTML(value) + '">' + escapeHTML(text) + "</option>");
    });
    state.filters[f.field] = "";
  });

  // search -> filter -> sort
  function getProcessedItems() {
    const q = state.query.trim().toLowerCase();

    let result = items.filter(function (item) {
      const matchesSearch = !q || searchFields.some(function (field) {
        return String(item[field]).toLowerCase().includes(q);
      });
      const matchesFilters = filters.every(function (f) {
        return !state.filters[f.field] || String(item[f.field]) === state.filters[f.field];
      });
      return matchesSearch && matchesFilters;
    });

    if (sorts[state.sort]) result = result.slice().sort(sorts[state.sort]);
    return result;
  }

  function render() {
    const processed = getProcessedItems();
    const totalPages = Math.max(1, Math.ceil(processed.length / pageSize));
    if (state.page > totalPages) state.page = totalPages;

    const start = (state.page - 1) * pageSize;
    const pageItems = processed.slice(start, start + pageSize);

    listEl.innerHTML = pageItems.length
      ? pageItems.map(renderItem).join("")
      : '<p class="empty-state">' + escapeHTML(emptyMessage) + "</p>";

    countEl.textContent = processed.length
      ? "Showing " + (start + 1) + "-" + (start + pageItems.length) + " of " + processed.length
      : "Showing 0 results";

    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    if (totalPages <= 1) { paginationEl.innerHTML = ""; return; }

    let html = '<button type="button" data-page="' + (state.page - 1) + '"' + (state.page === 1 ? " disabled" : "") + ">Prev</button>";
    for (let p = 1; p <= totalPages; p++) {
      html += '<button type="button" data-page="' + p + '" aria-label="Page ' + p + '"' +
        (p === state.page ? ' class="active" aria-current="page"' : "") + ">" + p + "</button>";
    }
    html += '<button type="button" data-page="' + (state.page + 1) + '"' + (state.page === totalPages ? " disabled" : "") + ">Next</button>";
    paginationEl.innerHTML = html;
  }

  // ---- events ----
  paginationEl.addEventListener("click", function (e) {
    const btn = e.target.closest("button[data-page]");
    if (!btn || btn.disabled) return;
    state.page = Number(btn.dataset.page);
    render();
  });

  searchEl.addEventListener("input", debounce(function () {
    state.query = searchEl.value;
    state.page = 1;
    render();
  }, 250));

  filters.forEach(function (f) {
    f.el.addEventListener("change", function () {
      state.filters[f.field] = f.el.value;
      state.page = 1;
      render();
    });
  });

  if (sortEl) {
    sortEl.addEventListener("change", function () {
      state.sort = sortEl.value;
      state.page = 1;
      render();
    });
  }

  if (resetEl) {
    resetEl.addEventListener("click", function () {
      searchEl.value = "";
      state.query = "";
      filters.forEach(function (f) { f.el.value = ""; state.filters[f.field] = ""; });
      if (sortEl) { sortEl.selectedIndex = 0; state.sort = sortEl.value; }
      state.page = 1;
      render();
    });
  }

  render();
}