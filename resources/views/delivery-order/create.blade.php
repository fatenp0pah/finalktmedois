@extends('layouts.app')
@section('title', 'Create Delivery Order')
@section('content')

<div class="ktm-page-header">
    <div>
        <h2 class="ktm-page-title"><i class="fa fa-plus-circle me-2" style="color:var(--primary)"></i>Create Delivery Order</h2>
        <div class="ktm-page-sub">Fill in delivery order details and upload supporting documents</div>
    </div>
    <a href="{{ route('vendor.do.dashboard') }}" class="btn-ktm-outline"><i class="fa fa-arrow-left me-1"></i> Back</a>
</div>

@if($errors->any())
<div class="ktm-alert ktm-alert-danger mb-4">
    <i class="fa fa-exclamation-circle mt-1" style="flex-shrink:0"></i>
    <div><strong>Please fix the following:</strong><ul style="margin:6px 0 0 16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
</div>
@endif

<form action="{{ route('vendor.do.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="ktm-card mb-4">
    <div class="ktm-card-header"><i class="fa fa-file-alt me-2"></i>Delivery Order Information</div>
    <div class="ktm-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="ktm-label">DO Number <span style="color:var(--text-muted);font-size:11px">(leave blank to auto-generate)</span></label>
                <input type="text" name="do_no" value="{{ old('do_no') }}" placeholder="e.g. DO-2026-005" class="form-control ktm-input">
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Order Date <span style="color:#dc2626">*</span></label>
                <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" class="form-control ktm-input" required>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">PO Number <span style="color:#dc2626">*</span></label>
                <input type="text" name="po_no" value="{{ old('po_no') }}" placeholder="e.g. PO-88925" class="form-control ktm-input" required>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Project Reference <span style="color:#dc2626">*</span></label>
                <input type="text" name="project_ref" value="{{ old('project_ref') }}" placeholder="e.g. KTM-PROJ-005" class="form-control ktm-input" required>
            </div>
        </div>
    </div>
</div>

<div class="ktm-card mb-4">
    <div class="ktm-card-header"><i class="fa fa-map-marker-alt me-2"></i>Customer & Address</div>
    <div class="ktm-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="ktm-label">Customer <span style="color:#dc2626">*</span></label>
                <input type="text" name="customer" value="{{ old('customer') }}" placeholder="e.g. KTM Berhad" class="form-control ktm-input" required>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Shipping Address <span style="color:#dc2626">*</span></label>
                <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control ktm-input" required>{{ old('shipping_address') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">
                    Invoice Address <span style="color:#dc2626">*</span>
                    <label style="font-weight:400;font-size:11px;margin-left:8px;cursor:pointer">
                        <input type="checkbox" id="same_addr" onchange="copyAddr()"> Same as shipping
                    </label>
                </label>
                <textarea name="invoice_address" id="invoice_address" rows="3" class="form-control ktm-input" required>{{ old('invoice_address') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="ktm-card mb-4">
    <div class="ktm-card-header"><i class="fa fa-box me-2"></i>Item & Delivery Details</div>
    <div class="ktm-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="ktm-label">Item No. <span style="color:#dc2626">*</span></label>
                <input type="text" name="item_no" value="{{ old('item_no') }}" placeholder="e.g. ITEM-005" class="form-control ktm-input" required>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Quantity <span style="color:#dc2626">*</span></label>
                <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" class="form-control ktm-input" required>
            </div>
            <div class="col-12">
                <label class="ktm-label">Item Description <span style="color:#dc2626">*</span></label>
                <textarea name="item_description" rows="2" class="form-control ktm-input" required>{{ old('item_description') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Delivery Date <span style="color:#dc2626">*</span></label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" class="form-control ktm-input" required>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Delivery Time <span style="color:#dc2626">*</span></label>
                <input type="time" name="delivery_time" value="{{ old('delivery_time') }}" class="form-control ktm-input" required>
            </div>
        </div>
    </div>
</div>

<div class="ktm-card mb-4">
    <div class="ktm-card-header"><i class="fa fa-upload me-2"></i>Document Upload</div>
    <div class="ktm-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="ktm-label">DO Document (PDF/Image)</label>
                <input type="file" name="do_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control ktm-input">
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px">PDF, JPG, PNG — Max 2MB</div>
            </div>
            <div class="col-md-6">
                <label class="ktm-label">Proof of Delivery</label>
                <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control ktm-input">
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Photo or acknowledgement receipt</div>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:12px">
    <a href="{{ route('vendor.do.dashboard') }}" class="btn-ktm-outline">Cancel</a>
    <button type="submit" name="action" value="draft" class="btn-ktm-outline"
        onclick="return confirm('Save as draft?')">
        <i class="fa fa-save me-1"></i> Save as Draft
    </button>
    <button type="submit" name="action" value="submit" class="btn-ktm"
        onclick="return confirm('Submit this Delivery Order for review?')">
        <i class="fa fa-paper-plane me-1"></i> Submit DO
    </button>
</div>

</form>

<script>
function copyAddr() {
    const cb = document.getElementById('same_addr');
    const invoice = document.getElementById('invoice_address');
    const shipping = document.getElementById('shipping_address');
    if (cb.checked) invoice.value = shipping.value;
    shipping.addEventListener('input', () => { if(cb.checked) invoice.value = shipping.value; });
}
</script>
@endsection
