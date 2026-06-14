<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class AllInvoice extends Component
{
    public function render()
    {
        // Fixed: use Auth::user()->vendor->VendorID (not auth('vendor'))
        // Invoice is linked via DOID → DeliveryOrder → VendorID
        $vendor = Auth::user()->vendor;

        $invoices = Invoice::whereHas(
            'deliveryOrder',
            fn($q) => $q->where('VendorID', $vendor->VendorID)
        )
            ->with('deliveryOrder')
            ->latest('SubmittedDate')
            ->get();

        return view('livewire.vendor.all-invoice', compact('invoices'));
    }
}
