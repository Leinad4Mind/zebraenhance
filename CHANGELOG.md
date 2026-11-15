# Changelog

All notable changes to Zebra Enhance are documented in this file.

## Unreleased

### Added

- Highlighted posts, topics, search results and private messages from friends with a coloured edge and a mark next to the author, in a separate colour for Close Friends. Each user can switch it off in UCP > Friends, and an ACP setting switches it off for the board; both are on by default.

## 2.7.0 - 2026-11-15

### Added

- Added "View your friends' posts" to the quick links menu, a search over the posts of all accepted friends that respects forum permissions, and a link to search each friend's posts in UCP > Friends. An ACP setting switches both on or off; it is on by default.
- Added a UCP choice for who can see a user's birthday and age, with the same visibility levels as the friend list. Hidden birthdays disappear from profiles, the member list, posts and the board index birthday list; the private friend list permission also shows them.
- Added a notice mode for content from foes: their quotes collapse into a notice that can be opened, and the topics they started are replaced by a notice row with a button that shows them.
- Topics started by foes are removed from forum lists and topic searches when content from foes is removed completely.

### Changed

- The UCP setting that blocked private messages from foes now chooses who can send private messages: everyone, everyone except foes, friends only, or Close Friends only. Existing choices carry over, and administrators and moderators are no longer filtered.
- A group message no longer fails for the whole group when one member would not accept it from the sender: only that member is left out, and the others receive it.
- Profile friend lists show 50 friends at a time with a "Show more" button that loads the next 50 in place, and say how many of the total are shown. Without JavaScript the button reloads the profile with more friends.
- The UCP setting for content from foes now offers three choices: do not hide, hide behind a notice, or remove completely. Existing users who enabled it keep the complete removal.

### Fixed

- Upgrading from 2.4.0 now carries over the permissions, their group and role settings, and the request limits that were renamed after that release; without this, friend features disappeared for everyone after the upgrade.
- Profile friend lists no longer stop silently at the first 100 friends.
- Completed the Bulgarian, Dutch, Farsi, German, Portuguese (Brazil), Spanish, and Swedish translations, and corrected inconsistent terms and forms of address in them.

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
