
// Show one dashboard section at a time (sidebar links)
function showSection(id) {
  document.querySelectorAll('.dashboard-section').forEach(section => {
    section.classList.remove('active');
  });
  document.getElementById(id).classList.add('active');

  document.querySelectorAll('.sidebar li').forEach(li => {
    li.classList.remove('active');
  });
  const clickedLi = [...document.querySelectorAll('.sidebar li')].find(li =>
    li.getAttribute("onclick")?.includes(id) || li.dataset.section === id
  );
  if (clickedLi) clickedLi.classList.add('active');
}

// Fuse.js fuzzy search implementation
document.addEventListener("DOMContentLoaded", function () {
  const searchBox = document.getElementById("search-query");
  const clearBtn = document.getElementById("clear-button");

  if (clearBtn) {
    clearBtn.addEventListener("click", function () {
      if (searchBox) searchBox.value = "";
      const tbody = document.getElementById("search-results");
      if (tbody) {
        tbody.innerHTML = "<tr><td colspan='10'>Please enter a search term.</td></tr>";
      }
    });
  }

  if (searchBox) {
    searchBox.addEventListener("input", function () {
      const query = searchBox.value.trim();
      const lowerQuery = query.toLowerCase();
      const isNumeric = /^\d+$/.test(query);

      if (!query) {
        document.getElementById("search-results").innerHTML = "<tr><td colspan='10'>Please enter a search term.</td></tr>";
        return;
      }

      fetch("php/get-items.php")
        .then(res => res.json())
        .then(items => {
          let results = [];

          if (isNumeric) {
            results = items.map(item => {
              const matches = [];

              if (item.item_code?.startsWith(query)) {
                matches.push({ key: "item_code", value: item.item_code, indices: [[0, query.length - 1]] });
              }
              if (item.year?.startsWith(query)) {
                matches.push({ key: "year", value: item.year, indices: [[0, query.length - 1]] });
              }

              return matches.length > 0 ? { item, matches } : null;
            }).filter(Boolean);
          } else if (["available", "unavailable"].includes(lowerQuery)) {
            results = items.map(item => {
              if (item.status?.toLowerCase() === lowerQuery) {
                return {
                  item,
                  matches: [{ key: "status", value: item.status, indices: [[0, query.length - 1]] }]
                };
              }
              return null;
            }).filter(Boolean);
          } else {
            const fuse = new Fuse(items, {
              keys: ["name", "details", "category", "item_code", "year", "status"],
              threshold: 0.5,
              includeScore: true,
              includeMatches: true
            });
            results = fuse.search(query);
          }

          updateItemTable(results, query);
        })
        .catch(err => {
          console.error("Fuzzy search error:", err);
          const tbody = document.getElementById("search-results");
          if (tbody) {
            tbody.innerHTML = "<tr><td colspan='10'>Search failed.</td></tr>";
          }
        });
    });
  }
});

function updateItemTable(results, query = "") {
  const tbody = document.getElementById("search-results");
  if (!tbody) return;

  tbody.innerHTML = "";

  if (results.length === 0) {
    tbody.innerHTML = "<tr><td colspan='10'>No items found.</td></tr>";
    return;
  }

  const highlightFuseMatch = (text, match) => {
    if (!match || !match.indices || match.indices.length === 0) return text;

    let highlighted = "";
    let lastIndex = 0;

    match.indices.sort((a, b) => a[0] - b[0]);

    match.indices.forEach(([start, end]) => {
      highlighted += text.slice(lastIndex, start);
      highlighted += `<span class="highlight">${text.slice(start, end + 1)}</span>`;
      lastIndex = end + 1;
    });

    highlighted += text.slice(lastIndex);
    return highlighted;
  };

  results.forEach(result => {
    const item = result.item || result;
    const matches = result.matches || [];

    const getMatch = (key) => matches.find(m => m.key === key);

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.id}</td>
      <td>${highlightFuseMatch(item.name, getMatch("name"))}</td>
      <td>${highlightFuseMatch(item.details, getMatch("details"))}</td>
      <td>${highlightFuseMatch(item.category, getMatch("category"))}</td>
      <td>${item.quantity}</td>
      <td>${highlightFuseMatch(item.item_code, getMatch("item_code"))}</td>
      <td>${highlightFuseMatch(item.year, getMatch("year"))}</td>
      <td>${item.location}</td>
      <td>${highlightFuseMatch(item.status, getMatch("status"))}</td>
      <td>${item.last_update}</td>
    `;

    tbody.appendChild(row);
  });
}
