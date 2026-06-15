<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────────────────
        // Use firstOrCreate to prevent duplicate entry errors on re-deploy
        $vendorUser = DB::table('users')->where('UserEmail', 'vendor@ktm.com')->first();
        if (!$vendorUser) {
            DB::table('users')->insert([
                'Username'     => 'ABC Supplies Sdn Bhd',
                'UserPassword' => Hash::make('Vendor@123'),
                'UserEmail'    => 'vendor@ktm.com',
                'UserRole'     => 'Vendor',
                'UserStatus'   => 'Active',
                'LastLogin'    => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $railtechUser = DB::table('users')->where('UserEmail', 'railtech@ktm.com')->first();
        if (!$railtechUser) {
            DB::table('users')->insert([
                'Username'     => 'RailTech Services',
                'UserPassword' => Hash::make('Vendor@456'),
                'UserEmail'    => 'railtech@ktm.com',
                'UserRole'     => 'Vendor',
                'UserStatus'   => 'Inactive',
                'LastLogin'    => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $officerUser = DB::table('users')->where('UserEmail', 'officer@ktm.com')->first();
        if (!$officerUser) {
            DB::table('users')->insert([
                'Username'     => 'KTM Officer',
                'UserPassword' => Hash::make('Officer@123'),
                'UserEmail'    => 'officer@ktm.com',
                'UserRole'     => 'Officer',
                'UserStatus'   => 'Active',
                'LastLogin'    => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Get user IDs after insert
        $vendorUserID   = DB::table('users')->where('UserEmail', 'vendor@ktm.com')->value('UserID');
        $railtechUserID = DB::table('users')->where('UserEmail', 'railtech@ktm.com')->value('UserID');
        $officerUserID  = DB::table('users')->where('UserEmail', 'officer@ktm.com')->value('UserID');

        // ── Vendors ───────────────────────────────────────────────────────────
        if (!DB::table('vendors')->where('VendorNumber', 'VND-2026-001')->exists()) {
            DB::table('vendors')->insert([
                'UserID'           => $vendorUserID,
                'VendorNumber'     => 'VND-2026-001',
                'CompanyName'      => 'ABC Supplies Sdn Bhd',
                'RefNumber'        => '202001234567',
                'VendorEmail'      => 'vendor@ktm.com',
                'VendorContactNum' => '0312345678',
                'ContactPerson'    => 'Ahmad bin Ali',
                'ExpiredDate'      => '2027-12-31',
                'VendorStatus'     => 'Active',
                'LastSyncDate'     => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        if (!DB::table('vendors')->where('VendorNumber', 'VND-2026-002')->exists()) {
            DB::table('vendors')->insert([
                'UserID'           => $railtechUserID,
                'VendorNumber'     => 'VND-2026-002',
                'CompanyName'      => 'RailTech Services',
                'RefNumber'        => '202009876543',
                'VendorEmail'      => 'railtech@ktm.com',
                'VendorContactNum' => '0387654321',
                'ContactPerson'    => 'Siti binti Hassan',
                'ExpiredDate'      => '2026-06-30',
                'VendorStatus'     => 'Inactive',
                'LastSyncDate'     => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // ── Staff ─────────────────────────────────────────────────────────────
        if (!DB::table('staff')->where('StaffEmail', 'officer@ktm.com')->exists()) {
            DB::table('staff')->insert([
                'UserID'       => $officerUserID,
                'StaffName'    => 'KTM Officer',
                'StaffEmail'   => 'officer@ktm.com',
                'StaffPhoneNum' => '0311112222',
                'StaffRole'    => 'Officer',
                'Department'   => 'Procurement',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Get vendor IDs
        $vendorID   = DB::table('vendors')->where('VendorNumber', 'VND-2026-001')->value('VendorID');

        // ── Sample Delivery Orders ─────────────────────────────────────────────
        if (!DB::table('delivery_orders')->where('DONumber', 'DO-2026-001')->exists()) {
            DB::table('delivery_orders')->insert([
                'DONumber'         => 'DO-2026-001',
                'VendorID'         => $vendorID,
                'PONumber'         => 'PO-88921',
                'ProjectReference' => 'KTM-PROJ-001',
                'Customer'         => 'KTM Berhad',
                'ShippingAddress'  => 'KTMB HQ, Jalan Sultan Hishamuddin, 50621 KL',
                'InvoiceAddress'   => 'KTMB HQ, Jalan Sultan Hishamuddin, 50621 KL',
                'ItemNo'           => 'ITEM-001',
                'ItemDescription'  => 'Railway maintenance equipment',
                'Quantity'         => 10,
                'DeliveryDate'     => '2026-05-26',
                'DeliveryTime'     => '10:30:00',
                'DOFileLink'       => null,
                'ProofFileLink'    => null,
                'DOStatus'         => 'Approved',
                'Remark'           => 'Approved by KTM Officer.',
                'SubmittedDate'    => '2026-05-26 09:00:00',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        if (!DB::table('delivery_orders')->where('DONumber', 'DO-2026-002')->exists()) {
            DB::table('delivery_orders')->insert([
                'DONumber'         => 'DO-2026-002',
                'VendorID'         => $vendorID,
                'PONumber'         => 'PO-88922',
                'ProjectReference' => 'KTM-PROJ-002',
                'Customer'         => 'KTM Cargo',
                'ShippingAddress'  => 'KTM Cargo Office, KL',
                'InvoiceAddress'   => 'KTM Cargo Office, KL',
                'ItemNo'           => 'ITEM-002',
                'ItemDescription'  => 'Cargo handling tools',
                'Quantity'         => 8,
                'DeliveryDate'     => '2026-05-27',
                'DeliveryTime'     => '11:00:00',
                'DOFileLink'       => null,
                'ProofFileLink'    => null,
                'DOStatus'         => 'Submitted',
                'Remark'           => 'Waiting for officer review.',
                'SubmittedDate'    => '2026-05-27 08:30:00',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // Get DO ID
        $doID = DB::table('delivery_orders')->where('DONumber', 'DO-2026-001')->value('DOID');

        // ── Sample Invoice ─────────────────────────────────────────────────────
        if (!DB::table('invoices')->where('InvoiceNumber', 'INV-20260526-0001')->exists()) {
            DB::table('invoices')->insert([
                'InvoiceNumber'      => 'INV-20260526-0001',
                'DOID'               => $doID,
                'InvoiceDescription' => 'Invoice for railway maintenance equipment supply',
                'Subtotal'           => 5000.00,
                'Tax'                => 300.00,
                'Discount'           => 0.00,
                'Penalty'            => 0.00,
                'TotalAmount'        => 5300.00,
                'InvoiceStatus'      => 'Submitted',
                'SubmittedDate'      => '2026-05-26 14:00:00',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        // ── Audit Log ─────────────────────────────────────────────────────────
        if (!DB::table('audit_logs')->where('Action', 'LOGIN')->where('UserID', $vendorUserID)->exists()) {
            DB::table('audit_logs')->insert([
                'UserID'         => $vendorUserID,
                'Action'         => 'LOGIN',
                'AffectedRecord' => 'UserID:' . $vendorUserID,
                'LogDescription' => 'Seeded login record for vendor@ktm.com',
                'Timestamp'      => now(),
            ]);
        }

        // ── Vendor API Log ────────────────────────────────────────────────────
        if (!DB::table('vendor_api_logs')->where('VendorID', $vendorID)->exists()) {
            DB::table('vendor_api_logs')->insert([
                'VendorID'  => $vendorID,
                'APIAction' => 'RetrieveVendor',
                'APIStatus' => 'Success',
                'LogDate'   => now(),
            ]);
        }
    }
}
