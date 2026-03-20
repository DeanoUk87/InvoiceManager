import type { NextConfig } from "next";

// BASE_PATH sets the subpath for this company deployment, e.g. /APC-Overnight
// Leave empty (default) for root deployment
const basePath = process.env.BASE_PATH ?? "";

const nextConfig: NextConfig = {
  basePath,
  // Expose basePath to client components via env
  env: {
    NEXT_PUBLIC_BASE_PATH: basePath,
  },
  serverExternalPackages: ["bcryptjs", "nodemailer"],
};

export default nextConfig;
