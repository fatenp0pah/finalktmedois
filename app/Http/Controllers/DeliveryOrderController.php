<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryOrder;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;

class DeliveryOrderController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────────────────────

    private function getSummary($query)
    {
        $orders = (clone $query)->get();
        return [
            'total'       => $orders->count(),
            'draft'       => $orders->where('DOStatus', 'Draft')->count(),
            'submitted'   => $orders->where('DOStatus', 'Submitted')->count(),
            'underReview' => $orders->where('DOStatus', 'Under Review')->count(),
            'approved'    => $orders->where('DOStatus', 'Approved')->count(),
            'rejected'    => $orders->where('DOStatus', 'Rejected')->count(),
        ];
    }

    private function vendorDOQuery()
    {
        return DeliveryOrder::where('VendorID', Auth::user()->vendor->VendorID);
    }

    // Simulates PO validation against KTM procurement records
    // In production: would call KTM procurement API to verify PO exists
    // For prototype: validates format and ensures PO is not empty
    private function validatePONumber(string $poNo): bool
    {
        // Must not be empty and must be at least 3 characters
        if (strlen(trim($poNo)) < 3) {
            return false;
        }
        // Simulate: PO numbers starting with 'INVALID' are rejected
        // This demonstrates the validation concept for the prototype
        if (strtoupper(substr(trim($poNo), 0, 7)) === 'INVALID') {
            return false;
        }
        return true;
    }

    // ── Dashboard ──────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isVendor()) {
            // Vendor sees all their own DOs including drafts
            $query = $this->vendorDOQuery();
        } else {
            // Officer sees all DOs EXCEPT drafts
            // Drafts are vendor-side only — not yet officially submitted
            $query = DeliveryOrder::with('vendor')
                ->where('DOStatus', '!=', 'Draft');
        }

        $deliveryOrders = (clone $query)->with('vendor')->latest('DOID')->get();
        $summary        = $this->getSummary($query);

        return view('delivery-order.dashboard', compact('deliveryOrders', 'summary'));
    }

    // ── Create DO ─────────────────────────────────────────────────────────────

    public function create()
    {
        // Only active vendors can create DOs
        if (!Auth::user()->isVendor()) {
            return redirect()->route('vendor.do.dashboard')
                ->with('error', 'Only vendors can create Delivery Orders.');
        }

        if (!Auth::user()->vendor->isActive()) {
            return redirect()->route('vendor.do.dashboard')
                ->with('error', 'Your account is ' . Auth::user()->vendor->VendorStatus . '. Only Active vendors can submit Delivery Orders.');
        }

        return view('delivery-order.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isVendor()) {
            return redirect()->route('vendor.do.dashboard')
                ->with('error', 'Only vendors can submit Delivery Orders.');
        }

        if (!Auth::user()->vendor->isActive()) {
            return redirect()->route('vendor.do.dashboard')
                ->with('error', 'Your account is inactive. Delivery Order submission is not permitted.');
        }

        $request->validate([
            'order_date'       => 'required|date',
            'po_no'            => 'required|string|max:50',
            'project_ref'      => 'required|string|max:100',
            'customer'         => 'required|string|max:100',
            'shipping_address' => 'required|string',
            'invoice_address'  => 'required|string',
            'item_no'          => 'required|string|max:50',
            'item_description' => 'required|string',
            'quantity'         => 'required|integer|min:1',
            'delivery_date'    => 'required|date',
            'delivery_time'    => 'required',
            'action'           => 'required|in:draft,submit',
            'do_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'proof_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'po_no.required'            => 'Purchase Order number is required.',
            'project_ref.required'      => 'KTM Project Reference is required.',
            'item_description.required' => 'Item description is required.',
            'delivery_date.required'    => 'Delivery date is required.',
            'quantity.min'              => 'Quantity must be at least 1.',
        ]);

        // ── PO Number validation ───────────────────────────────────────────────
        // RFP requirement: "system automatically validates the PO number
        // against KTM's procurement records"
        // Prototype: validates format — production would call procurement API
        if (!$this->validatePONumber($request->po_no)) {
            return back()
                ->withErrors(['po_no' =>
                    'PO Number "' . $request->po_no . '" could not be validated against KTM procurement records. Please check the PO number and try again.'
                ])
                ->withInput();
        }

        $vendor = Auth::user()->vendor;
        $action = $request->input('action');
        $status = ($action === 'draft') ? 'Draft' : 'Submitted';

        // Auto-generate DO number if not provided
        $doNo = trim($request->input('do_no', ''));
        if (!$doNo) {
            $count = DeliveryOrder::count() + 1;
            $doNo  = 'DO-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        // Check DO number is unique
        if (DeliveryOrder::where('DONumber', $doNo)->exists()) {
            return back()
                ->withErrors(['do_no' => 'DO Number "' . $doNo . '" already exists. Please use a different number.'])
                ->withInput();
        }

        // Handle file uploads
        $doFileLink    = null;
        $proofFileLink = null;
        if ($request->hasFile('do_file')) {
            $doFileLink = $request->file('do_file')->store('delivery-orders', 'public');
        }
        if ($request->hasFile('proof_file')) {
            $proofFileLink = $request->file('proof_file')->store('proof-of-delivery', 'public');
        }

        $do = DeliveryOrder::create([
            'DONumber'         => $doNo,
            'VendorID'         => $vendor->VendorID,
            'PONumber'         => $request->po_no,
            'ProjectReference' => $request->project_ref,
            'Customer'         => $request->customer,
            'ShippingAddress'  => $request->shipping_address,
            'InvoiceAddress'   => $request->invoice_address,
            'ItemNo'           => $request->item_no,
            'ItemDescription'  => $request->item_description,
            'Quantity'         => $request->quantity,
            'DeliveryDate'     => $request->delivery_date,
            'DeliveryTime'     => $request->delivery_time,
            'DOFileLink'       => $doFileLink,
            'ProofFileLink'    => $proofFileLink,
            'DOStatus'         => $status,
            'Remark'           => $status === 'Draft'
                ? 'Saved as draft by vendor.'
                : 'Submitted and awaiting officer review.',
            'SubmittedDate'    => $status === 'Submitted' ? now() : null,
        ]);

        // Audit log — satisfies NFR: all actions require audit logging
        AuditLog::log(
            Auth::id(),
            'DO_' . strtoupper($status),
            'DOID:' . $do->DOID,
            $doNo . ' ' . strtolower($status) . ' by ' . $vendor->CompanyName
        );

        // Notify all officers when DO is submitted
        if ($status === 'Submitted') {
            $officers = User::where('UserRole', 'Officer')->get();
            foreach ($officers as $officer) {
                Notification::send(
                    $officer->UserID,
                    $doNo . ' has been submitted by ' . $vendor->CompanyName . ' and is awaiting review.'
                );
            }
        }

        $msg = $status === 'Draft'
            ? $doNo . ' saved as draft successfully.'
            : $doNo . ' submitted successfully. Awaiting officer review.';

        return redirect()->route('vendor.do.dashboard')->with('success', $msg);
    }

    // ── Status Tracking ────────────────────────────────────────────────────────
    // RFP requirement: "DO status visible at all stages: Submitted → Under Review → Approved/Rejected"

    public function status()
    {
        $user  = Auth::user();
        $query = $user->isVendor()
            ? $this->vendorDOQuery()
            : DeliveryOrder::with('vendor');

        $deliveryOrders = (clone $query)->with('vendor')->latest('DOID')->get();

        return view('delivery-order.status', compact('deliveryOrders'));
    }

    // ── Review — officer only ──────────────────────────────────────────────────

    public function review()
    {
        if (!Auth::user()->isOfficer()) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Only officers can review Delivery Orders.');
        }

        $deliveryOrders = DeliveryOrder::with('vendor')
            ->whereIn('DOStatus', ['Submitted', 'Under Review', 'Approved', 'Rejected'])
            ->latest('DOID')
            ->get();

        return view('delivery-order.review', compact('deliveryOrders'));
    }

    public function updateReview(Request $request)
    {
        if (!Auth::user()->isOfficer()) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Only officers can approve or reject Delivery Orders.');
        }

        $request->validate([
            'DOID'   => 'required|exists:delivery_orders,DOID',
            'action' => 'required|in:approve,reject',
            'remark' => 'nullable|string|max:500',
        ]);

        $do     = DeliveryOrder::with('vendor')->findOrFail($request->DOID);
        $action = $request->action;
        $remark = trim($request->remark ?? '');

        // Remark is required for rejection
        if ($action === 'reject' && $remark === '') {
            return back()->with('error', 'Please enter a rejection reason before rejecting.');
        }

        $newStatus = $action === 'approve' ? 'Approved' : 'Rejected';
        $remark    = $remark ?: 'Delivery Order approved by officer.';

        $do->update([
            'DOStatus' => $newStatus,
            'Remark'   => $remark,
        ]);

        // Audit log
        AuditLog::log(
            Auth::id(),
            'DO_' . strtoupper($newStatus),
            'DOID:' . $do->DOID,
            $do->DONumber . ' ' . strtolower($newStatus) . ' by officer. Remark: ' . $remark
        );

        // Notify vendor of status change
        // RFP: "Automatic notifications sent whenever status changes"
        if ($do->vendor) {
            Notification::send(
                $do->vendor->UserID,
                $do->DONumber . ' has been ' . strtolower($newStatus) . ' by KTMB Officer. Remark: ' . $remark
            );
        }

        return back()->with('success', $do->DONumber . ' has been ' . strtolower($newStatus) . ' successfully.');
    }

    // ── Report ─────────────────────────────────────────────────────────────────

    public function report()
    {
        $user  = Auth::user();
        $query = $user->isVendor()
            ? $this->vendorDOQuery()
            : DeliveryOrder::with('vendor');

        $reports = (clone $query)->with('vendor')->latest('DOID')->get();
        $summary = $this->getSummary($query);

        return view('delivery-order.report', compact('reports', 'summary'));
    }

    // ── Detail ─────────────────────────────────────────────────────────────────

    public function detail($doNo)
    {
        $selectedDO = DeliveryOrder::with('vendor', 'invoice', 'proofs')
            ->where('DONumber', $doNo)
            ->firstOrFail();

        // Vendors can only see their own DOs — security enforcement
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if ($selectedDO->VendorID !== $vendor->VendorID) {
                abort(403, 'You do not have permission to view this Delivery Order.');
            }
        }

        // Audit log — view action recorded
        AuditLog::log(
            Auth::id(),
            'VIEW_DO',
            'DOID:' . $selectedDO->DOID,
            'Viewed DO ' . $selectedDO->DONumber
        );

        return view('delivery-order.detail', compact('selectedDO'));
    }

    // ── Notifications ──────────────────────────────────────────────────────────
    // RFP: "Automatic notifications sent to relevant parties whenever status changes"

    public function notifications()
    {
        $notifications = \App\Models\Notification::where('UserID', Auth::id())
            ->orderBy('CreatedDate', 'desc')
            ->get();

        // Mark all as read
        \App\Models\Notification::where('UserID', Auth::id())
            ->where('NotificationStatus', 'Unread')
            ->update(['NotificationStatus' => 'Read']);

        return view('delivery-order.notifications', compact('notifications'));
    }
}
