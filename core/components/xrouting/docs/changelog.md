# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.5.0] - 2023-01-24

### Changed

- Code refactoring:
  - Use a service class to collect global settings and methods in one place to avoid duplicated code
  - Separate the plugin events code into event based classes

### Fixed

- Fix some PHP warnings - i.e. PHP warning: Undefined array key "xrouting-debug"

## [1.4.2] - 2022-01-20

### Added

- Set a context based locale
- Switch cultureKey on switchContext

### Fixed

- Fix using the base_url context setting of the main context instead of the system setting
- Fix a PHP warning - max(): Array must contain at least one element

## [1.4.1] - 2015-05-20

### Added

- Add missing lexicons for allow_debug_info setting
- Add german lexicon

## [1.4.0] - 2015-05-20

### Changed

- Replace php constant for MODX_BASE_URL

### Added

- Add optional debug output (switch the allow_debug_info system setting to "Yes" and add &xrouting-debug=1 to your URL)

## [1.3.0] - 2014-09-18

### Added

- Add support for MODX installs in a subfolder

### Fixed

- Fix "Warning: Invalid argument supplied for foreach()"

## [1.2.0] - 2014-03-17

### Fixed

- Fixes some problems with rewriting the request url

## [1.1.0] - 2014-02-24

### Changed

- Quicker host-based matching

### Fixed

- Bugfix for contexts with same host where one base_url is '/'

### Added

- Setting to include www subdomain: xrouting.include_www
- Setting to eigher show an error or a default context when no match has been found: xrouting.show_no_match_error
- Setting for a default context (if no match was found and xrouting.show_no_match_error is false): xrouting.default_context

## [1.0.0] - 2014-02-01

### Added

- first public release
