"use client";

import { useEffect, useId, useRef, useState, type CSSProperties, type MouseEvent, type ReactNode, type RefObject } from "react";
import { ArrowLeftToLine, ArrowRightFromLine, ChevronDown, ChevronRight, PanelsTopLeft } from "lucide-react";
import { gsap } from "gsap";
import { Flip } from "gsap/Flip";
import { useGSAP } from "@gsap/react";
import LiquidWaterTrack from "./liquid-water";

/**
 * Liquid Glass Expandable Sidebar. MIT. Troy Lorents / Automate Flows.
 * A glass sidebar that expands from an icon rail into grouped navigation. The link list scrolls between the header and account so both remain reachable in short frames. Supports nested links, light and dark themes, and reduced motion.
 * Set activeId from your current route. Links work without a client router.
 * Omit theme to use your host's semantic colors. Supply glass and accent to tune the material.
 * CSS glass remains available when SVG backdrop displacement is unavailable.
 */
const navigationStyles = `
.gn-scope {
  --gn-card: var(--color-card, Canvas);
  --gn-ink: var(--color-card-foreground, var(--color-foreground, CanvasText));
  --gn-muted: color-mix(in srgb, var(--gn-ink) 78%, var(--gn-card));
  --gn-accent: var(--color-primary, Highlight);
  --gn-ring: color-mix(in srgb, var(--color-ring, Highlight) 30%, var(--gn-ink));
  --gn-line: var(--color-border, color-mix(in srgb, var(--gn-ink) 25%, var(--gn-card)));
  --gn-tint: var(--gn-card);
  --gn-edge-light: var(--color-glass-highlight, light-dark(Canvas, CanvasText));
  --gn-shadow: var(--color-glass-shadow, light-dark(CanvasText, Canvas));
  --gn-selection: color-mix(in srgb, var(--gn-accent) 15%, var(--gn-card));
  color: var(--gn-ink);
  font: inherit;
  min-inline-size: 0;
  max-inline-size: 100%;
}

.gn-scope[data-gn-theme="light"] { color-scheme: light; }
.gn-scope[data-gn-theme="dark"] { color-scheme: dark; }
.gn-scope[data-gn-theme] { --gn-card: Canvas; --gn-ink: CanvasText; --gn-ring: Highlight; }
.gn-scope *, .gn-scope *::before, .gn-scope *::after { box-sizing: border-box; }
.gn-scope [hidden] { display: none !important; }
.gn-scope a { color: inherit; text-decoration: none; }
.gn-scope button, .gn-scope input { font: inherit; color: inherit; }
.gn-scope button { cursor: pointer; }
.gn-scope :focus-visible {
  outline: 2px solid var(--gn-ring);
  outline-offset: 2px;
  box-shadow: 0 0 0 2px var(--gn-card);
}
.gn-scope ::selection { background: var(--gn-accent); color: var(--color-primary-foreground, HighlightText); }

.gn-material {
  --gn-scrim: 54%;
  --gn-face-light: 7%;
  background:
    linear-gradient(150deg, color-mix(in srgb, var(--gn-edge-light) var(--gn-face-light), transparent), transparent 44%),
    linear-gradient(color-mix(in srgb, var(--gn-card) var(--gn-scrim), transparent), color-mix(in srgb, var(--gn-card) var(--gn-scrim), transparent)),
    color-mix(in srgb, var(--gn-tint) calc(var(--gn-opacity) * 100%), transparent);
  -webkit-backdrop-filter: blur(var(--gn-blur)) saturate(var(--gn-saturation));
  backdrop-filter: blur(var(--gn-blur)) saturate(var(--gn-saturation)) var(--gn-displacement, blur(0px));
  mask-image: var(--gn-lens-mask, none);
  box-shadow:
    inset 0 0 0 .75px color-mix(in srgb, var(--gn-edge-light) calc(var(--gn-edge) * 48%), transparent),
    inset 0 1px 1px color-mix(in srgb, var(--gn-edge-light) calc(var(--gn-edge) * 92%), transparent),
    inset 0 -1px 1px color-mix(in srgb, var(--gn-edge-light) calc(var(--gn-edge) * 60%), transparent),
    inset 0 7px 10px -7px color-mix(in srgb, var(--gn-edge-light) calc(var(--gn-bend) * 60%), transparent),
    inset 0 -7px 10px -7px color-mix(in srgb, CanvasText calc(var(--gn-bend) * 24%), transparent),
    0 12px 24px -15px color-mix(in srgb, var(--gn-shadow) 50%, transparent);
}

.gn-filter-defs { position: absolute; pointer-events: none; }
.gn-sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip-path: inset(50%);
  white-space: nowrap;
}

@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
  .gn-material { background: var(--gn-card); }
}
@media (prefers-reduced-transparency: reduce), (prefers-contrast: more), (forced-colors: active) {
  .gn-material {
    background: var(--gn-card);
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
  }
}
@media (forced-colors: active) {
  .gn-material { border: 1px solid CanvasText; box-shadow: none; }
  .gn-scope :focus-visible { outline-color: Highlight; }
}

.gn-sidebar {
  --gn-muted: color-mix(in srgb, var(--gn-ink) 96%, var(--gn-card));
  --gn-sidebar-limit: max(212px, calc(100svh - var(--gn-sidebar-inset, 56px)));
  position: relative;
  inline-size: 288px;
  max-inline-size: 100%;
  min-block-size: min(480px, var(--gn-sidebar-limit));
  max-block-size: var(--gn-sidebar-limit);
  padding: 12px;
  border-radius: 26px;
  display: flex;
  flex-direction: column;
}
.gn-sidebar-surface {
  --gn-scrim: 66%;
  --gn-face-light: 4%;
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
}
.gn-sidebar > :not(.gn-sidebar-surface) { position: relative; }
.gn-sidebar[data-expanded="false"] { inline-size: 68px; padding-inline: 8px; border-radius: 32px; }
.gn-sidebar :is(button, input, a) { min-block-size: 44px; }
.gn-sidebar :is(button, a) { min-inline-size: 44px; touch-action: manipulation; }
.gn-sidebar button {
  display: grid;
  place-items: center;
  background: transparent;
  border: 0;
  border-radius: 12px;
  inline-size: 44px;
  padding: 0;
  flex: 0 0 44px;
}
.gn-sidebar :is(button, a):hover { background: color-mix(in srgb, var(--gn-card) 20%, transparent); }
.gn-sidebar :is(a, button, input):focus-visible { position: relative; z-index: 1; outline-offset: -2px; box-shadow: none; }
.gn-sidebar svg { inline-size: 17px; block-size: 17px; stroke-width: 1.8; flex: none; }

.gn-sidebar-brand {
  display: flex;
  align-items: center;
  gap: 9px;
  padding-block-end: 12px;
}
.gn-sidebar-brand button { margin-inline-start: auto; }
.gn-sidebar[data-expanded="false"] .gn-sidebar-brand { flex-direction: column; gap: 3px; }
.gn-sidebar[data-expanded="false"] .gn-sidebar-brand button { margin: 0; }
.gn-sidebar-mark, .gn-sidebar-avatar {
  display: grid;
  place-items: center;
  flex: none;
  inline-size: 34px;
  block-size: 34px;
  border-radius: 11px;
  background: #ffffff;
  color: var(--gn-ink);
  box-shadow: inset 0 1px 1px color-mix(in srgb, var(--gn-edge-light) 60%, transparent);
}
.gn-sidebar-mark img { inline-size: 26px; block-size: 26px; object-fit: contain; }
.gn-sidebar-brand-copy, .gn-sidebar-label { min-inline-size: 0; }
.gn-sidebar-brand-copy strong, .gn-sidebar-label strong {
  display: block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-size: .875rem;
  font-weight: 700;
}
.gn-sidebar-brand-copy small, .gn-sidebar-account small {
  display: block;
  margin-block-start: 2px;
  color: var(--gn-muted);
  font-size: .6875rem;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}
.gn-sidebar-body {
  --gn-list-start-fade: 0px;
  --gn-list-end-fade: 0px;
  position: relative;
  flex: 1 1 auto;
  min-block-size: 0;
  /* Keep the water highlight off the panel edges so it never looks clipped. */
  padding-inline: 6px;
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: thin;
  scrollbar-color: color-mix(in srgb, var(--gn-ink) 28%, transparent) transparent;
}
.gn-sidebar-body[data-scrollable="true"] {
  mask-image: linear-gradient(transparent, black var(--gn-list-start-fade),
    black calc(100% - var(--gn-list-end-fade)), transparent);
}
.gn-sidebar-body[data-scroll-start="true"] { --gn-list-start-fade: 12px; }
.gn-sidebar-body[data-scroll-end="true"] { --gn-list-end-fade: 16px; }
.gn-sidebar-body:focus-within { mask-image: none; }
.gn-sidebar[data-expanded="false"] .gn-sidebar-body[data-scrollable="false"] { overflow: visible; }
.gn-sidebar-brand, .gn-sidebar-account { flex-shrink: 0; }
.gn-sidebar-selection {
  position: absolute;
  inset: 0 0 auto;
  height: 44px;
  opacity: 0;
  border-radius: 17px;
  pointer-events: none;
}
.gn-sidebar-water-track {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 0;
  pointer-events: none;
  display: block;
}
.gn-sidebar-droplet, .gn-sidebar-ripple {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  z-index: 10;
}
.gn-sidebar-group + .gn-sidebar-group {
  margin-block-start: 9px;
  padding-block-start: 9px;
  border-block-start: 1px solid var(--gn-line);
}
.gn-sidebar-group p { margin: 0 9px 3px; color: var(--gn-muted); font-size: .6875rem; font-weight: 650; transition: opacity .4s ease, transform .4s ease; }
.gn-sidebar-disclosure { transition: transform .4s ease; }
.gn-sidebar-disclosure[aria-expanded="true"] svg { transform: rotate(0deg); }
.gn-sidebar-row a, .gn-sidebar-children a, .gn-sidebar-account {
  display: flex;
  align-items: center;
  position: relative;
  gap: 9px;
  padding-inline: 10px;
  border-radius: 15px;
  min-inline-size: 0;
  transition: background-color .3s ease, color .3s ease, box-shadow .3s ease, transform .3s ease;
}
.gn-sidebar-row a { flex: 1; }
.gn-sidebar-icon { position: relative; display: grid; place-items: center; flex: 0 0 18px; }
.gn-sidebar-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .8125rem; }
.gn-sidebar a[data-selected="true"] {
  font-weight: 750;
  background: color-mix(in srgb, var(--gn-selection) calc(var(--gn-strength) * 80%), transparent);
}
.gn-sidebar-body[data-water="on"] a[data-selected="true"] { background: transparent; }
.gn-sidebar-row a:hover, .gn-sidebar-children a:hover { transform: translateX(3px); }
.gn-sidebar-row a:active, .gn-sidebar-children a:active, .gn-sidebar-account:active { transform: scale(.97); transition-duration: .08s; }
.gn-sidebar-children { animation: gn-item-in .5s ease both; }
@keyframes gn-item-in {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: none; }
}
.gn-sidebar-group .gn-sidebar-children {
  margin-inline-start: 18px;
  border-inline-start: 1px solid var(--gn-line);
  padding-inline-start: 5px;
}
.gn-sidebar-children a { padding-inline-start: 8px; }
.gn-sidebar-children svg { inline-size: 14px; block-size: 14px; }
.gn-sidebar-badge { inline-size: 6px; block-size: 6px; flex: none; border-radius: 50%; background: var(--gn-accent); }
.gn-sidebar-empty { margin: 0; padding: 10px 4px; color: var(--gn-muted); font-size: .8125rem; }
.gn-sidebar-account {
  min-block-size: 54px;
  margin-block-start: 12px;
  padding: 8px 2px 0;
  border-block-start: 1px solid var(--gn-line);
  border-radius: 0 0 14px 14px;
}
.gn-sidebar-account[aria-current] { background: var(--gn-selection); }
.gn-sidebar-avatar { border-radius: 50%; font-size: .6875rem; font-weight: 750; }
.gn-sidebar-account > svg { margin-inline-start: auto; }
.gn-sidebar[data-expanded="false"] :is(.gn-sidebar-row a, .gn-sidebar-account) { justify-content: center; padding-inline: 0; }
.gn-sidebar[data-expanded="false"] .gn-sidebar-label {
  display: none;
  position: absolute;
  inset-inline-start: calc(100% + 8px);
  z-index: 2;
  inline-size: max-content;
  max-inline-size: 144px;
  padding: 6px 9px;
  background: var(--gn-card);
  border-radius: 7px;
  box-shadow: 0 4px 12px color-mix(in srgb, var(--gn-shadow) 18%, transparent);
}
.gn-sidebar[data-expanded="false"] a:is(:hover, :focus-visible) .gn-sidebar-label { display: block; }

@media (forced-colors: active) {
  .gn-sidebar a { color: LinkText; }
  .gn-sidebar a[data-selected="true"], .gn-sidebar-account[aria-current] { border: 1px solid Highlight; }
}

/* Icon scenes, ported from Nimbus UI · Liquid Navbar. The active item replays once. */
.gn-sidebar-icon[data-fx] svg { transition: transform .45s cubic-bezier(.3,1.6,.5,1); }
.gn-sidebar-icon[data-fx="pop"] svg { animation: nvPop .5s cubic-bezier(.3,1.6,.5,1) both; }
@keyframes nvPop { 0%{transform:scale(.72)}60%{transform:scale(1.2)}100%{transform:translateY(-1px) scale(1.08)} }
.gn-sidebar-icon[data-fx="plane"] svg { animation: nvPlane 1.15s cubic-bezier(.55,0,.3,1) both; }
@keyframes nvPlane { 0%{transform:none}12%{transform:translate(-3px,2px) rotate(7deg)}42%{transform:translate(64px,-26px) rotate(-24deg) scale(.9);opacity:0}43%{transform:translate(-60px,16px) rotate(-12deg) scale(.9);opacity:0}58%{opacity:1}80%{transform:translate(3px,-1px) rotate(2deg)}100%{transform:translateY(-1px) scale(1.08)} }
.gn-sidebar-icon[data-fx="plane"]::after { content:"";position:absolute;left:55%;top:48%;height:2px;width:46px;border-radius:2px;transform-origin:0 50%;background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,.95));animation:nvTrail 1.15s ease-out both;pointer-events:none; }
@keyframes nvTrail { 0%,8%{opacity:0;transform:rotate(-22deg) scaleX(0)}30%{opacity:.9;transform:rotate(-22deg) scaleX(1)}55%,100%{opacity:0;transform:rotate(-22deg) translateX(30px) scaleX(.2)} }
.gn-sidebar-icon[data-fx="drop"] svg { animation: nvDrop .95s cubic-bezier(.4,0,.3,1) both;transform-origin:50% 100%; }
@keyframes nvDrop { 0%{transform:translateY(-1px)}26%{transform:translateY(-13px) scale(.88,1.12)}46%{transform:translateY(3px) scale(1.3,.68)}62%{transform:translateY(-3px) scale(.92,1.08)}80%{transform:translateY(0) scale(1.04,.97)}100%{transform:translateY(-1px) scale(1.08)} }
.gn-sidebar-icon[data-fx="drop"]::before,.gn-sidebar-icon[data-fx="drop"]::after { content:"";position:absolute;bottom:0;left:50%;width:4px;height:4px;margin-left:-2px;border-radius:50%;background:currentColor;opacity:0;pointer-events:none; }
.gn-sidebar-icon[data-fx="drop"]::before { animation:nvSplashL .95s ease-out both; }
.gn-sidebar-icon[data-fx="drop"]::after { animation:nvSplashR .95s ease-out both; }
@keyframes nvSplashL { 0%,44%{opacity:0;transform:none}50%{opacity:.9;transform:translate(-5px,-6px)}75%,100%{opacity:0;transform:translate(-11px,1px) scale(.4)} }
@keyframes nvSplashR { 0%,44%{opacity:0;transform:none}50%{opacity:.9;transform:translate(5px,-7px)}75%,100%{opacity:0;transform:translate(12px,1px) scale(.4)} }
.gn-sidebar-icon[data-fx="spin"] svg { animation: nvSpin 1s cubic-bezier(.2,.75,.25,1) both; }
@keyframes nvSpin { 0%{transform:perspective(80px) rotateY(0)}100%{transform:perspective(80px) rotateY(360deg) translateY(-1px) scale(1.08)} }
.gn-sidebar-icon[data-fx="glow"] svg { animation: nvHop .8s cubic-bezier(.3,1.4,.5,1) both;transform-origin:50% 100%; }
@keyframes nvHop { 0%{transform:none}30%{transform:translateY(-5px) scale(1.04,.96)}55%{transform:translateY(0) scale(1.12,.88)}75%{transform:translateY(-2px) scale(.98,1.04)}100%{transform:translateY(-1px) scale(1.08)} }
.gn-sidebar-icon[data-fx="wave"] svg { animation: nvWave .9s ease-in-out both;transform-origin:50% 92%; }
@keyframes nvWave { 0%{transform:none}20%{transform:rotate(-15deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(4deg)}100%{transform:translateY(-1px) scale(1.08)} }
@media (prefers-reduced-motion: reduce) {
  .gn-sidebar-icon svg,.gn-sidebar-icon::before,.gn-sidebar-icon::after { animation:none!important;transition:none }
}
`;

