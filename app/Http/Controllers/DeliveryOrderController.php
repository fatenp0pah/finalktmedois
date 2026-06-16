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

    private function validatePONumber(string $poNo): bool
    {
        if (strlen(trim($poNo)) < 3) {
            return false;
        }
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
            $query = $this->vendorDOQuery();
        } else {
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

        if (!$this->validatePONumber($request->po_no)) {
            return back()
                ->withErrors([
                    'po_no' =>
                    'PO Number "' . $request->po_no . '" could not be validated against KTM procurement records. Please check the PO number and try again.'
                ])
                ->withInput();
        }

        $vendor = Auth::user()->vendor;
        $action = $request->input('action');
        $status = ($action === 'draft') ? 'Draft' : 'Submitted';

        $doNo = trim($request->input('do_no', ''));
        if (!$doNo) {
            $count = DeliveryOrder::count() + 1;
            $doNo  = 'DO-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        if (DeliveryOrder::where('DONumber', $doNo)->exists()) {
            return back()
                ->withErrors(['do_no' => 'DO Number "' . $doNo . '" already exists. Please use a different number.'])
                ->withInput();
        }

        $doFileLink    = null;
        $proofFileLink = null;
        if ($request->hasFile('do_file')) {
            $doFileLink = $request->file('do_file')->store('delivery-orders', 'public');
        }
        if ($request->hasFile('proof_file')) {
            $proofFileLink = $request->file('proof_file')->store('proof-of-delivery', 'public');
        }

        // FIX 1: OrderDate is now saved to database
        $do = DeliveryOrder::create([
            'DONumber'         => $doNo,
            'VendorID'         => $vendor->VendorID,
            'OrderDate'        => $request->order_date,
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

        AuditLog::log(
            Auth::id(),
            'DO_' . strtoupper($status),
            'DOID:' . $do->DOID,
            $doNo . ' ' . strtolower($status) . ' by ' . $vendor->CompanyName
        );

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

    // FIX 2: Start Review — officer sets DO to Under Review
    public function startReview(Request $request)
    {
        if (!Auth::user()->isOfficer()) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Only officers can review Delivery Orders.');
        }

        $request->validate([
            'DOID' => 'required|exists:delivery_orders,DOID',
        ]);

        $do = DeliveryOrder::with('vendor')->findOrFail($request->DOID);

        // Only Submitted DOs can be moved to Under Review
        if ($do->DOStatus !== 'Submitted') {
            return back()->with('error', 'Only Submitted DOs can be moved to Under Review.');
        }

        $do->update([
            'DOStatus' => 'Under Review',
            'Remark'   => 'Under review by KTMB Officer.',
        ]);

        AuditLog::log(
            Auth::id(),
            'DO_UNDER_REVIEW',
            'DOID:' . $do->DOID,
            $do->DONumber . ' set to Under Review by officer.'
        );

        // Notify vendor
        if ($do->vendor) {
            Notification::send(
                $do->vendor->UserID,
                $do->DONumber . ' is now under review by KTMB Officer.'
            );
        }

        return back()->with('success', $do->DONumber . ' is now Under Review.');
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

        if ($action === 'reject' && $remark === '') {
            return back()->with('error', 'Please enter a rejection reason before rejecting.');
        }

        $newStatus = $action === 'approve' ? 'Approved' : 'Rejected';
        $remark    = $remark ?: 'Delivery Order approved by officer.';

        $do->update([
            'DOStatus' => $newStatus,
            'Remark'   => $remark,
        ]);

        AuditLog::log(
            Auth::id(),
            'DO_' . strtoupper($newStatus),
            'DOID:' . $do->DOID,
            $do->DONumber . ' ' . strtolower($newStatus) . ' by officer. Remark: ' . $remark
        );

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

        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if ($selectedDO->VendorID !== $vendor->VendorID) {
                abort(403, 'You do not have permission to view this Delivery Order.');
            }
        }

        AuditLog::log(
            Auth::id(),
            'VIEW_DO',
            'DOID:' . $selectedDO->DOID,
            'Viewed DO ' . $selectedDO->DONumber
        );

        return view('delivery-order.detail', compact('selectedDO'));
    }

    // ── Notifications ──────────────────────────────────────────────────────────

    public function notifications()
    {
        $notifications = \App\Models\Notification::where('UserID', Auth::id())
            ->orderBy('CreatedDate', 'desc')
            ->get();

        \App\Models\Notification::where('UserID', Auth::id())
            ->where('NotificationStatus', 'Unread')
            ->update(['NotificationStatus' => 'Read']);

        return view('delivery-order.notifications', compact('notifications'));
    }
}
