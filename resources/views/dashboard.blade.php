<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Pending Invitations Section -->
            @if(isset($invitations) && $invitations->isNotEmpty())
            <div class="bg-indigo-50 border border-indigo-200 dark:bg-gray-800 dark:border-indigo-900 rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-200 mb-4">Pending Invitations</h3>
                    <div class="space-y-4">
                        @foreach($invitations as $invitation)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-4 rounded shadow-sm border border-gray-100 dark:border-gray-600">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        You have been invited to join <span class="font-bold">{{ $invitation->colocation->name }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                                    @csrf
                                    <x-primary-button class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900">
                                        Accept
                                    </x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                                    @csrf
                                    <x-danger-button>
                                        Refuse
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Dashboard Content -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="text-center py-8">
                        @if(auth()->user()->hasActiveColocation())
                        @php
                        $membership = auth()->user()->activeMembership();
                        @endphp
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Welcome to {{ $membership->colocation->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            You are currently an active <span class="font-semibold text-indigo-600 dark:text-indigo-400 uppercase text-sm">{{ $membership->role }}</span> in this colocation.
                        </p>
                        <a href="{{ route('colocations.show', $membership->colocation_id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Go to My Colocation
                        </a>
                        @else
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 8h8"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">You don't have an active colocation yet.</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-lg mx-auto">
                            EasyColoc helps you manage shared expenses effortlessly. Create a new colocation to start tracking expenses, settling debts, and inviting your roommates.
                        </p>
                        <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Create a Colocation
                        </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>