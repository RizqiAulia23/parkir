"use client";

import { useEffect, useRef, type MutableRefObject, type RefObject } from "react";

/**
 * Liquid Water Track for vertical sidebar navigation.
 * Ported from Nimbus UI · Liquid Navbar ("Waterline").
 * 
 * Instead of a rigid highlight, a fluid water drop lives in the sidebar track.
 * When the user switches pages, the active drop stretches toward the new item,
 * thins its neck, splits into floating satellite droplets on long jumps, and
 * merges back with a caustic ripple wave. The cursor lifts a 3D meniscus, and
 * ambient light shimmers with specular caustics and fresnel iridescence.
 */

const VERT = `attribute vec2 a;void main(){gl_Position=vec4(a,0.,1.);}`;

const FRAG = `precision highp float;
uniform vec2 uRes;
uniform float uDpr;
uniform float uTime;
uniform vec4 uBlob[6];
uniform float uSy[6];
uniform vec4 uRip[6];
uniform vec3 uMouse;
uniform float uDark;
uniform float uAmb;

float sdCap(vec2 p, vec4 B, float sy) {
  p -= B.xy;
  p.y /= sy;
  p.x = max(abs(p.x) - B.z, 0.);
  return (length(p) - B.w) * min(1., sy);
}

float smin(float a, float b, float k) {
  float h = clamp(.5 + .5 * (b - a) / k, 0., 1.);
  return mix(b, a, h) - k * h * (1. - h);
}

float dropD(vec2 p) {
  float d = 1e4;
  for (int i = 0; i < 6; i++) {
    if (uBlob[i].w > .5) {
      d = smin(d, sdCap(p, uBlob[i], uSy[i]), 22.);
    }
  }
  return d;
}

float lens(float d, float w) {
  float t = clamp(-d / w, 0., 1.);
  return sqrt(1. - (1. - t) * (1. - t));
}

float waves(vec2 p) {
  float w = 0.;
  for (int i = 0; i < 6; i++) {
    vec4 R = uRip[i];
    if (R.w > 0.) {
      float x = length(p - R.xy) - R.z * 260.;
      w += R.w * exp(-R.z * 1.8) * sin(x * .32) * exp(-x * x / 750.);
    }
  }
  vec2 m = p - uMouse.xy;
  w += uMouse.z * 3.8 * exp(-dot(m, m) / 1100.);
  w += uAmb * (sin(p.x * .06 + uTime * 1.2) * .4 + sin(p.y * .08 - uTime * .9 + p.x * .15) * .25);
  return w;
}

float H(vec2 p) {
  float dd = dropD(p);
  return lens(dd, 14.) * 8.5 + waves(p) * smoothstep(0., 8., -dd);
}

float caus(vec2 p, float t) {
  vec2 q = p;
  float c = 0.;
  for (int i = 0; i < 3; i++) {
    q += vec2(sin(q.y * 1.1 + t), cos(q.x * 1.3 - t * .7)) * .6;
    c += 1. / (1. + 16. * abs(sin(q.x) * sin(q.y)));
  }
  return c / 3.;
}

void main() {
  vec2 p = vec2(gl_FragCoord.x, uRes.y * uDpr - gl_FragCoord.y) / uDpr;
  float dd = dropD(p);
  if (dd > 14.0) { discard; }

  float h0 = H(p);
  vec3 n = normalize(vec3(h0 - H(p + vec2(1., 0.)), h0 - H(p + vec2(0., 1.)), 1.));
  vec3 V = vec3(0., 0., 1.);
  float spec = pow(max(dot(reflect(-normalize(vec3(-.45, -.8, .85)), n), V), 0.), 65.);
  float spec2 = pow(max(dot(reflect(-normalize(vec3(.6, .75, .7)), n), V), 0.), 22.) * .35;
  float fres = clamp((1. - n.z) * 4., 0., 1.);
  vec3 irid = .5 + .5 * cos(6.2831 * (vec3(0., .33, .67) + fres * .85 + p.x * .0028 + uTime * .035));
  float dm = smoothstep(1., -1., dd);
  float sh = smoothstep(8., -8., dropD(p - vec2(0., 4.))) * (1. - dm) * (.14 + .06 * uDark);
  vec3 c = vec3(0., .14, .36) * sh;
  float a = sh;
  float th = lens(dd, 14.);
  float cs = caus((p + n.xy * th * 18.) * .085, uTime * .9);
  vec3 tint = mix(vec3(.75, .92, 1.), vec3(.45, .75, 1.), uDark);
  float rim = dm * (1. - smoothstep(0., 7., -dd));
  float cres = dm * smoothstep(.12, .55, dot(n.xy, normalize(vec2(.55, .85)))) * smoothstep(0., 3., -dd);
  float body = .5 + .5 * clamp(-n.y * 3., -1., 1.);
  float da = dm * (.32 + .18 * body) * (1. - .35 * uDark);
  vec3 dc = mix(tint, vec3(1.), body * .6);
  float la = rim * .25;
  vec3 lc = mix(vec3(0., .42, .85), vec3(.1, .55, .95), uDark);
  dc = lc * la + dc * da * (1. - la);
  da = la + da * (1. - la);
  la = clamp(cres * .75 + smoothstep(.6, 1., cs) * .32 * th * dm, 0., 1.);
  dc = vec3(1.) * la + dc * (1. - la);
  da = la + da * (1. - la);
  la = clamp(dm * (spec * 1.3 + spec2 * .5), 0., 1.);
  dc = vec3(1.) * la + dc * (1. - la);
  da = la + da * (1. - la);
  la = dm * smoothstep(-2., 0., dd) * .45;
  dc = mix(vec3(1.), irid, .4) * la + dc * (1. - la);
  da = la + da * (1. - la);
  c = dc + c * (1. - da);
  a = da + a * (1. - da);
  gl_FragColor = vec4(c, a);
}
`;

