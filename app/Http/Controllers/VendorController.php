<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\Invoice;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendor = Auth::user()->vendor;

        $invoiceQuery = Invoice::whereHas(
            'deliveryOrder',
            fn($q) => $q->where('VendorID', $vendor->VendorID)
        );

        $totalDOs       = $vendor->deliveryOrders()->count();
        $approvedDOs    = $vendor->deliveryOrders()->where('DOStatus', 'Approved')->count();
        $submittedDOs   = $vendor->deliveryOrders()->where('DOStatus', 'Submitted')->count();
        $underReviewDOs = $vendor->deliveryOrders()->where('DOStatus', 'Under Review')->count();
        $rejectedDOs    = $vendor->deliveryOrders()->where('DOStatus', 'Rejected')->count();
        $totalInvoices  = (clone $invoiceQuery)->count();
        $paidInvoices   = (clone $invoiceQuery)->where('InvoiceStatus', 'Paid')->count();

        $totalInvoicedAmount = (clone $invoiceQuery)->sum('TotalAmount');
        $totalSubtotal       = (clone $invoiceQuery)->sum('Subtotal');
        $totalTax            = (clone $invoiceQuery)->sum('Tax');
        $totalDiscount       = (clone $invoiceQuery)->sum('Discount');
        $totalPenalty        = (clone $invoiceQuery)->sum('Penalty');
        $totalClaimedAmount  = (clone $invoiceQuery)->where('InvoiceStatus', 'Paid')->sum('TotalAmount');

        $recentInvoices = (clone $invoiceQuery)->latest('SubmittedDate')->take(4)->get();
        $recentDOs      = $vendor->deliveryOrders()->with('invoice')->latest('DOID')->take(5)->get();

        return view('vendor.dashboard', compact(
            'vendor',
            'totalDOs',
            'approvedDOs',
            'submittedDOs',
            'underReviewDOs',
            'rejectedDOs',
            'totalInvoices',
            'paidInvoices',
            'totalInvoicedAmount',
            'totalSubtotal',
            'totalTax',
            'totalDiscount',
            'totalPenalty',
            'totalClaimedAmount',
            'recentInvoices',
            'recentDOs'
        ));
    }

    public function profile()
    {
        $vendor = Auth::user()->vendor;

        AuditLog::log(
            Auth::id(),
            'VIEW_PROFILE',
            'VendorID:' . $vendor->VendorID,
            'Vendor viewed profile'
        );

        $apiLogs = $vendor->apiLogs()->latest('LogDate')->take(10)->get();

        return view('vendor.profile', compact('vendor', 'apiLogs'));
    }

    public function showInvoice($id)
    {
        $vendor  = Auth::user()->vendor;
        $invoice = \App\Models\Invoice::with(['deliveryOrder.vendor'])
            ->whereHas('deliveryOrder', fn($q) => $q->where('VendorID', $vendor->VendorID))
            ->findOrFail($id);

        AuditLog::log(
            Auth::id(),
            'VIEW_INVOICE',
            'InvoiceID:' . $invoice->InvoiceID,
            'Vendor viewed invoice ' . $invoice->InvoiceNumber
        );

        return view('vendor.invoice.show', compact('invoice'));
    }

    public function syncProfile()
    {
        $vendor = Auth::user()->vendor;
        $vendor->simulateApiSync();

        AuditLog::log(
            Auth::id(),
            'VENDOR_SYNC',
            'VendorID:' . $vendor->VendorID,
            'Manual sync from KTMB master database'
        );

        return back()->with('success', 'Vendor data synchronised. Last sync: ' . now()->format('d M Y, H:i'));
    }
}
