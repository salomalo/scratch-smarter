<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @package   WC-Memberships/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Subscription-tied membership plans handler.
 *
 * @since 1.8.0
 */
class WC_Memberships_Integration_Subscriptions_Membership_Plans {


	/**
	 * Handler constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		// helper object for subscription-tied membership plans
		require( wc_memberships()->get_plugin_path() . '/includes/integrations/subscriptions/class-wc-memberships-integration-subscriptions-membership-plan.php' );

		// init hooks that need to be executed early
		add_action( 'init', array( $this, 'init' ) );

		// handle granting access from a subscription product
		add_filter( 'wc_memberships_access_granting_purchased_product_id',               array( $this, 'adjust_access_granting_product_id' ), 10, 3 );
		add_action( 'wc_memberships_grant_membership_access_from_purchase',              array( $this, 'save_subscription_data' ), 10, 2 );
		add_filter( 'wc_memberships_grant_access_from_new_purchase',                     array( $this, 'maybe_grant_access_from_new_subscription' ), 10, 2 );
		add_filter( 'wc_memberships_grant_access_from_existing_purchase',                array( $this, 'maybe_grant_access_from_existing_subscription' ), 10, 2 );
		add_filter( 'wc_memberships_granted_access_from_existing_purchase',              array( $this, 'maybe_grant_access_from_existing_manual_subscription' ), 10, 3 );
		add_filter( 'wc_memberships_grant_access_from_existing_purchase_order_statuses', array( $this, 'grant_access_from_active_subscription' ) );
	}


	/**
	 * Initializes early hooks.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 */
	public function init() {

		add_filter( 'wc_memberships_membership_plan', array( $this, 'get_membership_plan' ), 2, 3 );
	}


	/**
	 * Filters a Membership Plan to return a subscription-tied Membership Plan.
	 *
	 * This method is a filter callback and should not be used directly.
	 * @see \wc_memberships_get_membership_plan() instead.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan
	 * @param null|\WP_Post $membership_plan_post the membership plan post object
	 * @param null|\WC_Memberships_User_Membership $user_membership the user membership object
	 * @return \WC_Memberships_Integration_Subscriptions_Membership_Plan|\WC_Memberships_Membership_Plan
	 */
	public function get_membership_plan( $membership_plan, $membership_plan_post = null, $user_membership = null ) {

		// We can't filter directly $membership_plan:
		// it may have both regular products and subscription products that grant access;
		// instead, the user membership type will tell the type of purchase.
		return wc_memberships_has_subscription_granted_access( $user_membership ) ? new WC_Memberships_Integration_Subscriptions_Membership_Plan( $membership_plan->post ) : $membership_plan;
	}


	/**
	 * Checks whether a membership plan can be accessed when a subscription is active
	 *
	 * @since 1.8.0
	 *
	 * @param int|WC_Memberships_Membership_Plan $plan_id Membership Plan ID or object
	 * @return bool true, if access is allowed, false otherwise
	 */
	public function grant_access_while_subscription_active( $plan_id ) {

		$plan_id = $plan_id instanceof WC_Memberships_Membership_Plan ? $plan_id->get_id() : $plan_id;

		/**
		 * Filter whether a plan grants access to a membership while subscription is active.
		 *
		 * @since 1.6.0
		 * @param bool $grants_access Default: true.
		 * @param int $plan_id Membership Plan ID.
		 */
		return apply_filters( 'wc_memberships_plan_grants_access_while_subscription_active', true, $plan_id );
	}


