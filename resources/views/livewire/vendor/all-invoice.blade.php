<div>

    {{-- Invoice list --}}
    @if($invoices->count() > 0)

    <div style="overflow-x:auto">
        <table class="ktm-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>DO Reference</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Discount</th>
                    <th>Penalty</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Claim Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td style="font-weight:600;color:#1e3a8a">
                        {{ $invoice->InvoiceNumber ?? '—' }}
                    </td>
                    <td style="color:var(--text-muted);font-size:12px">
                        {{ $invoice->deliveryOrder->DONumber ?? 'DO#' . $invoice->DOID }}
                    </td>
                    <td>RM {{ number_format($invoice->Subtotal, 2) }}</td>
                    <td style="color:#d97706">RM {{ number_format($invoice->Tax, 2) }}</td>
                    <td style="color:#16a34a">- RM {{ number_format($invoice->Discount, 2) }}</td>
                    <td style="color:#dc2626">- RM {{ number_format($invoice->Penalty, 2) }}</td>
                    <td style="font-weight:700;color:#1e3a8a">
                        RM {{ number_format($invoice->TotalAmount, 2) }}
                    </td>
                    <td>
                        @if($invoice->InvoiceStatus === 'Submitted')
                            <span class="badge-submitted">Submitted</span>
                        @elseif($invoice->InvoiceStatus === 'Finance Review')
                            <span class="badge-review">Finance Review</span>
                        @elseif($invoice->InvoiceStatus === 'Payment Processing')
                            <span class="badge-inactive">Processing</span>
                        @elseif($invoice->InvoiceStatus === 'Paid')
                            <span class="badge-approved">Paid</span>
                        @else
                            <span class="badge-rejected">{{ $invoice->InvoiceStatus }}</span>
                        @endif
                    </td>
                    <td>
                        {{-- Claim progress tracker - satisfies Module 3 requirement --}}
                        @php
                            $stages = ['Submitted','Finance Review','Payment Processing','Paid'];
                            $current = array_search($invoice->InvoiceStatus, $stages);
                            $current = $current === false ? 0 : $current;
                        @endphp
                        <div style="display:flex;align-items:center;gap:3px;min-width:200px">
                            @foreach($stages as $i => $stage)
                                <div style="display:flex;align-items:center;gap:3px">
                                    <div style="width:8px;height:8px;border-radius:50%;
                                        background:{{ $i <= $current ? '#1e3a8a' : '#e5e7eb' }};
                                        flex-shrink:0" title="{{ $stage }}"></div>
                                    @if(!$loop->last)
                                    <div style="width:16px;height:2px;
                                        background:{{ $i < $current ? '#1e3a8a' : '#e5e7eb' }}"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div style="font-size:10px;color:#6b7280;margin-top:3px">
                            Step {{ $current + 1 }} of 4: {{ $invoice->InvoiceStatus }}
                        </div>
                    </td>
                    <td style="font-size:11px;color:var(--text-muted)">
                        {{ $invoice->SubmittedDate
                            ? \Carbon\Carbon::parse($invoice->SubmittedDate)->timezone('Asia/Kuala_Lumpur')->format('d M Y, H:i')
                            : '—' }}
                    </td>
                    <td>
                        <a href="{{ route('vendor.invoice.show', $invoice->InvoiceID) }}"
                           style="color:#1e3a8a;font-size:12px;font-weight:600;text-decoration:none">
                            View →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else

    <div style="text-align:center;padding:48px 24px;color:var(--text-muted)">
        <i class="fa fa-file-invoice"
           style="font-size:40px;display:block;margin-bottom:16px;color:#bfdbfe"></i>
        <div style="font-size:15px;font-weight:600;color:#1e3a8a;margin-bottom:6px">
            No invoices yet
        </div>
        <div style="font-size:13px;margin-bottom:16px">
            Submit your first invoice after your Delivery Order has been approved.
        </div>
        @if(Auth::user()->vendor->isActive())
        <a href="{{ route('vendor.invoice.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;
                  border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
            <i class="fa fa-plus"></i> Submit Invoice
        </a>
        @endif
    </div>

    @endif

</div>
