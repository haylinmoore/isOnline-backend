# isonline-backend
The server-side storage and validation code for isOnline V2.

## Files of interest:
| File name      | Contents                                      |
| -------------- | --------------------------------------------- |
| [API.md]       | Documentation for the isOnline API            |
| [DB.md]        | Information on the general database structure |
| [LICENSE]      | License for usage of the source code          |

## Notices:
1. The following files have had been modified from the original files hosted on
   the official Scratchtools site (<https://scratchtools.tk/>). This has been
   done to maintain the security of information stored on the site:
   * [global.php]
   * [environment.php]
2. The file [DB.md] only shows the structure of tables within the Scratchtools
   database used by isOnline. This has again been done for security.
3. isOnline has active bot detection. The condition for users to be marked as a
   bot is to have sent more than 20 API requests per a 10 second period. If a
   user has been marked as a bot, they will have to contact
   [chooper100](https://scratch.mit.edu/users/chooper100/) in order to reset
   their account. The script used to reset accounts is part of the private
   Scratchtools administration API and is therefore not included in this public
   repository.

[API.md]: ../master/API.md
[DB.md]: ../master/DB.md
[LICENSE]: ../master/LICENSE
[global.php]: ../master/src/global.php
[environment.php]: ../master/src/environment.php
