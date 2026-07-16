<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the public XML sitemap.
     */
    public function sitemap(): Response
    {
        /*
         * Public static pages.
         *
         * Only include pages that should appear in Google search.
         */
        $staticPages = [
            [
                'url' => route('home'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'url' => route('products.index'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'url' => route('categories.index'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'url' => route('about'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'url' => route('contact.show'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'url' => route('faq'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'url' => route('shipping'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'url' => route('returns'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'url' => route('support'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];

        /*
         * Only active products should be indexed.
         */
        $products = Product::query()
            ->active()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        /*
         * Include only categories that contain active products.
         */
        $categoryIds = Product::query()
            ->active()
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $categories = Category::query()
            ->whereIn('id', $categoryIds)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        /*
         * Include only brands used by active products.
         */
        $brandIds = Product::query()
            ->active()
            ->whereNotNull('brand_id')
            ->distinct()
            ->pluck('brand_id');

        $brands = Brand::query()
            ->whereIn('id', $brandIds)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        /*
         * Include only vendors used by active products.
         *
         * Your vendor public route currently uses the vendor route
         * parameter, which defaults to the vendor ID.
         */
        $vendorIds = Product::query()
            ->active()
            ->whereNotNull('vendor_id')
            ->distinct()
            ->pluck('vendor_id');

        $vendors = Vendor::query()
            ->whereIn('id', $vendorIds)
            ->select(['id', 'slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        return response()
            ->view('sitemap', compact(
                'staticPages',
                'products',
                'categories',
                'brands',
                'vendors'
            ))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}