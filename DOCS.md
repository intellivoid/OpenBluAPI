# OpenBlu API Documentation

## Authentication Methods :lock:
There are two types of methods for authentication, the first method is the
most common form of authentication which is the use of a API Key, the second
method is the use of a certificate.

### API Key :closed_lock_with_key:
The API Key identifies your API Subscription (plan) on OpenBlu which is associated with your Intellivoid Account, it is a simple form
of authentication which is used with the parameter `api_key` in either a
`POST` or `GET` request, the examples given in this documentation will explain
how to accomplish this

### Certificate :lock_with_ink_pen:
A Certificate is represented as a `.crt` file, *which is non-standard*. this
is sent to the server under the `certificate` parameter with the contents
base64 encoded. This also works with a `POST` or `GET` request. The purpose of
a certificate is designed for user-friendly clients which would simply prompt
the user for a certificate file instead of a API Key.

Authentication used with a certificate is very similar to authentication used with
a API Key, with the only difference is that the contents of the certificate is
base64 encoded before it gets sent to the server.

#### Multiple Authentication Methods Notice
Although no errors will be raised when using both an API Key and Certificate during
authentication, the certificate will simply be ignored even if the API Key is invalid.

:triangular_flag_on_post: ***It is recommended to only use one form of authentication***

----------------------------------------------------------------------------

# Authentication Examples :mag:

When this documentation talks about "Parameters", we are talking about the values within
a `GET` request or a `POST` request (URL Parameters or `multipart/form-data`). The API
Doesn't care where you place your values, for as long as it sent appropriately within the
supported request method. This means you can include your API Key in the URL while the rest of your request parameters in a `POST` Request Body if that's your style.

### API Key Example :star:

Authentication using a API Key is simply done with the `api_key` parameter.

`GET` Request
```http
GET /openblu/v1/exampleMethod?api_key=123 HTTP/1.1
Host: api.intellivoid.info
```

`POST` Request
```http
POST /openblu/v1/exampleMethod HTTP/1.1
Content-Length: 80
Host: api.intellivoid.info
Content-Type: application/x-www-form-urlencoded

api_key=123
```

`POST` Request but with `api_key` as the `GET` parameter
```http
POST /openblu/v1/exampleMethod?api_key=123 HTTP/1.1
Content-Length: 7
Host: api.intellivoid.info
Content-Type: application/x-www-form-urlencoded

foo=bar
```



### Certificate Example :star:

Authentication using a Certificate is simply done with the `certificate` parameter
but the contents of the certificate must be base64 encoded.

For this example the certificate we are using is gonna be

```text
3e4fbdf5993a1b8db769c7498c3087957dba64a161b86de39fe687184884a091e40a95615d847fe0e08e9e20b750a6683af511e2c5c944e63e8a0eb4ed1aa38597def321a753fceb818eb1be7d3e7916a80c99504882fabffb104c39d7f5843f6a094f62a6bfc54147f874d874fb89915f7a83c7f0084795bef43a0290acbd83(8359f8080333b065243814f7ce6fc328fb6bd82caee4e410beb9351a94a97bfa0668d09faf528fc4408d37050125265103c2687602b5f7586aaf62002a3b21e018bd9b6c6aabf47f1297a8e9facb546b4bed6e96f82cd5a70885e4a21d42e72a0dbb8d5d320d8d67a2262cae0cba23d62427a5fb25edfa0cfe11b92e98b82768)^32568f2822a2b90a9cbe32a05280a913-099eab34/intellivoid
```

When converted to base64, the output is
```base64
M2U0ZmJkZjU5OTNhMWI4ZGI3NjljNzQ5OGMzMDg3OTU3ZGJhNjRhMTYxYjg2ZGUzOWZlNjg3MTg0ODg0YTA5MWU0MGE5NTYxNWQ4NDdmZTBlMDhlOWUyMGI3NTBhNjY4M2FmNTExZTJjNWM5NDRlNjNlOGEwZWI0ZWQxYWEzODU5N2RlZjMyMWE3NTNmY2ViODE4ZWIxYmU3ZDNlNzkxNmE4MGM5OTUwNDg4MmZhYmZmYjEwNGMzOWQ3ZjU4NDNmNmEwOTRmNjJhNmJmYzU0MTQ3Zjg3NGQ4NzRmYjg5OTE1ZjdhODNjN2YwMDg0Nzk1YmVmNDNhMDI5MGFjYmQ4Myg4MzU5ZjgwODAzMzNiMDY1MjQzODE0ZjdjZTZmYzMyOGZiNmJkODJjYWVlNGU0MTBiZWI5MzUxYTk0YTk3YmZhMDY2OGQwOWZhZjUyOGZjNDQwOGQzNzA1MDEyNTI2NTEwM2MyNjg3NjAyYjVmNzU4NmFhZjYyMDAyYTNiMjFlMDE4YmQ5YjZjNmFhYmY0N2YxMjk3YThlOWZhY2I1NDZiNGJlZDZlOTZmODJjZDVhNzA4ODVlNGEyMWQ0MmU3MmEwZGJiOGQ1ZDMyMGQ4ZDY3YTIyNjJjYWUwY2JhMjNkNjI0MjdhNWZiMjVlZGZhMGNmZTExYjkyZTk4YjgyNzY4KV4zMjU2OGYyODIyYTJiOTBhOWNiZTMyYTA1MjgwYTkxMy0wOTllYWIzNC9pbnRlbGxpdm9pZA==
```


