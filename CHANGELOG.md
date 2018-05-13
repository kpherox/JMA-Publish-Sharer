# Changelog
Changelog.

## [Unreleased]
### Added
- Support Twitter OAuth login.
- Add feature test.

### Changed
- Laravel version upgrade 5.5 -> 5.6
- Remove rate limit for websub endpoint.
- Save entry url's xml.

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
- entries column of feeds table.

## [Version 0.1 (Pre-Release)][0.1] - 2017-10-15
**Initial release.**
- Subscribe check.
- Save received feed.

[Unreleased]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.2...develop
[0.2.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.2...v0.2.1
[0.2]: https://github.com/kPherox/JMA-Publish-Sharer/compare/v0.1...v0.2
[0.1]: https://github.com/kPherox/JMA-Publish-Sharer/compare/3a2ef9c...v0.1

