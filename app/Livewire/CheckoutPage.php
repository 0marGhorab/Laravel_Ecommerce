<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Stripe\StripeClient;

class CheckoutPage extends Component
{
    public Cart $cart;

    // Shipping Address Fields
    public ?int $shippingAddressId = null;
    public string $shippingFullName = '';
    public string $shippingPhoneCode = '+1';
    public string $shippingPhone = '';
    public string $shippingAddressLine1 = '';
    public string $shippingAddressLine2 = '';
    public string $shippingCity = '';
    public string $shippingState = '';
    public string $shippingPostalCode = '';
    public string $shippingCountry = 'US';

    // Billing Address Fields
    public bool $billingSameAsShipping = true;
    public ?int $billingAddressId = null;
    public string $billingFullName = '';
    public string $billingPhoneCode = '+1';
    public string $billingPhone = '';
    public string $billingAddressLine1 = '';
    public string $billingAddressLine2 = '';
    public string $billingCity = '';
    public string $billingState = '';
    public string $billingPostalCode = '';
    public string $billingCountry = 'US';

    // Shipping Method
    public ?string $shippingMethod = null;
    public float $shippingCost = 0.00;

    // Payment Method: 'stripe' | 'paypal' | 'cod'
    public string $paymentMethod = 'cod';

    // Coupon
    public string $couponCode = '';
    public ?int $appliedCouponId = null;
    public ?string $appliedCouponCode = null;

    public function mount()
    {
        $this->cart = Cart::current();

        // Redirect if cart is empty
        if ($this->cart->items->count() === 0) {
            return redirect()->route('cart.index');
        }

        // Load cart items with products
        $this->cart->load('items.product');

        // Set default shipping method based on order total
        $subtotal = (float) $this->cart->items->sum('total_price');
        if ($subtotal >= 100) {
            $this->shippingMethod = 'free';
            $this->shippingCost = 0.00;
        } else {
            $this->shippingMethod = 'standard';
            $this->shippingCost = 5.99;
        }

        // Default payment method: first configured option
        if (!empty(config('services.stripe.secret'))) {
            $this->paymentMethod = 'stripe';
        } elseif (class_exists(\App\Services\PayPalService::class) && app(\App\Services\PayPalService::class)->isConfigured()) {
            $this->paymentMethod = 'paypal';
        } else {
            $this->paymentMethod = 'cod';
        }

        // Pre-fill shipping address if user is logged in and has a default address
        if (auth()->check()) {
            $defaultAddress = Address::where('user_id', auth()->id())
                ->where('type', 'shipping')
                ->where('is_default', true)
                ->first();

            if ($defaultAddress) {
                $this->shippingAddressId = $defaultAddress->id;
                $this->shippingFullName = $defaultAddress->full_name;
                $parsedPhone = $this->parsePhoneNumber($defaultAddress->phone);
                $this->shippingPhoneCode = $parsedPhone['code'];
                $this->shippingPhone = $parsedPhone['number'];
                $this->shippingAddressLine1 = $defaultAddress->address_line1;
                $this->shippingAddressLine2 = $defaultAddress->address_line2 ?? '';
                $this->shippingCity = $defaultAddress->city;
                $this->shippingState = $defaultAddress->state ?? '';
                $this->shippingPostalCode = $defaultAddress->postal_code;
                $this->shippingCountry = $defaultAddress->country;
            } elseif (auth()->user()->name) {
                // Pre-fill name from user account
                $this->shippingFullName = auth()->user()->name;
            }
        }
    }

    public function updatedShippingAddressId($value): void
    {
        if (!empty($value) && is_numeric($value) && auth()->check()) {
            $this->selectShippingAddress((int)$value);
        } elseif (empty($value) || $value === '') {
            // Clear all shipping fields when "Use a new address" is selected
            $this->shippingAddressId = null;
            $this->clearShippingFields();
        }
    }

    protected function clearShippingFields(): void
    {
        $this->shippingFullName = '';
        $this->shippingPhoneCode = '+1';
        $this->shippingPhone = '';
        $this->shippingAddressLine1 = '';
        $this->shippingAddressLine2 = '';
        $this->shippingCity = '';
        $this->shippingState = '';
        $this->shippingPostalCode = '';
        $this->shippingCountry = 'US';
        
        // If billing same as shipping, also clear billing
        if ($this->billingSameAsShipping) {
            $this->clearBillingFields();
        }
    }

