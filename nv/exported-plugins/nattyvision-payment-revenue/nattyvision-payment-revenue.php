<?php
/**
 * Plugin Name: NattyVision Payment Method Revenue
 * Description: Per-payment-method breakdown of money collected, refunded, and kept (net), with a date-range filter and per-method order drill-down. Gross = amount captured before processor fees.
 * Version:     1.2.0
 * Author:      NattyVision
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Declare HPOS (High-Performance Order Storage) compatibility. */
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
} );

/* Add the report page under the WooCommerce menu. */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'woocommerce',
		'Payment Method Revenue',
		'Payment Revenue',
		'manage_woocommerce',
		'nv-payment-revenue',
		'nv_payment_revenue_render_page'
	);
} );

/**
 * Pull orders in the date range / statuses and aggregate by payment method.
 *
 * Gross   = sum of order totals (what was captured to Stripe / received by e-transfer, before processor fees).
 * Refund  = sum of refunds issued against those orders (partial or full).
 * Net     = gross - refund (what you actually kept).
 * Each method bucket also keeps a list of its individual orders for the drill-down.
 */
function nv_payment_revenue_get_data( $start, $end, $statuses ) {
	$args = array(
		'type'    => 'shop_order',
		'status'  => $statuses,
		'limit'   => 200,
		'paged'   => 1,
		'orderby' => 'date',
		'order'   => 'ASC',
		'return'  => 'objects',
	);

	$start_ts = $start ? strtotime( $start . ' 00:00:00' ) : 0;
	$end_ts   = $end ? strtotime( $end . ' 23:59:59' ) : 0;
	if ( $start_ts && $end_ts ) {
		$args['date_created'] = $start_ts . '...' . $end_ts;
	} elseif ( $start_ts ) {
		$args['date_created'] = '>=' . $start_ts;
	} elseif ( $end_ts ) {
		$args['date_created'] = '<=' . $end_ts;
	}

	$rows     = array();
	$g_count  = 0;
	$g_gross  = 0.0;
	$g_refund = 0.0;

	do {
		$orders = wc_get_orders( $args );
		foreach ( $orders as $order ) {
			$key   = $order->get_payment_method() ?: 'unknown';
			$title = $order->get_payment_method_title();
			if ( '' === $title ) {
				$title = 'unknown' === $key ? 'No payment method' : $key;
			}

			$gross  = (float) $order->get_total();
			$refund = (float) $order->get_total_refunded();
			$status = $order->get_status(); // e.g. 'processing', 'on-hold'
			$dc     = $order->get_date_created();

			if ( ! isset( $rows[ $key ] ) ) {
				$rows[ $key ] = array(
					'title'  => $title,
					'count'  => 0,
					'gross'  => 0.0,
					'refund' => 0.0,
					'orders' => array(),
				);
			}
			$rows[ $key ]['count']++;
			$rows[ $key ]['gross']  += $gross;
			$rows[ $key ]['refund'] += $refund;
			$rows[ $key ]['orders'][] = array(
				'id'      => $order->get_id(),
				'number'  => $order->get_order_number(),
				'date'    => $dc ? $dc->date_i18n( 'Y-m-d' ) : '—',
				'status'  => $status,
				'status_label' => wc_get_order_status_name( $status ),
				'gross'   => $gross,
				'refund'  => $refund,
				'url'     => $order->get_edit_order_url(),
			);

			$g_count++;
			$g_gross  += $gross;
			$g_refund += $refund;
		}

		$fetched = count( $orders );
		$args['paged']++;
	} while ( $fetched === $args['limit'] );

	// Sort methods by net collected, highest first.
	uasort( $rows, function ( $a, $b ) {
		return ( $b['gross'] - $b['refund'] ) <=> ( $a['gross'] - $a['refund'] );
	} );

	return array(
		'rows'     => $rows,
		'g_count'  => $g_count,
		'g_gross'  => $g_gross,
		'g_refund' => $g_refund,
		'g_net'    => $g_gross - $g_refund,
	);
}