/** Shared geometry keeps the clipped glass, specular light and refraction aligned. */
const notchGeometry = { top: 24, bottom: 78, middle: 51, radius: 27 };
const clampEffect = (value = 1) => Number.isFinite(value) ? Math.max(0, Math.min(1.5, value)) : 1;
const notchDepth = (intensity = 1) => 26 + clampEffect(intensity) * 6;
function notchScoop(width: number, count: number, center: number) {
  return Math.max(1, Math.min(48, (width - 24) / Math.max(1, count) * 0.72, center - 4, width - center - 4));
}
function notchedPath(width: number, count: number, center: number, depth: number) {
  const { top, bottom, middle, radius } = notchGeometry;
  const scoop = notchScoop(width, count, center);
  const start = Math.min(radius, center - scoop), end = Math.max(width - radius, center + scoop);
  let path = `M${start} ${top} H${center - scoop}`;
  for (let step = 1; step <= 48; step++) {
    const t = step / 24 - 1;
    path += ` L${(center + t * scoop).toFixed(2)} ${(top + depth * (1 - t*t)**2).toFixed(2)}`;
  }
  return `${path} H${end} Q${width} ${top} ${width} ${middle} Q${width} ${bottom} ${width-radius} ${bottom} H${radius} Q0 ${bottom} 0 ${middle} Q0 ${top} ${start} ${top} Z`;
}