type Ctx = { gl: WebGLRenderingContext; u: Record<string, WebGLUniformLocation | null> };

function initGL(canvas: HTMLCanvasElement): Ctx | null {
  const gl = canvas.getContext("webgl", { premultipliedAlpha: true, alpha: true, antialias: false }) as WebGLRenderingContext | null;
  if (!gl) return null;
  const sh = (type: number, src: string) => {
    const s = gl.createShader(type)!;
    gl.shaderSource(s, src);
    gl.compileShader(s);
    return gl.getShaderParameter(s, gl.COMPILE_STATUS) ? s : null;
  };
  const vs = sh(gl.VERTEX_SHADER, VERT);
  const fs = sh(gl.FRAGMENT_SHADER, FRAG);
  if (!vs || !fs) return null;
  const p = gl.createProgram()!;
  gl.attachShader(p, vs);
  gl.attachShader(p, fs);
  gl.linkProgram(p);
  if (!gl.getProgramParameter(p, gl.LINK_STATUS)) return null;
  gl.useProgram(p);
  const buf = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, buf);
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
  const loc = gl.getAttribLocation(p, "a");
  gl.enableVertexAttribArray(loc);
  gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
  const u: Ctx["u"] = {};
  for (const n of ["uRes", "uDpr", "uTime", "uBlob", "uSy", "uRip", "uMouse", "uDark", "uAmb"]) u[n] = gl.getUniformLocation(p, n);
  return { gl, u };
}

type Body = { y: number; v: number };
type Sat = { x: number; y: number; vx: number; vy: number; r: number; k: number };
type Rip = { x: number; y: number; age: number; amp: number };

function plip(audioRef: MutableRefObject<AudioContext | null>) {
  try {
    const AC = window.AudioContext || (window as any).webkitAudioContext;
    if (!AC) return;
    const ctx = (audioRef.current ||= new AC());
    const t = ctx.currentTime;
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.type = "sine";
    o.frequency.setValueAtTime(440 + Math.random() * 70, t);
    o.frequency.exponentialRampToValueAtTime(1400, t + 0.07);
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(0.09, t + 0.008);
    g.gain.exponentialRampToValueAtTime(0.0001, t + 0.16);
    o.connect(g).connect(ctx.destination);
    o.start(t);
    o.stop(t + 0.18);
  } catch {}
}

export type LiquidWaterTrackProps = {
  /** Kept for API parity; selection changes are picked up through the DOM. */
  activeId?: string;
  listRef: RefObject<HTMLDivElement | null>;
  sound?: boolean;
};

