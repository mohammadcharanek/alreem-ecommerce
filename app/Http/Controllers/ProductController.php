<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminMiddleware;
use App\Imports\ProductsImport;
use App\Imports\ProductsWithImagesImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', AdminMiddleware::class])->only([
            'adminIndex', 'create', 'store', 'edit', 'update',
            'destroy', 'updateStock', 'quickUpdate'
        ]);
    }

    /* ============================================================
     * FRONTEND PRODUCT PAGES
     * ============================================================ */

    public function index(Request $request)
    {
        return $this->renderProductListing($request);
    }

    // Route-model binding by slug: /p/{product:slug}
    public function show(Product $product)
    {
        $product->load([
            'category:id,name,slug',
            'brand:id,name,slug',
            'vendor:id,name,slug',
            'images' => fn ($query) => $query
                ->select('id', 'product_id', 'image', 'alt', 'is_primary', 'sort_order')
                ->ordered(),
        ]);

        return view('products.show', compact('product'));
    }

    public function productsByCategory(Request $request, string $slug)
    {
        $category = Category::query()
            ->select('id', 'name', 'slug', 'parent_id')
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderProductListing($request, categoryContext: $category);
    }

    public function byBrand(Request $request, string $brandSlug)
    {
        $brand = Brand::query()
            ->select('id', 'name', 'slug')
            ->where('slug', $brandSlug)
            ->firstOrFail();

        return $this->renderProductListing($request, brandContext: $brand);
    }

    public function productsByVendor(Request $request, Vendor $vendor)
    {
        return $this->renderProductListing($request, vendorContext: $vendor);
    }

    /**
     * Build the public product listing for the main page and contextual
     * category, brand, and vendor routes.
     */
    private function renderProductListing(
        Request $request,
        ?Category $categoryContext = null,
        ?Brand $brandContext = null,
        ?Vendor $vendorContext = null,
    ) {
        $allCategories = Category::query()
            ->select('id', 'name', 'slug', 'parent_id')
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        $vendors = Vendor::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        $q = trim((string) $request->query('q', ''));
        $q = mb_substr($q, 0, 100);

        $allowedSorts = [
            'recommended',
            'new',
            'latest',
            'price_asc',
            'price_desc',
            'name_asc',
            'name_desc',
            'rating_desc',
        ];

        $sort = (string) $request->query('sort', 'recommended');
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'recommended';
        $sort = $sort === 'new' ? 'latest' : $sort;

        $stockFilter = (string) $request->query('stock', '');
        $stockFilter = in_array($stockFilter, ['in_stock', 'out_of_stock'], true)
            ? $stockFilter
            : '';

        $selectedCategoryIds = $this->normalizeIdList(
            $request->query('category', []),
            $allCategories->pluck('id')
        );

        $selectedBrandIds = $this->normalizeIdList(
            $request->query('brand', []),
            $brands->pluck('id')
        );

        $selectedVendorIds = $this->normalizeIdList(
            $request->query('vendor', []),
            $vendors->pluck('id')
        );

        $minPrice = $this->normalizeMoney($request->query('min_price'));
        $maxPrice = $this->normalizeMoney($request->query('max_price'));

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $forcedCategoryIds = $categoryContext
            ? $this->categoryAndDescendantIds($allCategories, $categoryContext->id)
            : [];

        $categoryIdsForQuery = $this->combineSelectedAndForcedIds(
            $selectedCategoryIds,
            $forcedCategoryIds
        );

        $brandIdsForQuery = $this->combineSelectedAndForcedIds(
            $selectedBrandIds,
            $brandContext ? [(int) $brandContext->id] : []
        );

        $vendorIdsForQuery = $this->combineSelectedAndForcedIds(
            $selectedVendorIds,
            $vendorContext ? [(int) $vendorContext->id] : []
        );

        $effectivePriceSql = 'CASE WHEN discount_price IS NOT NULL AND discount_price > 0 AND discount_price < price THEN discount_price ELSE price END';

        $query = Product::query()
            ->active()
            ->search($q ?: null)
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'vendor:id,name,slug',
                'images' => fn ($imageQuery) => $imageQuery
                    ->select('id', 'product_id', 'image', 'alt', 'is_primary', 'sort_order'),
            ]);

        if ($categoryIdsForQuery !== null) {
            $categoryIdsForQuery
                ? $query->whereIn('category_id', $categoryIdsForQuery)
                : $query->whereRaw('1 = 0');
        }

        if ($brandIdsForQuery !== null) {
            $brandIdsForQuery
                ? $query->whereIn('brand_id', $brandIdsForQuery)
                : $query->whereRaw('1 = 0');
        }

        if ($vendorIdsForQuery !== null) {
            $vendorIdsForQuery
                ? $query->whereIn('vendor_id', $vendorIdsForQuery)
                : $query->whereRaw('1 = 0');
        }

        if ($stockFilter === 'in_stock') {
            $query->where('stock', '>', 0);
        } elseif ($stockFilter === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        if ($minPrice !== null) {
            $query->whereRaw("{$effectivePriceSql} >= ?", [$minPrice]);
        }

        if ($maxPrice !== null) {
            $query->whereRaw("{$effectivePriceSql} <= ?", [$maxPrice]);
        }

        match ($sort) {
            'latest' => $query
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
            'price_asc' => $query
                ->orderByRaw("{$effectivePriceSql} ASC")
                ->orderBy('name'),
            'price_desc' => $query
                ->orderByRaw("{$effectivePriceSql} DESC")
                ->orderBy('name'),
            'name_asc' => $query
                ->orderBy('name')
                ->orderBy('id'),
            'name_desc' => $query
                ->orderByDesc('name')
                ->orderBy('id'),
            'rating_desc' => $query
                ->orderByRaw('COALESCE(rating, 0) DESC')
                ->orderBy('name'),
            default => $query
                ->orderByDesc('featured')
                ->orderByDesc('is_new')
                ->orderByRaw('COALESCE(rating, 0) DESC')
                ->orderBy('name'),
        };

        $products = $query
            ->paginate(12)
            ->withQueryString();

        $wishlistProducts = [];

        if (auth()->check() && $products->isNotEmpty()) {
            $wishlistProducts = DB::table('wishlists')
                ->where('user_id', auth()->id())
                ->whereIn('product_id', $products->getCollection()->pluck('id'))
                ->pluck('product_id')
                ->map(static fn ($id) => (int) $id)
                ->all();
        }

        $categoryOptions = $this->buildCategoryOptions($allCategories);

        $filterLabels = [
            'categories' => $allCategories
                ->whereIn('id', $selectedCategoryIds)
                ->pluck('name', 'id')
                ->all(),
            'brands' => $brands
                ->whereIn('id', $selectedBrandIds)
                ->pluck('name', 'id')
                ->all(),
            'vendors' => $vendors
                ->whereIn('id', $selectedVendorIds)
                ->pluck('name', 'id')
                ->all(),
        ];

        $activeFilterCount = ($q !== '' ? 1 : 0)
            + count($selectedCategoryIds)
            + count($selectedBrandIds)
            + count($selectedVendorIds)
            + ($stockFilter !== '' ? 1 : 0)
            + (($minPrice !== null || $maxPrice !== null) ? 1 : 0);

        return view('products.index', [
            'products' => $products,
            'categoryOptions' => $categoryOptions,
            'brands' => $brands,
            'vendors' => $vendors,
            'q' => $q,
            'sort' => $sort,
            'stockFilter' => $stockFilter,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'selectedCategoryIds' => $selectedCategoryIds,
            'selectedBrandIds' => $selectedBrandIds,
            'selectedVendorIds' => $selectedVendorIds,
            'filterLabels' => $filterLabels,
            'activeFilterCount' => $activeFilterCount,
            'wishlistProducts' => $wishlistProducts,
            'clearFiltersUrl' => url()->current(),
            'category' => $categoryContext,
            'brand' => $brandContext,
            'vendor' => $vendorContext,
        ]);
    }

    private function normalizeIdList(mixed $value, \Illuminate\Support\Collection $validIds): array
    {
        $values = is_array($value)
            ? $value
            : (($value === null || $value === '') ? [] : [$value]);

        $validLookup = $validIds
            ->map(static fn ($id) => (int) $id)
            ->flip();

        return collect($values)
            ->filter(static fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false)
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $validLookup->has($id))
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeMoney(mixed $value): ?float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        return round(max(0, (float) $value), 2);
    }

    private function combineSelectedAndForcedIds(array $selectedIds, array $forcedIds): ?array
    {
        if (!$selectedIds && !$forcedIds) {
            return null;
        }

        if (!$forcedIds) {
            return $selectedIds;
        }

        if (!$selectedIds) {
            return $forcedIds;
        }

        return array_values(array_intersect($selectedIds, $forcedIds));
    }

    private function categoryAndDescendantIds(
        \Illuminate\Support\Collection $categories,
        int $categoryId,
    ): array {
        $childrenByParent = $categories->groupBy(
            static fn (Category $category) => (int) ($category->parent_id ?? 0)
        );

        $ids = [];
        $stack = [$categoryId];

        while ($stack) {
            $currentId = (int) array_pop($stack);

            if (in_array($currentId, $ids, true)) {
                continue;
            }

            $ids[] = $currentId;

            foreach ($childrenByParent->get($currentId, collect()) as $child) {
                $stack[] = (int) $child->id;
            }
        }

        return $ids;
    }

    private function buildCategoryOptions(
        \Illuminate\Support\Collection $categories,
    ): \Illuminate\Support\Collection {
        $childrenByParent = $categories->groupBy(
            static fn (Category $category) => (int) ($category->parent_id ?? 0)
        );

        $options = collect();
        $visited = [];

        $appendChildren = function (int $parentId, int $depth = 0) use (
            &$appendChildren,
            &$visited,
            $childrenByParent,
            $options,
        ): void {
            foreach ($childrenByParent->get($parentId, collect()) as $category) {
                $categoryId = (int) $category->id;

                if (isset($visited[$categoryId])) {
                    continue;
                }

                $visited[$categoryId] = true;
                $options->push([
                    'id' => $categoryId,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'depth' => $depth,
                ]);

                $appendChildren($categoryId, $depth + 1);
            }
        };

        $appendChildren(0);

        // Keep orphaned categories visible instead of silently dropping them.
        foreach ($categories as $category) {
            if (!isset($visited[(int) $category->id])) {
                $options->push([
                    'id' => (int) $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'depth' => 0,
                ]);
            }
        }

        return $options;
    }

    /* ============================================================
     * ADMIN PRODUCT MANAGEMENT
     * ============================================================ */

    public function adminIndex()
    {
        $q          = request('q');
        $brandId    = request('brand_id');
        $categoryId = request('category_id');
        $active     = request('active'); // '1' | '0' | null

        $query = Product::with(['category', 'brand', 'images']);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($active !== null && $active !== '') {
            $query->where('is_active', (bool)((int)$active));
        }

        $products   = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::with('children.children')->whereNull('parent_id')->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::with(['children.children'])->whereNull('parent_id')->orderBy('name')->get();
        $vendors    = Vendor::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'vendors', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:products,slug',
            'sku'              => 'nullable|string|max:255|unique:products,sku',
            'barcode'          => 'nullable|string|max:255',
            'rating'           => 'nullable|numeric|min:0|max:5',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0.01',
            'discount_price'   => 'nullable|numeric|min:0|lt:price',
            'stock'            => 'nullable|integer|min:0',
            'brand_id'         => 'nullable|exists:brands,id',
            'category_id'      => 'nullable|exists:categories,id',
            'vendor_id'        => 'nullable|exists:vendors,id',
            'is_active'        => 'sometimes|boolean',
            'featured'         => 'sometimes|boolean',
            'is_new'           => 'sometimes|boolean',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:500',

            // Images arrays
            'images'        => 'nullable|array',
            'images.*'      => 'file|image|mimes:jpeg,jpg,png,webp|max:4096',
            'images_alt'    => 'nullable|array',
            'images_alt.*'  => 'nullable|string|max:255',
            'images_sort'   => 'nullable|array',
            'images_sort.*' => 'nullable|integer|min:0',
            'primary_index' => 'nullable|integer|min:0',
        ]);

        // Flags & defaults (use boolean() so hidden 0 + checked 1 works correctly)
        $validated['is_active'] = $request->boolean('is_active');
        $validated['featured']  = $request->boolean('featured');
        $validated['is_new']    = $request->boolean('is_new');
        $validated['stock']     = $validated['stock'] ?? 0;

        // If a slug is provided, sanitize it; otherwise the Model will generate on creating()
        if (!blank($validated['slug'] ?? null)) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // Optional SKU fallback
        $validated['sku'] = $validated['sku'] ?? ('SKU-' . strtoupper(Str::random(6)));

        // SEO fallbacks
        if (blank($validated['meta_title'] ?? null)) {
            $validated['meta_title'] = method_exists(Product::class, 'makeMetaTitle')
                ? Product::makeMetaTitle($validated['name'])
                : $validated['name'];
        }
        if (blank($validated['meta_description'] ?? null)) {
            $src = $validated['description'] ?? $validated['name'];
            $validated['meta_description'] = method_exists(Product::class, 'makeMetaDescription')
                ? Product::makeMetaDescription($src)
                : mb_strimwidth(strip_tags($src), 0, 155, '…');
        }

        DB::transaction(function () use ($request, $validated) {
            /** @var Product $product */
            $product = Product::create($validated);

            $files   = $request->file('images', []);
            $alts    = $request->input('images_alt', []);
            $orders  = $request->input('images_sort', []);
            $primary = (int) $request->input('primary_index', 0);

            if (!empty($files)) {
                foreach ($files as $i => $file) {
                    $path = $file->store("products/{$product->id}", 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'alt'        => $alts[$i] ?? $product->name,
                        'is_primary' => $i === $primary,
                        'sort_order' => isset($orders[$i]) ? (int)$orders[$i] : $i,
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::with(['children.children'])->whereNull('parent_id')->orderBy('name')->get();
        $vendors    = Vendor::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'vendors', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'sku'              => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'barcode'          => 'nullable|string|max:255',
            'rating'           => 'nullable|numeric|min:0|max:5',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0.01',
            'discount_price'   => 'nullable|numeric|min:0|lt:price',
            'stock'            => 'nullable|integer|min:0',
            'brand_id'         => 'nullable|exists:brands,id',
            'category_id'      => 'nullable|exists:categories,id',
            'vendor_id'        => 'nullable|exists:vendors,id',
            'is_active'        => 'sometimes|boolean',
            'featured'         => 'sometimes|boolean',
            'is_new'           => 'sometimes|boolean',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:500',

            // New images to append
            'images'        => 'nullable|array',
            'images.*'      => 'file|image|mimes:jpeg,jpg,png,webp|max:4096',
            'images_alt'    => 'nullable|array',
            'images_alt.*'  => 'nullable|string|max:255',
            'images_sort'   => 'nullable|array',
            'images_sort.*' => 'nullable|integer|min:0',
            'primary_index' => 'nullable|integer|min:0',

            // Existing images editable fields
            'existing_images'              => 'nullable|array',
            'existing_images.*.alt'        => 'nullable|string|max:255',
            'existing_images.*.sort_order' => 'nullable|integer|min:0',
            'primary_image_id'             => 'nullable|integer|min:1',
        ]);

        // Flags (use boolean() so hidden 0 + checked 1 works correctly)
        $validated['is_active'] = $request->boolean('is_active');
        $validated['featured']  = $request->boolean('featured');
        $validated['is_new']    = $request->boolean('is_new');
        $validated['stock']     = $validated['stock'] ?? $product->stock ?? 0;

        // If slug provided, sanitize; uniqueness is enforced by validation + model on updating()
        if (!blank($validated['slug'] ?? null)) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        // SEO fallbacks
        if (blank($validated['meta_title'] ?? null)) {
            $validated['meta_title'] = method_exists(Product::class, 'makeMetaTitle')
                ? Product::makeMetaTitle($validated['name'])
                : $validated['name'];
        }
        if (blank($validated['meta_description'] ?? null)) {
            $src = $validated['description'] ?? $validated['name'];
            $validated['meta_description'] = method_exists(Product::class, 'makeMetaDescription')
                ? Product::makeMetaDescription($src)
                : mb_strimwidth(strip_tags($src), 0, 155, '…');
        }

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update($validated);

            // 1) Update existing images (alt/sort_order)
            $existing = $request->input('existing_images', []); // [id => ['alt'=>..., 'sort_order'=>...]]
            if (!empty($existing)) {
                foreach ($existing as $imgId => $vals) {
                    ProductImage::where('product_id', $product->id)
                        ->where('id', $imgId)
                        ->update([
                            'alt'        => $vals['alt'] ?? null,
                            'sort_order' => isset($vals['sort_order']) ? (int)$vals['sort_order'] : 0,
                        ]);
                }
            }

            // 2) Set primary among existing (only if no new primary_index will be used)
            if (!$request->filled('primary_index') && $request->filled('primary_image_id')) {
                $pid = (int) $request->input('primary_image_id');
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductImage::where('product_id', $product->id)->where('id', $pid)->update(['is_primary' => true]);
            }

            // 3) Append newly uploaded images (+ optional new primary_index)
            $files   = $request->file('images', []);
            $alts    = $request->input('images_alt', []);
            $orders  = $request->input('images_sort', []);
            $primary = $request->filled('primary_index') ? (int) $request->input('primary_index') : null;

            if (!empty($files)) {
                if (!is_null($primary)) {
                    // If new primary is chosen, clear previous primaries
                    ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                }

                $startOrder = (int) ($product->images()->max('sort_order') ?? -1) + 1;
                foreach ($files as $i => $file) {
                    $path = $file->store("products/{$product->id}", 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'alt'        => $alts[$i] ?? $product->name,
                        'is_primary' => (!is_null($primary) && $i === $primary),
                        'sort_order' => isset($orders[$i]) ? (int)$orders[$i] : ($startOrder + $i),
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated successfully.');
    }

    // Quick inline update (category, brand, price, discount_price, stock, is_active)
    public function quickUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'    => 'nullable|exists:categories,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'is_active'      => 'required|boolean',
        ]);

        try {
            // Normalize: empty discount => null
            if ($request->filled('discount_price') === false) {
                $validated['discount_price'] = null;
            }

            $product->update([
                'category_id'    => $validated['category_id'] ?? null,
                'brand_id'       => $validated['brand_id'] ?? null,
                'price'          => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'stock'          => $validated['stock'],
                'is_active'      => (bool) $validated['is_active'],
            ]);

            return back()->with('success', 'Product updated successfully!');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Failed to update product: '.$e->getMessage()]);
        }
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $product->stock = $request->stock;
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Stock updated successfully!');
    }

    public function destroy(Product $product)
    {
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return redirect()->route('admin.products.index')
                ->withErrors(['error' => 'This product cannot be deleted because it is linked to existing orders.']);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    /* ============================================================
     * BULK IMPORTS
     * ============================================================ */

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ProductsImport, $request->file('file'));
        return redirect()->back()->with('success', 'Products imported successfully!');
    }

    public function importProductsWithImages(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductsWithImagesImport('products_import_images'), $request->file('excel_file'));
            return redirect()->back()->with('success', 'Products with images imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }
}
