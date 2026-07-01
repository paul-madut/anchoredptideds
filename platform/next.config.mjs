/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  // Generated hero images + logos are served from Supabase Storage.
  images: { remotePatterns: [{ protocol: 'https', hostname: '**.supabase.co' }] },
};

export default nextConfig;
