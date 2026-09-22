---
name: Vitality Core
colors:
  surface: '#f8f9ff'
  surface-dim: '#d0dbed'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e6eeff'
  surface-container-high: '#dee9fc'
  surface-container-highest: '#d9e3f6'
  on-surface: '#121c2a'
  on-surface-variant: '#3c4a42'
  inverse-surface: '#27313f'
  inverse-on-surface: '#eaf1ff'
  outline: '#6c7a71'
  outline-variant: '#bbcabf'
  surface-tint: '#006c49'
  primary: '#006c49'
  on-primary: '#ffffff'
  primary-container: '#10b981'
  on-primary-container: '#00422b'
  inverse-primary: '#4edea3'
  secondary: '#4059aa'
  on-secondary: '#ffffff'
  secondary-container: '#8fa7fe'
  on-secondary-container: '#1d3989'
  tertiary: '#855300'
  on-tertiary: '#ffffff'
  tertiary-container: '#e29100'
  on-tertiary-container: '#523200'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#6ffbbe'
  primary-fixed-dim: '#4edea3'
  on-primary-fixed: '#002113'
  on-primary-fixed-variant: '#005236'
  secondary-fixed: '#dce1ff'
  secondary-fixed-dim: '#b6c4ff'
  on-secondary-fixed: '#00164e'
  on-secondary-fixed-variant: '#264191'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#121c2a'
  surface-variant: '#d9e3f6'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 60px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 32px
  xl: 48px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 32px
---

## Brand & Style
The design system is engineered for EngixCare to project an aura of clinical precision fused with human-centric warmth. It targets healthcare administrators and practitioners who require high-density data visualization without the cognitive load typically associated with legacy medical software.

The visual style is **Modern Corporate with Glassmorphic Accents**. It utilizes a "Sanctuary" aesthetic—ultra-clean white spaces, soft-focus depth, and a disciplined use of color to highlight critical health indicators. The interface feels light and breathable, reducing user fatigue during long shifts, while the subtle use of translucent layers in navigation provides a premium, state-of-the-art technological feel.

## Colors
This design system utilizes a palette rooted in psychological safety and clarity. 

- **Primary (Healthcare Green):** Used for primary actions, success states, and growth indicators. It represents vitality and the core "Care" mission.
- **Secondary (Deep Trust Blue):** Reserved for global navigation, structural elements, and authoritative headers to ground the interface in professionalism.
- **Accent (Energy Orange):** Used sparingly for alerts, attention-required states, or high-priority vitality metrics.
- **Surface Strategy:** The background is a crisp white to ensure maximum contrast for medical records. Soft Gray is utilized for card backgrounds and layout containers to create a clear "object-on-surface" hierarchy.

## Typography
Inter is selected for its exceptional legibility in data-dense environments. The system prioritizes a tight typographic scale to ensure information density remains manageable.

- **Headlines:** Use Semi-Bold weights with slight negative letter-spacing to create a confident, modern appearance.
- **Body Text:** Standardizes on 14px and 16px for optimal readability of patient records.
- **Labels:** Small, uppercase labels with increased letter-spacing are used for table headers and section overviews to differentiate metadata from primary data.
- **Mobile Adjustments:** For mobile views, `display-lg` and `headline-lg` should scale down by 25% to fit narrow viewports without breaking layout.

## Layout & Spacing
The design system follows an **8px grid system** to maintain mathematical harmony.

- **Layout Model:** A 12-column fluid grid for the main content area. The sidebar is fixed at 280px (expanded) or 80px (collapsed).
- **Desktop:** 32px outer margins with 24px gutters between dashboard cards.
- **Tablet:** 24px margins; cards may stack from 3-columns to 2-columns.
- **Mobile:** 16px margins; all cards reflow to a single-column stack.
- **Rhythm:** Generous internal padding (24px) within cards prevents data from feeling "cluttered," reinforcing the premium healthcare aesthetic.

## Elevation & Depth
Depth is used functionally to separate the navigation, content, and overlay layers.

- **Level 1 (Surface):** The main background (`#F9FAFB`) is the base.
- **Level 2 (Cards):** White cards utilize a very soft, diffused shadow (`0px 4px 20px rgba(31, 41, 55, 0.05)`) to appear slightly lifted.
- **Level 3 (Navigation):** The sidebar uses a **Glassmorphism** effect with a background blur (20px) and 80% opacity of the Secondary Blue or White, creating a sophisticated sense of transparency.
- **Level 4 (Modals):** High-elevation shadows with a dark overlay (40% opacity) for focus-heavy tasks like adding a new patient entry.

## Shapes
The shape language is "Optimistically Rounded." 

- **Small Elements:** Buttons and input fields use a consistent **12px (0.75rem)** radius.
- **Large Elements:** Dashboard cards and modal containers use a more pronounced **16px (1rem)** radius to evoke a soft, friendly, and non-threatening medical environment.
- **Badges:** Status indicators (Success, Pending, Alert) use fully pill-shaped (rounded-full) corners to distinguish them from interactive buttons.

## Components
- **Buttons:** Primary buttons are Healthcare Green with white text. Hover states shift the background to a slightly darker shade. Ghost buttons with 1px borders are used for secondary actions.
- **KPI Cards:** Features a bold `headline-md` value, a `label-sm` title, and a simplified sparkline chart using a 2px stroke in either Primary Green (upward trend) or Energy Orange (downward/critical).
- **Data Tables:** Row heights are generous (56px minimum). Use `border-b` for separation rather than full grids. The header row uses `label-md` styling with a Subtle Gray background.
- **Input Fields:** 12px rounded corners with a 1px `border-color`. On focus, the border transitions to Primary Green with a soft outer glow. Error states use a 1px Red border and 12px helper text.
- **Sidebar:** Icons are 24px modern line icons (2px stroke). Active states are indicated by a vertical 4px bar on the left edge and a subtle background tint.
- **Status Badges:** Subtle, low-opacity background tints of Green, Blue, or Orange with high-contrast text for high readability at a glance.