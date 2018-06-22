# Changelog
Changelog.

## [Unreleased]

## [Version 0.4 (Pre-Release)][0.4] - 2018-06-23
### Added
- Entries list to index page.
- Entry page & kinds template.
- Entries filter.
- EntryDetails table.
- Google Maps JavaScript API key.

### Changed
- XML Document database to file.
- Optimize entries table.

## [Version 0.3 (Pre-Release)][0.3] - 2018-06-03
### Added
- Support Social login.
- Add feature test.
- Add sample data to test directory.

### Changed
- Laravel version upgrade 5.5 -> 5.6.
- Bootstrap version upgrade 3.x -> 4.x.
- Rename PubSubHubbub to WebSub.
- Rename model namespace App\Model to App\Eloquents\Model.
- Save entry url's xml.
- Add relation for Entry and Feed.

### Removed
- Rate limit for websub endpoint.

## [Version 0.2.2 (Pre-Release)][0.2.2] - 2017-10-23
### Fixed
- Change `$entries->feed_uuid = $uuid` to `$entries->feed_uuid = $uuid[2]`.

## [Version 0.2.1 (Pre-Release)][0.2.1] - 2017-10-23
### Fixed
- Remove `array_push($entriesUUID, $entryUUID);`.

## [Version 0.2 (Pre-Release)][0.2] - 2017-10-23
### Changed
- Modifiable timezone and locale.

### Removed
- Entries column of feeds table.

## [Version 0.1 (Pre-Release)][0.1] - 2017-10-15
**Initial release.**
- Subscribe check.
- Save received feed.

[Unreleased]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.4...develop
[0.4]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.3...v0.4
[0.3]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.2...v0.3
[0.2.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2...v0.2.1
[0.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.1...v0.2
[0.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/3a2ef9c...v0.1

