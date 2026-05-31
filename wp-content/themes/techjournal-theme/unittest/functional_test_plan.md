# Technical Functional Test Plan & Execution Log
**Theme: TechJournal Premium WordPress Theme**
**Author: Antigravity AI Coding Assistant**
**Date: May 2026**

---

## 1. Scope of Testing
This document outlines the systematic manual and automated validation procedures for the TechJournal WordPress Theme to ensure production-grade stability, seamless performance, and design integrity across all viewports.

---

## 2. Core Functional Modules

### A. Homepage Hero Slider & Pinned News Ticker
*   **Test Case H1 - Pinned News Ticker (Desktop/Tablet/Mobile):**
    *   *Verification:* Ensure the ticker bar displays the exact latest sticky post titles in non-truncated, readable format.
    *   *Touch-Friendly Action:* Validate swipe behavior on mobile touch screens.
*   **Test Case H2 - Hero Featured Slider (Strictly 7-Post Count):**
    *   *Verification:* Query matches the strict limit of 7 featured posts. Transition buttons (Prev/Next) have at least `44px x 44px` interactive touch targets.
    *   *Aspect Ratio:* Aspect ratio scales dynamically as `aspect-[4/3] sm:aspect-[16/10]` to prevent image distortion on mobile devices.
    *   *Mobile Exclusion:* Thumbnail navigation strip at the bottom is hidden on mobile viewports (`hidden sm:grid`) to prevent workspace clutter.

### B. Grid and List Article Card Abstractions
*   **Test Case C1 - Reusable Content Cards (`template-parts/content-card.php`):**
    *   *Verification:* Check list view formatting inside Category, Tag, Search, and Archive pages. Ensure metadata aligns correctly.
    *   *Color Sync:* Author metadata "BY Admin TechBlog" and badges must render in theme-standard Royal Blue (`text-primary` / `bg-primary`).
*   **Test Case C2 - Reusable Grid Cards (`template-parts/content-grid.php`):**
    *   *Verification:* Verify home listing layout rendering. Card hover triggers instant subtle scale zoom (`group-hover:scale-102`) and smooth opacity shift.

### C. Sidebar Component
*   **Test Case S1 - Responsive Sidebar Widget Grid Layout:**
    *   *Verification:* On desktop screens (`lg: 1024px`), sidebar widgets load vertically (`space-y-6`).
    *   *Tablet Adaptability:* On tablet devices (`md: 768px` up to `lg: 1024px`), the sidebar dynamically refactors into a responsive two-column grid (`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6`), displaying widgets side-by-side to maximize canvas space.
    *   *Clean Stacking on Mobile:* Below `768px`, widgets automatically collapse into a single vertical stack.

### D. AJAX "Load More" Content Orchestrator
*   **Test Case A1 - Payload Delivery & Dynamic Insertion:**
    *   *Verification:* Clicking the button triggers an immediate async request. Spinner (`#load-more-spinner`) displays instantly.
    *   *Architecture Alignment:* Newly loaded articles are rendered server-side via `template-parts/content-card.php` or `template-parts/content-grid.php` depending on context, guaranteeing 100% visual consistency.
    *   *Hover Preservation:* Hover events and transitional animations are automatically inherited by newly appended DOM elements without page refresh.

### E. Custom Premium Templates (About & Contact)
*   **Test Case T1 - About Page (Trang Giới Thiệu):**
    *   *Verification:* Custom templates select smoothly in wp-admin Page Attributes. Core values list displays sequential numbers and custom quote block. Team bio grid shows founder details.
    *   *Brand Alignment:* All `#ff0000` hardcoded highlights are fully upgraded to `text-primary` and `bg-primary-container` (Royal Blue style).
*   **Test Case T2 - Contact Page (Trang Liên Hệ):**
    *   *Verification:* Features zero-border-radius inputs, real-time focus states, and instant AJAX submission pipeline mock.

---

## 3. Test Execution Summary

| Test Case ID | Target Feature | Device / Viewport | Test Status | Remarks |
| :--- | :--- | :--- | :--- | :--- |
| **H1** | Pinned Ticker | All | **PASSED** | Non-truncated news ticker text renders beautifully. |
| **H2** | Hero Slider | Mobile / Tablet | **PASSED** | Aspect ratio adapted; mobile thumbnail indicators safely hidden. |
| **C1** | Content Card | All | **PASSED** | Modular file created. Standardized post title and meta across all lists. |
| **C2** | Content Grid | Homepage | **PASSED** | Smooth zoom scaling active. Verified no visual gaps. |
| **S1** | Sidebar Grid | Tablet (768px - 1024px) | **PASSED** | Widgets stack side-by-side beautifully in 2 columns. |
| **A1** | AJAX Load More | All | **PASSED** | Complete modularization. Matches server-rendered cards exactly. |
| **T1** | About Page | All | **PASSED** | Custom bio details, responsive profile card, Royal Blue color scheme. |
| **T2** | Contact Page | All | **PASSED** | Zero-border styling, interactive inputs, focus glows active. |

---

## 4. Automation & Parse Validation Log
*   **PHP Lint Check status:** All templates, template parts, and parent files have been structurally parsed and contain no syntax, curly brace, loop termination, or variable leakage errors.
*   **WP Query Security status:** Safe wrapper checks (`wp_reset_postdata()`) are called on all WP queries to prevent active loops from corrupting the global `$post` context.
