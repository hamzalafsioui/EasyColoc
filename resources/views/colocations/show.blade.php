<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('colocations.index') }}" class="hover:underline">Colocations</a>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $colocation->name }}</span>
                </div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    {{ $colocation->name }}
                    <span class="ml-3 px-2 py-0.5 text-xs font-semibold rounded-full {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($colocation->status) }}
                    </span>
                </h2>
            </div>

            <div class="flex items-center space-x-3">
                @php $myMembership = $colocation->memberships()->where('user_id', auth()->id())->first(); @endphp

                @if($myMembership && $myMembership->role === 'owner' && $colocation->status === 'active')
                <form action="{{ route('colocations.cancel', $colocation) }}" method="POST" onsubmit="return confirm('Are you sure? This will update all members reputation based on their current balance and close the colocation.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel Colocation
                    </button>
                </form>

                <a href="{{ route('colocations.edit', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Edit
                </a>
                @endif

                @if($myMembership && $myMembership->role !== 'owner' && $colocation->status === 'active')
                <form action="{{ route('memberships.leave', $colocation) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave? Your balance must be settled first.')">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Leave Colocation</button>
                </form>
                @endif
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

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Details & Members -->
                <div class="space-y-6">
                    <!-- About Card -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">About</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                {{ $colocation->description ?? 'No description provided.' }}
                            </p>

                            @if($myMembership && $myMembership->role === 'owner')
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'manage-categories' }))" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    Manage Categories
                                </button>
                            </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-400">
                                Created {{ $colocation->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Members Card -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Members</h3>
                            <div class="flex items-center space-x-4">
                                @if($myMembership && $myMembership->role === 'owner')
                                <a href="{{ route('invitations.index', $colocation) }}" class="text-xs text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 underline">Sent Invitations</a>
                                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'invite-member' }))" class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:underline">+ Invite</button>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-4">
                                @foreach($colocation->memberships()->whereNull('left_at')->with('user')->get() as $member)
                                <li class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                                                {{ $member->user->name }}
                                                @if($member->role === 'owner')
                                                <span class="ml-2 px-1.5 py-0.5 text-[10px] bg-amber-100 text-amber-800 rounded flex items-center">
                                                    <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 2a1 1 0 011 1v1.323l3.945 2.154a1 1 0 01.505.871V10a1 1 0 01-1 1h-1a1 1 0 01-1-1V8.677L10 7.323l-2.45 1.354V10a1 1 0 01-1 1H5.55a1 1 0 01-1-1V7.348a1 1 0 01.505-.871L9 4.323V3a1 1 0 011-1z"></path>
                                                        <path d="M5 13a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"></path>
                                                    </svg>
                                                    Owner
                                                </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">Rep: {{ $member->user->reputation ?? 0 }}</div>
                                        </div>
                                    </div>

                                    @if($myMembership && $myMembership->role === 'owner' && $member->user_id !== auth()->id())
                                    <form action="{{ route('memberships.remove', [$colocation, $member->user]) }}" method="POST" onsubmit="return confirm('Remove this member? Any debt will be imputed to you.')">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Finances & Expenses -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Global Balance Summary -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Balances Summary</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @forelse($balances as $userId => $data)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ strtoupper(substr($data['user']->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium dark:text-white">{{ $data['user']->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold {{ $data['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $data['balance'] >= 0 ? '+' : '' }}{{ number_format($data['balance'], 2) }} €
                                        </span>
                                        <div class="text-[10px] text-gray-400">Paid: {{ number_format($data['amount_paid'], 2) }} €</div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-sm col-span-2 text-center py-4">No balances to calculate yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Settlements -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/50 shadow sm:rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-indigo-100 dark:border-indigo-900/50">
                            <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-300">Who owes what?</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                @forelse($settlements as $settlement)
                                <li class="flex items-center justify-between text-sm py-2 px-4 bg-white dark:bg-gray-800 rounded shadow-sm border border-indigo-50 dark:border-indigo-900">
                                    <div class="flex items-center">
                                        <span class="font-bold text-red-500">{{ $settlement['from']->name }}</span>
                                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        <span class="font-bold text-green-500">{{ $settlement['to']->name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($settlement['amount'], 2) }} €</span>
                                        @if(auth()->id() === $settlement['from']->id)
                                        <form action="{{ route('colocations.payments.store', $colocation) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="to_user_id" value="{{ $settlement['to']->id }}">
                                            <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold rounded hover:bg-indigo-700 uppercase">Mark Paid</button>
                                        </form>
                                        @endif
                                    </div>
                                </li>
                                @empty
                                <div class="text-center py-4">
                                    <svg class="mx-auto h-8 w-8 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-green-700 dark:text-green-400">All settled up! Everyone's even.</p>
                                </div>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Expenses History -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Expenses</h3>
                            <div class="flex items-center space-x-2">
                                <form action="{{ route('colocations.show', $colocation) }}" method="GET" class="flex items-center">
                                    <input type="month" name="month" value="{{ $month }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                                </form>
                                @if($colocation->status === 'active')
                                <a href="{{ route('colocations.expenses.index', $colocation) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline px-2">View All</a>
                                <a href="{{ route('colocations.expenses.create', $colocation) }}" class="px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 uppercase">+ Add</a>
                                @endif
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payer</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($expenses as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            {{ $expense->date->format('M d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $expense->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $expense->category->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $expense->payer->name ?? 'Deleted User' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                                            {{ number_format($expense->amount, 2) }} €
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                            No expenses recorded for this period.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($myMembership && $myMembership->role === 'owner')
    <x-modal name="invite-member" focusable>
        <form method="POST" action="{{ route('invitations.create', $colocation) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Invite a new member') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Enter the email address of the person you want to invite to this colocation.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="email" value="{{ __('Email Address') }}" class="sr-only" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-3/4" placeholder="{{ __('Email') }}" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Send Invitation') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="manage-categories" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Manage Categories') }}
            </h2>

            <div class="space-y-4">
                <!-- Add Category Form -->
                <form method="POST" action="{{ route('colocations.categories.store', $colocation) }}" class="flex items-end space-x-2">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="new_category_name" value="{{ __('New Category') }}" />
                        <x-text-input id="new_category_name" name="name" type="text" class="mt-1 block w-full" placeholder="{{ __('Category Name') }}" required />
                    </div>
                    <x-primary-button>
                        {{ __('Add') }}
                    </x-primary-button>
                </form>

                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Existing Categories</h3>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($colocation->categories as $category)
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $category->name }}</span>
                            <form action="{{ route('colocations.categories.destroy', [$colocation, $category]) }}" method="POST" onsubmit="return confirm('Delete this category? Only possible if no expenses use it.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
    @endif
</x-app-layout>