const clearNavigationGlass = {
  blur: 2,
  opacity: 0.08,
  saturation: 1.3,
  edge: 0.85,
  distortion: 28,
};

type GlassSettings = {
  blur: number;
  opacity: number;
  saturation: number;
  tint: string;
  edge: number;
  distortion: number;
};

type GlassNavItem = {
  id: string;
  label: string;
  href: string;
  icon: ReactNode;
  /** The little scene the icon plays when tapped: pop, plane, spin, glow, wave, drop. */
  fx?: string;
  /** Spoken as well as shown. For example, "2 unread messages". */
  badge?: string;
};

type NavigationGlassProps = {
  /** Optional material overrides. Defaults are clear glass. */
  glass?: Partial<GlassSettings>;
  /** Omitting this inherits the host's scheme and semantic CSS variables. */
  theme?: "light" | "dark";
  /** A CSS color override for the selection accent. */
  accent?: string;
  /** Motion defaults to on, subject to the visitor's reduced-motion setting. */
  motion?: boolean;
  effects?: { speed?: number };
  className?: string;
  style?: CSSProperties;
};

type GlassNavProps = NavigationGlassProps & {
  items: GlassNavItem[];
  /** The current route ID. Omit it when no link matches the current page. */
  activeId?: string;
  label?: string;
  /** Controls contour lift, beam strength or notch depth for the chosen variant. */
  effectIntensity?: number;
  /** For client routers. Call event.preventDefault() to handle the link yourself. */
  onNavigate?: (item: GlassNavItem, event: MouseEvent<HTMLAnchorElement>) => void;
};

