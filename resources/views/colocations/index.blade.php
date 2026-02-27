<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Colocations') }}
            </h2>
            <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                New Colocation
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(isset($colocations) && $colocations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($colocations as $colocation)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold">{{ $colocation->name }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($colocation->status) }}
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-2">
                                {{ $colocation->description ?? 'No description provided.' }}
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex -space-x-2">
                                    @foreach($colocation->users->take(5) as $user)
                                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-gray-800" title="{{ $user->name }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if($colocation->users->count() > 5)
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 text-xs font-bold ring-2 ring-white dark:ring-gray-800">
                                        +{{ $colocation->users->count() - 5 }}
                                    </div>
                                    @endif
                                </div>
                                <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    Manage →
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 8h8"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No colocations found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You haven't joined or created any colocation yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create one now
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>