<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FavoriteService
{
    /**
     * @var array<string, class-string<Model>>
     */
    protected array $favoritableMap = [
        'product' => Product::class,
        'market' => Market::class,
    ];

    public function __construct(private Favorite $favorite)
    {
    }

    /**
     * Summary of list
     * @param int $userId
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list(int $userId, ?string $type = null, ?int $limit = null, ?int $offset = null): LengthAwarePaginator
    {
        $query = $this->favorite
            ->with('favoritable')
            ->forUser($userId);

        if ($type) {
            $query->where('favoritable_type', $this->resolveFavoritableClass($type));
        }

        return $query->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);
    }

    /**
     * Summary of add
     * @param int $userId
     * @param string $type
     * @param string $favoritableId
     * @return \App\Models\Favorite
     */
    public function add(int $userId, string $type, string $favoritableId): Favorite
    {
        $favoritableClass = $this->resolveFavoritableClass($type);
        $favoritable = $this->findFavoritable($favoritableClass, $favoritableId);

        return $this->favorite->firstOrCreate([
            'user_id' => $userId,
            'favoritable_type' => $favoritableClass,
            'favoritable_id' => $favoritable->getKey(),
        ]);
    }

    /**
     * Summary of remove
     * @param int $userId
     * @param string $type
     * @param string $favoritableId
     * @return bool
     */
    public function remove(int $userId, string $type, string $favoritableId): bool
    {
        $favoritableClass = $this->resolveFavoritableClass($type);

        return (bool) $this->favorite->forUser($userId)
            ->ofType($favoritableClass)
            ->where('favoritable_id', $favoritableId)
            ->delete();
    }

    /**
     * Summary of resolveFavoritableClass
     * @param string $type
     * @return string
     */
    protected function resolveFavoritableClass(string $type): string
    {
        $normalized = Str::lower($type);
        $favoritableClass = $this->favoritableMap[$normalized] ?? null;

        if (!$favoritableClass) {
            throw new InvalidArgumentException('Unsupported favorite type: ' . $type);
        }

        return $favoritableClass;
    }

    /**
     * Summary of findFavoritable
     * @param string $favoritableClass
     * @param string $favoritableId
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function findFavoritable(string $favoritableClass, string $favoritableId): Model
    {
        /** @var Model|null $model */
        $model = $favoritableClass::find($favoritableId);

        if (!$model) {
            throw (new ModelNotFoundException())->setModel($favoritableClass, [$favoritableId]);
        }

        return $model;
    }
}
