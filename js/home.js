// Toggle the hamburger menu open/close state
function toggleMenu() {
  document.getElementById("navLinks").classList.toggle("active");
}

// Close the mobile menu when clicking outside of nav or hamburger
document.addEventListener("click", function (e) {
  const nav = document.getElementById("navLinks");
  const hamburger = document.querySelector(".hamburger");

  if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
    nav.classList.remove("active");
  }
});

// Automatically close mobile menu when screen is resized to desktop
window.addEventListener("resize", function () {
  const nav = document.getElementById("navLinks");
  if (window.innerWidth > 768) {
    nav.classList.remove("active");
  }
});

// Open the login popup and update its content based on role
function openPopup(role) {
  const popup = document.getElementById("loginPopup");
  const title = document.getElementById("popupTitle");
  const usernameInput = document.getElementById("usernameInput");

  // Update title and input placeholder depending on role
  title.textContent = role + " Login";
  usernameInput.placeholder = role === "Member" ? "Library ID" : "Username";

  // Show the popup
  popup.style.display = "block";
}

// Close the login popup
function closePopup() {
  document.getElementById("loginPopup").style.display = "none";
}

// Close the popup if clicking outside of the popup content
window.addEventListener("click", function (e) {
  const popup = document.getElementById("loginPopup");
  if (e.target === popup) {
    popup.style.display = "none";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector(".search-input");
  const resultsTable = document.getElementById("search-results");

  if (!searchInput || !resultsTable) return;

  searchInput.addEventListener("input", function () {
    const query = searchInput.value.trim();
    const lowerQuery = query.toLowerCase();
    const isNumeric = /^\d+$/.test(query);

    if (!query) {
      resultsTable.innerHTML = "<tr><td colspan='10'>Start typing to search...</td></tr>";
      return;
    }

    fetch("php/get-items.php")
      .then(res => res.json())
      .then(items => {
        let results = [];

        if (isNumeric) {
          results = items.map(item => {
            const matches = [];
            if (item.isbn?.startsWith(query)) {
              matches.push({ key: "isbn", value: item.isbn, indices: [[0, query.length - 1]] });
            }
            if (item.year?.startsWith(query)) {
              matches.push({ key: "year", value: item.year, indices: [[0, query.length - 1]] });
            }
            return matches.length > 0 ? { item, matches } : null;
          }).filter(Boolean);
        } else if (["available", "unavailable"].includes(lowerQuery)) {
          results = items.map(item => {
            if (item.status?.toLowerCase() === lowerQuery) {
              return { item, matches: [{ key: "status", value: item.status, indices: [[0, query.length - 1]] }] };
            }
            return null;
          }).filter(Boolean);
        } else {
          const fuse = new Fuse(items, {
            keys: ["name", "details", "category", "isbn", "year", "status"],
            threshold: 0.5,
            includeScore: true,
            includeMatches: true
          });
          results = fuse.search(query);
        }

        updateResultsTable(results);
      })
      .catch(err => {
        console.error("Search error:", err);
        resultsTable.innerHTML = "<tr><td colspan='10'>Search failed.</td></tr>";
      });
  });

  function highlight(text, match) {
    if (!match?.indices?.length) return text;

    let highlighted = "";
    let lastIndex = 0;
    match.indices.forEach(([start, end]) => {
      highlighted += text.slice(lastIndex, start);
      highlighted += `<span class='highlight'>${text.slice(start, end + 1)}</span>`;
      lastIndex = end + 1;
    });
    highlighted += text.slice(lastIndex);
    return highlighted;
  }

  function updateResultsTable(results) {
    if (!results.length) {
      resultsTable.innerHTML = "<tr><td colspan='10'>No items found.</td></tr>";
      return;
    }

    resultsTable.innerHTML = "";

    results.forEach(result => {
      const item = result.item || result;
      const matches = result.matches || [];

      const getMatch = (key) => matches.find(m => m.key === key);

      resultsTable.innerHTML += `
        <tr>
          <td>${item.id}</td>
          <td>${highlight(item.name, getMatch("name"))}</td>
          <td>${highlight(item.details, getMatch("details"))}</td>
          <td>${highlight(item.category, getMatch("category"))}</td>
          <td>${item.quantity}</td>
          <td>${highlight(item.isbn, getMatch("isbn"))}</td>
          <td>${highlight(item.year, getMatch("year"))}</td>
          <td>${item.location}</td>
          <td>${highlight(item.status, getMatch("status"))}</td>
          <td>${item.last_update}</td>
        </tr>
      `;
    });
  }
    // Clear button logic
  const clearButton = document.getElementById("clear-button");
  if (clearButton) {
    clearButton.addEventListener("click", function () {
      searchInput.value = "";
      resultsTable.innerHTML = "<tr><td colspan='10'>Start typing to search...</td></tr>";
    });
  }

});