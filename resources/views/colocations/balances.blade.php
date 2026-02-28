<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('colocations.index') }}" class="hover:underline">Colocations</a>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('colocations.show', $colocation) }}" class="hover:underline">{{ $colocation->name }}</a>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Balances & Repayments</span>
                </div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    Balances & Repayments
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Balances Breakdown -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Individual Balances</h3>
                        <p class="text-sm text-gray-500">How much each person has paid vs their share.</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($balances as $userId => $data)
                            <div class="p-4 rounded-xl border {{ $data['balance'] >= 0 ? 'bg-green-50 border-green-100 dark:bg-green-900/10 dark:border-green-900/30' : 'bg-red-50 border-red-100 dark:bg-red-900/10 dark:border-red-900/30' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center text-sm font-bold shadow-sm">
                                            {{ strtoupper(substr($data['user']->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold dark:text-white">{{ $data['user']->name }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wider">Member</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-black {{ $data['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $data['balance'] >= 0 ? '+' : '' }}{{ number_format($data['balance'], 2) }} €
                                        </div>
                                        <div class="text-[10px] font-medium text-gray-400 uppercase">Current Balance</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                                    <div>
                                        <div class="text-[10px] text-gray-400 uppercase">Paid</div>
                                        <div class="text-xs font-bold dark:text-gray-200">{{ number_format($data['amount_paid'], 2) }} €</div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-gray-400 uppercase">Share</div>
                                        <div class="text-xs font-bold dark:text-gray-200">{{ number_format($data['share'], 2) }} €</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-gray-400 uppercase">Transfers</div>
                                        <div class="text-xs font-bold dark:text-gray-200">{{ number_format($data['total_sent'] - $data['total_received'], 2) }} €</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Suggested Repayments -->
                <div class="space-y-6">
                    <div class="bg-indigo-600 rounded-2xl shadow-xl overflow-hidden text-white">
                        <div class="px-6 py-8 border-b border-indigo-500/50 relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="text-2xl font-bold mb-2">Who owes whom?</h3>
                                <p class="text-indigo-100 text-sm">Suggested transactions to settle all debts with the fewest steps.</p>
                            </div>
                            <!-- Subtle background decoration -->
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        </div>
                        <div class="p-6 bg-white dark:bg-gray-800">
                            <ul class="space-y-4">
                                @forelse($settlements as $settlement)
                                <li class="group p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-red-500 uppercase mb-1">Debtor</span>
                                                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center border-2 border-red-100 dark:border-red-900/30">
                                                    <span class="text-red-600 dark:text-red-400 font-bold">{{ strtoupper(substr($settlement['from']->name, 0, 1)) }}</span>
                                                </div>
                                                <span class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $settlement['from']->name }}</span>
                                            </div>

                                            <div class="flex flex-col items-center px-4">
                                                <div class="text-lg font-black text-gray-900 dark:text-white mb-1">
                                                    {{ number_format($settlement['amount'], 2) }} €
                                                </div>
                                                <svg class="w-8 h-8 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                            </div>

                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-green-500 uppercase mb-1">Receiver</span>
                                                <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center border-2 border-green-100 dark:border-green-900/30">
                                                    <span class="text-green-600 dark:text-green-400 font-bold">{{ strtoupper(substr($settlement['to']->name, 0, 1)) }}</span>
                                                </div>
                                                <span class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $settlement['to']->name }}</span>
                                            </div>
                                        </div>

                                        @if(auth()->id() === $settlement['from']->id)
                                        <form action="{{ route('colocations.payments.store', $colocation) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="to_user_id" value="{{ $settlement['to']->id }}">
                                            <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                            <button type="submit" class="ml-4 px-4 py-2 bg-indigo-600 text-white text-xs font-black rounded-lg hover:bg-indigo-700 uppercase tracking-tighter shadow-lg shadow-indigo-600/20 transition-all hover:scale-105 active:scale-95">
                                                Mark Paid
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </li>
                                @empty
                                <div class="text-center py-10">
                                    <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">All Clear!</h4>
                                    <p class="text-gray-500 text-sm">Everyone is settled up. No debts remaining.</p>
                                </div>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-xl p-4 flex space-x-3">
                        <svg class="w-6 h-6 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                            <strong>How is this calculated?</strong> We take the total expenses, divide by the number of members, and adjust by how much each person has already paid or transferred directly to others.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>