// ponytail: kept from upstream for optional notched/dock variants; unused by the shipped sidebar.
void notchDepth;
void notchedPath;
void clearNavigationGlass;
export type { GlassNavProps };

function isPlainNavigation(event: MouseEvent<HTMLAnchorElement>) {
  return !event.defaultPrevented && event.button === 0 &&
    !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey &&
    event.currentTarget.target !== "_blank";
}

const range = (value: number | undefined, fallback: number, min: number, max: number) =>
  typeof value === "number" && Number.isFinite(value) ? Math.min(max, Math.max(min, value)) : fallback;

const navigationSpeed = (speed?: number) => range(speed, 1, 0.5, 2);

function useNavigationReducedMotion(motion: boolean) {
  const [reduced, setReduced] = useState(() =>
    typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches
  );
  useEffect(() => {
    const media = window.matchMedia("(prefers-reduced-motion: reduce)");
    const update = () => setReduced(media.matches);
    media.addEventListener("change", update);
    update();
    return () => media.removeEventListener("change", update);
  }, []);
  return reduced || !motion;
}

const svgNamespace = "http://www.w3.org/2000/svg";

function roundedDistance(x: number, y: number, halfWidth: number, halfHeight: number, radius: number) {
  const dx = Math.abs(x) - halfWidth + radius;
  const dy = Math.abs(y) - halfHeight + radius;
  return Math.hypot(Math.max(dx, 0), Math.max(dy, 0)) + Math.min(Math.max(dx, dy), 0) - radius;
}