    public function selectShippingAddress(int $addressId): void
    {
        $address = Address::where('user_id', auth()->id())
            ->where('id', $addressId)
            ->firstOrFail();

        $this->shippingAddressId = $address->id;
        $this->shippingFullName = $address->full_name;
        $parsedPhone = $this->parsePhoneNumber($address->phone);
        $this->shippingPhoneCode = $parsedPhone['code'];
        $this->shippingPhone = $parsedPhone['number'];
        $this->shippingAddressLine1 = $address->address_line1;
        $this->shippingAddressLine2 = $address->address_line2 ?? '';
        $this->shippingCity = $address->city;
        $this->shippingState = $address->state ?? '';
        $this->shippingPostalCode = $address->postal_code;
        $this->shippingCountry = $address->country;

        // Copy to billing if same as shipping is checked
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function useNewShippingAddress(): void
    {
        $this->shippingAddressId = null;
    }

    public function updatedBillingSameAsShipping(): void
    {
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function validateShippingFullName(): void
    {
        $this->validateOnly('shippingFullName', [
            'shippingFullName' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
        ], [
            'shippingFullName.required' => 'Full name is required.',
            'shippingFullName.min' => 'Full name must be at least 2 characters.',
            'shippingFullName.max' => 'Full name cannot exceed 255 characters.',
            'shippingFullName.regex' => 'Full name can only contain letters, spaces, hyphens, apostrophes, and periods.',
        ]);
    }

    public function updatedShippingFullName(): void
    {
        $this->validateShippingFullName();
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function validateShippingPhone(): void
    {
        $this->validateOnly('shippingPhone', [
            'shippingPhone' => ['required', 'string', 'regex:/^[0-9]{7,15}$/'],
        ], [
            'shippingPhone.required' => 'Phone number is required.',
            'shippingPhone.regex' => 'Phone number must be between 7 and 15 digits.',
        ]);
    }

    public function updatedShippingPhone(): void
    {
        $this->validateShippingPhone();
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function updatedShippingPhoneCode(): void
    {
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function validateShippingAddressLine1(): void
    {
        $this->validateOnly('shippingAddressLine1', [
            'shippingAddressLine1' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'shippingAddressLine1.required' => 'Address line 1 is required.',
            'shippingAddressLine1.min' => 'Address must be at least 5 characters.',
            'shippingAddressLine1.max' => 'Address cannot exceed 255 characters.',
        ]);
    }

    public function updatedShippingAddressLine1(): void
    {
        $this->validateShippingAddressLine1();
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function updatedShippingAddressLine2(): void
    {
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function validateShippingCity(): void
    {
        $this->validateOnly('shippingCity', [
            'shippingCity' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
        ], [
            'shippingCity.required' => 'City is required.',
            'shippingCity.min' => 'City must be at least 2 characters.',
            'shippingCity.max' => 'City cannot exceed 100 characters.',
            'shippingCity.regex' => 'City can only contain letters, spaces, hyphens, apostrophes, and periods.',
        ]);
    }

    public function updatedShippingCity(): void
    {
        $this->validateShippingCity();
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function updatedShippingState(): void
    {
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function validateShippingPostalCode(): void
    {
        $rules = ['required', 'string'];
        $customMessages = ['shippingPostalCode.required' => 'Postal code is required.'];
        
        // Add format and range validation based on country
        switch ($this->shippingCountry) {
            case 'US':
                $rules[] = 'regex:/^\d{5}(-\d{4})?$/';
                $customMessages['shippingPostalCode.regex'] = 'ZIP code must be in format 12345 or 12345-6789.';
                
                // Validate ZIP code range (00501-99950)
                $postalCode = trim($this->shippingPostalCode);
                if (preg_match('/^(\d{5})(-\d{4})?$/', $postalCode, $matches)) {
                    $zip5 = (int)$matches[1];
                    if ($zip5 < 501 || $zip5 > 99950) {
                        $this->addError('shippingPostalCode', 'ZIP code must be between 00501 and 99950.');
                        return;
                    }
                    
                    // Validate ZIP+4 extension if present
                    if (isset($matches[2]) && !empty($matches[2])) {
                        $zip4 = (int)ltrim($matches[2], '-');
                        if ($zip4 < 0 || $zip4 > 9999) {
                            $this->addError('shippingPostalCode', 'ZIP+4 extension must be between 0000 and 9999.');
                            return;
                        }
                    }
                } elseif (!empty($postalCode)) {
                    // If format doesn't match, the regex validation will catch it
                    // But we add a custom message here for clarity
                    $this->addError('shippingPostalCode', 'ZIP code must be in format 12345 or 12345-6789.');
                    return;
                }
                break;
            case 'CA':
                $rules[] = 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/';
                $customMessages['shippingPostalCode.regex'] = 'Postal code must be in format A1A 1A1.';
                break;
            case 'GB':
                $rules[] = 'regex:/^[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}$/i';
                $customMessages['shippingPostalCode.regex'] = 'Postcode must be in UK format (e.g., SW1A 1AA).';
                break;
            default:
                // For other countries, allow only numbers
                $rules[] = 'regex:/^\d+$/';
                $rules[] = 'min:4';
                $rules[] = 'max:10';
                $customMessages['shippingPostalCode.regex'] = 'Postal code must contain only numbers.';
                $customMessages['shippingPostalCode.min'] = 'Postal code must be at least 4 digits.';
                $customMessages['shippingPostalCode.max'] = 'Postal code cannot exceed 10 digits.';
        }
        
        $this->validateOnly('shippingPostalCode', [
            'shippingPostalCode' => $rules,
        ], $customMessages);
    }

    public function updatedShippingPostalCode(): void
    {
        $this->validateShippingPostalCode();
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }

    public function updatedShippingCountry(): void
    {
        // Re-validate postal code when country changes
        if (!empty($this->shippingPostalCode)) {
            $this->validateShippingPostalCode();
        }
        if ($this->billingSameAsShipping) {
            $this->copyShippingToBilling();
        }
    }


    protected function parsePhoneNumber(string $phone): array
    {
        // Check if phone starts with a known country code
        $codes = array_keys($this->getCountryCodesProperty());
        
        foreach ($codes as $code) {
            $codeWithoutPlus = ltrim($code, '+');
            if (str_starts_with($phone, $code) || str_starts_with($phone, $codeWithoutPlus)) {
                $number = substr($phone, strlen($code));
                return ['code' => $code, 'number' => $number];
            }
        }
        
        // Default to +1 if no code detected
        return ['code' => '+1', 'number' => $phone];
    }

    protected function copyShippingToBilling(): void
    {
        $this->billingFullName = $this->shippingFullName;
        $this->billingPhoneCode = $this->shippingPhoneCode;
        $this->billingPhone = $this->shippingPhone;
        $this->billingAddressLine1 = $this->shippingAddressLine1;
        $this->billingAddressLine2 = $this->shippingAddressLine2;
        $this->billingCity = $this->shippingCity;
        $this->billingState = $this->shippingState;
        $this->billingPostalCode = $this->shippingPostalCode;
        $this->billingCountry = $this->shippingCountry;
        $this->billingAddressId = null;
    }

    public function updatedBillingAddressId($value): void
    {
        if (!empty($value) && is_numeric($value) && auth()->check()) {
            $this->selectBillingAddress((int)$value);
        } elseif (empty($value) || $value === '') {
            // Clear all billing fields when "Use a new address" is selected
            $this->billingAddressId = null;
            $this->billingSameAsShipping = false;
            $this->clearBillingFields();
        }
    }

    protected function clearBillingFields(): void
    {
        $this->billingFullName = '';
        $this->billingPhoneCode = '+1';
        $this->billingPhone = '';
        $this->billingAddressLine1 = '';
        $this->billingAddressLine2 = '';
        $this->billingCity = '';
        $this->billingState = '';
        $this->billingPostalCode = '';
        $this->billingCountry = 'US';
    }

    public function selectBillingAddress(int $addressId): void
    {
        $address = Address::where('user_id', auth()->id())
            ->where('id', $addressId)
            ->firstOrFail();

        $this->billingAddressId = $address->id;
        $this->billingSameAsShipping = false;
        $this->billingFullName = $address->full_name;
        $parsedPhone = $this->parsePhoneNumber($address->phone);
        $this->billingPhoneCode = $parsedPhone['code'];
        $this->billingPhone = $parsedPhone['number'];
        $this->billingAddressLine1 = $address->address_line1;
        $this->billingAddressLine2 = $address->address_line2 ?? '';
        $this->billingCity = $address->city;
        $this->billingState = $address->state ?? '';
        $this->billingPostalCode = $address->postal_code;
        $this->billingCountry = $address->country;
    }

    public function useNewBillingAddress(): void
    {
        $this->billingAddressId = null;
        $this->billingSameAsShipping = false;
    }

    // Billing validation methods
    public function validateBillingFullName(): void
    {
        $this->validateOnly('billingFullName', [
            'billingFullName' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
        ], [
            'billingFullName.required' => 'Full name is required.',
            'billingFullName.min' => 'Full name must be at least 2 characters.',
            'billingFullName.max' => 'Full name cannot exceed 255 characters.',
            'billingFullName.regex' => 'Full name can only contain letters, spaces, hyphens, apostrophes, and periods.',
        ]);
    }

    public function validateBillingPhone(): void
    {
        $this->validateOnly('billingPhone', [
            'billingPhone' => ['required', 'string', 'regex:/^[0-9]{7,15}$/'],
        ], [
            'billingPhone.required' => 'Phone number is required.',
            'billingPhone.regex' => 'Phone number must be between 7 and 15 digits.',
        ]);
    }

    public function validateBillingAddressLine1(): void
    {
        $this->validateOnly('billingAddressLine1', [
            'billingAddressLine1' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'billingAddressLine1.required' => 'Address line 1 is required.',
            'billingAddressLine1.min' => 'Address must be at least 5 characters.',
            'billingAddressLine1.max' => 'Address cannot exceed 255 characters.',
        ]);
    }

    public function validateBillingCity(): void
    {
        $this->validateOnly('billingCity', [
            'billingCity' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
        ], [
            'billingCity.required' => 'City is required.',
            'billingCity.min' => 'City must be at least 2 characters.',
            'billingCity.max' => 'City cannot exceed 100 characters.',
            'billingCity.regex' => 'City can only contain letters, spaces, hyphens, apostrophes, and periods.',
        ]);
    }

    public function validateBillingPostalCode(): void
    {
        $rules = ['required', 'string'];
        $customMessages = ['billingPostalCode.required' => 'Postal code is required.'];
        
        // Add format and range validation based on country
        switch ($this->billingCountry) {
            case 'US':
                $rules[] = 'regex:/^\d{5}(-\d{4})?$/';
                $customMessages['billingPostalCode.regex'] = 'ZIP code must be in format 12345 or 12345-6789.';
                
                // Validate ZIP code range (00501-99950)
                $postalCode = trim($this->billingPostalCode);
                if (preg_match('/^(\d{5})(-\d{4})?$/', $postalCode, $matches)) {
                    $zip5 = (int)$matches[1];
                    if ($zip5 < 501 || $zip5 > 99950) {
                        $this->addError('billingPostalCode', 'ZIP code must be between 00501 and 99950.');
                        return;
                    }
                    
                    // Validate ZIP+4 extension if present
                    if (isset($matches[2]) && !empty($matches[2])) {
                        $zip4 = (int)ltrim($matches[2], '-');
                        if ($zip4 < 0 || $zip4 > 9999) {
                            $this->addError('billingPostalCode', 'ZIP+4 extension must be between 0000 and 9999.');
                            return;
                        }
                    }
                } elseif (!empty($postalCode)) {
                    // If format doesn't match, the regex validation will catch it
                    // But we add a custom message here for clarity
                    $this->addError('billingPostalCode', 'ZIP code must be in format 12345 or 12345-6789.');
                    return;
                }
                break;
            case 'CA':
                $rules[] = 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/';
                $customMessages['billingPostalCode.regex'] = 'Postal code must be in format A1A 1A1.';
                break;
            case 'GB':
                $rules[] = 'regex:/^[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}$/i';
                $customMessages['billingPostalCode.regex'] = 'Postcode must be in UK format (e.g., SW1A 1AA).';
                break;
            default:
                // For other countries, allow only numbers
                $rules[] = 'regex:/^\d+$/';
                $rules[] = 'min:4';
                $rules[] = 'max:10';
                $customMessages['billingPostalCode.regex'] = 'Postal code must contain only numbers.';
                $customMessages['billingPostalCode.min'] = 'Postal code must be at least 4 digits.';
                $customMessages['billingPostalCode.max'] = 'Postal code cannot exceed 10 digits.';
        }
        
        $this->validateOnly('billingPostalCode', [
            'billingPostalCode' => $rules,
        ], $customMessages);
    }

    public function updatedBillingFullName(): void
    {
        if (!$this->billingSameAsShipping) {
            $this->validateBillingFullName();
        }
    }

    public function updatedBillingPhone(): void
    {
        if (!$this->billingSameAsShipping) {
            $this->validateBillingPhone();
        }
    }

    public function updatedBillingAddressLine1(): void
    {
        if (!$this->billingSameAsShipping) {
            $this->validateBillingAddressLine1();
        }
    }

    public function updatedBillingCity(): void
    {
        if (!$this->billingSameAsShipping) {
            $this->validateBillingCity();
        }
    }

    public function updatedBillingPostalCode(): void
    {
        if (!$this->billingSameAsShipping) {
            $this->validateBillingPostalCode();
        }
    }

    public function updatedBillingCountry(): void
    {
        // Re-validate postal code when country changes
        if (!$this->billingSameAsShipping && !empty($this->billingPostalCode)) {
            $this->validateBillingPostalCode();
        }
    }

    public function getSubtotalProperty(): float
    {
        return (float) $this->cart->items->sum('total_price');
    }

    public function getDiscountAmountProperty(): float
    {
        if (!$this->appliedCouponId) {
            return 0.0;
        }
        $coupon = Coupon::find($this->appliedCouponId);
        if (!$coupon) {
            return 0.0;
        }
        return $coupon->calculateDiscount($this->subtotal);
    }

    public function applyCoupon(): void
    {
        $this->resetValidation('couponCode');
        $code = trim($this->couponCode);
        if ($code === '') {
            $this->addError('couponCode', 'Please enter a coupon code.');
            return;
        }

        $userId = auth()->id();
        $coupon = Coupon::findValid($code, $this->subtotal, $userId, $message);

        if (!$coupon) {
            $this->addError('couponCode', $message ?? 'Invalid coupon code.');
            return;
        }

        $this->appliedCouponId = $coupon->id;
        $this->appliedCouponCode = $coupon->code;
        $this->couponCode = $coupon->code;
    }

    public function removeCoupon(): void
    {
        $this->appliedCouponId = null;
        $this->appliedCouponCode = null;
        $this->couponCode = '';
        $this->resetValidation('couponCode');
    }

    public function getShippingMethodsProperty(): array
    {
        return [
            'standard' => [
                'name' => 'Standard Shipping',
                'description' => '5-7 business days',
                'cost' => 5.99,
            ],
            'express' => [
                'name' => 'Express Shipping',
                'description' => '2-3 business days',
                'cost' => 12.99,
            ],
            'overnight' => [
                'name' => 'Overnight Shipping',
                'description' => 'Next business day',
                'cost' => 24.99,
            ],
            'free' => [
                'name' => 'Free Shipping',
                'description' => '7-10 business days (Orders over $100)',
                'cost' => 0.00,
            ],
        ];
    }

    public function updatedShippingMethod(): void
    {
        if ($this->shippingMethod && isset($this->getShippingMethodsProperty()[$this->shippingMethod])) {
            $method = $this->getShippingMethodsProperty()[$this->shippingMethod];
            
            // Check if free shipping applies (orders over $100)
            if ($this->shippingMethod === 'free' && $this->subtotal < 100) {
                // Auto-select standard shipping if free shipping is not available
                $this->shippingMethod = 'standard';
                $this->shippingCost = 5.99;
                $this->addError('shippingMethod', 'Free shipping is only available for orders over $100.');
                return;
            }
            
            $this->shippingCost = $method['cost'];
        } else {
            $this->shippingCost = 0.00;
        }
    }

    public function getTaxProperty(): float
    {
        // Tax: 8% of (subtotal - discount + shipping)
        $taxable = $this->subtotal - $this->discountAmount + $this->shippingCost;
        return round(max(0, $taxable) * 0.08, 2);
    }

    public function getGrandTotalProperty(): float
    {
        return round($this->subtotal - $this->discountAmount + $this->shippingCost + $this->tax, 2);
    }

    public function getIsFormValidProperty(): bool
    {
        // Check if all required fields are filled
        $shippingValid = !empty($this->shippingFullName) 
            && !empty($this->shippingPhone) 
            && !empty($this->shippingAddressLine1)
            && !empty($this->shippingCity)
            && !empty($this->shippingPostalCode)
            && !empty($this->shippingCountry);

        $billingValid = $this->billingSameAsShipping || (
            !empty($this->billingFullName) 
            && !empty($this->billingPhone) 
            && !empty($this->billingAddressLine1)
            && !empty($this->billingCity)
            && !empty($this->billingPostalCode)
            && !empty($this->billingCountry)
        );

        return $shippingValid && $billingValid && !empty($this->shippingMethod);
    }

    public function placeOrder(): void
    {
        // Require authentication for placing orders
        if (!auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        // Validate all fields
        $this->validateAllFields();

        if (!$this->isFormValid) {
            $this->addError('form', 'Please fill in all required fields correctly.');
            return;
        }

        // Reload cart to ensure we have latest data
        $this->cart->refresh();
        $this->cart->load('items.product');

        if ($this->cart->items->count() === 0) {
            $this->redirect(route('cart.index'));
            return;
        }

        DB::beginTransaction();
        try {
            // Create or get shipping address
            $shippingAddress = null;
            if ($this->shippingAddressId && auth()->check()) {
                $shippingAddress = Address::where('user_id', auth()->id())
                    ->where('id', $this->shippingAddressId)
                    ->first();
            }

            if (!$shippingAddress) {
                $shippingAddress = Address::create([
                    'user_id' => auth()->id(),
                    'type' => 'shipping',
                    'full_name' => $this->shippingFullName,
                    'phone' => $this->shippingPhoneCode . $this->shippingPhone,
                    'address_line1' => $this->shippingAddressLine1,
                    'address_line2' => $this->shippingAddressLine2,
                    'city' => $this->shippingCity,
                    'state' => $this->shippingState,
                    'postal_code' => $this->shippingPostalCode,
                    'country' => $this->shippingCountry,
                    'is_default' => false,
                ]);
            }

            // Create or get billing address
            $billingAddress = null;
            if ($this->billingSameAsShipping) {
                $billingAddress = $shippingAddress;
            } elseif ($this->billingAddressId && auth()->check()) {
                $billingAddress = Address::where('user_id', auth()->id())
                    ->where('id', $this->billingAddressId)
                    ->first();
            }

            if (!$billingAddress && !$this->billingSameAsShipping) {
                $billingAddress = Address::create([
                    'user_id' => auth()->id(),
                    'type' => 'billing',
                    'full_name' => $this->billingFullName,
                    'phone' => $this->billingPhoneCode . $this->billingPhone,
                    'address_line1' => $this->billingAddressLine1,
                    'address_line2' => $this->billingAddressLine2,
                    'city' => $this->billingCity,
                    'state' => $this->billingState,
                    'postal_code' => $this->billingPostalCode,
                    'country' => $this->billingCountry,
                    'is_default' => false,
                ]);
            }

            // Generate unique order number
            do {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            } while (Order::where('order_number', $orderNumber)->exists());

            $discountTotal = $this->discountAmount;
            $couponId = $this->appliedCouponId;
            $couponCode = $this->appliedCouponCode;

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'status' => 'pending',
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'subtotal' => $this->subtotal,
                'discount_total' => $discountTotal,
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'tax_total' => $this->tax,
                'shipping_total' => $this->shippingCost,
                'grand_total' => $this->grandTotal,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
            ]);

            if ($couponId) {
                Coupon::where('id', $couponId)->increment('times_used');
            }

            // Create order items
            foreach ($this->cart->items as $cartItem) {
                if (!$cartItem->product) {
                    throw new \Exception("Product not found for cart item ID: {$cartItem->id}");
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'sku' => $cartItem->product->sku ?? '',
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->total_price,
                ]);
            }

            // Stripe: redirect to Stripe Checkout; cart is cleared and email sent on success callback
            if ($this->paymentMethod === 'stripe' && !empty(config('services.stripe.secret'))) {
                $checkoutSession = $this->createStripeCheckoutSession($order);
                DB::commit();
                $this->redirect($checkoutSession->url);
                return;
            }

            // Clear cart items
            $this->cart->items()->delete();
            
            // Update cart status to converted
            $this->cart->status = 'converted';
            $this->cart->save();

            DB::commit();

            // Send order confirmation email
            $order->load(['user']);
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
            }

            // Store success flag in session for popup after redirect
            session()->flash('order_placed_success', true);

            // Redirect to products dashboard
            $this->redirect(route('products.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Order placement failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'cart_id' => $this->cart->id ?? null,
            ]);
            
            // Show more specific error message
            $errorMessage = 'An error occurred while placing your order. Please try again.';
            if (config('app.debug')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            
            $this->addError('form', $errorMessage);
        }
    }

    private function createStripeCheckoutSession(Order $order): \Stripe\Checkout\Session
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $currency = strtolower(config('services.stripe.currency', 'usd'));
        $amountCents = (int) round($order->grand_total * 100);

        return $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' => 'Order ' . $order->order_number,
                            'description' => config('app.name') . ' – Order total',
                        ],
                    ],
                ],
            ],
            'client_reference_id' => (string) $order->id,
            'success_url' => route('checkout.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index', [], true),
        ]);
    }

