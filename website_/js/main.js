// js/main.js - Logic tương tác trang chủ, tìm kiếm, lọc danh mục, bookmark trái tim (Bootstrap 5)

let activeCategory = 0; // 0 = Tất cả
let activeStatus = "all"; // all, upcoming, ongoing, ended, bookmarked
let searchKeyword = "";

document.addEventListener("DOMContentLoaded", () => {
  renderCategoryPills();
  renderEvents();
  setupEventListeners();
});

function setupEventListeners() {
  // Tìm kiếm sự kiện
  const searchInput = document.getElementById("search-input");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      searchKeyword = e.target.value.trim().toLowerCase();
      renderEvents();
    });
  }

  // Lọc theo trạng thái / Yêu thích
  const statusTabs = document.querySelectorAll(".status-filter-tab");
  statusTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      statusTabs.forEach((t) => {
        t.classList.remove("btn-dark", "active");
        t.classList.add("btn-outline-secondary");
      });
      tab.classList.remove("btn-outline-secondary");
      tab.classList.add("btn-dark", "active");

      activeStatus = tab.dataset.status;
      renderEvents();
    });
  });
}

// Render các chip lọc danh mục ở trang chủ
function renderCategoryPills() {
  const container = document.getElementById("category-pills-container");
  if (!container) return;

  const categories = getCategories().filter((c) => c.active);
  let html = `
    <button type="button" onclick="selectCategory(0)" class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium ${
      activeCategory === 0 ? "btn-dark shadow-xs" : "btn-outline-secondary"
    }">
      Tất cả
    </button>
  `;

  categories.forEach((cat) => {
    const isSelected = activeCategory === cat.id;
    html += `
      <button type="button" onclick="selectCategory(${cat.id})" class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium ${
        isSelected ? "btn-dark shadow-xs" : "btn-outline-secondary"
      }">
        ${cat.name}
      </button>
    `;
  });

  container.innerHTML = html;
}

function selectCategory(catId) {
  activeCategory = catId;
  renderCategoryPills();
  renderEvents();
}

