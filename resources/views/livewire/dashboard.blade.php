<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- User Info Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Willkommen, {{ auth()->user()->name }}!</h2>
                        <p class="text-gray-600">
                            Rolle: <span class="font-semibold">{{ ucfirst(auth()->user()->role) }}</span> |
                            E-Mail: <span class="font-semibold">{{ auth()->user()->email }}</span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Selection Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-xl font-bold mb-4">Produkt-Verwaltung</h3>

                {{-- Action Dropdown --}}
                <div class="mb-6">
                    <label for="action" class="block text-sm font-medium text-gray-700 mb-2">
                        Aktion auswählen
                    </label>
                    <select wire:model.live="selectedAction" id="action"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Bitte wählen --</option>
                        <optgroup label="Lesen (alle Rollen)">
                            <option value="get-by-category">Produkte nach Kategorie abrufen</option>
                            <option value="get-product">Einzelnes Produkt abrufen</option>
                            <option value="out-of-stock">Nicht vorrätige Produkte</option>
                            <option value="low-stock-products">Suche Produkte mit Stückzahl weniger als 10</option>
                            <option value="view-categories">Alle existierenden Kategorien anzeigen</option>
                        </optgroup>
                        <optgroup label="Bestand (alle Rollen)">
                            <option value="stock-adjustment">Bestandsanpassung</option>
                        </optgroup>
                        @if(in_array(auth()->user()->role, ['admin', 'manager']))
                            <optgroup label="Verwaltung (Admin/Manager)">
                                <option value="create-product">Neues Produkt erstellen</option>
                                <option value="update-product">Produkt aktualisieren</option>
                                <option value="delete-product">Produkt löschen</option>
                            </optgroup>
                        @endif
                        @if(in_array(auth()->user()->role, ['admin', 'manager']))
                            <optgroup label="Kategorien (Admin/Manager)">
                                <option value="create-category">Neue Kategorie erstellen</option>
                                <option value="delete-category">Kategorie löschen</option>
                            </optgroup>
                        @endif
                    </select>
                </div>

                {{-- Dynamic Input Forms --}}
                @if($selectedAction)
                    <form wire:submit.prevent="executeAction" class="space-y-4">

                        {{-- Get Products by Category --}}
                        @if($selectedAction === 'get-by-category')
                            <div class="space-y-4">
                                <div class="text-sm text-gray-600 mb-3">
                                    Suchen Sie nach Kategorie-ID <strong>und/oder</strong> Kategoriename:
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategorie-ID</label>
                                    <input wire:model="categoryId" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 1">
                                </div>
                                <div class="text-center text-gray-500 font-semibold">ODER</div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategoriename</label>
                                    <input wire:model="categoryName" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Smartphones">
                                </div>
                            </div>
                        @endif

                        {{-- Get Single Product --}}
                        @if($selectedAction === 'get-product')
                            <div class="space-y-4">
                                <div class="text-sm text-gray-600 mb-3">
                                    Suchen Sie nach Produkt-ID <strong>und/oder</strong> Produktname
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produkt-ID</label>
                                    <input wire:model="productId" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 5">
                                </div>
                                <div class="text-center text-gray-500 font-semibold">UND / ODER</div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produktname</label>
                                    <input wire:model="productName" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Laptop Dell XPS 15">
                                </div>
                            </div>
                        @endif

                        {{-- Delete Product --}}
                        @if($selectedAction === 'delete-product')
                            <div class="space-y-4">
                                <div class="text-sm text-gray-600 mb-3">
                                    Löschen Sie nach Produkt-ID <strong>UND</strong> dazugehörigem Produktname
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produkt-ID *</label>
                                    <input wire:model="productId" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 5" required>
                                </div>
                                <div class="text-center text-gray-500 font-semibold">UND</div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produktname *</label>
                                    <input wire:model="productName" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Laptop Dell XPS 15" required>
                                </div>
                                <p class="mt-1 text-sm text-red-600 font-semibold">Achtung: Diese Aktion kann nicht rückgängig
                                    gemacht werden!</p>
                            </div>
                        @endif

                        {{-- Create Product --}}
                        @if($selectedAction === 'create-product')
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produktname *</label>
                                    <input wire:model="name" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Laptop Dell XPS">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preis * (€)</label>
                                    <input wire:model="price" type="number" step="0.01"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 999.99">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategorie-ID *</label>
                                    <input wire:model="categoryIdForCreate" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Anfangsbestand *</label>
                                    <input wire:model="stockQuantity" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 10">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lieferant</label>
                                    <input wire:model="supplier" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. TechSupply GmbH">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hersteller</label>
                                    <input wire:model="manufacturer" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Dell">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                                    <input wire:model="ISBN" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. 978-3-16-148410-0">
                                </div>
                        @endif

                            {{-- Create Category --}}
                            @if($selectedAction === 'create-category')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategoriename *</label>
                                    <input wire:model="categoryNameForCreate" type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="z.B. Smartphones, Laptops, Tablets">
                                    <p class="mt-1 text-sm text-gray-500">Eine neue Produktkategorie anlegen (Admin/Manager)</p>
                                </div>
                            @endif

                            {{-- Delete Category --}}
                            @if($selectedAction === 'delete-category')
                                <div class="space-y-4">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Achtung: Kategorie löschen</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p>Das Löschen einer Kategorie löscht auch <strong>alle Produkte</strong> in
                                                        dieser Kategorie!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategorie-ID *</label>
                                        <input wire:model="categoryId" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategoriename zur
                                            Bestätigung *</label>
                                        <input wire:model="categoryName" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Geben Sie den Kategorienamen zur Bestätigung ein">
                                        <p class="mt-1 text-sm text-red-600">Sicherheitsabfrage: Name muss exakt übereinstimmen!
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Update Product --}}
                            @if($selectedAction === 'update-product')
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Produkt-ID *</label>
                                        <input wire:model="productId" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 5">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neuer Name *</label>
                                        <input wire:model="name" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. Laptop Dell XPS 15">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neuer Preis * (€)</label>
                                        <input wire:model="price" type="number" step="0.01"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 1099.99">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neue Kategorie-ID *</label>
                                        <input wire:model="categoryIdForCreate" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neuer Lieferant</label>
                                        <input wire:model="supplier" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. TechSupply GmbH">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neuer Hersteller</label>
                                        <input wire:model="manufacturer" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. Dell">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Neue ISBN</label>
                                        <input wire:model="ISBN" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 978-3-16-148410-0">
                                    </div>
                                </div>
                            @endif

                            {{-- Stock Adjustment --}}
                            @if($selectedAction === 'stock-adjustment')
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Produkt-ID *</label>
                                        <input wire:model="productId" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. 5">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Produktname *</label>
                                        <input wire:model="productName" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. Produktname">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Anpassung * (+/-
                                            Stück)</label>
                                        <input wire:model="adjustment" type="text"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="z.B. -5 oder +10">
                                        <p class="mt-1 text-sm text-gray-500">Negative Werte reduzieren, positive erhöhen den
                                            Bestand</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Submit Button --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Ausführen
                                </button>
                            </div>
                    </form>
                @endif

                {{-- Error Display --}}
                @if($error)
                    <div class="mt-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Fehler</h3>
                                <p class="mt-2 text-sm text-red-700">{{ $error }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Result Display --}}
                @if($result)
                    <div class="mt-6">
                        {{-- Check if result is a Category (has 'name' but no 'price' property) --}}
                        @if(is_object($result) && isset($result->id) && isset($result->name) && !isset($result->price))
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Kategorie erfolgreich erstellt</h3>
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $result->name }}</h3>
                                </div>
                                <div class="border-t border-gray-200">
                                    <dl>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Kategorie-ID</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $result->id }}</dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Kategoriename</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <flux:badge color="blue" size="lg">{{ $result->name }}</flux:badge>
                                            </dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Erstellt am</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                {{ \Carbon\Carbon::parse($result->created_at)->format('d.m.Y H:i') }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            {{-- Single Product Display --}}
                        @elseif(is_object($result) && isset($result->id))
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ergebnis der Bestandsanpassung</h3>
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $result->name }}</h3>
                                </div>
                                <div class="border-t border-gray-200">
                                    <dl>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Produkt-ID</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $result->id }}</dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $result->name }}
                                            </dd>
                                        </div>
                                        @if(isset($result->isbn) && $result->isbn)
                                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                                <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $result->isbn }}
                                                </dd>
                                            </div>
                                        @endif
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Lagerbestand</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <flux:badge
                                                    :color="$result->count == 0 ? 'red' : ($result->count < 10 ? 'yellow' : 'green')">
                                                    {{ $result->count }} Stück
                                                </flux:badge>
                                            </dd>
                                        </div>
                                        @if(isset($result->manufacturer) && $result->manufacturer)
                                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                                <dt class="text-sm font-medium text-gray-500">Hersteller</dt>
                                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                    {{ $result->manufacturer }}
                                                </dd>
                                            </div>
                                        @endif
                                        @if(isset($result->supplier) && $result->supplier)
                                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                                <dt class="text-sm font-medium text-gray-500">Lieferant</dt>
                                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $result->supplier }}
                                                </dd>
                                            </div>
                                        @endif
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Preis</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                                {{ number_format($result->price, 2, ',', '.') }} €
                                            </dd>
                                        </div>
                                        @if(isset($result->last_supplied_at) && $result->last_supplied_at)
                                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                                <dt class="text-sm font-medium text-gray-500">Letzte Lieferung</dt>
                                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                    {{ \Carbon\Carbon::parse($result->last_supplied_at)->format('d.m.Y H:i') }}
                                                </dd>
                                            </div>
                                        @endif
                                        @if(isset($result->category))
                                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                                <dt class="text-sm font-medium text-gray-500">Kategorie</dt>
                                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                    <flux:badge color="blue">
                                                        {{ $result->category->name ?? 'N/A' }} (ID:
                                                        {{ $result->category->id ?? 'N/A' }})
                                                    </flux:badge>
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            {{-- Multiple Products Display (Table with Flux Badges) --}}
                        @elseif($selectedAction !== 'view-categories' && $selectedAction !== 'delete-category' && (is_array($result) || (is_object($result) && !isset($result->id))))
                            @php
                                $products = is_array($result) ? $result : (array) $result;
                            @endphp

                            {{-- Check if this is a delete operation result --}}
                            @if($selectedAction === 'delete-product')
                                <h3 class="text-lg font-medium text-red-600 mb-4">
                                    <svg class="inline h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Produkt erfolgreich gelöscht
                                </h3>
                            @endif

                            <div class="flex flex-col">
                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            ID</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Name</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            ISBN</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Kategorie</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Bestand</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Preis</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Hersteller</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Lieferant</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Letzte Lieferung</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @forelse($products as $product)
                                                        @php
                                                            $prod = is_object($product) ? $product : (object) $product;
                                                            $count = $prod->count ?? 0;
                                                        @endphp
                                                        <tr>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {{ $prod->id ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $prod->name ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $prod->isbn ?? '-' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                @if(isset($prod->category))
                                                                    <flux:badge color="blue">
                                                                        {{ is_object($prod->category) ? $prod->category->name : ($prod->category['name'] ?? 'N/A') }}
                                                                        (ID:
                                                                        {{ is_object($prod->category) ? $prod->category->id : ($prod->category['id'] ?? 'N/A') }})
                                                                    </flux:badge>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                <flux:badge
                                                                    :color="$count == 0 ? 'red' : ($count < 10 ? 'yellow' : 'green')"
                                                                    class="{{ $count <= 5 ? 'ring-2 ring-red-600' : '' }}">
                                                                    {{ $count }} Stück
                                                                </flux:badge>
                                                            </td>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                {{ number_format($prod->price ?? 0, 2, ',', '.') }} €
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $prod->manufacturer ?? '-' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $prod->supplier ?? '-' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                @if(isset($prod->last_supplied_at) && $prod->last_supplied_at)
                                                                    {{ \Carbon\Carbon::parse($prod->last_supplied_at)->format('d.m.Y') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                                                Keine Produkte gefunden</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Delete Category Success Message --}}
                        @if($selectedAction === 'delete-category' && $result)
                            <div class="mt-6">
                                <div class="rounded-md bg-red-50 p-4 border border-red-200">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">
                                                Kategorie erfolgreich gelöscht
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                @php
                                                    $deleteResult = is_object($result) ? $result : (object) $result;
                                                @endphp
                                                <ul class="list-disc list-inside space-y-1">
                                                    <li><strong>Kategorie-ID:</strong> {{ $deleteResult->category_id ?? 'N/A' }}
                                                    </li>
                                                    <li><strong>Kategoriename:</strong>
                                                        {{ $deleteResult->category_name ?? 'N/A' }}</li>
                                                    <li><strong>Gelöschte Produkte:</strong>
                                                        {{ $deleteResult->deleted_products_count ?? 0 }}</li>
                                                    <li class="mt-2 text-red-600 font-semibold">
                                                        {{ $deleteResult->message ?? 'Kategorie wurde gelöscht.' }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- View All Categories --}}
                        @if($selectedAction === 'view-categories' && $result)
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Alle Kategorien</h4>
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                    <button wire:click="showCategories"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        Kategorien-Ansicht wechseln 
                                    </button>
                                    <div class="overflow-x-auto">
                                        @if(!$showCategoriesComponent)
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            ID</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Kategoriename</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Gesamtanzahl enthaltener Produkttypen</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Niedriger Bestand (&lt;10)</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Nicht vorrätig</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Erstellt am</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @forelse($result as $category)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                {{ $category->id }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {{ $category->name }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $category->products_count ?? 0 }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 text-sm">
                                                                @if($category->products_with_low_stock_count && $category->products_with_low_stock_count > 0)
                                                                    <div class="flex flex-col space-y-1">
                                                                        @foreach($category->productsWithLowStock as $product)
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-50 text-yellow-800 border border-yellow-200 w-fit">
                                                                                {{ $product->name }} ({{ $product->count }})
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 text-sm">
                                                                @if($category->productsOutOfStock && $category->productsOutOfStock->count() > 0)
                                                                    <div class="flex flex-col space-y-1">
                                                                        @foreach($category->productsOutOfStock as $product)
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded text-xs bg-red-50 text-red-800 border border-red-200 w-fit">
                                                                                {{ $product->name }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ \Carbon\Carbon::parse($category->created_at)->format('d.m.Y H:i') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Keine
                                                                Kategorien vorhanden</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        @endif
                                        @if($showCategoriesComponent)
                                            @livewire('categories', ['categories' => $result])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>