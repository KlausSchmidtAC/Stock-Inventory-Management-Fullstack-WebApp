<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryTransaction;
use Illuminate\Auth\Access\AuthorizationException;

class CreateProduct
{
    use AsAction;

    /**
     * Haupt-Business-Logik: Erstellt ein neues Produkt
     * Wird sowohl von Livewire (Dashboard) als auch von API-Routen (asController) verwendet
     */
    public function handle(array $data): Product
    {
        // 1. Berechtigung prüfen
        Gate::authorize('create', Product::class);

        // 2. Validator aufsetzen
        $validator = Validator::make(
            [
                'name' => $data['name'] ?? null,
                'isbn' => $data['ISBN'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'supplier' => $data['supplier'] ?? null,
                // Wir mappen 'count' auf 'stockQuantity' für die Validierung
                'stockQuantity' => $data['stockQuantity'] ?? null,
                'price' => $data['price'] ?? null,
                'categoryId' => $data['categoryId'] ?? null
            ],
            [
                'name' => 'required|string|max:255',
                'isbn' => [
                    'sometimes',
                    'nullable',
                    'string',
                    'max:255',
                    function ($_, $value, $fail) {
                        if (!empty($value) && !$this->isValidIsbn($value)) {
                            $fail('Die eingegebene ISBN ist ungültig.');
                        }
                    }
                ],
                'manufacturer' => 'nullable|string|max:255',
                'supplier' => 'nullable|string|max:255',
                'stockQuantity' => 'required|integer|min:0|max:100',
                'price' => 'required|numeric|min:0',
                'last_supplied_at' => 'nullable|date',
                'categoryId' => 'required|exists:categories,id',
            ],
            [
                'name.required' => 'Bitte geben Sie einen Produktnamen ein.',
                'stockQuantity.required' => 'Bitte geben Sie einen Anfangsbestand ein.',
                'stockQuantity.integer' => 'Anfangsbestand muss eine Ganzzahl sein.',
                'stockQuantity.min' => 'Anfangsbestand darf nicht negativ sein.',
                'stockQuantity.max' => 'Anfangsbestand darf die Obergrenze von 100 nicht überschreiten.',
                'price.required' => 'Bitte geben Sie einen Preis ein.',
                'price.numeric' => 'Preis muss eine Zahl sein.',
                'price.min' => 'Preis darf nicht negativ sein.',
                'categoryId.required' => 'Bitte wählen Sie eine Kategorie aus.',
                'categoryId.exists' => 'Die ausgewählte Kategorie existiert nicht.',
                'supplier.string' => 'Lieferant muss eine Zeichenkette sein.',
                'manufacturer.string' => 'Hersteller muss eine Zeichenkette sein.',
                'last_supplied_at.date' => 'Letztes Lieferdatum muss ein gültiges Datum sein.',
                'isbn.string' => 'ISBN muss eine Zeichenkette sein.',
                'isbn.max' => 'ISBN darf maximal 255 Zeichen lang sein.',
            ]
        );

        // Löst bei Fehlern automatisch die ValidationException aus
        $validated = $validator->validate();

        // 3. Datenbank-Transaktion für Erstellung und Log
        return DB::transaction(function () use ($validated) {

            // Falls deine DB-Spalte 'count' heißt, mappen wir es hier zurück:
            $productData = $validated;
            $productData['count'] = $validated['stockQuantity'];
            $productData['category_id'] = $validated['categoryId'];

            // Produkt erstellen
            $product = Product::create($productData);

            // Inventory-Transaction für die Neuanlage schreiben
            InventoryTransaction::create([
                'product_id' => $product->id,
                'column_name_of_change' => 'all',
                'reason_for_change' => 'Produkt neu angelegt',
                'old_value' => null,
                'new_value' => $product->name,
                'user_id' => Auth::id(),
            ]);

            // Transaktion für den Initial-Bestand loggen (falls Bestand > 0)
            if ($validated['stockQuantity'] > 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'column_name_of_change' => 'count', // oder stockQuantity
                    'reason_for_change' => 'Anfangsbestand eingebucht',
                    'old_value' => 0,
                    'new_value' => $validated['stockQuantity'],
                    'user_id' => Auth::id(),
                ]);
            }

            return $product;
        });
    }

    /**
     * Controller-Methode für API-Routen
     */
    public function asController(Request $request): JsonResponse
    {
        try {
            // Wir übergeben alle relevanten Felder direkt an die handle-Methode
            $product = $this->handle($request->only([
                'name',
                'ISBN',
                'manufacturer',
                'supplier',
                'stockQuantity',
                'price',
                'last_supplied_at',
                'categoryId'
            ]));

            // 201 Created ist der korrekte HTTP-Statuscode für neu erstellte Ressourcen
            return response()->json($product, 201);
        } catch (AuthorizationException $e) {
            // Wenn das Gate::authorize('create') fehlschlägt
            return response()->json([
                'message' => 'Du bist nicht berechtigt, neue Produkte anzulegen.'
            ], 403);
        } catch (ValidationException $e) {
            // Wenn die Validierung (inkl. der ISBN-Prüfung) fehlschlägt
            return response()->json([
                'message' => 'Die übergebenen Daten sind ungültig.',
                'errors' => $e->errors() // Liefert das strukturierte Fehler-Array fürs Frontend
            ], 422);
        } catch (\Exception $e) {
            // Für alle unerwarteten System- oder Datenbankfehler
            return response()->json([
                'message' => 'Ein unerwarteter Fehler ist aufgetreten.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    function isValidIsbn($isbn)
    {
        $isbn = str_replace(['-', ' '], '', strtoupper($isbn));
        if (preg_match('/^\d{9}[\dX]$/', $isbn)) {
            // ISBN-10
            $sum = 0;
            for ($i = 0; $i < 9; $i++) $sum += ((int)$isbn[$i]) * (10 - $i);
            $check = $isbn[9] == 'X' ? 10 : (int)$isbn[9];
            $sum += $check;
            return $sum % 11 == 0;
        } elseif (preg_match('/^97[89]\d{10}$/', $isbn)) {
            // ISBN-13
            $sum = 0;
            for ($i = 0; $i < 12; $i++) $sum += ((int)$isbn[$i]) * ($i % 2 ? 3 : 1);
            $check = (10 - ($sum % 10)) % 10;
            return $check == (int)$isbn[12];
        }
        return false;
    }
}
