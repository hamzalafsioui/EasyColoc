<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Global Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Colocations</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_colocations'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Expenses</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_expenses_count'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sum: {{ number_format($stats['total_expenses_amount'], 2) }} €</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-red-500 uppercase">Banned Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_banned_users'] }}</p>
                </div>
            </div>

            <!-- Users Overview Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">User Management</h3>
                            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline flex items-center">
                                Manage All Users
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium dark:text-gray-200">Total Registered</span>
                                </div>
                                <span class="text-lg font-bold dark:text-white">{{ $stats['total_users'] }}</span>
                            </div>
                            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/10 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg text-red-600 dark:text-red-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium dark:text-gray-200">Banned Accounts</span>
                                </div>
                                <span class="text-lg font-bold text-red-600">{{ $stats['total_banned_users'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-600 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden flex flex-col justify-center">
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold mb-4">Platform Growth</h3>
                        <p class="text-indigo-100 mb-6 font-medium">Currently managing {{ $stats['total_colocations'] }} active colocations with a total volume of {{ number_format($stats['total_expenses_amount'], 2) }} € in expenses.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-xl font-bold transition-all hover:bg-indigo-50 active:scale-95">
                            Platform Dashboard
                        </a>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>