export function LiquidWaterTrack({ listRef, sound = true }: LiquidWaterTrackProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const audioRef = useRef<AudioContext | null>(null);

  const st = useRef({
    a: { y: 0, v: 0 } as Body,
    b: { y: 0, v: 0 } as Body,
    sy: { s: 1, v: 0 },
    lead: 0 as 0 | 1,
    lagFrom: 0,
    sats: [] as Sat[],
    rips: [] as Rip[],
    t: { x: 0, y: 0, q: 0, r: 20 },
    q: 0,
    mouse: { x: 0, y: 0, s: 0, on: false },
    moving: false,
    split: false,
    ready: false,
    w: 0,
    h: 0,
    visible: true,
    reduce: false,
    raf: 0,
    last: 0,
    time: 0,
    glc: null as Ctx | null,
  });

  const triggerRipple = (amp = 1.3) => {
    const s = st.current;
    if (s.reduce) return;
    s.rips.push({ x: s.t.x || s.w / 2, y: s.t.y || 44, age: 0, amp });
  };

  useEffect(() => {
    const cv = canvasRef.current;
    const list = listRef.current;
    if (!cv || !list) return;

    const s = st.current;
    s.reduce = !!window.matchMedia?.("(prefers-reduced-motion: reduce)").matches;
    s.glc = initGL(cv);
    list.dataset.water = s.glc ? "on" : "off";

    const lost = (e: Event) => { e.preventDefault(); s.glc = null; list.dataset.water = "off"; };
    cv.addEventListener("webglcontextlost", lost);

    const draw = () => {
      if (!s.glc || !s.w || !s.h) return;
      const { gl: g, u } = s.glc;
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      g.viewport(0, 0, g.drawingBufferWidth, g.drawingBufferHeight);
      g.uniform2f(u.uRes, s.w, s.h);
      g.uniform1f(u.uDpr, dpr);
      g.uniform1f(u.uTime, s.time);

      const blobs = new Float32Array(24);
      const sy = new Float32Array(6).fill(1);
      const T = s.t;
      const sep = Math.abs(s.a.y - s.b.y);
      const thin = Math.min(sep / (2 * (s.q + T.r) || 1), 1);
      const head = s.lead === 0 ? s.a : s.b;
      const tail = s.lead === 0 ? s.b : s.a;

      // Head capsule
      blobs.set([T.x, head.y, s.q * (1 - 0.22 * thin), T.r * (1 - 0.1 * thin)], 0);
      // Tail capsule
      blobs.set([T.x, tail.y, s.q * (1 - 0.65 * thin), T.r * (1 - 0.45 * thin)], 4);
      sy[0] = sy[1] = s.sy.s;

      // Satellite droplets between head and tail
      s.sats.slice(0, 4).forEach((d, i) => {
        blobs.set([d.x, d.y, 0, d.r], 8 + i * 4);
      });

      g.uniform4fv(u.uBlob, blobs);
      g.uniform1fv(u.uSy, sy);

      const rips = new Float32Array(24);
      s.rips.slice(-6).forEach((p, i) => rips.set([p.x, p.y, p.age, p.amp], i * 4));
      g.uniform4fv(u.uRip, rips);

      g.uniform3f(u.uMouse, s.mouse.x, s.mouse.y, s.mouse.s);
      g.uniform1f(u.uDark, 0);
      g.uniform1f(u.uAmb, s.reduce ? 0 : 1);

      g.clearColor(0, 0, 0, 0);
      g.clear(g.COLOR_BUFFER_BIT);
      g.drawArrays(g.TRIANGLES, 0, 3);
    };

    const step = (dt: number, now: number) => {
      const T = s.t;
      s.time += dt;
      s.q += (T.q - s.q) * Math.min(1, dt * 12);
      const bodies = [s.a, s.b];
      let still = true;

      if (s.reduce) {
        s.a.y = s.b.y = T.y;
        s.a.v = s.b.v = 0;
        s.sy.s = 1;
        s.sats = [];
      } else {
        bodies.forEach((B, i) => {
          const lead = i === s.lead;
          const k = lead ? 220 : 75;
          const c = lead ? 26 : 14;
          const f = !lead && now < s.lagFrom ? 0.08 : 1;
          B.v += (k * (T.y - B.y) - c * B.v) * dt * f;
          B.y += B.v * dt;
          if (Math.abs(T.y - B.y) > 0.4 || Math.abs(B.v) > 3) still = false;
        });

        const L = bodies[s.lead];
        const syT = 1 + 0.28 * Math.min(Math.abs(L.v) / 1300, 1);
        s.sy.v += (280 * (syT - s.sy.s) - 10 * s.sy.v) * dt;
        s.sy.s += s.sy.v * dt;
        if (Math.abs(s.sy.s - 1) > 0.002 || Math.abs(s.sy.v) > 0.02) still = false;

        // Break the neck: spawn droplets between head and tail on long jumps
        const sep = Math.abs(s.a.y - s.b.y);
        if (!s.split && s.moving && sep > 52) {
          s.split = true;
          const lo = bodies[1 - s.lead], hi = bodies[s.lead];
          [0.3, 0.52, 0.74].forEach((t, j) => {
            s.sats.push({
              x: T.x + (Math.random() - 0.5) * 14,
              y: lo.y + (hi.y - lo.y) * t + (Math.random() - 0.5) * 8,
              vx: (Math.random() - 0.5) * 45,
              vy: (lo.v + hi.v) * 0.45,
              r: 4.8 + Math.random() * 2.6 - j * 0.5,
              k: 70 + Math.random() * 80,
            });
          });
        }

        s.sats = s.sats.filter((d) => {
          d.vx += (70 * (T.x - d.x) - 7 * d.vx) * dt;
          d.vy += (d.k * (T.y - d.y) - 9 * d.vy) * dt;
          d.x += d.vx * dt;
          d.y += d.vy * dt;
          if (Math.abs(T.y - d.y) < T.r) d.r -= dt * 24;
          return d.r > 0.6;
        });
        if (s.sats.length) still = false;
      }

      if (s.moving && still) {
        s.moving = false;
        // Impact ripple on arrival!
        s.rips.push({ x: T.x, y: T.y, age: 0, amp: s.split ? 1.5 : 0.85 });
      }

      s.rips.forEach((p) => (p.age += dt * 1.35));
      s.rips = s.rips.filter((p) => p.age < 2.4);

      const target = s.mouse.on ? 1 : 0;
      s.mouse.s += (target - s.mouse.s) * Math.min(1, dt * 7);
    };

    const loop = () => {
      if (!s.glc || s.raf) return;
      s.last = performance.now();
      const tick = (now: number) => {
        const dt = Math.min((now - s.last) / 1000, 1 / 30);
        s.last = now;
        step(dt, now);
        draw();
        const busy = s.moving || s.rips.length > 0 || s.sats.length > 0 || s.mouse.s > 0.01;
        if (s.visible && (!s.reduce || busy)) s.raf = requestAnimationFrame(tick);
        else s.raf = 0;
      };
      s.raf = requestAnimationFrame(tick);
    };

    const measure = () => {
      const cvEl = canvasRef.current;
      const listEl = listRef.current;
      if (!cvEl || !listEl) return;

      const fullW = listEl.clientWidth || 236;
      const fullH = Math.max(listEl.clientHeight, listEl.scrollHeight) || 400;

      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      if (cvEl.width !== Math.round(fullW * dpr) || cvEl.height !== Math.round(fullH * dpr)) {
        cvEl.width = Math.round(fullW * dpr);
        cvEl.height = Math.round(fullH * dpr);
        cvEl.style.width = `${fullW}px`;
        cvEl.style.height = `${fullH}px`;
      }
      s.w = fullW;
      s.h = fullH;

      const selected = listEl.querySelector<HTMLElement>('[data-selected="true"]');
      if (!selected) return;

      let top = 0;
      for (let node: HTMLElement | null = selected; node && node !== listEl; node = node.offsetParent as HTMLElement | null) {
        top += node.offsetTop;
      }
      const itemW = selected.offsetWidth || fullW;
      const itemH = selected.offsetHeight || 44;
      const nx = selected.offsetLeft + itemW / 2;
      const ny = top + itemH / 2;

      const T = s.t;
      T.r = Math.min(itemH / 2, 18);
      // Never let the water capsule spill past the canvas, or it looks clipped.
      T.q = Math.max(0, Math.min(itemW / 2 - T.r, nx - T.r - 2, s.w - nx - T.r - 2));

      if (!s.ready) {
        s.a.y = s.b.y = ny;
        s.q = T.q;
        s.ready = true;
      } else if (Math.abs(ny - T.y) > 0.5) {
        s.lead = Math.abs(ny - s.a.y) <= Math.abs(ny - s.b.y) ? 0 : 1;
        s.lagFrom = performance.now() + 65;
        s.moving = true;
        s.split = false;
        if (sound) plip(audioRef);
      }
      T.x = nx;
      T.y = ny;

      loop();
    };

    measure();

    const ro = new ResizeObserver(() => measure());
    ro.observe(list);
    const mo = new MutationObserver(() => measure());
    mo.observe(list, { subtree: true, attributes: true, attributeFilter: ["data-selected"] });

    const onPointerMove = (e: PointerEvent) => {
      if (e.pointerType !== "mouse") return;
      const rect = cv.getBoundingClientRect();
      s.mouse.x = e.clientX - rect.left;
      s.mouse.y = e.clientY - rect.top;
      s.mouse.on = true;
      loop();
    };
    const onPointerLeave = () => {
      s.mouse.on = false;
      loop();
    };

    list.addEventListener("pointermove", onPointerMove);
    list.addEventListener("pointerleave", onPointerLeave);

    const io = new IntersectionObserver(([e]) => {
      s.visible = e.isIntersecting;
      if (s.visible) loop();
    });
    io.observe(list);

    const onVis = () => {
      s.visible = document.visibilityState === "visible";
      if (s.visible) loop();
    };
    document.addEventListener("visibilitychange", onVis);

    return () => {
      ro.disconnect();
      mo.disconnect();
      io.disconnect();
      list.removeEventListener("pointermove", onPointerMove);
      list.removeEventListener("pointerleave", onPointerLeave);
      document.removeEventListener("visibilitychange", onVis);
      cv.removeEventListener("webglcontextlost", lost);
      cancelAnimationFrame(s.raf);
      s.raf = 0;
    };
  }, [listRef, sound]);

  return <canvas className="gn-sidebar-water-track" ref={canvasRef} aria-hidden="true" />;
}

export default LiquidWaterTrack;
