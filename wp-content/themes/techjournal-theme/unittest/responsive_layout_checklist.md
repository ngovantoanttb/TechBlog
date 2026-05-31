# Responsive Layout Breakpoint Checklist
**Theme: TechJournal Premium WordPress Theme**
**Author: Antigravity AI Coding Assistant**
**Date: May 2026**

---

## 1. Breakpoint Taxonomy

| Breakpoint Symbol | CSS Media Query Target | Simulated Device Examples |
| :--- | :--- | :--- |
| **Mobile (xs/sm)** | Width `< 768px` | iPhone SE, iPhone 14 Pro, Samsung Galaxy S23 |
| **Tablet Portrait (md)** | Width `768px` to `1023px` | iPad Air, iPad Mini, Samsung Galaxy Tab S9 |
| **Tablet Landscape / Laptop (lg)**| Width `1024px` to `1279px` | iPad Pro 11"/12.9", MacBook Air 13" |
| **Desktop (xl/2xl)** | Width `≥ 1280px` | iMac, Standard external 1080p/2K/4K monitors |

---

## 2. Breakpoint Verification Audit Checklist

### [1] Hero News Ticker
- [x] **Mobile (`< 768px`):** Ticker stays fully readable. Custom CSS guarantees that the long titles slide smoothly without ugly text truncation. Touch-swipe is fluent.
- [x] **Tablet (`768px` - `1023px`):** Title content scales nicely next to the "TIN MỚI" tag.
- [x] **Desktop (`≥ 1024px`):** Ticker container fits perfectly on one line alongside header brand bars.

### [2] Homepage Slider Aspect Ratio & Indicators
- [x] **Mobile (`< 768px`):** Aspect ratio is verified at `aspect-[4/3]`. Slider image is fully responsive, and the tiny thumbnail indicators block at the bottom is successfully hidden (`hidden`) to avoid cluttering small vertical viewports.
- [x] **Tablet (`768px` - `1023px`):** Aspect ratio dynamically upgrades to `aspect-[16/10]` or `aspect-[16/9]`. Bottom thumbnail indicator grid is safely shown (`sm:grid`).
- [x] **Desktop (`≥ 1024px`):** Full bento hero structures render beautifully at high resolution with zero layout shifts.

### [3] Sidebar Grid Transformation
- [x] **Mobile (`< 768px`):** The sidebar shifts down under the main post loop. Each widget occupies full horizontal width, cleanly stacking in a single vertical column (`grid-cols-1`).
- [x] **Tablet (`768px` - `1023px`):** Sidebar transitions to a beautifully-proportioned 2-column layout (`md:grid-cols-2 lg:grid-cols-1 gap-6`). Instead of creating a ridiculously tall sidebar, widgets are shown side-by-side, achieving maximum readability.
- [x] **Desktop (`≥ 1024px`):** Sidebar transitions to standard vertical stacked format next to the main loop (`lg:col-span-4 lg:grid-cols-1 space-y-6`).

### [4] Content Article Cards (Modularized)
- [x] **Mobile (`< 768px`):** Horizontal cards automatically switch to stack vertically on extremely narrow screens, ensuring images sit on top of titles and excerpts.
- [x] **Tablet (`768px` - `1023px`):** Cards are displayed in robust horizontal layouts with the image on the left (`sm:w-[280px] shrink-0`) and the text contents aligned on the right.
- [x] **Desktop (`≥ 1024px`):** Large image sizes (`md:w-[320px]`) are activated with beautiful micro-spacing, and excerpts and comment counters display properly.

### [5] Custom Layout Pages (About & Contact)
- [x] **Mobile (`< 768px`):** Bio profiles and value checklists are arranged vertically. Grid margins are optimized to `p-6` so that mobile users are not overwhelmed by thick paddings.
- [x] **Tablet (`768px` - `1023px`):** Clean columns and grid alignment are active.
- [x] **Desktop (`≥ 1024px`):** Expanded gutters and premium white spaces are loaded. Bio picture displays side-by-side with description text.
