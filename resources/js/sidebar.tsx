import React from "react";
import { createRoot } from "react-dom/client";
import {
  ArrowLeftRight,
  BarChart3,
  Car,
  LayoutDashboard,
  MapPin,
  PanelsTopLeft,
  ScrollText,
  Users,
  Wallet,
} from "lucide-react";
import LiquidGlassExpandableSidebar, {
  type GlassNavGroup,
} from "@/components/ui/liquid-glass-expandable-sidebar";

const icons: Record<string, React.ReactNode> = {
  dashboard: <LayoutDashboard />,
  switch: <ArrowLeftRight />,
  users: <Users />,
  cash: <Wallet />,
  pin: <MapPin />,
  car: <Car />,
  log: <ScrollText />,
  chart: <BarChart3 />,
};

type SidebarItem = { id: string; label: string; href: string; icon: string; badge?: string; fx?: string };
type SidebarConfig = {
  brand?: string;
  brandDetail?: string;
  activeId?: string | null;
  groups?: { label: string; items: SidebarItem[] }[];
};

function Sidebar({ config }: { config: SidebarConfig }) {
  const [activeId, setActiveId] = React.useState(config.activeId ?? null);

  React.useEffect(() => {
    // The app router announces each menu switch so the marker slides without a remount.
    const onNavigate = (event: Event) => {
      const detail = (event as CustomEvent<{ activeId?: string | null }>).detail;
      setActiveId(detail?.activeId ?? null);
    };
    window.addEventListener("app:navigate", onNavigate);
    return () => window.removeEventListener("app:navigate", onNavigate);
  }, []);

  const groups: GlassNavGroup[] = (config.groups ?? []).map((group) => ({
    label: group.label,
    items: group.items.map((item) => ({
      id: item.id,
      label: item.label,
      href: item.href,
      icon: icons[item.icon] ?? <PanelsTopLeft />,
      badge: item.badge,
      fx: item.fx,
    })),
  }));

  return (
    <LiquidGlassExpandableSidebar
      groups={groups}
      activeId={activeId ?? undefined}
      brand={config.brand ?? "Parkir Kantor"}
      brandDetail={config.brandDetail}
      brandIcon={<PanelsTopLeft />}
      glass={{ blur: 8, opacity: 0.05, saturation: 1.4, edge: 0.9, distortion: 24 }}
      motion={true}
      effects={{ speed: 1.2 }}
      selectionLight={0.9}
    />
  );
}

const el = document.getElementById("sidebar-root");

if (el) {
  const config: SidebarConfig = JSON.parse(el.dataset.config ?? "{}");
  createRoot(el).render(<Sidebar config={config} />);
}
