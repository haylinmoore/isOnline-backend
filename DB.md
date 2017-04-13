# Database Tables
The following tables within the Scratchtools database are used by isOnline:
- isonline

## isonline
*Stores data for the isOnline project.*

Columns:

| Name               | Type            | Null | Default | Comments                                         |
| ------------------ | --------------- | ---- | ------- | ------------------------------------------------ |
| user **[Primary]** | `varchar(20)`   | No   | *None*  | The username of the user                         |
| keycode            | `char(64)`      | No   | *None*  | The API key for the user                         |
| timestamp          | `int(11)`       | No   | *None*  | The Unix timestamp of the last recorded activity |
| status             | `varchar(30)`   | Yes  | `NULL`  | The last known status of the user                |
| log                | `json`          | Yes  | `NULL`  | The timestamps of the last 10 API requests       |
| bot                | `tinyint(1)`    | No   | `0`     | Whether the user has been identified as a bot    |

Example record:

| user       | keycode        | timestamp  | status | log                                                                                                            | bot |
| ---------- | -------------- | ---------- | ------ | -------------------------------------------------------------------------------------------------------------- | --- |
| chooper100 | *sha-256 hash* | 1489347721 | absent | `[1491580987, 1491580987, 1491580988, 1491580988, 1491580988, 1491580988, 1491580988, 1491580988, 1491580988]` | 0   |