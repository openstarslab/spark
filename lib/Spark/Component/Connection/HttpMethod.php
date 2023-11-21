<?php

namespace Spark\Component\Connection;

/**
 * UserRolesNames
 */
enum HttpMethod: string
{
    case POST = "POST";
    case GET = "GET";
    case DELETE = "DELETE";
    case PATCH = "PATCH";
    case PUT = "PUT";
}