@extends('layouts.app')

@section('title', 'Support Us - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Hero -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Support Our Community</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Your donations help us maintain and improve our community platform, organize events, and support local developers.
            </p>
        </div>

        <!-- Active Milestones -->
        @if($milestones->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Active Campaigns</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($milestones as $milestone)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                            @if($milestone->image)
                                <img src="{{ $milestone->image }}" alt="{{ $milestone->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-primary-500 to-primary-700 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $milestone->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $milestone->description }}</p>

                                <!-- Progress -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">Raised</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($milestone->current_amount ?? 0, 2) }} / ${{ number_format($milestone->target_amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-primary-600 h-3 rounded-full transition-all" 
                                             style="width: {{ min(($milestone->current_amount ?? 0) / $milestone->target_amount * 100, 100) }}%"></div>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ round(($milestone->current_amount ?? 0) / $milestone->target_amount * 100) }}% of goal
                                    </div>
                                </div>

                                @auth
                                    <button onclick="openDonationModal({{ $milestone->id }})" 
                                            class="w-full py-3 px-4 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition">
                                        Donate Now
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                                        Sign in to Donate
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Why Donate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Why Your Support Matters</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Community Growth</h3>
                    <p class="text-gray-600 dark:text-gray-400">Help us expand and reach more developers across Uganda</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Events & Meetups</h3>
                    <p class="text-gray-600 dark:text-gray-400">Fund community events, workshops, and networking sessions</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Platform Development</h3>
                    <p class="text-gray-600 dark:text-gray-400">Support ongoing development and maintenance of our platform</p>
                </div>
            </div>
        </div>

        <!-- Donation Modal -->
        <div id="donationModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Make a Donation</h3>
                    <button onclick="closeDonationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('donation.initialize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="milestone_id" id="milestone_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount (USD)</label>
                            <input type="number" name="amount" min="1" step="0.01" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="setAmount(10)" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">$10</button>
                            <button type="button" onclick="setAmount(25)" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">$25</button>
                            <button type="button" onclick="setAmount(50)" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">$50</button>
                            <button type="button" onclick="setAmount(100)" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">$100</button>
                        </div>
                        <button type="submit" class="w-full py-3 px-4 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDonationModal(milestoneId) {
    document.getElementById('milestone_id').value = milestoneId;
    document.getElementById('donationModal').classList.remove('hidden');
    document.getElementById('donationModal').classList.add('flex');
}

function closeDonationModal() {
    document.getElementById('donationModal').classList.add('hidden');
    document.getElementById('donationModal').classList.remove('flex');
}

function setAmount(amount) {
    document.querySelector('input[name="amount"]').value = amount;
}
</script>
@endpush
@endsection
