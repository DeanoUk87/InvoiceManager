import type { NextConfig } from "next";

// BASE_PATH sets the subpath for this company deployment, e.g. /APC-Overnight
// Leave empty (default) for root deployment
const basePath = process.env.BASE_PATH ?? "";

const nextConfig: NextConfig = {
  basePath,
  // Standalone output bundles everything needed into .next/standalone
  // allowing the app to run with just: node .next/standalone/server.js
  // No npm install needed on the server.
  output: "standalone",
  // Expose basePath to client components via env
  env: {
    NEXT_PUBLIC_BASE_PATH: basePath,
  },
  serverExternalPackages: ["bcryptjs", "nodemailer"],
};

export default nextConfig;
