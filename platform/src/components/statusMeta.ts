import type { SiteStatus } from '@/lib/types';

/** Shared status pill styling for the CRM. */
export const STATUS_META: Record<SiteStatus, { label: string; bg: string; fg: string }> = {
  submitted: { label: 'Submitted', bg: '#e5e0d3', fg: '#4a4636' },
  in_review: { label: 'In review', bg: '#dfe6ef', fg: '#274066' },
  approved: { label: 'Approved', bg: '#e0ecdd', fg: '#2c5a3c' },
  building: { label: 'Building…', bg: '#f3e6cf', fg: '#7a5a1e' },
  deploying: { label: 'Deploying…', bg: '#f3e6cf', fg: '#7a5a1e' },
  live: { label: 'Live', bg: '#d5ecd9', fg: '#1f6b3a' },
  failed: { label: 'Failed', bg: '#f0d8d3', fg: '#8a3521' },
};
