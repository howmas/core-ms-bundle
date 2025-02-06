Requirements

# Config

Make sure `admin_csp_header` is disable:

```bash
pimcore_admin:
    admin_csp_header:
        enabled: false
```

Install `markdown_to_html` in https://twig.symfony.com/doc/3.x/filters/markdown_to_html.html

```bash
    composer require twig/markdown-extra
    composer require twig/extra-bundle
    composer require league/commonmark
```
