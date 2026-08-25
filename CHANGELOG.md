# Changelog

All notable changes to Zebra Enhance are documented in this file.

## 2.6.0 - 2026-08-25

### Added

- Added optional suppression of quote, mention, reply, topic, and bookmark notifications created by foes.
- Added temporary foes for 24 hours, 7 days, 30 days, or a permanent duration, with automatic expiry.
- Added an enhanced UCP foe manager with search, pagination, recorded dates, private notes, bulk removal, and protection status.
- Added per-foe PM, content, and notification exceptions that can inherit or override the user's global privacy preferences.
- Added an ACP master switch and individual availability controls for every enhanced foe capability; the suite is disabled by default on upgrade.

## 2.5.0 - 2026-08-24

### Added

- Added a UCP preference that blocks private messages from foes, including group-message and nonstandard submission safeguards.
- Added a separate UCP preference that completely hides foe-authored posts from topics, reply reviews, and post-search results, plus identifiable quotes wherever phpBB renders them.

## 2.4.0 - 2026-08-24

### Added

- Confirmed friend requests with stable request IDs, incoming and outgoing UCP lists, notifications, and optional email delivery.
- CSRF-protected AJAX actions to create, accept, decline, decline and block, cancel, and bulk-process friend requests.
- Optional request messages and friend controls on user profiles.
- Directional Close Friends and private custom friend circles with vendor-prefixed integration events.
- Privacy-aware profile friend lists, mutual friends, friend suggestions, request policies, and decline cooldowns.
- Paginated friend/request lists, friend search, ACP configuration, ACL options, and a read-only ACP pending-request report.

### Changed

- Updated the extension for the phpBB 3.3.x series (tested on 3.3.0 and 3.3.17) and PHP 7.4 or newer.
- Friend acceptance and removal are symmetric and transactional.

### Fixed

- Preserved directional foe rows when friendships are removed and cleaned mutual friendships when a foe is added.
- Corrected notification lifecycle handling during enable, disable, and purge.
- Removed username-dependent request actions in favor of numeric request IDs.
- Added cleanup for requests, circles, cooldowns, and extension notifications when users are deleted.
- Hardened authorization, CSRF validation, output escaping, migration rollback, duplicate-key handling, and release validation.

## 1.0.0

- Original Zebra Enhance release for phpBB 3.1.
