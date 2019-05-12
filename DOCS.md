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