`GET` Request
```http
GET /openblu/v1/exampleMethod?certificate=M2U0ZmJkZjU5OTNhMWI4ZGI3NjljNzQ5OGMzMDg3OTU3ZGJhNjRhMTYxYjg2ZGUzOWZlNjg3MTg0ODg0YTA5MWU0MGE5NTYxNWQ4NDdmZTBlMDhlOWUyMGI3NTBhNjY4M2FmNTExZTJjNWM5NDRlNjNlOGEwZWI0ZWQxYWEzODU5N2RlZjMyMWE3NTNmY2ViODE4ZWIxYmU3ZDNlNzkxNmE4MGM5OTUwNDg4MmZhYmZmYjEwNGMzOWQ3ZjU4NDNmNmEwOTRmNjJhNmJmYzU0MTQ3Zjg3NGQ4NzRmYjg5OTE1ZjdhODNjN2YwMDg0Nzk1YmVmNDNhMDI5MGFjYmQ4Myg4MzU5ZjgwODAzMzNiMDY1MjQzODE0ZjdjZTZmYzMyOGZiNmJkODJjYWVlNGU0MTBiZWI5MzUxYTk0YTk3YmZhMDY2OGQwOWZhZjUyOGZjNDQwOGQzNzA1MDEyNTI2NTEwM2MyNjg3NjAyYjVmNzU4NmFhZjYyMDAyYTNiMjFlMDE4YmQ5YjZjNmFhYmY0N2YxMjk3YThlOWZhY2I1NDZiNGJlZDZlOTZmODJjZDVhNzA4ODVlNGEyMWQ0MmU3MmEwZGJiOGQ1ZDMyMGQ4ZDY3YTIyNjJjYWUwY2JhMjNkNjI0MjdhNWZiMjVlZGZhMGNmZTExYjkyZTk4YjgyNzY4KV4zMjU2OGYyODIyYTJiOTBhOWNiZTMyYTA1MjgwYTkxMy0wOTllYWIzNC9pbnRlbGxpdm9pZA%3D%3D HTTP/1.1
Host: api.intellivoid.info
```

`POST` Request
```http
POST /openblu/v1/exampleMethod HTTP/1.1
Content-Length: 778
Host: api.intellivoid.info
Content-Type: application/x-www-form-urlencoded

certificate=M2U0ZmJkZjU5OTNhMWI4ZGI3NjljNzQ5OGMzMDg3OTU3ZGJhNjRhMTYxYjg2ZGUzOWZlNjg3MTg0ODg0YTA5MWU0MGE5NTYxNWQ4NDdmZTBlMDhlOWUyMGI3NTBhNjY4M2FmNTExZTJjNWM5NDRlNjNlOGEwZWI0ZWQxYWEzODU5N2RlZjMyMWE3NTNmY2ViODE4ZWIxYmU3ZDNlNzkxNmE4MGM5OTUwNDg4MmZhYmZmYjEwNGMzOWQ3ZjU4NDNmNmEwOTRmNjJhNmJmYzU0MTQ3Zjg3NGQ4NzRmYjg5OTE1ZjdhODNjN2YwMDg0Nzk1YmVmNDNhMDI5MGFjYmQ4Myg4MzU5ZjgwODAzMzNiMDY1MjQzODE0ZjdjZTZmYzMyOGZiNmJkODJjYWVlNGU0MTBiZWI5MzUxYTk0YTk3YmZhMDY2OGQwOWZhZjUyOGZjNDQwOGQzNzA1MDEyNTI2NTEwM2MyNjg3NjAyYjVmNzU4NmFhZjYyMDAyYTNiMjFlMDE4YmQ5YjZjNmFhYmY0N2YxMjk3YThlOWZhY2I1NDZiNGJlZDZlOTZmODJjZDVhNzA4ODVlNGEyMWQ0MmU3MmEwZGJiOGQ1ZDMyMGQ4ZDY3YTIyNjJjYWUwY2JhMjNkNjI0MjdhNWZiMjVlZGZhMGNmZTExYjkyZTk4YjgyNzY4KV4zMjU2OGYyODIyYTJiOTBhOWNiZTMyYTA1MjgwYTkxMy0wOTllYWIzNC9pbnRlbGxpdm9pZA%3D%3D
```

