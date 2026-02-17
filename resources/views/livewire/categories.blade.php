<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Kategorien</h1>
            <p class="text-gray-600 dark:text-gray-400">Übersicht aller Produktkategorien</p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <!-- Category Name -->
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                            {{ $category->name }}
                        </h2>

                        <!-- Statistics -->
                        <div class="space-y-3">
                            <!-- Total Products -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Gesamtanzahl unterschiedlicher Produkttypen
                                </span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ $category->products_count }}
                                </span>
                            </div>

                            <!-- Low Stock Products -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Niedriger Bestand (&lt;10)
                                </span>
                                <span class="font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $category->products_with_low_stock_count }}
                                </span>
                            </div>

                            <!-- Out of Stock Products -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Nicht vorrätig
                                </span>
                                <span class="font-bold text-red-600 dark:text-red-400">
                                    {{ $category->products_out_of_stock_count }}
                                </span>
                            </div>
                        </div>

                        <!-- View Products Link -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('dashboard') }}?category={{ $category->id }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium flex items-center justify-center">
                                Produkte anzeigen
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 mx-auto text-yellow-600 dark:text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-yellow-800 dark:text-yellow-300 font-medium">Keine Kategorien vorhanden</p>
                        <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-2">Es wurden noch keine Kategorien angelegt.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
