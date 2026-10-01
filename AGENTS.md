# Instructions for AI Agents (Antigravity, Cursor, Copilot, Claude)

> **Repository:** BBPOM Semarang ITSM (Helpdesk & Layanan TI)  
> **Mandatory Reference:** Read [DESIGN_SYSTEM.md](./DESIGN_SYSTEM.md) before making ANY frontend or UI changes.

---

## 1. Mandatory Rule: Propose `/impeccable` Options Before Building Any UI
Whenever the user asks to build, improve, or modify any user interface, component, or interaction:
1. **DO NOT** immediately dump generic code or basic template forms.
2. **ALWAYS** provide an `/impeccable` design critique first:
   - Propose **2–3 curated concept options** with distinct UX tradeoffs, visual depth (glassmorphism, micro-interactions, elevation, borders), and layout hierarchy.
   - Highlight the **single recommended option** with clear rationale aligned with BBPOM corporate branding (Emerald & Slate palette).
   - Solicit the user's decision before generating the final implementation code.

---

## 2. Zero AI Slop & Emojis Ban
- **NO RAW EMOJIS:** Never output or write emojis in UI markup, Blade templates, notifications, flash messages, or labels (e.g. no 🚀, 🔥, ⚠️, ✅, ❌, 📌, 💡, ⏱️).
- **STRICT REMIX ICON USAGE:** Exclusively use [Remix Icon](https://remixicon.com/) classes (`ri-*`).
- **NO FLAT / BASIC BOXES:** Avoid single-layer flat colored boxes with harsh contrasts. Use subtle gradients, soft borders (`border-slate-200/80` or `border-emerald-500/20`), soft shadows (`shadow-sm`, `shadow-xl`), and frosted glass backdrops (`backdrop-blur-md bg-slate-900/60`).

---

## 3. Strict Atomic Design Pattern
- Every shared or modular UI element **MUST** be placed in `resources/views/components/` as a Blade atomic component (e.g. `<x-draft-recovery-alert />`, `<x-leave-form-confirmation-modal />`, `<x-sla-info-card />`).
- **NEVER** duplicate multi-line modal, stepper, or badge code directly inside page views.

---

## 4. Alpine.js & HTML Parser Safety
- **NEVER** write comparison operators (`>`, `<`, `&&`) inside inline HTML attributes like `<div x-data="{ ... length > 0 ... }">`. The browser's HTML parser will treat `>` as tag termination, causing syntax truncation and leaking raw JavaScript into the user's screen.
- Always declare complex state in a dedicated function inside a `<script>` tag (e.g. `function ticketGatewayData() { return { ... }; }`).

---

## 5. Verification Protocol
Before finishing any task, you must run and verify:
```bash
npm run build
php artisan test
```
All tests must pass (100%), assets must build without errors, and no git diff should contain unescaped emojis or leaked script tags.
