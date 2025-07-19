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
    li.getAttribute("onclick")?.includes(id)
  );
  if (clickedLi) clickedLi.classList.add('active');
}

// Delete item from database using custom confirm modal
function deleteRow(button) {
  const row = button.closest("tr");
  const itemId = row.getAttribute("data-id");

  showConfirm("Are you sure you want to delete this item?", () => {
    fetch("php/delete-item.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(itemId)}`
    })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === "success") {
          showToast("Item deleted.");
          const isSearch = row.closest("tbody").id === "search-results";

          if (isSearch) {
            const query = document.getElementById("search-query")?.value.trim();
            if (query) {
              fetch("php/smart-search.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `query=${encodeURIComponent(query)}`
              })
                .then(res => res.json())
                .then(data => updateItemTable(data))
                .catch(err => {
                  console.error("Search refresh failed after delete:", err);
                  showToast("Deleted, but could not refresh table.");
                });
            }
          } else {
            loadItems();
          }
        } else {
          showToast("Failed to delete item.");
        }
      })
      .catch(error => {
        console.error("Delete error:", error);
        showToast("An error occurred.");
      });
  });
}





// Inline edit
function editRow(button) {
  const row = button.closest("tr");
  if (row.classList.contains("editing")) return;
  row.classList.add("editing");

  const cells = row.querySelectorAll("td");
  const values = [...cells].slice(1, 9).map(cell => cell.textContent.trim());

  cells[1].innerHTML = `<input type="text" value="${values[0]}">`;
  cells[2].innerHTML = `<input type="text" value="${values[1]}">`;
  cells[3].innerHTML = `<input type="text" value="${values[2]}">`;
  cells[4].innerHTML = `<input type="number" class="quantity-input" value="${values[3]}">`;
  cells[5].innerHTML = `<input type="text" value="${values[4]}">`;
  cells[6].innerHTML = `<input type="number" class="year-input" value="${values[5]}">`;
  cells[7].innerHTML = `<input type="text" value="${values[6]}">`;
  cells[8].innerHTML = `
    <select>
      <option value="Available" ${values[7] === "Available" ? "selected" : ""}>Available</option>
      <option value="Unavailable" ${values[7] === "Unavailable" ? "selected" : ""}>Unavailable</option>
    </select>
  `;

  cells[10].innerHTML = `
    <button class="save-btn" onclick="saveRow(this)">Save</button>
    <button class="cancel-btn" onclick="cancelEdit(this)">Cancel</button>
  `;
}

function saveRow(button) {
  const row = button.closest("tr");
  const itemId = row.getAttribute("data-id");
  const inputs = row.querySelectorAll("input");
  const select = row.querySelector("select");

  const updatedData = {
    id: itemId,
    name: inputs[0].value,
    details: inputs[1].value,
    category: inputs[2].value,
    quantity: inputs[3].value,
    isbn: inputs[4].value,
    year: inputs[5].value,
    location: inputs[6].value,
    status: select.value
  };

  const formData = new URLSearchParams();
  for (const key in updatedData) {
    formData.append(key, updatedData[key]);
  }

  fetch("php/update-item.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: formData.toString()
  })
    .then(res => res.text())
    .then(response => {
      if (response.trim() === "success") {
        showToast("Item updated.");
        const isSearch = row.closest("tbody").id === "search-results";

        // Refresh the correct table
        if (isSearch) {
          const query = document.getElementById("search-query")?.value.trim();
          if (query) {
            fetch("php/smart-search.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `query=${encodeURIComponent(query)}`
            })
              .then(res => res.json())
              .then(data => updateItemTable(data))
              .catch(err => {
                console.error("Error refreshing after save (search mode):", err);
                showToast("Update succeeded, but refresh failed.");
              });
          }
        } else {
          loadItems();
        }
      } else {
        showToast("Update failed.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      showToast("An error occurred.");
    });
}










function cancelEdit(button) {
  const row = button.closest("tr");
  row.classList.remove("editing");

  const isSearch = row.closest("tbody").id === "search-results";

  if (isSearch) {
    // Refresh the entire search table
    const query = document.getElementById("search-query")?.value.trim();
    if (query) {
      fetch("php/smart-search.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `query=${encodeURIComponent(query)}`
      })
        .then(res => res.json())
        .then(data => {
          updateItemTable(data); // rebuilds table from search results
        })
        .catch(err => {
          console.error("Search cancel error:", err);
          showToast("Could not cancel properly.");
        });
    }
  } else {
    loadItems();
  }
}





// Add item form handler
document.getElementById("add-item-form").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  fetch("php/add-item.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      if (response === "success") {
        showToast("Item added successfully.");
        e.target.reset();
        loadItems();
      } else {
        showToast("Failed to add item.");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      showToast("An error occurred.");
    });
});