/**
 * Build an edge displacement map from the measured glass shape. The map is
 * regenerated after resize, a material edit, or a completed notch transition.
 * CSS blur and highlights provide the material when SVG backdrop filters are unavailable.
 */
function useNavigationRefraction(scope: RefObject<HTMLDivElement | null>, distortion: number) {
  const id = useId().replace(/[^a-zA-Z0-9]/g, "");
  useEffect(() => {
    const root = scope.current;
    const svgBackdrop = /Chrome|Chromium|Edg/.test(navigator.userAgent) && !/CriOS/.test(navigator.userAgent);
    if (!root || distortion === 0 || !svgBackdrop || !CSS.supports("backdrop-filter", "url(#lens)")) return;

    const preference = window.matchMedia("(prefers-reduced-transparency: reduce), (prefers-contrast: more), (forced-colors: active)");
    const svg = document.createElementNS(svgNamespace, "svg");
    svg.setAttribute("class", "gn-filter-defs");
    svg.setAttribute("width", "0");
    svg.setAttribute("height", "0");
    svg.setAttribute("aria-hidden", "true");
    const definitions = document.createElementNS(svgNamespace, "defs");
    svg.append(definitions);
    root.append(svg);

    let frame = 0;
    const surfaces = Array.from(root.querySelectorAll<HTMLElement>("[data-gn-lens]"));
    const signatures = new Map<HTMLElement, string>();
    const clear = (surface: HTMLElement) => {
      surface.style.removeProperty("--gn-displacement");
      surface.style.removeProperty("--gn-lens-mask");
      signatures.delete(surface);
    };

    function build() {
      frame = 0;
      for (const [index, surface] of surfaces.entries()) {
        if (preference.matches || !surface.offsetWidth || !surface.offsetHeight) {
          clear(surface);
          continue;
        }
        const width = surface.offsetWidth;
        const height = surface.offsetHeight;
        const rail = surface.closest<HTMLElement>('[data-variant="notched"][data-shaped="true"]');
        const notched = rail && surface.dataset.gnLens === "rail";
        const center = Number(rail?.dataset.center ?? width / 2);
        const depth = Number(rail?.dataset.notchDepth ?? 32);
        const scoop = notchScoop(width, rail?.querySelectorAll(".gn-rail-link").length ?? 1, center);
        const radius = Math.min(parseFloat(getComputedStyle(surface).borderTopLeftRadius) || 26, width / 2, height / 2);
        const signature = [width, height, radius, notched ? center : 0, notched ? depth : 0].join(":");
        if (signatures.get(surface) === signature) continue;

        const map = document.createElement("canvas");
        map.width = Math.min(224, Math.ceil(width / 2));
        map.height = Math.min(256, Math.ceil(height / 2));
        const ctx = map.getContext("2d");
        if (!ctx) continue;
        const image = ctx.createImageData(map.width, map.height);
        const bevel = Math.min(18, height / 4);
        const distance = notched
          ? (x: number, y: number) => {
              const offset = Math.min(Math.abs(x - center) / scoop, 1);
              const bowl = (1 - offset * offset) ** 2;
              const { middle, radius: edge, top } = notchGeometry;
              return Math.max(roundedDistance(x - width / 2, y - middle, width / 2, edge, edge), top + depth * bowl - y);
            }
          : (x: number, y: number) => roundedDistance(x - width / 2, y - height / 2, width / 2, height / 2, radius);

        for (let row = 0; row < map.height; row++) {
          for (let column = 0; column < map.width; column++) {
            const x = (column + 0.5) * width / map.width;
            const y = (row + 0.5) * height / map.height;
            const d = distance(x, y);
            const bend = d < 0 ? Math.sin(Math.min(1, -d / bevel) * Math.PI) : 0;
            const dx = distance(x + 1, y) - distance(x - 1, y);
            const dy = distance(x, y + 1) - distance(x, y - 1);
            const length = Math.hypot(dx, dy) || 1;
            const pixel = (row * map.width + column) * 4;
            image.data[pixel] = 128 + dx / length * bend * 110;
            image.data[pixel + 1] = 128 + dy / length * bend * 110;
            image.data[pixel + 2] = 128;
            image.data[pixel + 3] = 255;
          }
        }

        ctx.putImageData(image, 0, 0);
        const filterId = `gn-optics-${id}-${index}`;
        const maskId = `${filterId}-boundary`;
        const filter = document.createElementNS(svgNamespace, "filter");
        const lens = document.createElementNS(svgNamespace, "feImage");
        const displacement = document.createElementNS(svgNamespace, "feDisplacementMap");
        const attributes = (element: SVGElement, values: Record<string, string>) => {
          for (const [key, value] of Object.entries(values)) element.setAttribute(key, value);
        };
        attributes(filter, { id: filterId, x: "0", y: "0", width: "100%", height: "100%", "color-interpolation-filters": "sRGB" });
        attributes(lens, { href: map.toDataURL(), width: "100%", height: "100%", preserveAspectRatio: "none", result: "lens" });
        attributes(displacement, { in: "SourceGraphic", in2: "lens", scale: String(distortion), xChannelSelector: "R", yChannelSelector: "G" });
        filter.append(lens, displacement);
        definitions.querySelector(`#${filterId}`)?.remove();
        definitions.append(filter);

        // Mask the composite result, including pixels displaced past a rounded corner.
        // The animated notch already owns its mask so its glass follows the live path.
        definitions.querySelector(`#${maskId}`)?.remove();
        if (notched) {
          surface.style.removeProperty("--gn-lens-mask");
        } else {
          const bounds = document.createElementNS(svgNamespace, "mask");
          const shape = document.createElementNS(svgNamespace, "rect");
          attributes(bounds, {
            id: maskId, maskUnits: "userSpaceOnUse", maskContentUnits: "userSpaceOnUse",
            x: "0", y: "0", width: String(width), height: String(height),
          });
          attributes(shape, { width: String(width), height: String(height), rx: String(radius), fill: "white" });
          bounds.append(shape);
          definitions.append(bounds);
          surface.style.setProperty("--gn-lens-mask", `url(#${maskId})`);
        }

        surface.style.setProperty("--gn-displacement", `url(#${filterId})`);
        signatures.set(surface, signature);
      }
    }

    const schedule = () => {
      if (!frame) frame = requestAnimationFrame(build);
    };
    const observer = new ResizeObserver(schedule);
    surfaces.forEach(surface => observer.observe(surface));
    preference.addEventListener("change", schedule);
    root.addEventListener("glass:settled", schedule);
    schedule();
    return () => {
      cancelAnimationFrame(frame);
      observer.disconnect();
      preference.removeEventListener("change", schedule);
      root.removeEventListener("glass:settled", schedule);
      surfaces.forEach(clear);
      svg.remove();
    };
  }, [scope, id, distortion]);
}

