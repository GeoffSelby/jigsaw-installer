# Jigsaw Installer

CLI to easily install and initialize [Jigsaw](https://jigsaw.tighten.co) sites with a single command

<a href="https://github.com/GeoffSelby/jigsaw-installer/actions"><img src="https://github.com/GeoffSelby/jigsaw-installer/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/geoffselby/jigsaw-installer"><img src="https://img.shields.io/packagist/dt/geoffselby/jigsaw-installer" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/geoffselby/jigsaw-installer"><img src="https://img.shields.io/packagist/v/geoffselby/jigsaw-installer" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/geoffselby/jigsaw-installer"><img src="https://img.shields.io/packagist/l/geoffselby/jigsaw-installer" alt="License"></a>

## Installation

Install the CLI with [Composer](https://getcomposer.org):

```bash
composer global require geoffselby/jigsaw-installer
```

## Usage

To install and initialize a new site with [Jigsaw](https://jigsaw.tighten.co):

```bash
jigsaw new my-site
```

> This will install Jigsaw in a directory named `my-site` and initialize your Jigsaw site. It also initializes Git for you and creates an initial commit automatically.

### Using starter templates

Jigsaw allows you to use a starter template when initializing a new site. You can choose between the two official starters ([blog](https://github.com/tighten/jigsaw-blog-template) and [docs](https://github.com/tighten/jigsaw-docs-template)) or any third-party starter on [Packagist](https://packagist.org/packages/tightenco/jigsaw?query=tighten%20jigsaw%20starter).

To use an official starter with this installer, use the `--starter` option:

```bash
jigsaw new my-site --starter blog
```

Or, to use a third-party starter, use the `vendor/package` syntax:

```bash
jigsaw new my-site --starter rickwest/jigsaw-clean-blog
```

### Installing the development version

If you would like to install the development version of Jigsaw to test out new features before a stable release, use the `--dev` option:

```bash
jigsaw new my-site --dev
```

### Don't initialize Git

If you don't want to initialize Git automatically, use the `--no-git` option:

```bash
jigsaw new my-site --no-git
```

## License

Jigsaw Installer is open-sourced software licensed under the [MIT license](LICENSE.md).
