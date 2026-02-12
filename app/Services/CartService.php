<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    public static function makeItemKey(int $productId, ?int $variantId = null, ?int $customValue = null): string
    {
        $key = 'p' . $productId;
        if ($variantId) {
            $key .= '-v' . $variantId;
        }
        if ($customValue) {
            $key .= '-c' . $customValue;
        }
        return $key;
    }

    public static function addItem(array $cart, Product $product, ?ProductVariant $variant, int $quantity = 1, ?string $cardMessage = null, ?int $customValue = null): array
    {
        $quantity = max(1, $quantity);
        if ($product->track_stock) {
            if ($product->available_stock < 1) {
                return $cart;
            }
            $quantity = min($quantity, $product->available_stock);
        }

        $customValue = self::normalizeCustomValue($variant, $customValue);
        $key = self::makeItemKey($product->id, $variant?->id, $customValue);

        $unitPrice = self::calculateUnitPrice($product, $variant, $customValue);
        $originalPrice = self::calculateComparePrice($product, $variant);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
            if ($product->track_stock) {
                $cart[$key]['quantity'] = min($cart[$key]['quantity'], $product->available_stock);
            }
            return $cart;
        }

        $cart[$key] = [
            'key' => $key,
            'product_id' => $product->id,
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $unitPrice,
            'original_price' => $originalPrice,
            'image' => $product->main_image,
            'quantity' => $quantity,
            'category_id' => $product->category_id,
            'variant_id' => $variant?->id,
            'variant_label' => $variant ? ($variant->label ?: $variant->name) : null,
            'variant_type' => $variant?->type,
            'unit_label' => $variant?->unit_label,
            'custom_value' => $customValue,
            'card_message' => $cardMessage ? mb_substr(trim($cardMessage), 0, 200) : '',
        ];

        return $cart;
    }

    public static function sanitizeCart(array $sessionCart): array
    {
        if (empty($sessionCart)) {
            return [];
        }

        $productIds = collect($sessionCart)
            ->map(fn ($item) => (int) ($item['product_id'] ?? $item['id'] ?? 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($productIds)) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->with(['activeVariants'])
            ->get()
            ->keyBy('id');

        $clean = [];
        foreach ($sessionCart as $item) {
            $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            if (!$productId) {
                continue;
            }

            $product = $products->get($productId);
            if (!$product) {
                continue;
            }

            $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            $variant = null;
            if ($variantId) {
                $variant = $product->activeVariants->firstWhere('id', $variantId);
                if (!$variant) {
                    continue;
                }
            }

            $customValue = isset($item['custom_value']) ? (int) $item['custom_value'] : null;
            $customValue = self::normalizeCustomValue($variant, $customValue);

            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < 1) {
                continue;
            }
            if ($product->track_stock) {
                $quantity = min($quantity, $product->available_stock);
            }
            if ($quantity < 1) {
                continue;
            }

            $key = self::makeItemKey($product->id, $variant?->id, $customValue);
            $unitPrice = self::calculateUnitPrice($product, $variant, $customValue);
            $originalPrice = self::calculateComparePrice($product, $variant);

            $clean[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $unitPrice,
                'original_price' => $originalPrice,
                'image' => $product->main_image,
                'quantity' => $quantity,
                'category_id' => $product->category_id,
                'variant_id' => $variant?->id,
                'variant_label' => $variant ? ($variant->label ?: $variant->name) : null,
                'variant_type' => $variant?->type,
                'unit_label' => $variant?->unit_label,
                'custom_value' => $customValue,
                'card_message' => isset($item['card_message'])
                    ? mb_substr(trim((string) $item['card_message']), 0, 200)
                    : '',
            ];
        }

        return $clean;
    }

    public static function calculateUnitPrice(Product $product, ?ProductVariant $variant, ?int $customValue = null): int
    {
        $base = $product->price;
        if (!$variant) {
            return max(0, (int) $base);
        }

        if ($variant->type === 'range') {
            $customValue = self::normalizeCustomValue($variant, $customValue);
            $min = max(1, (int) ($variant->min_value ?? 1));
            $extraUnits = max(0, (int) ($customValue ?? $min) - $min);
            $pricePerUnit = (int) ($variant->price_per_unit ?? 0);
            $base = $variant->price_override !== null ? (int) $variant->price_override : (int) $base;
            return max(0, $base + ($extraUnits * $pricePerUnit));
        }

        if ($variant->price_override !== null) {
            return max(0, (int) $variant->price_override);
        }

        return max(0, (int) $base + (int) $variant->price_modifier);
    }

    public static function calculateComparePrice(Product $product, ?ProductVariant $variant): int
    {
        $base = $product->compare_price ?: $product->price;
        if (!$variant) {
            return max(0, (int) $base);
        }

        if ($variant->price_override !== null) {
            return max(0, (int) $variant->price_override);
        }

        return max(0, (int) $base + (int) $variant->price_modifier);
    }

    public static function normalizeCustomValue(?ProductVariant $variant, ?int $customValue): ?int
    {
        if (!$variant || $variant->type !== 'range') {
            return null;
        }

        $min = max(1, (int) ($variant->min_value ?? 1));
        $max = (int) ($variant->max_value ?? $min);
        $step = max(1, (int) ($variant->step_value ?? 1));

        $value = $customValue ?? $min;
        $value = max($min, min($value, $max));
        $value = $min + (int) (floor(($value - $min) / $step) * $step);

        return $value;
    }
}
