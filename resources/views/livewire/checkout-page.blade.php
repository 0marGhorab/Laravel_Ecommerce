<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

        @if ($cart->items->count() === 0)
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">Your cart is empty.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>

                        @if(auth()->check() && $savedShippingAddresses->count() > 0)
                            <div class="mb-4 space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Saved Addresses</label>
                                @foreach($savedShippingAddresses as $address)
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                        {{ $shippingAddressId == $address->id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300' }}">
                                        <input type="radio" 
                                               wire:model.live="shippingAddressId" 
                                               value="{{ $address->id }}"
                                               class="mt-1 mr-3 text-indigo-600">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $address->full_name }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                            </div>
                                            <div class="text-sm text-gray-600">{{ $address->phone }}</div>
                                        </div>
                                    </label>
                                @endforeach

                                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ ($shippingAddressId === null || $shippingAddressId === '') ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300' }}">
                                    <input type="radio" 
                                           wire:model.live="shippingAddressId" 
                                           value=""
                                           class="mt-1 mr-3 text-indigo-600">
                                    <div class="font-medium text-gray-900">Use a new address</div>
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
                                            class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('shippingPhone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
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
                                           class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('shippingPostalCode') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                           required />
                                    <x-input-error :messages="$errors->get('shippingPostalCode')" class="mt-2" />
                                </div>

                                    <div>
                                        <x-input-label for="shippingCountry" value="Country *" />
                                        <select wire:model.live="shippingCountry" 
                                                id="shippingCountry" 
                                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('shippingCountry') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
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
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Same as shipping address</span>
                            </label>
                        </div>

                        @if(!$billingSameAsShipping)
                            @if(auth()->check() && $savedBillingAddresses->count() > 0)
                                <div class="mb-4 space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Saved Billing Addresses</label>
                                    @foreach($savedBillingAddresses as $address)
                                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                            {{ $billingAddressId == $address->id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300' }}">
                                            <input type="radio" 
                                                   wire:model.live="billingAddressId" 
                                                   value="{{ $address->id }}"
                                                   class="mt-1 mr-3 text-indigo-600">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">{{ $address->full_name }}</div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                </div>
                                                <div class="text-sm text-gray-600">{{ $address->phone }}</div>
                                            </div>
                                        </label>
                                    @endforeach

                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                        {{ ($billingAddressId === null || $billingAddressId === '') ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300' }}">
                                        <input type="radio" 
                                               wire:model.live="billingAddressId" 
                                               value=""
                                               class="mt-1 mr-3 text-indigo-600">
                                        <div class="font-medium text-gray-900">Use a new address</div>
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
                                                class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('billingPhone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
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
                                               class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('billingPostalCode') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" 
                                               required />
                                        <x-input-error :messages="$errors->get('billingPostalCode')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="billingCountry" value="Country *" />
                                        <select wire:model.live="billingCountry" 
                                                id="billingCountry" 
                                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('billingCountry') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
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
                            <div class="text-sm text-gray-500 italic">
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
                                
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ $shippingMethod === $key ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300' }}
                                    {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <input type="radio" 
                                           wire:model.live="shippingMethod" 
                                           value="{{ $key }}"
                                           {{ !$isAvailable ? 'disabled' : '' }}
                                           class="mt-1 mr-3 text-indigo-600">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $method['name'] }}</div>
                                                <div class="text-sm text-gray-600">{{ $method['description'] }}</div>
                                            </div>
                                            <div class="font-semibold text-gray-900">
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
                    <div class="bg-white p-6 rounded-lg shadow-sm sticky top-4">
                        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>

                        <!-- Cart Items -->
                        <div class="space-y-3 mb-4">
                            @foreach ($cart->items as $item)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">
                                            {{ $item->product?->name ?? 'Product removed' }}
                                        </div>
                                        <div class="text-gray-500">
                                            Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                        </div>
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        ${{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold">
                                    @if($shippingMethod)
                                        ${{ number_format($this->shippingCost, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-semibold">
                                    @if($shippingMethod)
                                        ${{ number_format($this->tax, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                            </div>
                            <div class="border-t pt-2 flex justify-between">
                                <span class="font-semibold text-lg">Total</span>
                                <span class="font-bold text-lg text-indigo-600">
                                    @if($shippingMethod)
                                        ${{ number_format($this->grandTotal, 2) }}
                                    @else
                                        ${{ number_format($this->subtotal, 2) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($this->isFormValid)
                            <button
                                wire:click="placeOrder"
                                class="mt-6 w-full px-4 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Complete Order
                            </button>
                        @else
                            <button
                                wire:click.prevent="validateAllFields"
                                class="mt-6 w-full px-4 py-3 bg-gray-400 text-white rounded-md cursor-pointer hover:bg-gray-500 transition"
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
