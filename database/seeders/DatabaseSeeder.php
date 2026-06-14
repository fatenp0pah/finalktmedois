<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────────
        // Vendor accounts — pre-seeded from KTMB master database
        // Passwords follow complexity: min 8 chars, uppercase, number
        DB::table('users')->insert([
            [
                'Username'    => 'ABC Supplies Sdn Bhd',
                'UserPassword' => Hash::make('Vendor@123'),
                'UserEmail'   => 'vendor@ktm.com',
                'UserRole'    => 'Vendor',
                'UserStatus'  => 'Active',
                'LastLogin'   => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'Username'    => 'RailTech Services',
                'UserPassword' => Hash::make('Vendor@456'),
                'UserEmail'   => 'railtech@ktm.com',
                'UserRole'    => 'Vendor',
                'UserStatus'  => 'Inactive',
                'LastLogin'   => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Officer accounts
            [
                'Username'    => 'KTM Officer',
                'UserPassword' => Hash::make('Officer@123'),
                'UserEmail'   => 'officer@ktm.com',
                'UserRole'    => 'Officer',
                'UserStatus'  => 'Active',
                'LastLogin'   => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ── Vendors — pre-seeded from KTMB master DB (Excel schema) ───────────
        // Maps: SUPPLIERID→VendorNumber, SUPPLIER_COMP_NAME→CompanyName,
        //       SUPPLIER_COMP_REG_NO→RefNumber, SUPPLIER_CTC_NO→VendorContactNum
        //       SUPPLIER_CTC_PERSON→ContactPerson, SUPPLIER_EMAIL_ADD→VendorEmail
        //       SUPPLIER_EXPIRED_DATE→ExpiredDate, SUPPLIER_CTC_STATUS→VendorStatus
        DB::table('vendors')->insert([
            [
                'UserID'         => 1,
                'VendorNumber'   => 'VND-2026-001',
                'CompanyName'    => 'ABC Supplies Sdn Bhd',
                'RefNumber'      => '202001234567',
                'VendorEmail'    => 'vendor@ktm.com',
                'VendorContactNum' => '0312345678',
                'ContactPerson'  => 'Ahmad bin Ali',
                'ExpiredDate'    => '2027-12-31',
                'VendorStatus'   => 'Active',
                'LastSyncDate'   => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'UserID'         => 2,
                'VendorNumber'   => 'VND-2026-002',
                'CompanyName'    => 'RailTech Services',
                'RefNumber'      => '202009876543',
                'VendorEmail'    => 'railtech@ktm.com',
                'VendorContactNum' => '0387654321',
                'ContactPerson'  => 'Siti binti Hassan',
                'ExpiredDate'    => '2026-06-30',
                'VendorStatus'   => 'Inactive',
                'LastSyncDate'   => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // ── Staff ──────────────────────────────────────────────────────────────
        DB::table('staff')->insert([
            [
                'UserID'      => 3,
                'StaffName'   => 'KTM Officer',
                'StaffEmail'  => 'officer@ktm.com',
                'StaffPhoneNum' => '0311112222',
                'StaffRole'   => 'Officer',
                'Department'  => 'Procurement',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ── Sample Delivery Orders ─────────────────────────────────────────────
        DB::table('delivery_orders')->insert([
            [
                'DONumber'         => 'DO-2026-001',
                'VendorID'         => 1,
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
            ],
            [
                'DONumber'         => 'DO-2026-002',
                'VendorID'         => 1,
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
            ],
        ]);

        // ── Sample Invoice (linked to Approved DO) ────────────────────────────
        DB::table('invoices')->insert([
            [
                'InvoiceNumber'     => 'INV-20260526-0001',
                'DOID'              => 1,
                'InvoiceDescription' => 'Invoice for railway maintenance equipment supply',
                'Subtotal'          => 5000.00,
                'Tax'               => 300.00,
                'Discount'          => 0.00,
                'Penalty'           => 0.00,
                'TotalAmount'       => 5300.00,
                'InvoiceStatus'     => 'Submitted',
                'SubmittedDate'     => '2026-05-26 14:00:00',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

        // ── Audit Log entries ─────────────────────────────────────────────────
        DB::table('audit_logs')->insert([
            [
                'UserID'         => 1,
                'Action'         => 'LOGIN',
                'AffectedRecord' => 'UserID:1',
                'LogDescription' => 'Seeded login record for vendor@ktm.com',
                'Timestamp'      => now(),
            ],
        ]);

        // ── Vendor API Log entries ────────────────────────────────────────────
        DB::table('vendor_api_logs')->insert([
            [
                'VendorID'  => 1,
                'APIAction' => 'RetrieveVendor',
                'APIStatus' => 'Success',
                'LogDate'   => now(),
            ],
        ]);
    }
}
