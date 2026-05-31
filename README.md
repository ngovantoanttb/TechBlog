# TechBlog - Modern Editorial WordPress Theme

[![WordPress Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-8892BF.svg)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3-38bdf8.svg)](https://tailwindcss.com)
[![Design Style](https://img.shields.io/badge/Design-Editorial%20%7C%20Flat%20UI-success.svg)](#)

**TechBlog** là một giao diện WordPress (WordPress Theme) được thiết kế và phát triển theo phong cách **Editorial Grid** hiện đại, chuyên dành cho các trang tin tức công nghệ, tạp chí trực tuyến và blog chia sẻ kiến thức chuyên sâu. Dự án tập trung vào trải nghiệm đọc tối ưu, tốc độ tải trang vượt trội, chuẩn SEO Google và ngôn ngữ thiết kế phẳng (Flat UI, Zero-Border-Radius) tinh tế với hệ màu chủ đạo **Xanh - Trắng** đầy tính công nghệ và chuyên nghiệp.

---

## 🎨 Ngôn Ngữ Thiết Kế & Triết Lý Phát Triển

Giao diện được xây dựng dựa trên các nguyên tắc thiết kế hiện đại hàng đầu:
*   **Chủ Đề Màu Sắc (Color Palette):**
    *   **Màu chủ đạo (Primary):** Xanh dương công nghệ năng động (`#3b82f6` - Tailwind Blue 500) tạo điểm nhấn hiện đại.
    *   **Màu nền & Nội dung:** Sự kết hợp hoàn hảo giữa nền trắng tinh khiết (`bg-white`), xám đá phiến thanh lịch (`text-slate-900`, `bg-slate-900`) giúp cải thiện độ tương phản và giảm mỏi mắt khi đọc lâu.
    *   **Màu danh mục (Accent Color):** Sắc đỏ tinh tế chỉ dành riêng cho các thẻ danh mục/nhãn đặc biệt để thu hút sự chú ý của độc giả.
*   **Triết Lý Flat UI:** Thiết kế phẳng hoàn toàn, loại bỏ các góc bo tròn (`rounded-none`) mang lại cảm giác mạnh mẽ, góc cạnh và đậm chất tạp chí in ấn cao cấp.
*   **Typography Tiếng Việt:** Tối ưu hóa phông chữ **Be Vietnam Pro** hiển thị mượt mà trên mọi thiết bị di động, xử lý xuất sắc các ký tự có dấu phức tạp trong Tiếng Việt.

---

## ✨ Các Tính Năng Nổi Bật

### 1. Header & Điều Hướng Hiện Đại (`header.php`)
*   **Header Tĩnh Màu Trắng Đặc (`bg-white`):** Không sử dụng hiệu ứng làm mờ hay trong suốt, đảm bảo sự tập trung cao nhất vào nội dung bài viết.
*   **Thanh Tìm Kiếm Inline Thông Minh:** Tích hợp trực tiếp ô tìm kiếm (Input Search) vào header giúp người dùng gõ tìm kiếm lập tức mà không cần chuyển trang hoặc bấm dropdown.
*   **Cấu trúc Schema JSON-LD chuẩn SEO:** Tích hợp trực tiếp các thẻ Metadata JSON-LD trong thẻ `<head>`, tự động đồng bộ hóa thông tin tác giả, bài viết và ảnh đại diện mặc định để Google lập chỉ mục chính xác 100%.

### 2. Trang Chủ Editorial Grid (`front-page.php`)
*   **Editorial Layout:** Bố cục dạng lưới phân chia khoa học thành các khu vực: Bài viết ghim tiêu điểm (Pinned/Featured), Bài viết mới nhất (Newest), và Sidebar xem nhiều nhất (Most Viewed).
*   **PHP Query Optimization:** Loại bỏ hoàn toàn sự trùng lặp bài viết giữa các widget thông qua thuật toán loại trừ ID thông minh ở cấp độ truy vấn cơ sở dữ liệu.
*   **Đồng Bộ Thời Gian Thực:** Hiển thị lời chúc chào mừng thân thiện bên cạnh thời gian thực tại góc trái của trang chủ.

### 3. Footer Chuẩn SEO & Tối Giản (`footer.php`)
*   **Logo Chống Lỗi Răng Cưa:** Sử dụng tệp SVG logo chất lượng cao kết hợp thuộc tính CSS `clip-path: inset(1px)` để loại bỏ hoàn toàn các lỗi viền trắng/kẻ trắng thừa khi render trên nền tối (`bg-slate-900`).
*   **Chủ Đề Chính 2 Cột:** Tự động lấy ra **toàn bộ các chuyên mục** của hệ thống và phân bổ đều làm 2 cột bằng CSS Grid của Tailwind, giúp tăng trải nghiệm điều hướng cho người dùng cuối.
*   **Liên Kết Mạng Xã Hội Phẳng:** Các icon Facebook, Telegram, Email được tối giản hóa và tăng trải nghiệm hover mượt mà.

### 4. Hệ Thống Thumbnail Đồng Bộ Toàn Cục (`functions.php`)
*   Sử dụng hàm bổ trợ trung tâm `techblog_get_placeholder_img()` để tự động thay thế ảnh đại diện mặc định bằng tệp SVG **`placeholder-thumbnail.svg`** cho mọi bài viết chưa thiết lập Ảnh tiêu biểu (Featured Image).
*   Ảnh placeholder này đồng bộ hoàn hảo trên tất cả các trang (Home, Archive, Search, Single) và hệ thống Schema SEO.

### 5. Trang Liên Hệ Tích Hợp AJAX (`page-lien-he.php`)
*   Form liên hệ thiết kế phẳng, trực quan.
*   Nút gửi liên hệ màu xanh primary được tích hợp hiệu ứng xoay spinner động (`animate-spin`) và vô hiệu hóa trạng thái bấm khi đang gửi dữ liệu qua AJAX, mang lại trải nghiệm tương tác cực kỳ cao cấp.

---

## 📁 Cấu Trúc Thư Mục Giao Diện (Theme Structure)

```text
techjournal-theme/
├── assets/
│   ├── css/                  # Chứa file CSS được biên dịch từ Tailwind
│   └── images/               # Chứa các tài nguyên SVG, logo và placeholder
│       ├── techblog-logo.svg           # Logo chính thức trên Header
│       ├── Logo-TechBlog-footer.svg    # Logo hiển thị trên nền tối Footer
│       └── placeholder-thumbnail.svg   # Ảnh thumbnail mặc định cho bài viết
├── 404.php                   # Giao diện thông báo lỗi 404
├── archive.php               # Giao diện lưu trữ bài viết
├── category.php              # Giao diện danh mục/chuyên mục
├── comments.php              # Khu vực bình luận bài viết
├── footer.php                # Phần chân trang (Chủ đề chính 2 cột, Logo tối giản)
├── front-page.php            # Trang chủ phong cách Editorial Grid chuyên nghiệp
├── functions.php             # Tệp xử lý PHP logic, hỗ trợ theme và API
├── header.php                # Phần đầu trang (Inline search, Schema SEO, Nav menu)
├── home.php                  # Giao diện trang tin tức mặc định
├── index.php                 # Tệp mẫu dự phòng cốt lõi
├── page-gioi-thieu.php       # Giao diện trang "Giới thiệu" tùy chỉnh
├── page-lien-he.php          # Giao diện trang "Liên hệ" tích hợp AJAX
├── page.php                  # Giao diện trang tĩnh mặc định
├── search.php                # Giao diện trang kết quả tìm kiếm
├── single.php                # Giao diện chi tiết bài viết (Bài viết liên quan)
└── style.css                 # File thông tin Meta Theme WordPress
```

---

## 🛠️ Công Nghệ Sử Dụng

1.  **WordPress Core (PHP):** Tận dụng tối đa các API chuẩn của WordPress như `WP_Query`, `get_categories`, `esc_url`, `wp_nonce_field` để đảm bảo tính an toàn bảo mật và khả năng tương thích cao.
2.  **Tailwind CSS:** Sử dụng framework Tailwind CSS giúp phát triển giao diện nhanh chóng, nhất quan về khoảng cách (`spacing`), cỡ chữ (`typography`) và màu sắc (`slate`, `blue`).
3.  **Google Material Symbols:** Sử dụng thư viện icon hiện đại, tối giản của Google giúp giao diện nhẹ nhàng và thanh thoát.

---

## 🚀 Hướng Dẫn Cài Đặt & Phát Triển

1.  **Sao chép Theme:** 
    *   Tải thư mục `techjournal-theme` và đặt vào thư mục chứa theme của WordPress: `wp-content/themes/`.
2.  **Kích hoạt Theme:**
    *   Truy cập vào Trang quản trị WordPress (`wp-admin`) -> **Giao diện (Appearance)** -> **Giao diện (Themes)**.
    *   Bấm **Kích hoạt (Activate)** giao diện **TechJournal Theme**.
3.  **Cài đặt Trang tĩnh:**
    *   Tạo trang mới mang tên **Giới thiệu** và đặt đường dẫn slug là `gioi-thieu`. Hệ thống sẽ tự động nhận diện template `page-gioi-thieu.php`.
    *   Tạo trang mới mang tên **Liên hệ** và đặt đường dẫn slug là `lien-he`. Hệ thống sẽ tự động nhận diện template `page-lien-he.php`.
4.  **Tải ảnh mặc định:**
    *   Đảm bảo các tệp ảnh logo và placeholder nằm đúng vị trí trong thư mục `assets/images/` để giao diện hiển thị chính xác nhất.

## 📝 Bản Quyền & Phát Triển

Dự án được phát triển và tối ưu hóa bởi **Ngô Văn Toàn**. 
Vui lòng giữ lại thông tin bản quyền và tác giả trong file `style.css` khi sử dụng và chỉnh sửa cho mục đích cá nhân hoặc thương mại.

*   *Phát triển bởi:* **Ngô Văn Toàn**
*   *Phiên bản hiện tại:* 1.2.0 (Modernized Edition)
*   *Màu sắc nhận diện:* **Blue & White (#3b82f6 / #ffffff)**
