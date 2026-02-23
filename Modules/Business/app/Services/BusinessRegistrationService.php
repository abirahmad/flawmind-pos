<?php

namespace Modules\Business\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Business\Models\Business;
use Modules\Business\Models\BusinessLocation;

class BusinessRegistrationService
{
    /**
     * Register a new business with an owner user and first location.
     * Mirrors the ERP's BusinessUtil::createNewBusiness / newBusinessDefaultResources / addLocation logic.
     *
     * @param  array  $data  Validated registration payload
     * @return array         {user, business, location, access_token, token_type}
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // ─────────────────────────────────────────────────────────────
            // 1. Create the owner user (no business_id yet)
            // ─────────────────────────────────────────────────────────────
            $user = User::create([
                'user_type'                   => 'user',
                'surname'                     => $data['surname'] ?? null,
                'first_name'                  => $data['first_name'],
                'last_name'                   => $data['last_name'] ?? null,
                'username'                    => $data['username'],
                'email'                       => $data['email'] ?? null,
                'password'                    => Hash::make($data['password']),
                'language'                    => $data['language'] ?? config('app.locale', 'en'),
                'allow_login'                 => true,
                'status'                      => 'active',
                'is_cmmsn_agnt'               => false,
                'selected_contacts'           => false,
                'is_enable_service_staff_pin' => false,
            ]);

            // ─────────────────────────────────────────────────────────────
            // 2. Create business with ERP-equivalent defaults
            // ─────────────────────────────────────────────────────────────
            $business = Business::create([
                'name'               => $data['name'],
                'currency_id'        => $data['currency_id'],
                'owner_id'           => $user->id,
                'time_zone'          => $data['time_zone'],
                'fy_start_month'     => $data['fy_start_month'],
                'accounting_method'  => $data['accounting_method'],
                'start_date'         => $data['start_date'] ?? null,
                'tax_label_1'        => $data['tax_label_1'] ?? null,
                'tax_number_1'       => $data['tax_number_1'] ?? null,
                'tax_label_2'        => $data['tax_label_2'] ?? null,
                'tax_number_2'       => $data['tax_number_2'] ?? null,
                // Defaults mirrored from ERP's createNewBusiness()
                'sell_price_tax'        => 'includes',
                'default_profit_percent' => 25,
                'enable_inline_tax'     => false,
                'enabled_modules'       => ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'],
                'keyboard_shortcuts'    => [
                    'pos' => [
                        'express_checkout'      => 'shift+e',
                        'pay_n_ckeckout'        => 'shift+p',
                        'draft'                 => 'shift+d',
                        'cancel'                => 'shift+c',
                        'edit_discount'         => 'shift+i',
                        'edit_order_tax'        => 'shift+t',
                        'add_payment_row'       => 'shift+r',
                        'finalize_payment'      => 'shift+f',
                        'recent_product_quantity' => 'f2',
                        'add_new_product'       => 'f4',
                    ],
                ],
                'ref_no_prefixes' => [
                    'purchase'          => 'PO',
                    'stock_transfer'    => 'ST',
                    'stock_adjustment'  => 'SA',
                    'sell_return'       => 'CN',
                    'expense'           => 'EP',
                    'contacts'          => 'CO',
                    'purchase_payment'  => 'PP',
                    'sell_payment'      => 'SP',
                    'business_location' => 'BL',
                ],
                'is_active'  => true,
                'created_by' => $user->id,
            ]);

            // ─────────────────────────────────────────────────────────────
            // 3. Link user → business
            // ─────────────────────────────────────────────────────────────
            $user->update(['business_id' => $business->id]);

            // ─────────────────────────────────────────────────────────────
            // 4. Default Invoice Scheme
            // ─────────────────────────────────────────────────────────────
            $schemeId = DB::table('invoice_schemes')->insertGetId([
                'business_id'   => $business->id,
                'name'          => 'Default',
                'scheme_type'   => 'blank',
                'prefix'        => '',
                'start_number'  => 1,
                'invoice_count' => 0,
                'total_digits'  => 4,
                'is_default'    => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // ─────────────────────────────────────────────────────────────
            // 5. Default Invoice Layout
            // ─────────────────────────────────────────────────────────────
            $layoutId = DB::table('invoice_layouts')->insertGetId([
                'business_id'              => $business->id,
                'name'                     => 'Default',
                'invoice_no_prefix'        => 'Invoice No.',
                'invoice_heading'          => 'Invoice',
                'sub_total_label'          => 'Subtotal',
                'discount_label'           => 'Discount',
                'tax_label'                => 'Tax',
                'total_label'              => 'Total',
                'show_landmark'            => 1,
                'show_city'                => 1,
                'show_state'               => 1,
                'show_zip_code'            => 1,
                'show_country'             => 1,
                'highlight_color'          => '#000000',
                'footer_text'              => '',
                'is_default'               => 1,
                'invoice_heading_not_paid' => '',
                'invoice_heading_paid'     => '',
                'total_due_label'          => 'Total Due',
                'paid_label'               => 'Total Paid',
                'show_payments'            => 1,
                'show_customer'            => 1,
                'customer_label'           => 'Customer',
                'table_product_label'      => 'Product',
                'table_qty_label'          => 'Quantity',
                'table_unit_price_label'   => 'Unit Price',
                'table_subtotal_label'     => 'Subtotal',
                'date_label'               => 'Date',
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);

            // ─────────────────────────────────────────────────────────────
            // 6. Default Walk-In Customer contact
            // ─────────────────────────────────────────────────────────────
            $contactRefCount = $this->getAndIncrementRefCount('contacts', $business->id);
            $contactId = 'CO' . str_pad($contactRefCount, 4, '0', STR_PAD_LEFT);

            DB::table('contacts')->insert([
                'business_id'    => $business->id,
                'type'           => 'customer',
                'name'           => 'Walk-In Customer',
                'contact_id'     => $contactId,
                'is_default'     => 1,
                'credit_limit'   => 0,
                'created_by'     => $user->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // ─────────────────────────────────────────────────────────────
            // 7. Default unit (Pieces)
            // ─────────────────────────────────────────────────────────────
            DB::table('units')->insert([
                'business_id'  => $business->id,
                'actual_name'  => 'Pieces',
                'short_name'   => 'Pc(s)',
                'allow_decimal' => 0,
                'created_by'   => $user->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // ─────────────────────────────────────────────────────────────
            // 8. First business location (auto-generated location_id)
            // ─────────────────────────────────────────────────────────────
            $locationRefCount = $this->getAndIncrementRefCount('business_location', $business->id);
            $locationId       = 'BL' . str_pad($locationRefCount, 4, '0', STR_PAD_LEFT);

            $location = BusinessLocation::create([
                'business_id'            => $business->id,
                'location_id'            => $locationId,
                'name'                   => $data['location_name'],
                'landmark'               => $data['landmark'] ?? '',
                'city'                   => $data['city'] ?? '',
                'state'                  => $data['state'] ?? '',
                'zip_code'               => $data['zip_code'] ?? '',
                'country'                => $data['country'] ?? '',
                'mobile'                 => $data['mobile'] ?? '',
                'alternate_number'       => $data['alternate_number'] ?? '',
                'website'                => $data['website'] ?? '',
                'email'                  => '',
                'invoice_scheme_id'      => $schemeId,
                'invoice_layout_id'      => $layoutId,
                'sale_invoice_layout_id' => $layoutId,
                'is_active'              => true,
            ]);

            // ─────────────────────────────────────────────────────────────
            // 9. Issue Passport access token
            // ─────────────────────────────────────────────────────────────
            $token = $user->createToken('registration_token')->accessToken;

            return [
                'user'         => $user->fresh(),
                'business'     => $business->fresh(),
                'location'     => $location,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ];
        });
    }

    /**
     * Atomically increment (or create) a reference counter for a given type and business.
     * Mirrors ERP's Util::setAndGetReferenceCount().
     */
    private function getAndIncrementRefCount(string $refType, int $businessId): int
    {
        $record = DB::table('reference_counts')
            ->where('ref_type', $refType)
            ->where('business_id', $businessId)
            ->first();

        if ($record) {
            DB::table('reference_counts')
                ->where('id', $record->id)
                ->increment('ref_count');

            return $record->ref_count + 1;
        }

        DB::table('reference_counts')->insert([
            'ref_type'    => $refType,
            'business_id' => $businessId,
            'ref_count'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return 1;
    }
}
