# ShopChop Theme Settings

A companion WordPress plugin for **The Splash Shop** — a WooCommerce store for selling pool supplies (chemicals, equipment, accessories, and more). This plugin provides custom Elementor widgets designed specifically for the ShopChop theme.

---

## Requirements

- WordPress 6.0+
- PHP 8.0+
- WooCommerce
- Elementor (free)

---

## What's Inside

All widgets appear in **The Splash Shop** group in the Elementor panel.

| Widget | Description |
|---|---|
| **Hero Carousel** | Full-width slideshow with heading, subheading, CTA button, and background image per slide |
| **Brand Logos** | Responsive logo strip with grayscale-to-colour hover effect |
| **Category Grid** | WooCommerce product category grid with image, label, and link |
| **USP Bar** | Horizontal bar of unique selling points (icon + label) |
| **Products Carousel** | Swiper-powered product carousel queried by featured, sale, new arrivals, category, tag, or hand-picked IDs |
| **Product Spotlight** | Single featured product highlight with image, details, and CTA |
| **Promo Banner** | Full-width promotional banner with background image, heading, and CTA |
| **Testimonials Carousel** | Customer testimonial slider with star rating, quote, and author |
| **Blog Posts Grid** | Latest blog posts in a responsive grid with archive-style cards |

---

## Installation

1. Upload the `shopchop-theme-settings` folder to `/wp-content/plugins/`
2. Activate via **Plugins → Installed Plugins**
3. Widgets appear automatically in Elementor in **The Splash Shop** group

---

## Development

This plugin is tightly coupled to the ShopChop theme. CSS for all widget output lives in the theme's Tailwind stylesheet — the plugin itself ships no styles.

**Plugin slug:** `shopchop-theme-settings`  
**Text domain:** `shopchop-theme-settings`  
**Prefix:** `ShopChop_` (classes), `shopchop_` (functions/hooks)
