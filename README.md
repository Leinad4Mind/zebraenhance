# Zebra Enhance

Zebra Enhance adds confirmed friend requests, directional Close Friends, notifications, and profile friend-list privacy controls to phpBB's Zebra module.

## Requirements

- phpBB 3.3.17
- PHP 7.4 or newer

The 2.0 line is for phpBB 3.3 and will refuse to enable on phpBB 4.x.

## Features

- Friend requests must be accepted before phpBB creates the mutual friendship.
- Incoming and outgoing requests appear in UCP > Friends.
- Request and acceptance notifications use a unique request ID.
- Each user can independently mark an accepted friend as a Close Friend.
- Profile friend lists can be visible to everyone, registered users, non-foes, friends, Close Friends, or nobody.
- Friend acceptance/removal is symmetric and transactional.
- Requests and extension notifications are cleaned when a user is deleted; phpBB core cleans the Zebra rows.
- Close Friends changes use an ACL-checked, CSRF-protected POST endpoint. Its JavaScript is loaded only on UCP > Friends.

## Permissions

The 2.0 migration adds these ACP permissions:

- `u_ze_use` — use friend requests and enhanced friend lists
- `u_ze_close_friends` — manage Close Friends
- `m_ze_view_private_friendlists` — view private profile friend lists

The two user permissions are granted to the standard registered-user groups during upgrade. The moderator override is granted to Global Moderators.

## Install

Copy the extension to `ext/anavaro/zebraenhance`, then enable **Zebra Enhance** in ACP > Customise > Manage extensions.

## Upgrade from 1.x

1. Back up the forum database and extension files.
2. Disable Zebra Enhance in the ACP. Do **not** purge or delete its data.
3. Replace the files in `ext/anavaro/zebraenhance` with the 2.0 files.
4. Enable the extension again and let phpBB run its migrations.
5. Purge phpBB's cache.

The migration copies valid rows from the legacy `zebra_confirm` table into the new uniquely keyed request table. The legacy table remains available for downgrade safety and is removed only when the extension is purged. Existing 1.x notifications are discarded because their user-based item IDs are incompatible with the unique request IDs used by 2.0.

## Development

The test suite targets the official phpBB 3.3.17 source tree. Production code is checked against the phpBB extension coding standard, and all PHP files are syntax-checked from PHP 7.4 through current PHP 8 releases in CI.

The complete local verification also exercises the functional suite with phpBB served by PHP 7.4 and PHP 8.4.

## License

GNU General Public License, version 2. See [license.txt](license.txt).