/* Render the admin page. */
function nv_payment_revenue_render_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$start = isset( $_GET['nv_start'] ) ? sanitize_text_field( wp_unslash( $_GET['nv_start'] ) ) : '';
	$end   = isset( $_GET['nv_end'] ) ? sanitize_text_field( wp_unslash( $_GET['nv_end'] ) ) : '';

	$all_statuses = wc_get_order_statuses();
	$default  = array( 'wc-processing', 'wc-completed', 'wc-refunded' );
	$selected = isset( $_GET['nv_status'] ) && is_array( $_GET['nv_status'] )
		? array_map( 'sanitize_text_field', wp_unslash( $_GET['nv_status'] ) )
		: $default;

	$data = nv_payment_revenue_get_data( $start, $end, $selected );
	$rows = $data['rows'];

	// Statuses considered "normal" paid money; anything else gets highlighted in the drill-down.
	$normal = array( 'processing', 'completed' );

	$base = admin_url( 'admin.php?page=nv-payment-revenue' );
	?>
	<div class="wrap">
		<h1>Payment Method Revenue</h1>

		<p style="margin-top:4px;">
			Quick ranges:
			<a href="<?php echo esc_url( add_query_arg( array( 'nv_start' => date_i18n( 'Y-m-01' ), 'nv_end' => date_i18n( 'Y-m-d' ) ), $base ) ); ?>">This month</a> &nbsp;|&nbsp;
			<a href="<?php echo esc_url( add_query_arg( array( 'nv_start' => date_i18n( 'Y-m-d', strtotime( '-30 days' ) ), 'nv_end' => date_i18n( 'Y-m-d' ) ), $base ) ); ?>">Last 30 days</a> &nbsp;|&nbsp;
			<a href="<?php echo esc_url( add_query_arg( array( 'nv_start' => date_i18n( 'Y-01-01' ), 'nv_end' => date_i18n( 'Y-m-d' ) ), $base ) ); ?>">This year</a> &nbsp;|&nbsp;
			<a href="<?php echo esc_url( $base ); ?>">All time</a>
		</p>

		<form method="get" style="background:#fff;border:1px solid #ccd0d4;padding:12px 16px;margin:12px 0;">
			<input type="hidden" name="page" value="nv-payment-revenue" />
			<label>From <input type="date" name="nv_start" value="<?php echo esc_attr( $start ); ?>" /></label>
			&nbsp;
			<label>To <input type="date" name="nv_end" value="<?php echo esc_attr( $end ); ?>" /></label>
			&nbsp;&nbsp;
			<strong>Statuses:</strong>
			<?php foreach ( $all_statuses as $skey => $label ) : ?>
				<label style="margin-right:8px;white-space:nowrap;">
					<input type="checkbox" name="nv_status[]" value="<?php echo esc_attr( $skey ); ?>" <?php checked( in_array( $skey, $selected, true ) ); ?> />
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach; ?>
			&nbsp;
			<button type="submit" class="button button-primary">Run report</button>
		</form>

		<?php
		$range_label = ( $start || $end )
			? esc_html( ( $start ?: '…' ) . ' to ' . ( $end ?: '…' ) )
			: 'All time';
		?>
		<p><em>Showing: <?php echo $range_label; ?></em></p>

		<!-- Quick-read summary -->
		<div style="display:flex;gap:12px;margin:12px 0;flex-wrap:wrap;">
			<div style="background:#fff;border:1px solid #ccd0d4;padding:12px 18px;min-width:170px;">
				<div style="color:#666;font-size:12px;text-transform:uppercase;">Collected (gross)</div>
				<div style="font-size:22px;font-weight:600;"><?php echo wp_kses_post( wc_price( $data['g_gross'] ) ); ?></div>
			</div>
			<div style="background:#fff;border:1px solid #ccd0d4;padding:12px 18px;min-width:170px;">
				<div style="color:#666;font-size:12px;text-transform:uppercase;">Refunded</div>
				<div style="font-size:22px;font-weight:600;color:#b32d2e;">−<?php echo wp_kses_post( wc_price( $data['g_refund'] ) ); ?></div>
			</div>
			<div style="background:#fff;border:1px solid #ccd0d4;padding:12px 18px;min-width:170px;">
				<div style="color:#666;font-size:12px;text-transform:uppercase;">Net kept</div>
				<div style="font-size:22px;font-weight:600;color:#1e7e34;"><?php echo wp_kses_post( wc_price( $data['g_net'] ) ); ?></div>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped" style="max-width:900px;">
			<thead>
				<tr>
					<th>Payment Method</th>
					<th style="text-align:right;">Orders</th>
					<th style="text-align:right;">Gross Collected</th>
					<th style="text-align:right;">Refunded</th>
					<th style="text-align:right;">Net Kept</th>
					<th style="text-align:right;">% of Net</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="6">No orders found for this range / status selection.</td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php $net = $row['gross'] - $row['refund']; ?>
						<tr>
							<td><?php echo esc_html( $row['title'] ); ?></td>
							<td style="text-align:right;"><?php echo (int) $row['count']; ?></td>
							<td style="text-align:right;"><?php echo wp_kses_post( wc_price( $row['gross'] ) ); ?></td>
							<td style="text-align:right;color:<?php echo $row['refund'] > 0 ? '#b32d2e' : '#999'; ?>;">
								<?php echo $row['refund'] > 0 ? '−' . wp_kses_post( wc_price( $row['refund'] ) ) : wp_kses_post( wc_price( 0 ) ); ?>
							</td>
							<td style="text-align:right;font-weight:600;"><?php echo wp_kses_post( wc_price( $net ) ); ?></td>
							<td style="text-align:right;"><?php echo $data['g_net'] > 0 ? esc_html( number_format( $net / $data['g_net'] * 100, 1 ) ) : '0.0'; ?>%</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<?php if ( ! empty( $rows ) ) : ?>
				<tfoot>
					<tr>
						<th>Total</th>
						<th style="text-align:right;"><?php echo (int) $data['g_count']; ?></th>
						<th style="text-align:right;"><?php echo wp_kses_post( wc_price( $data['g_gross'] ) ); ?></th>
						<th style="text-align:right;color:#b32d2e;">−<?php echo wp_kses_post( wc_price( $data['g_refund'] ) ); ?></th>
						<th style="text-align:right;"><?php echo wp_kses_post( wc_price( $data['g_net'] ) ); ?></th>
						<th style="text-align:right;">100%</th>
					</tr>
				</tfoot>
			<?php endif; ?>
		</table>

		<?php if ( ! empty( $rows ) ) : ?>
			<h2 style="margin-top:26px;">Order detail</h2>
			<p style="color:#666;margin-top:-6px;">Click a method to expand its orders. Rows in <span style="background:#fff3cd;padding:1px 5px;">yellow</span> are not Processing/Completed (e.g. On-hold), which is usually where a discrepancy hides.</p>

			<?php foreach ( $rows as $row ) : ?>
				<?php $net = $row['gross'] - $row['refund']; ?>
				<details style="background:#fff;border:1px solid #ccd0d4;margin:8px 0;max-width:900px;">
					<summary style="cursor:pointer;padding:10px 14px;font-weight:600;">
						<?php echo esc_html( $row['title'] ); ?>
						<span style="font-weight:400;color:#666;">
							— <?php echo (int) $row['count']; ?> orders · Net <?php echo wp_kses_post( wc_price( $net ) ); ?>
						</span>
					</summary>
					<table class="wp-list-table widefat striped" style="border:0;">
						<thead>
							<tr>
								<th style="width:80px;">Order</th>
								<th style="width:110px;">Date</th>
								<th>Status</th>
								<th style="text-align:right;">Gross</th>
								<th style="text-align:right;">Refunded</th>
								<th style="text-align:right;">Net</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $row['orders'] as $o ) : ?>
								<?php
								$o_net   = $o['gross'] - $o['refund'];
								$flag    = ! in_array( $o['status'], $normal, true );
								$bg      = $flag ? 'background:#fff3cd;' : '';
								?>
								<tr style="<?php echo esc_attr( $bg ); ?>">
									<td><a href="<?php echo esc_url( $o['url'] ); ?>">#<?php echo esc_html( $o['number'] ); ?></a></td>
									<td><?php echo esc_html( $o['date'] ); ?></td>
									<td><?php echo esc_html( $o['status_label'] ); ?></td>
									<td style="text-align:right;"><?php echo wp_kses_post( wc_price( $o['gross'] ) ); ?></td>
									<td style="text-align:right;color:<?php echo $o['refund'] > 0 ? '#b32d2e' : '#999'; ?>;">
										<?php echo $o['refund'] > 0 ? '−' . wp_kses_post( wc_price( $o['refund'] ) ) : '—'; ?>
									</td>
									<td style="text-align:right;font-weight:600;"><?php echo wp_kses_post( wc_price( $o_net ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</details>
			<?php endforeach; ?>
		<?php endif; ?>

		<p style="color:#666;margin-top:16px;max-width:900px;">
			<strong>Gross Collected</strong> is the full amount captured for each order (what hit Stripe, or what an e-transfer came in at) before payment-processor fees.
			<strong>Refunded</strong> is money sent back, including partial refunds on orders still marked Completed or Processing.
			<strong>Net Kept</strong> = Gross − Refunded. Refunded statuses are included by default so fully-refunded orders are captured; that money still shows as collected and then refunded, netting to zero.
		</p>
	</div>
	<?php
}
