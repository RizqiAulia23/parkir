import React from "react";
import { createRoot } from "react-dom/client";
import Index from "@/components/ui/travel-connect-signin-1";

type LoginConfig = {
  action?: string;
  csrfToken?: string;
  errors?: Record<string, string>;
  oldUsername?: string;
  title?: string;
  subtitle?: string;
  brand?: string;
};

const el = document.getElementById("login-root");

if (el) {
  const config: LoginConfig = JSON.parse(el.dataset.config ?? "{}");
  createRoot(el).render(<Index {...config} />);
}
