<div>

    {{-- Success flash --}}
    @if(session()->has('success'))
    <div class="ktm-alert ktm-alert-success mb-4">
        <i class="fa fa-check-circle mt-1" style="flex-shrink:0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form wire:submit.prevent="save" class="row g-3">

        {{-- DO Selection --}}
        <div class="col-12">
            <label class="ktm-label">Delivery Order <span style="color:#dc2626">*</span></label>
            <select wire:model="DOID" class="form-select ktm-input">
                <option value="">— Select an Approved Delivery Order —</option>
                @foreach($availableDOs as $do)
                <option value="{{ $do->DOID }}">
                    {{ $do->DONumber }} — PO: {{ $do->PONumber ?? 'N/A' }}
                    ({{ $do->ProjectReference ?? 'No Ref' }})
                </option>
                @endforeach
            </select>
            @error('DOID')
                <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
            @if($availableDOs->isEmpty())
                <div style="color:#6b7280;font-size:12px;margin-top:4px">
                    <i class="fa fa-info-circle me-1" style="color:#1e3a8a"></i>
                    No approved DOs available. A DO must be approved by an officer before you can submit an invoice.
                </div>
            @endif
        </div>

        {{-- Invoice Description --}}
        <div class="col-12">
            <label class="ktm-label">Invoice Description</label>
            <input type="text" wire:model="InvoiceDescription"
                placeholder="e.g. Supply of materials for KTM Station Renovation"
                class="form-control ktm-input">
            @error('InvoiceDescription')
                <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
        </div>

        {{-- Proof of Delivery --}}
        <div class="col-12">
            <label class="ktm-label">Proof of Delivery <span style="color:#dc2626">*</span></label>
            <input type="file" wire:model="proof_of_delivery"
                accept=".pdf,.jpg,.jpeg,.png" class="form-control ktm-input">
            <div style="font-size:11px;color:#6b7280;margin-top:4px">
                Accepted: PDF, JPG, PNG — Max 5MB
            </div>
            <div wire:loading wire:target="proof_of_delivery"
                 style="font-size:12px;color:#1e3a8a;margin-top:4px">
                <i class="fa fa-spinner fa-spin me-1"></i> Uploading...
            </div>
            @error('proof_of_delivery')
                <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
        </div>

        {{-- Subtotal --}}
        <div class="col-md-6">
            <label class="ktm-label">
                Subtotal / PO Price (RM) <span style="color:#dc2626">*</span>
            </label>
            <input type="number" step="0.01" min="0"
                wire:model.live="Subtotal"
                placeholder="0.00"
                class="form-control ktm-input">
            <div style="font-size:11px;color:#6b7280;margin-top:4px">
                Enter the Purchase Order price. Tax and penalty are calculated from this value.
            </div>
            @error('Subtotal')
                <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
        </div>

        {{-- Discount --}}
        <div class="col-md-6">
            <label class="ktm-label">Discount / Credit Note (RM)</label>
            <input type="number" step="0.01" min="0"
                wire:model.live="Discount"
                placeholder="0.00"
                class="form-control ktm-input">
            <div style="font-size:11px;color:#6b7280;margin-top:4px">
                Enter credit note value if applicable. Leave 0 if no discount.
            </div>
            @error('Discount')
                <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
        </div>

        {{-- Late Delivery Checkbox --}}
        <div class="col-12">
            <label class="ktm-label">Penalty</label>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:12px 16px">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin:0">
                    <input type="checkbox" wire:model.live="late_delivery"
                        style="width:16px;height:16px;accent-color:#dc2626">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#991b1b">
                            Late Delivery
                        </div>
                        <div style="font-size:11px;color:#b91c1c;margin-top:2px">
                            Tick this if delivery was delayed. A penalty of 1% of Subtotal
                         (RM {{ number_format((float)$Subtotal * 0.01, 2) }})will be deducted.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Live Calculation Summary --}}
        <div class="col-12">
            <label class="ktm-label">Claim Calculation (Auto-calculated)</label>
            <div style="background:#1e3a8a;border-radius:12px;padding:18px 20px">

                {{-- Subtotal row --}}
                <div style="display:flex;justify-content:space-between;
                            font-size:13px;color:rgba(255,255,255,0.8);
                            padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.15)">
                    <span>Subtotal (PO Price)</span>
                    <span style="font-weight:600">RM {{ number_format((float)$Subtotal, 2) }}</span>
                </div>

                {{-- Tax row --}}
                <div style="display:flex;justify-content:space-between;
                            font-size:13px;padding:10px 0;
                            border-bottom:1px solid rgba(255,255,255,0.1)">
                    <span style="color:rgba(255,255,255,0.7)">
                        <i class="fa fa-plus" style="font-size:10px;margin-right:4px;color:#93c5fd"></i>
                        Tax (6% of Subtotal)
                    </span>
                    <span style="color:#93c5fd;font-weight:600">
                        + RM {{ number_format((float)$Tax, 2) }}
                    </span>
                </div>

                {{-- Discount row --}}
                <div style="display:flex;justify-content:space-between;
                            font-size:13px;padding:10px 0;
                            border-bottom:1px solid rgba(255,255,255,0.1)">
                    <span style="color:rgba(255,255,255,0.7)">
                        <i class="fa fa-minus" style="font-size:10px;margin-right:4px;color:#6ee7b7"></i>
                        Discount / Credit Note
                    </span>
                    <span style="color:#6ee7b7;font-weight:600">
                        - RM {{ number_format((float)$Discount, 2) }}
                    </span>
                </div>

                {{-- Penalty row --}}
                <div style="display:flex;justify-content:space-between;
                            font-size:13px;padding:10px 0;
                            border-bottom:1px solid rgba(255,255,255,0.15)">
                    <span style="color:rgba(255,255,255,0.7)">
                        <i class="fa fa-minus" style="font-size:10px;margin-right:4px;color:#fca5a5"></i>
                        Penalty (1% of Subtotal{{ $late_delivery ? ' — Late Delivery' : ' — Not Applied' }})
                    </span>
                    <span style="color:{{ $late_delivery ? '#fca5a5' : 'rgba(255,255,255,0.3)' }};font-weight:600">
                        - RM {{ number_format((float)$Penalty, 2) }}
                    </span>
                </div>

                {{-- Formula reminder --}}
                <div style="font-size:10px;color:rgba(255,255,255,0.4);
                            margin:8px 0;text-align:center;font-style:italic">
                    Total = (PO Price − Discount − Penalty) + Tax
                </div>

                {{-- Total Amount --}}
                <div style="display:flex;justify-content:space-between;
                            align-items:center;padding-top:10px">
                    <span style="color:#fbbf24;font-size:14px;font-weight:700">
                        Total Claim Amount
                    </span>
                    <span style="color:#fbbf24;font-size:24px;font-weight:800">
                        RM {{ number_format((float)$TotalAmount, 2) }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Submit button --}}
        <div class="col-12 mt-2">
            <button type="submit"
                style="width:100%;padding:12px;background:linear-gradient(135deg,#1e3a8a,#1e40af);
                       color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;
                       cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading.remove wire:target="save">
                    <i class="fa fa-paper-plane me-1"></i>
                    Submit Invoice — RM {{ number_format((float)$TotalAmount, 2) }}
                </span>
                <span wire:loading wire:target="save">
                    <i class="fa fa-spinner fa-spin me-1"></i> Submitting...
                </span>
            </button>
        </div>

    </form>
</div>
