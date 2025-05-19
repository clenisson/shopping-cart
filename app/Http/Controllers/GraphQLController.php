<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;
use App\Models\Client;
use App\Models\Product;
use GraphQL\Error\DebugFlag;

class GraphQLController extends Controller
{
    protected $schema, $rootValue;

    public function __construct()
    {
        $sdl = file_get_contents(base_path('graphql/schema.graphql'));

        $this->schema = BuildSchema::build($sdl);
        $this->rootValue = [

        'clients' => function ($root, array $args, $ctx, $info) {
            return Client::all();
        },

        'client' => function ($root, array $args, $ctx, $info) {
            return Client::find($args['id']);
        },

        'createClient' => function ($root, array $args, $ctx, $info) {
            return Client::create([
                'name'  => $args['name'],
                'email' => $args['email'],
            ]);
        },
        'updateClient' => function ($root, array $args, $ctx, $info) {
            $client = Client::findOrFail($args['id']);
            $client->fill(array_filter([
                'name'  => $args['name']  ?? null,
                'email' => $args['email'] ?? null,
            ], fn($v) => ! is_null($v)));
            $client->save();
            return $client;
        },
        'deleteClient' => function ($root, array $args, $ctx, $info) {
            return (bool) Client::destroy($args['id']);
        },

        'products' => function ($root, array $args, $ctx, $info) {
            return Product::all();
        },

        'product' => function ($root, array $args, $ctx, $info) {
            return Product::find($args['id']);
        },

        'createProduct' => function ($root, array $args, $ctx, $info) {
            return Product::create([
                'name'  => $args['name'],
                'price' => $args['price'],
            ]);
        },
        'updateProduct' => function ($root, array $args, $ctx, $info) {
            $product = Product::findOrFail($args['id']);
            $product->fill(array_filter([
                'name'  => $args['name']  ?? null,
                'price' => $args['price'] ?? null,
            ], fn($v) => ! is_null($v)));
            $product->save();
            return $product;
        },
        'deleteProduct' => function ($root, array $args, $ctx, $info) {
            return (bool) Product::destroy($args['id']);
        },
    ];

    }

    public function handle(Request $request)
    {
        $input = $request->only(['query', 'variables']);
        try {
            $result = GraphQL::executeQuery(
                $this->schema,
                $input['query'],
                $this->rootValue,
                null,
                $input['variables'] ?? []
            );
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE|DebugFlag::INCLUDE_TRACE);
        } catch (\Exception $e) {
            $output = [
                'errors' => [
                    ['message' => $e->getMessage()]
                ]
            ];
        }

        return response()->json($output);
    }
}
