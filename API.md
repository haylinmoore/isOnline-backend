# isOnline Public API #
This is the specification for the part of the API used by the official isOnline extension(s).

## Test key ##
```
GET https://scratchtools.tk/isonline/api/v1/[username]/[key]/test/
```

### Parameters ###
- **Username:** the username of the user sending the request
- **Key:** the API key of the user sending the request

### Standard response ###
Status code 200
```json
{
    "valid":true
}
```
### Remarks ###
- Valid is only set to true if both the username and key are correct and the user is not a bot
- The request body will always be of the same format: no error codes will be displayed
- A generic 404 error may occur however if the URL is invalid

## Get status ##
```
GET https://scratchtools.tk/isonline/api/v1/[username]/[key]/get/[other]/
```

### Parameters ###
- **Username:** the username of the user sending the request
- **Key:** the API key of the user sending the request
- **Other:** the username of the user to get the status of

### Standard response ###
Status code 200
```json
{
    "timestamp":"Unix timestamp",
    "status":"online|absent|dnd"
}
```

### Incorrect username & API key response ###
Status code 403
```json
{
    "timestamp":null,
    "status":"incorrect key"
}
```

### Bot detected response ###
Status code 403
```json
{
    "timestamp":null,
    "status":"bot"
}
```

### User not found/registered response ###
Status code 404
```json
{
    "timestamp":null,
    "status":"not registered"
}
```

### Generic error response ###
Status code 500
```json
{
    "timestamp":null,
    "status":"error"
}
```

### Remarks ###
- Will not auto-detect if offline (so status will always be last recorded status)
- Timestamp will be when the last set request was (in GMT - UTC+00:00) using Unix time (seconds since Epoch)
- A timestamp of 0 and a status of "online" indicates that a user has been verified but has not yet set their status

## Set status ##

```
POST https://scratchtools.tk/isonline/api/v1/[username]/set/[status]/
```

### Parameters ###
- **Username:** the username of the user sending the request
- **Key:** the API key of the user sending the request
- **Status:** the new status of the user

### Request body ###
No request body

### Standard response ###
Status code 200
```json
{
    "result":"success"
}
```

### Incorrect API key response ###
Status code 403
```json
{
    "result":"incorrect key"
}
```

### Bot detected response ###
Status code 403
```json
{
    "result":"bot"
}
```

### User not found/registered response ###
Status code 404
```json
{
    "result":"not registered"
}
```

### Generic error response ###
Status code 500
```json
{
    "result":"error"
}
```

### Remarks ###
- No offline status
- Server will auto-detect timestamp
- The only allowed status values are:
  * online
  * absent
  * dnd

## User Count ##

```
GET https://scratchtools.tk/isonline/api/v1/count/
```

### Parameters ###
No parameters

### Standard response ###
Status code 200
```json
{
    "count":"sum of users"
}
```

### Generic error ###
Status code 500
```json
{
    "count":null
}
```

## Excluded API Pages ##
These API pages should only be used by [the official isOnline registration page](https://scratchtools.tk/isonline/register/):

- [keycheck.php](../master/src/isonline/api/v1/keycheck.php)
- [keygen.php](../master/src/isonline/api/v1/keygen.php)
- [validate.php](../master/src/isonline/api/v1/validate.php)

Therefore, they have been excluded from the public API specification
and are liable to change at any point without any notice provided.