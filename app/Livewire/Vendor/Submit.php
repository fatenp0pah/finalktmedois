<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\DeliveryOrder;
use App\Models\ProofDelivery;
use App\Models\AuditLog;

class Submit extends Component
{
    use WithFileUploads;

    public $DOID;
    public $InvoiceDescription;
    public $proof_of_delivery;
    public $late_delivery = false;

    // All monetary values based on Subtotal (PO Price)
    public $Subtotal    = 0;
    public $Tax         = 0;   // auto: 6% of Subtotal
    public $Discount    = 0;   // manual: credit note value
    public $Penalty     = 0;   // auto: 1% of Subtotal if late
    public $TotalAmount = 0;   // Subtotal + Tax - Discount - Penalty

    public $availableDOs = [];

    protected $rules = [
        'DOID'               => 'required|exists:delivery_orders,DOID',
        'InvoiceDescription' => 'nullable|string|max:255',
        'proof_of_delivery'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'Subtotal'           => 'required|numeric|min:0.01',
        'Discount'           => 'nullable|numeric|min:0',
    ];

    protected $messages = [
        'DOID.required'              => 'Please select a Delivery Order.',
        'DOID.exists'                => 'Selected Delivery Order is invalid.',
        'proof_of_delivery.required' => 'Please upload proof of delivery.',
        'Subtotal.required'          => 'Please enter the subtotal amount.',
        'Subtotal.min'               => 'Subtotal must be greater than 0.',
    ];

    public function mount()
    {
        $vendor = Auth::user()->vendor;
        $this->availableDOs = DeliveryOrder::where('VendorID', $vendor->VendorID)
            ->where('DOStatus', 'Approved')
            ->whereDoesntHave('invoice')
            ->get();
    }

    // Recalculate live whenever any of these fields change
    public function updated($field)
    {
        if (in_array($field, ['Subtotal', 'Discount', 'late_delivery'])) {
            $this->calculateTotal();
        }
    }

    public function calculateTotal(): void
    {
        // Formula from client document (Calculation_Formula_-_Tax_-Discount-Penalties.txt):
        //
        // Tax     = 6% of PO Price (Subtotal)
        // Discount: PO Price - Discount  → discount deducted from PO Price
        // Penalty : PO Price - Penalty   → penalty deducted from PO Price (1% if late)
        //
        // Therefore:
        // Base    = PO Price - Discount - Penalty   (deductions from PO Price first)
        // Tax     = 6% × PO Price                   (always based on original PO Price)
        // Total   = Base + Tax
        //         = (PO Price - Discount - Penalty) + Tax

        $poPprice = max(0, (float) $this->Subtotal);
        $discount = max(0, (float) $this->Discount);

        // Discount cannot exceed PO Price
        if ($discount > $poPprice) {
            $discount = $poPprice;
            $this->Discount = $discount;
        }

        // Tax = 6% of PO Price (always based on original PO Price, not after deductions)
        $tax = round($poPprice * 0.06, 2);

        // Penalty = 1% of PO Price (only if late delivery ticked)
        $penalty = $this->late_delivery
            ? round($poPprice * 0.01, 2)
            : 0;

        // Base after deductions: PO Price - Discount - Penalty
        $base = $poPprice - $discount - $penalty;

        // Total = Base + Tax
        $total = round($base + $tax, 2);

        // Ensure total never goes negative
        $total = max(0, $total);

        $this->Tax         = $tax;
        $this->Penalty     = $penalty;
        $this->TotalAmount = $total;
    }

    public function save()
    {
        $this->validate();

        // Recalculate one final time before saving to ensure accuracy
        $this->calculateTotal();

        $vendor = Auth::user()->vendor;

        // Store proof of delivery file
        $filePath = $this->proof_of_delivery->store('proof_of_delivery', 'public');
        $fileName = $this->proof_of_delivery->getClientOriginalName();
        $fileType = strtoupper($this->proof_of_delivery->getClientOriginalExtension());

        // Generate invoice number: INV-YYYYMMDD-XXXX
        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad(
            Invoice::count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        // Save Invoice record
        $invoice = Invoice::create([
            'InvoiceNumber'      => $invoiceNumber,
            'DOID'               => $this->DOID,
            'InvoiceDescription' => $this->InvoiceDescription,
            'Subtotal'           => $this->Subtotal,
            'Tax'                => $this->Tax,
            'Discount'           => $this->Discount,
            'Penalty'            => $this->Penalty,
            'TotalAmount'        => $this->TotalAmount,
            'InvoiceStatus'      => 'Submitted',
            'SubmittedDate'      => now(),
        ]);

        // Save Proof of Delivery record
        ProofDelivery::create([
            'DOID'         => $this->DOID,
            'FileName'     => $fileName,
            'FileType'     => $fileType,
            'FileLink'     => $filePath,
            'UploadedDate' => now(),
        ]);

        // Audit log
        AuditLog::log(
            Auth::id(),
            'INVOICE_SUBMITTED',
            'InvoiceID:' . $invoice->InvoiceID,
            'Invoice ' . $invoiceNumber . ' submitted for DO: ' . $this->DOID
                . ' | Total: RM' . number_format($this->TotalAmount, 2)
        );

        $this->reset([
            'DOID',
            'InvoiceDescription',
            'proof_of_delivery',
            'Subtotal',
            'Tax',
            'Discount',
            'Penalty',
            'TotalAmount',
            'late_delivery',
        ]);

        $this->mount();

        session()->flash(
            'success',
            'Invoice submitted successfully! Invoice No: ' . $invoiceNumber .
                ' | Total: RM ' . number_format($invoice->TotalAmount, 2)
        );
    }

    public function render()
    {
        return view('livewire.vendor.submit');
    }
}
