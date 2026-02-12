<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportProductsFromCsv extends Command
{
    protected $signature = 'products:import-csv
                            {path : Ruta del archivo CSV}
                            {--images-path= : Carpeta base donde están las imágenes}
                            {--no-images : No copiar imágenes al storage}
                            {--dry-run : Simula la importación sin guardar}';

    protected $description = 'Importa productos desde un CSV y organiza imágenes por producto.';

    public function handle(): int
    {
        $csvPath = $this->resolvePath($this->argument('path'));
        if (!File::exists($csvPath)) {
            $this->error("CSV no encontrado: {$csvPath}");
            return 1;
        }

        $imagesBase = $this->option('images-path')
            ? $this->resolvePath($this->option('images-path'))
            : dirname($csvPath);

        $importImages = !$this->option('no-images');
        $dryRun = (bool) $this->option('dry-run');

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->error('No se pudo abrir el CSV.');
            return 1;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            $this->error('El CSV no tiene encabezados.');
            fclose($handle);
            return 1;
        }

        $header = array_map(fn ($item) => trim((string) $item), $header);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $data = $this->mapRow($header, $row);
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                $this->warn("Fila {$line}: nombre vacío, se omite.");
                $skipped++;
                continue;
            }

            $slug = trim((string) ($data['slug'] ?? ''));
            $slug = $slug !== '' ? $slug : Str::slug($name);

            $categoryPath = trim((string) ($data['category'] ?? ''));
            $category = $this->findCategoryByPath($categoryPath);
            if (!$category) {
                $this->warn("Fila {$line}: categoría no encontrada ({$categoryPath}).");
                $skipped++;
                continue;
            }

            $product = Product::withTrashed()->where('slug', $slug)->first();
            $isNew = !$product;
            if (!$product) {
                $product = new Product();
            } elseif ($product->trashed()) {
                $product->restore();
            }

            $shortDescription = trim((string) ($data['short_description'] ?? ''));
            $description = trim((string) ($data['description'] ?? ''));
            if ($description === '' && $shortDescription !== '') {
                $description = $shortDescription;
            }
            if ($description === '') {
                $description = 'Arreglo floral artesanal con flores frescas de temporada.';
            }

            $metaTitle = trim((string) ($data['meta_title'] ?? ''));
            if ($metaTitle === '') {
                $metaTitle = $name . ' | Flores D&D';
            }
            $metaDescription = trim((string) ($data['meta_description'] ?? ''));
            if ($metaDescription === '') {
                $metaDescription = Str::limit($description, 155, '');
            }

            $variantsRaw = trim((string) ($data['variants'] ?? ''));
            $variants = $this->parseVariants($variantsRaw);

            $basePrice = $this->parseMoney($data['base_price_clp'] ?? '');
            $resolvedPrice = null;
            if ($basePrice > 0) {
                $resolvedPrice = $basePrice;
            } elseif (!empty($variants)) {
                $resolvedPrice = collect($variants)->pluck('price_override')->min() ?? 0;
            }

            $product->fill([
                'name' => $name,
                'slug' => $slug,
                'category_id' => $category->id,
                'short_description' => $shortDescription ?: null,
                'description' => $description,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
            ]);

            if ($isNew) {
                $product->price = $resolvedPrice ?? 0;
                $product->is_active = ($resolvedPrice ?? 0) > 0;
                $product->is_new = true;
                $product->has_fresh_guarantee = true;
                $product->has_personalized_card = true;
                $product->track_stock = false;
                $product->stock = 0;
            } elseif ($resolvedPrice !== null) {
                $product->price = $resolvedPrice;
            }

            $seasonTag = trim((string) ($data['season_tag'] ?? ''));
            if ($seasonTag !== '') {
                $badges = collect($product->custom_badges ?? [])->filter()->values()->all();
                if (!in_array($seasonTag, $badges, true)) {
                    $badges[] = $seasonTag;
                }
                $product->custom_badges = $badges;
            }

            if ($importImages) {
                $mainImagePath = $this->resolveMainImagePath($slug, $data, $imagesBase, $dryRun);
                if ($mainImagePath) {
                    $product->main_image = $mainImagePath;
                }
            }

            if ($isNew && (!$product->main_image || $product->main_image === '')) {
                $placeholder = $this->copyPlaceholder('products/' . Str::slug($slug), $dryRun);
                if ($placeholder) {
                    $product->main_image = $placeholder;
                }
            }

            if (!$dryRun) {
                $product->save();
            }

            if (!empty($variantsRaw)) {
                if (!$dryRun) {
                    $product->variants()->delete();
                    foreach ($variants as $index => $variant) {
                        $product->variants()->create([
                            'name' => $variant['name'],
                            'label' => $variant['label'],
                            'type' => 'fixed',
                            'price_modifier' => 0,
                            'price_override' => $variant['price_override'],
                            'is_active' => true,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            if ($importImages) {
                $this->importGalleryImages($product, $data, $imagesBase, $dryRun);
            }

            if ($isNew) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        $this->info("Importación finalizada. Nuevos: {$created}, Actualizados: {$updated}, Omitidos: {$skipped}.");
        if ($dryRun) {
            $this->warn('Modo dry-run: no se guardaron cambios.');
        }

        return 0;
    }

    private function resolvePath(string $path): string
    {
        if (Str::startsWith($path, ['/','\\'])) {
            return $path;
        }
        return base_path($path);
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function mapRow(array $header, array $row): array
    {
        $data = [];
        foreach ($header as $index => $key) {
            $data[$key] = $row[$index] ?? null;
        }
        return $data;
    }

    private function parseMoney(?string $value): int
    {
        if (!$value) {
            return 0;
        }
        $normalized = preg_replace('/[^0-9]/', '', $value);
        return (int) $normalized;
    }

    private function parseVariants(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $parts = array_map('trim', explode('|', $value));
        $variants = [];
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $segments = array_map('trim', explode('=', $part));
            if (count($segments) !== 2) {
                continue;
            }
            [$label, $priceRaw] = $segments;
            $price = $this->parseMoney($priceRaw);
            if ($price <= 0) {
                continue;
            }
            $name = $this->inferVariantName($label);
            $variants[] = [
                'name' => $name,
                'label' => $label,
                'price_override' => $price,
            ];
        }

        return $variants;
    }

    private function inferVariantName(string $label): string
    {
        $clean = trim($label);
        if (preg_match('/^(S|M|L|XL)\b/i', $clean, $matches)) {
            return strtoupper($matches[1]);
        }
        return $clean;
    }

    private function findCategoryByPath(string $path): ?Category
    {
        if ($path === '') {
            return null;
        }

        $parts = array_map('trim', explode('>', $path));
        $parent = null;
        $category = null;

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $query = Category::query()
                ->where(function ($q) use ($part) {
                    $q->where('name', $part)
                        ->orWhere('slug', Str::slug($part));
                });

            if ($parent) {
                $query->where('parent_id', $parent->id);
            }

            $category = $query->first();
            if (!$category) {
                return null;
            }
            $parent = $category;
        }

        return $category;
    }

    private function resolveMainImagePath(string $slug, array $data, string $basePath, bool $dryRun): ?string
    {
        $mainImage = trim((string) ($data['main_image'] ?? ''));
        if ($mainImage === '') {
            return null;
        }

        $source = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $mainImage;
        if (!File::exists($source)) {
            $this->warn("Imagen principal no encontrada para {$slug}: {$source}");
            return null;
        }

        $folder = 'products/' . Str::slug($slug);
        return $this->copyImage($source, $folder, 'main', $dryRun);
    }

    private function importGalleryImages(Product $product, array $data, string $basePath, bool $dryRun): void
    {
        $slug = $product->slug ?: Str::slug($product->name);
        $folder = 'products/' . Str::slug($slug);

        $galleryRaw = trim((string) ($data['gallery_images'] ?? ''));
        if ($galleryRaw === '') {
            return;
        }

        $galleryItems = array_filter(array_map('trim', explode('|', $galleryRaw)));
        $gallery = [];
        foreach ($galleryItems as $index => $image) {
            $source = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $image;
            if (!File::exists($source)) {
                $this->warn("Imagen de galería no encontrada para {$product->name}: {$source}");
                continue;
            }
            $dest = $this->copyImage($source, $folder . '/gallery', 'gallery-' . ($index + 1), $dryRun);
            if ($dest) {
                $gallery[] = $dest;
            }
        }

        if (!$dryRun && !empty($gallery)) {
            $product->gallery_images = $gallery;
            $product->save();
        }
    }

    private function copyImage(string $source, string $destFolder, string $prefix, bool $dryRun): ?string
    {
        if (!File::exists($source)) {
            return null;
        }

        $extension = pathinfo($source, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $prefix . '-' . now()->format('YmdHis') . '.' . $extension;
        $destPath = trim($destFolder, '/') . '/' . $filename;

        if ($dryRun) {
            return $destPath;
        }

        Storage::disk('public')->putFileAs($destFolder, new \Illuminate\Http\File($source), $filename);

        return $destPath;
    }

    private function copyPlaceholder(string $destFolder, bool $dryRun): ?string
    {
        $placeholder = public_path('images/og-default.svg');
        if (!File::exists($placeholder)) {
            return null;
        }

        $destPath = trim($destFolder, '/') . '/main-placeholder.svg';

        if ($dryRun) {
            return $destPath;
        }

        Storage::disk('public')->putFileAs($destFolder, new \Illuminate\Http\File($placeholder), 'main-placeholder.svg');

        return $destPath;
    }
}
