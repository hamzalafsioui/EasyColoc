<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border border-indigo-100 dark:border-indigo-900">
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-300 mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">You're Invited!</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">To join the colocation</p>
                <h2 class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ $invitation->colocation->name }}</h2>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg text-sm text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                    <p class="italic">"{{ $invitation->colocation->description ?? 'No description provided.' }}"</p>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-600 flex items-center justify-between text-xs">
                        <span>Invited by: <strong>{{ $invitation->inviter->name }}</strong></span>
                        <span>{{ $invitation->colocation->memberships->count() }} members</span>
                    </div>
                </div>

                @if($invitation->status !== 'pending' || $invitation->expires_at < now())
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm text-center">
                    This invitation is no longer active.
            </div>
            @else
            <div class="flex flex-col space-y-4">
                @auth
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <x-primary-button class="w-full justify-center py-3 bg-green-600 hover:bg-green-700 focus:bg-green-700">
                        {{ __('Accept Invitation') }}
                    </x-primary-button>
                </form>
                @else
                <a href="{{ route('register', ['invitation' => $invitation->token]) }}" class="inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 text-center">
                    Login/Register to Join
                </a>
                @endauth

                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                    @csrf
                    <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-red-500 transition-colors">
                        No thanks, I'll pass
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-indigo-500 underline">What is EasyColoc?</a>
        </div>
    </div>
    </div>
</x-guest-layout>