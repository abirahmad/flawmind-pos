<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "FlawMind API Documentation",
    description: "API documentation for FlawMind ERP system",
    contact: new OA\Contact(
        name: "API Support",
        email: "support@flawmind.com"
    )
)]
#[OA\Server(
    url: "/api",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Enter your Bearer token in the format: Bearer {token}"
)]
abstract class Controller
{
    //
}