	/**
	 * Adjusts the product ID that grants access to a membership plan on purchase.
	 *
	 * Subscription products take priority over all other products.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param int $product_id Product ID
	 * @param array $access_granting_product_ids array of product IDs in the purchase order
	 * @param \WC_Memberships_Membership_Plan $plan Membership Plan to access
	 * @return int ID of the Subscription product that grants access, if multiple IDs are in a purchase order, the one that grants longest membership access is used
	 */
	public function adjust_access_granting_product_id( $product_id, $access_granting_product_ids, WC_Memberships_Membership_Plan $plan ) {

		// check if more than one products may grant access,
		// and if the plan even allows access while subscription is active
		if (    count( $access_granting_product_ids ) > 1
		     && $this->grant_access_while_subscription_active( $plan ) ) {

			// first, find all subscription products that grant access
			$access_granting_subscription_product_ids = array();

			foreach ( $access_granting_product_ids as $_product_id ) {

				if ( WC_Subscriptions_Product::is_subscription( $_product_id ) ) {
					$access_granting_subscription_product_ids[] = $_product_id;
				}
			}

			// if there are any, decide which one actually gets to grant access
			if ( ! empty( $access_granting_subscription_product_ids ) ) {

				// only one subscription grants access, short-circuit it as the winner
				if ( 1 === count( $access_granting_subscription_product_ids ) ) {

					$product_id = $access_granting_subscription_product_ids[0];

				// multiple subscriptions grant access
				} else {

					$longest_expiration_date = 0;

					// let's select the most gracious one:
					// whichever gives access for a longer period, wins
					foreach ( $access_granting_subscription_product_ids as $_subscription_product_id ) {

						$expiration_date = WC_Subscriptions_Product::get_expiration_date( $_subscription_product_id );

						// no expiration date always means the longest period
						if ( ! $expiration_date ) {

							$product_id = $_subscription_product_id;
							break;
						}

						// the current Subscription has a longer expiration date
						// than the previous one in the loop
						if ( strtotime( $expiration_date ) > $longest_expiration_date ) {

							$product_id              = $_subscription_product_id;
							$longest_expiration_date = strtotime( $expiration_date );
						}
					}
				}
			}
		}

		return $product_id;
	}


