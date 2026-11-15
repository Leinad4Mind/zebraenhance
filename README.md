# Zebra Enhance

Zebra Enhance adds confirmed friend requests, directional Close Friends, custom friend circles, notifications, privacy controls for friend lists, birthdays, and private messages, and optional foe protections to phpBB's Zebra module.

## Requirements

- phpBB 3.3.0 or newer in the 3.3.x series (the latest maintenance release is strongly recommended)
- PHP 7.4 or newer

The 2.x line is for phpBB 3.3 and will refuse to enable on phpBB 4.x.

## Features

- Friend requests must be accepted before phpBB creates the mutual friendship.
- Incoming and outgoing requests appear in UCP > Friends.
- Incoming requests can be declined and blocked in one protected action.
- Requests sent from a profile may include an optional personal message.
- Users can accept requests from everyone, only friends of friends, or nobody.
- Profile controls create, accept, decline, decline-and-block, or cancel requests using numeric IDs and CSRF-protected AJAX.
- Request and acceptance notifications use a unique request ID.
- Request and acceptance notifications support board alerts and opt-in email through phpBB's notification preferences.
- Each user can independently mark an accepted friend as a Close Friend.
- Users can create up to 20 private friend circles and assign accepted friends to several circles.
- Profiles show mutual friends only when the viewer may see that profile's friend list.
- "View your friends' posts" in the quick links menu searches the posts of all accepted friends, and each friend in UCP > Friends has a link to search their posts. Administrators can switch both off in the ACP.
- UCP suggestions show privacy-eligible friends of friends and exclude existing, blocked, or pending relationships.
- Profile friend lists can be visible to everyone, registered users, non-foes, friends, Close Friends, or nobody.
- Profile friend lists show 50 friends at a time; "Show more" loads the next 50 in place through the same profile page and privacy checks.
- Users choose who can send them private messages: everyone, everyone except foes, friends only, or Close Friends only. Administrators and moderators can always send them. In a group message, only the members who would not accept it are left out.
- Users choose who can see their birthday and age with the same visibility levels as their friend list. A hidden birthday removes the age from profiles, the member list and posts, and leaves the user out of the board index birthday list.
- Users choose whether content from foes is shown, hidden behind a notice, or removed completely. The choice covers their posts (in topics, reply reviews, and post searches), the topics they started (in forum lists and topic searches), and identifiable quotes of their posts wherever phpBB renders them. A notice can be opened to reveal what it hides; for posts it is phpBB's own placeholder.
- Users can suppress quote, mention, reply, topic, and bookmark notifications created by foes.
- Foes can expire automatically after 24 hours, 7 days, or 30 days, or remain permanent.
- An enhanced UCP foe manager adds search, pagination, recorded dates, private notes, bulk removal, and a clear protection summary.
- PM, content, and notification protection can inherit the global default or be explicitly allowed/blocked for each foe.
- Administrators can enable the foe enhancement suite and independently expose PM, content, notification, temporary-foe, private-note, and per-foe exception controls.
- Friend acceptance/removal is symmetric and transactional.
- Friend and request lists are paginated. Administrators can configure the maximum pending requests per account.
- UCP users can search accepted friends and process selected incoming or outgoing requests in one action.
- Authorized administrators can review a paginated, read-only report of all pending requests in the ACP.
- A configurable cooldown prevents an explicitly declined requester from immediately contacting the same user again.
- Requests, circles, circle memberships, and extension notifications are cleaned when a user is deleted; phpBB core cleans the Zebra rows.
- Close Friends changes use an ACL-checked, CSRF-protected POST endpoint. Its JavaScript is loaded only on UCP > Friends.
- Vendor-prefixed [integration events](docs/events.md) let other extensions react to relationship changes.

## Languages

Bulgarian, Dutch, English, Farsi, German, Portuguese (Brazil), Spanish, and Swedish are included.

## Permissions

The 2.0 migration adds these ACP permissions:

- `u_zebraenhance_use_friend_requests` — use friend requests and enhanced friend lists
- `u_zebraenhance_manage_close_friends` — manage Close Friends
- `m_zebraenhance_view_private_friendlists` — view private profile friend lists, birthdays and ages

The two user permissions are granted to the standard registered-user groups during upgrade. The moderator override is granted to Global Moderators.

## Install

Copy the extension to `ext/anavaro/zebraenhance`, then enable **Zebra Enhance** in ACP > Customise > Manage extensions.

Board-wide request limits, the friends' posts search, and foe feature availability are configured in ACP > Customise > Zebra Enhance. Foe enhancements are disabled by default until an administrator enables them.

Friend list and birthday privacy are available in UCP > Friends. Once foe enhancements are enabled, the private message, foe content, and foe notification choices appear there too, and per-user foe settings are available in UCP > Friends & Foes > Enhanced foe management.

## Upgrade from 1.x

1. Back up the forum database and extension files.
2. Disable Zebra Enhance in the ACP. Do **not** purge or delete its data.
3. Replace the files in `ext/anavaro/zebraenhance` with the current 2.x files.
4. Enable the extension again and let phpBB run its migrations.
5. Purge phpBB's cache.

Upgrades within 2.x follow the same steps. Upgrading to 2.7.0 keeps each user's private message choice and leaves birthdays visible to everyone until users restrict them.

The migration copies valid rows from the legacy `zebra_confirm` table into the new uniquely keyed request table. The legacy table remains available for downgrade safety and is removed only when the extension is purged. Existing 1.x notifications are discarded because their user-based item IDs are incompatible with the unique request IDs used by 2.0.

## Development

The test suite targets both the first and latest supported phpBB 3.3 releases. The latest phpBB suite runs on PHP 7.4 and PHP 8.1. Production code is checked against the phpBB extension coding standard, and all PHP files are syntax-checked from PHP 7.4 through current PHP 8 releases in CI.

The complete local verification also exercises the functional suite with phpBB served by PHP 7.4 and PHP 8.4. phpBB 3.3's bundled PHPUnit is run with PHP 7.4 because that legacy test runner cannot start on PHP 8.4; this does not affect the PHP 8.4 web-runtime test.

## License

GNU General Public License, version 2. See [license.txt](license.txt).