// Render danh sách sự kiện theo bộ lọc
function renderEvents() {
  const container = document.getElementById("events-grid-container");
  const countLabel = document.getElementById("events-count-label");
  if (!container) return;

  const allEvents = getEvents();
  const categories = getCategories();
  const bookmarks = getBookmarks();

  let filtered = allEvents.filter((event) => {
    // Lọc theo danh mục
    if (activeCategory !== 0 && event.categoryId !== activeCategory) {
      return false;
    }
    // Lọc theo trạng thái hoặc bookmark yêu thích
    if (activeStatus === "bookmarked") {
      if (!bookmarks.includes(event.id)) return false;
    } else if (activeStatus !== "all" && event.status !== activeStatus) {
      return false;
    }
    // Tìm kiếm theo từ khóa
    if (searchKeyword) {
      const matchTitle = event.title.toLowerCase().includes(searchKeyword);
      const matchLoc = event.location.toLowerCase().includes(searchKeyword);
      const cat = categories.find((c) => c.id === event.categoryId);
      const matchCat = cat && cat.name.toLowerCase().includes(searchKeyword);
      if (!matchTitle && !matchLoc && !matchCat) return false;
    }
    return true;
  });

  if (countLabel) {
    countLabel.textContent = `Hiển thị ${filtered.length} sự kiện`;
  }

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="col-12 text-center py-5 px-3 bg-white rounded-4 border shadow-sm my-3">
        <div class="mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
          <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <h3 class="fs-5 fw-bold text-dark mb-1">Không tìm thấy sự kiện nào</h3>
        <p class="small text-secondary mb-3">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bớt bộ lọc.</p>
        <div>
          <button type="button" onclick="resetFilters()" class="btn btn-sm btn-dark px-3 py-2 fw-medium rounded-pill">
            Đặt lại bộ lọc
          </button>
        </div>
      </div>
    `;
    return;
  }

  let html = "";
  filtered.forEach((event) => {
    const cat = categories.find((c) => c.id === event.categoryId) || { name: "Chung" };
    const isSaved = bookmarks.includes(event.id);
    const percent = Math.min(100, Math.round((event.registered / event.capacity) * 100));

    html += `
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border shadow-sm card-hover-scale overflow-hidden">
          <!-- Thumbnail -->
          <div class="position-relative overflow-hidden bg-light" style="height: 200px;">
            <img src="${event.image}" alt="${event.title}" class="w-100 h-100 object-fit-cover" loading="lazy" />
            
            <!-- Category & Status Badge -->
            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1 align-items-center">
              <span class="badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
                ${cat.name}
              </span>
              ${getStatusBadge(event.status)}
            </div>

            <!-- Nút Bookmark Trái Tim Yêu Thích -->
            <button 
              type="button"
              onclick="handleBookmarkClick(event, ${event.id})"
              class="bookmark-btn-${event.id} position-absolute top-0 end-0 m-3 btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border ${
                isSaved ? "text-danger" : "text-secondary"
              }"
              style="width: 36px; height: 36px; z-index: 5;"
              title="${isSaved ? "Bỏ lưu sự kiện" : "Lưu vào yêu thích"}"
              aria-label="${isSaved ? "Bỏ lưu sự kiện" : "Lưu vào yêu thích"}"
            >
              ${getHeartSymbol(isSaved, `w-4 h-4 ${isSaved ? "text-danger" : ""}`)}
            </button>
          </div>

          <!-- Body -->
          <div class="card-body d-flex flex-column justify-content-between p-4">
            <div>
              <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                ${getCalendarSymbol("w-3.5 h-3.5 text-muted")}
                <span>${fmtDateTime(event.startDate)}</span>
              </div>

              <h3 class="fs-5 fw-bold text-dark mb-2 line-clamp-2">
                <a href="event-detail.html?id=${event.id}" class="text-decoration-none text-dark">${event.title}</a>
              </h3>

              <div class="d-flex align-items-center gap-1 text-muted small mb-3 line-clamp-1">
                ${getMapPinSymbol("w-3.5 h-3.5 text-muted flex-shrink-0")}
                <span>${event.location}</span>
              </div>
            </div>

            <!-- Capacity & Action -->
            <div class="mt-3">
              <div class="mb-3">
                <div class="d-flex justify-content-between small text-secondary mb-1">
                  <span>Số chỗ đã đăng ký</span>
                  <span class="fw-semibold text-dark tabular-nums">${event.registered}/${event.capacity} (${percent}%)</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar ${
                    percent >= 100 ? "bg-danger" : percent >= 80 ? "bg-warning" : "bg-dark"
                  }" role="progressbar" style="width: ${percent}%" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>

              <div class="d-flex gap-2 pt-2 border-top">
                <a href="event-detail.html?id=${event.id}" class="btn btn-sm btn-outline-secondary flex-fill py-2 fw-medium">
                  Xem chi tiết
                </a>
                <a href="event-detail.html?id=${event.id}&register=1" class="btn btn-sm btn-dark flex-fill py-2 fw-medium">
                  Đăng ký vé
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// Xử lý bấm Bookmark Trái tim
function handleBookmarkClick(e, eventId) {
  e.preventDefault();
  e.stopPropagation();

  const isAdded = toggleBookmark(eventId);
  const events = getEvents();
  const ev = events.find((item) => item.id === eventId);
  const title = ev ? ev.title : "Sự kiện";

  if (isAdded) {
    showToast(`Đã lưu "${title}" vào mục Yêu thích!`, "success");
  } else {
    showToast(`Đã bỏ lưu "${title}" khỏi mục Yêu thích!`, "info");
  }

  // Cập nhật lại giao diện nút trái tim
  document.querySelectorAll(`.bookmark-btn-${eventId}`).forEach((btn) => {
    btn.classList.add("animate-heart-pop");
    btn.innerHTML = getHeartSymbol(isAdded, `w-5 h-5 ${isAdded ? "text-danger" : ""}`);
    if (isAdded) {
      btn.classList.remove("text-secondary");
      btn.classList.add("text-danger");
    } else {
      btn.classList.remove("text-danger");
      btn.classList.add("text-secondary");
    }
    setTimeout(() => btn.classList.remove("animate-heart-pop"), 350);
  });

  // Nếu đang ở tab Yêu thích thì render lại danh sách
  if (activeStatus === "bookmarked") {
    renderEvents();
  }
}

function resetFilters() {
  activeCategory = 0;
  activeStatus = "all";
  searchKeyword = "";
  const searchInput = document.getElementById("search-input");
  if (searchInput) searchInput.value = "";
  renderCategoryPills();

  const statusTabs = document.querySelectorAll(".status-filter-tab");
  statusTabs.forEach((tab) => {
    if (tab.dataset.status === "all") {
      tab.classList.remove("btn-outline-secondary");
      tab.classList.add("btn-dark", "active");
    } else {
      tab.classList.remove("btn-dark", "active");
      tab.classList.add("btn-outline-secondary");
    }
  });

  renderEvents();
}