	/**
	 * Only grants access to new subscriptions if they're not a subscription renewal.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param bool $grant_access
	 * @param array $args
	 * @return bool
	 */
	public function maybe_grant_access_from_new_subscription( $grant_access, $args ) {

		if ( isset( $args['order_id'] ) && is_numeric( $args['order_id'] ) && wcs_order_contains_renewal( $args['order_id'] ) ) {

			// subscription renewals cannot grant access
			$grant_access = false;

		} elseif ( isset( $args['order_id'], $args['product_id'], $args['user_id'] ) ) {

			// reactivate a cancelled/pending cancel User Membership,
			// when re-purchasing the same Subscription that grants access

			$product = wc_get_product( $args['product_id'] );

			if ( $product && WC_Subscriptions_Product::is_subscription( $product ) ) {

				$user_id = (int) $args['user_id'];
				$order   = wc_get_order( (int) $args['order_id'] );
				$plans   = wc_memberships()->get_plans_instance()->get_membership_plans();

				// loop over all available membership plans
				foreach ( $plans as $plan ) {

					// skip if no products grant access to this plan
					if ( ! $plan->has_products() ) {
						continue;
					}

					$access_granting_product_ids = wc_memberships_get_order_access_granting_product_ids( $plan, $order );

					foreach ( $access_granting_product_ids as $access_granting_product_id ) {

						// sanity check: make sure the selected product ID in fact does grant access
						if ( ! $plan->has_product( $access_granting_product_id ) ) {
							continue;
						}

						if ( (int) $product->get_id() === (int) $access_granting_product_id ) {

							$user_membership = wc_memberships_get_user_membership( $user_id, $plan );

							// check if the user purchasing is already member of a plan
							// but the membership is cancelled or pending cancellation
							if ( wc_memberships_is_user_member( $user_id, $plan ) && $user_membership->has_status( array( 'pending', 'cancelled' ) ) ) {

								$order_id                = SV_WC_Order_Compatibility::get_prop( $order, 'id' );
								$subscription_membership = new WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );

								/* translators: Placeholders: %1$s is the subscription product name, %2%s is the order number */
								$note = sprintf( __( 'Membership re-activated due to subscription re-purchase (%1$s, Order %2$s).', 'woocommerce-memberships' ),
									$product->get_title(),
									'<a href="' . admin_url( 'post.php?post=' . $order_id  . '&action=edit' ) .'" >' . $order_id. '</a>'
								);

								$subscription_membership->activate_membership( $note );

								$subscription = wc_memberships_get_order_subscription( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), $product->get_id() );
								$subscription_membership->set_subscription_id( SV_WC_Order_Compatibility::get_prop( $subscription, 'id' ) );
							}
						}
					}
				}
			}
		}

		return $grant_access;
	}


	/**
	 * Only grants access from existing subscription if it's active
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 * @param bool $grant_access
	 * @param array $args
	 * @return bool
	 */
	public function maybe_grant_access_from_existing_subscription( $grant_access, $args ) {

		$product = wc_get_product( $args['product_id'] );

		// handle access from subscriptions
		if ( $product && isset( $args['order_id'] ) && $args['order_id'] > 0 && WC_Subscriptions_Product::is_subscription( $product ) ) {

			$subscription = wc_memberships_get_order_subscription( $args['order_id'], $product->get_id() );

			// handle deleted subscriptions
			if ( ! is_array( $subscription ) && ! $subscription instanceof WC_Subscription ) {
				return false;
			}

			$status = is_array( $subscription ) ? $subscription['status'] : $subscription->get_status();

			if ( 'active' !== $status ) {
				$grant_access = false;
			}
		}

		return $grant_access;
	}


	/**
	 * Extends retroactive access granting to manually created subscriptions.
	 *
	 * Conditions:
	 *
	 *  1. The user membership was not already created by looking at past purchases
	 *  2. Membership Plan has Subscriptions options
	 *  3. The user is not already a member of the plan
	 *
	 * Conditions 1 and 3 exist because if the user has already a membership, and a subscription was manually added,
	 * then the admins should determine as well whether to also manually tie the subscription to the membership, or not.
	 *
	 * @see \WC_Memberships_Grant_Retroactive_Access::grant_access_to_existing_purchases()
	 *
	 * @internal
	 *
	 * @since 1.10.1
	 *
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership|null $user_membership granted user membership or null when not granted by a purchase
	 * @param \WP_User $user a user that could be already a member
	 * @param \WC_Memberships_Membership_Plan|\WC_Memberships_Integration_Subscriptions_Membership_Plan $membership_plan a plan that could include subscription access
	 * @return \WC_Memberships_User_Membership|\WC_Memberships_Integration_Subscriptions_User_Membership|null
	 */
	public function maybe_grant_access_from_existing_manual_subscription( $user_membership, $user, $membership_plan ) {

		if (    ! $user_membership
		     &&   $user instanceof WP_User
		     && ! wc_memberships_is_user_member( $user, $membership_plan, false ) ) {

			$subscription_plan       = new WC_Memberships_Integration_Subscriptions_Membership_Plan( $membership_plan->get_id() );
			$access_granting_sub_ids = $subscription_plan->get_subscription_product_ids();
			$integration             = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

			if ( $integration && ! empty( $access_granting_sub_ids ) ) {

				$subscription_posts = get_posts( array(
					'nopaging'    => true,
					'fields'      => 'ids',
					'post_type'   => 'shop_subscription',
					'post_status' => array( 'wc-active', 'wc-pending', 'wc-on-hold' ),
					'meta_key'    => '_customer_user',
					'meta_value'  => $user->ID,
				) );

				foreach ( $subscription_posts as $subscription_post ) {

					// check whether a membership was already granted while looping subscriptions
					if ( ! $user_membership && ( $subscription = wcs_get_subscription( $subscription_post ) ) ) {

						$parent_order_id = SV_WC_Order_Compatibility::get_prop( $subscription, 'parent_id' );

						// the subscription has no parent order, therefore must be a manually added one
						if ( ! $parent_order_id && $user->ID === (int) $subscription->get_user_id() ) {

							$items = $subscription->get_items();

							if ( ! empty( $items ) ) {

								foreach ( $items as $item ) {

									if ( $item instanceof WC_Order_Item_Product && is_callable( $item, 'get_product' ) ) {
										$product = $item->get_product();
									} elseif ( is_callable( $subscription, 'get_product_from_item' ) ) {
										$product = $subscription->get_product_from_item( $item );
									} else {
										$product = null;
									}

									// the product matches a subscription product that would grant access
									if ( $product && in_array( $product->get_id(), $access_granting_sub_ids, false ) ) {

										/**
										 * Filters when a membership is about to be granted from an existing subscription that has been created manually.
										 *
										 * @since 1.10.1
										 *
										 * @param bool $grant_access default true
										 * @param array $args associative array of arguments
										 */
										$grant_access = (bool) apply_filters( 'wc_memberships_grant_access_from_manual_subscription', true, array(
											'user_id'         => $user->ID,
											'product_id'      => $product->get_id(),
											'subscription_id' => $subscription->get_id(),
											'plan_id'         => $subscription_plan->get_id(),
										) );

										if ( $grant_access ) {

											try {

												$user_membership = wc_memberships_create_user_membership( array(
													'user_id'    => $user->ID,
													'plan_id'    => $subscription_plan->get_id(),
													'product_id' => $product->get_id(),
												) );

												// tie membership to subscription
												if ( $user_membership instanceof WC_Memberships_Integration_Subscriptions_User_Membership ) {

													$user_membership->set_subscription_id( $subscription->get_id() );

													// synchronize statuses
													if ( $user_membership->is_in_active_period() ) {

														$membership_status   = $user_membership->get_status();
														$subscription_status = $subscription->get_status();
														$free_trial_end_date = $subscription->get_date( 'trial_end' );

														if ( ! empty( $free_trial_end_date ) ) {
															$user_membership->set_free_trial_end_date( $free_trial_end_date );
														}

														if ( $user_membership->is_in_free_trial_period() ) {

															if ( 'free_trial' !== $user_membership ) {
																$user_membership->update_status( 'free_trial' );
															}

														} elseif ( 'active' === $subscription_status && 'active' !== $membership_status ) {

															$user_membership->update_status( 'active' );

														} elseif ( 'paused' !== $membership_status && in_array( $subscription_status, array( 'pending', 'on-hold' ), false ) ) {

															$user_membership->pause_membership();
														}
													}
												}

												break;

											} catch ( SV_WC_Plugin_Exception $e ) {

												// nothing to do here for now
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $user_membership;
	}


	/**
	 * Adds 'active' to valid order statuses for granting membership access.
	 *
	 * Filters `'wc_memberships_grant_access_from_existing_purchase_order_statuses'`.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param array $statuses
	 * @return array
	 */
	public function grant_access_from_active_subscription( $statuses ) {
		return array_merge( $statuses, array( 'active' ) );
	}


	/**
	 * Saves related subscription data when a membership access is granted via a purchase.
	 *
	 * Sets the end date to match subscription end date.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param WC_Memberships_Membership_Plan $plan
	 * @param array $args
	 */
	public function save_subscription_data( WC_Memberships_Membership_Plan $plan, $args ) {

		$product     = wc_get_product( $args['product_id'] );
		$integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

		// handle access from subscriptions
		if (    $product
		     && $integration
		     && WC_Subscriptions_Product::is_subscription( $product )
		     && $integration->has_membership_plan_subscription( $plan->get_id() ) ) {

			$subscription = wc_memberships_get_order_subscription( $args['order_id'], $product->get_id() );

			if ( $subscription ) {

				$subscription_membership = new WC_Memberships_Integration_Subscriptions_User_Membership( $args['user_membership_id'] );

				$subscription_membership->set_subscription_id( SV_WC_Order_Compatibility::get_prop( $subscription, 'id' ) );

				$subscription_plan  = new WC_Memberships_Integration_Subscriptions_Membership_Plan( $subscription_membership->get_plan_id() );
				$access_length_type = $subscription_plan->get_access_length_type();

				if ( 'subscription' === $access_length_type && $this->grant_access_while_subscription_active( $plan ) ) {
					$membership_end_date = $integration->get_subscription_event_date( $subscription, 'end' );
				} else {
					$membership_end_date = $subscription_plan->get_expiration_date( current_time( 'mysql', true ), $args );
				}

				// maybe update the trial end date
				if ( $trial_end_date = $integration->get_subscription_event_date( $subscription, 'trial_end' ) ) {
					$subscription_membership->set_free_trial_end_date( $trial_end_date );
				}

				$subscription_membership->set_end_date( $membership_end_date );
			}
		}
	}


}
