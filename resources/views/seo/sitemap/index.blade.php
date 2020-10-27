@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ url('sitemap/listings') }}</loc>
        <lastmod>{{ $listing->updated_at->tz('UTC')->toAtomString() }}</lastmod>
    </sitemap>
    <sitemap>
      <loc>{{ url('sitemap/games') }}</loc>
        <lastmod>{{ $listing->updated_at->tz('UTC')->toAtomString() }}</lastmod>
    </sitemap>
</sitemapindex>