function NavigationGlass({
  glass, theme, accent, motion = true, className = "", style, children,
}: NavigationGlassProps & { children: ReactNode }) {
  const root = useRef<HTMLDivElement>(null);
  const material = clearNavigationGlass;
  const distortion = range(glass?.distortion, material.distortion, 0, 60);
  useNavigationRefraction(root, distortion);
  const materialStyle = {
    "--gn-blur": `${range(glass?.blur, material.blur, 0, 40)}px`,
    "--gn-opacity": range(glass?.opacity, material.opacity, 0.04, 0.95),
    "--gn-saturation": range(glass?.saturation, material.saturation, 0.5, 2),
    "--gn-edge": range(glass?.edge, material.edge, 0, 1),
    "--gn-bend": distortion / 60,
    ...(glass?.tint ? { "--gn-tint": glass.tint } : {}),
    ...(accent ? { "--gn-accent": accent } : {}),
    ...style,
  } as CSSProperties;
  return (
    <div ref={root} className={`gn-scope ${className}`} data-gn-theme={theme} data-motion={motion ? "on" : "off"} style={materialStyle}>
      <style>{navigationStyles}</style>
      {children}
    </div>
  );
}

gsap.registerPlugin(Flip, useGSAP);

type GlassSidebarItem = GlassNavItem & { children?: GlassNavItem[] };
type GlassNavGroup = { label: string; items: GlassSidebarItem[] };
type ExpandableGlassSidebarProps = NavigationGlassProps & {
  groups: GlassNavGroup[];
  activeId?: string;
  brand?: string;
  brandDetail?: string;
  brandIcon?: ReactNode;
  /** An id opts the account link into active state and onNavigate. */
  account?: { id?: string; name: string; detail: string; initials: string; href: string };
  expanded?: boolean;
  defaultExpanded?: boolean;
  selectionLight?: number;
  onExpandedChange?: (expanded: boolean) => void;
  onNavigate?: (item: GlassNavItem, event: MouseEvent<HTMLAnchorElement>) => void;
};

