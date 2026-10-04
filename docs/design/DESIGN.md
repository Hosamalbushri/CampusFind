# CampusFind — Refero Design System Specification

## 1. Design Overview
CampusFind utilizes a modern, clean, and accessible design system derived from Refero Styles. It provides high clarity for lost-and-found management across university campuses, featuring a distinct deep pine/teal identity, soft sage/mint accents, elevated rounded cards, and full bilingual support (Arabic RTL & English LTR).

---

## 2. Color Palette & Design Tokens

### Primary Brand (Teal / Pine)
- **Primary Default**: `#185c54` / `#1b5c51` (`bg-teal-800` / `brand-primary`)
- **Primary Hover**: `#134942` / `#154a41`
- **Primary Light / Mint**: `#e6f4ee` / `#dff3ea` (Used for pill tags, ambient cards, and subtle highlights)
- **Primary Dark**: `#0c2b27`
- **Accent Green**: `#15803d` / `#16a34a`

### Neutral Grayscale
- **Background (Light)**: `#f8fafc` (Slate 50)
- **Background (Dark)**: `#090d12` / `#030712` (Gray 950)
- **Card Surface (Light)**: `#ffffff` (White with `shadow-sm` or `shadow-xl`)
- **Card Surface (Dark)**: `#111827` / `#0f172a` (Gray 900)
- **Text Main**: `#1e293b` (Slate 800) / `#ffffff` (Dark Mode)
- **Text Secondary**: `#64748b` (Slate 500) / `#9ca3af` (Dark Mode)
- **Borders (Light)**: `#e2e8f0` (Slate 200) / `#f1f5f9` (Slate 100)
- **Borders (Dark)**: `#1f2937` (Gray 800) / `#374151` (Gray 700)

---

## 3. Typography Scale
- **Font Family**: Cairo (`'Cairo', ui-sans-serif, system-ui, sans-serif`)
- **Display Headings**: `text-4xl` to `text-6xl`, `font-black`, `tracking-tight`, `leading-[1.15]`
- **Section Headings**: `text-2xl` to `text-3xl`, `font-extrabold`, `tracking-tight`
- **Card Titles**: `text-base` to `text-lg`, `font-bold`
- **Body Text**: `text-sm` to `text-base`, `leading-relaxed`, `text-slate-600 dark:text-slate-300`
- **Captions & Meta**: `text-xs`, `font-medium`, `text-slate-400`

---

## 4. Spacing & Border Radius System
- **Controls & Buttons**: `rounded-xl` (12px)
- **Standard Cards**: `rounded-2xl` (16px)
- **Hero & Feature Containers**: `rounded-3xl` (24px) to `rounded-[2.5rem]` (40px)
- **Pill Badges**: `rounded-full` (9999px)
- **Vertical Spacing**: `py-10` to `py-16` on sections, `gap-4` to `gap-8` on grids.

---

## 5. UI Component Conventions

1. **Buttons**:
   - `primary`: Deep pine background `#1b5c51`, white bold text, `rounded-xl`, subtle shadow.
   - `secondary`: Crisp white background, slate-200 border, dark text, `rounded-xl`.
   - `ghost`: Transparent background, hover:bg-slate-100 dark:hover:bg-gray-800.

2. **Cards**:
   - Standard card: White / Gray-900 surface, slate-100 / gray-800 border, `rounded-2xl`, smooth hover elevations.
   - Interactive item card: Media preview container with 4:3 aspect ratio, category pill, reference tag, and clean meta details.

3. **Form Inputs**:
   - `rounded-xl`, border-slate-300 dark:border-gray-700, focus ring with primary teal color.

4. **Bilingual / Directional**:
   - Direction-aware chevrons (`rtl:rotate-180`).
   - Symmetrical flex alignment.
