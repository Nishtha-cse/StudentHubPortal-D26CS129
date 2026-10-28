/* Practical 6 - FAQ page (faqs.html). Uses the existing accordion markup/CSS from Practical 4. */
import { loadData } from "./dataService.js";
import { createListView } from "./listView.js";
import { escapeHTML, showLoading, showError, showInfo, hideStatus } from "./utils.js";

const statusEl = document.getElementById("status");

function renderFaq(faq) {
  const id = "faq-" + faq.id;
  return (
    '<div class="accordion-item">' +
      "<h2>" +
        '<button type="button" class="accordion-trigger" id="' + id + '-trigger" aria-expanded="false" aria-controls="' + id + '-panel">' +
          "<span>" + escapeHTML(faq.question) + ' <span class="badge">' + escapeHTML(faq.category) + "</span></span>" +
        "</button>" +
      "</h2>" +
      '<div class="accordion-panel" id="' + id + '-panel" role="region" aria-labelledby="' + id + '-trigger" hidden>' +
        "<p>" + escapeHTML(faq.answer) + "</p>" +
      "</div>" +
    "</div>"
  );
}

async function init() {
  showLoading(statusEl, "Loading FAQs...");
  try {
    const result = await loadData("../json/faqs.json", "studenthub-faqs");

    if (result.fromCache) showInfo(statusEl, "Could not reach the server - showing the last saved FAQs.");
    else hideStatus(statusEl);

    createListView({
      items: result.data,
      listEl: document.getElementById("list"),
      paginationEl: document.getElementById("pagination"),
      countEl: document.getElementById("resultCount"),
      searchEl: document.getElementById("searchInput"),
      searchFields: ["question", "answer", "category"],
      filters: [{ el: document.getElementById("categoryFilter"), field: "category" }],
      sortEl: document.getElementById("sortSelect"),
      sorts: {
        "default":      function (a, b) { return a.id - b.id; },
        "question-asc": function (a, b) { return a.question.localeCompare(b.question); },
        "category-asc": function (a, b) { return a.category.localeCompare(b.category) || a.id - b.id; }
      },
      resetEl: document.getElementById("resetBtn"),
      pageSize: 8,
      renderItem: renderFaq,
      emptyMessage: "No FAQs match your search."
    });
  } catch (error) {
    showError(statusEl, "Could not load FAQs. " + error.message, init);
  }
}

init();