<?xml version="1.0" encoding="UTF-8"?> 
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  {{BEGIN url}} 
  <url>
    <loc>http://{{host}}{{loc}}</loc>
    {{IF lastmod}} 
    <lastmod>{{lastmod}} </lastmod>
    {{END IF}}{{IF changefreq}} 
    <changefreq>{{changefreq}}</changefreq>
    {{END IF}}{{IF priority}} 
    <priority>{{priority}}</priority>
    {{END IF}} 
  </url>
  {{END url}} 
</urlset>
