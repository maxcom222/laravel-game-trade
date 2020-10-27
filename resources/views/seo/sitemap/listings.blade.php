@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="' .  url('xslt/xml-sitemap.xsl') . '"?>'; @endphp

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($listings as $listing)
        <url>
            <loc>{{ $listing->url_slug }}</loc>
            <lastmod>{{ $listing->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
