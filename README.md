# archivesPlugin
Plugin for CollectiveAccess providence allowing tree view both in ajax &amp; separated screen



## Installing

### Pre-requisites

PDF export uses **wkhtmltopdf** (HTML to PDF) and **pdfunite** (poppler-utils, to prepend
attached PDFs). phantomjs and pdftk are no longer used. Under Debian :

```bash
apt install --no-install-recommends wkhtmltopdf poppler-utils
```

The wkhtmltopdf path is read from providence's `external_applications.conf`
(`wkhtmltopdf_app`), with `/usr/bin/wkhtmltopdf` as fallback.

Web fonts imported from Google Fonts are stripped from the PDF only : wkhtmltopdf draws
them as vector paths, which leaves the PDF without any searchable or copyable text. The PDF
uses the system sans-serif font instead (DejaVu Sans on Debian). The HTML print view is
unchanged.

Remember to allow the apache sys user to be able to write inside archives/temp :

```bash
chown www-data:www-data archives/temp
```

The generated PDF is served directly from `archives/temp/<id>.pdf` : if the web server
blocks direct access to `app/`, allow `app/plugins/archives/temp/[0-9]+.pdf`.
