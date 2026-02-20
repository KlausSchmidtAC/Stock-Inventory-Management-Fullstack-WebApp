<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            TechGear Inventory Management
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Melden Sie sich mit Ihrem Account an
        </p>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form wire:submit="login" class="space-y-6">
            
            {{-- E-Mail Feld --}}
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    E-Mail-Adresse
                </label>
                <div class="mt-2">
                    <input 
                        wire:model="email"
                        id="email" 
                        type="email" 
                        autocomplete="email" 
                        required
                        class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('email') ring-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Passwort Feld --}}
            <div>
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                    Passwort
                </label>
                <div class="mt-2">
                    <input 
                        wire:model="password"
                        id="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required
                        class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Angemeldet bleiben --}}
            <div class="flex items-center">
                <input 
                    wire:model="remember"
                    id="remember" 
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Angemeldet bleiben
                </label>
            </div>

            {{-- Submit Button --}}
            <div>
                <button 
                    type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    Anmelden
                </button>
            </div>
        </form>
    </div>
</div>
