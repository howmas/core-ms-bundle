# Installation

1. On your Pimcore 11 root project:
```bash
composer require howmas/core-ms-bundle
```

2. Update `config/bundles.php` file:
```bash
return [
    ....
    HowMAS\CoreMSBundle\HowMASCoreMSBundle::class => ['all' => true],
];
```

# Requirements
[Follow requirements and ready for bundle](docs/REQUIREMENTS.md)

# Documents
[docs](docs)
