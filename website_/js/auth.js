// js/auth.js - Xử lý xác thực, đăng nhập/đăng ký, phân quyền vai trò và đăng xuất

function getCurrentUser() {
  // Nếu người dùng đã chủ động bấm Đăng xuất -> luôn trả về null (không tự động đăng nhập lại)
  if (localStorage.getItem("eventvn_logged_out") === "true") {
    return null;
  }

  const data = localStorage.getItem("eventvn_current_user");
  if (data === "null" || !data) {
    // Nếu chưa từng có phiên nào và chưa từng bấm đăng xuất, khởi tạo người dùng mặc định lần đầu
    if (!localStorage.getItem("eventvn_initialized")) {
      localStorage.setItem("eventvn_initialized", "true");
      const defaultUser = getUsers().find((u) => u.id === 1) || null;
      if (defaultUser) {
        localStorage.setItem("eventvn_current_user", JSON.stringify(defaultUser));
      }
      return defaultUser;
    }
    return null;
  }

  try {
    return JSON.parse(data);
  } catch (e) {
    return null;
  }
}

function setCurrentUser(user) {
  if (!user) {
    localStorage.setItem("eventvn_current_user", "null");
    localStorage.setItem("eventvn_logged_out", "true");
  } else {
    localStorage.setItem("eventvn_current_user", JSON.stringify(user));
    localStorage.removeItem("eventvn_logged_out");
  }
  updateNavbarAuth();
}

function login(email, password) {
  const users = getUsers();
  const found = users.find(
    (u) => u.email.toLowerCase().trim() === email.toLowerCase().trim() && u.password === password
  );
  if (!found) {
    return { success: false, message: "Email hoặc mật khẩu không chính xác!" };
  }
  if (!found.active) {
    return { success: false, message: "Tài khoản này hiện đang bị tạm khóa!" };
  }

  localStorage.removeItem("eventvn_logged_out");
  setCurrentUser(found);
  return { success: true, user: found };
}

function register(name, email, phone, password) {
  const users = getUsers();
  const exists = users.some((u) => u.email.toLowerCase().trim() === email.toLowerCase().trim());
  if (exists) {
    return { success: false, message: "Email này đã được đăng ký trên hệ thống!" };
  }

  // Tạo avatar từ 2-3 chữ cái đầu
  const initials = name
    .trim()
    .split(/\s+/)
    .map((w) => w[0]?.toUpperCase())
    .slice(0, 3)
    .join("");

  const newUser = {
    id: Date.now(),
    name: name.trim(),
    email: email.trim().toLowerCase(),
    phone: phone.trim(),
    role: "user", // Người đăng ký mới luôn mặc định là thành viên thường (role: user)
    active: true,
    avatar: initials || "TV",
    password: password,
    bio: "Thành viên mới tham gia cộng đồng EventVN.",
    location: "Hà Nội, Việt Nam",
    gender: "Nam",
    birthdate: "2000-01-01",
  };

  users.push(newUser);
  saveUsers(users);

  localStorage.removeItem("eventvn_logged_out");
  setCurrentUser(newUser);
  return { success: true, user: newUser };
}

// Hàm Đăng xuất: Xóa hoàn toàn phiên làm việc và chuyển hướng
function logout() {
  localStorage.setItem("eventvn_logged_out", "true");
  localStorage.setItem("eventvn_current_user", "null");
  localStorage.removeItem("eventvn_current_user");

  updateNavbarAuth();
  showToast("Bạn đã đăng xuất thành công!", "info");

  setTimeout(() => {
    window.location.href = "index.html";
  }, 350);
}

// Kiểm tra quyền Admin khi truy cập các trang quản trị
function requireAdmin() {
  const user = getCurrentUser();
  if (!user || user.role !== "admin") {
    alert("Quyền truy cập bị từ chối: Trang này chỉ dành cho Quản trị viên (Admin)!");
    window.location.href = "auth.html?redirect=admin.html";
    return false;
  }
  return true;
}

// ─── Render Navbar Header & Role Switcher ────────────────────────────────────
function updateNavbarAuth() {
  const user = getCurrentUser();
  const containers = document.querySelectorAll(".navbar-auth-container");
  const roleSwitcherContainers = document.querySelectorAll(".navbar-role-switcher");

  // Xử lý nút chuyển đổi vai trò [Người dùng | Admin]:
  // CHỈ HIỆN KHI TÀI KHOẢN ĐANG ĐĂNG NHẬP LÀ ADMIN (role === 'admin')
  roleSwitcherContainers.forEach((container) => {
    if (user && user.role === "admin") {
      const isCurrentAdminPage = window.location.pathname.includes("admin.html");
      container.innerHTML = `
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border" role="group">
          <a href="index.html" class="btn btn-sm rounded-pill py-1 px-3 ${
            !isCurrentAdminPage ? "btn-dark fw-semibold" : "btn-light text-secondary border-0"
          }">Người dùng</a>
          <a href="admin.html" class="btn btn-sm rounded-pill py-1 px-3 ${
            isCurrentAdminPage ? "btn-dark fw-semibold" : "btn-light text-secondary border-0"
          }">Admin</a>
        </div>
      `;
      container.classList.remove("d-none");
      container.classList.remove("hidden");
    } else {
      // Người dùng thường (role === 'user') hoặc chưa đăng nhập: ẨN HOÀN TOÀN
      container.innerHTML = "";
      container.classList.add("d-none");
      container.classList.add("hidden");
    }
  });

  // Xử lý Avatar & nút đăng nhập/đăng xuất trên Navbar
  containers.forEach((container) => {
    if (user) {
      container.innerHTML = `
        <div class="d-flex align-items-center gap-2">
          <!-- BẤM VÀO AVATAR CHUYỂN TỚI TRANG HỒ SƠ RIÊNG (profile.html) -->
          <a href="profile.html" title="Xem hồ sơ cá nhân" class="d-flex align-items-center gap-2 text-decoration-none p-1 rounded-pill">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
              ${user.avatar || "U"}
            </span>
            <div class="d-none d-sm-block text-start pe-1 lh-sm">
              <div class="fw-semibold text-dark small">${user.name}</div>
              <div class="text-muted small" style="font-size: 12px;">
                ${user.role === "admin" ? "Quản trị viên" : "Thành viên"}
              </div>
            </div>
          </a>

          <!-- Nút Đăng xuất nhanh -->
          <button 
            type="button" 
            onclick="logout()" 
            title="Đăng xuất khỏi tài khoản" 
            aria-label="Đăng xuất khỏi tài khoản"
            class="btn btn-sm btn-outline-danger border-0 rounded-circle d-flex align-items-center justify-content-center"
            style="width: 34px; height: 34px; padding: 0;"
          >
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      `;
    } else {
      // Khi đã đăng xuất hoặc chưa đăng nhập: Hiển thị Đăng nhập / Đăng ký
      container.innerHTML = `
        <div class="d-flex align-items-center gap-2">
          <a href="auth.html" class="btn btn-sm btn-outline-secondary">Đăng nhập</a>
          <a href="auth.html?mode=register" class="btn btn-sm btn-dark">Đăng ký</a>
        </div>
      `;
    }
  });

  // Cập nhật số lượng bookmark
  if (typeof updateBookmarkBadges === "function") {
    updateBookmarkBadges();
  }
}

// Tự động gọi khi trang tải xong
document.addEventListener("DOMContentLoaded", () => {
  updateNavbarAuth();
});
