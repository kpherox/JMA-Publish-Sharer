# Changelog
Changelog.

## [Unreleased]


## [Version 0.5 (Pre-Release)][0.5] - 2018-08-15
### Added
- Added notification to social account.
- Supported LINE Login.
- Added MariaDB driver for json column.

### Changed
- Modifiable database prefix.
- Supported multiline headline.

### Fixed
- Fixed full text of headline can't be saved.
- Fixed don't save feed_uuid when new feed recoed.


## [Version 0.4.4 (Pre-Release, hotfix)][0.4.4] - 2018-07-08
### Fixed
- Fixed incorrect variable name.


## [Version 0.4.3 (Pre-Release)][0.4.3] - 2018-07-08
### Added
- Added `entry.ippanho` template.

### Changed
- Feed primary key `id` -> `uuid`.
- Deleted `feeds.id` column.

### Fixed
- Fixed entry `parsed_headline`.
- Fixed breaking design for observatory page.


## [Version 0.4.2 (Pre-Release)][0.4.2] - 2018-07-02
### Added
- Supported gzip for entry xml file.
- Added entry directory to git repository.

### Changed
- Replaced blade foreach to vue list of type/kind/observatory list.
- Added `shallow = true` to submodules.
- Replaced `\Storage::get('entry/$uuid')` and `\Storage::put('entry/$uuid', $xmlDoc)` to mutator.
- Changed logout button to form submit button from JavaScript's click event.

### Fixed
- Fixed kind name of config/jmaxml.kinds `指定河川洪水予報`.


## [Version 0.4.1 (Pre-Release, hotfix)][0.4.1] - 2018-06-23
### Fixed
- Fixed undefined variable when running migrate.


## [Version 0.4 (Pre-Release)][0.4] - 2018-06-23
### Added
- Entries list to index page.
- Entry page & kinds template.
- Entries filter.
- EntryDetails table. [#1]
- Google Maps JavaScript API key.

### Changed
- XML Document database to file. [#1]
- Optimized entries table. [#1]


## [Version 0.3 (Pre-Release)][0.3] - 2018-06-03
### Added
- Supported Social login.
- Added feature test.
- Added sample data to test directory.

### Changed
- Laravel version upgrade 5.5 -> 5.6.
- Bootstrap version upgrade 3.x -> 4.x.
- Renamed PubSubHubbub to WebSub.
- Renamed model namespace App\Model to App\Eloquents\Model.
- Save entry url's xml.
- Added relation for Entry and Feed.

### Removed
- Rate limit for websub endpoint.


## [Version 0.2.2 (Pre-Release, hotfix)][0.2.2] - 2017-10-23
### Fixed
- Changed `$entries->feed_uuid = $uuid` to `$entries->feed_uuid = $uuid[2]`.


## [Version 0.2.1 (Pre-Release, hotfix)][0.2.1] - 2017-10-23
### Fixed
- Removed `array_push($entriesUUID, $entryUUID);`.


## [Version 0.2 (Pre-Release)][0.2] - 2017-10-23
### Changed
- Modifiable timezone and locale.

### Removed
- Entries column of feeds table.


## [Version 0.1 (Pre-Release)][0.1] - 2017-10-15
**Initial release.**
- Subscribe check.
- Save received feed.


[Unreleased]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.5...develop
[0.5]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4.4...v0.5
[0.4.4]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4.3...v0.4.4
[0.4.3]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4.2...v0.4.3
[0.4.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4.1...v0.4.2
[0.4.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4...v0.4.1
[0.4]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.3...v0.4
[0.3]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.2...v0.3
[0.2.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2...v0.2.1
[0.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.1...v0.2
[0.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/3a2ef9c...v0.1

[#1]: https://github.com/kPherox/JMA-Publish-Sharer/pull/1
