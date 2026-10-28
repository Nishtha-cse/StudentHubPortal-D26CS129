/* Practical 6 - fetching JSON (Fetch API) + localStorage cache (advanced extension) */

export async function fetchJSON(url) {
  const response = await fetch(url);
  if (!response.ok) {
    throw new Error("Server returned " + response.status + " " + response.statusText);
  }
  const data = await response.json();            // throws if the JSON is invalid
  if (!Array.isArray(data)) {
    throw new Error("Unexpected JSON format (an array was expected).");
  }
  return data;
}

/*
 * Loads data from the network and caches the latest response in localStorage.
 * If the network request fails, the cached copy is used instead.
 * Returns { data, fromCache }.
 */
export async function loadData(url, cacheKey) {
  try {
    const data = await fetchJSON(url);
    try { localStorage.setItem(cacheKey, JSON.stringify(data)); } catch (e) { /* storage full / blocked */ }
    return { data: data, fromCache: false };
  } catch (error) {
    const cached = localStorage.getItem(cacheKey);
    if (cached) {
      return { data: JSON.parse(cached), fromCache: true };
    }
    throw error;
  }
}