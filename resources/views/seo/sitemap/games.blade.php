@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="' .  url('xslt/xml-sitemap.xsl') . '"?>'; @endphp

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($games as $game)
        <url>
            <loc>{{ $game->url_slug }}</loc>
            <lastmod>{{ $game->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>
