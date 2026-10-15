# GB Date Filters

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/glowingblue/date-filters.svg)](https://packagist.org/packages/glowingblue/date-filters) [![Total Downloads](https://img.shields.io/packagist/dt/glowingblue/date-filters.svg)](https://packagist.org/packages/glowingblue/date-filters)

A [Flarum](https://flarum.org) extension. Allows filtering discussions by date ranges in the frontend.

## Features

- **Date filter dropdown** on the discussion list toolbar with presets: All Time, Today, Yesterday, Last week, Last month, Last year.
- **Custom date range** modal to pick any start and end date.
- Filters discussions by creation date and works together with search.
- Feature visibility controlled by the **View date filter in forum** permission.

<img width="1103" height="461" alt="Date filter dropdown on the discussion list" src="https://github.com/user-attachments/assets/33ac59c4-1ef8-4e35-835a-f85148c1e36d" />

<img width="1118" height="493" alt="Custom date range modal" src="https://github.com/user-attachments/assets/ff49295b-49a3-4b19-8085-0363c8092c4f" />

## Installation

Install with composer:

```sh
composer require glowingblue/date-filters:"*"
```

## Updating

```sh
composer update glowingblue/date-filters:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/glowingblue/date-filters)
- [GitHub](https://github.com/glowingblue/flarum-ext-date-filters)
- [Discuss](https://discuss.flarum.org/d/40083-gb-date-filters)
