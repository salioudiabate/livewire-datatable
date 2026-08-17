# Security Policy

## Supported versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

This package follows [semantic versioning](https://semver.org/). Security fixes are released against the latest `1.x` minor/patch release; there is no long-term support branch at this time.

## Reporting a vulnerability

Please **do not** open a public GitHub issue for security vulnerabilities.

Instead, report it privately using one of these channels:

- [GitHub's private vulnerability reporting](https://github.com/salioudiabate/livewire-datatable/security/advisories/new) for this repository, or
- Email **saliou.diiabate@gmail.com** with a description of the issue, steps to reproduce, and its potential impact.

This is a solo-maintained open-source project. There's no formal SLA, but I aim to acknowledge reports within a few days and to keep you updated as I investigate and prepare a fix. Please allow time for a patch to be released before any public disclosure.

## Scope

In scope: vulnerabilities in this package's own code — the `DataSource` adapters, query building, sorting/search/filter handling, bulk delete, export, and the shipped Blade views (e.g. anything that could lead to SQL injection, XSS, or an authorization bypass caused by a bug in the package itself).

Out of scope: how a consuming application wires up its own authorization. `BulkAction::permission()` and `deletePermission()` call into the host application's own [Laravel Gates](https://laravel.com/docs/authorization#gates) — misconfiguring those, or a vulnerability in the host app's own gate definitions, is not an issue in this package. See the README's callout on the two independent bulk-delete permission checks if you're setting those up.

## Supported dependencies

This package targets actively supported PHP, Laravel, and Livewire versions as declared in `composer.json`. Running on an unsupported/end-of-life PHP or Laravel version is outside this policy's scope — please upgrade first.
