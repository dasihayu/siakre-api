<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;

class GenerateSwaggerDocsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate-all {--force : Overwrite existing doc files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis men-generate file dokumentasi Swagger untuk semua route API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning API routes...');

        $routes = Route::getRoutes();
        $groupedRoutes = [];

        foreach ($routes as $route) {
            $uri = $route->uri();

            // Hanya proses route yang berawalan api/ dan bukan route bawaan l5-swagger
            if (! str_starts_with($uri, 'api/') || str_contains($uri, 'documentation') || str_contains($uri, 'oauth2')) {
                continue;
            }

            $actionName = $route->getActionName();
            if ($actionName === 'Closure' || ! str_contains($actionName, '@')) {
                continue;
            }

            [$controllerClass, $methodName] = explode('@', $actionName);

            if (! class_exists($controllerClass)) {
                continue;
            }

            $methods = array_filter($route->methods(), fn ($m) => ! in_array($m, ['HEAD']));
            $httpMethod = strtolower($methods[0] ?? 'get');

            $groupedRoutes[$controllerClass][] = [
                'uri' => '/'.ltrim($uri, '/'),
                'httpMethod' => $httpMethod,
                'methodName' => $methodName,
                'middleware' => $route->middleware(),
            ];
        }

        $docsPath = app_path('Docs');
        if (! is_dir($docsPath)) {
            mkdir($docsPath, 0755, true);
        }

        $force = (bool) $this->option('force');
        $generatedCount = 0;

        foreach ($groupedRoutes as $controllerClass => $actions) {
            $controllerShortName = class_basename($controllerClass);
            $docClassName = str_replace('Controller', 'Doc', $controllerShortName);
            $fileName = $docsPath.'/'.$docClassName.'.php';

            if (file_exists($fileName) && ! $force) {
                $this->line("  - Lewati {$docClassName}.php (sudah ada, gunakan --force untuk overwrite)");

                continue;
            }

            $tag = Str::headline(str_replace('Controller', '', $controllerShortName));
            $content = $this->buildDocFileContent($docClassName, $tag, $controllerClass, $actions);

            file_put_contents($fileName, $content);
            $this->info("  + Berhasil membuat {$docClassName}.php");
            $generatedCount++;
        }

        $this->info("\nRegenerating Swagger UI docs...");
        $this->call('l5-swagger:generate');

        $this->newLine();
        $this->info("Selesai! {$generatedCount} file dokumentasi berhasil di-scaffold.");
        $this->line('Akses Swagger UI di: '.url('/api/documentation'));

        return Command::SUCCESS;
    }

    /**
     * Membangun kode PHP file Doc.
     */
    protected function buildDocFileContent(string $className, string $tag, string $controllerClass, array $actions): string
    {
        $methodsCode = [];
        $registeredMethods = [];

        foreach ($actions as $action) {
            $methodName = $action['methodName'];
            $httpMethod = ucfirst(strtolower($action['httpMethod']));
            $uri = $action['uri'];
            $isAuth = collect($action['middleware'])->contains(fn ($m) => str_contains($m, 'Authenticate') || str_contains($m, 'auth'));

            // Hindari method duplikat di class Doc yang sama jika ada route ganda
            $docMethodName = in_array($methodName, $registeredMethods) ? $methodName.'_'.strtolower($httpMethod) : $methodName;
            $registeredMethods[] = $docMethodName;

            // Parameter path {id}, {user}, etc
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $uri, $matches);
            $pathParams = $matches[1] ?? [];

            $parametersCode = '';
            if (! empty($pathParams)) {
                $paramLines = [];
                foreach ($pathParams as $param) {
                    $paramLines[] = "        new OA\\Parameter(\n            name: '{$param}',\n            in: 'path',\n            required: true,\n            schema: new OA\\Schema(type: 'string', example: '1')\n        ),";
                }
                $parametersCode = "    parameters: [\n".implode("\n", $paramLines)."\n    ],\n";
            }

            // Security
            $securityCode = $isAuth ? "    security: [['bearerAuth' => []]],\n" : '';

            // Request body untuk POST dan PUT
            $requestBodyCode = '';
            if (in_array(strtolower($httpMethod), ['post', 'put', 'patch'])) {
                $formRequestProps = $this->extractFormRequestProperties($controllerClass, $methodName);
                if (! empty($formRequestProps)) {
                    $propStrings = [];
                    foreach ($formRequestProps as $field => $type) {
                        $propStrings[] = "                new OA\\Property(property: '{$field}', type: '{$type}'),";
                    }
                    $requestBodyCode = "    requestBody: new OA\\RequestBody(\n        required: true,\n        content: new OA\\JsonContent(\n            properties: [\n".implode("\n", $propStrings)."\n            ]\n        )\n    ),\n";
                }
            }

            $summary = Str::headline($methodName).' '.$tag;

            $methodsCode[] = <<<PHP
    #[OA\\{$httpMethod}(
        path: '{$uri}',
        summary: '{$summary}',
        tags: ['{$tag}'],
{$securityCode}{$parametersCode}{$requestBodyCode}        responses: [
            new OA\\Response(response: 200, description: 'Operasi berhasil'),
            new OA\\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\\Response(response: 401, description: 'Unauthenticated'),
            new OA\\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function {$docMethodName}(): void {}
PHP;
        }

        $allMethods = implode("\n\n", $methodsCode);

        return <<<PHP
<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\\Tag(name: '{$tag}', description: 'Endpoint API {$tag}')]
class {$className}
{
{$allMethods}
}

PHP;
    }

    /**
     * Coba ekstrak properti dari FormRequest jika controller method menggunakan FormRequest.
     */
    protected function extractFormRequestProperties(string $controllerClass, string $methodName): array
    {
        $properties = [];
        try {
            $reflector = new ReflectionClass($controllerClass);
            if (! $reflector->hasMethod($methodName)) {
                return $properties;
            }

            $method = $reflector->getMethod($methodName);
            foreach ($method->getParameters() as $param) {
                $type = $param->getType();
                if ($type && ! $type->isBuiltin()) {
                    $className = $type->getName();
                    if (is_subclass_of($className, 'Illuminate\Foundation\Http\FormRequest')) {
                        $requestInstance = new $className;
                        if (method_exists($requestInstance, 'rules')) {
                            $rules = $requestInstance->rules();
                            foreach (array_keys($rules) as $field) {
                                if (! str_contains($field, '.')) {
                                    $properties[$field] = 'string';
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore reflection errors
        }

        return $properties;
    }
}
