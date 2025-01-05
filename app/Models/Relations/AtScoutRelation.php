<?php

namespace App\Models\Relations;

use App\Models\AtScout;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property-read Item $parent
 */
class AtScoutRelation extends Relation
{
    public function __construct(Item $item)
    {
        parent::__construct(AtScout::query(), $item);
    }

    public function addConstraints(): void
    {
        // No base constraints needed.
    }

    public function addEagerConstraints(array $models): void
    {
        /** @var Item[] $models */
        $this->query->where(function ($query) use ($models) {
            foreach ($models as $model) {
                $query->orWhere(function ($q) use ($model) {
                    $q->where('bevers_item_id', $model->id)
                        ->orWhere('welpen_item_id', $model->id)
                        ->orWhere('scouts_item_id', $model->id)
                        ->orWhere('explorers_item_id', $model->id)
                        ->orWhere('roverscouts_item_id', $model->id)
                        ->orWhere('extra_item_id', $model->id);
                });
            }
        });
    }

    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation): array
    {
        /** @var Item[] $models */
        foreach ($models as $model) {
            $matches = $results->filter(function (AtScout $atScout) use ($model) {
                return $atScout->bevers_item_id === $model->id
                    || $atScout->welpen_item_id === $model->id
                    || $atScout->scouts_item_id === $model->id
                    || $atScout->explorers_item_id === $model->id
                    || $atScout->roverscouts_item_id === $model->id
                    || $atScout->extra_item_id === $model->id;
            });

            $model->setRelation($relation, $matches);
        }
        return $models;
    }

    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
