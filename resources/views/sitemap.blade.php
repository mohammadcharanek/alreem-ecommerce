{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Static public pages --}}
    @foreach($staticPages as $page)
        <url>
            <loc>{{ $page['url'] }}</loc>
            <changefreq>{{ $page['changefreq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach

    {{-- Active products --}}
    @foreach($products as $product)
        <url>
            <loc>{{ route('products.show', $product) }}</loc>
            @if($product->updated_at)
                <lastmod>{{ $product->updated_at->utc()->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    {{-- Categories containing active products --}}
    @foreach($categories as $category)
        <url>
            <loc>{{ route('products.byCategory', ['slug' => $category->slug]) }}</loc>
            @if($category->updated_at)
                <lastmod>{{ $category->updated_at->utc()->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    {{-- Brands containing active products --}}
    @foreach($brands as $brand)
        <url>
            <loc>{{ route('products.byBrand', ['brandSlug' => $brand->slug]) }}</loc>
            @if($brand->updated_at)
                <lastmod>{{ $brand->updated_at->utc()->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach

    {{-- Vendors containing active products --}}
    @foreach($vendors as $vendor)
        <url>
            <loc>{{ route('products.byVendor', ['vendor' => $vendor->id]) }}</loc>
            @if($vendor->updated_at)
                <lastmod>{{ $vendor->updated_at->utc()->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach

</urlset>