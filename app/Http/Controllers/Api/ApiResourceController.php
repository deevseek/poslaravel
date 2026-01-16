<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ApiResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $query = $resource['model']::query();

        if ($request->filled('search') && ! empty($resource['searchable'])) {
            $search = (string) $request->input('search');
            $query->where(function ($query) use ($resource, $search): void {
                foreach ($resource['searchable'] as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $perPage = $request->integer('per_page', 15);
        $paginator = $query->paginate($perPage);

        return response()->json($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);
        $modelClass = $resource['model'];

        $validated = $request->validate($this->rules($modelClass));
        $payload = Arr::only($validated, $this->fillable($modelClass));

        /** @var Model $model */
        $model = $modelClass::create($payload);

        return response()->json([
            'data' => $model,
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);
        $modelClass = $resource['model'];
        $model = $this->findModel($request, $modelClass);

        return response()->json([
            'data' => $model,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);
        $modelClass = $resource['model'];
        $model = $this->findModel($request, $modelClass);

        $validated = $request->validate($this->rules($modelClass));
        $payload = Arr::only($validated, $this->fillable($modelClass));

        $model->fill($payload);
        $model->save();

        return response()->json([
            'data' => $model,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);
        $modelClass = $resource['model'];
        $model = $this->findModel($request, $modelClass);

        $model->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }

    private function resolveResource(Request $request): array
    {
        $segments = explode('/', trim($request->route()->uri(), '/'));

        if ($segments[0] === 'v1') {
            array_shift($segments);
        }

        $resourceKey = $segments[0] ?? '';
        $resources = config('api_resources.resources', []);

        if (! isset($resources[$resourceKey])) {
            abort(404, 'Resource not configured.');
        }

        return $resources[$resourceKey];
    }

    private function findModel(Request $request, string $modelClass): Model
    {
        $parameters = array_values($request->route()->parameters());
        $id = $parameters[0] ?? null;

        return $modelClass::query()->findOrFail($id);
    }

    private function fillable(string $modelClass): array
    {
        $model = new $modelClass();

        return $model->getFillable();
    }

    private function rules(string $modelClass): array
    {
        $fillable = $this->fillable($modelClass);

        return Arr::mapWithKeys($fillable, fn (string $field) => [$field => ['sometimes']]);
    }
}
