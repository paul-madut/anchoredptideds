import IntakeFlow from '@/components/IntakeFlow';
import { PRESETS } from '@/lib/presets';

export const dynamic = 'force-dynamic';

export default function IntakePage() {
  // Presets are static config; pass straight to the client flow.
  return <IntakeFlow presets={PRESETS} />;
}