function loadItems() {
  fetch("php/get-items.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("item-body");
      tbody.innerHTML = "";

      data.forEach(item => {
        const row = document.createElement("tr");
        row.setAttribute("data-id", item.id);

        row.innerHTML = `
          <td>${item.id}</td>
          <td>${item.name}</td>
          <td>${item.details}</td>
          <td>${item.category}</td>
          <td>${item.quantity}</td>
          <td>${item.item_code || ""}</td>
          <td>${item.year || ""}</td>
          <td>${item.location || ""}</td>
          <td>${item.status || ""}</td>
          <td>${item.last_update || ""}</td>
          <td>
            <button class="edit-btn" onclick="editRow(this)">Edit</button>
            <button class="delete-btn" onclick="deleteRow(this)">Delete</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    });
}

loadItems();

function showToast(message) {
  const toast = document.getElementById("toast");
  toast.textContent = message;
  toast.className = "show";
  setTimeout(() => {
    toast.className = toast.className.replace("show", "");
  }, 3000);
}

function showConfirm(message, onConfirm) {
  const overlay = document.getElementById("confirm-box");
  document.getElementById("confirm-message").textContent = message;
  overlay.style.display = "block";

  document.getElementById("confirm-yes").onclick = () => {
    overlay.style.display = "none";
    onConfirm();
  };
  document.getElementById("confirm-no").onclick = () => {
    overlay.style.display = "none";
  };
}

function showDashboardSection(type) {
  document.querySelectorAll('.dashboard-section').forEach(section => {
    section.classList.remove('active');
  });

  if (type === 'add') {
    document.getElementById('add-section').classList.add('active');
  } else if (type === 'search') {
    document.getElementById('search-section').classList.add('active');
  }

  document.querySelectorAll('.sidebar ul li').forEach(li => {
    li.classList.remove('active');
  });
  const clickedLi = [...document.querySelectorAll('.sidebar ul li')].find(li =>
    li.getAttribute("onclick")?.includes(type)
  );
  if (clickedLi) clickedLi.classList.add('active');
}

// ✅ Fuse.js fuzzy search implementation
document.addEventListener("DOMContentLoaded", function () {
  const searchBox = document.getElementById("search-query");
  const clearBtn = document.getElementById("clear-button");

  if (clearBtn) {
    clearBtn.addEventListener("click", function () {
      if (searchBox) searchBox.value = "";
      const tbody = document.getElementById("search-results");
      if (tbody) {
        tbody.innerHTML = "<tr><td colspan='11'>Please enter a search term.</td></tr>";
      }
    });
  }

  if (searchBox) {
    searchBox.addEventListener("input", function () {
      const query = searchBox.value.trim();
      const lowerQuery = query.toLowerCase();
      const isNumeric = /^\d+$/.test(query);

      if (!query) {
        document.getElementById("search-results").innerHTML = "<tr><td colspan='11'>Please enter a search term.</td></tr>";
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
                matches.push({ key: "isbn", value: item.item_code, indices: [[0, query.length - 1]] });
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
          showToast("Search failed.");
        });
    });
  }
});






// ✅ Fixed: update results in the correct search section table
function updateItemTable(results, query = "") {
  const tbody = document.getElementById("search-results");
  if (!tbody) return;

  tbody.innerHTML = "";

  if (results.length === 0) {
    tbody.innerHTML = "<tr><td colspan='11'>No items found.</td></tr>";
    return;
  }

  const highlightFuseMatch = (text, match) => {
    if (!match || !match.indices || match.indices.length === 0) return text;

    let highlighted = "";
    let lastIndex = 0;

    // Sort the ranges just in case
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
    const item = result.item || result; // Fuse returns { item, matches }, fallback for plain result
    const matches = result.matches || [];

    const getMatch = (key) => matches.find(m => m.key === key);

    const row = document.createElement("tr");
    row.setAttribute("data-id", item.id);

    row.innerHTML = `
      <td>${item.id}</td>
      <td>${highlightFuseMatch(item.name, getMatch("name"))}</td>
      <td>${highlightFuseMatch(item.details, getMatch("details"))}</td>
      <td>${highlightFuseMatch(item.category, getMatch("category"))}</td>
      <td>${item.quantity}</td>
      <td>${highlightFuseMatch(item.item_code, getMatch("isbn"))}</td>
      <td>${highlightFuseMatch(item.year, getMatch("year"))}</td>
      <td>${item.location}</td> <!-- Not highlighted -->
      <td>${highlightFuseMatch(item.status, getMatch("status"))}</td>
      <td>${item.last_update}</td>
      <td>
        <button class="edit-btn" onclick="editRow(this)">Edit</button>
        <button class="delete-btn" onclick="deleteRow(this)">Delete</button>
      </td>
    `;

    tbody.appendChild(row);
  });
}