    public function validateAllFields(): void
    {
        $rules = [];
        $messages = [];

        // Shipping address validation rules
        $rules['shippingFullName'] = ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-\'\.]+$/'];
        $messages['shippingFullName.required'] = 'Full name is required.';
        $messages['shippingFullName.min'] = 'Full name must be at least 2 characters.';
        $messages['shippingFullName.max'] = 'Full name cannot exceed 255 characters.';
        $messages['shippingFullName.regex'] = 'Full name can only contain letters, spaces, hyphens, apostrophes, and periods.';

        $rules['shippingPhone'] = ['required', 'string', 'regex:/^[0-9]{7,15}$/'];
        $messages['shippingPhone.required'] = 'Phone number is required.';
        $messages['shippingPhone.regex'] = 'Phone number must be between 7 and 15 digits.';

        $rules['shippingAddressLine1'] = ['required', 'string', 'min:5', 'max:255'];
        $messages['shippingAddressLine1.required'] = 'Address line 1 is required.';
        $messages['shippingAddressLine1.min'] = 'Address must be at least 5 characters.';
        $messages['shippingAddressLine1.max'] = 'Address cannot exceed 255 characters.';

        $rules['shippingCity'] = ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s\-\'\.]+$/'];
        $messages['shippingCity.required'] = 'City is required.';
        $messages['shippingCity.min'] = 'City must be at least 2 characters.';
        $messages['shippingCity.max'] = 'City cannot exceed 100 characters.';
        $messages['shippingCity.regex'] = 'City can only contain letters, spaces, hyphens, apostrophes, and periods.';

        // Shipping postal code validation based on country
        $postalRules = ['required', 'string'];
        switch ($this->shippingCountry) {
            case 'US':
                $postalRules[] = 'regex:/^\d{5}(-\d{4})?$/';
                $messages['shippingPostalCode.regex'] = 'ZIP code must be in format 12345 or 12345-6789.';
                break;
            case 'CA':
                $postalRules[] = 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/';
                $messages['shippingPostalCode.regex'] = 'Postal code must be in format A1A 1A1.';
                break;
            case 'GB':
                $postalRules[] = 'regex:/^[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}$/i';
                $messages['shippingPostalCode.regex'] = 'Postcode must be in UK format (e.g., SW1A 1AA).';
                break;
            default:
                $postalRules[] = 'regex:/^\d+$/';
                $postalRules[] = 'min:4';
                $postalRules[] = 'max:10';
                $messages['shippingPostalCode.regex'] = 'Postal code must contain only numbers.';
                $messages['shippingPostalCode.min'] = 'Postal code must be at least 4 digits.';
                $messages['shippingPostalCode.max'] = 'Postal code cannot exceed 10 digits.';
        }
        $rules['shippingPostalCode'] = $postalRules;
        $messages['shippingPostalCode.required'] = 'Postal code is required.';

        $rules['shippingCountry'] = ['required', 'string'];
        $messages['shippingCountry.required'] = 'Country is required.';

        // Billing address validation (if not same as shipping)
        if (!$this->billingSameAsShipping) {
            $rules['billingFullName'] = ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-Z\s\-\'\.]+$/'];
            $messages['billingFullName.required'] = 'Full name is required.';
            $messages['billingFullName.min'] = 'Full name must be at least 2 characters.';
            $messages['billingFullName.max'] = 'Full name cannot exceed 255 characters.';
            $messages['billingFullName.regex'] = 'Full name can only contain letters, spaces, hyphens, apostrophes, and periods.';

            $rules['billingPhone'] = ['required', 'string', 'regex:/^[0-9]{7,15}$/'];
            $messages['billingPhone.required'] = 'Phone number is required.';
            $messages['billingPhone.regex'] = 'Phone number must be between 7 and 15 digits.';

            $rules['billingAddressLine1'] = ['required', 'string', 'min:5', 'max:255'];
            $messages['billingAddressLine1.required'] = 'Address line 1 is required.';
            $messages['billingAddressLine1.min'] = 'Address must be at least 5 characters.';
            $messages['billingAddressLine1.max'] = 'Address cannot exceed 255 characters.';

            $rules['billingCity'] = ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s\-\'\.]+$/'];
            $messages['billingCity.required'] = 'City is required.';
            $messages['billingCity.min'] = 'City must be at least 2 characters.';
            $messages['billingCity.max'] = 'City cannot exceed 100 characters.';
            $messages['billingCity.regex'] = 'City can only contain letters, spaces, hyphens, apostrophes, and periods.';

            // Billing postal code validation based on country
            $billingPostalRules = ['required', 'string'];
            switch ($this->billingCountry) {
                case 'US':
                    $billingPostalRules[] = 'regex:/^\d{5}(-\d{4})?$/';
                    $messages['billingPostalCode.regex'] = 'ZIP code must be in format 12345 or 12345-6789.';
                    break;
                case 'CA':
                    $billingPostalRules[] = 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/';
                    $messages['billingPostalCode.regex'] = 'Postal code must be in format A1A 1A1.';
                    break;
                case 'GB':
                    $billingPostalRules[] = 'regex:/^[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}$/i';
                    $messages['billingPostalCode.regex'] = 'Postcode must be in UK format (e.g., SW1A 1AA).';
                    break;
                default:
                    $billingPostalRules[] = 'regex:/^\d+$/';
                    $billingPostalRules[] = 'min:4';
                    $billingPostalRules[] = 'max:10';
                    $messages['billingPostalCode.regex'] = 'Postal code must contain only numbers.';
                    $messages['billingPostalCode.min'] = 'Postal code must be at least 4 digits.';
                    $messages['billingPostalCode.max'] = 'Postal code cannot exceed 10 digits.';
            }
            $rules['billingPostalCode'] = $billingPostalRules;
            $messages['billingPostalCode.required'] = 'Postal code is required.';

            $rules['billingCountry'] = ['required', 'string'];
            $messages['billingCountry.required'] = 'Country is required.';
        }

        // Shipping method validation
        $rules['shippingMethod'] = ['required'];
        $messages['shippingMethod.required'] = 'Please select a shipping method.';

        // Validate all fields at once
        $this->validate($rules, $messages);
    }

    public function getCountryCodesProperty(): array
    {
        return [
            '+1' => 'US/CA (+1)',
            '+44' => 'UK (+44)',
            '+61' => 'Australia (+61)',
            '+49' => 'Germany (+49)',
            '+33' => 'France (+33)',
            '+39' => 'Italy (+39)',
            '+34' => 'Spain (+34)',
            '+31' => 'Netherlands (+31)',
            '+32' => 'Belgium (+32)',
            '+41' => 'Switzerland (+41)',
            '+43' => 'Austria (+43)',
            '+45' => 'Denmark (+45)',
            '+46' => 'Sweden (+46)',
            '+47' => 'Norway (+47)',
            '+48' => 'Poland (+48)',
            '+351' => 'Portugal (+351)',
            '+353' => 'Ireland (+353)',
            '+358' => 'Finland (+358)',
            '+7' => 'Russia/Kazakhstan (+7)',
            '+81' => 'Japan (+81)',
            '+82' => 'South Korea (+82)',
            '+86' => 'China (+86)',
            '+91' => 'India (+91)',
            '+971' => 'UAE (+971)',
            '+966' => 'Saudi Arabia (+966)',
            '+20' => 'Egypt (+20)',
            '+27' => 'South Africa (+27)',
            '+55' => 'Brazil (+55)',
            '+52' => 'Mexico (+52)',
            '+54' => 'Argentina (+54)',
        ];
    }

    public function render()
    {
        $savedShippingAddresses = collect();
        $savedBillingAddresses = collect();

        if (auth()->check()) {
            // Single query for all user addresses, then split by type (saves one DB round-trip)
            $addresses = Address::where('user_id', auth()->id())
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $savedShippingAddresses = $addresses->where('type', 'shipping')->values();
            $savedBillingAddresses = $addresses->where('type', 'billing')->values();

            // Copy shipping to billing on mount if same as shipping is checked
            if ($this->billingSameAsShipping && !empty($this->shippingFullName)) {
                $this->copyShippingToBilling();
            }
        }

        $stripeConfigured = !empty(config('services.stripe.secret'));
        $paypalConfigured = class_exists(\App\Services\PayPalService::class)
            && app(\App\Services\PayPalService::class)->isConfigured();

        return view('livewire.checkout-page', [
            'savedShippingAddresses' => $savedShippingAddresses,
            'savedBillingAddresses' => $savedBillingAddresses,
            'stripeConfigured' => $stripeConfigured,
            'paypalConfigured' => $paypalConfigured,
        ]);
    }
}
