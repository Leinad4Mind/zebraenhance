# Integration events

Zebra Enhance dispatches phpBB events after relationship changes have been
persisted successfully. Other extensions can subscribe through the standard
phpBB event dispatcher without reading Zebra Enhance tables directly.

Event names and existing payload fields are treated as a compatibility
contract. Future releases may add optional fields but will not rename or
remove the fields documented here within the 2.x series.

No event is dispatched for invalid, unauthorized, missing, or idempotent
operations.

Payloads are informational. Changing an event field in a subscriber does not
change or roll back the completed relationship operation.

## Friend request events

The following events share the same core payload:

| Event | Meaning |
| --- | --- |
| `anavaro.zebraenhance.friend_request_created` | A new pending request was created. |
| `anavaro.zebraenhance.friend_request_accepted` | A request was accepted and the mutual friendship exists. |
| `anavaro.zebraenhance.friend_request_declined` | The recipient declined the request or it was removed because of a foe relationship. |
| `anavaro.zebraenhance.friend_request_cancelled` | The requester cancelled the pending request. |

| Field | Type | Description |
| --- | --- | --- |
| `request_id` | integer | Stable ID of the request. |
| `requester_id` | integer | User who created the request. |
| `recipient_id` | integer | User who received the request. |
| `actor_id` | integer | User whose action caused this event. |
| `request_time` | integer | Unix timestamp stored with the request. |
| `reason` | string | Present only for decline/cancel events: `user`, `foe`, or `relationship_removed`. |

## Friendship removal

`anavaro.zebraenhance.friendship_removed` is dispatched once per affected
friend after the symmetric friendship rows have been removed by a normal UCP
removal or foe operation. Bulk phpBB user deletion does not dispatch one event
per former relationship.

| Field | Type | Description |
| --- | --- | --- |
| `user_id` | integer | User who initiated the removal or foe operation. |
| `friend_id` | integer | Other user in the former friendship. |
| `reason` | string | `relationship_removed` or `foe`. |

## Close Friends

`anavaro.zebraenhance.close_friend_changed` is directional: changing one
user's Close Friends list does not change the other user's list.

| Field | Type | Description |
| --- | --- | --- |
| `owner_id` | integer | Owner of the Close Friends list. |
| `friend_id` | integer | Friend whose state changed. |
| `old_state` | boolean | Previous Close Friend state. |
| `new_state` | boolean | New Close Friend state. |

## Friend-list visibility

`anavaro.zebraenhance.friend_list_visibility_changed` is dispatched when a
user selects a different profile friend-list visibility level.

| Field | Type | Description |
| --- | --- | --- |
| `user_id` | integer | Owner of the profile setting. |
| `old_visibility` | integer | Previous visibility level, from 0 through 5. |
| `new_visibility` | integer | New visibility level, from 0 through 5. |

## Subscriber example

```php
static public function getSubscribedEvents()
{
	return array(
		'anavaro.zebraenhance.friend_request_accepted' => 'friend_request_accepted',
	);
}

public function friend_request_accepted($event)
{
	$requester_id = (int) $event['requester_id'];
	$recipient_id = (int) $event['recipient_id'];
	// React to the new friendship here.
}
```
