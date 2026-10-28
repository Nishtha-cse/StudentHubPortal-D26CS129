/* Practical 6 - Student directory (used on dashboard.html and admin.html) */
import { loadData } from "./dataService.js";
import { createListView } from "./listView.js";
import { escapeHTML, showLoading, showError, showInfo, hideStatus } from "./utils.js";

const statusEl = document.getElementById("status");

function renderStudent(s) {
  return (
    "<article>" +
      "<h3>" + escapeHTML(s.name) + "</h3>" +
      '<p class="meta">' + escapeHTML(s.enrollment) + " &middot; " + escapeHTML(s.course) + " &middot; Year " + s.year + "</p>" +
      '<p class="meta">' + escapeHTML(s.email) + "</p>" +
      "<p>City: " + escapeHTML(s.city) + " &middot; CGPA: " + s.cgpa.toFixed(2) + "</p>" +
    "</article>"
  );
}

async function init() {
  showLoading(statusEl, "Loading students...");
  try {
    const result = await loadData("../json/studentssss.json", "studenthub-studentssss");

    if (result.fromCache) showInfo(statusEl, "Could not reach the server - showing the last saved students.");
    else hideStatus(statusEl);

    createListView({
      items: result.data,
      listEl: document.getElementById("list"),
      paginationEl: document.getElementById("pagination"),
      countEl: document.getElementById("resultCount"),
      searchEl: document.getElementById("searchInput"),
      searchFields: ["name", "email", "enrollment", "city"],
      filters: [
        { el: document.getElementById("courseFilter"), field: "course" },
        { el: document.getElementById("yearFilter"), field: "year", label: function (v) { return "Year " + v; } }
      ],
      sortEl: document.getElementById("sortSelect"),
      sorts: {
        "name-asc":  function (a, b) { return a.name.localeCompare(b.name); },
        "name-desc": function (a, b) { return b.name.localeCompare(a.name); },
        "cgpa-desc": function (a, b) { return b.cgpa - a.cgpa; },
        "year-asc":  function (a, b) { return a.year - b.year || a.name.localeCompare(b.name); }
      },
      resetEl: document.getElementById("resetBtn"),
      pageSize: 6,
      renderItem: renderStudent,
      emptyMessage: "No students match your search."
    });
  } catch (error) {
    showError(statusEl, "Could not load students. " + error.message, init);
  }
}

init();