const DEFAULT_FX: Record<string, string> = {
  dashboard: "glow",
  transaksi: "plane",
  users: "wave",
  tarif: "drop",
  area: "spin",
  kendaraan: "pop",
  log: "wave",
  laporan: "glow",
};

function LiquidGlassExpandableSidebar({
  groups, activeId, brand = "Workspace", brandDetail, brandIcon, account, expanded: controlled,
  defaultExpanded = true, onExpandedChange, onNavigate, motion = true, selectionLight = 1, effects, ...material
}: ExpandableGlassSidebarProps) {
  const [localExpanded, setLocalExpanded] = useState(defaultExpanded);
  const [closedGroups, setClosedGroups] = useState<Set<string>>(() => new Set());
  const expanded = controlled ?? localExpanded;
  const root = useRef<HTMLElement>(null);
  const body = useRef<HTMLDivElement>(null);
  const toggleButton = useRef<HTMLButtonElement>(null);
  const bodyId = useId();
  const flip = useRef<ReturnType<typeof Flip.getState> | null>(null);
  const reduced = useNavigationReducedMotion(motion);
  const speed = navigationSpeed(effects?.speed);

  const hasResults = groups.some(group => group.items.length > 0);

  const toggle = () => {
    if (root.current && !reduced) flip.current = Flip.getState(root.current);
    if (controlled === undefined) setLocalExpanded(!expanded);
    onExpandedChange?.(!expanded);
  };
  const toggleGroup = (id: string) => {
    setClosedGroups(current => {
      const next = new Set(current);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };
  const navigate = (item: GlassNavItem, event: MouseEvent<HTMLAnchorElement>) => {
    if (!isPlainNavigation(event)) return;
    // Press feedback only; the app router prevents default and swaps the page.
    if (!reduced) {
      gsap.fromTo(event.currentTarget, { scale: .96 }, {
        scale: 1,
        duration: .16,
        ease: "power2.out",
        clearProps: "transform",
      });
    }
    onNavigate?.(item, event);
  };

  useEffect(() => {
    if (expanded) return;
    if (body.current?.contains(document.activeElement) &&
      (document.activeElement instanceof HTMLButtonElement || document.activeElement?.closest(".gn-sidebar-children"))) {
      toggleButton.current?.focus();
    }
  }, [expanded]);

  useGSAP(() => {
    const snapshot = flip.current;
    flip.current = null;
    if (snapshot && root.current && !reduced) {
      Flip.from(snapshot, { targets: root.current, duration: .5 / speed, ease: "power2.inOut", scale: false });
    } else if (reduced && root.current) {
      Flip.killFlipsOf(root.current, true);
    }
  }, { scope: root, dependencies: [expanded, reduced, speed] });

  useEffect(() => {
    const list = body.current;
    if (!list) return;
    const updateScrollEdges = () => {
      const remaining = list.scrollHeight - list.clientHeight;
      list.dataset.scrollable = String(remaining > 1);
      list.dataset.scrollStart = String(list.scrollTop > 1);
      list.dataset.scrollEnd = String(remaining - list.scrollTop > 1);
    };
    updateScrollEdges();
    const resize = new ResizeObserver(updateScrollEdges);
    resize.observe(list);
    list.addEventListener("scroll", updateScrollEdges, { passive: true });
    return () => {
      resize.disconnect();
      list.removeEventListener("scroll", updateScrollEdges);
    };
  }, [activeId, expanded, closedGroups, groups]);

  const currentAccount = account?.id !== undefined && account.id === activeId;
  return (
    <NavigationGlass {...material} motion={motion}>
      <nav className="gn-sidebar" ref={root} data-expanded={expanded}
        aria-label={`${brand} navigation`} style={{ "--gn-strength": clampEffect(selectionLight) } as CSSProperties}>
        <div className="gn-sidebar-surface gn-material" data-gn-lens="sidebar" aria-hidden="true" />
        <div className="gn-sidebar-brand">
          <span className="gn-sidebar-mark" aria-hidden="true">{brandIcon ?? <PanelsTopLeft />}</span>
          {expanded && <div className="gn-sidebar-brand-copy"><strong>{brand}</strong>{brandDetail && <small>{brandDetail}</small>}</div>}
          <button type="button" ref={toggleButton} aria-expanded={expanded} aria-controls={bodyId}
            aria-label={expanded ? "Collapse navigation" : "Expand navigation"} onClick={toggle}>
            {expanded ? <ArrowLeftToLine aria-hidden="true" /> : <ArrowRightFromLine aria-hidden="true" />}
          </button>
        </div>
        <div className="gn-sidebar-body" ref={body} id={bodyId}>
          <LiquidWaterTrack activeId={activeId} listRef={body} sound />
          {groups.filter(group => group.items.length > 0).map(group => (
            <div className="gn-sidebar-group" key={group.label}>
              {expanded && <p>{group.label}</p>}
              <ul aria-label={group.label}>
                {group.items.map(item => {
                  const children = item.children;
                  const open = expanded && !closedGroups.has(item.id);
                  const childIsCurrent = item.children?.some(child => child.id === activeId);
                  const current = item.id === activeId;
                  const currentBranch = childIsCurrent && !open;
                  const panelId = `${bodyId}-${encodeURIComponent(item.id)}`;
                  return (
                    <li key={item.id}>
                      <div className="gn-sidebar-row">
                        <a href={item.href} data-selected={current || !!currentBranch}
                          aria-label={item.badge ? `${item.label}, ${item.badge}` : item.label}
                          aria-current={current ? "page" : currentBranch ? "location" : undefined}
                          onClick={event => navigate(item, event)}>
                          <span className="gn-sidebar-icon" aria-hidden="true"
                            data-fx={current ? (item.fx ?? DEFAULT_FX[item.id]) : undefined}
                            key={current ? `fx-${item.id}` : "idle"}>{item.icon}</span>
                          <span className="gn-sidebar-label" aria-hidden="true">{item.label}</span>
                          {expanded && item.badge && <span className="gn-sidebar-badge" aria-hidden="true" />}
                        </a>
                        {expanded && !!children?.length && (
                          <button type="button" className="gn-sidebar-disclosure" aria-label={`${item.label} subpages`}
                            aria-expanded={open} aria-controls={panelId} onClick={() => toggleGroup(item.id)}>
                            <ChevronDown aria-hidden="true" />
                          </button>
                        )}
                      </div>
                      {!!children?.length && (
                        <ul className="gn-sidebar-children" id={panelId} hidden={!open}>
                          {children.map(child => (
                            <li key={child.id}>
                              <a href={child.href} aria-current={child.id === activeId ? "page" : undefined} data-selected={child.id === activeId}
                                onClick={event => navigate(child, event)}>
                                <span className="gn-sidebar-icon" aria-hidden="true"
                                  data-fx={child.id === activeId ? (child.fx ?? DEFAULT_FX[child.id]) : undefined}
                                  key={child.id === activeId ? `fx-${child.id}` : "idle"}>{child.icon}</span>
                                <span className="gn-sidebar-label">{child.label}</span>
                              </a>
                            </li>
                          ))}
                        </ul>
                      )}
                    </li>
                  );
                })}
              </ul>
            </div>
          ))}
          <p role="status" className={hasResults ? "gn-sr-only" : "gn-sidebar-empty"}>
            {!hasResults && "No pages available."}
          </p>
        </div>
        {account && (
          <a className="gn-sidebar-account" href={account.href} aria-label={expanded ? undefined : account.name}
            aria-current={currentAccount ? "page" : undefined}
            onClick={event => { if (account.id) navigate({ id: account.id, label: account.name, href: account.href, icon: null }, event); }}>
            <span className="gn-sidebar-avatar" aria-hidden="true">{account.initials}</span>
            <span className="gn-sidebar-label" aria-hidden={!expanded || undefined}><strong>{account.name}</strong>{" "}<small>{account.detail}</small></span>
            {expanded && <ChevronRight aria-hidden="true" />}
          </a>
        )}
      </nav>
    </NavigationGlass>
  );
}

// ponytail: notch variant props are exported but the shipped sidebar renders the default glass.
export default LiquidGlassExpandableSidebar;
export { LiquidGlassExpandableSidebar };
export type { ExpandableGlassSidebarProps, GlassNavItem, NavigationGlassProps, GlassSettings, GlassNavGroup, GlassSidebarItem };
