<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\MataKuliah;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
    protected $description = 'Otomatis men-generate file dokumentasi Swagger untuk semua route API dengan tabel struktur dan data riil';

    /**
     * Cache data riil dari database.
     *
     * @var array<string, mixed>
     */
    protected array $realDataCache = [];

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
        $activeDocClasses = [];

        foreach ($groupedRoutes as $controllerClass => $actions) {
            $controllerShortName = class_basename($controllerClass);
            $docClassName = str_replace('Controller', 'Doc', $controllerShortName);
            $activeDocClasses[] = $docClassName.'.php';
            $fileName = $docsPath.'/'.$docClassName.'.php';

            if (file_exists($fileName) && ! $force) {
                $this->line("  - Lewati {$docClassName}.php (sudah ada, gunakan --force untuk overwrite)");

                continue;
            }

            $tag = $this->determineTag($controllerShortName);
            $content = $this->buildDocFileContent($docClassName, $tag, $controllerClass, $actions);

            file_put_contents($fileName, $content);
            $this->info("  + Berhasil membuat {$docClassName}.php");
            $generatedCount++;
        }

        // Hapus file doc yatim / route lama yang sudah tidak ada
        if ($force) {
            $existingFiles = glob($docsPath.'/*Doc.php') ?: [];
            foreach ($existingFiles as $file) {
                $base = basename($file);
                if (! in_array($base, $activeDocClasses)) {
                    @unlink($file);
                    $this->warn("  x Menghapus file usang {$base}");
                }
            }
        }

        $this->info("\nRegenerating Swagger UI docs...");
        $this->call('l5-swagger:generate');

        $this->newLine();
        $this->info("Selesai! {$generatedCount} file dokumentasi berhasil di-scaffold dengan tabel struktur dan data riil.");
        $this->line('Akses Swagger UI di: '.url('/api/documentation'));

        return Command::SUCCESS;
    }

    /**
     * Menentukan nama Tag yang rapi dan seragam.
     */
    protected function determineTag(string $controllerShortName): string
    {
        $base = str_replace('Controller', '', $controllerShortName);

        $customTags = [
            'Auth' => 'Autentikasi',
            'User' => 'Manajemen User',
            'Role' => 'Manajemen Role',
            'Permission' => 'Manajemen Permission',
            'PerguruanTinggi' => 'Perguruan Tinggi',
            'Prodi' => 'Program Studi',
            'Upps' => 'UPPS',
            'AnggotaUpps' => 'Anggota UPPS',
            'Kurikulum' => 'Kurikulum',
            'Cpl' => 'Capaian Pembelajaran (CPL)',
            'MataKuliah' => 'Mata Kuliah',
            'MataKuliahCpl' => 'Mapping Mata Kuliah CPL',
            'Mahasiswa' => 'Mahasiswa',
            'Angkatan' => 'Angkatan',
            'Kelas' => 'Kelas',
            'TahunAkademik' => 'Tahun Akademik',
            'KegiatanMahasiswa' => 'Kegiatan Mahasiswa',
            'AngkatanKelas' => 'Angkatan Kelas',
            'EnrollmentKelas' => 'Enrollment Kelas',
            'StatusMahasiswa' => 'Status Mahasiswa',
            'EnrollmentMataKuliah' => 'Enrollment Mata Kuliah',
            'MataKuliahAngkatanKelas' => 'Mata Kuliah Angkatan Kelas',
            'PrestasiMahasiswa' => 'Prestasi Mahasiswa',
            'EvidencePrestasiMahasiswa' => 'Evidence Prestasi Mahasiswa',
            'JenisPrestasi' => 'Jenis Prestasi',
            'NilaiMahasiswa' => 'Nilai Mahasiswa',
        ];

        return $customTags[$base] ?? Str::headline($base);
    }

    /**
     * Membangun kode PHP file Doc lengkap dengan tabel Markdown deskripsi.
     */
    protected function buildDocFileContent(string $className, string $tag, string $controllerClass, array $actions): string
    {
        $methodsCode = [];
        $registeredMethods = [];
        $modelClass = $this->resolveModelClass($controllerClass);

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
                    $exampleParam = $this->resolvePathParamExample($param, $modelClass);
                    $paramLines[] = "            new OA\\Parameter(\n                name: '{$param}',\n                in: 'path',\n                required: true,\n                description: 'ID data {$param}',\n                schema: new OA\\Schema(type: 'integer', example: {$exampleParam})\n            ),";
                }
                $parametersCode = "        parameters: [\n".implode("\n", $paramLines)."\n        ],\n";
            }

            // Security
            $securityCode = $isAuth ? "        security: [['bearerAuth' => []]],\n" : '';

            // Request body untuk POST dan PUT
            $requestBodyCode = '';
            $props = [];
            if (in_array(strtolower($httpMethod), ['post', 'put', 'patch'])) {
                $props = $this->extractPropertiesWithRealData($controllerClass, $methodName, $modelClass);
                if (! empty($props)) {
                    $propStrings = [];
                    $requiredFields = [];
                    foreach ($props as $field => $meta) {
                        $propCode = $this->formatPropertyAnnotation($field, $meta);
                        $propStrings[] = $propCode;
                        if (empty($meta['nullable'])) {
                            $requiredFields[] = $field;
                        }
                    }

                    $requiredExport = ! empty($requiredFields)
                        ? "required: ['".implode("', '", $requiredFields)."'],\n                "
                        : '';

                    $requestBodyCode = "        requestBody: new OA\\RequestBody(\n            required: true,\n            content: new OA\\JsonContent(\n                {$requiredExport}properties: [\n".implode("\n", $propStrings)."\n                ]\n            )\n        ),\n";
                }
            }

            // Buat Markdown description berupa tabel terstruktur (Request Header, Request Body, Response)
            $markdownDescription = $this->buildMarkdownDescription(
                $httpMethod,
                $isAuth,
                $pathParams,
                $props,
                $tag,
                $methodName,
                $modelClass,
                $controllerClass
            );
            $escapedDescription = var_export($markdownDescription, true);

            $summary = $this->buildSummary($methodName, $tag);
            $isPost = strtolower($httpMethod) === 'post';
            $isAuthMethod = ($controllerClass === 'App\\Http\\Controllers\\AuthController' || class_basename($controllerClass) === 'AuthController');
            $successCode = ($isPost && ! $isAuthMethod) ? 201 : 200;
            $successDesc = $successCode === 201 ? 'Data berhasil dibuat' : 'Operasi berhasil';

            $methodsCode[] = <<<PHP
    #[OA\\{$httpMethod}(
        path: '{$uri}',
        summary: '{$summary}',
        description: {$escapedDescription},
        tags: ['{$tag}'],
{$securityCode}{$parametersCode}{$requestBodyCode}        responses: [
            new OA\\Response(response: {$successCode}, description: '{$successDesc}'),
            new OA\\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\\Response(response: 422, description: 'Validasi entitas gagal'),
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
     * Membangun tabel Markdown untuk Header, Path Parameter, Request Body, dan Response.
     */
    protected function buildMarkdownDescription(
        string $httpMethod,
        bool $isAuth,
        array $pathParams,
        array $props,
        string $tag,
        string $methodName,
        ?string $modelClass,
        string $controllerClass = ''
    ): string {
        $sections = [];

        // 1. Request Headers Table
        $headerRows = [];
        if ($isAuth) {
            $headerRows[] = '| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |';
        }
        $hasBody = in_array(strtolower($httpMethod), ['post', 'put', 'patch']) && ! empty($props);
        if ($hasBody) {
            $headerRows[] = '| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |';
        }
        $headerRows[] = '| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |';

        $sections[] = "### 📋 1. Request Headers\n| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |\n| :--- | :--- | :--- | :--- | :--- |\n".implode("\n", $headerRows);

        // 2. Path Parameters Table (jika ada)
        if (! empty($pathParams)) {
            $paramRows = [];
            foreach ($pathParams as $param) {
                $ex = $this->resolvePathParamExample($param, $modelClass);
                $paramRows[] = "| `{$param}` | `integer` | **Ya (Required)** | Parameter ID unik {$param} pada path URL | `{$ex}` |";
            }
            $sections[] = "### 🔍 2. Path Parameters\n| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |\n| :--- | :--- | :--- | :--- | :--- |\n".implode("\n", $paramRows);
        }

        // 3. Request Body Table (jika POST/PUT/PATCH dan ada properti)
        if ($hasBody) {
            $bodyRows = [];
            foreach ($props as $field => $meta) {
                $typeStr = $meta['type'];
                if (! empty($meta['format'])) {
                    $typeStr .= " ({$meta['format']})";
                }
                $isRequired = empty($meta['nullable']) ? '**Ya (Required)**' : '*Tidak (Opsional / Nullable)*';
                $desc = $meta['description'] ?? Str::headline($field);

                $exVal = $meta['example'] ?? '-';
                if (is_array($exVal)) {
                    $exStr = json_encode($exVal, JSON_UNESCAPED_SLASHES);
                } elseif (is_bool($exVal)) {
                    $exStr = $exVal ? 'true' : 'false';
                } else {
                    $exStr = (string) $exVal;
                }
                $exStr = str_replace('|', '&#124;', $exStr);

                $bodyRows[] = "| `{$field}` | `{$typeStr}` | {$isRequired} | {$desc} | `{$exStr}` |";
            }
            $sectionNum = ! empty($pathParams) ? '3' : '2';
            $sections[] = "### 📥 {$sectionNum}. Request Body\n| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |\n| :--- | :--- | :--- | :--- | :--- |\n".implode("\n", $bodyRows);
        }

        // 4. Response Structure Table
        $sectionCount = 1 + (! empty($pathParams) ? 1 : 0) + ($hasBody ? 1 : 0);
        $respNum = $sectionCount + 1;
        $isPost = strtolower($httpMethod) === 'post';
        $isDelete = strtolower($httpMethod) === 'delete';
        $isAuthMethod = ($controllerClass === 'App\\Http\\Controllers\\AuthController' || class_basename($controllerClass) === 'AuthController');
        $successStatus = ($isPost && ! $isAuthMethod) ? '**201 Created**' : '**200 OK**';
        $successCode = ($isPost && ! $isAuthMethod) ? 201 : 200;
        $successMsg = ($isPost && ! $isAuthMethod) ? 'Data berhasil dibuat' : ($isDelete ? 'Data berhasil dihapus' : 'Operasi berhasil');

        $respRows = [];
        $respRows[] = "| {$successStatus} | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |";
        $respRows[] = "| | `code` | `integer` | **Ya (Required)** | HTTP Status Code ({$successCode}) | `{$successCode}` |";
        $respRows[] = "| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | \"{$successMsg}\" |";

        // Detail data payload
        if ($isDelete) {
            $respRows[] = '| | `data` | `null / object` | *Tidak (Opsional)* | Payload hasil operasi hapus | `null` |';
        } elseif ($methodName === 'login' || $methodName === 'googleLogin') {
            $respRows[] = '| | `data` | `object` | **Ya (Required)** | Objek data autentikasi | `{ ... }` |';
            $respRows[] = '| | `data.user` | `object` | **Ya (Required)** | Informasi data akun pengguna login | `{ ... }` |';
            $respRows[] = '| | `data.user.id` | `integer` | **Ya (Required)** | ID unik pengguna | `1` |';
            $respRows[] = '| | `data.user.nama` | `string` | **Ya (Required)** | Nama lengkap pengguna | \"Administrator\" |';
            $respRows[] = '| | `data.user.email` | `string` | **Ya (Required)** | Alamat email terdaftar | \"admin@siakre.polines.ac.id\" |';
            $respRows[] = '| | `data.user.role` | `string` | **Ya (Required)** | Role hak akses sistem | \"ADMINISTRATOR\" |';
            $respRows[] = '| | `data.authorization` | `object` | **Ya (Required)** | Kredensial token akses JWT | `{ ... }` |';
            $respRows[] = '| | `data.authorization.access_token` | `string` | **Ya (Required)** | Token Bearer JWT untuk request berikutnya | \"eyJhbGci...\" |';
            $respRows[] = '| | `data.authorization.token_type` | `string` | **Ya (Required)** | Jenis token otentikasi | \"Bearer\" |';
            $respRows[] = '| | `data.authorization.expires_in` | `integer` | **Ya (Required)** | Masa berlaku token dalam detik | `3600` |';
            $respRows[] = '| | `data.authorization.permissions` | `array[string]` | **Ya (Required)** | Daftar permissions aktif pengguna | `[\"user-create\", ...]` |';
        } elseif ($methodName === 'me') {
            $respRows[] = '| | `data` | `object` | **Ya (Required)** | Profil pengguna yang sedang login | `{ ... }` |';
            $respRows[] = '| | `data.id` | `integer` | **Ya (Required)** | ID unik pengguna | `1` |';
            $respRows[] = '| | `data.name` | `string` | **Ya (Required)** | Nama pengguna | \"Administrator\" |';
            $respRows[] = '| | `data.email` | `string` | **Ya (Required)** | Email pengguna | \"admin@siakre.polines.ac.id\" |';
            $respRows[] = '| | `data.role` | `string` | **Ya (Required)** | Role hak akses pengguna | \"ADMINISTRATOR\" |';
        } elseif ($methodName === 'index') {
            $respRows[] = '| | `data` | `array[object]` | **Ya (Required)** | Koleksi daftar data entitas | `[ { ... } ]` |';
            $sample = $this->getSampleRecord($modelClass);
            if ($sample) {
                foreach ($sample as $col => $val) {
                    $colType = is_int($val) ? 'integer' : (is_float($val) ? 'number' : (is_bool($val) ? 'boolean' : 'string'));
                    $isNullable = is_null($val) || (isset($props[$col]) && ! empty($props[$col]['nullable']));
                    $reqStr = $isNullable ? '*Tidak (Opsional / Nullable)*' : '**Ya (Required)**';
                    $valStr = is_null($val) ? 'null' : (is_bool($val) ? ($val ? 'true' : 'false') : (is_string($val) ? '"'.addslashes((string) $val).'"' : (string) $val));
                    $valStr = str_replace('|', '&#124;', $valStr);
                    $desc = isset($props[$col]['description']) ? $props[$col]['description'] : Str::headline($col);
                    $respRows[] = "| | `data[].{$col}` | `{$colType}` | {$reqStr} | {$desc} | `{$valStr}` |";
                }
            }
        } else {
            // show, store, update
            $respRows[] = '| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |';
            $sample = $this->getSampleRecord($modelClass);
            if ($sample) {
                foreach ($sample as $col => $val) {
                    $colType = is_int($val) ? 'integer' : (is_float($val) ? 'number' : (is_bool($val) ? 'boolean' : 'string'));
                    $isNullable = is_null($val) || (isset($props[$col]) && ! empty($props[$col]['nullable']));
                    $reqStr = $isNullable ? '*Tidak (Opsional / Nullable)*' : '**Ya (Required)**';
                    $valStr = is_null($val) ? 'null' : (is_bool($val) ? ($val ? 'true' : 'false') : (is_string($val) ? '"'.addslashes((string) $val).'"' : (string) $val));
                    $valStr = str_replace('|', '&#124;', $valStr);
                    $desc = isset($props[$col]['description']) ? $props[$col]['description'] : Str::headline($col);
                    $respRows[] = "| | `data.{$col}` | `{$colType}` | {$reqStr} | {$desc} | `{$valStr}` |";
                }
            }
        }

        // 422 Unprocessable jika ada payload body
        if ($hasBody) {
            $respRows[] = '| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |';
            $respRows[] = '| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |';
            $respRows[] = '| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \"The given data was invalid.\" |';
            $respRows[] = '| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\"field\": [\"Pesan error\"]}` |';
        }

        if ($isAuth) {
            $respRows[] = '| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \"Unauthenticated.\" |';
            $respRows[] = '| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \"User does not have the right permissions.\" |';
        }

        if (! empty($pathParams)) {
            $respRows[] = '| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \"Data tidak ditemukan\" |';
        }

        $respRows[] = '| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \"Permintaan tidak valid\" |';

        $sections[] = "### 📤 {$respNum}. Struktur Response\n| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |\n| :--- | :--- | :--- | :--- | :--- | :--- |\n".implode("\n", $respRows);

        return implode("\n\n", $sections);
    }

    /**
     * Membuat ringkasan yang jelas dan natural untuk setiap method.
     */
    protected function buildSummary(string $methodName, string $tag): string
    {
        return match ($methodName) {
            'index' => "Daftar Semua Data {$tag}",
            'store' => "Tambah Data {$tag} Baru",
            'show' => "Detail Data {$tag}",
            'update' => "Perbarui Data {$tag}",
            'destroy' => "Hapus Data {$tag}",
            'sync' => "Sinkronisasi {$tag}",
            'login' => 'Login Pengguna dan Dapatkan Token JWT',
            'googleLogin' => 'Login Menggunakan Google ID Token',
            'me' => 'Profil Pengguna Terautentikasi',
            'logout' => 'Logout dan Invalidasi Token',
            'refresh' => 'Perbarui Token Akses JWT',
            default => Str::headline($methodName).' '.$tag,
        };
    }

    /**
     * Menemukan Model Eloquent yang berkorespondensi dengan Controller.
     */
    protected function resolveModelClass(string $controllerClass): ?string
    {
        $baseName = str_replace(['App\\Http\\Controllers\\', 'Controller'], '', $controllerClass);

        if ($baseName === 'Role') {
            return Role::class;
        }
        if ($baseName === 'Permission') {
            return Permission::class;
        }

        $candidates = [
            "App\\Models\\{$baseName}",
            'App\\Models\\'.Str::singular($baseName),
        ];

        foreach ($candidates as $c) {
            if (class_exists($c) && is_subclass_of($c, Model::class)) {
                return $c;
            }
        }

        return null;
    }

    /**
     * Mendapatkan contoh ID riil untuk path parameter.
     */
    protected function resolvePathParamExample(string $param, ?string $modelClass): int
    {
        if ($param === 'mataKuliahId') {
            try {
                $id = MataKuliah::value('id');
                if ($id) {
                    return (int) $id;
                }
            } catch (\Throwable) {
            }
        }

        if ($modelClass && class_exists($modelClass)) {
            try {
                $id = $modelClass::value('id');
                if ($id) {
                    return (int) $id;
                }
            } catch (\Throwable) {
            }
        }

        return 1;
    }

    /**
     * Format OA\Property dengan tipe, deskripsi, dan contoh data riil.
     */
    protected function formatPropertyAnnotation(string $field, array $meta): string
    {
        $type = $meta['type'] ?? 'string';
        $desc = addslashes($meta['description'] ?? Str::headline($field));
        $format = isset($meta['format']) ? ", format: '{$meta['format']}'" : '';
        $nullable = ! empty($meta['nullable']) ? ', nullable: true' : '';

        if ($type === 'array') {
            $itemType = $meta['itemType'] ?? 'string';
            $itemExample = $meta['itemExample'] ?? "'example'";
            $exampleArr = array_values($meta['example'] ?? []);
            $encoded = json_encode($exampleArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $exampleExport = str_replace(['"', ':'], ["'", ' => '], $encoded);

            return "                    new OA\\Property(property: '{$field}', type: 'array', description: '{$desc}', items: new OA\\Items(type: '{$itemType}', example: {$itemExample}), example: {$exampleExport}),";
        }

        $example = $meta['example'] ?? '';
        if (is_numeric($example) && ($type === 'integer' || $type === 'number')) {
            $exampleStr = $example;
        } elseif (is_bool($example)) {
            $exampleStr = $example ? 'true' : 'false';
        } else {
            $escaped = addslashes((string) $example);
            $exampleStr = "'{$escaped}'";
        }

        return "                    new OA\\Property(property: '{$field}', type: '{$type}'{$format}, example: {$exampleStr}, description: '{$desc}'{$nullable}),";
    }

    /**
     * Mengekstrak properti request dengan data riil dari DB dan validasi.
     *
     * @return array<string, array{type: string, example: mixed, description: string, format?: string, nullable?: bool, itemType?: string, itemExample?: string}>
     */
    protected function extractPropertiesWithRealData(string $controllerClass, string $methodName, ?string $modelClass): array
    {
        $shortController = class_basename($controllerClass);

        // Kasus Khusus: Autentikasi
        if ($shortController === 'AuthController') {
            if ($methodName === 'login') {
                return [
                    'email' => [
                        'type' => 'string',
                        'format' => 'email',
                        'example' => 'administrator@polines.ac.id',
                        'description' => 'Email akun pengguna Polines',
                        'nullable' => false,
                    ],
                    'password' => [
                        'type' => 'string',
                        'format' => 'password',
                        'example' => 'password',
                        'description' => 'Kata sandi akun pengguna',
                        'nullable' => false,
                    ],
                ];
            }
            if ($methodName === 'googleLogin') {
                return [
                    'id_token' => [
                        'type' => 'string',
                        'example' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjEyMzQ1NiJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJlbWFpbCI6ImRhc2loYXl1QHBvbGluZXMuYWMuaWQifQ.signature',
                        'description' => 'Google OAuth2 ID Token kredensial',
                        'nullable' => false,
                    ],
                ];
            }

            return [];
        }

        // Kasus Khusus: MataKuliahCpl Sync
        if ($shortController === 'MataKuliahCplController' && $methodName === 'sync') {
            $cplIds = DB::table('cpl')->take(3)->pluck('id')->toArray();
            if (empty($cplIds)) {
                $cplIds = [1, 2];
            }

            return [
                'cpl_ids' => [
                    'type' => 'array',
                    'itemType' => 'integer',
                    'itemExample' => '1',
                    'example' => $cplIds,
                    'description' => 'Array ID CPL yang dipetakan ke mata kuliah',
                    'nullable' => false,
                ],
            ];
        }

        // Kasus Khusus: Role
        if ($shortController === 'RoleController') {
            $perms = DB::table('permissions')->where('guard_name', 'api')->take(3)->pluck('name')->toArray();
            if (empty($perms)) {
                $perms = ['perguruan_tinggi.read', 'prodi.read'];
            }

            return [
                'name' => [
                    'type' => 'string',
                    'example' => 'SUPERVISOR',
                    'description' => 'Nama Role sistem akreditasi',
                    'nullable' => false,
                ],
                'permissions' => [
                    'type' => 'array',
                    'itemType' => 'string',
                    'itemExample' => "'perguruan_tinggi.read'",
                    'example' => $perms,
                    'description' => 'Daftar nama permission yang ditugaskan ke role',
                    'nullable' => false,
                ],
            ];
        }

        // Kasus Khusus: User
        if ($shortController === 'UserController') {
            return [
                'nama' => [
                    'type' => 'string',
                    'example' => 'Dr. Andi Wijaya, S.T., M.Kom.',
                    'description' => 'Nama lengkap pengguna',
                    'nullable' => false,
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'example' => 'andi@polines.ac.id',
                    'description' => 'Email resmi institusi Polines (@polines.ac.id)',
                    'nullable' => false,
                ],
                'password' => [
                    'type' => 'string',
                    'format' => 'password',
                    'example' => 'password123',
                    'description' => 'Kata sandi pengguna (min 8 karakter)',
                    'nullable' => false,
                ],
                'role' => [
                    'type' => 'string',
                    'example' => UserRole::DOSEN->value,
                    'description' => 'Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN)',
                    'nullable' => false,
                ],
            ];
        }

        // 1. Cek FormRequest jika ada
        $rules = $this->extractRulesFromFormRequest($controllerClass, $methodName);

        // 2. Jika tidak ada FormRequest, cek $request->validate() di method
        if (empty($rules)) {
            $rules = $this->extractRulesFromMethodSource($controllerClass, $methodName);
        }

        // 3. Gabungkan field dari rules dan model fillable
        $fillableFields = $modelClass ? (new $modelClass)->getFillable() : [];
        $fields = array_values(array_unique(array_merge(array_keys($rules), $fillableFields)));

        // Ambil data riil sampel dari database
        $sampleRecord = $this->getSampleRecord($modelClass);

        $properties = [];
        foreach ($fields as $field) {
            if (str_contains($field, '.')) {
                continue;
            }

            $rule = $rules[$field] ?? '';
            $ruleStr = is_array($rule) ? implode('|', $rule) : (string) $rule;

            $properties[$field] = $this->buildFieldMetadata($field, $ruleStr, $sampleRecord);
        }

        return $properties;
    }

    /**
     * Ekstrak rules dari FormRequest jika digunakan.
     */
    protected function extractRulesFromFormRequest(string $controllerClass, string $methodName): array
    {
        try {
            $reflector = new ReflectionClass($controllerClass);
            if (! $reflector->hasMethod($methodName)) {
                return [];
            }

            foreach ($reflector->getMethod($methodName)->getParameters() as $param) {
                $type = $param->getType();
                if ($type && ! $type->isBuiltin()) {
                    $className = $type->getName();
                    if (is_subclass_of($className, 'Illuminate\Foundation\Http\FormRequest')) {
                        $instance = new $className;
                        if (method_exists($instance, 'rules')) {
                            return $instance->rules();
                        }
                    }
                }
            }
        } catch (\Throwable) {
        }

        return [];
    }

    /**
     * Ekstrak rules langsung dari kode program method via Reflection.
     */
    protected function extractRulesFromMethodSource(string $controllerClass, string $methodName): array
    {
        try {
            $refMethod = new ReflectionMethod($controllerClass, $methodName);
            $fileName = $refMethod->getFileName();
            if (! $fileName || ! file_exists($fileName)) {
                return [];
            }

            $lines = file($fileName);
            $code = implode('', array_slice($lines, $refMethod->getStartLine() - 1, $refMethod->getEndLine() - $refMethod->getStartLine() + 1));

            if (preg_match('/validate\(\s*\[(.*?)\]\s*(?:,|\))/s', $code, $matches)) {
                $rulesBlock = $matches[1];
                $rules = [];
                if (preg_match_all("/['\"]([a-zA-Z0-9_]+)['\"]\s*=>\s*(?:['\"]([^'\"]+)['\"]|\[([^\]]+)\])/", $rulesBlock, $ruleMatches, PREG_SET_ORDER)) {
                    foreach ($ruleMatches as $m) {
                        $rules[$m[1]] = ! empty($m[2]) ? $m[2] : $m[3];
                    }
                }

                return $rules;
            }
        } catch (\Throwable) {
        }

        return [];
    }

    /**
     * Ambil 1 record riil dari model/tabel sebagai sumber data contoh.
     */
    protected function getSampleRecord(?string $modelClass): ?array
    {
        if (! $modelClass || ! class_exists($modelClass)) {
            return null;
        }

        if (isset($this->realDataCache[$modelClass])) {
            return $this->realDataCache[$modelClass];
        }

        try {
            /** @var Model $instance */
            $instance = new $modelClass;
            $table = $instance->getTable();
            $record = DB::table($table)->latest('id')->first();
            if ($record) {
                $arr = (array) $record;
                unset($arr['password'], $arr['remember_token']);
                $this->realDataCache[$modelClass] = $arr;

                return $this->realDataCache[$modelClass];
            }
        } catch (\Throwable) {
        }

        return null;
    }

    /**
     * Susun metadata lengkap (tipe, contoh riil, deskripsi, format) untuk field.
     */
    protected function buildFieldMetadata(string $field, string $ruleStr, ?array $sampleRecord): array
    {
        $isNullable = str_contains($ruleStr, 'nullable');
        // Jika rule explicitly required atau jika bukan nullable dan field utama
        if (empty($ruleStr)) {
            $isNullable = in_array($field, ['alamat', 'pimpinan', 'pimpinan_upps', 'visi', 'misi', 'sk_kurikulum', 'kategori', 'tanggal_kelulusan', 'tanggal_selesai', 'dosen_id', 'evidence', 'mata_kuliah_angkatan_kelas_id', 'avatar', 'google_id']);
        }

        $isInteger = str_contains($ruleStr, 'integer') || str_contains($ruleStr, 'numeric') || str_ends_with($field, '_id') || str_starts_with($field, 'id_') || in_array($field, ['tahun', 'sks', 'angkatan', 'nim', 'masa_studi_bulan', 'berlaku_sampai']);
        $isDate = str_contains($ruleStr, 'date') || str_contains($field, 'tanggal');

        $type = $isInteger ? 'integer' : 'string';
        if (in_array($field, ['nilai_angka', 'bobot_nilai'])) {
            $type = 'number';
        }
        $format = $isDate ? 'date' : (str_contains($field, 'email') ? 'email' : null);

        // Cari data riil dari record database
        $realVal = null;
        if ($sampleRecord && array_key_exists($field, $sampleRecord) && $sampleRecord[$field] !== null) {
            $realVal = $sampleRecord[$field];
        }

        // Jika tidak ada di record, periksa referensi relasi Foreign Key secara dinamis
        if ($realVal === null) {
            $realVal = $this->resolveForeignKeyRealValue($field);
        }

        // Jika tetap belum ada, gunakan kamus nilai cerdas berdasarkan nama kolom
        if ($realVal === null) {
            $realVal = $this->fallbackRealValue($field, $type);
        }

        if ($type === 'integer') {
            $realVal = (int) $realVal;
        }

        $meta = [
            'type' => $type,
            'example' => $realVal,
            'description' => $this->generateFieldDescription($field),
            'nullable' => $isNullable,
        ];

        if ($format) {
            $meta['format'] = $format;
        }

        return $meta;
    }

    /**
     * Mencari ID riil di tabel tujuan untuk foreign key.
     */
    protected function resolveForeignKeyRealValue(string $field): ?int
    {
        $fkMap = [
            'id_pt' => ['table' => 'perguruan_tinggis', 'col' => 'id'],
            'prodi_id' => ['table' => 'prodi', 'col' => 'id'],
            'id_prodi' => ['table' => 'prodi', 'col' => 'id'],
            'id_upps' => ['table' => 'upps', 'col' => 'id'],
            'id_kurikulum' => ['table' => 'kurikulum', 'col' => 'id'],
            'mahasiswa_id' => ['table' => 'data_mahasiswa', 'col' => 'id'],
            'id_mahasiswa' => ['table' => 'data_mahasiswa', 'col' => 'id'],
            'tahun_akademik_id' => ['table' => 'tahun_akademik', 'col' => 'id'],
            'angkatan_id' => ['table' => 'angkatan', 'col' => 'id'],
            'kelas_id' => ['table' => 'kelas', 'col' => 'id'],
            'angkatan_kelas_id' => ['table' => 'angkatan_kelas', 'col' => 'id'],
            'id_prestasi_mahasiswa' => ['table' => 'prestasi_mahasiswa', 'col' => 'id'],
            'id_enrollment_mahasiswa_angkatan_kelas' => ['table' => 'enrollment_mahasiswa_angkatan_kelas', 'col' => 'id'],
            'mata_kuliah_id' => ['table' => 'mata_kuliah', 'col' => 'id'],
            'enrollment_mata_kuliah_id' => ['table' => 'enrollment_mata_kuliah', 'col' => 'id'],
            'enrollment_mahasiswa_angkatan_kelas_id' => ['table' => 'enrollment_mahasiswa_angkatan_kelas', 'col' => 'id'],
            'mata_kuliah_angkatan_kelas_id' => ['table' => 'mata_kuliah_angkatan_kelas', 'col' => 'id'],
            'dosen_id' => ['table' => 'users', 'col' => 'id'],
        ];

        if (isset($fkMap[$field])) {
            try {
                $val = DB::table($fkMap[$field]['table'])->value($fkMap[$field]['col']);
                if ($val) {
                    return (int) $val;
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    /**
     * Kamus nilai default yang sangat realistis untuk bidang akademik Polines.
     */
    protected function fallbackRealValue(string $field, string $type): mixed
    {
        $dictionary = [
            'kode_pt' => 'PT001',
            'nama_pt' => 'Politeknik Negeri Semarang',
            'alamat' => 'Jl. Prof. H. Soedarto, S.H., Tembalang, Semarang 50275',
            'pimpinan' => 'Dr. Eni Dwi Wardihani, S.T., M.T.',
            'visi' => 'Menjadi perguruan tinggi vokasi yang unggul, berdaya saing global, dan berkarakter pada tahun 2029.',
            'misi' => 'Menyelenggarakan pendidikan vokasi yang bermutu tinggi dan relevan dengan industri.',
            'kode_prodi' => 'PR01',
            'nama_prodi' => 'S.Tr. Teknologi Rekayasa Komputer',
            'akreditasi' => 'Unggul',
            'kode_upps' => 'UPPS01',
            'nama_upps' => 'Jurusan Teknik Elektro',
            'pimpinan_upps' => 'Dr. Amin Suharjono, S.T., M.T.',
            'jenis' => 'Jurusan',
            'nama' => 'Dr. Amin Suharjono, S.T., M.T.',
            'jabatan' => 'Ketua Jurusan Teknik Elektro',
            'nama_kurikulum' => 'Kurikulum Merdeka TRK 2024',
            'berlaku_sampai' => 2028,
            'sk_kurikulum' => 'SK/2024/001/TRK',
            'kode_cpl' => 'CPL-01',
            'deskripsi_cpl' => 'Bertakwa kepada Tuhan YME dan mampu menunjukkan etika moral profesi rekayasa.',
            'kategori' => 'Sikap',
            'kode_mata_kuliah' => 'TRK101',
            'nama_mata_kuliah' => 'Algoritma dan Pemrograman Terapan',
            'sks' => 3,
            'nim' => 43325006,
            'nama_mahasiswa' => 'Dasi Hayu Permana',
            'tahun_masuk' => 2024,
            'jenis_pendaftaran' => 'SNBT',
            'tanggal_kelulusan' => '2028-09-13',
            'masa_studi_bulan' => 48,
            'angkatan' => 2024,
            'kelas' => 'TRK-1A',
            'tahun' => 2024,
            'nama_kegiatan' => 'Divisi Riset Himpunan Mahasiswa Elektro',
            'jenis_kegiatan' => 'Organisasi',
            'peran' => 'Ketua Divisi',
            'tingkat' => 'Polines',
            'tanggal_mulai' => '2024-02-01',
            'tanggal_selesai' => '2024-12-20',
            'penyelenggara' => 'HME Polines',
            'status_validasi' => 'Disetujui',
            'status' => 'Aktif',
            'nama_aktivitas' => 'Juara 1 Hackathon Nasional IoT & Smart City',
            'evidence' => 'sertifikat_juara_1_hackathon_iot_2024.pdf',
            'email' => 'andi@polines.ac.id',
            'password' => 'password123',
            'role' => 'KAPRODI',
        ];

        if (isset($dictionary[$field])) {
            return $dictionary[$field];
        }

        return $type === 'integer' ? 1 : 'Contoh '.Str::headline($field);
    }

    /**
     * Membuat deskripsi yang informatif dalam Bahasa Indonesia.
     */
    protected function generateFieldDescription(string $field): string
    {
        $descriptions = [
            'kode_pt' => 'Kode unik identitas Perguruan Tinggi',
            'nama_pt' => 'Nama resmi Perguruan Tinggi',
            'alamat' => 'Alamat lengkap domisili kampus',
            'pimpinan' => 'Nama pimpinan / Direktur Perguruan Tinggi',
            'visi' => 'Pernyataan visi Perguruan Tinggi',
            'misi' => 'Pernyataan misi Perguruan Tinggi',
            'kode_prodi' => 'Kode unik Program Studi',
            'nama_prodi' => 'Nama Program Studi',
            'akreditasi' => 'Peringkat akreditasi BAN-PT/LAM INFOKOM',
            'id_pt' => 'ID referensi Perguruan Tinggi',
            'kode_upps' => 'Kode Unit Pengelola Program Studi (Fakultas/Jurusan)',
            'nama_upps' => 'Nama resmi Unit Pengelola Program Studi',
            'pimpinan_upps' => 'Nama pimpinan UPPS (Ketua Jurusan / Dekan)',
            'prodi_id' => 'ID referensi Program Studi',
            'id_prodi' => 'ID referensi Program Studi',
            'jenis' => 'Jenis entitas unit pengelola (misal: Jurusan, Fakultas)',
            'id_upps' => 'ID referensi UPPS',
            'nama' => 'Nama lengkap pengurus atau anggota',
            'jabatan' => 'Nama jabatan struktural dalam organisasi',
            'nama_kurikulum' => 'Nama kurikulum akademik',
            'berlaku_sampai' => 'Tahun masa berakhirnya kurikulum',
            'sk_kurikulum' => 'Nomor Surat Keputusan (SK) pemberlakuan kurikulum',
            'id_kurikulum' => 'ID referensi Kurikulum',
            'kode_cpl' => 'Kode unik Capaian Pembelajaran Lulusan (CPL)',
            'deskripsi_cpl' => 'Uraian kompetensi dan capaian pembelajaran',
            'kategori' => 'Kategori CPL (Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus)',
            'kode_mata_kuliah' => 'Kode unik Mata Kuliah',
            'nama_mata_kuliah' => 'Nama resmi Mata Kuliah',
            'sks' => 'Bobot satuan kredit semester (SKS)',
            'nim' => 'Nomor Induk Mahasiswa (unik int)',
            'nama_mahasiswa' => 'Nama lengkap mahasiswa terdaftar',
            'tahun_masuk' => 'Tahun kalender mahasiswa pertama kali terdaftar',
            'jenis_pendaftaran' => 'Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri)',
            'enrollment_mahasiswa_angkatan_kelas_id' => 'ID referensi enrollment mahasiswa pada angkatan kelas',
            'mata_kuliah_angkatan_kelas_id' => 'ID referensi penawaran mata kuliah pada angkatan kelas',
            'nilai_angka' => 'Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00)',
            'nilai_huruf' => 'Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E)',
            'bobot_nilai' => 'Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00)',
            'status_kelulusan' => 'Status kelulusan mata kuliah (Lulus / Tidak Lulus)',
            'tanggal_kelulusan' => 'Tanggal kelulusan resmi yudisium mahasiswa',
            'masa_studi_bulan' => 'Lama masa studi mahasiswa dalam satuan bulan',
            'angkatan' => 'Tahun angkatan mahasiswa',
            'kelas' => 'Nama kode rombongan belajar / kelas (misal: TRK-1A)',
            'tahun' => 'Tahun kalender akademik',
            'mahasiswa_id' => 'ID referensi Mahasiswa',
            'id_mahasiswa' => 'ID referensi Mahasiswa',
            'tahun_akademik_id' => 'ID referensi Tahun Akademik',
            'nama_kegiatan' => 'Nama kegiatan kemahasiswaan atau lomba',
            'jenis_kegiatan' => 'Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM)',
            'peran' => 'Peran atau posisi mahasiswa dalam kegiatan',
            'tingkat' => 'Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional)',
            'tanggal_mulai' => 'Tanggal pelaksanaan kegiatan dimulai',
            'tanggal_selesai' => 'Tanggal pelaksanaan kegiatan selesai',
            'penyelenggara' => 'Instansi atau organisasi penyelenggara kegiatan',
            'status_validasi' => 'Status persetujuan validasi (Disetujui, Pending, Ditolak)',
            'kelas_id' => 'ID referensi Kelas',
            'angkatan_id' => 'ID referensi Angkatan',
            'angkatan_kelas_id' => 'ID referensi Angkatan Kelas',
            'id_enrollment_mahasiswa_angkatan_kelas' => 'ID referensi Enrollment Mahasiswa Angkatan Kelas',
            'status' => 'Status keaktifan mahasiswa (Aktif, Cuti, Lulus)',
            'mata_kuliah_id' => 'ID referensi Mata Kuliah',
            'dosen_id' => 'ID referensi Dosen pengampu (User)',
            'enrollment_mata_kuliah_id' => 'ID referensi Penawaran Enrollment Mata Kuliah',
            'nama_aktivitas' => 'Nama prestasi kejuaraan atau kompetisi',
            'evidence' => 'Nama file atau link berkas bukti sertifikat pendukung',
            'email' => 'Alamat email pengguna',
            'password' => 'Kata sandi akun',
            'role' => 'Hak akses peran pengguna dalam sistem',
        ];

        return $descriptions[$field] ?? Str::headline($field);
    }
}
