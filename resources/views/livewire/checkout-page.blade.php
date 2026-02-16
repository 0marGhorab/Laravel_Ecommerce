<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-warm-darker mb-6 animate-fade-in">Checkout</h1>

        @if ($cart->items->count() === 0)
            <x-empty-state
                title="Your cart is empty"
                description="Add items from the store before checkout."
                ctaText="Continue shopping"
                ctaUrl="{{ route('products.index') }}"
            >
                <x-slot:icon>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    </svg>
                </x-slot:icon>
            </x-empty-state>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="card-cozy p-6">
                        <h2 class="text-lg font-semibold text-warm-darker mb-4">Shipping Information</h2>

                        @if(auth()->check() && $savedShippingAddresses->count() > 0)
                            <div class="mb-4 space-y-2">
                                <label class="block text-sm font-medium text-warm-darker mb-2">Saved Addresses</label>
                                @foreach($savedShippingAddresses as $address)
                                    <label class="flex items-start p-3 border rounded-xl cursor-pointer hover:bg-cream-50 transition
                                        {{ $shippingAddressId == $address->id ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                        <input type="radio" 
                                               wire:model.live="shippingAddressId" 
                                               value="{{ $address->id }}"
                                               class="mt-1 mr-3 text-warm">
                                        <div class="flex-1">
                                            <div class="font-medium text-warm-darker">{{ $address->full_name }}</div>
                                            <div class="text-sm text-warm/80">
                                                {{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}
                                            </div>
                                            <div class="text-sm text-warm/80">
                                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                            </div>
                                            <div class="text-sm text-warm/80">{{ $address->phone }}</div>
                                        </div>
                                    </label>
                                @endforeach

                                <label class="flex items-start p-3 border rounded-xl cursor-pointer hover:bg-cream-50 transition
                                    {{ ($shippingAddressId === null || $shippingAddressId === '') ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                    <input type="radio" 
                                           wire:model.live="shippingAddressId" 
                                           value=""
                                           class="mt-1 mr-3 text-warm">
                                    <div class="font-medium text-warm-darker">Use a new address</div>
                                </label>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="shippingFullName" value="Full Name *" />
                                <x-text-input wire:model.blur="shippingFullName" 
                                              @blur="$wire.validateShippingFullName()"
                                              id="shippingFullName" 
                                              class="block mt-1 w-full {{ $errors->has('shippingFullName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                              type="text" 
                                              required />
                                <x-input-error :messages="$errors->get('shippingFullName')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="shippingPhone" value="Phone Number *" />
                                <div class="mt-1 flex gap-2">
                                    <select wire:model="shippingPhoneCode" 
                                            id="shippingPhoneCode" 
                                            class="w-32 rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30"
                                            required>
                                        @foreach($this->countryCodes as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input wire:model.blur="shippingPhone" 
                                           @blur="$wire.validateShippingPhone()"
                                           id="shippingPhone" 
                                           type="tel" 
                                           inputmode="numeric"
                                           pattern="[0-9]*"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           class="flex-1 rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('shippingPhone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                           placeholder="Enter phone number (7-15 digits)"
                                           required />
                                </div>
                                <x-input-error :messages="$errors->get('shippingPhone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="shippingAddressLine1" value="Address Line 1 *" />
                                <x-text-input wire:model.blur="shippingAddressLine1" 
                                              @blur="$wire.validateShippingAddressLine1()"
                                              id="shippingAddressLine1" 
                                              class="block mt-1 w-full {{ $errors->has('shippingAddressLine1') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                              type="text" 
                                              required />
                                <x-input-error :messages="$errors->get('shippingAddressLine1')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="shippingAddressLine2" value="Address Line 2 (Optional)" />
                                <x-text-input wire:model="shippingAddressLine2" 
                                              id="shippingAddressLine2" 
                                              class="block mt-1 w-full" 
                                              type="text" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="shippingCity" value="City *" />
                                    <x-text-input wire:model.blur="shippingCity" 
                                                  @blur="$wire.validateShippingCity()"
                                                  id="shippingCity" 
                                                  class="block mt-1 w-full {{ $errors->has('shippingCity') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                                  type="text" 
                                                  required />
                                    <x-input-error :messages="$errors->get('shippingCity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="shippingState" value="State/Province" />
                                    <x-text-input wire:model="shippingState" 
                                                  id="shippingState" 
                                                  class="block mt-1 w-full" 
                                                  type="text" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="shippingPostalCode" value="ZIP/Postal Code *" />
                                    <input wire:model.blur="shippingPostalCode" 
                                           @blur="$wire.validateShippingPostalCode()"
                                           id="shippingPostalCode" 
                                           type="text" 
                                           inputmode="numeric"
                                           x-data="{
                                               restrictInput(event) {
                                                   const country = $wire.get('shippingCountry');
                                                   if (country === 'US') {
                                                       let val = event.target.value.replace(/[^0-9-]/g, '');
                                                       val = val.replace(/(\d{5})(\d+)/, '$1-$2');
                                                       val = val.replace(/^(\d{5})-(\d{4}).*/, '$1-$2');
                                                       event.target.value = val;
                                                   } else if (!['CA', 'GB'].includes(country)) {
                                                       event.target.value = event.target.value.replace(/[^0-9]/g, '');
                                                   }
                                               }
                                           }"
                                           @input="restrictInput($event)"
                                           x-bind:pattern="$wire.shippingCountry === 'US' ? '[0-9-]*' : (['CA', 'GB'].includes($wire.shippingCountry) ? '.*' : '[0-9]*')"
                                           x-bind:placeholder="$wire.shippingCountry === 'US' ? '12345 or 12345-6789' : (['CA', 'GB'].includes($wire.shippingCountry) ? 'Enter postal code' : 'Enter postal code (numbers only)')"
                                           class="block mt-1 w-full rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('shippingPostalCode') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                           required />
                                    <x-input-error :messages="$errors->get('shippingPostalCode')" class="mt-2" />
                                </div>

                                    <div>
                                        <x-input-label for="shippingCountry" value="Country *" />
                                        <select wire:model.live="shippingCountry" 
                                                id="shippingCountry" 
                                                class="block mt-1 w-full rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('shippingCountry') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                                                required>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                            <option value="DE">Germany</option>
                                            <option value="FR">France</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('shippingCountry')" class="mt-2" />
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold mb-4">Billing Information</h2>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.live="billingSameAsShipping" 
                                       class="rounded border-cream-300 text-warm shadow-sm focus:ring-warm/30">
                                <span class="ml-2 text-sm text-warm/80">Same as shipping address</span>
                            </label>
                        </div>

                        @if(!$billingSameAsShipping)
                            @if(auth()->check() && $savedBillingAddresses->count() > 0)
                                <div class="mb-4 space-y-2">
                                    <label class="block text-sm font-medium text-warm-darker mb-2">Saved Billing Addresses</label>
                                    @foreach($savedBillingAddresses as $address)
                                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-cream-50 transition
                                            {{ $billingAddressId == $address->id ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                            <input type="radio" 
                                                   wire:model.live="billingAddressId" 
                                                   value="{{ $address->id }}"
                                                   class="mt-1 mr-3 text-warm">
                                            <div class="flex-1">
                                                <div class="font-medium text-warm-darker">{{ $address->full_name }}</div>
                                                <div class="text-sm text-warm/80">
                                                    {{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}
                                                </div>
                                                <div class="text-sm text-warm/80">
                                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                </div>
                                                <div class="text-sm text-warm/80">{{ $address->phone }}</div>
                                            </div>
                                        </label>
                                    @endforeach

                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-cream-50 transition
                                        {{ ($billingAddressId === null || $billingAddressId === '') ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                        <input type="radio" 
                                               wire:model.live="billingAddressId" 
                                               value=""
                                               class="mt-1 mr-3 text-warm">
                                        <div class="font-medium text-warm-darker">Use a new address</div>
                                    </label>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="billingFullName" value="Full Name *" />
                                    <x-text-input wire:model.blur="billingFullName" 
                                                  @blur="$wire.validateBillingFullName()"
                                                  id="billingFullName" 
                                                  class="block mt-1 w-full {{ $errors->has('billingFullName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                                  type="text" 
                                                  required />
                                    <x-input-error :messages="$errors->get('billingFullName')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="billingPhone" value="Phone Number *" />
                                    <div class="mt-1 flex gap-2">
                                        <select wire:model="billingPhoneCode" 
                                                id="billingPhoneCode" 
                                                class="w-32 rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30"
                                                required>
                                            @foreach($this->countryCodes as $code => $label)
                                                <option value="{{ $code }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <input wire:model.blur="billingPhone" 
                                               @blur="$wire.validateBillingPhone()"
                                               id="billingPhone" 
                                               type="tel" 
                                               inputmode="numeric"
                                               pattern="[0-9]*"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                               class="flex-1 rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('billingPhone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                               placeholder="Enter phone number (7-15 digits)"
                                               required />
                                    </div>
                                    <x-input-error :messages="$errors->get('billingPhone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="billingAddressLine1" value="Address Line 1 *" />
                                    <x-text-input wire:model.blur="billingAddressLine1" 
                                                  @blur="$wire.validateBillingAddressLine1()"
                                                  id="billingAddressLine1" 
                                                  class="block mt-1 w-full {{ $errors->has('billingAddressLine1') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                                  type="text" 
                                                  required />
                                    <x-input-error :messages="$errors->get('billingAddressLine1')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="billingAddressLine2" value="Address Line 2 (Optional)" />
                                    <x-text-input wire:model="billingAddressLine2" 
                                                  id="billingAddressLine2" 
                                                  class="block mt-1 w-full" 
                                                  type="text" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="billingCity" value="City *" />
                                        <x-text-input wire:model.blur="billingCity" 
                                                      @blur="$wire.validateBillingCity()"
                                                      id="billingCity" 
                                                      class="block mt-1 w-full {{ $errors->has('billingCity') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                                      type="text" 
                                                      required />
                                        <x-input-error :messages="$errors->get('billingCity')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="billingState" value="State/Province" />
                                        <x-text-input wire:model="billingState" 
                                                      id="billingState" 
                                                      class="block mt-1 w-full" 
                                                      type="text" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="billingPostalCode" value="ZIP/Postal Code *" />
                                        <input wire:model.blur="billingPostalCode" 
                                               @blur="$wire.validateBillingPostalCode()"
                                               id="billingPostalCode" 
                                               type="text" 
                                               inputmode="numeric"
                                               x-data="{
                                                   restrictInput(event) {
                                                       const country = $wire.get('billingCountry');
                                                       if (country === 'US') {
                                                           let val = event.target.value.replace(/[^0-9-]/g, '');
                                                           val = val.replace(/(\d{5})(\d+)/, '$1-$2');
                                                           val = val.replace(/^(\d{5})-(\d{4}).*/, '$1-$2');
                                                           event.target.value = val;
                                                       } else if (!['CA', 'GB'].includes(country)) {
                                                           event.target.value = event.target.value.replace(/[^0-9]/g, '');
                                                       }
                                                   }
                                               }"
                                               @input="restrictInput($event)"
                                               x-bind:pattern="$wire.billingCountry === 'US' ? '[0-9-]*' : (['CA', 'GB'].includes($wire.billingCountry) ? '.*' : '[0-9]*')"
                                               x-bind:placeholder="$wire.billingCountry === 'US' ? '12345 or 12345-6789' : (['CA', 'GB'].includes($wire.billingCountry) ? 'Enter postal code' : 'Enter postal code (numbers only)')"
                                               class="block mt-1 w-full rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('billingPostalCode') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                               required />
                                        <x-input-error :messages="$errors->get('billingPostalCode')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="billingCountry" value="Country *" />
                                        <select wire:model.live="billingCountry" 
                                                id="billingCountry" 
                                                class="block mt-1 w-full rounded-md border-cream-300 shadow-sm focus:border-warm focus:ring-warm/30 {{ $errors->has('billingCountry') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                                                required>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                            <option value="DE">Germany</option>
                                            <option value="FR">France</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('billingCountry')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-warm/70 italic">
                                Billing address will be the same as shipping address.
                            </div>
                        @endif
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold mb-4">Shipping Method</h2>
                        
                        <div class="space-y-3">
                            @foreach($this->shippingMethods as $key => $method)
                                @php
                                    $isAvailable = true;
                                    if ($key === 'free' && $this->subtotal < 100) {
                                        $isAvailable = false;
                                    }
                                @endphp
                                
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-cream-50 transition
                                    {{ $shippingMethod === $key ? 'border-warm bg-cream-100' : 'border-cream-300' }}
                                    {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <input type="radio" 
                                           wire:model.live="shippingMethod" 
                                           value="{{ $key }}"
                                           {{ !$isAvailable ? 'disabled' : '' }}
                                           class="mt-1 mr-3 text-warm">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-warm-darker">{{ $method['name'] }}</div>
                                                <div class="text-sm text-warm/80">{{ $method['description'] }}</div>
                                            </div>
                                            <div class="font-semibold text-warm-darker">
                                                @if($method['cost'] > 0)
                                                    ${{ number_format($method['cost'], 2) }}
                                                @else
                                                    Free
                                                @endif
                                            </div>
                                        </div>
                                        @if($key === 'free' && !$isAvailable)
                                            <div class="text-xs text-red-600 mt-1">
                                                Add ${{ number_format(100 - $this->subtotal, 2) }} more to qualify for free shipping
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        
                        <x-input-error :messages="$errors->get('shippingMethod')" class="mt-2" />
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="card-cozy p-6 sticky top-4">
                        <h2 class="text-lg font-semibold text-warm-darker mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-4">
                            @foreach ($cart->items as $item)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex-1">
                                        <div class="font-medium text-warm-darker">
                                            {{ $item->product?->name ?? 'Product removed' }}
                                        </div>
                                        <div class="text-warm/70">
                                            Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                        </div>
                                    </div>
                                    <div class="font-semibold text-warm-darker">
                                        ${{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-cream-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-warm/80">Subtotal</span>
                                <span class="font-semibold text-warm-darker">${{ number_format($this->subtotal, 2) }}</span>
                            </div>
                            @if($appliedCouponId)
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-warm/80">
                                        Discount ({{ $appliedCouponCode }})
                                        <button type="button" wire:click="removeCoupon" class="ml-1 text-red-500 hover:underline text-xs">Remove</button>
                                    </span>
                                    <span class="font-semibold text-green-600">-${{ number_format($this->discountAmount, 2) }}</span>
                                </div>
                            @else
                                <div class="flex gap-2 items-center">
                                    <input type="text"
                                           wire:model="couponCode"
                                           wire:keydown.enter.prevent="applyCoupon"
                                           placeholder="Coupon code"
                                           class="flex-1 rounded-lg border-cream-300 text-sm focus:border-warm focus:ring-warm/30 text-warm-darker" />
                                    <button type="button"
                                            wire:click="applyCoupon"
                                            class="btn-cozy-soft shrink-0 text-sm py-1.5">
                                        Apply
                                    </button>
                                </div>
                                @error('couponCode')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-warm/80">Shipping</span>
                                <span class="font-semibold text-warm-darker">
                                    @if($shippingMethod)
                                        ${{ number_format($this->shippingCost, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-warm/80">Tax</span>
                                <span class="font-semibold text-warm-darker">
                                    @if($shippingMethod)
                                        ${{ number_format($this->tax, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                            </div>
                            <div class="border-t border-cream-200 pt-2 flex justify-between">
                                <span class="font-semibold text-lg text-warm-darker">Total</span>
                                <span class="font-bold text-lg text-warm-dark">
                                    @if($shippingMethod)
                                        ${{ number_format($this->grandTotal, 2) }}
                                    @else
                                        ${{ number_format($this->subtotal, 2) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-t border-cream-200 pt-4 mt-4">
                            <h3 class="text-sm font-semibold text-warm-darker mb-3">Payment Method</h3>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-cream-50 transition {{ $paymentMethod === 'stripe' ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="stripe" class="text-warm mr-3">
                                    <span class="flex items-center justify-center shrink-0 w-10 h-6">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="#6772E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                            <line x1="1" y1="10" x2="23" y2="10"/>
                                        </svg>
                                    </span>
                                    <div class="flex-1 min-w-0 ml-2">
                                        <span class="font-medium">Card (Stripe)</span>
                                        <span class="ml-2 text-sm text-warm/70">— Pay with card</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-cream-50 transition {{ $paymentMethod === 'paypal' ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="paypal" class="text-warm mr-3">
                                    <span class="flex items-center justify-center shrink-0 w-10 h-6">
                                        <svg class="h-6 w-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72a.77.77 0 0 1 .76-.646h6.23c2.573 0 4.578.543 5.921 1.593 1.343 1.05 1.987 2.51 1.987 4.284 0 .384-.032.768-.096 1.15-.032.192-.064.384-.096.576-.64 2.656-2.508 4.032-5.378 4.032H9.23a.77.77 0 0 0-.76.642l-.704 4.48-.48 3.072-.096.642a.514.514 0 0 0 .512.578h3.39c.416 0 .768-.29.832-.706l.032-.192.48-3.072.064-.384a.834.834 0 0 1 .832-.706h.512c2.124 0 3.774-.48 4.893-1.422 1.12-.942 1.702-2.246 1.702-3.842 0-.448-.032-.896-.096-1.342-.096-.642-.224-1.25-.384-1.826-.64-2.338-2.286-3.554-4.829-3.554H7.684a.77.77 0 0 0-.76.642L4.4 20.963a.514.514 0 0 0 .512.578h2.164a.641.641 0 0 0 .633-.514l.48-2.944.032-.192a.641.641 0 0 1 .635-.514z" fill="#003087"/>
                                            <path d="M20.06 6.944c-.448-2.85-2.286-4.35-5.378-4.35H9.23a.897.897 0 0 0-.896.77L5.5 20.098a.514.514 0 0 0 .512.578h3.39c.416 0 .768-.29.832-.706l.704-4.48a.641.641 0 0 1 .633-.514h.512c2.573 0 4.578-.543 5.921-1.593 1.343-1.05 1.987-2.51 1.987-4.284 0-.192-.032-.384-.064-.576-.224-.898-.48-1.67-.768-2.338.896-.898 1.47-2.05 1.47-3.458 0-.448-.064-.866-.16-1.25z" fill="#009CDE"/>
                                        </svg>
                                    </span>
                                    <div class="flex-1 min-w-0 ml-2">
                                        <span class="font-medium">PayPal</span>
                                        <span class="ml-2 text-sm text-warm/70">— Pay with PayPal</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-cream-50 transition {{ $paymentMethod === 'cod' ? 'border-warm bg-cream-100' : 'border-cream-300' }}">
                                    <input type="radio" wire:model.live="paymentMethod" value="cod" class="text-warm mr-3">
                                    <span class="flex items-center justify-center shrink-0 w-10 h-6 text-warm">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </span>
                                    <div class="flex-1 min-w-0 ml-2">
                                        <span class="font-medium">Cash on Delivery (COD)</span>
                                        <span class="ml-2 text-sm text-warm/70">— Pay when you receive</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @if($this->isFormValid)
                            <button
                                wire:click="placeOrder"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-wait"
                                class="mt-6 w-full btn-cozy py-3"
                            >
                                <span wire:loading.remove wire:target="placeOrder">
                                    @if($paymentMethod === 'stripe')
                                        Pay with Card
                                    @elseif($paymentMethod === 'paypal')
                                        Pay with PayPal
                                    @else
                                        Place Order (Cash on Delivery)
                                    @endif
                                </span>
                                <span wire:loading wire:target="placeOrder" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-cream" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Placing order…
                                </span>
                            </button>
                        @else
                            <button
                                wire:click.prevent="validateAllFields"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-wait"
                                class="mt-6 w-full px-4 py-3 bg-cream-300 text-warm-darker font-medium rounded-xl cursor-pointer hover:bg-warm/20 transition"
                            >
                                Complete Order
                            </button>
                        @endif
                        
                        @if($errors->has('form'))
                            <div class="mt-2 text-sm text-red-600">
                                {{ $errors->first('form') }}
                            </div>
                        @endif
                        
                        @if($errors->has('shippingMethod'))
                            <div class="mt-2 text-sm text-red-600">
                                {{ $errors->first('shippingMethod') }}
                            </div>
                        @endif
                        
                        @if(!$this->isFormValid && $errors->any())
                            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</p>
                                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                    @foreach($errors->get('shippingFullName') as $error)
                                        <li>Shipping Full Name: {{ $error }}</li>
                                    @endforeach
                                    @foreach($errors->get('shippingPhone') as $error)
                                        <li>Shipping Phone: {{ $error }}</li>
                                    @endforeach
                                    @foreach($errors->get('shippingAddressLine1') as $error)
                                        <li>Shipping Address Line 1: {{ $error }}</li>
                                    @endforeach
                                    @foreach($errors->get('shippingCity') as $error)
                                        <li>Shipping City: {{ $error }}</li>
                                    @endforeach
                                    @foreach($errors->get('shippingPostalCode') as $error)
                                        <li>Shipping Postal Code: {{ $error }}</li>
                                    @endforeach
                                    @foreach($errors->get('shippingCountry') as $error)
                                        <li>Shipping Country: {{ $error }}</li>
                                    @endforeach
                                    @if(!$billingSameAsShipping)
                                        @foreach($errors->get('billingFullName') as $error)
                                            <li>Billing Full Name: {{ $error }}</li>
                                        @endforeach
                                        @foreach($errors->get('billingPhone') as $error)
                                            <li>Billing Phone: {{ $error }}</li>
                                        @endforeach
                                        @foreach($errors->get('billingAddressLine1') as $error)
                                            <li>Billing Address Line 1: {{ $error }}</li>
                                        @endforeach
                                        @foreach($errors->get('billingCity') as $error)
                                            <li>Billing City: {{ $error }}</li>
                                        @endforeach
                                        @foreach($errors->get('billingPostalCode') as $error)
                                            <li>Billing Postal Code: {{ $error }}</li>
                                        @endforeach
                                        @foreach($errors->get('billingCountry') as $error)
                                            <li>Billing Country: {{ $error }}</li>
                                        @endforeach
                                    @endif
                                    @foreach($errors->get('shippingMethod') as $error)
                                        <li>Shipping Method: {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
