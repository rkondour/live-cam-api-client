# ModjoCams Live Stream JSON Cache & SEO Filter (PHP 8.4)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2%20%7C%208.4-777bb4.svg)](https://www.php.net/)
[![Live Demo](https://img.shields.io/badge/Live%20Demo-ModjoCams.com-0C6A93.svg)](https://modjocams.com/)

A lightweight, zero-dependency **PHP 8.4 library** designed to fetch, normalize, and cache live streaming JSON feeds into ultra-fast flat-file indexes with built-in **SEO Soft-404 protection** (tag popularity threshold filtering).

---

## 🚀 Live Production Showcase

This library powers the real-time Server-Side Rendered (SSR) architecture of **[ModjoCams.com](https://modjocams.com/)**, achieving sub-15ms TTFB using PHP 8.4 OPcache JIT and Nginx FastCGI RAM caching.

* **Live Production Application:** [https://modjocams.com/](https://modjocams.com/)
* **Dynamic XML Sitemap Output:** [https://modjocams.com/sitemap.xml](https://modjocams.com/sitemap.xml)
* **SSR Category Routing Examples:**
  * [ModjoCams `#latina` Stream Directory](https://modjocams.com/tag/latina)
  * [ModjoCams `#asian` Stream Directory](https://modjocams.com/tag/asian)
  * [ModjoCams `#milf` Stream Directory](https://modjocams.com/tag/milf)
  * [ModjoCams `#ebony` Stream Directory](https://modjocams.com/tag/ebony)
  * [ModjoCams `#blonde` Stream Directory](https://modjocams.com/tag/blonde)
  * [ModjoCams `#brunette` Stream Directory](https://modjocams.com/tag/brunette)

---

## ✨ Key Features

* **Zero Database Overhead:** Designed for `tmpfs` / RAM-disk flat-file JSON caching (`models-cache.json` & `tags-cache.json`), eliminating MySQL/Redis bottlenecks under high concurrent traffic.
* **SEO Soft-404 Prevention:** Automatically filters out low-density tags (`< 15` live streams online) so search engine crawlers only index rich category pages.
* **Strict Tag Sanitization:** Normalizes raw hashtags into clean, canonical lowercase URL slugs.
* **PHP 8.4 Ready:** Strict typing (`declare(strict_types=1)`), compatible with OPcache `tracing` JIT compilation.

---

## 📦 Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require modjocams/live-cam-api-client
```

---

## 🛠️ Quick Start

```php
<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ModjoCams\LiveCamClient\FeedClient;

// Initialize client with a minimum threshold of 15 active streams per tag
$client = new FeedClient(
    apiUrl: 'https://modjocams.com/api/models?limit=120',
    minTagPopularity: 15
);

$data = $client->fetchAndNormalize();

printf("Total active streams: %d\n", $data['total_models']);
printf("Indexable SEO tags (>= 15 streams): %d\n", count($data['popular_tags']));
```

---

## 📄 License

Released under the [MIT License](LICENSE). Maintained by the engineering team at **[ModjoCams.com](https://modjocams.com/)**.
