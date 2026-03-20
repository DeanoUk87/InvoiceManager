/**
 * Returns the configured base path (e.g. "/APC-Overnight") or empty string.
 * Use this for callbackUrls and any place where Next.js doesn't auto-prefix.
 */
export const BASE_PATH = process.env.NEXT_PUBLIC_BASE_PATH ?? "";

/**
 * Prefix a path with the base path for use in callbackUrls / fetch calls.
 * Next.js <Link href> and router.push() are handled automatically by basePath config.
 * Only needed for: signOut callbackUrl, fetch() calls to /api/..., window.location
 */
export function withBase(path: string): string {
  if (!BASE_PATH) return path;
  if (path.startsWith(BASE_PATH)) return path;
  return `${BASE_PATH}${path}`;
}
