<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    /**
     * Attributes allowed for mass assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'discount_price',
        'stock',
        'brand_id',
        'category_id',
        'vendor_id',
        'meta_title',
        'meta_description',
        'is_active',
        'featured',
        'is_new',
        'rating',
        'barcode',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'discount_price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'is_new' => 'boolean',
        'rating' => 'float',
    ];

    /**
     * Accessors included when converting the model to an array or JSON.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'display_price',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'wishlists'
        )->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Return the effective selling price.
     */
    public function getDisplayPriceAttribute(): float
    {
        return $this->has_discount
            ? (float) $this->discount_price
            : (float) $this->price;
    }

    /**
     * Determine whether the product has a valid discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price !== null
            && (float) $this->discount_price > 0
            && (float) $this->discount_price < (float) $this->price;
    }

    /**
     * Return the discount percentage.
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (
            ! $this->has_discount
            || (float) $this->price <= 0
        ) {
            return 0;
        }

        return (int) round(
            (
                1
                - (
                    (float) $this->discount_price
                    / (float) $this->price
                )
            ) * 100
        );
    }

    /**
     * Determine whether the product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return (int) $this->stock > 0;
    }

    /**
     * Return stock without allowing negative values.
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, (int) $this->stock);
    }

    /**
     * Return the first product image URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $image = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->first();

        return $image?->url;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Include only active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'is_active',
            '=',
            true,
            'and'
        );
    }

    /**
     * Search products by name, SKU, or description.
     */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(
            function (Builder $subQuery) use ($search): void {
                $subQuery
                    ->where(
                        'name',
                        'like',
                        '%' . $search . '%',
                        'and'
                    )
                    ->orWhere(
                        'sku',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $search . '%'
                    );
            },
            null,
            null,
            'and'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(
            function (Product $product): void {
                if (blank($product->slug)) {
                    $product->slug = static::makeUniqueSlug(
                        (string) $product->name
                    );
                } else {
                    $product->slug = static::ensureUniqueSanitizedSlug(
                        $product->slug,
                        null,
                        (string) $product->name
                    );
                }

                if (blank($product->barcode)) {
                    $product->barcode = static::generateBarcode();
                }

                static::ensureMeta($product);
            }
        );

        static::updating(
            function (Product $product): void {
                if ($product->isDirty('slug')) {
                    $product->slug = static::ensureUniqueSanitizedSlug(
                        $product->slug,
                        (int) $product->id,
                        (string) $product->name
                    );
                } elseif (blank($product->slug)) {
                    $product->slug = static::makeUniqueSlug(
                        (string) $product->name,
                        (int) $product->id
                    );
                }

                static::ensureMeta($product);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Generate a unique 13-digit product barcode.
     */
    public static function generateBarcode(): string
    {
        do {
            $barcode = str_pad(
                (string) random_int(0, 9_999_999_999_999),
                13,
                '0',
                STR_PAD_LEFT
            );

            $exists = static::query()
                ->where(
                    'barcode',
                    '=',
                    $barcode,
                    'and'
                )
                ->exists();
        } while ($exists);

        return $barcode;
    }

    /**
     * Fill SEO metadata when it is empty.
     */
    protected static function ensureMeta(Product $product): void
    {
        if (blank($product->meta_title)) {
            $product->meta_title = static::makeMetaTitle(
                (string) $product->name
            );
        }

        if (blank($product->meta_description)) {
            $source = filled($product->description)
                ? (string) $product->description
                : (string) $product->name;

            $product->meta_description =
                static::makeMetaDescription($source);
        }
    }

    /**
     * Create an SEO title capped at 60 characters.
     */
    public static function makeMetaTitle(string $name): string
    {
        return Str::limit(
            trim($name),
            60,
            ''
        );
    }

    /**
     * Create an SEO description capped at 160 characters.
     */
    public static function makeMetaDescription(
        ?string $text
    ): string {
        $plainText = strip_tags((string) $text);

        $plainText = preg_replace(
            '/\s+/',
            ' ',
            $plainText
        ) ?? '';

        return Str::limit(
            trim($plainText),
            160,
            ''
        );
    }

    /**
     * Create a unique slug from a product name.
     */
    public static function makeUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'product';
        }

        $slug = $base;
        $suffix = 1;

        while (static::slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Sanitize a supplied slug and ensure it is unique.
     */
    protected static function ensureUniqueSanitizedSlug(
        ?string $slug,
        ?int $ignoreId = null,
        ?string $fallbackName = null
    ): string {
        $slug = Str::slug((string) $slug);

        if ($slug === '') {
            return static::makeUniqueSlug(
                (string) $fallbackName,
                $ignoreId
            );
        }

        if (! static::slugExists($slug, $ignoreId)) {
            return $slug;
        }

        $base = $slug;
        $suffix = 1;

        do {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        } while (
            static::slugExists(
                $candidate,
                $ignoreId
            )
        );

        return $candidate;
    }

    /**
     * Determine whether a slug is already used.
     */
    protected static function slugExists(
        string $slug,
        ?int $ignoreId = null
    ): bool {
        $query = static::query()->where(
            'slug',
            '=',
            $slug,
            'and'
        );

        if ($ignoreId !== null) {
            $query->where(
                'id',
                '!=',
                $ignoreId,
                'and'
            );
        }

        return $query->exists();
    }
}