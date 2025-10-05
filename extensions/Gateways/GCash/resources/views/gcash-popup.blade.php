@extends('layouts.app', ['full_content' => true])

@section('content')

{{-- Define a variable for the initial credit balance by calculating it using the new model attribute --}}
@php
    $initialUserCredits = 0.00;
    if (Auth::check()) {
        // We use the new computed attribute which forces a fresh DB query (User::total_credits)
        $initialUserCredits = Auth::user()->total_credits; 
    }
@endphp

<div class="w-full max-w-4xl mx-auto pt-24 pb-8"> 
    <div class="bg-background-secondary rounded-xl shadow-2xl p-6 md:p-10 w-full h-fit" 
        x-data="{
            // We pass the initial value as a string to avoid Alpine.js parsing issues
            userCredits: '{{ number_format($initialUserCredits, 2, '.', '') }}', 
            invoiceTotal: {{ number_format($total, 2, '.', '') }},
            invoiceId: {{ $invoice->id }},
            checkCreditsInterval: null,
            isSufficient: false,
            isLoading: false,

            checkCredits() {
                this.isSufficient = this.userCredits >= this.invoiceTotal;
            },

            // CORRECTED FETCH FUNCTION WITH CACHE BUSTER
            fetchCredits() {
                this.isLoading = true;
                
                let apiToken = document.head.querySelector('meta[name=\"api-token\"]')?.content;
                let authorizationHeader = apiToken ? {'Authorization': 'Bearer ' + apiToken} : {};

                // CRITICAL FIX: Append a unique timestamp to the URL to defeat proxy/browser caching
                const cacheBusterUrl = `/api/v1/user/credits/balance?_ts=${Date.now()}`;

                fetch(cacheBusterUrl, { 
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest', 
                        ...authorizationHeader
                    },
                    credentials: 'include' 
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            console.error('Authentication failed for credit fetch. Check API token/session.');
                        }
                        throw new Error('Network response was not ok. Status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Update userCredits and trigger UI refresh
                    this.userCredits = parseFloat(data.balance); 
                    this.checkCredits();
                })
                .catch(error => {
                    console.error('Error fetching credits:', error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            payInvoice() {
                 window.location.href = `/invoices/${this.invoiceId}/pay/credit`;
            }
        }"
        x-init="
            checkCredits();
            checkCreditsInterval = setInterval(() => { fetchCredits() }, 10000);
        "
        x-on:beforeunload.window="clearInterval(checkCreditsInterval)" 
    >
        <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-primary-600 pb-3">GCash Top-up & Payment Portal</h2>

        {{-- Payment Details Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-8">
            {{-- GCash Instructions --}}
            <div>
                <h3 class="text-xl font-bold mb-4 text-primary-400">GCash Top-up Instructions</h3>
                <div class="space-y-4">
                    <p class="text-lg font-bold text-red-400">
                        ⚠️ Important: Ensure your profile's phone number matches your GCash number.
                    </p>
                    <p>
                        Update it in your <a href="/account" class="text-primary hover:underline">Profile Settings</a>
                    </p>
                    <p class="font-semibold">
                        Send the required amount (1 credit = 1 PHP) to the following GCash account:
                    </p>
                    <div class="bg-blue-900 border-l-4 border-blue-500 text-blue-200 p-4 rounded-md">
                        <p>
                            <span class="font-bold">Name:</span> JA●●●E P.
                        </p>
                        <p class="flex items-center gap-2 mt-1">
                            <span class="font-bold">Number:</span>
                            <a class="text-blue-300 hover:underline">09914209201</a>
                            {{-- Alpine.js copy button --}}
                            <button
                                type="button"
                                @click="
                                    $clipboard('09914209201');
                                    $refs.copyText.textContent = 'Copied!';
                                    setTimeout(() => $refs.copyText.textContent = 'Copy', 1500);
                                "
                                class="ml-2 px-2 py-1 text-xs bg-blue-700 text-blue-100 rounded hover:bg-blue-600 transition"
                            >
                                <span x-ref="copyText">Copy</span>
                            </button>
                        </p>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">
                        After sending, wait for your credit balance to update below (auto-refreshing every 10 seconds).
                    </p>
                    <a class="mt-2 text-sm text-gray-400"> If you don't receive the credits within 1 minute, create a ticket on <a href="https://discord.atbphosting.com" class="text-primary hover:underline">Discord</a></a>
                    <br>
                    <a href="https://docs.atbphosting.com/Home/02_Billing/Paying%20With%20Gcash" class="text-primary hover:underline">More Info</a>
                </div>
            </div>

            {{-- Invoice and Credit Status --}}
            <div class="bg-background-tertiary rounded-lg p-6 flex flex-col space-y-4">
                <h3 class="text-xl font-bold mb-2 text-white">Invoice Summary</h3>

                {{-- Invoice Info --}}
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="font-medium text-gray-300">Invoice ID:</span>
                    <span class="font-bold text-white">#{{ $invoice->id }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="font-medium text-gray-300">Invoice Total:</span>
                    <span class="font-bold text-lg text-primary-500">{{ $invoice->currency->symbol }}{{ $invoice->total }} {{ $invoice->currency->code }}</span>
                </div>

                {{-- Credit Balance (Auto-refreshing) --}}
                <div class="pt-4 space-y-2">
                    <div class="flex justify-between items-center bg-background-secondary p-3 rounded-md">
                        <span class="font-medium text-gray-300">Your Current Credit Balance:</span>
                        <span class="font-extrabold text-xl text-green-400 flex items-center">
                            <span x-text="userCredits.toFixed(2)"></span> 
                            <span x-show="isLoading" class="ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </span>
                    </div>

                    {{-- Status/Pay Button --}}
                    <div class="pt-4">
                        <template x-if="isSufficient">
                            <button
                                @click="payInvoice()"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition duration-150"
                            >
                                Pay Invoice #{{ $invoice->id }} with Credits
                            </button>
                        </template>
                        <template x-if="!isSufficient">
                            <div class="bg-red-900 text-red-100 p-3 rounded-lg text-center font-semibold">
                                Insufficient Credits. Please Top-up to proceed.
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection