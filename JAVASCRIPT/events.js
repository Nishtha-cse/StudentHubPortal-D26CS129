/* Practical 6 - Events page (event.html) */
import { loadData } from "./dataService.js";
import { createListView } from "./listView.js";
import { escapeHTML, formatDate, showLoading, showError, showInfo, hideStatus } from "./utils.js";

const statusEl = document.getElementById("status");

function renderEvent(ev) {
  const badge = ev.status === "Open" ? "badge-open" : "badge-closed";
  return (
    "<article>" +
      '<h3>' + escapeHTML(ev.title) + ' <span class="badge ' + badge + '">' + escapeHTML(ev.status) + "</span></h3>" +
      '<p class="meta">' + escapeHTML(ev.category) + " &middot; " + formatDate(ev.date) + "</p>" +
      '<p class="meta">Venue: ' + escapeHTML(ev.venue) + " &middot; Seats: " + ev.seats + "</p>" +
      "<p>" + escapeHTML(ev.description) + "</p>" +
    "</article>"
  );
}

async function init() {
  showLoading(statusEl, "Loading events...");
  try {
    const result = await loadData("../json/events.json", "studenthub-events");

    if (result.fromCache) showInfo(statusEl, "Could not reach the server - showing the last saved events.");
    else hideStatus(statusEl);

    createListView({
      items: result.data,
      listEl: document.getElementById("list"),
      paginationEl: document.getElementById("pagination"),
      countEl: document.getElementById("resultCount"),
      searchEl: document.getElementById("searchInput"),
      searchFields: ["title", "venue", "category", "description"],
      filters: [
        { el: document.getElementById("categoryFilter"), field: "category" },
        { el: document.getElementById("statusFilter"), field: "status" }
      ],
      sortEl: document.getElementById("sortSelect"),
      sorts: {
        "date-asc":   function (a, b) { return new Date(a.date) - new Date(b.date); },
        "date-desc":  function (a, b) { return new Date(b.date) - new Date(a.date); },
        "title-asc":  function (a, b) { return a.title.localeCompare(b.title); },
        "seats-desc": function (a, b) { return b.seats - a.seats; }
      },
      resetEl: document.getElementById("resetBtn"),
      pageSize: 6,
      renderItem: renderEvent,
      emptyMessage: "No events match your search."
    });
  } catch (error) {
    showError(statusEl, "Could not load events. " + error.message, init);
  }
}

init();