# Zebra Enhance

Zebra Enhance adds confirmed friend requests, directional Close Friends, custom friend circles, notifications, and profile friend-list privacy controls to phpBB's Zebra module.

## Requirements

- phpBB 3.3.0 or newer in the 3.3.x series (the latest maintenance release is strongly recommended)
- PHP 7.4 or newer

The 2.x line is for phpBB 3.3 and will refuse to enable on phpBB 4.x.

## Features

- Friend requests must be accepted before phpBB creates the mutual friendship.
- Incoming and outgoing requests appear in UCP > Friends.
- Requests sent from a profile may include an optional personal message.
- Users can accept requests from everyone, only friends of friends, or nobody.
- Profile controls create, accept, decline, or cancel requests using numeric IDs and CSRF-protected AJAX.
- Request and acceptance notifications use a unique request ID.
- Request and acceptance notifications support board alerts and opt-in email through phpBB's notification preferences.
- Each user can independently mark an accepted friend as a Close Friend.
- Users can create up to 20 private friend circles and assign accepted friends to several circles.
- Profile friend lists can be visible to everyone, registered users, non-foes, friends, Close Friends, or nobody.
- Friend acceptance/removal is symmetric and transactional.
- Friend and request lists are paginated. Administrators can configure the maximum pending requests per account.
- A configurable cooldown prevents an explicitly declined requester from immediately contacting the same user again.
- Requests, circles, circle memberships, and extension notifications are cleaned when a user is deleted; phpBB core cleans the Zebra rows.
- Close Friends changes use an ACL-checked, CSRF-protected POST endpoint. Its JavaScript is loaded only on UCP > Friends.
- Vendor-prefixed [integration events](docs/events.md) let other extensions react to relationship changes.

## Permissions

The 2.0 migration adds these ACP permissions:

- `u_ze_use` — use friend requests and enhanced friend lists
- `u_ze_close_friends` — manage Close Friends
- `m_ze_view_private_friendlists` — view private profile friend lists

The two user permissions are granted to the standard registered-user groups during upgrade. The moderator override is granted to Global Moderators.

## Install

Copy the extension to `ext/anavaro/zebraenhance`, then enable **Zebra Enhance** in ACP > Customise > Manage extensions.

Board-wide request limits are available in ACP > Customise > Zebra Enhance. Individual request privacy is available in UCP > Friends.

## Upgrade from 1.x

1. Back up the forum database and extension files.
2. Disable Zebra Enhance in the ACP. Do **not** purge or delete its data.
3. Replace the files in `ext/anavaro/zebraenhance` with the current 2.x files.
4. Enable the extension again and let phpBB run its migrations.
5. Purge phpBB's cache.

The migration copies valid rows from the legacy `zebra_confirm` table into the new uniquely keyed request table. The legacy table remains available for downgrade safety and is removed only when the extension is purged. Existing 1.x notifications are discarded because their user-based item IDs are incompatible with the unique request IDs used by 2.0.

## Development

The test suite targets both the first and latest supported phpBB 3.3 releases. Production code is checked against the phpBB extension coding standard, and all PHP files are syntax-checked from PHP 7.4 through current PHP 8 releases in CI.

The complete local verification also exercises the functional suite with phpBB served by PHP 7.4 and PHP 8.4. phpBB 3.3's bundled PHPUnit is run with PHP 7.4 because that legacy test runner cannot start on PHP 8.4; this does not affect the PHP 8.4 web-runtime test.

## License

GNU General Public License, version 2. See [license.txt](license.txt).