----------------------------------------------------------------------------

# Authentication Errors

If you simply did not provide the correct parameters to authenticate or the
authentication method fails you will be receive a JSON response explaining
the error, alongside with the appropriate HTTP Error Codes. Below are a examples
of possible responses for authentication failures


### Authentication Required :exclamation:

This is returned when the server expected an authentication method but none was given

```json
{
    "status": false,
    "code": 401,
    "message": "Authentication is required"
}
```

### Incorrect Authentication :no_entry:

This is returned when the given authentication data is incorrect/invalid

```json
{
    "status": false,
    "code": 401,
    "message": "Incorrect Authentication"
}
```


### Suspended :fire:

This is returned if your Access Key was suspended (Certificate or API Key)

```json
{
    "status": false,
    "code": 403,
    "message": "Your access key has been suspended"
}
```

----------------------------------------------------------------------------

# API Methods

OpenBlu's API Functionality is basic in terms of functionality. At the moment
you can only retrieve server listings and individual server details.

| Module Name | Access URI                                         | Request Methods | Description                                                                                                                             |
|-------------|----------------------------------------------------|-----------------|-----------------------------------------------------------------------------------------------------------------------------------------|
| GetServers  | https://api.intellivoid.info/openblu/v1/getServers | `GET` `POST`    | Retrieves a list of available servers which are represented in an array as ServerListing Objects that contains limited information      |
| GetServer  | https://api.intellivoid.info/openblu/v1/getServer  | `GET` `POST`    | Gets the full server information, Host Name IP Address, OpenVPN Configuration Parameters, Certificates and a .ovpn file contents itself |


### GetServers Module

This module retrieves a full list of available servers that you can connect to from
OpenBlu, Each server represented in the array is a ServerListing Object which means
limited information about each server is displayed until you request for the server
details with `GetServer` module.

| Parameter Name  | Default Value | Required | Description                                                                                                                                                                                                                                |
|-----------------|---------------|----------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| order_by        | sessions      | No       | The value to order the results by, for example if the value is `sessions` the results will be ordered by the amount of sessions depending of the value `order_direction`                                                                   |
| order_direction | ascending     | No       | The direction of the order results, the two acceptable values are `ascending` or `descending`.  `ascending` will display the servers in an ascending order depending on what `order_by` is  set to, vice versa for the `descending` value. |



Basic order filters can be applied to alter the output of the listing, either by
searching for a country, ordered by the amount of sessions to the server, etc. The
possible values for `order_by` are displayed on the list below

| Possible Value | Description                                                                |
|----------------|----------------------------------------------------------------------------|
| last_updated   | The Unix Timestamp of when this server was last updated to OpenBlu         |
| ping           | The ping time (ms) from OpenBlu to this server                             |
| score          | The score of the server depending on the sessions over time                |
| sessions       | The current amount of users connected to this server right now             |
| total_sessions | The total amount of servers that has been connected to the server all time |


##### Example Request

An example request is displayed below using a simple GET request. The response is
larger, but for demonstration purposes the results are cut

URL: `https://api.intellivoid.info/openblu/v1/getServers?api_key=<API KEY>`

```json
{
    "status": true,
    "status_code": 200,
    "payload": {
        "total_results": 247,
        "servers": [
            {
                "id": "501ee1fe673f6bc9",
                "score": 1243666,
                "ping": 12,
                "country": "Japan",
                "country_short": "JP",
                "sessions": 277,
                "total_sessions": 2138574,
                "last_updated": 1554909553,
                "created": 1554828214
            },
			...
            {
                "id": "17442bb5ef39b811",
                "score": 6900,
                "ping": 0,
                "country": "France",
                "country_short": "FR",
                "sessions": 0,
                "total_sessions": 0,
                "last_updated": 1554909562,
                "created": 1554909562
            }
        ]
    },
    "ref_code": "fdd5a0bce8986718118780365eb25690ae0ca1919bbd3146c04f14f09a6d2b0d"
}

```

Each server is represented in a ServerListing object, the structure is shown below

#### ServerListing Object


| Variable Name  | Type      | Description                                                                 |
|----------------|-----------|-----------------------------------------------------------------------------|
| id             | `string`  | The ID of the server, using this you can retrieve the server details        |
| score          | `integer` | The score of the server depending on the amount of sessions over time       |
| ping           | `integer` | The ping (ms) between OpenBlu and the server                                |
| country        | `string`  | The name of the country that this server is located at                      |
| country_short  | `string`  | A shorter representation of the country in two letters (CA, US, KR, etc...) |
| sessions       | `integer` | The amount of users currently connected to this server                      |
| total_sessions | `integer` | The total amount of users that has connected to this server (All time)      |
| last_updated   | `integer` | The Unix Timestamp of when this server was last updated                     |
| created        | `integer` | The Unix Timestamp of when this server was created                          |